<?php
include "blocks/_header_page_jeu.php";
ob_start();


$type_lieu = 2;
$nom_lieu  = 'un dispensaire';

include "blocks/_test_lieu.php";

$perso = new perso;
$perso = $verif_connexion->perso;

$soins = $_REQUEST['soins'];
$soin  = $_REQUEST['soin'];

if ($erreur == 0)
{
    $erreur_soins = 0;
    $tab_temple   = $perso->get_lieu();
    $nom_lieu     = $tab_lieu['lieu']->lieu_nom;
    $type_lieu    = $tab_temple['lieu_type']->tlieu_libelle;
    echo("<p><img src=\"../images/temple.gif\"><strong>$nom_lieu</strong> - $type_lieu");
    $pv[1]     = 5;
    $pv[2]     = 10;
    $pv[3]     = 20;
    $cout[1]   = 20;
    $cout[2]   = 35;
    $cout[3]   = 60;
    $req_soins = "select temple_soins($perso_cod,$pv[$soins],$cout[$soins]) as soins";
    $stmt = $pdo->query($req_soins);
    $result = $stmt->fetch();
    if ($result['soins'] == 0)
    {
        if ($soin == "soin_male")
        {
            echo("<p>La jeune femme voluptueuse soigne votre corps avec douceur, durant un long moment qui vous semble un morceau de paradis dans l’enfer de ces Souterrains. ");
        } else
        {
            echo("<p>Le jeune homme de ses grandes mains vous soigne pourtant avec douceur, durant un long moment qui vous semble un morceau de paradis dans l’enfer de ces Souterrains. ");
        }
    } else
    {
        printf("<p>Une anomalie est survenue : <strong>%s</strong>", $result['soins']);
    }

		
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
