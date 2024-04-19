<?php //header('Content-Type: text/plain');
//ini_set("display_errors","1");
//ini_set("display_startup_errors","1");
//ini_set("html_errors","1");
//ini_set("pgsql.ignore_notice","0");
//ini_set("error_reporting","E_ALL");
//error_reporting(E_ALL);
header("Content-type: image/png");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date dans le passé

function rotationPng($img, $angle=0)
{
    // On charge l'image
    $source = imagecreatefrompng($img);

    // on récupere la largeur et la hauteur de l'image source
    $largeur_source = imagesx($source);
    $hauteur_source = imagesy($source);

		/* Ajouté le 6/12/2010 par Maverick */
		// Convertion de la source en couleurs vraies, pour gérer la transparence lors de la rotation.
		$base = imagecreatetruecolor($largeur_source, $hauteur_source);
		imagecopy($base, $source, 0, 0, 0, 0, $largeur_source, $hauteur_source);
		imagedestroy($source);
		$source = $base;
		$base = NULL;
		/* Fin ajout */

    // on tourne l'image avec l'angle souhaité

    //$destination = imagerotate($source, $angle, -1);

		/* Correction le 6/12/2010 par Maverick */
    $destination = imagerotate($source, $angle, 0); // Couleur de fond en noir
		$bgcolor = imagecolorallocate($destination, 0, 0, 0);
		/* Fin correction */

    /*if(!$destination)
    {
    	die('arg...');
    }*/

    //$destination =  imagerotateEquivalent($source, $angle,  -1, 1);

    // on récupere la largeur et la hauteur de l'image destination (celle tournée)
    $largeur_destination = imagesx($destination);
    $hauteur_destination = imagesy($destination);

    // l'image a grandi, il faut resizer
    if ($largeur_destination > $largeur_source)
    {
        // on calcule les marges
        $debut_largeur = ($largeur_destination - $largeur_source)/2;
        $debut_hauteur = ($hauteur_destination - $hauteur_source)/2;

        // on crée la nouvelle image resizée
        $image_resized = imagecreatetruecolor( $largeur_source, $hauteur_source );

				/* Modifié le 6/12/2010 par Maverick (la transparence est gérée après la fusion des images) */
        // imagesavealpha($image_resized, true);
        // $trans_colour = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        // imagefill($image_resized, 0, 0, $trans_colour);
				/* Fin modif */

        imagecopy($image_resized, $destination, 0, 0, $debut_largeur, $debut_hauteur, $largeur_source, $hauteur_source);
        $destination = $image_resized;
    }

		/* Ajouté le 6/12/2010 par Maverick */
		imagecolortransparent($destination, $bgcolor); // Mise en transparence du noir
		/* Fin ajout */

    return $destination;
}

//$img = "/home/delain/public_html/images/aiguille1.png";

/* Ajouté le 6/12/2010 par Maverick (changement de serveur) */
$dirimg = str_replace( "/jeu_test", "/images/", dirname(__FILE__) );
$img = $dirimg.'aiguille1.png';
/* Fin ajout */

$output = rotationPng($img, $angle);
//print_r($output);

// On affiche l'image de destination
imagepng($output);
imagedestroy($output);
