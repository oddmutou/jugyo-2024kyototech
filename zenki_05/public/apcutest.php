<?php
// APCuでカウントを保存するキーを決める(APCuはkey/value形式で保存)
$apcu_key = 'access_counter';

// カウントを取得
$count = apcu_fetch($apcu_key);

// カウントの中身が数字ではない(初回は少なくともfalse)の場合は0にしてあげる
$count = is_numeric($count) ? $count : 0;

// カウントをインクリメント
$count++;

// カウントを保存
apcu_store($apcu_key, $count);
?>

あなたは <?= strval($count) ?> 人目の訪問者です!
