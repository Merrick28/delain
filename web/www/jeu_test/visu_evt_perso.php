<?php 
/****************************************/
/* EN TETES                             */
/****************************************/
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
// contenu de la page
$contenu_page = "";

if (!isset($visu))
    $visu = '';

$visu = str_replace(";", " ", $visu);
if (!preg_match('/^[0-9]+$/', $visu))
{
	echo "Anomalie sur numéro perso !";
	exit();
}
/*****************************/
/* GESTION DE LA DESCRIPTION */
/*****************************/
if(!isset($met))
	$met = 'vide';
if ($met == 'aff')
	$db->query('update compte set compt_vue_desc = 1 where compt_cod = ' . $compt_cod);
if ($met == 'masq')
	$db->query('update compte set compt_vue_desc = 0 where compt_cod = ' . $compt_cod);
$req = "select compt_vue_desc from compte where compt_cod = $compt_cod";
$db->query($req);
$db->next_record();
if ($db->f("compt_vue_desc") == 1)
{
	include "perso2_description.php";
	$req = 'select perso_nom from perso where perso_cod = ' . $visu;
	$db->query($req);
	$db->next_record();
	$contenu_page .= '<form name="message" method="post" action="messagerie2.php">
	<input type="hidden" name="m" value="2">
	<input type="hidden" name="n_dest" value="' . $db->f('perso_nom') . '">
	<input type="hidden" name="dmsg_cod">
	</form>
	<div style=text-align:center>
	<a href="javascript:document.message.submit();">Envoyer un message !</a><br>
	<a href="' . $PHP_SELF . '?met=masq&visu=' . $visu . '">Masquer la description ?</a></div>';

}
else
{
	$contenu_page .= '<center><a href="' . $PHP_SELF . '?met=aff&visu=' . $visu . '">Afficher la description ?</a></center>';
}
/****************************************/
/* CONTENU                              */
/****************************************/
$db_evt = new base_delain;
$db_detail = new base_delain;
if (!isset($pevt_start))
{
	$pevt_start = 0;
}
if ($pevt_start < 0)
{
	$pevt_start = 0;
}
$req_nom = "select perso_nom,race_nom,perso_sex from perso,race where perso_cod = $visu and perso_race_cod = race_cod";
$db->query($req_nom);
$db->next_record();
$nom = $db->f("perso_nom");
$race = $db->f("race_nom");
$sexe = $db->f("perso_sex");
$contenu_page .= '<center><table cellspacing="2">
	<tr>
	<td colspan="3" class="titre"><div class="titre">Evènements de ' . $nom  . '(' . $sexe  . ' - ' . $race . ')</div></td>
	</tr>';
if ($db->is_admin($compt_cod))
/****************/
/* Compte admin */
/****************/
{
	$req_evt = 'select levt_cod, to_char(levt_date,\'DD/MM/YYYY hh24:mi:ss\') as evt_date, tevt_libelle, levt_texte, 
			levt_perso_cod1, p0.perso_nom as nom1, 
			levt_attaquant, p1.perso_nom as nom2,
			levt_cible, p2.perso_nom as nom3
		from ligne_evt
		inner join type_evt ON tevt_cod = levt_tevt_cod
		inner join perso p0 ON p0.perso_cod = levt_perso_cod1
		left outer join perso p1 ON p1.perso_cod = levt_attaquant
		left outer join perso p2 ON p2.perso_cod = levt_cible
		where levt_perso_cod1 = ' . $visu . '
		order by levt_cod desc
		limit 20
		offset ' . $pevt_start;

	$db->query($req_evt);
	$contenu_page .= '<form name="visu_evt" method="post" action="visu_evt_perso.php">
	<input type="hidden" name="visu">';
	while($db->next_record())
	{
		$contenu_page .= '<tr>
			<td class="soustitre3">' . $db->f("evt_date") . '</td>
			<td class="soustitre3"><b>' . $db->f("tevt_libelle") . '</b></td>';
			
		$texte_evt = str_replace('[perso_cod1]',"<b><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_perso_cod1") . ";document.visu_evt.submit();\">". $db->f("nom1") ."</a></b>",$db->f("levt_texte"));
		if ($db->f("levt_attaquant") != '')
		{
			$texte_evt = str_replace('[attaquant]',"<b><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_attaquant") . ";document.visu_evt.submit();\">".$db->f("nom2")."</A></b>",$texte_evt);
		}
		if ($db->f("levt_cible") != '')
		{
			$texte_evt = str_replace('[cible]',"<b><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_cible") . ";document.visu_evt.submit();\">".$db->f("nom3")."</a></b>",$texte_evt);
		}

		$contenu_page .= '<td>' . $texte_evt . '</td></tr>';
	}
}

else
/********************************/
/* Compte normal                */
/********************************/
{
	$req_evt = 'select levt_cod, to_char(levt_date,\'DD/MM/YYYY hh24:mi:ss\') as evt_date, tevt_libelle, tevt_texte, 
			levt_perso_cod1, p0.perso_nom as nom1, 
			levt_attaquant, p1.perso_nom as nom2,
			levt_cible, p2.perso_nom as nom3
		from ligne_evt
		inner join type_evt ON tevt_cod = levt_tevt_cod
		inner join perso p0 ON p0.perso_cod = levt_perso_cod1
		left outer join perso p1 ON p1.perso_cod = levt_attaquant
		left outer join perso p2 ON p2.perso_cod = levt_cible
		where levt_perso_cod1 = ' . $visu . '
			and levt_visible = \'O\'
		order by levt_cod desc
		limit 20
		offset ' . $pevt_start;

	$db->query($req_evt);
	$contenu_page .= '<form name="form_visu" method="post" action="visu_evt_perso.php">
	<input type="hidden" name="visu" value="' . $visu . '">';
    $first = true;
    while($db->next_record())
    {
        // Ne pas afficher les effets automatiques s'ils sont la dernière action du perso (Pour ne pas signaler le début du tour)
        if ($first && 'Effet automatique' == $db->f("tevt_libelle"))
            continue;
        $first = false;
		$levt_attaquant = $db->f("levt_attaquant");
		$levt_cible = $db->f("levt_cible");
		$num_perso = $db->f("levt_perso_cod1");
		$texte = $db->f("tevt_texte");
		
		$contenu_page .= '<tr><td class="soustitre3">' .$db->f("evt_date") . '</td>
			<td class="soustitre3"><b>' . $db->f("tevt_libelle") . '</b></td>';


		$texte_evt = str_replace('[perso_cod1]',"<b><a href=\"javascript:document.form_visu.visu.value=$num_perso;document.form_visu.submit();\">". $db->f("nom1") ."</a></b>\n",$texte);
		if ($levt_attaquant != '')
		{
			$texte_evt = str_replace('[attaquant]',"<b><a href=\"javascript:document.form_visu.visu.value=$levt_attaquant;document.form_visu.submit();\">" . $db->f("nom2") . "</a></b>\n",$texte_evt);
		}
		if ($levt_cible != '')
		{
			$texte_evt = str_replace('[cible]',"<b><a href=\"javascript:document.form_visu.visu.value=$levt_cible;document.form_visu.submit();\">" . $db->f("nom3") . "</a></b>\n",$texte_evt);
		}
		$contenu_page .= '<td>' . $texte_evt . '</td></tr>';
	}
}
$contenu_page .= '<tr></form><td><form name="evt" method="post" action="visu_evt_perso.php"><input type="hidden" name="pevt_start">
	<input type="hidden" name="visu" value="' . $visu . '">';
if ($pevt_start != 0)
{
	$contenu_page .= '<div align="left"><a href="javascript:document.evt.pevt_start.value=' . $pevt_start . '-20;document.evt.submit();"><== Précédent</a></div>';
}
$contenu_page .= '</td><td></td>
	<td><div align="right"><a href="javascript:document.evt.pevt_start.value=' . $pevt_start . '+20;document.evt.submit();">Suivant ==></a></div></td>
	</tr></form></table></center>';
//$t->set_var("contenu_page",$contenu_page);
/********************/
/* Envoi du contenu */
/********************/
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p("Sortie");
?>
