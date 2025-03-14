<?php
// call_api.php

// レスポンスをプレーンテキストで返す
header('Content-Type: text/plain; charset=utf-8');

// POST以外は許可しない例
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405: Method Not Allowed
    echo "エラー: POSTメソッド以外は許可していません。";
    exit;
}

if (isset($_POST['submit1'])) {
    // ■■■ submit1: 架電チェックの処理 ■■■
    // 処理結果を文字列として組み立てていく
    $outputText = "=== 架電チェック結果 ===\n";

    // 1) ZABBIX社内の確認
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh syanai';
    $res = exec($cmd);
    $kaden = ($res == 2) ? "架電しない" : "架電する";
    $outputText .= "ZABBIX社内 : {$kaden}\n";

    // 2) ザビ家デフォルト
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh zabike1';
    $res = exec($cmd);
    $kaden = ($res == 2) ? "架電しない" : "架電する";
    $outputText .= "ザビ家(デフォルト) : {$kaden}\n";

    // 3) ザビ家 ixMark(高田)宛て
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh zabike2';
    $res = exec($cmd);
    $kaden = ($res == 2) ? "架電しない" : "架電する";
    $outputText .= "ザビ家(ixMark高田) : {$kaden}\n";

    // 4) Openstack unit1 デフォルト
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_1';
    $res = exec($cmd);
    $kaden = ($res == 2) ? "架電しない" : "架電する";
    $outputText .= "Openstack unit1(デフォルト) : {$kaden}\n";

    // 5) Openstack unit1 橘内宛て
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_2';
    $res = exec($cmd);
    $kaden = ($res == 2) ? "架電しない" : "架電する";
    $outputText .= "Openstack unit1(橘内宛) : {$kaden}\n";

    // 6) Openstack unit1 工藤宛て
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_3';
    $res = exec($cmd);
    $kaden = ($res == 2) ? "架電しない" : "架電する";
    $outputText .= "Openstack unit1(工藤宛) : {$kaden}\n";

    // 7) Openstack unit2
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit2';
    $res = exec($cmd);
    $kaden = ($res == 2) ? "架電しない" : "架電する";
    $outputText .= "Openstack unit2 : {$kaden}\n";

    // 8) グリーンイノベーション
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh green';
    $res = exec($cmd);
    $kaden = ($res == 2) ? "架電しない" : "架電する";
    $outputText .= "グリーンイノベーション : {$kaden}\n";

    $outputText .= "---- チェック完了 ----\n";

    // テキストを返す
    echo $outputText;
    exit;

} elseif (isset($_POST['submit2'])) {
    // ■■■ submit2: 架電「設定」の処理 ■■■
    $cmd = 'sudo /usr/local/bin/pigeon-set.sh';
    exec($cmd, $output, $retVal);

    if ($retVal === 0) {
        echo "架電設定しました\n";
    } else {
        http_response_code(500);
        echo "架電設定に失敗しました\n";
    }
    exit;

} else {
    // ■■■ それ以外: 架電「解除」の処理 ■■■
    $cmd = 'sudo /usr/local/bin/pigeon-unset.sh';
    exec($cmd, $output, $retVal);

    if ($retVal === 0) {
        echo "架電解除しました\n";
    } else {
        http_response_code(500);
        echo "架電解除に失敗しました\n";
    }
    exit;
}

