<?php
include "blocks/_header_page_jeu.php";
$contenu_page = '<p class="titre">Changement d’adresse électronique</p>';
$ok = 1;
if ($_POST['mail1'] != $_POST['mail2']) {
    $contenu_page .= '<p>Les deux adresses ne correspondent pas !</p>';
    $ok = 0;
}
$req = "select compt_cod from compte where compt_mail = '" . $_POST['mail1'] . "' and compt_cod != $compt_cod ";
$db->query($req);
if ($db->nf() != 0) {
    $ok = 0;
    $contenu_page .= '<p>Un autre compte existe déjà avec cette adresse !</p>';
}
if ($ok == 1) {
    $valide = validateEmail($_POST['mail1']);
    if (!$valide[0]) {
        $ok = 0;
        $contenu_page .= '<p>Adresse électronique non valide !</p>';
    }
}
if ($ok == 0) {
    $contenu_page .= '
	<p>Le changement d’adresse électronique n’a pas pu être fait.<br>
	<p class="text-align:center;"><a href="change_mail.php">Retour !</a></p>';
} else {
    $contenu_page .= '
	<form method="post" action="valide_change_mail2.php" name="final">
	<input type="hidden" name="mail1" value="' . $_POST['mail1'] . '">
	<p>Le changement est prêt à être effectué.<br>
	En cliquant sur <strong>j’accepte</strong>, je valide le changement, et je serai déconnecté du jeu jusqu’à réception du mail.
	<input type="submit" class="test centrer" value="J’accepte !">
	</form>';
}

include "blocks/_footer_page_jeu.php";
