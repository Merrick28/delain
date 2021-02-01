<?php
include "blocks/_header_page_jeu.php";

$c            = new concentrations();
$is_concentre = false;
if (!$c->getByPerso($perso_cod))
{
    echo("<p>Vous n'avez effectué aucune concentration.</p>");
} else
{
    $is_concentre = true;
    echo "<p>Vous êtes concentré(e) pendant " . $c->concentration_nb_tours . " tours.";
}
echo("<p><div align=\"center\"><a href=\"valide_concentration.php\">Se concentrer ! (4 PA)</a></div></p>");
if ($is_concentre != 0)
{
    echo("<p><em>Attention !! Les concentrations ne se cumulent pas. Si vous vous concentrez de nouveau, la concentration précédente sera annulée !</em></p>");
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
