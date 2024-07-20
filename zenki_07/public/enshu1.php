<?php
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');

if (isset($_POST['body'])) {
  // POSTで送られてくるフォームパラメータ body がある場合

  // hogehogeテーブルにINSERTする
  $insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:body)");
  $insert_sth->execute([
      ':body' => $_POST['body'],
  ]);

  // 処理が終わったらリダイレクトする
  // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
  header("HTTP/1.1 302 Found");
  header("Location: ./enshu1.php");
  return;
}

// hogehogeテーブルからデータを取得
$select_sth = $dbh->prepare('SELECT * FROM hogehoge ORDER BY created_at DESC');
$select_sth->execute();
?>

<!-- フォームのPOST先はこのファイル自身にする -->
<form method="POST" action="./enshu1.php">
  <textarea name="body"></textarea>
  <button type="submit">送信</button>
</form>

<hr style="margin: 3em 0;"></hr>

<?php foreach($select_sth as $row): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt>送信日時</dt>
    <dd><?= $row['created_at'] ?></dd>
    <dt>送信内容</dt>
    <dd><?= nl2br(htmlspecialchars($row['text'])) ?></dd>
  </dl>
<?php endforeach ?>
