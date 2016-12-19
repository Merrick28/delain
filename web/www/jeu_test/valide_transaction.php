<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
if ($_POST['type_a'] == 'o')
{
	if ($tran)
	{
		foreach($tran as $key=>$val)
		{
			/*controle de l'acheteur*/
			$req = "select tran_acheteur from transaction where tran_cod = $key";
			$db->query($req);
			$db->next_record();
			$acheteur = $db->f("tran_acheteur");
			if ($acheteur != $perso_cod)
			{
				echo"<p>Erreur ! Vous essayez de valider une transaction qui ne vous est pas destinée !";
				break;
			}
			$req_acc_tran = "select accepte_transaction($key) as resultat";
			$db->query($req_acc_tran);
			$db->next_record();
			$resultat_temp = $db->f("resultat");
			$tab_res = explode(";",$resultat_temp);
			if ($tab_res[0] == -1)
			{
				echo("<p>Une erreur est survenue : $tab_res[1]");
			}
			else
			{
				echo("<p>La transaction a été validée. L'objet se trouve maintenant dans votre inventaire.");
			}
		}
	}
	else
	{
		echo "<p>Aucune transaction cochée !";
	}
}
if ($_POST['type_a'] == 'n')
{
	if ($tran)
	{
		foreach($tran as $key=>$val)
		{
			$req_ref_tran = "delete from transaction where tran_cod = $key";
			if ($db->query($req_ref_tran))
			{
				echo "<p>La transaction a été annulée !";
			}
			else
			{
				echo "<p>Une erreur est survenue !";
			}
		}
	}
	else
	{
		echo "<p>Aucune transaction cochée !";
	}
}
?>
<br><br><a href="transactions2.php">Retour aux transactions</a>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
