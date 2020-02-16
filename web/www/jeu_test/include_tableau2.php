<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include "../includes/constantes.php";

$is_attaque = 1;
//
/* position du joueur et autres données */
//

$requete = "select pos_x, pos_y, pos_etage, ppos_pos_cod, distance_vue($perso_cod) as distance_vue, etage_libelle, pgroupe_groupe_cod,
				type_arme($perso_cod) as type_arme, portee_attaque($perso_cod) as portee, valeur_bonus($perso_cod, 'DES') as desorientation
				FROM perso
				INNER JOIN perso_position ON ppos_perso_cod = perso_cod 
				INNER JOIN positions ON ppos_pos_cod = pos_cod 
				INNER JOIN etage ON etage_numero = pos_etage
				LEFT OUTER JOIN groupe_perso ON pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1
				where perso_cod = $perso_cod ";
$stmt    = $pdo->query($requete);
$result  = $stmt->fetch();

$portee         = ($result['distance_vue'] > $result['portee']) ? $result['portee'] : $result['distance_vue'];
$type_arme      = ($result['type_arme'] == 2) ? 2 : 1;
$desorientation = ($result['desorientation'] == 0);

$pos_cod      = $result['ppos_pos_cod'];
$x            = $result['pos_x'];
$y            = $result['pos_y'];
$etage        = $result['pos_etage'];
$lib_etage    = $result['etage_libelle'];
$distance_vue = $result['distance_vue'];
$coterie      = $result['pgroupe_groupe_cod'];


if (!isset($tab_vue)) $tab_vue = -1;
switch ($tab_vue)
{
    case "0":
        include "incl_vue_joueur.php";
        break;
    case "1":
        include "incl_vue_partisan.php";
        break;
    case "2":
        include "incl_vue_monstre.php";
        break;
    case "4":
        include "incl_vue_lieu.php";
        break;
    case "3":
        include "incl_vue_objet.php";
        break;
    case "5":
        include "incl_vue_joueur.php";
        include "incl_vue_partisan.php";
        include "incl_vue_monstre.php";
        include "incl_vue_objet.php";
        include "incl_vue_lieu.php";
        break;
    default:
        break;
}
echo "<script type='text/javascript'>tailleCadre();</script>";

