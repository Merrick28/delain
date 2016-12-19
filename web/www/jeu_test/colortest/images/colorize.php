<?php 
function image_Epmty($dim){
	$image = imagecreatetruecolor($dim[0], $dim[1]) ; 
	$rose = imagecolorallocatealpha($image, 10, 0, 10,127); // Déclaration du rose.
	imagecolortransparent($image, $rose) ; // Le rose devient transparent. 
	imagefill ( $image , 0 , 0 , $rose );
	return $image;
}

function image_Load($url) {
	$src = imagecreatefrompng($url) ; 
	$dim = getimagesize($url) ;
	$img = image_Epmty($dim);
	imagecopy($img, $src, 0, 0, 0, 0, $dim[0], $dim[1]) ;
	return $img;
}


function image_Colorize($url, $rgb) {
	$src = imagecreatefrompng($url) ; 
	$dim = getimagesize($url) ;
	$img = image_Epmty($dim);
	imagecopy($img, $src, 0, 0, 0, 0, $dim[0], $dim[1]) ;

	imageTrueColorToPalette($img,true,256);
	  $numColors = imageColorsTotal($img);
	  // on détermine la couleur de luminosité max
	 /* $lmax = 0;
 	  for ($x = 0; $x < $numColors; $x++) {
		list($r,$g,$b,$a) = array_values(imageColorsForIndex($img,$x));
		//$lmax = ($r > $lmax)?$r:$lmax;
		//$lmax = ($v > $lmax)?$v:$lmax;
		//$lmax = ($b > $lmax)?$b:$lmax;
		$lmax = ($r + $v + $b)/3;
	  }*/
	  $lumscale = 170 / 240;
	  for ($x = 0; $x < $numColors; $x++) {
		list($r,$g,$b,$a) = array_values(imageColorsForIndex($img,$x));
		if($a == 0x00){
			$grayscale = ($r + $g + $b) / 3 / 0xff / $lumscale;
			imageColorSet($img,$x,
			  min($grayscale * $rgb[0],255),
			  min($grayscale * $rgb[1],255),
			  min($grayscale * $rgb[2],255)
			);
		}
	  }
	  $res = image_Epmty($dim) ;
	  imagecopy($res, $img, 0, 0, 0, 0, $dim[0], $dim[1]) ;
	  return $res;
}
function parseColor($colorAsString){
	$col = str_replace("#", '', $colorAsString);
	$colR = hexdec(substr($col,0,2));
    $colG = hexdec(substr($col,2,2));
    $colB = hexdec(substr($col,4,2));
	return array($colR,$colG,$colB);
}

//$color = array(0xff,0xaa,0x2a); // color to convert to
if(isset($_GET['col'])){
	$color = parseColor($_GET['col']);
} else {
	$color = parseColor("FFFFFF");
}

if(isset($_GET['col2'])){
	$color2 = parseColor($_GET['col2']);
} else {
	$color2 = parseColor("FFFFFF");
}

$url = 'elfe-F.png' ; // Image source. 
//$url2 = 'elfe-F-cheuveux.png';
$url2 = 'elfe-F-cheveux2.png';
$dim = getimagesize($url2) ;

$url3 = 'elfe-F-veste.png';
$dim3 = getimagesize($url3);


//$color = array(0xff,0x00,0x00);
//$image1 = image_colorize($url,$color);
$image1 = image_Load($url);
//$color = array(0xFF,0x00,0x00);

$image2 = image_colorize($url2,$color);
imagecopy($image1, $image2, 70,6, 0, 0, $dim[0], $dim[1]) ;


$image3 = image_colorize($url3,$color2);
imagecopy($image1, $image3, 0,0, 0, 0, $dim3[0], $dim3[1]) ;

header('Content-Type: image/png') ; 
imagepng($image1) ; // Finalisation de l'image. 
imagedestroy($image1) ;
imagedestroy($image2) ;
imagedestroy($image3) ;
?>
