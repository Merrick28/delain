<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
$contenu_page .= '<script language="javascript">
function valide_form1() {
	if (document.attaquer1.ctl.value == 0)
	{
		document.attaquer1.ctl.value = 1;
		document.attaquer1.submit();
		return true;
	}
	else
	{
		document.location.replace(\'combat.php\');
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
		document.location.replace(\'combat.php\');
	}
}


function vued()
{
parent.droite.location.href="frame_vue.php";
}
</script>';
if(!isset($methode))
	$methode = "debut";
switch($methode)
{
	case 'debut':
		$req = "select * from auth.demande_temp
		join auth.appli on (appli_cod = dtemp_appli_cod)
			where dtemp_compt_cod = " . $compt_cod . "
			and not dtemp_valide";
		$db->query($req);
		if($db->nf() == 0)
		{
			$contenu_page .= "Vous n'avez aucune autorisation externe à gérer pour le moment.";
		}
		else
		{
			$contenu_page .= "Autorisations en attent pour : <br><table>";
			while($db->next_record())
			{
				$contenu_page .= "<tr><td>" . $db->f("appli_nom") . '</td><td><a href="' . $PHP_SELF . '?methode=suite&dtemp_cod=' . $db->f('dtemp_cod') . '&valide=o">Valider</a></td><td><a href="' . $PHP_SELF . '?methode=suite&dtemp_cod=' . $db->f('dtemp_cod') . '&valide=n">refuser</a></td></tr>';
			}
			$contenu_page .= '</table>';
		}
		break;
	case 'suite':
		if($valide == 'o')
		{
			$req = "update auth.demande_temp
				set dtemp_valide = true
				where dtemp_cod = " . $dtemp_cod;
			$db->query($req);
			$contenu_page .= "L'accès à cette application a bien été validé.";
		}
		if($valide == 'n')
		{
			$req = "delete from auth.demande_temp
				where dtemp_cod = " . $dtemp_cod;
			$db->query($req);
			$contenu_page .= "L'accès à cette application a bien été refusé.";
		}
		break;
}
$t->set_var('CONTENU_COLONNE_DROITE',$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
