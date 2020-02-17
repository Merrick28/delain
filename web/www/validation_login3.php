<?php
$target = '  target="_top"';

// changement de perso
if (isset($_REQUEST['perso']))
{
    $change_perso = $_REQUEST['perso'];
}

$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
$compte     = $verif_connexion->compte;
$perso      = $verif_connexion->perso;
include_once "includes/classes.php";
if (!$verif_auth)
{
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}

$frameless        = ($compte->compt_frameless == 'O');
$autorise_monstre = ($compte->autorise_4e_monstre() == 't');


ob_start();
?>
    <script>
        function debug(str) {
            console.log(str)
        }
    </script>
<?php


require "_block_valide_autorise_joue_perso.php";
if ($autorise == 1)
{
    $myAuth = new myauth;
    $myAuth->start();
    $myAuth->perso_cod = $num_perso;
    $tableau_numeros   = array();
    $tableau_noms      = array();
    if (isset($_REQUEST['activeTout']) && $_REQUEST['activeTout'] == 1)
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
    } else
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
            echo "<p><strong>Pour " . $tableau_noms[$key] . " :</strong></p>";
        }

        $dlt1 = $perso_dlt->perso_dlt;      // memo de la dlt avant calcul

        // on passe la dlt
        echo $perso_dlt->calcul_dlt();
        $date_dlt = new DateTime($perso_dlt->perso_dlt);


        echo "<br>Votre nouvelle date limite de tour est : <strong>" . $date_dlt->format('d/m/Y H:i:s') . "</strong>";

        // on affichage le solde des points d'action
        echo "<br>Il vous reste " . $perso_dlt->perso_pa . " points d’action.";

        // on vérifie si une mission n’est pas validée
        $missions = $perso_dlt->missions();
        if ($missions !== '')
        {
            echo "<hr /><strong>Évaluation de vos missions en cours</strong><br />$missions<hr />";
        }

        // on vérifie s'il y a du nouveau dans les quetes-auto en cours.
        $quetes = $perso_dlt->quete_auto();
        if ($quetes !== '')
        {
            echo "<hr /><strong>Évaluation de vos quêtes en cours</strong><br />$quetes<hr />";
        }

        // recherche des evts non lus
        // TODO: mettre les evts en crud
        $liste_evt = $perso_dlt->getEvtNonLu();

        // TODO : mettre des evts bidon pour tester
        if (count($liste_evt) != 0)
        {
            echo "<p style='margin-top:10px;'><strong>Vos derniers événements importants :</strong></p>";
            echo "<p>";
            foreach ($liste_evt as $detail_evt)
            {
                require "_block_nouveaux_evts.php";
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
    } else
    {
        echo "<center><a href='validation_login2.php'>La vie continue... Retrouver mes autres persos !</a></center><br />";
    }
    echo "</form>";

    echo "<p style=\"text-align:center;\"><br /><em>Date et heure serveur : $maintenant</em></p>";
} else
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


$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'PERSO'        => $perso,
    'PHP_SELF'     => $_SERVER['PHP_SELF'],
    'CONTENU_PAGE' => $contenu_page

);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));