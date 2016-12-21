<?php 
include "verif_connexion.php";
include "img_pack.php";
include "template.inc";
// definition des variables de template
$t = new template;
$t->set_file("FileRef","../template/classic/general.tpl");
// chemins
$t->set_var("g_url",$type_flux.G_URL);
$t->set_var("g_che",G_CHE);
// scripts JS
$javascript = "<script language=\"javascript\"> 
function valide_form1() { 
	if (document.attaquer1.ctl.value == 0)
	{
		document.attaquer1.ctl.value = 1;
		document.attaquer1.submit();
		return true;
	}
	else
	{
		document.location.replace('combat.php');
	}
}
function valide_form2() { 
	if (document.attaquer2.ctl.value == 0)
	{
		document.attaquer2.ctl.value = 1;
		document.attaquer2.submit();
		return true;
	}
	else
	{
		document.location.replace('combat.php');
	}
}

function retour()
{
parent.gauche.location.href=\"menu.php\";
}
</script>";
$t->set_var("javascript",$javascript);
// fonction body onload
$action_onload = ' onload="retour();"';
$t->set_var("action_onload",$action_onload);
// chemin des images
$t->set_var("img_path",G_IMAGES);
// contenu de la page
$contenu_page = "";
if (!$db->is_admin($compt_cod))
{
	$limite_bonbon = $db->getparm_n(103);
	if ($db->compte_objet($perso_cod,448) < $limite_bonbon)
		$contenu_page = 'Vous devez avoir au moins ' . $limite_bonbon . ' bonbons pour pouvoir les donner.';
	else
	{
	$pa = $db->getparm_n(102);
	$req = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
	$db->query($req);
	$db->next_record();
	$pos = $db->f("ppos_pos_cod");
	$req = "select perso_cod,perso_nom  from perso,perso_position
		where perso_gmon_cod in (30,331)
		and perso_actif = 'O'
		and perso_cod = ppos_perso_cod
		and ppos_pos_cod = $pos ";
	$db->query($req);
	if ($db->nf() != 0)
	{
		$contenu_page .= '<p>Vous rencontrez des mosntres qui semblent intéresser par les bonbons que vous possédez. Si vous souhaitez donner des bonbons à un monstre, merci de le choisir ci dessous : (' . $pa . ' PA)</a>.';
		$contenu_page .= '<form method="post" action="action.php"><input type="hidden" name="methode" value="donne_bonbon">';
		while($db->next_record())
		{
			$contenu_page .= '<input type="radio" name="cible" value="' . $db->f('perso_cod') . '">' . $db->f("perso_nom") . '<br>';
		}
		$contenu_page .= '<center><input type="submit" value="Donner 5 bonbons"></center>';
	}
	else
	{
		$contenu_page .= 'Personne ne semble interessé par vos bonbons...';
	}
	}
}
else
{
	$contenu_page .= "<p>Vous ne pouvez pas valider des actions en étant administrateur !";
}
$t->set_var("contenu_page",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");