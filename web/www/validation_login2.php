<?php

include_once "includes/classes.php";
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
$compt_cod  = $verif_connexion->compt_cod;
include_once "includes/constantes.php";
include_once "includes/fonctions.php";

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
            <p><a href="index.php" target="_top">Retour à l’accueil</a></p>
        </div>
        </body>
        </html>
        <?php
    } else
    {

        //$ip = getenv("REMOTE_ADDR");
        $ip = getUserIpAddr();

        $callapi = new callapi();
        if ($callapi->call(API_URL . '/compte', 'GET', $_SESSION['api_token']))
        {
            $error_message = '';
            $compte_json   = json_decode($callapi->content, true);
            $compt_cod     = $compte_json['compte']['compt_cod'];
            $compte        = new compte;
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
        if ($compte->is_admin_monstre())
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
            $admin     = 'O';
            $compt_cod = $compte->compt_cod;
            $chemin    = 'jeu_test';
            include "jeu_test/switch_monstre.php";
            echo '</div></body></html>';
            die('');
        } // Si admin
        elseif ($compte->is_admin())
        {
            echo("<html><head>"); ?>
            <link rel="stylesheet" type="text/css" href="style.css?v<?php echo $__VERSION; ?>" title="essai">
            <link rel="stylesheet" type="text/css" href="css/container-fluid.css?v<?php echo $__VERSION; ?>">
            <?php
            echo("</head>");
            echo '<body background="images/fond5.gif" onload="retour();">';

            echo '<div class="bordiv">';
            $is_admin = true; ?>
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
        } // Si joueur
        else
        {
            $allevt_oldmonstre = array();
            $perso_monstre     = 0;
            $tabnews           = array();
            if ($callapi->call(
                API_URL . '/news',
                'GET'
            ))
            {
                $tabNews = json_decode($callapi->content, true);
            } else
            {
                die('Erreur sur appel API news ' . $callapi->content);
            }
            $news_cod = $tabNews['news'][0]['news_cod'];
            // Récupération du numéro du monstre actuel, s'il existe.
            $pdo         = new bddpdo();
            $req         = "select perso_cod from perso inner join perso_compte on pcompt_perso_cod = perso_cod
				where pcompt_compt_cod = :compte and perso_type_perso = 2
				order by pcompt_date_attachement desc limit 1";
            $stmt        = $pdo->prepare($req);
            $stmt        = $pdo->execute(array($compt_cod), $stmt);
            $monstre_cod = 0;
            if ($result = $stmt->fetch())
            {
                $monstre_cod = $result['perso_cod'];
            }


            $der_news   = $compte->compt_der_news;
            $nv_monstre = ($compte->attribue_monstre_4e_perso() > 0);

            if ($compte->compt_hibernation != 'O')
            {
                if ($der_news < $news_cod || $nv_monstre)
                {
                    if ($nv_monstre)
                    {
                        if ($monstre_cod > 0)
                        {
                            // on charge le détail de ce monstre
                            $perso_monstre = new perso();
                            $perso_monstre->charge($monstre_cod);

                            // on récupère les événements du monstre pour les afficher.
                            $levt              = new ligne_evt();
                            $allevt_oldmonstre = $levt->getByPersoNonLu($monstre_cod);
                            $allevt_oldmonstre = $levt->mise_en_page_evt($allevt_oldmonstre, false);


                            // On relâche le monstre du compte du joueur
                            $req_mort = "select relache_monstre_4e_perso(:monstre_cod, 1::smallint) as resultat";
                            $stmt     = $pdo->prepare($req_mort);
                            $stmt     = $pdo->execute(array(":monstre_cod" => $monstre_cod), $stmt);
                        }
                    }
                    //print_r($_SESSION);
                    $news    = new news;
                    $tabnews = $news->getNewsSup($der_news);

                    $compte->compt_der_news = $news_cod;
                    $compte->stocke();
                }

                // on efface l'hibernation si il en reste
                if ($compte->compt_hibernation == 'T')
                {
                    $req  = "select fin_hibernation(:compte) ";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":compte" => $compt_cod), $stmt);
                }
                if ($compte->compt_acc_charte != 'N')
                {
                    // on passe maintenant par un appel api
                    if ($callapi->call(API_URL . '/compte/persos', 'GET', $_SESSION['api_token']))
                    {
                        $persos_compte = $callapi->content;
                        $persos_compte = json_decode($persos_compte, true);
                        //$persos_compte = $compte->getPersosActifs();
                        $nb_persos = count($persos_compte['persos']) + count($persos_compte['sittes']);
                        if ($nb_persos != 0)
                        {
                            ob_start();
                            $origine_switch = 'accueil';
                            include "tab_switch.php";
                            $tab_switch = ob_get_clean();
                        }
                    } else
                    {
                        die('Erreur sur appel API (get persos compte) : ' . $callapi->content);
                    }
                }
            }
            $template     = $twig->load('validation_login2_perso.twig');
            $options_twig = array(
                'COMPTE'          => $compte,
                'DER_NEWS'        => $der_news,
                'NEWS_COD'        => $news_cod,
                'NOUVEAU_MONSTRE' => $nv_monstre,
                'MONSTRE_COD'     => $monstre_cod,
                'EVTOLDMONSTRE'   => $allevt_oldmonstre,
                'PERSO_MONSTRE'   => $perso_monstre,
                'TAB_NEWS'        => $tabnews,
                'LISTE_PERSO'     => $persos_compte,
                'TAB_SWITCH'      => $tab_switch,
                'NV_MONSTRE'      => $nv_monstre,
                'NB_PERSOS'       => $nb_persos


            );
            echo $template->render(array_merge($options_twig_defaut, $options_twig));
        }
    }
}
