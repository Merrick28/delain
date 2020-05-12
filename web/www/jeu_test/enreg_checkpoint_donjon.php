<?php
//if(!isset($db))
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso = $verif_connexion->perso;
// on regarde si le joueur est bien sur un point de passage

$type_lieu = 40;
$nom_lieu  = 'un point de passage';

include "blocks/_test_lieu.php";

if ($erreur == 0)
{

    $tab_lieu     = $perso->get_lieu();
    $nom_lieu     = $tab_lieu['lieu']->lieu_nom;
    $desc_lieu    = $tab_lieu['lieu']->lieu_description;
    $preg_pos_cod = "0";
    $ppos_pos_cod = "1";

    echo("<p><strong>$nom_lieu</strong> - $desc_lieu ");
    echo("<p> Vous voyez une pancarte, sur laquelle d'autres aventuriers ont déjà marqué leur passage.<br>");

    // Recherche d'une inscription dans les registres pour retour rapide en arene
    $req = "select preg_pos_cod, ppos_pos_cod, preg_date_inscription, 'X:' || pos_x::text || ' Y:' || pos_y::text || ' / ' ||etage_libelle as checkpoint  
              from perso_registre  
              join positions on pos_cod=preg_pos_cod 
              join etage on etage_numero=pos_etage 
              join perso_position on ppos_perso_cod=preg_perso_cod
              where preg_perso_cod=$perso_cod ";

    $db->query($req);
    if ($db->next_record())
    {
        $date_inscription = $db->f("preg_date_inscription");
        $checkpoint       = $db->f("checkpoint");
        $preg_pos_cod     = $db->f("preg_pos_cod");
        $ppos_pos_cod     = $db->f("ppos_pos_cod");
        if ($date_inscription != '')
        {
            echo "<br>Vous êtes bien inscrit(e) dans nos registres. <br>";
            if ($preg_pos_cod == $ppos_pos_cod)
            {
                echo "Si vous mourrez, au batiment administratif vous bénéficiez d'un retour rapide pour revenir directement ici (<strong> {$checkpoint}</strong>)<br>";
            } else
            {
                echo "Si vous mourrez, au batiment administratif vous bénéficiez d'un retour rapide pour aller:<strong> {$checkpoint}</strong><br>";
                echo "<br>Si vous préférez plutôt revenir ici, faites votre marque ici!<br>";
            }
        }
    }

    if ($preg_pos_cod != $ppos_pos_cod)
    {
        echo("<br><p><a href=\"action.php?methode=enreg_pos_donjon\">Graver une croix sur la pancarte?</a></p>");
    }

}