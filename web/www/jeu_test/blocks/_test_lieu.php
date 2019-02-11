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
    // 20190127 - Marlyza: $type_lieu peut-être un tableau si plusieurs lieu propose le service!
    if ((is_array($type_lieu) && !in_array($tab_lieu['type_lieu'], $type_lieu)) || (!is_array($type_lieu) && ($tab_lieu['type_lieu'] != $type_lieu)))
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
    $tlieu_cod = $tab_lieu['type_lieu'];
}