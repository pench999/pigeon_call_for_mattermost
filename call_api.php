<?php
// call_api.php

header('Content-Type: application/json; charset=utf-8');

// POST以外は許可しない例
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405: Method Not Allowed
    echo json_encode([
        'error' => 'Method not allowed. Please use POST.'
    ]);
    exit;
}

// 実際の処理分岐
// submit1, submit2, それ以外のどれがセットされているかによって処理を分ける
if (isset($_POST['submit1'])) {

    // ■■■ submit1: 架電チェック系の処理 ■■■
    // ここでは実行結果を配列にまとめ、最後にJSONで返す

    // 処理結果を格納する配列
    $results = [];

    // 1) ZABBIX社内の確認
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh syanai';
    $res = exec($cmd);
    $results[] = [
        'target' => 'ZABBIX Syanai',
        'kaden'  => ($res == 2) ? false : true  // 2なら「架電しない」、それ以外なら「架電する」
    ];

    // 2) ザビ家デフォルトの確認
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh zabike1';
    $res = exec($cmd);
    $results[] = [
        'target' => 'Zabike (default)',
        'kaden'  => ($res == 2) ? false : true
    ];

    // 3) ザビ家 ixMark(高田)宛ての確認
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh zabike2';
    $res = exec($cmd);
    $results[] = [
        'target' => 'Zabike (ixMark TAKADA)',
        'kaden'  => ($res == 2) ? false : true
    ];

    // 4) Openstack unit1 デフォルト
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_1';
    $res = exec($cmd);
    $results[] = [
        'target' => 'Openstack unit1 (default)',
        'kaden'  => ($res == 2) ? false : true
    ];

    // 5) Openstack unit1 橘内宛て
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_2';
    $res = exec($cmd);
    $results[] = [
        'target' => 'Openstack unit1 (To KITSUNAI)',
        'kaden'  => ($res == 2) ? false : true
    ];

    // 6) Openstack unit1 工藤宛て
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit1_3';
    $res = exec($cmd);
    $results[] = [
        'target' => 'Openstack unit1 (To KUDO)',
        'kaden'  => ($res == 2) ? false : true
    ];

    // 7) Openstack unit2
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh unit2';
    $res = exec($cmd);
    $results[] = [
        'target' => 'Openstack unit2',
        'kaden'  => ($res == 2) ? false : true
    ];

    // 8) グリーンイノベーション
    $cmd = 'sudo /usr/local/bin/pigeon-check-part.sh green';
    $res = exec($cmd);
    $results[] = [
        'target' => 'Green Innovation',
        'kaden'  => ($res == 2) ? false : true
    ];

    // 最終的なレスポンスをJSONで返す
    echo json_encode([
        'status' => 'success',
        'message' => 'submit1: 架電チェック完了',
        'results' => $results
    ], JSON_UNESCAPED_UNICODE);
    exit;

} elseif (isset($_POST['submit2'])) {

    // ■■■ submit2: 架電設定の処理 ■■■
    $cmd = 'sudo /usr/local/bin/pigeon-set.sh';
    exec($cmd, $output, $retVal);

    if ($retVal === 0) {
        // 成功
        echo json_encode([
            'status' => 'success',
            'message' => '架電設定しました'
        ]);
    } else {
        // 失敗
        http_response_code(500);
        echo json_encode([
            'status' => 'fail',
            'message' => '架電設定に失敗しました'
        ]);
    }
    exit;

} else {
    // ■■■ それ以外: 架電解除の処理 ■■■
    $cmd = 'sudo /usr/local/bin/pigeon-unset.sh';
    exec($cmd, $output, $retVal);

    if ($retVal === 0) {
        echo json_encode([
            'status' => 'success',
            'message' => '架電解除しました'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'fail',
            'message' => '架電解除に失敗しました'
        ]);
    }
    exit;
}

