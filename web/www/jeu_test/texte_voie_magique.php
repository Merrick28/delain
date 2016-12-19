<?php $req = 'select count (1) as nv5 from perso, perso_nb_sorts_total, sorts where perso_cod = pnbst_perso_cod and pnbst_sort_cod = sort_cod and sort_niveau >= 5 and pnbst_nombre > 0 and perso_cod = ' . $perso_cod;
$db->query($req); $db->next_record();
$nv5 = $db->f('nv5');
$req = 'select count(1) as mem from perso_sorts, perso where psort_perso_cod = perso_cod and perso_type_perso = 1 and perso_cod = ' . $perso_cod;
$db->query($req); $db->next_record();
$mem = $db->f('mem');
if($nv5 > 0 && $mem > 5)
{
$db->query('select perso_voie_magique from perso
    where perso_voie_magique != 0 and perso_cod = ' . $perso_cod);
// Voie déjà choisie
if ($db->nf())
{
    $db->next_record();
    $db->query('select mvoie_libelle, mvoie_description from voie_magique
        where mvoie_cod = ' . $db->f('perso_voie_magique'));
    if (!$db->nf())
    {
        $contenu_page .= 'Vous avez choisi une voie magique, mais celle-ci n\'existe plus. Merci de signaler ce problème sur le forum.';
    }
    else
    {
        $db->next_record();
        $contenu_page .= 'Vous avez choisi la voie magique: <b>' .
            $db->f('mvoie_libelle') . '</b> dont la description est la suivante:<br />';
        $contenu_page .= $db->f('mvoie_description');
    }
}

// Proposition de choix de voie
else
{
    $contenu_page .= 'Pour accentuer votre maîtrise de la magie dans certains domaines, vous avez la possibilité de choisir, <b> de manière quasi définitive</b>, une voie de prédilection pour exercer votre art.';
    $db->query('select mvoie_cod, mvoie_libelle, mvoie_description from voie_magique order by mvoie_cod');
    $options = '<select name="voie">';
    while ($db->next_record())
    {
        $options .= '<option value="' . $db->f('mvoie_cod') . '">' . $db->f('mvoie_libelle') . '</option>';
        $contenu_page .= '<h3>' .  $db->f('mvoie_libelle') . '</h3>' .
            $db->f('mvoie_description');
    }
    $options .= '</select>';

    $contenu_page .= '<h2>Choisir une voie:</h2> <i>ATTENTION, il  sera  couteux (mais pas impossible) de changer de voie , réfléchissez-bien</i>';
    $contenu_page .= '<form action="action.php" method="post">
        <input type="hidden" name="methode" value="voie_magique">';
    $contenu_page .= $options;
    $contenu_page .= '<br><input type="submit" class="test" value="Choisir !"></form>';
}
} // Fin de vérification d'autorisation
else
{
    $contenu_page .= 'Mauvaise idée. C\'est pas joli tout ça...';
}

?>
