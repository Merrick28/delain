<?php
include_once "includes/classes.php";
$verif_auth = false;
include G_CHE . "ident.php";

include_once "includes/constantes.php";
include_once "includes/fonctions.php";

require_once CHEMIN . '../includes/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(CHEMIN . '/../templates');

$twig     = new Twig_Environment($loader, array());
$template = $twig->loadTemplate('validation_login2.twig');

if ($verif_auth)
{
    $compte->compt_der_connex = date('Y-m-d H:i:s');
    $compte->stocke();
    $evt_monstre = array();
    if ($is_admin_monstre === true)
    {
        // TODO : mettre en template
        echo("<html><head>");
        ?>
        <link rel="stylesheet" type="text/css" href="style.css" title="essai">
        <?php
        echo("</head>");
        echo '<body background="images/fond5.gif" onload="retour();">';

        echo '<div class="bordiv">';
        $admin  = 'O';
        $chemin = 'jeu_test';
        include "jeu_test/switch_monstre.php";
        echo '</div></body></html>';
        die('');
    }
    if ($is_admin === true)
    {
        // TODO : mettre en template
        echo("<html><head>");
        ?>
        <link rel="stylesheet" type="text/css" href="style.css" title="essai">
        <?php
        echo("</head>");
        echo '<body background="images/fond5.gif" onload="retour();">';

        echo '<div class="bordiv">';
        $is_admin = true;
        ?>
        <style>
            #formulaire {
                padding: 5px;
                margin: 10px 0 0 10px;
                border: 1px dashed #999;
                width: 590px;
            }

            #formulaire fieldset {
                border: 0;
                margin: 0;
                padding: 0;
            }

            #formulaire fieldset label {
                display: block;
            }

            #formulaire legend {
                margin: 0 0 5px;
            }

            #formulaire p {
                display: block;
                padding: 5px 0 0;
                margin: 10px 0 0;
                width: 580px;
            }

            #zoneResultats {
                border: 1px solid #000;
                background-color: #fff;
                display: block;
                overflow: auto;
                margin-left: 200px;
                padding: 0;
                position: absolute;
                width: 400px;
            }

            #zoneResultats li {
                background: #fff;
                display: block;
                margin: 0;
                padding: 0;
            }

            #zoneResultats li a {
                display: block;
                padding: 2px;
                text-decoration: none;
            }

            #zoneResultats li a:hover {
                background-color: #ffffc0;
            }

            input {
                margin: 0;
            }
        </style>
        <?php
        include 'sadmin.php';
        echo "<form name=\"login2\" method=\"post\" id=\"login2\" action=\"jeu_test/index.php\" target=\"_top\">";
        echo "<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">";
        echo "<input type=\"hidden\" name=\"idsessadm\" value=\"$compt_cod\">";
        echo "<p>Entrez directement le numéro de perso : <input type=\"text\" name=\"num_perso\"> <input type=\"submit\" value=\"Voir !\" class=\"test\">";
        echo '<p>Tapez un nom de perso pour trouver son numéro :
				<input type="text" name="foo" id="foo" value="" onkeyup="loadData();document.getElementById(\'zoneResultats\').style.visibility = \'hidden\'" />
				<ul id="zoneResultats" style="visibility: hidden;"></ul>';
        echo "";
        echo "</form>";
        echo '</div></body></html>';
        die('');
    }
    if ($type_perso == 'joueur')
    {
        // on calcule la dernière news
        $news                     = new news();
        $tab_last_news            = $news->getNews(0);
        $last_news                = $tab_last_news[0];
        $news_cod                 = $last_news->news_cod;
        $ok_4                     = $compte->autorise_4e_global();
        $attribue_nouveau_monstre = false;
        $affiche_news             = array();
        if ($compte->compt_der_news < $news_cod)
        {
            $affiche_news           = $news->getNewsSup($compte->compt_der_news);
            $compte->compt_der_news = $news_cod;
            $compte->stocke();
        }

        // Récupération du numéro du monstre actuel, s'il existe.
        $monstre_cod    = 0;
        $monstre_joueur = $compte->getMonstreJoueur();
        if ($monstre_joueur !== false)
        {
            $monstre_temp = new perso;
            $monstre_temp->charge($monstre_joueur);
            $monstre_cod = $monstre_temp->perso_cod;
        }
        $nv_monstre = $compte->attribue_monstre_4e_perso();
        if ($nv_monstre != -1)
        {

            // on a un monstre_cod > 0, donc il y avait un monstre, mais il a été remplacé
            // puisque nv_monstre == true
            if ($monstre_cod > 0)
            {
                // on récupère les événements du monstre pour les afficher.

                $ancien_monstre = new perso;
                $ancien_monstre->charge($monstre_cod);
                $liste_evt = $ancien_monstre->getEvtNonLu();

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
                    $texte_evt = str_replace('[perso_cod1]', "<b>" . $ancien_monstre->perso_nom . "</b>", $detail_evt->levt_texte);
                    if ($detail_evt->levt_attaquant != '')
                    {
                        $texte_evt = str_replace('[attaquant]', "<b>" . $perso_attaquant->perso_nom . "</b>", $texte_evt);
                    }
                    if ($detail_evt->levt_cible != '')
                    {
                        $texte_evt = str_replace('[cible]', "<b>" . $perso_cible->perso_nom . "</b>", $texte_evt);
                    }
                    $date_evt      = new DateTime($detail_evt->levt_date);
                    $evt_monstre[] = $date_evt->format('d/m/Y H:i:s') . " : " . $texte_evt . " (" . $detail_evt->tevt->tevt_libelle . ")<br />";
                }
                // On relâche le monstre du compte du joueur
                $ancien_monstre->relache_monstre_4e_perso();
                $ancien_monstre->marqueEvtLus();
                $attribue_nouveau_monstre = true;
            }
        }

        $ok_4 = $compte->autorise_4e_global();

        // on efface l'hibernation si il en reste
        if ($compte->compt_hibernation == 'T')
        {
            $compte->fin_hibernation();
        }

        $persos_actifs = $compte->getPersosActifs();

        $nb_perso_max = $compte->compt_ligne_perso * 3;

        $type_4        = $compte->compt_type_quatrieme;
        $premier_perso = $persos_actifs[0]->perso_cod;

        $perso_quatrieme = array();
        $perso_joueur    = array();
        $familiers       = array();
        foreach ($persos_actifs as $perso_actif)
        {
            // on prend toutes les infos nécessaires du perso
            $perso_actif->prepare_for_tab_switch();
            if ($perso_actif->perso_type_perso == 1 && $perso_actif->perso_pnj != 2)
            {
                // on est sur un perso normal
                $perso_joueur[] = $perso_actif;
            }
            if ($perso_actif->perso_type_perso == 2 || $perso_actif->perso_pnj == 2)
            {
                // on est sur un 4e, monstre ou perso
                $perso_quatrieme[] = $perso_actif;
            }
            if ($perso_actif->perso_type_perso == 3)
            {
                // familiers
                $familiers[] = $perso_actif;
            }
        }
        // on regarde s'il faut afficher des cases vides
        $cases_vides = $nb_perso_max - count($perso_joueur);
        for ($i = 0; $i < $cases_vides; $i++)
        {
            $perso_vide             = new perso;
            $perso_vide->perso_vide = true;
            $perso_joueur[]         = $perso_vide;
        }

        // on regarde s'il y a des comptes sittés
        $persos_sittes = $compte->getPersosSittes();
        if (count($persos_sittes) != 0)
        {
            foreach ($persos_sittes as $perso_sitte)
            {
                $perso_sitte->prepare_for_tab_switch();
            }
        }


    }

}

// affichage de la page
$options_twig = array(
    'URL'                      => G_URL,
    'URL_IMAGES'               => G_IMAGES,
    'HTTPS'                    => $type_flux,
    'ISAUTH'                   => $verif_auth,
    'IS_ADMIN_MONSTRE'         => $is_admin_monstre,
    'COMPTE'                   => $compte,
    'TYPE_PERSO'               => $type_perso,
    'NV_MONSTRE'               => $nv_monstre,
    'ANCIEN_MONSTRE'           => $monstre_cod,
    'EVT_MONSTRE'              => $evt_monstre,
    'AFFICHE_NEWS'             => $affiche_news,
    'PERSOS_ACTIFS'            => $persos_actifs,
    'PERSOS_JOUEURS'           => $perso_joueur,
    'PERSOS_QUATRIEME'         => $perso_quatrieme,
    'NB_PERSO_MAX'             => $nb_perso_max,
    'ATTRIBUE_NOUVEAU_MONSTRE' => $attribue_nouveau_monstre,
    'OK_4'                     => $ok_4,
    'FAMILIERS'                => $familiers,
    'PERSOS_SITTES'            => $persos_sittes,
    'PREMIER_PERSO'            => $premier_perso,

);
echo $template->render($options_twig);

