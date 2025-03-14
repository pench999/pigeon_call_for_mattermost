<?php
//セッションを使うことを宣言
session_start();

//データベースに接続する
try {
  $pdo = new PDO('sqlite:user.db', '', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
}
catch (PDOExeption $e) {
  exit ('データベースエラー');
}

//ログイン状態の場合ログイン後のページにリダイレクト
if (isset($_SESSION["login"])) {
  session_regenerate_id(TRUE);
  header("Location: submit.php");
  exit();
}

//postされて来なかったとき
if (count($_POST) === 0) {
  $message = "";
}
//postされて来た場合
else {
  //ユーザー名またはパスワードが送信されて来なかった場合
  if(empty($_POST["uname"]) || empty($_POST["pass"])) {
    $message = "ユーザー名とパスワードを入力してください";
  }
  //ユーザー名とパスワードが送信されて来た場合
  else {
    //post送信されてきたユーザー名がデータベースにあるか検索
    try {
      $stmt = $pdo -> prepare('SELECT * FROM users WHERE name=?');
      $stmt -> bindParam(1, $_POST['uname'], PDO::PARAM_STR, 10);
      $stmt -> execute();
      $result = $stmt -> fetch(PDO::FETCH_ASSOC);
    }
    catch (PDOExeption $e) {
      exit('データベースエラー');
    }

    //検索したユーザー名に対してパスワードが正しいかを検証
    //正しくないとき
    if (!password_verify($_POST['pass'], $result['pass'])) {
      $message="ユーザー名かパスワードが違います";
    }
    //正しいとき
    else {
      session_regenerate_id(TRUE); //セッションidを再発行
      $_SESSION["login"] = $_POST['uname']; //セッションにログイン情報を登録
      header("Location: submit.php"); //ログイン後のページにリダイレクト
      exit();
    }
  }
}

$message = htmlspecialchars($message);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>架電確認君</title>
<link href="login.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1>架電確認君ログインページ</h1>
<div class="message"><?php echo $message;?></div>
<div class="loginform">
  <form action="login.php" method="post">
<!--    <ul> -->
    <a>ユーザー名：<input name="uname" type="text"></a><BR>
    <a>パスワード：<input name="pass" type="password"></a><BR>
    <a><input name="ログイン" type="submit" value="ログイン"></a>
<!--    </ul> -->
  </form>
</div>
</body>
</html>
