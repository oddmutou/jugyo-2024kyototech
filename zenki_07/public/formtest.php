<?php

if (isset($_POST['body'])) {
  // POSTで送られてくるフォームパラメータ body がある場合

  print('以下の内容を受け取りました!<br>');

  // 送られてきた内容を出力する際は必ず htmlspecialchars() を用いエスケープすること
  // XSS対策です。
  print(nl2br(htmlspecialchars($_POST['body'])));
}

?>

<!-- フォームのPOST先はこのファイル自身にする -->
<form method="POST" action="./formtest.php">
  <textarea name="body"></textarea>
  <button type="submit">送信</button>
</form>
