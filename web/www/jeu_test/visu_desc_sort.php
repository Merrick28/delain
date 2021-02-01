<?php
include "blocks/_header_page_jeu.php";
$sort_cod  = $_REQUEST['sort_cod'];
$erreur    = 0;
$req_lance =
    'select pnbst_cod from perso_nb_sorts_total where pnbst_perso_cod = ' . $perso_cod . ' and pnbst_sort_cod = ' . $sort_cod;
$stmt      = $pdo->query($req_lance);
$nb_lance  = $stmt->rowCount();

$req_memo =
    'select psort_cod from perso_sorts where psort_perso_cod = ' . $perso_cod . ' and psort_sort_cod = ' . $sort_cod;
$stmt     = $pdo->query($req_memo);
$nb_memo  = $stmt->rowCount();

if (($nb_memo == 0) && ($nb_lance == 0))
{
    $erreur = 1;
}
$req  = 'select dper_niveau,dper_dieu_cod from dieu_perso where dper_perso_cod = ' . $perso_cod;
$stmt = $pdo->query($req);
if ($stmt->rowCount() != 0)
{
    $result   = $stmt->fetch();
    $niveau   = $result['dper_niveau'] + 1;
    $dieu_cod = $result['dper_dieu_cod'];
    $req      = 'select dsort_sort_cod from dieu_sorts where dsort_sort_cod = ' . $sort_cod . '
		and dsort_dieu_cod = ' . $dieu_cod . '
		and dsort_niveau <= ' . $niveau;
    $stmt     = $pdo->query($req);
    if ($stmt->rowCount() != 0)
    {
        $erreur = 0;
    }

}
if ($erreur == 1)
{
    $contenu_page .= "<p>Vous ne pouvez pas consulter la description de ce sort, car vous ne l'avez jamais lancé et vous ne l'avez pas mémorisé !";
}
if (!isset($sort_cod))
{
    $contenu_page .= "<p>Une erreur est survenue pendant la recherche du sort !";
} else
{
    if ($erreur == 0)
    {
        $req    = 'select sort_nom,sort_description,sort_cout,sort_distance,sort_soi_meme,sort_monstre,sort_joueur,sort_case,sort_temps_recharge,comp_libelle
			from sorts,competences where sort_cod = ' . $sort_cod . ' and sort_comp_cod = comp_cod ';
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();

        $temps_min   = intval($result['sort_temps_recharge']);
        $temps_heure = intval($temps_min / 60);
        $temps_min   = $temps_min - (60 * $temps_heure);
        $temps_jours = intval($temps_heure / 24);
        $temps_heure = $temps_heure - (24 * $temps_jours);

        $temps = $temps_min . ' min';
        if ($temps_heure > 0)
        {
            $temps = $temps_heure . ' h ' . $temps;
        }
        if ($temps_jours > 0)
        {
            $temps = $temps_jours . ' jours ' . $temps;
        }

        $contenu_page .= '<p class="titre">' . $result['sort_nom'] . '</p>
			<strong>Cout en PA : </strong>' . $result['sort_cout'] . '<br>
			<strong>Compétence utilisée : </strong>' . $result['comp_libelle'] . '<br>
			<strong>Distance max. : </strong>' . $result['sort_distance'] . '<br>
			<strong>Temps min. entre deux lancements : </strong>' . $temps . '<br>
			<strong>Cibles possibles : </strong>';
        if ($result['sort_case'] == 'O')
            $contenu_page .= 'cases.<br>';
        else
        {
            $contenu_page .= '<ul>';
            if ($result['sort_soi_meme'] == 'O')
                $contenu_page .= '<li>Soi même</li>';
            if ($result['sort_joueur'] == 'O')
                $contenu_page .= '<li>Aventuriers</li>';
            if ($result['sort_monstre'] == 'O')
                $contenu_page .= '<li>Monstres</li>';
            $contenu_page .= '</ul>';
        }
        $contenu_page .= '<strong>Description	: </strong>' . $result['sort_description'] . '<br>';
        $req_rune     = "select gobj_nom_generique from objet_generique,sort_rune ";
        $req_rune     = $req_rune . "where srune_sort_cod = $sort_cod ";
        $req_rune     = $req_rune . "and srune_gobj_cod = gobj_cod ";
        $stmt         = $pdo->query($req_rune);
        $texte        = '';
        if ($stmt->rowCount() != 0)
        {
            while ($result = $stmt->fetch())
            {
                $texte = $texte . $result['gobj_nom_generique'] . ", ";
            }
            $contenu_page .= '<p style="text-align:center;"><strong>Runes : </strong>' . $texte . '</p>';
        }

    }
}
include "blocks/_footer_page_jeu.php";
