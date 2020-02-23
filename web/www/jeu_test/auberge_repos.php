<?php
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
ob_start();
// on regarde si le joueur est bien sur une banque
$type_lieu = 4;
$nom_lieu  = 'une auberge';

define('APPEL', 1);
include "blocks/_test_lieu.php";

if ($erreur == 0)
{

    $nb_pa = $perso->perso_pa;
    $prix  = $nb_pa * 2;
    $sexe  = $perso->perso_sex;

    if ($perso->perso_po < $prix)
    {
        echo("<p>Vous savez, $nom_sexe[$sexe], n'ous n'apprécions pas vraiment le genre de personnes qui n'ont pas de quoi payer ce qu'elles demandent.<br />");
        echo("Revenez quand vous poches seront plus pleines, ou bien allez dormir dehors, au milieu des monstres.");
        $erreur = 1;
    } else
    {
        $gain_pv = $nb_pa * 1.5;
        $gain_pv = round($gain_pv);
        $diff_pv = $perso->perso_pv_max - $perso->perso_pv;
        if ($gain_pv > $diff_pv)
        {
            $gain_pv = $diff_pv;
        }
        $perso->perso_pv = $perso->perso_pv + $gain_pv;
        $perso->perso_pa = 0;
        $perso->perso_po = $perso->perso_po - $prix;
        $perso->stocke();

        echo("<p>Vous vous êtes bien reposé. Vous avez regagné <strong>$gain_pv</strong> PV");

    }
    echo("<p><a href=\"auberge.php\">Retour</a>");


}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
