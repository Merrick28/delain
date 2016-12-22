<?php

/* if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
  {
  if ($_SERVER['HTTP_X_FORWARDED_HOST'] != 'www.jdr-delain.net')
  {
  die('L\'accÃ¨s du jeu se fait uniquement par l\'adresse <a href="http://www.jdr-delain.net">www.jdr-delain.net</a>');
  }
  } */
include_once 'classes.php';
include_once "constantes.php";
include_once G_CHE . 'ident.php';
if (!$verif_auth)
{
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}

//$compt_cod = $compt_cod_temp;
if (($compt_cod == '') || ($compt_cod < 1))
{
    if (isset($auth))
        $auth->logout();
    //$auth->auth_loginform();
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}
$perso_cod = trim($perso_cod);
if (empty($compt_cod))
{
    if (isset($auth))
        $auth->logout();
    //$auth->auth_loginform();
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}
if (empty($perso_cod))
{
    if (isset($auth))
        $auth->logout();
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}
$db = new base_delain;

$req = 'select compt_admin, autorise_4e_monstre(compt_quatre_perso, compt_dcreat) as autorise_monstre from compte where compt_cod = ' . $compt_cod;
if (rtrim($req) == 'select compt_admin, autorise_4e_monstre(compt_quatre_perso, compt_dcreat) as autorise_monstre from compte where compt_cod =')
{
    // on a un probleme de $compt_cod qui a disparu, malgré les tests précédents
    // la session est probablement complètement bousillée
    // on essaie quand même de l'effacer
    if (isset($auth))
        $auth->logout();
    // on redirige
    if (!headers_sent())
        header('Location: ' . $type_flux . G_URL);
    die();
}

$db->query($req);
$db->next_record();

$admin            = $db->f('compt_admin');
$autorise_monstre = ($db->f('autorise_monstre') == 't');
//$admin = $auth->auth['admin'];
if ((!$db->is_admin_monstre($compt_cod)) && (!$db->is_admin($compt_cod)))
{
    if ($verif_auth)
    {
        if ($admin != 'O')
        {
            $req        = 'select perso_type_perso,perso_actif from perso where perso_cod = ' . $perso_cod;
            $db->query($req);
            $db->next_record();
            $type_perso = $db->f('perso_type_perso');
            $req        = 'mauvaise requête ! (erreur sur type perso). [Type=' . $type_perso . '][Perso=' . $perso_cod . '][Compte=' . $compt_cod . ']';
            if ($type_perso == 1 || ($type_perso == 2 && $autorise_monstre))
            {
                $req = 'select compt_password,compt_hibernation from compte,perso_compte where pcompt_perso_cod = ' . $perso_cod . ' and pcompt_compt_cod = compt_cod ';
            }
            else if ($type_perso == 3)
            {
                $req = 'select compt_password,compt_hibernation from compte,perso_compte,perso_familier ';
                $req = $req . 'where pfam_familier_cod = ' . $perso_cod;
                $req = $req . ' and pfam_perso_cod = pcompt_perso_cod ';
                $req = $req . 'and pcompt_compt_cod = compt_cod ';
            }
            else // Erreur sur type perso
            {
                $phrase = "Anomalie ! Veuillez vous reconnecter !";
                header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
                die();
            }

            if ($db->f("perso_actif") != 'O')
            {
                $phrase = "Anomalie ! Votre perso est soit inactif, soit en hibernation !";
                header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
                die();
            }

            $db->query($req);
            $db->next_record();

            $pass_actu = $db->f('compt_password');
            //
            // controle des mots de passe
            //
			if ($type_auth == "normal")
            {
                $err_mdp = 0;
                /*if (!isset($_COOKIE[apc_fetch('nom_cook')]))
                {
                    $phrase = 'Anomalie ! Les cookies nécessaires à la session ne sont pas stockés. Vérifiez que votre navigateur accepte bien les cookies (ceux ci sont indispensables au fonctionnement du jeu) et que l’horloge de votre poste est à jour (y compris le fuseau horaire).';
                    header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
                    die();
                }
                else
                {
                    if ($_COOKIE[apc_fetch('nom_cook')] == '')
                    {
                        $phrase = 'Anomalie ! Les cookies nécessaires à la session ne sont pas stockés. Vérifiez que votre navigateur accepte bien les cookies (ceux ci sont indispensables au fonctionnement du jeu) et que l’horloge de votre poste est à jour (y compris le fuseau horaire).';
                        header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
                        die();
                    }
                }*/


                if ($err_mdp == 1)
                {
                    //
                    // on vérifie par acquis de conscience que le compte ne soit pas sitté
                    //
					if ($type_perso == 1)
                    {
                        $req = 'select compt_password,compt_hibernation from perso_compte,compte_sitting,compte
							where pcompt_perso_cod = ' . $perso_cod . '
							and pcompt_compt_cod = csit_compte_sitte
							and csit_ddeb <= now()
							and csit_dfin >= now()
							and csit_compte_sitteur = compt_cod';
                    }
                    elseif ($type_perso == 3)
                    {
                        $req = 'select compt_password,compt_hibernation from perso_compte,perso_familier,compte_sitting,compte
							where pcompt_perso_cod = pfam_perso_cod
							and pfam_familier_cod = ' . $perso_cod . '
							and pcompt_compt_cod = csit_compte_sitte
							and csit_ddeb <= now()
							and csit_dfin >= now()
							and csit_compte_sitteur = compt_cod';
                    }
                    else
                    {
                        $req = 'select * from compte where 1 = 2';  // Requête bidon, parce que sinon c’était le $req déclaré plus haut qui était pris en compte...
                    }
                    //log_sitting('Requete sitting : ' . $req . "\n");
                    $db->query($req);
                    if ($db->next_record())
                    {
                        $pass_actu = $db->f('compt_password');
                        if ($pass_actu == $password)
                        {
                            $err_mdp = 0;
                            //log_sitting('Sitting OK - password = ' . $password  . ' - pass_actu = ' . $pass_actu . "\n");
                        }
                        /* else
                          {
                          log_sitting('Sitting foireux - password = ' . $password  . ' - pass_actu = ' . $pass_actu . "\n");
                          } */
                    }
                }
                if ($err_mdp == 1)
                {
                    header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
                    die();
                }
                if ($db->f('compt_hibernation') == 'O')
                {
                    $phrase = 'Votre compte est en hibernation ! ';
                    header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
                    die();
                }
            }
            if (!isset($cook_pass))
                $cook_pass = md5($pass_actu);
            setcookie('cook_pass', $cook_pass, time() + 3600, '/');
            $perso_nom = str_replace(chr(39), ' ', $perso_nom);
        }
        else
        {
            $db        = new base_delain;
            $perso_nom = str_replace(chr(39), " ", $perso_nom);
        }
    }
    else
    {
        echo '<script language=\'JavaScript\'>parent.document.location.replace(\'fin_session2.php\');</script>';
        exit(); //Termine le script
    }
}



page_close();
$nom_template = 'general';
