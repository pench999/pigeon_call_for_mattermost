<?php

// レスポンスをプレーンテキストで返す
header('Content-Type: text/plain; charset=utf-8');

// POST以外は許可しない例
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405: Method Not Allowed
    echo "エラー: POSTメソッド以外は許可していません。";
    exit;
}
// Mattermostが送ってくる token を取得
//    （Outgoing Webhook設定で発行されたトークン値）
$receivedToken = $_POST['token'] ?? ''; // JSON形式なら json_decode(file_get_contents('php://input'), true) で取得

// 事前に控えておいた "Outgoing Webhook" のトークンと比較
$expectedToken = 'sodbrqqtt7dwpym58a14u58q9o'; // ここをMattermost管理画面で生成されたtokenに合わせる
if ($receivedToken !== $expectedToken) {
    // トークン不一致=不正アクセス
    http_response_code(403);
    echo "Invalid token\n";
    exit;
}
// ■■■ submit2: 架電「設定」の処理 ■■■
    $cmd = 'sudo /usr/local/bin/pigeon-set.sh';
    exec($cmd, $output, $retVal);

    if ($retVal === 0) {
//        echo "架電設定しました\n";
    echo json_encode(
    ["text" => "架電設定しました\n"],
    );
    } else {
        http_response_code(500);
//        echo "架電設定に失敗しました\n";
    echo json_encode(
    ["text" => "架電設定に失敗しました\n"], 
    );
    }
    exit;

