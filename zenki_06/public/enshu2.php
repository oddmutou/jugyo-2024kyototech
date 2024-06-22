<?php
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');

// アクセスログを保存
$insert_sth = $dbh->prepare("INSERT INTO access_logs (user_agent, remote_ip) VALUES (:user_agent, :remote_ip)");
$insert_sth->execute([
    ':user_agent' => $_SERVER['HTTP_USER_AGENT'],
    ':remote_ip' => $_SERVER["REMOTE_ADDR"],
]);

// アクセスログを取得
$select_sth = $dbh->prepare('SELECT * FROM access_logs ORDER BY created_at DESC');
$select_sth->execute();
?>

<div class="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
  <h1>アクセスログ</h1>
</div>

<?php foreach($select_sth as $log): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt>日時</dt>
    <dd><?= $log['created_at'] ?></dd>
    <dt>リモートIP</dt>
    <dd><?= $log['remote_ip'] ?></dd>
    <dt>UserAgent</dt>
    <dd><?= $log['user_agent'] ?></dd>
  </dl>
<?php endforeach ?>
