<?php
// セッションIDの取得(なければ新規で作成&設定)
$session_cookie_name = 'session_id';
$session_id = $_COOKIE[$session_cookie_name] ?? base64_encode(random_bytes(64));
if (!isset($_COOKIE[$session_cookie_name])) {
    setcookie($session_cookie_name, $session_id);
}

// 接続 (redisコンテナの6379番ポートに接続)
$redis = new Redis();
$redis->connect('redis', 6379);

// redisにセッション変数を保存しておくキーを決めておきます。
$redis_session_key = "session-" . $session_id;

// 既にセッション変数(の配列)が何かしら格納されていればそれを，なければ空の配列を $session_values変数に保存。
$session_values = $redis->exists($redis_session_key)
    ? json_decode($redis->get($redis_session_key), true)
    : [];

$count = isset($session_values["count"]) ? intval($session_values["count"]) : 0;
$count++;
// 前回の訪問日時を $before_accessed_at として取り出しておく
$before_accessed_at = $session_values["accessed_at"] ?? '';

$session_values["count"] = strval($count);
// 訪問日時を現在日時で上書き
$session_values["accessed_at"] = date('Y/m/d h:i:s');
$redis->set($redis_session_key, json_encode($session_values));

print(strval($count) . "回目の訪問です。<br>");
// 前回の訪問日時 $before_accessed_at が空ではない場合 出力する
if (!empty($before_accessed_at)) {
  print("前回の訪問は " . $before_accessed_at . " でした。");
}
?>
