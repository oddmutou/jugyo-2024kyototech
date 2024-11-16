<?php
// 接続 (redisコンテナの6379番ポートに接続)
$redis = new Redis();
$redis->connect('redis', 6379);
// 投稿を保存するキーを決める
$key = 'bbs_kakikomi';
// 投稿をReidsから取得。なければ空文字列に。
$kakikomi = $redis->exists($key) ? $redis->get($key) : '';
if (!empty($_POST['kakikomi'])) { // 投稿されている場合は保存
  $kakikomi = $_POST['kakikomi'];
  $redis->set($key, strval($kakikomi));
    return header('Location: ./redis_bbs.php'); // 再読込でのPOST防止のリダイレクト
  }
?>
<form method="POST">
  <textarea name="kakikomi"></textarea><br>
  <button type="submit">更新</button>
</form>
<br>
<hr>
現在の内容<br>
<br>
<div><?= nl2br(htmlspecialchars($kakikomi)) ?></div>
