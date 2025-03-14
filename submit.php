<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Pigeonにおける架電状況</title>
  </head>
  <body>
    <h1>各ZABBIXのPigeon設定</h1>
    <script>
      ask = () => {
        return confirm('本当に実行していいんだな？');
      }
    </script>
    <form method="post" target="submit" onsubmit="return ask()">
      <input type="submit" name="submit1" value="状態確認">
      <input type="submit" name="submit2" value="架電設定する">
      <input type="submit" value="架電設定解除する">
    </form>
  <BR>
  <button onclick="location.href='logout.php'">ログアウト</button>
<?php

session_start();

if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['submit1'])) {
     echo '<BR>';
     echo 'ZABBIX社内の確認をするよ>>>>>>>>>>';
    @ob_flush();
    @flush();
    sleep( 1 );
     $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh syanai';
     if (exec($cmd) == 2){
         echo '<font size="5">架電しないよ</font>';
     }else{
         echo '<font size="5">架電するよ</font>';
     }
     echo '<BR>';
     echo '<BR>';
     echo 'ザビ家のデフォルトの確認をするよ>>>>>>>>>>';
    @ob_flush();
    @flush();
    sleep( 1 );
     $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh zabike1';
     if (exec($cmd) == 2){
         echo '<font size="5">架電しないよ</font>';
     }else{
         echo '<font size="5">架電するよ</font>';
     }
     echo '<BR>';
     echo 'ザビ家のixMark(高田)宛ての確認をするよ>>>>>>>>>>';
    @ob_flush();
    @flush();
    sleep( 1 );
     $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh zabike2';
     if (exec($cmd) == 2){
         echo '<font size="5">架電しないよ</font>';
     }else{
         echo '<font size="5">架電するよ</font>';
     }
     echo '<BR>';
     echo '<BR>';
     echo 'Openstack unit1 デフォルトの確認をするよ>>>>>>>>>>';
    @ob_flush();
    @flush();
    sleep( 1 );
     $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_1';
     if (exec($cmd) == 2){
         echo '<font size="5">架電しないよ</font>';
     }else{
         echo '<font size="5">架電するよ</font>';
     }
     echo '<BR>';
     echo 'Openstack unit1 橘内宛ての確認をするよ>>>>>>>>>>';
    @ob_flush();
    @flush();
    sleep( 1 );
     $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_2';
     if (exec($cmd) == 2){
         echo '<font size="5">架電しないよ</font>';
     }else{
         echo '<font size="5">架電するよ</font>';
     }
     echo '<BR>';
     echo 'Openstack unit1 工藤宛ての確認をするよ>>>>>>>>>>';
    @ob_flush();
    @flush();
    sleep( 1 );
     $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_3';
     if (exec($cmd) == 2){
         echo '<font size="5">架電しないよ</font>';
     }else{
         echo '<font size="5">架電するよ</font>';
     }
     echo '<BR>';
     echo '<BR>';
     echo 'Openstack unit2の確認をするよ>>>>>>>>>>';
    @ob_flush();
    @flush();
    sleep( 1 );
     $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit2';
     if (exec($cmd) == 2){
         echo '<font size="5">架電しないよ</font>';
     }else{
         echo '<font size="5">架電するよ</font>';
     }
     echo '<BR>';
     echo '<BR>';
     echo 'グリーンイノベーションの確認をするよ>>>>>>>>>>';
    @ob_flush();
    @flush();
    sleep( 1 );
     $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh green';
     if (exec($cmd) == 2){
         echo '<font size="5">架電しないよ</font>';
     }else{
         echo '<font size="5">架電するよ</font>';
     }
     echo '<BR><BR>';
     echo '終わり'; 
  } elseif (isset($_POST['submit2'])) {
     echo '<BR>';
     echo '今設定してるんで、ちょっと待ってください';
    @ob_flush();
    @flush();
     $cmd = 'sudo /usr/local/bin/pigeon-set.sh';
     exec($cmd, $opt, $ret);
     #print_r($ret);
     if ($ret == 0){
         echo '<h3>架電設定しました</h3>';
     }else{
         echo '<h3>何故か失敗しました</h3>';
     }
  } else {
     echo '<BR>';
     echo '今設定してるんで、ちょっと待ってください';
    @ob_flush();
    @flush();
     $cmd = 'sudo /usr/local/bin/pigeon-unset.sh';
     exec($cmd, $opt, $ret);
     #print_r($ret);
     if ($ret == 0){
         echo '<h3>架電解除しました</h3>';
     }else{
         echo '<h3>何故か失敗しました</h3>';
     }
  }
}
?>
  </body>
</html>
