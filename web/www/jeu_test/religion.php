<?php 
$param = new parametres();
include "blocks/_header_page_jeu.php";
ob_start();
$methode = get_request_var('methode', 'entree');
//
// on regarde si le joueur a bien le droit de venir ici
//
$erreur = 0;
$req = "select dper_dieu_cod, dper_niveau from dieu_perso where dper_perso_cod = $perso_cod and dper_niveau >= 2 ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
	echo "<p>Vous n’avez pas accès à cette page !";
	$erreur = 1;
}
else
{
	$result = $stmt->fetch();
	$dieu_cod = $result['dper_dieu_cod'];
	$niveau = $result['dper_niveau'];
}
if ($erreur == 0)
{

	$req = "select dieu_nom from dieu where dieu_cod = $dieu_cod ";
	$stmt = $pdo->query($req);
	$result = $stmt->fetch();
	echo "<p class=\"titre\">" , $result['dieu_nom'] , "</p>";

	if ($niveau > 3)
		echo '<p><a href="gerant_temple.php">Affectation des temples</a></p>';

	$req = "select tfid_perso_cod from temple_fidele where tfid_perso_cod = $perso_cod ";
	$stmt = $pdo->query($req);
	if ($stmt->rowCount() != 0)
		echo '<p><a href="gere_temple.php">Gestion de vos temples</a></p>';

	switch($methode)
	{
		case 'entree':
			if ($niveau >= 3)
			{
				echo "<a href=\"religion.php?methode=renegat\">Voir la liste des renégats</a><br>";
			}
			echo "<br><p><a href=\"action.php?methode=prie_ext&dieu=" , $dieu_cod , "\">Prier votre dieu ?</> (" , $param->getparm(48) , " PA)</p>";
			break;
		case 'renegat':
			echo "<p>Liste des renégats :<br>";
			$req = "select perso_nom,perso_cod from perso,dieu_renegat ";
			$req = $req . "where dren_dieu_cod = $dieu_cod ";
			$req = $req . "and dren_perso_cod = perso_cod  and dren_datfin > now()";
			$stmt = $pdo->query($req);
			if ($stmt->rowCount() == 0)
			{
				echo "<p>Aucun renégat à ce jour pour ce dieu.";
			}
			else
			{
				while ($result = $stmt->fetch())
				{
					echo $result['perso_nom'] , "<br>";	
				}
				
			}
			break;
	}
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
