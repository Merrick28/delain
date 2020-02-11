<?php
include "blocks/_header_page_jeu.php";
ob_start();
if ($db->is_admin($compt_cod))
{
	$req = "select compt_nom from compte where compt_cod = $compte ";
	$stmt = $pdo->query($req);
	$result = $stmt->fetch();
	echo "<p>Voulez vous vraiment invalider le compte <strong>" . $result['compt_nom'] . "</strong>? (Cette action est définitive, elle a comme effet de transformer tous les persos en monstres ";
	echo "et d'empêcher le login du fautif).";
	echo "<p><a href=\"valide_invalide_compte.php?compte=$compte\">OUI ! </a>";
	echo "<p><a href=\"detail_compte.php\">NON !</a>";
}
else
{
	echo "<p>Erreur ! Vous n'êtes pas administrateur !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
