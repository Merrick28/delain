<?php $req =
    'select count (1) as nv5 from perso, perso_nb_sorts_total, sorts where perso_cod = pnbst_perso_cod and pnbst_sort_cod = sort_cod and sort_niveau >= 5 and pnbst_nombre > 0 and perso_cod = ' . $perso_cod;
$stmt      = $pdo->query($req);
$result    = $stmt->fetch();
$nv5       = $result['nv5'];
$req       =
    'select count(1) as mem from perso_sorts, perso where psort_perso_cod = perso_cod and perso_type_perso = 1 and perso_cod = ' . $perso_cod;
$stmt      = $pdo->query($req);
$result    = $stmt->fetch();
$mem       = $result['mem'];
if ($nv5 > 0 && $mem > 5)
{
    $stmt = $pdo->query('select perso_voie_magique from perso
    where perso_voie_magique != 0 and perso_cod = ' . $perso_cod);
    // Voie déjà choisie
    if ($stmt->rowCount())
    {
        $result = $stmt->fetch();
        $stmt   = $pdo->query('select mvoie_libelle, mvoie_description from voie_magique
        where mvoie_cod = ' . $result['perso_voie_magique']);
        if (!$result = $stmt->fetch())
        {
            $contenu_page .= 'Vous avez choisi une voie magique, mais celle-ci n\'existe plus. Merci de signaler ce problème sur le forum.';
        } else
        {

            $contenu_page .= 'Vous avez choisi la voie magique: <strong>' .
                             $result['mvoie_libelle'] . '</strong> dont la description est la suivante:<br />';
            $contenu_page .= $result['mvoie_description'];
        }
    } // Proposition de choix de voie
    else
    {
        $contenu_page .= 'Pour accentuer votre maîtrise de la magie dans certains domaines, vous avez la possibilité de choisir, <strong> de manière quasi définitive</strong>, une voie de prédilection pour exercer votre art.';
        $stmt         =
            $pdo->query('select mvoie_cod, mvoie_libelle, mvoie_description from voie_magique order by mvoie_cod');
        $options      = '<select name="voie">';
        while ($result = $stmt->fetch())
        {
            $options      .= '<option value="' . $result['mvoie_cod'] . '">' . $result['mvoie_libelle'] . '</option>';
            $contenu_page .= '<h3>' . $result['mvoie_libelle'] . '</h3>' .
                             $result['mvoie_description'];
        }
        $options .= '</select>';

        $contenu_page .= '<h2>Choisir une voie:</h2> <em>ATTENTION, il  sera  couteux (mais pas impossible) de changer de voie , réfléchissez-bien</em>';
        $contenu_page .= '<form action="action.php" method="post">
        <input type="hidden" name="methode" value="voie_magique">';
        $contenu_page .= $options;
        $contenu_page .= '<br><input type="submit" class="test" value="Choisir !"></form>';
    }
} // Fin de vérification d'autorisation
else
{
    $contenu_page .= 'Vous ne pouvez pas accéder aux voies spécialisées. Il faut maîtriser au moins six sortilèges pour cela.';
}

