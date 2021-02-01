<?php
include "blocks/_header_page_jeu.php";
$visu = $_POST['visu'];
$req = "select perso_nom from perso where perso_cod = $visu ";
$stmt = $pdo->query($req);
$result = $stmt->fetch();
$nom_cible = $result['perso_nom'];
$contenu_page .= "<p>Etes-vous sur de vouloir déclencher une révolution contre <strong>" . $nom_cible . "</strong> ?<br>";
$contenu_page .= '
<form name="revolution" method="post" action="action.php">
<input type="hidden" name="methode" value="revolution">
<input type="hidden" name="cible" value="'. $visu.'">
<p style="text-align:center;"><a href="javascript:document.revolution.submit();">OUI, je veux renverser cet administrateur inique !</a><br>
<p style="text-align:center;"><a href="guilde.php">NON, il ne faut pas contrarier les admins, après ça tourne mal.</a><br>';

//
// génération du fichier final
//
include "blocks/_footer_page_jeu.php";
