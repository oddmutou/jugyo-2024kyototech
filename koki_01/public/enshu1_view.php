<?php
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');
if (!isset($_GET['id'])) {
  // URLクエリパラメータ id がない場合
  return; // 処理終了
}
$id = intval($_GET['id']); // 表示したい投稿のID
$select_sth = $dbh->prepare('SELECT * FROM bbs_entries WHERE id = :id');
// 文字列ではなく数値をプレースホルダにバインドする場合は bindParam()
$select_sth->bindParam(':id', $id, PDO::PARAM_INT);
$select_sth->execute();
$entry = $select_sth->fetch();
if (!$entry) {
  // 投稿情報が取れなかった場合
  return; // 終了
}
?>
<a href="./enshu1.php">一覧に戻る</a>
<dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
  <dt>ID</dt>
  <dd><?= $entry['id'] ?></dd>
  <dt>日時</dt>
  <dd><?= $entry['created_at'] ?></dd>
  <dt>内容</dt>
  <dd><?= nl2br(htmlspecialchars($entry['body'])) // 必ず htmlspecialchars() すること ?></dd>
</dl>
