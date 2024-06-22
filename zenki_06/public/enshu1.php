<?php
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');

// 1アクセスにつき1行insertすれば内容は何でも良い
$insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:text)");
$insert_sth->execute(array(
    ':text' => 'hello world!!!!!!!!!'
));

// 行数をカウント
$select_sth = $dbh->prepare('SELECT COUNT(*) FROM hogehoge');
$select_sth->execute();
$count = $select_sth->fetchColumn();
?>

現在のアクセス数は <?= $count ?> です。
