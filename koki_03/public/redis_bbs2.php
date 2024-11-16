<?php
$redis = new Redis();
$redis->connect('redis', 6379);
// JSONに変換した投稿のリストを保存するキーを決める
$key = 'bbs_kakikomi_list_json';
// 投稿をReidsから取得してJSONからデコード。なければ空の配列に
$kakikomi_list = $redis->exists($key) ? json_decode($redis->get($key)) : [];
if (!empty($_POST['kakikomi'])) { // 投稿されている場合は保存
  $kakikomi = $_POST['kakikomi'];
  array_unshift($kakikomi_list, $kakikomi); // 投稿内容を kakikomi_list の先頭に加える
  $redis->set($key, json_encode($kakikomi_list)); // RedisにJSONにエンコードしてから保存
  return header('Location: ./redis_bbs2.php'); // 再読込でのPOST防止のリダイレクト
}
?>
<form method="POST">
  <textarea name="kakikomi"></textarea><br>
  <button type="submit">更新</button>
</form>
<br>
<hr>
<?php foreach($kakikomi_list as $kakikomi): ?>
<div>
  <br>
  <?= nl2br(htmlspecialchars($kakikomi)) ?><br>
  <br>
  <hr>
</div>
<?php endforeach; ?>
