<?php
include "blocks/_header_page_jeu.php";
ob_start();
include "constantes.php";

$db2 = new base_delain;
$erreur = 0;

if (!isset($type_lance))
{
    echo "<p>Erreur technique lors de l’appel du sort. Veuillez recommencer.</p>";
    $erreur = 1;
    $type_lance = -1;
}

if ($type_lance == 0) // runes
{
    $resultat = $fam_1 . $fam_2 . $fam_3 . $fam_4 . $fam_5 . $fam_6;
    $req_sort = "select sort_cod from sorts where sort_combinaison = '$resultat' ";
    $db = new base_delain;
    $db->query($req_sort);
    $nbr_sort = $db->nf();
    if ($nbr_sort == 0)
    {
        echo "<p>Vous ne vous sentez pas capable d'associer ces runes pour lancer un sort.";
        $erreur = 1;
    } else
    {
        $db->next_record();
        $sort_cod = $db->f("sort_cod");
        $req = "select pcomp_cod from perso_competences,sorts
										where sort_cod = $sort_cod
										and sort_comp_cod = pcomp_pcomp_cod
										and pcomp_perso_cod = $perso_cod ";
        $db->query($req);
        if ($db->nf() == 0)
        {
            echo "<p>Vous ne vous sentez pas capable d'associer ces runes pour lancer un sort.";
            $erreur = 1;
        }
    }
}
else
{
    //($type_lance == 1) // mémorisé
    //($type_lance == 2) // receptacle
    //($type_lance == 3) // divin
    //($type_lance == 4) // parchemin
    //($type_lance == 5) // objets magiques

    $sort_cod = $_REQUEST['sort'];
    if ($sort_cod == '')
    {
        echo "<p>Erreur sur le sort lancé !";
        $erreur = 1;
    }
    if (!isset($sort_cod))
    {
        echo "<p>Erreur sur le sort lancé !";
        $erreur = 1;
    }
}
if ($erreur == 0)
{
    /*************************/
    /* R E C E P T A C L E S */
    /*************************/
    $req = "select perso_nb_receptacle, perso_pa from perso where perso_cod = $perso_cod ";
    $db->query($req);
    $db->next_record();
    $nb_rec = $db->f("perso_nb_receptacle");
    $nb_pa = $db->f("perso_pa");
    $req = "select count(recsort_cod) as nombre from recsort where recsort_perso_cod = $perso_cod ";
    $db->query($req);
    $db->next_record();
    $nb_rec_utl = $db->f("nombre");

    if ($nb_rec_utl < $nb_rec)
    {
        if (($type_lance != 3) && ($type_lance != 4))
        {
            echo "<p><a href=\"action.php?methode=receptacle&sort=", $sort_cod, "&type_lance=", $type_lance, "\">Mettre ce sort dans un réceptacle ?</a>";
        }
    }
    $req = "select sort_aggressif, sort_distance, sort_soi_meme, sort_monstre, sort_joueur, sort_case, sort_nom, sort_fonction, sort_niveau
		from sorts where sort_cod = $sort_cod";
    $db2->query($req);
    $db2->next_record();
    $portee = $db2->f("sort_distance");
    $nom_sort = $db2->f("sort_nom");
    $sort_niveau = $db2->f("sort_niveau");
    /***********************/
    /* E N L U M I N U R E */
    /***********************/
    if (($type_lance != 3) && ($type_lance != 4))
    {
        $is_enlumineur_niv3 = $db->existe_competence($perso_cod, 93);
        $is_enlumineur_niv2 = $is_enlumineur_niv3 || $db->existe_competence($perso_cod, 92);
        $is_enlumineur_niv1 = $is_enlumineur_niv2 || $db->existe_competence($perso_cod, 91);
        $has_peau_niv1 = $db->compte_objet($perso_cod, 481) >= 1;
        $has_peau_niv2 = $db->compte_objet($perso_cod, 482) >= 1;
        $has_peau_niv3 = $db->compte_objet($perso_cod, 483) >= 1;
        $has_peau_niv4 = $db->compte_objet($perso_cod, 729) >= 1;

        if (($is_enlumineur_niv1 && $has_peau_niv1 && $sort_niveau <= 2)
            || ($is_enlumineur_niv1 && $has_peau_niv2 && $sort_niveau <= 3)
            || ($is_enlumineur_niv2 && $has_peau_niv3 && $sort_niveau <= 4)
            || ($is_enlumineur_niv3 && $has_peau_niv4 && $sort_niveau <= 5))
        {
            echo "<p><a href='action.php?methode=enluminure&sort=$sort_cod&type_lance=$type_lance'>Enluminer ce sort ?</a></p><br />";
        }
    }
    $req = "select pnbs_nombre
		from perso_nb_sorts
		where pnbs_sort_cod = " . $sort_cod . "
			and pnbs_perso_cod = " . $perso_cod;
    $db->query($req);
    if ($db->nf() != 0)
    {
        echo "<br /><p> Vous vous apprêtez à lancer le sort <strong>" . $nom_sort . "</strong>.<br></p>";
    }
    if ($db2->f("sort_case") == 'N')
    {
        $aggressif = $db2->f("sort_aggressif");
        $soi_meme = $db2->f("sort_soi_meme");
        $sort_joueur = $db2->f("sort_joueur");
        $sort_dieu = substr($db2->f("sort_fonction"), 0, 2);
        $type_cible = "0";
        if ($db2->f("sort_monstre") == 'O')
        {
            $type_cible = $type_cible . ",2,3";
        }
        if ($db2->f("sort_joueur") == 'O')
        {
            $type_cible = $type_cible . ",1";
        }
        include "include_magie.php";
    } else
    {
        include "incl_magie_case.php";
    }
}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";