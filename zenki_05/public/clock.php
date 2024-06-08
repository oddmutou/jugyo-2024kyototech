<?php

$now = new \DateTime("now", new \DateTimeZone('Asia/Tokyo'));
?>
<!DOCTYPE html>
<head>
</head>
<body>
現在の日時は<br>
<?php echo($now->format('Y-m-d H:i:s')); ?>

<div style="margin-top: 5em;">

<?php
  $svg_area_size = 1000;
  $circle_r = 495;

  $seconds = intval($now->format('s'));
  $max_seconds = 60;
  $seconds_rad = ($seconds / $max_seconds) * 2 * pi();
  $seconds_x = ($svg_area_size / 2) + floor(sin($seconds_rad) * ($circle_r * 0.95));
  $seconds_y = ($svg_area_size / 2) - floor(cos($seconds_rad) * ($circle_r * 0.95));

  $minutes = intval($now->format('i'));
  $max_minutes = 60;
  $minutes_rad = ($minutes / $max_minutes) * 2 * pi();
  $minutes_x = ($svg_area_size / 2) + floor(sin($minutes_rad) * ($circle_r * 0.8));
  $minutes_y = ($svg_area_size / 2) - floor(cos($minutes_rad) * ($circle_r * 0.8));

  $hours = intval($now->format('h'));
  $max_hours = 12;
  // 時針は分も考慮する
  $hours_rad = (($hours + ($minutes / $max_minutes)) / $max_hours) * 2 * pi();
  $hours_x = ($svg_area_size / 2) + floor(sin($hours_rad) * ($circle_r * 0.5));
  $hours_y = ($svg_area_size / 2) - floor(cos($hours_rad) * ($circle_r * 0.5));
?>

<svg width="500" height="500" viewBox="0 0 1000 1000"
     xmlns="http://www.w3.org/2000/svg" version="1.1">

  <!-- 枠の円 -->
  <circle cx="<?= $svg_area_size / 2 ?>" cy="<?= $svg_area_size / 2 ?>"
    r="<?= $circle_r ?>" stroke="black" fill="white" stroke-width="5"/>

  <!-- マーカー -->
  <?php
    foreach(range(1, 60) as $marker):
    $marker_rad = ($marker / 60) * 2 * pi();
    $marker_x = ($svg_area_size / 2) + floor(sin($marker_rad) * ($circle_r * 0.95));
    $marker_y = ($svg_area_size / 2) - floor(cos($marker_rad) * ($circle_r * 0.95));
  ?>
    <?php if($marker % 5 === 0): ?>
      <circle cx="<?= $marker_x ?>" cy="<?= $marker_y ?>"
        r="10" stroke="gray" fill="gray" stroke-width="1"/>
    <?php else: ?>
      <circle cx="<?= $marker_x ?>" cy="<?= $marker_y ?>"
        r="3" stroke="gray" fill="gray" stroke-width="1"/>
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- 秒針 -->
  <g stroke="black">
    <line x1="<?= $svg_area_size / 2 ?>" y1="<?= $svg_area_size / 2 ?>"
      x2="<?= $seconds_x ?>" y2="<?= $seconds_y ?>" stroke-width="5"/>
  </g>

  <!-- 分針 -->
  <g stroke="black">
    <line x1="<?= $svg_area_size / 2 ?>" y1="<?= $svg_area_size / 2 ?>"
      x2="<?= $minutes_x ?>" y2="<?= $minutes_y ?>" stroke-width="10"/>
  </g>

  <!-- 時針 -->
  <g stroke="black">
    <line x1="<?= $svg_area_size / 2 ?>" y1="<?= $svg_area_size / 2 ?>"
      x2="<?= $hours_x ?>" y2="<?= $hours_y ?>" stroke-width="15"/>
  </g>

</svg>
</div>

</body>
