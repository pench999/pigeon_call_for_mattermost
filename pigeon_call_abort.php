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
$expectedToken = '5fwutxrxdfnj5jrxugxrafw7iw'; // ここをMattermost管理画面で生成されたtokenに合わせる
if ($receivedToken !== $expectedToken) {
    // トークン不一致=不正アクセス
    http_response_code(403);
    echo "Invalid token\n";
    exit;
}

/**
 * Pigeonの calls API から「status」が "waiting" の resultId を抽出し、
 * それらをまとめて chain/abort API に POST して中断処理を行うサンプル。
 */
function abortWaitingCalls() {
    // 1. calls API (GET) の URL と認証トークン、abort API の URL を設定
    $callsUrl   = "https://fairway.cloud.kompira.jp/api/apps/pigeon/calls?offset=0&limit=100";
    $abortUrl   = "https://fairway.cloud.kompira.jp/api/apps/pigeon/chain/abort";
    $authToken  = "Nmj0J/50cCvffIL3GpJzukQ1XaLlP5STApeK3dmp"; // 環境に合わせて書き換えてください

    // 2. calls API へ GET リクエストを送り、レスポンス(JSON)を取得
    $ch = curl_init($callsUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'X-Authorization: Token ' . $authToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("cURL Error (GET calls): " . curl_error($ch));
    }
    curl_close($ch);

    // 3. レスポンス JSON を連想配列に変換
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON デコードエラー: " . json_last_error_msg());
    }

    // 4. "items" 配下から、status が "waiting" の要素だけを抽出
    if (!isset($data['items'])) {
    //  echo "calls API のレスポンスに 'items' が含まれていません。\n";
        echo json_encode(
        ["text" => "calls API のレスポンスに 'items' が含まれていません。\n"],
        );
        return;
    }
    $waitingResultIds = [];
    foreach ($data['items'] as $item) {
        // 各コールオブジェクトに "status" と "resultId" が存在すればチェック
        if (isset($item['status']) && isset($item['resultId'])) {
            if ($item['status'] === 'waiting') {
                $waitingResultIds[] = $item['resultId'];
            }
        }
    }

    // 抽出結果がなければ何もせず終了
    if (count($waitingResultIds) === 0) {
    //  echo "waiting 状態のコールはありませんでした。\n";
        echo json_encode(
        ["text" => "waiting 状態のコールはありませんでした。\n"],
        );
        return;
    }

    // 5. status が "waiting" の resultId をすべて中断(POST /chain/abort)
    //    → ログ出力を最後にまとめて行うために、結果を一時保管する
    $abortedList = [];    // 成功した resultId
    $failedList  = [];    // 失敗した resultId やステータスコード等

    foreach ($waitingResultIds as $rid) {
        $payload = json_encode(["resultId" => $rid]);

        $ch = curl_init($abortUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: */*',
            'X-Authorization: Token ' . $authToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $abortResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            // cURL自体のエラー
            $errorMsg = curl_error($ch);
            $failedList[] = "resultId: {$rid}, cURL error: {$errorMsg}";
            curl_close($ch);
            continue;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // HTTPステータスコードで簡易的に成功/失敗を振り分け
        if ($httpCode >= 200 && $httpCode < 300) {
            $abortedList[] = $rid;
        } else {
            $failedList[] = "resultId: {$rid}, HTTP status: {$httpCode}";
        }
    }

    // 6. 全ての abort リクエストを完了したので、結果をまとめて JSON 出力
    $logs = "";

    if (!empty($abortedList)) {
        $logs .= "以下の resultId で abort に成功しました:\n";
        foreach ($abortedList as $id) {
            $logs .= "  - {$id}\n";
        }
    }
    if (!empty($failedList)) {
        $logs .= "\n以下の resultId で abort に失敗しました:\n";
        foreach ($failedList as $info) {
            $logs .= "  - {$info}\n";
        }
    }

    // JSON でキーが "text" のみになるように整形
    $output = [
        "text" => $logs
    ];
    // 日本語をユニコードエスケープしない (JSON_UNESCAPED_UNICODE) なら画面上は日本語が可読形式で表示される
    echo json_encode($output, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

// 実行
abortWaitingCalls();


