<?php
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');
session_start();

$sql = 'SELECT bbs_entries.*, users.name AS user_name, users.icon_filename AS user_icon_filename'
  . ' FROM bbs_entries'
  . ' INNER JOIN users ON bbs_entries.user_id = users.id'
  . (isset($_GET['last_id']) ? ' WHERE bbs_entries.id < :last_id ' : '')
  . ' ORDER BY bbs_entries.id DESC'
  . ' LIMIT 10';
$sql_params = [];
if (isset($_GET['last_id'])) {
  $sql_params[':last_id'] = intval($_GET['last_id']);
}
$select_sth = $dbh->prepare($sql);
$select_sth->execute($sql_params);

$last_id_sql = 'SELECT bbs_entries.id'
  . ' FROM bbs_entries'
  . ' ORDER BY bbs_entries.id ASC'
  . ' LIMIT 1';
$last_id_select_sth = $dbh->prepare($last_id_sql);
$last_id_select_sth->execute();
$last_id_result = $last_id_select_sth->fetch();
$last_id = intval($last_id_result['id']);

// JSONに吐き出す用のArray
$result_entries = [];
$last_rendered_entry_id = null;
foreach ($select_sth as $entry) {
  $last_rendered_entry_id = $entry['id'];
  $result_entry = [
    'id' => $entry['id'],
    'user_name' => $entry['user_name'],
    'user_icon_file_url' => empty($entry['user_icon_filename']) ? '' : ('/image/' . $entry['user_icon_filename']),
    'body' => nl2br(htmlspecialchars($entry['body'])),
    'image_file_url' => empty($entry['image_filename']) ? '' : ('/image/' . $entry['image_filename']),
    'created_at' => $entry['created_at'],
  ];
  $result_entries[] = $result_entry;
}

header("HTTP/1.1 200 OK");
header("Content-Type: application/json");
print(json_encode([
  'entries' => $result_entries,
  'last_rendered_entry_id' => $last_rendered_entry_id,
  'last_entries_id' => $last_id,
]));
