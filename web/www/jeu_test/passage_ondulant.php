<?php
if (!defined("APPEL"))
    die("Erreur d'appel de page !");

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = new perso;
$perso     = $verif_connexion->perso;
// on regarde si le joueur est bien sur un passage ondulant
$erreur = 0;
if (!$perso->is_lieu())
{
    echo("<p>Erreur ! Vous n'êtes pas sur un passage ondulant !!!");
    $erreur = 1;
}
if ($erreur == 0)
{
    $tab_lieu = $perso->get_lieu();
    if ($tab_lieu['lieu']->lieu_tlieu_cod != 29 and $tab_lieu['lieu']->lieu_tlieu_cod != 30)
    {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur un passage ondulant !!!");
    }
}

if ($erreur == 0)
{
    $nom_lieu  = $tab_lieu['lieu']->lieu_nom;
    $desc_lieu = $tab_lieu['lieu']->lieu_description;
    $cout_pa   = $tab_lieu['lieu']->lieu_prelev;
    $type_lieu = $tab_lieu['lieu']->lieu_tlieu_cod;
    echo("<p><strong>$nom_lieu</strong> - $desc_lieu ");
    echo("<p>Ce passage a quelque chose d’étrange, il ne semble pas constitué de la même manière que les passages magiques que vous connaissez.");
    if ($type_lieu == 29)
    {
        echo("<p><strong>Il semblerait bien que vous ne puissiez pas le prendre en étant tangible.</strong> Certainement une propriété de la matière qui pourrait vous empêcher de le prendre.");
    }
    echo("<p><a href=\"action.php?methode=passage\">Prendre ce passage ! (" . $cout_pa . " PA)</a></p>");
}