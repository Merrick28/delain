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
                margin-left: 200;
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
        $news          = new news();
        $tab_last_news = $news->getNews(0);
        $last_news     = $tab_last_news[0];
        $news_cod      = $last_news->news_cod;

        // Récupération du numéro du monstre actuel, s'il existe.
        $monstre_joueur = $compte->getMonstreJoueur();
        if ($monstre_joueur !== false)
        {
            $monstre_cod = $monstre_joueur->perso_cod;
        }

        $nv_monstre = $compte->attribue_monstre_4e_perso();

        if ($compte->compt_der_news < $news_cod)
        {
            $affiche_news = $news->getNewsSup($compte->compt_der_news);
            $compte->compt_der_news = $news_cod;
        }
        if ($nv_monstre)
        {
            // on a un monstre_cod > 0, donc il y avait un monstre, mais il a été remplacé
            // puisque nv_monstre == true
            if ($monstre_cod > 0)
            {
                // on récupère les événements du monstre pour les afficher.
                $evt_monstre    = array();
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
            }
        }

        // on efface l'hibernation si il en reste
        if ($compte->compt_hibernation == 'T')
        {
            $compte->fin_hibernation();
        }

        $persos_actifs = $compte->getPersosActifs();

        $req_perso = "SELECT pcompt_perso_cod, perso_nom FROM perso
						INNER JOIN perso_compte ON pcompt_perso_cod = perso_cod
						WHERE pcompt_compt_cod = $compt_cod AND perso_actif = 'O'";
        $db        = new base_delain;
        $db->query($req_perso);
        $nb_perso = $db->nf();
        ob_start();
        if ($nb_perso == 0)
        {
            echo("<p>Aucun joueur dirigé.</p>");
            echo("<form name=\"nouveau\" method=\"post\">");
            echo("<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">");

            echo("<a href=\"javascript:document.nouveau.action='cree_perso_compte.php';document.nouveau.submit();\">Créer un nouveau personnage !</a>");
            echo("</form>");
        }
        else
        {
            echo("<table background=\"images/fondparchemin.gif\" border=\"0\">");
            echo("<form name=\"login\" method=\"post\" action=\"validation_login3.php\">");
            echo("<input type=\"hidden\" name=\"perso\">");
            echo("<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">");
            //echo("<input type=\"hidden\" name=\"password\" value=\"$pass\">");
            echo("<input type=\"hidden\" name=\"activeTout\" value=\"0\">");

            include "tab_switch.php";

            echo("</table>");
            echo("</form>");
        }

        echo "<p style=\"text-align:center;\"><a href=\"http://www.jdr-delain.net/jeu_test/logout.php\"><b>se déconnecter</b></a></p>";
        echo "<p style=\"text-align:center;\"><br /><i>Date et heure serveur : " . date('d/m/Y H:i:s') . "</i></p>";


        echo '</div></body></html>';
        ob_clean();
        die('');
    }

}

// affichage de la page
$options_twig = array(
    'URL' => G_URL,
    'URL_IMAGES' => G_IMAGES,
    'HTTPS' => $type_flux,
    'ISAUTH' => $verif_auth,
    'IS_ADMIN_MONSTRE' => $is_admin_monstre,
    'COMPTE' => $compte,
    'TYPE_PERSO' => $type_perso,
    'NV_MONSTRE' => $nv_monstre,
    'EVT_MONSTRE' => $evt_monstre,
    'AFFICHE_NEWS' => $affiche_news,
    'PERSOS_ACTIFS' => $persos_actifs
);
echo $template->render($options_twig);

