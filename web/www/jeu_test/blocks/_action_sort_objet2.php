<?php
$verif_connexion::verif_appel();
$result       = $stmt->fetch();
$contenu_page .= $result['resultat'];

// on recharge le perso
$perso = $verif_connexion->perso;

// on regarde combien de sorts ont été lancé
$pnbs = new perso_nb_sorts();
$pnbs->getByPersoSort($perso_cod, $sort->sort_cod);

// bouton de relance
$sort_pa = $perso->get_cout_pa_magie($sort->sort_cod, $type_lance);
if ($perso->perso_pa >= $sort_pa && ($pnbs->pnbs_nombre < 2 || is_null($pnbs->pnbs_nombre)))
{
    $adds         =
        ($type_lance != 0) ? "" : "&fam_1=" . (1 * substr($sort->sort_combinaison, 0, 1)) . "&fam_2=" . (1 * substr($sort->sort_combinaison, 1, 1)) . "&fam_3=" . (1 * substr($sort->sort_combinaison, 2, 1)) . "&fam_4=" . (1 * substr($sort->sort_combinaison, 3, 1)) . "&fam_5=" . (1 * substr($sort->sort_combinaison, 4, 1)) . "&fam_6=" . (1 * substr($sort->sort_combinaison, 5, 1));
    $adds         .= ($type_lance != 5) ? "" : "&objsort_cod=" . $_REQUEST["objsort_cod"];
    $contenu_page .= '<br><br><a href="choix_sort.php?&sort=' . $sort_cod . '&type_lance=' . $type_lance . $adds . '" class="centrer">Relancer (' . $sort_pa . ' PA)</a></center>';
}

if ($type_lance == 5)
{   // On fait le menage maintenant que le sort a été lancé
    $req  = 'delete from objets_sorts_magie where objsortm_perso_cod = :perso_cod; ';
    $stmt = $pdo->prepare($req);
    $pdo->execute(array(':perso_cod' => $perso_cod), $stmt);
}
