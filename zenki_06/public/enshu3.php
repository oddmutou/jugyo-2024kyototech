<?php
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');

// ページ数をURLクエリパラメータから取得。無い場合は1ページ目とみなす
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// 1ページあたりの行数を決める
$count_per_page = 10;

// ページ数に応じてスキップする行数を計算
$skip_count = $count_per_page * ($page - 1);

// 全行数取得
$count_sth = $dbh->prepare('SELECT COUNT(*) FROM access_logs;');
$count_sth->execute();
$count_all = $count_sth->fetchColumn();
if ($skip_count >= $count_all) {
    print('このページは存在しません!'); // スキップする行数が全行数より多かったらおかしいのでエラーメッセージ表示し終了
    return;
}

// アクセスログを取得
$select_sth = $dbh->prepare('SELECT * FROM access_logs ORDER BY created_at DESC LIMIT :count_per_page OFFSET :skip_count');
// 文字列ではなく数値をプレースホルダにバインドする場合は bindParam() を使い，第三引数にINTであることを伝えるための定数を渡す
$select_sth->bindParam(':count_per_page', $count_per_page, PDO::PARAM_INT);
$select_sth->bindParam(':skip_count', $skip_count, PDO::PARAM_INT);
$select_sth->execute();

?>

<div style="margin-bottom: 1em;">
  <h1 style="text-align: center;">アクセスログ閲覧</h1>
</div>

<div style="width: 100%; text-align: center; margin-bottom: 1.5em">
  <a href="./enshu2.php">アクセスログの保存は演習2のページで</a>
</div>

<div style="width: 100%; text-align: center; padding-top: 1em; border-top: 1px solid #ccc; margin-bottom: 0.5em">
  <?= $page ?>ページ目
  (全 <?= floor($count_all / $count_per_page) + 1 ?>ページ中)
</div>

<div style="display: flex; justify-content: center;">
  <div style="width: 100%; max-width: 1000px;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 2em;">
      <div>
        <?php if($page > 1): // 前のページがあれば表示 ?>
          <a href="?page=<?= $page - 1 ?>">前のページ</a>
        <?php endif; ?>
      </div>
      <div>
        <?php if($count_all > $page * $count_per_page): // 次のページがあれば表示 ?>
          <a href="?page=<?= $page + 1 ?>">次のページ</a>
        <?php endif; ?>
      </div>
    </div>
    <table style="border: none; table-layout: fixed; border-collapse: collapse;">
      <thead>
        <tr>
          <th style="width: 25%; padding: 0.5em; border-right: 1px solid #ccc;">日時</th>
          <th style="width: 25%; padding: 0.5em; border-right: 1px solid #ccc;">リモートIP</th>
          <th style="width: 50%; padding: 0.5em;">UserAgent</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($select_sth as $log): ?>
          <tr style="border-top: 1px solid #ccc;">
            <td style="padding: 0.5em; border-right: 1px solid #ccc;"><?= $log['created_at'] ?></td>
            <td style="padding: 0.5em; border-right: 1px solid #ccc;"><?= $log['remote_ip'] ?></td>
            <td style="padding: 0.5em;"><?= $log['user_agent'] ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
