<?php 


function image_colorize(&$img,$rgb) {
  imageTrueColorToPalette($img,true,256);
  $numColors = imageColorsTotal($img);

  for ($x = 0; $x < $numColors; $x++) {
    list($r,$g,$b) = array_values(imageColorsForIndex($img,$x));

    // calculate grayscale in percent
    $grayscale = ($r + $g + $b) / 3 / 0xff;

    imageColorSet($img,$x,
      $grayscale * $rgb[0],
      $grayscale * $rgb[1],
      $grayscale * $rgb[2]
    );
  }
  return true;
}

function parseColor($colorAsString){
	$col = str_replace("#", '', $colorAsString);
	$colR = hexdec(substr($col,0,2));
    $colG = hexdec(substr($col,2,2));
    $colB = hexdec(substr($col,4,2));
	return array($colR,$colG,$colB);
}

//$color = array(0xff,0xaa,0x2a); // color to convert to
$color = parseColor($_GET['col']);

$image = $_GET['img'];

header("Content-type: image/png");
//$string = $_GET['text'];
$im     = imagecreatefrompng($image);
//$orange = imagecolorallocate($im, 220, 210, 60);
//$px     = (imagesx($im) - 7.5 * strlen($string)) / 2;
//imagestring($im, 3, $px, 9, $string, $orange);

//image_colorize($im,$color);
imagepng($im);
imagedestroy($im);

?>
