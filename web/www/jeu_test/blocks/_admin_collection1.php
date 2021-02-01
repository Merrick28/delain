<?php

$texte_etat = '';
if ($result['ferme'] != 1)
{
    $toutesPassees = false;
}
if ($result['ouvert'] == 1)
{
    $texte_etat = ' (ouverte)';
}
if ($result['futur'] == 1)
{
    $texte_etat = ' (future)';
}
if ($result['ferme'] == 1)
{
    $texte_etat = ' (fermée)';
}
if ($ccol_cod == $result['ccol_cod'])
{
    echo "<p><strong><a href='?methode=collection_visu&ccol_cod=" . $result['ccol_cod'] . "'>" . $result['ccol_titre'] . "$texte_etat</a></strong></p>";
} else
{
    echo "<p><a href='?methode=collection_visu&ccol_cod=" . $result['ccol_cod'] . "'>" . $result['ccol_titre'] . "$texte_etat</a></p>";
}

