<?php
  $exif = exif_read_data('./images/oshiro.jpeg');
?>

<img src="./images/oshiro.jpeg" style="width: 200px"><br>
この画像のexif情報は以下の通りです。<br>
<?= nl2br(print_r($exif, true)); ?>
