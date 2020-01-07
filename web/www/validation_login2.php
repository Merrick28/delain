<?php

include_once "includes/classes.php";
include_once "ident.php";
include_once "includes/constantes.php";
include_once "includes/fonctions.php";

function getUserIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


$is_log = 0;
if ($verif_auth)
{
    if ($compt_cod == '')
    {
        //
        // on recherche le type perso
        //

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <link rel="stylesheet" type="text/css" href="style.css?v<?php echo $__VERSION; ?>" title="essai">
            <title>Login</title>
        </head>
        <body background="images/fond5.gif">
        <div class="bordiv">
            <p>Identification échouée !</p>
            <p><a href="login2.php" target="_top">Retour à l’accueil</a></p>
        </div>
        </body>
        </html>
        <?php
    } else
    {
        //$ip = getenv("REMOTE_ADDR");
        $ip = getUserIpAddr();

        $db = new base_delain;


        $callapi = new callapi();
        if ($callapi->call(API_URL . '/compte', 'GET', $_SESSION['api_token']))
        {
            $error_message = '';
            $compte_json   = json_decode($callapi->content, true);
            $compt_cod     = $compte_json['compt_cod'];

            $compte = new compte;
            $compte->charge($compt_cod);
        } else
        {
            die('Erreur sur le chargement du compte : ' . $callapi->content);
        }

        $compte->compt_der_connex = date('Y-m-d H:i:s');
        $compte->compt_ip         = $ip;
        $compte->stocke();

        // Ici on sépare si monstre ou joueur
        // si monstre
        if ($is_admin_monstre === true)
        {
            ?>
            <!DOCTYPE html>
            <html>
            <head>

                <link rel="stylesheet" type="text/css" href="style.css?v<?php echo $__VERSION; ?>" title="essai">
                <link rel="stylesheet" type="text/css" href="css/container-fluid.css?v<?php echo $__VERSION; ?>">
                <title>Login</title>
            </head>
            <body background="images/fond5.gif" onload="retour();">
            <?php


            echo '<div class="bordiv">';
            $admin  = 'O';
            $chemin = 'jeu_test';
            include "jeu_test/switch_monstre.php";
            echo '</div></body></html>';
        }
        // Si admin
        if ($is_admin === true)
        {

            echo("<html><head>");
            ?>
            <link rel="stylesheet" type="text/css" href="style.css?v<?php echo $__VERSION; ?>" title="essai">
            <link rel="stylesheet" type="text/css" href="css/container-fluid.css?v<?php echo $__VERSION; ?>">
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
        }
        // Si joueur
        if ($type_perso == 'joueur')
        {

            echo("<html><head>");
            ?>
            <link rel="stylesheet" type="text/css" href="style.css?v<?php echo $__VERSION; ?>" title="essai">
            <link rel="stylesheet" type="text/css" href="css/container-fluid.css?v<?php echo $__VERSION; ?>">
            <?php
            echo("</head>");
            echo '<body background="images/fond5.gif" onload="retour();">';

            echo '<div class="bordiv">';

            if ($callapi->call(API_URL . '/news?start_news=' . $start_news,
                               'GET'))
            {
                $tabNews = json_decode($callapi->content);
            } else
            {
                die('Erreur sur appel API news ' . $callapi->content);
            }
            $news_cod = $tabNews[0]['news_cod'];

            /*$req = "select news_cod from news order by news_cod desc limit 1";
            $db->query($req);
            $db->next_record();
            $news_cod = $db->f("news_cod");*/

            // Récupération du numéro du monstre actuel, s'il existe.
            $pdo  = new bddpdo();
            $req  = "select perso_cod from perso inner join perso_compte on pcompt_perso_cod = perso_cod
				where pcompt_compt_cod = :compte and perso_type_perso = 2
				order by pcompt_date_attachement desc limit 1";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":compte" => $compte_json['compt_cod']), $stmt);
            if ($result = $stmt->fetch())
            {
                $monstre_cod = $result['perso_cod'];
            }


            $der_news   = $compte->compt_der_news;
            $nv_monstre = ($compte->attribue_monstre_4e_perso() > 0);
            $hiber      = $compte->compt_hibernation;
            $charte     = $compte->compt_acc_charte;
            //_SESSION[_authsession][data][compt_der_news];
            if ($hiber == 'O')
            {
                $date = $compte->compt_dfin_hiber;
                $date = new DateTime($date);
                echo "<p>Votre compte est en hibernation jusqu’au " . $date->format('Y-m-d H:i:s') . "<br>";
                echo "Vous ne pouvez pas vous connecter d’ici là.";
            } else
            {
                if ($der_news < $news_cod || $nv_monstre)
                {
                    ?>
                    <p class="titre">Dernières nouvelles : </p>
                    <?php
                    if ($nv_monstre)
                    {
                        ?>
                        <p class="titre">Nouveau monstre !</p>
                        <p class="texteNorm">
                            Un nouveau monstre vient de vous être affecté. Prenez-en bien soin :)
                        </p>
                        <?php
                        if ($monstre_cod > 0)
                        {
                            // on charge le détail de ce monstre
                            $perso_monstre = new perso();
                            $perso_monstre->charge($monstre_cod);

                            // on récupère les événements du monstre pour les afficher.
                            $levt   = new ligne_evt();
                            $allevt = $levt->getByPersoNonLu($monstre_cod);

                            if (count($allevt) != 0)
                            {
                                echo '<p>Les derniers événements du monstre précédent :</p>';
                                foreach ($allevt as $detailevt)
                                {
                                    $type_evt = new type_evt();
                                    $type_evt->charge($detailevt->levt_tevt_cod);
                                    $texte_evt =
                                        str_replace('[perso_cod1]', "<strong>" . $perso_monstre->perso_nom . "</strong>",
                                                    $detailevt->levt_texte);
                                    if ($detailevt->levt_attaquant != '')
                                    {
                                        $texte_evt =
                                            str_replace('[attaquant]', "<strong>" .
                                                                       $detailevt->perso_attaquant->perso_nom . "</strong>", $texte_evt);

                                    }

                                    if ($detailevt->levt_cible != '')
                                    {
                                        $perso_cible = new perso;
                                        $perso_cible->charge($detailevt->levt_cible);
                                        $texte_evt =
                                            str_replace('[cible]', "<strong>" . $detailevt->perso_cible->perso_nom . "</strong>",
                                                        $texte_evt);
                                        unset($perso_cible);

                                    }
                                    echo $detailevt->levt_date . " : " . $texte_evt . "(" . $db->f("tevt_libelle") . ")";
                                    unset($type_evt);
                                }
                            } else
                            {
                                echo "<p>Aucun événement depuis votre dernière DLT</p>";
                            }
                            // On relâche le monstre du compte du joueur
                            $req_mort = "select relache_monstre_4e_perso(:monstre_cod, 1::smallint) as resultat";
                            $stmt = $pdo->prepare($req_mort);
                            $stmt = $pdo->execute(array(":monstre_cod" => $monstre_cod),$stmt);
                        }
                    }
                    //print_r($_SESSION);
                    $news    = new news;
                    $tabnews = $news->getNewsSup($der_news);

                    //while ($db->next_record())
                    foreach ($tabnews as $valnews)
                    {

                        ?>
                        <p class="titre"><?php echo $valnews->news_titre ?></p>
                        <p class="texteNorm" style="text-align:right;"><?php echo $valnews->date_news ?></p>
                        <p class="texteNorm">
                            <?php echo $valnews->news_texte; ?>
                        </p>
                        <p class="texteNorm" style="text-align:right;">
                            <?php
                            if ($valnews->news_mail_auteur != "")
                            {
                                echo '<a href="mailto:' . $valnews->news_mail_auteur . '">' .
                                     $valnews->news_auteur . '</a>';
                            } else
                            {
                                echo $valnews->news_auteur;
                            }
                            ?>
                        </p>
                        <?php

                    }
                    $compte->compt_der_news = $news_cod;
                    $compte->stocke();
                }
                // on efface l'hibernation si il en reste
                if ($hiber == 'T')
                {
                    $req = "select fin_hibernation($compt_cod) ";
                    $db->query($req);
                }
                if ($charte == 'N')
                {
                    echo "<p>Vous devez revalider la <a href=\"charte.php\" target=\"_blank\">charte des joueurs</a>.<br>";
                    echo "Cette opération est nécessaire pour continuer.<br>";
                    echo "Afin de valider la charte, cliquez <a href=\"valide_charte.php\">ici.<a/>";
                } else
                {
                    $req_perso = "SELECT pcompt_perso_cod, perso_nom FROM perso
						INNER JOIN perso_compte ON pcompt_perso_cod = perso_cod
						WHERE pcompt_compt_cod = $compt_cod AND perso_actif = 'O'";
                    $db        = new base_delain;
                    $db->query($req_perso);
                    $nb_perso = $db->nf();
                    if ($nb_perso == 0)
                    {
                        echo("<p>Aucun joueur dirigé.</p>");
                        echo("<form name=\"nouveau\" method=\"post\">");
                        echo("<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">");

                        echo("<a href=\"javascript:document.nouveau.action='cree_perso_compte.php';document.nouveau.submit();\">Créer un nouveau personnage !</a>");
                        echo("</form>");
                    } else
                    {
                        //echo("<table background=\"images/fondparchemin.gif\" border=\"0\">");
                        echo("<form name=\"login\" method=\"post\" action=\"validation_login3.php\">");
                        echo("<input type=\"hidden\" name=\"perso\">");
                        echo("<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">");
                        //echo("<input type=\"hidden\" name=\"password\" value=\"$pass\">");
                        echo("<input type=\"hidden\" name=\"activeTout\" value=\"0\">");

                        echo '<div class="container-fluid">';
                        include "tab_switch.php";
                        echo '</div>';

                        //echo("</table>");
                        echo("</form>");
                    }

                    echo "<p style=\"text-align:center;\"><a href=\"http://www.jdr-delain.net/jeu_test/logout.php\"><strong>se déconnecter</strong></a></p>";
                    echo "<p style=\"text-align:center;\"><br /><em>Date et heure serveur : " . date('d/m/Y H:i:s') . "</em></p>";
                }
            }
            echo '</div></body></html>';
        }
    }
}

