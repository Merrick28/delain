<?php 
include "classes.php";
include 'includes/template.inc';
$t = new template;
$t->set_file('FileRef','template/delain/index.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
//
// identification
//
ob_start();
include G_CHE . "ident.php";
$ident = montre_formulaire_connexion($verif_auth);
ob_end_clean();
$t->set_var("IDENT",$ident);

//
// Récupération des paramètres
//
$tri = (isset($_GET['trier']));

//
//Contenu de la div de droite
//
$contenu_page = '';
$contenu_page .= '<p><a href="?trier=1">Lister les plus fréquemment affichés en premier</a> (Attention, les objets qu’aucun aventurier ne porte ne seront PAS listés, de même que les monstres non présents dans les souterrains.)</p>';

// OBJETS

$contenu_page .= '
    <h1 id="goto-objets">Images d’objets</h1>
';
$contenu_page .= '<p style="clear:right;"><a href="#goto-monstres">Voir les monstres</a></p>';

// Requête sur les objets 
if ($tri)
	$req_objets = 'select gobj_nom, gobj_image from objet_generique
		inner join objets on obj_gobj_cod = gobj_cod
		inner join perso_objets on perobj_obj_cod = obj_cod
		inner join perso on perso_cod = perobj_perso_cod
		where perso_type_perso = 1
		group by gobj_tobj_cod, gobj_nom, gobj_image
		order by count(*) desc, gobj_tobj_cod, gobj_nom';
else
	$req_objets = 'select gobj_nom, gobj_image from objet_generique order by gobj_tobj_cod, gobj_nom';
$db->query($req_objets);

// Boucle d’affichage des objets
$path = 'http://images.jdr-delain.net';
while ($db->next_record())
{
	$img = $db->f('gobj_image');
	$nom = $db->f('gobj_nom');
	$contenu_page .= "<div style='float:left; border: solid 1px black;'><img src='$path/$img' style='width:80px; height:80px;' /><br />$nom</div>";
}

// MONSTRES

$contenu_page .= '
	<h1 style="clear:both;" id="goto-monstres">Images de monstres</h1>
';
$contenu_page .= '<p style="clear:right;"><a href="#goto-objets">Voir les objets</a></p>';

// Requête sur les monstres 
if ($tri)
	$req_monstres = 'select gmon_nom, gmon_avatar, count(*) from monstre_generique
		inner join perso on perso_gmon_cod = gmon_cod
		group by gmon_race_cod, gmon_nom, gmon_avatar
		order by count(*) desc, gmon_race_cod, gmon_nom';
else
	$req_monstres = 'select gmon_nom, gmon_avatar from monstre_generique order by gmon_race_cod, gmon_nom';
$db->query($req_monstres);

// Boucle d’affichage des monstres
$path = 'http://images.jdr-delain.net/avatars';
while ($db->next_record())
{
	$img = $db->f('gmon_avatar');
	$nom = $db->f('gmon_nom');
	$contenu_page .= "<div style='float:left; border: solid 1px black;'><img src='$path/$img' style='width:80px; height:80px;' /><br />$nom</div>";
}
$contenu_page .= "<div style='clear:both;'>&nbsp;</div>";


$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
