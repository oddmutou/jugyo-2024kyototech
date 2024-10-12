<?php
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');

if (isset($_POST['body'])) {
  // POSTで送られてくるフォームパラメータ body がある場合

  // insertする
  $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body) VALUES (:body)");
  $insert_sth->execute([
    ':body' => $_POST['body'],
  ]);

  // 処理が終わったらリダイレクトする
  // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
  header("HTTP/1.1 302 Found");
  header("Location: ./enshu2.php");
  return;
}

$select_sth = null;
if (isset($_GET['query'])) {
  // urlクエリパラメータ query がある場合
  $select_sth = $dbh->prepare('SELECT * FROM bbs_entries WHERE body LIKE :query ORDER BY created_at DESC');
  $select_sth->execute([
    ':query' => '%' . $_GET['query'] . '%',

  ]);
} else {
  // ない場合
  $select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');
  $select_sth->execute();
}
?>

<!-- フォームのPOST先はこのファイル自身にする -->
<form method="POST" action="./enshu2.php">
  <textarea name="body"></textarea>
  <button type="submit">送信</button>
</form>

<hr>

<form method="GET" action="./enshu2.php">
  <?php if(isset($_GET['query'])): ?>
    <input type="text" name="query" value="<?= $_GET['query'] ?>">
  <?php else: ?>
    <input type="text" name="query">
  <?php endif ?>
  <button type="submit">検索</button>
</form>
<?php if(isset($_GET['query'])): ?>
  現在「<?= $_GET['query'] ?>」で検索中。
  <a href="./enshu2.php">検索解除</a>
<?php endif; ?>

<hr>

<?php foreach($select_sth as $entry): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt>ID</dt>
    <dd><?= $entry['id'] ?></dd>
    <dt>日時</dt>
    <dd><?= $entry['created_at'] ?></dd>
    <dt>内容</dt>
    <dd><?= nl2br(htmlspecialchars($entry['body'])) // 必ず htmlspecialchars() すること ?></dd>
  </dl>
<?php endforeach ?>
