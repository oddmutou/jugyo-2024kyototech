<?php
session_start();

// ログインしていなければリダイレクト
if (empty($_SESSION['login_user_id'])) {
  header("HTTP/1.1 302 Found");
  header("Location: /login.php");
  return;
}

// DBに接続
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');
// セッションにあるログインIDから、ログインしている対象の会員情報を引く
$select_sth = $dbh->prepare("SELECT * FROM users WHERE id = :id");
$select_sth->execute([
    ':id' => $_SESSION['login_user_id'],
]);
$user = $select_sth->fetch();

if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
  // 画像アップロード処理
  if (preg_match('/^image\//', mime_content_type($_FILES['image']['tmp_name'])) !== 1) {
    // アップロードされたものが画像ではなかった場合
    header("HTTP/1.1 302 Found");
    header("Location: ./icon.php");
    return;
  }
  // 元のファイル名から拡張子を取得
  $pathinfo = pathinfo($_FILES['image']['name']);
  $extension = $pathinfo['extension'];
  // 新しいファイル名を決める。他の投稿の画像ファイルと重複しないように時間+乱数で決める。
  $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.' . $extension;
  $filepath =  '/var/www/upload/image/' . $image_filename;
  move_uploaded_file($_FILES['image']['tmp_name'], $filepath);

  // ログインしている会員情報の icon_filename カラムを更新する
  $update_sth = $dbh->prepare("UPDATE users SET icon_filename = :icon_filename WHERE id = :id");
  $update_sth->execute([
      ':id' => $user['id'],
      ':icon_filename' => $image_filename,
  ]);

  // 処理が終わったらリダイレクトする
  header("HTTP/1.1 302 Found");
  header("Location: ./icon.php");
  return;
}
?>
<a href="/bbs.php">掲示板に戻る</a>

<h1>アイコン画像設定/変更</h1>
<div>
  <?php if(empty($user['icon_filename'])): ?>
  現在未設定
  <?php else: ?>
  <img src="/image/<?= $user['icon_filename'] ?>"
    style="height: 5em; width: 5em; border-radius: 50%; object-fit: cover;">
  <?php endif; ?>
</div>

<form method="POST" enctype="multipart/form-data">
  <div style="margin: 1em 0;">
    <input type="file" accept="image/*" name="image">
  </div>
  <button type="submit">アップロード</button>
</form>
