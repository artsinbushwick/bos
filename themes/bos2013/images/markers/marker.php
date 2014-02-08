<?php

if (preg_match('/(\d+)-(\d+)\.png$/', $_SERVER['REQUEST_URI'], $matches)) {
  list(, $width, $n) = $matches;
} else if (!empty($_GET['width']) && !empty($_GET['n'])) {
  $width = intval($_GET['width']);
  $n = intval($_GET['n']);
} else {
  die("Please specify 'width' and 'n' arguments");
}

$font = "helvetica-bold.ttf";

$size = 11;
if ($n > 99) {
  $size = 9;
}

$im = imagecreatefrompng("$width.png");
imagesavealpha($im, true);
$white = imagecolorallocate($im, 255, 255, 255);
$box = imagettfbbox($size, 0, $font, $n);
if ($width == 30) {
  imagettftext($im, $size, 0, 15 + ($box[0] - $box[2]) / 2, 19, $white, $font, $n);
} else if ($width == 40) {
  imagettftext($im, $size, 0, 19 + ($box[0] - $box[2]) / 2, 25, $white, $font, $n);
}

$filename = "$width-$n.png";
imagepng($im, $filename);
imagedestroy($im);

header('Content-Type: image/png');
echo file_get_contents($filename);

?>
