<?php
include "blocks/_header_page_jeu.php";

$req_concentration = "select concentration_nb_tours from concentrations where concentration_perso_cod = $perso_cod";
$db->query($req_concentration);
$nb_concentration = $db->nf();

if ($nb_concentration == 0)
{
    echo("<p>Vous n'avez effectué aucune concentration.</p>");
} else
{
    $db->next_record();
    printf("<p>Vous êtes concentré(e) pendant %s tours.", $db->f("concentration_nb_tours"));
}
echo("<p><div align=\"center\"><a href=\"valide_concentration.php\">Se concentrer ! (4 PA)</a></div></p>");
if ($nb_concentration != 0)
{
    echo("<p><em>Attention !! Les concentrations ne se cumulent pas. Si vous vous concentrez de nouveau, la concentration précédente sera annulée !</em></p>");
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
