<?php
include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
if ($db->is_admin($compt_cod)) {
    echo "<p class=\"titre\">Ajouter un commentaire sur ce compte</p>";
    echo "<form name=\"comment\" action=\"valide_modif_detail_compte.php\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"methode\" value=\"comment\">";
    echo "<input type=\"hidden\" name=\"compte\" value=\"$compte\">";
    echo "<p>Entrez votre commentaire ci dessous : (la date, heure et auteur du message seront rajoutés automatiquement)<br>";
    echo "<textarea name=\"comment\" cols=\"50\" rows=\"20\"></textarea><br>";
    echo "<input type=\"submit\" class=\"test centrer\" value=\"Entrer !\">";
    echo "</form>";


} else {
    echo "<p>Erreur ! Vous n'êtes pas administrateur !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

