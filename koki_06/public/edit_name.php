<?php
session_start();

// セッションにログインIDが無ければ (=ログインされていない状態であれば) ログイン画面にリダイレクトさせる
if (empty($_SESSION['login_user_id'])) {
  header("HTTP/1.1 302 Found");
  header("Location: ./login.php");
  return;
}

// DBに接続
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');
// セッションにあるログインIDから、ログインしている対象の会員情報を引く
$insert_sth = $dbh->prepare("SELECT * FROM users WHERE id = :id");
$insert_sth->execute([
    ':id' => $_SESSION['login_user_id'],
]);
$user = $insert_sth->fetch();

if (isset($_POST['name'])) {
  // フォームから name が送信されてきた場合の処理

  // ログインしている会員情報のnameカラムを更新する
  $insert_sth = $dbh->prepare("UPDATE users SET name = :name WHERE id = :id");
  $insert_sth->execute([
    ':id' => $user['id'],
    ':name' => $_POST['name'],
  ]);
  // 成功したら成功したことを示すクエリパラメータつきのURLにリダイレクト
  header("HTTP/1.1 302 Found");
  header("Location: ./edit_name.php?success=1");
  return;
}
?>

<h1>名前変更</h1>
<form method="POST">
  <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>">
  <button type="submit">決定</button>
</form>

<?php if(!empty($_GET['success'])): ?>
<div>
  名前の変更処理が完了しました。
</div>
<?php endif; ?>
