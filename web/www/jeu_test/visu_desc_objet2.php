<?php
define('APPEL', 1);
include "blocks/_header_page_jeu.php";
ob_start();
$objet = $_GET['objet'];
if (!preg_match('/^[0-9]*$/i', $objet))
{
    echo "<p>Anomalie sur numéro objet !";
    exit();
}
$autorise = 0;
$req      = "select perobj_cod from perso_objets,objets
	where perobj_perso_cod = $perso_cod
	and perobj_obj_cod = obj_cod
	and perobj_identifie = 'O' 
	and obj_gobj_cod = $objet ";
$stmt     = $pdo->query($req);
if ($stmt->rowCount() != 0)
    $autorise = 1;
// on regarde si l'objet est dans une échoppe sur laquelle on est

$perso = new perso;
$perso = $verif_connexion->perso;

if ($perso->is_lieu())
{
    $tab_lieu = $perso->get_lieu();
    $lieu_cod = $tab_lieu['lieu']->lieu_cod;
    $req      = "select mstock_obj_cod from stock_magasin,objets
		where mstock_lieu_cod = $lieu_cod
		and mstock_obj_cod = obj_cod
		and obj_gobj_cod = $objet";
    $stmt     = $pdo->query($req);
    if ($stmt->rowCount() != 0)
        $autorise = 1;

    $req  = "select mgstock_cod from  	stock_magasin_generique
		where mgstock_lieu_cod = $lieu_cod
		and mgstock_gobj_cod = $objet";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
        $autorise = 1;
}
if ($autorise == 1)
{
    $req = "select gobj_nom, gobj_tobj_cod, tobj_libelle, gobj_poids, gobj_pa_normal, gobj_pa_eclair, gobj_distance, gobj_deposable, gobj_comp_cod,
				gobj_description, gobj_seuil_dex, gobj_seuil_force, gobj_niveau_min 
			from objet_generique,type_objet 
			where gobj_cod = $objet 
				and gobj_tobj_cod = tobj_cod 
				and (gobj_visible is null or gobj_visible != 'N') ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
    {
        $affichage_plus = true;
        require "blocks/_visu_desc_objet.php";

    } else
    {
        echo "<p>Aucun objet trouvé !";
    }
} else
{
    echo "Vous n'avez pas accès au détail de cet objet !";
}
$retour  = "inventaire.php";
$origine = $_REQUEST['origine'];
if ($origine == 'e')
{
    $retour = "lieu.php?methode=acheter";
}
if ($origine == 'a')
{
    $retour = "admin_echoppe_tarif.php";
}
echo "<a class='centrer' href=\"$retour\">Retour !</a>";


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

