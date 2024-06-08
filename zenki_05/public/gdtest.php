<?php
if (isset($_GET['color'])) {

  // 入力されたカラーコードを正規表現でRGBに分解
  preg_match('/#([0-f]{2})([0-f]{2})([0-f]{2})/', $_GET['color'], $matches);

  // RGBそれぞれを16進数から10進数に変換
  $red = hexdec($matches[1]);
  $green = hexdec($matches[2]);
  $blue = hexdec($matches[3]);


  // 縦横500pxの画像を作成
  $image = imagecreate(500, 500);
  // 入力された色を背景色として指定
  imagecolorallocate($image, $red, $green, $blue);

  // ヘッダと画像を出力して終了
  header('Content-Type: image/png');
  imagepng($image);

  return;
}
?>

色を選んで「決定」を押してね。<br>
<form method="GET">
  <input type="color" name="color" placeholder="#000000">
  <button type="submit">決定</button>
</form>
