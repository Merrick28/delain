<?php
$target = '  target="_top"';

// changement de perso
if (isset($_REQUEST['perso']))
{
    $change_perso = $_REQUEST['perso'];
}

include_once "ident.php";
include_once "includes/classes.php";
if (!$verif_auth)
{
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}
// normalement, les objets $compte et $perso sont déjà chargés par la page ident.php

$frameless        = ($compte->compt_frameless == 'O');
$autorise_monstre = ($compte->autorise_4e_monstre() == 't');


include_once 'includes/template.inc';
$t = new template('jeu_test');
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
ob_start();
?>
    <script>
        function debug(str) {
            console.log(str)
        }
    </script>
<?php


$num_resultat = 0;
$db           = new base_delain;
$requete
              = "SELECT perso_cod, perso_nom, coalesce(perso_mortel, 'N') AS perso_mortel, 
			perso_dlt
		FROM perso WHERE perso_cod = " . $perso_cod;
$db->query($requete);
$num_resultat = $db->nf();

$nom          = $perso->perso_nom;
$perso_nom    = str_replace(chr(39), " ", $nom);
$maintenant   = date("d/m/Y H:i:s");
$perso_mortel = $perso->perso_mortel;
$num_perso    = $perso->perso_cod;
$perso_cod    = $num_perso;
$autorise     = 0;
$type_perso   = $perso->perso_type_perso;
if ($type_perso == 1 || ($type_perso == 2 && $autorise_monstre))
{
    // on va quand même charger le perso_compte
    $pcompt = new perso_compte();
    $tab    = $pcompt->getBy_pcompt_perso_cod($perso->perso_cod);
    if ($tab !== false)
    {
        // On a trouvé un perso_compte pour ce perso
        if ($tab[0]->pcompt_compt_cod == $compte->compt_cod)
        {
            // le compte compt_cod correspond au compt_cod courant, on autorise
            $autorise = 1;
        }
    }
}
elseif ($type_perso == 3)
{
    $pfam    = new perso_familier();
    $pcompt  = new perso_compte();
    $tab_fam = $pfam->getBy_pfam_familier_cod($perso->perso_cod);
    if ($tab_fam !== false)
    {
        // on est bien dans la table familiers
        $tab_pcompt = $pcompt->getBy_pcompt_perso_cod($tab_fam[0]->pfam_perso_cod);
        {
            if ($tab_pcompt !== false)
            {
                // on est bien dabs la table pcompte
                if ($tab_pcompt[0]->pcompt_compt_cod == $compte->compt_cod)
                {
                    // le compte compt_cod correspond au compt_cod courant, on autorise
                    $autorise = 1;
                }
            }
        }
    }
}
if ($autorise != 1)
    //
    // on va quand même vérifier que le compte n'est pas sitté
    //
{
    if ($type_perso == 1)
    {
        $cs = new compte_sitting();
        if ($cs->isSittingValide($compte->compt_cod, $perso->perso_cod))
        {
            $autorise = 1;
        }
    }
    elseif ($type_perso == 3)
    {
        $cs = new compte_sitting();
        if ($cs->isSittingFamilierValide($compte->compt_cod, $perso->perso_cod))
        {
            $autorise = 1;
        }
    }
}
if ($autorise == 1)
{
    $myAuth = new myauth;
    $myAuth->start();
    $myAuth->perso_cod = $num_perso;
    $tableau_numeros   = array();
    $tableau_noms      = array();
    if (isset($activeTout) && $activeTout == 1)
    {
        $pcompt = new perso_compte();
        // on prend tous les perso_compte du compte
        $tab_compte = $pcompt->getBy_pcompt_compt_cod($compte->compt_cod);
        foreach ($tab_compte as $dcompte)
        {
            $temp_perso = new perso;
            if ($temp_perso->charge($dcompte->pcompt_perso_cod))
            {
                // on charge le perso
                if ($temp_perso->perso_actif == 'O')
                {
                    // il est actif, on l'ajoute au tableau
                    $tableau_numeros[] = $temp_perso->perso_cod;
                    $tableau_noms[]    = $temp_perso->perso_nom;
                    // on regarde s'il y a un familier associé
                    $temp_fam = new perso_familier();
                    // on regarde si ce perso a un familier
                    $tab_fam = $temp_fam->getBy_pfam_perso_cod($temp_perso->perso_cod);
                    if ($tab_fam !== false)
                    {
                         // il a au moins un familier, on boucle
                        foreach ($tab_fam as $detail_fam)
                        {
                            $perso_fam = new perso;
                            if ($perso_fam->charge($detail_fam->pfam_familier_cod))
                            {
                                if ($perso_fam->perso_actif == 'O')
                                {
                                    // il est actif, on l'ajoute
                                    $tableau_numeros[] = $perso_fam->perso_cod;
                                    $tableau_noms[]    = $perso_fam->perso_nom;
                                }

                            }

                        }

                    }

                }

            }
        }
    }
    else
    {
        $tableau_numeros[] = $num_perso;
    }

    echo "<p>Identification réussie !</p><br /><br />";
    $premier_perso = true;

    foreach ($tableau_numeros as $key => $numero_perso)
    {
        $perso_dlt = new perso;
        $perso_dlt->charge($numero_perso);
        if (!$premier_perso)
        {
            echo '<hr />';
        }

        if (isset($tableau_noms[$key]))
        {
            echo "<p><b>Pour " . $tableau_noms[$key] . " :</b></p>";
        }

        // on passe la dlt
        echo $perso_dlt->calcul_dlt();
        $date_dlt = new DateTime($perso_dlt->perso_dlt);

        echo "<br>Votre nouvelle date limite de tour est : <b>" . $date_dlt->format('d/m/Y H:i:s') . "</b>";

        // on affichage le solde des points d'action
        echo "<br>Il vous reste " . $perso_dlt->perso_pa . " points d’action.";

        // on vérifie si une mission n’est pas validée
        $missions = $perso_dlt->missions();
        if ($missions !== '')
        {
            echo "<hr /><b>Évaluation de vos missions en cours</b><br />$missions<hr />";
        }


        // recherche des evts non lus
        // TODO: mettre les evts en crud
        $liste_evt = $perso_dlt->getEvtNonLu();

        // TODO : mettre des evts bidon pour tester
        if (count($liste_evt) != 0)
        {
            echo "<p style='margin-top:10px;'><b>Vos derniers événements importants :</b></p>";
            echo "<p>";
            $db_evt = new base_delain;
            foreach ($liste_evt as $detail_evt)
            {
                if (!empty($detail_evt->levt_attaquant != ''))
                {
                    $perso_attaquant = new perso;
                    $perso_attaquant->charge($detail_evt->levt_attaquant);
                }
                if (!empty($detail_evt->levt_cible != ''))
                {
                    $perso_cible = new perso;
                    $perso_cible->charge($detail_evt->levt_cible);
                }

                //$tab_nom_evt = pg_fetch_array($res_nom_evt,0);
                $texte_evt = str_replace('[perso_cod1]', "<b>" . $perso_dlt->perso_nom . "</b>", $detail_evt->levt_texte);
                if ($detail_evt->levt_attaquant != '')
                {
                    $texte_evt = str_replace('[attaquant]', "<b>" . $perso_attaquant->perso_nom . "</b>", $texte_evt);
                }
                if ($detail_evt->levt_cible != '')
                {
                    $texte_evt = str_replace('[cible]', "<b>" . $perso_cible->perso_nom . "</b>", $texte_evt);
                }
                $date_evt = new DateTime($detail_evt->levt_date);
                echo $date_evt->format('d/m/Y H:i:s') . " : " . $texte_evt . " (" . $detail_evt->tevt->tevt_libelle . ")<br />";
            }
            $perso_dlt->marqueEvtLus();
            //$req_raz_evt = "update ligne_evt set levt_lu = 'O' where levt_perso_cod1 = $numero_perso and levt_lu = 'N'";
            //$db->query($req_raz_evt);
        }
        $premier_perso = false;
    }
    // formulaire pour passer au jeu


    echo "<form name=\"ok\" method=\"post\" action=\"jeu_test/index.php\" target=\"_top\">";


    echo "<input type=\"hidden\" name=\"nom_perso\" value=\"$nom\">";
    if ($perso_mortel != 'M')
    {
        echo "<center><input type=\"submit\" value=\"Jouer !!\" class=\"test\"><br />";
        $checked = ($frameless) ? 'checked="checked"' : '';
        echo "<input type=\"hidden\" name=\"changed_frameless\" id='hidframeless' value=\"0\" />";
    }
    else
    {
        echo "<center><a href='validation_login2.php'>La vie continue... Retrouver mes autres persos !</a></center><br />";
    }
    echo "</form>";

    echo "<p style=\"text-align:center;\"><br /><i>Date et heure serveur : $maintenant</i></p>";
}
else
{
    echo "Accès refusé !";
}

echo '</div>';
?>

<?php


$contenu_page = ob_get_contents();
ob_end_clean();

if ($perso_mortel != 'M')
{
    // on va maintenant charger toutes les variables liées au menu
    // sauf si le perso est définitivement mort (sinon ça plante...)
    include_once('jeu_test/variables_menu.php');
}

$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse("Sortie", "FileRef");
$t->p("Sortie");

