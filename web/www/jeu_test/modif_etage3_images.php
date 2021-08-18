<?php /* Affichage de tous les styles de murs et fonds */

include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur = 0;
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";
if ($erreur == 0)
{
    $baseimage = '../../images/res/';

    // On traite d'abord un eventuel upload de fichier (avatar du monstre) identique pour creation/modification
    if ( ($_FILES["image_file"]["tmp_name"] != ""))
    {
        $filename = $_FILES["image_file"]["name"];
        move_uploaded_file($_FILES["image_file"]["tmp_name"], $baseimage . '/' . $filename);
    } else if ($_POST["methode"] == "delete") {
        $filename = $_POST["fichier"] ;
        @unlink($baseimage . '/' . $filename);
    } else {
        $filename = "" ;
    }

    // ressources des images pour les admins
    echo 'Ajouter un fichier: 
            <form name="cre" method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'">
            <input type="file" name="image_file" accept="image/*">
            <input type="submit" class="test centrer"  value="Uploader l\'image !">
            </form><hr>';

	// browse files
    $files = preg_grep('/^([^.])/', scandir($baseimage));

    foreach ($files as $fichier)
    {
        $style = ($fichier == $filename ) ? "width: 100%; background-color: lightgray;" : "";
        echo '<div style="'.$style.' display:inline-flex"><form method="post">
                                    <input type="hidden" name="methode" value="delete">
                                    <input type="hidden" name="fichier" value="'.$fichier.'">
                                    <input style="margin-top: 10px;" type="submit" value="Supprimer">
                                </form>';
        echo "<img style=\"height :50px;\" src=\"/images/res/$fichier\">&nbsp;&nbsp;&nbsp;&nbsp;<a style=\"margin-top: 10px;\" target='_blank' href='/images/res/$fichier'>/images/res/$fichier</a><br></div><br>";
	}

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
