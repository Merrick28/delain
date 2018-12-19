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
ob_start();
$erreur = 0;
if (!isset($temps_dlt))
{
	echo("<p>Vous devez saisir une valeur de temps !!");
	$erreur = 1;
}
if ($erreur == 0)
{
	if ($temps_dlt <= 0)
	{
		echo("<p>Vous ne pouvez pas saisir une valeur nulle ou négative !");
		$erreur = 1;
	}
}
if ($erreur == 0)
{
	if (!isset($methode))
	{
		$methode = "debut";
	}
	switch($methode)
	{
		case "debut":
				$req = "select to_char((perso_dlt + '$temps_dlt minutes'::interval),'DD/MM/YYYY à hh24:mi:ss') as nvdlt,
                               to_char(prochaine_dlt($perso_cod) + '$temps_dlt minutes'::interval,'DD/MM/YYYY à hh24:mi:ss') as nxtdlt
					from perso
					where perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				$nvdlt = $db->f('nvdlt');
                $nxtdlt = $db->f('nxtdlt');
				?>
				Etes vous sûr de vouloir décaler votre dlt de <?php echo $temps_dlt ?> minutes ? <br />
				Votre prochaine dlt commencera le <strong><?php echo $nvdlt;?></strong> <em>(la suivante le <strong><?php echo $nxtdlt;?></strong>)</em>
				<br><strong><a href="<?php echo $PHP_SELF;?>?methode=validation&temps_dlt=<?php echo $temps_dlt;?>">Oui</a>
			<br><br><a href="perso2.php">Non</a></strong>
				<?php 
		break;
		case "validation":
				$temps_dlt = round($temps_dlt);
				$req = "update perso set perso_dlt = perso_dlt + '$temps_dlt minutes'::interval where perso_cod = $perso_cod ";
				$db->query($req);

				echo("<p>Votre DLT a bien été repoussée de $temps_dlt minutes. ");
				$req2 = "select to_char(perso_dlt,'dd/mm/yyyy hh24:mi:ss') as dlt from perso where perso_cod = $perso_cod";
				$db->query($req2);
				$db->next_record();
				printf("<p>Votre nouvelle DLT est à <strong>%s</strong>.",$db->f("dlt"));
		break;
	}
}
echo("<p style=\"text-align:center\"><a href=\"perso2.php\">Retour</a>");



$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
