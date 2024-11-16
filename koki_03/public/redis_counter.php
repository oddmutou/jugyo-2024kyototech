<?php
// 接続 (redisコンテナの6379番ポートに接続)
$redis = new Redis();
$redis->connect('redis', 6379);
// カウントを保存するキーを決める
$key = 'access_count';
// カウントをReidsから取得し整数値に。いま何も保存されていなければ0とする。
$count = $redis->exists($key) ? intval($redis->get($key)) : 0;
// カウントをインクリメント
$count++;
// カウントをRedisに文字列として保存
$redis->set($key, strval($count));
?>
現在のカウントは <?= $count ?> です。
