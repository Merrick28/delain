<?php
//if(!isset($db))
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
// on regarde si le joueur est bien sur un point de passage

$type_lieu = 38;
$nom_lieu  = 'un point de passage';

include "blocks/_test_lieu.php";

if ($erreur == 0)
{

    $perso = new perso;
    $perso->charge($perso_cod);
    $tab_lieu  = $perso->get_lieu();
    $nom_lieu  = $tab_lieu['lieu']->lieu_nom;
    $desc_lieu = $tab_lieu['lieu']->lieu_description;
    echo("<p><strong>$nom_lieu</strong> - $desc_lieu ");

    // Recherche d'une inscription dans les registres pour retour rapide en arene
    $req = "select preg_date_inscription from perso_registre where preg_perso_cod=$perso_cod ";
    $stmt = $pdo->query($req);
    if($result = $stmt->fetch())
    {
        $date_inscription = $result['preg_date_inscription'];
        if ($date_inscription != '')
        {
            echo "<br>Vous vous êtes déjà inscrit(e) dans nos registres à la date du " . date("d/m/Y à H:i:s", strtotime($date_inscription)) . " <br><br>";
        }
    }


    echo("<p>Vous voyez un vieux registre poussiéreux.");
    echo("<p><a href=\"action.php?methode=enreg_pos_donjon\">Inscrire votre nom sur le registre?</a></p>");
}