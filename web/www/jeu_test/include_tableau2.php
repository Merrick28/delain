<?php include_once "verif_connexion.php";
include "../includes/constantes.php";
if (!isset($db))
    $db = new base_delain;
$db_refuge  = new base_delain;
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
$db->query($requete);
$db->next_record();

$portee         = ($db->f("distance_vue") > $db->f("portee")) ? $db->f("portee") : $db->f("distance_vue");
$type_arme      = ($db->f("type_arme") == 2) ? 2 : 1;
$desorientation = ($db->f("desorientation") == 0);

$pos_cod      = $db->f("ppos_pos_cod");
$x            = $db->f("pos_x");
$y            = $db->f("pos_y");
$etage        = $db->f("pos_etage");
$lib_etage    = $db->f("etage_libelle");
$distance_vue = $db->f("distance_vue");
$coterie      = $db->f("pgroupe_groupe_cod");

$db2 = new base_delain;
if (!isset($_REQUEST['tab_vue']))
{
    $tab_vue = -1;
} else
{
    $tab_vue = $_REQUEST['tab_vue'];
}
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

