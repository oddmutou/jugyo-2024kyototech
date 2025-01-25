<?php
session_start();
$dbh = new PDO('mysql:host=mysql;dbname=kyototech', 'root', '');

if (isset($_POST['body']) && !empty($_SESSION['login_user_id'])) {
  // POSTで送られてくるフォームパラメータ body がある かつ ログイン状態 の場合

  $image_filename = null;
  if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
    // アップロードされた画像がある場合
    if (preg_match('/^image\//', mime_content_type($_FILES['image']['tmp_name'])) !== 1) {
      // アップロードされたものが画像ではなかった場合
      header("HTTP/1.1 302 Found");
      header("Location: ./bbs.php");
      return;
    }
    // 元のファイル名から拡張子を取得
    $pathinfo = pathinfo($_FILES['image']['name']);
    $extension = $pathinfo['extension'];
    // 新しいファイル名を決める。他の投稿の画像ファイルと重複しないように時間+乱数で決める。
    $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.' . $extension;
    $filepath =  '/var/www/upload/image/' . $image_filename;
    move_uploaded_file($_FILES['image']['tmp_name'], $filepath);
  }

  // insertする
  $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (user_id, body, image_filename) VALUES (:user_id, :body, :image_filename);");
  $insert_sth->execute([
    ':user_id' => $_SESSION['login_user_id'], // ログインしている会員情報の主キー
    ':body' => $_POST['body'],
    ':image_filename' => $image_filename,
  ]);
  // 処理が終わったらリダイレクトする
  // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
  header("HTTP/1.1 302 Found");
  header("Location: ./bbs.php");
  return;
}
?>

<?php if(empty($_SESSION['login_user_id'])): ?>
  投稿するには<a href="/login.php">ログイン</a>が必要です。
<?php else: ?>
</div><a href="/icon.php">アイコン画像の設定はこちら</a>。</div>
<form method="POST" action="./bbs.php" enctype="multipart/form-data">
  <textarea name="body"></textarea>
  <div style="margin: 1em 0;">
    <input type="file" accept="image/*" name="image" id="imageInput">
  </div>
  <button type="submit">送信</button>
</form>
<?php endif; ?>

<hr>

<dl id="entryTemplate" style="display: none; margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
  <dt>番号</dt>
  <dd data-role="entryIdArea"></dd>
  <dt>投稿者</dt>
  <dd>
    <a href="" data-role="entryUserAnchor">
      <img data-role="entryUserIconImage"
        style="height: 2em; width: 2em; border-radius: 50%; object-fit: cover;">
      <span data-role="entryUserNameArea"></span>
    </a>
  </dd>
  <dt>日時</dt>
  <dd data-role="entryCreatedAtArea"></dd>
  <dt>内容</dt>
  <dd data-role="entryBodyArea">
  </dd>
</dl>
<div id="entriesRenderArea"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // 最後に描画した投稿のIDを保存しておく変数
  let lastRenderedEntryId = null;

  // 投稿を描画する関数
  const renderEntries = () => {
    const entryTemplate = document.getElementById('entryTemplate');
    const entriesRenderArea = document.getElementById('entriesRenderArea');
    const request = new XMLHttpRequest();
    request.onload = (event) => {
      const response = event.target.response;
      response.entries.forEach((entry) => {
        // テンプレートとするものから要素をコピー
        const entryCopied = entryTemplate.cloneNode(true);
        // display: none を display: block に書き換える
        entryCopied.style.display = 'block';

        // 番号(ID)を表示
        entryCopied.querySelector('[data-role="entryIdArea"]').innerText = entry.id.toString();

        // アイコン画像が存在する場合は表示 なければimg要素ごと非表示に
        if (entry.user_icon_file_url) {
          entryCopied.querySelector('[data-role="entryUserIconImage"]').src = entry.user_icon_file_url;
        } else {
          entryCopied.querySelector('[data-role="entryUserIconImage"]').style.display = 'none';
        }
        // 名前を表示
        entryCopied.querySelector('[data-role="entryUserNameArea"]').innerText = entry.user_name;

        // 投稿日時を表示
        entryCopied.querySelector('[data-role="entryCreatedAtArea"]').innerText = entry.created_at;

        // 本文を表示 (ここはHTMLなのでinnerHTMLで)
        entryCopied.querySelector('[data-role="entryBodyArea"]').innerHTML = entry.body;

        // 画像が存在する場合に本文の下部に画像を表示
        if (entry.image_file_url) {
          const imageElement = new Image();
          imageElement.src = entry.image_file_url; // 画像URLを設定
          imageElement.style.display = 'block'; // ブロック要素にする (img要素はデフォルトではインライン要素のため)
          imageElement.style.marginTop = '1em'; // 画像上部の余白を設定
          imageElement.style.maxHeight = '300px'; // 画像を表示する最大サイズ(縦)を設定
          imageElement.style.maxWidth = '300px'; // 画像を表示する最大サイズ(横)を設定
          entryCopied.querySelector('[data-role="entryBodyArea"]').appendChild(imageElement); // 本文エリアに画像を追加
        }

        // 最後に実際の描画を行う
        entriesRenderArea.appendChild(entryCopied);
      });

      // 最後に描画した投稿のIDを更新
      lastRenderedEntryId = response.last_rendered_entry_id;
      // 最後に描画した投稿がサーバー側にある最後の投稿と異なる(=まだ読み込む投稿がある)場合
      // 一番したの投稿までスクロールした場合に投稿を描画する関数を呼ぶ
      if (lastRenderedEntryId > response.last_entries_id) {
        let observer = new IntersectionObserver((entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              observer.unobserve(entry.target);
              renderEntries()
            }
          });
        }, {
          rootMargin: '0px',
          threshold: 1.0
        });
        observer.observe(entriesRenderArea.lastChild);
      }
    }

    // 最後に描画した投稿のIDが設定されていればそれをURLクエリパラメータに設定
    const requestPath = '/bbs_json.php' + (lastRenderedEntryId === null ? '' : '?last_id=' + lastRenderedEntryId.toString());
    request.open('GET', requestPath, true);
    request.responseType = 'json';
    request.send();
  }
  renderEntries();

  const imageInput = document.getElementById("imageInput");
  imageInput.addEventListener("change", () => {
    if (imageInput.files.length < 1) {
      // 未選択の場合
      return;
    }
    if (imageInput.files[0].size > 5 * 1024 * 1024) {
      // ファイルが5MBより多い場合
      alert("5MB以下のファイルを選択してください。");
      imageInput.value = "";
    }
  });
});
</script>
