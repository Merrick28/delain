<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 19/12/18
 * Time: 20:37
 */
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
    $erreur = 1;
    if ($use_contenu_page)
    {
        $contenu_page = "<p>Erreur ! Vous n'êtes pas sur " . $nom_lieu . "!";
    }
    else
    {
        echo "<p>Erreur ! Vous n'êtes pas sur " . $nom_lieu . "!";
    }
}
if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    if ($tab_lieu['type_lieu'] != $type_lieu)
    {
        $erreur = 1;
        if ($use_contenu_page)
        {
            $contenu_page = "<p>Erreur ! Vous n'êtes pas sur " . $nom_lieu . "!";
        }
        else
        {
            echo "<p>Erreur ! Vous n'êtes pas sur " . $nom_lieu . "!";
        }
    }
    $lieu_cod = $tab_lieu['lieu_cod'];
}