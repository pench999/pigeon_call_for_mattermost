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
    $authToken  = "token"; // 環境に合わせて書き換えてください

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
            throw new Exception("cURL Error (POST abort): " . curl_error($ch));
        }

        // レスポンスやステータスコードのチェック(必要に応じて)
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // ログ出力 (必要に応じて内容を変更)
        if ($httpCode === 200 || $httpCode === 201) {
        //  echo "Aborted call with resultId: {$rid}\n";
        echo json_encode(
        ["text" => "{$rid} 呼び出し待ちコールを中止しました。\n"],
        );
        } else {
        //  echo "Failed to abort call with resultId: {$rid}, HTTP Status: {$httpCode}\n";
        echo json_encode(
        ["text" => "{$rid} 中止に失敗しました。HTTP Status: {$httpCode}\n"],
        );
            // 実際にはエラー処理を追加しても良いでしょう
        }
    }
}

// 実行
abortWaitingCalls();

