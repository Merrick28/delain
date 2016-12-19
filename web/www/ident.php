<?php

session_start();

if (!isset($type_auth))
{
    $type_auth = 'normal';
}
$dsn = 'pgsql://' . SERVER_USERNAME . ':' . SERVER_PASSWORD . '@' . SERVER_HOST . '/' . SERVER_DBNAME;
//require_once('PEAR.php');
//require_once('MDB2.php');
//erquire_once('Auth/Auth.php');

//
// fonction d'affichage du formulaire de login si pas authentifié
//
function montre_formulaire_connexion($isAuthOk)
{
    $resultat = "";

    /* if ($erreur !== "")
      {
      $resultat .= '<b>Erreur !</b> ' . $erreur . '<br />';
      } */

    if (!$isAuthOk)
    {
        $resultat .= '<strong><a href="formu_cree_compte.php" style="font-size:1.1em">Créer un compte</a></strong><br /><br />Se connecter :<br />';
        $resultat .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
				Login<br />
				<input type="text" name="username" size="15"><br />
				Password<br />
				<input type="password" name="password" size="15"><br /><br />
				<input type="submit" class="test" value="Valider !"><br /><br />
				<a href="renvoi_mdp.php" target="droite">Mot de passe oublié ? </a>
			</form>';
        $resultat .= '<a href="' . $_SERVER['PHP_SELF'] . '?login">Login with Google</a>';
    }
    else
    {
        global $compt_nom;
        $resultat .= '<b>' . $compt_nom . '</b><div style="margin:5px">
			<b><img src="http://images.jdr-delain.net/attaquer.gif" title="Jouer" />&nbsp;<a href="validation_login2.php">Jouer</a></b><hr style="margin-left:5px;margin-right:5px"/>
			<img src="http://images.jdr-delain.net/deconnection.gif" title="Se déconnecter" />&nbsp;<a href="logout.php" target="_top">Se déconnecter</a></div>';
    }
    return $resultat;
}

/* function myauth_callback()
  {
  montre_formulaire_connexion(false);
  } */

$pdo = new bddpdo;

$compte = new compte;

/* * ***************** */
/* FONCTIONS GOOGLE */
/* * ***************** */
$google_auth = false;
$google_temp = false;
require "google.php";
if (isset($_SESSION['google_account']))
{
    $google_temp = true;
    $google_id   = $_SESSION['google_account'];
}
try
{
    $openid = new LightOpenID('www.jdr-delain.net');
    if (!$openid->mode)
    {
        if (isset($_GET['login']))
        {
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');
            header('Location: ' . $openid->authUrl());
        }
    }
    elseif ($openid->mode == 'cancel')
    {
        echo 'Authentification annulée.';
    }
    else
    {
        if ($openid->validate())
        {
            // Là on a validé l'auth de google
            $google_temp = true;
            $google_id   = $openid->identity;
            //echo "Login google OK";
        }
        else
        {
            echo 'Utilisateur ' . $openid->identity . ' non connecté.';
        }
    }
} catch (ErrorException $e)
{
    echo $e->getMessage();
}
if ($google_temp)
{
    $mdb2 = & MDB2::connect($dsn);
    $req  = "select * from compte where compt_google = '" . $google_id . "'";
    $res  = & $mdb2->query($req);
    if ($res->numRows() != 0)
    {
        $row                        = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        $google_compte              = $row['compt_cod'];
        $compt_cod                  = $google_compte;
        $compt_nom                  = $row['compt_nom'];
        $g_compt_monstre            = $row['compt_monstre'];
        $g_compt_admin              = $row['compt_admin'];
        $compt_nom                  = $row['compt_nom'];
        $google_auth                = true;
        $_SESSION['google_account'] = $google_id;
        //echo "Google compte : " . $google_compte;
    }
    $mdb2->disconnect();
}
if (!isset($type_auth))
{
    $type_auth = 'normal';
}
// si on change perso, il faut le faire tout de suite !
// on passe par du pg_query standard pour ne pas bousculer les classes

switch ($type_auth)
{
    case "normal":
       
        $normal_auth = false;

        // Nous sommes ici dans le cas d'une auth classique du jeu, sans passer par une api externe
        //
		// extension de la classe Auth
        //
	/*class My_Auth extends Auth
        {

            var $perso_cod;
            var $compt_cod;

        }*/

        //
        // options de la classe Auth
        //
	if ($google_auth)
        {
            $verif_auth = true;
            if (isset($change_perso))
            {

                $req  = "update compte set compt_der_perso_cod = ? where compt_cod = ?";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($change_perso, $compt_cod), $stmt);
            }

            $type_perso       = 'joueur';
            $is_admin_monstre = false;
            $is_admin         = false;
            if ($g_compt_monstre == 'O')
            {
                $type_perso       = 'monstre';
                $is_admin_monstre = true;
            }
            if ($g_compt_admin == 'O')
            {
                $type_perso = 'admin';
                $is_admin   = true;
            }
            // perso
            $req  = "select perso_nom,perso_cod from perso,compte
				where perso_cod = compt_der_perso_cod
				and compt_cod = ?";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array($compt_cod), $stmt);
            $row  = $stmt->fetch();

            $perso_nom = $row['perso_nom'];
            $perso_cod = $row['perso_cod'];


            $auth->perso_cod = $perso_cod;
            $auth->compt_cod = $compt_cod;
        }
        else
        {
             
            $myAuth = new myauth;
            $myAuth->start();
            if (!$myAuth->verif_auth)
            {
                //die('ok');
                if (isset($_POST['username']) && isset($_POST['password']))
                {
                    
                    if ($compte->getByLoginPassword($_POST['username'], $_POST['password']))
                    {
                        $myAuth->stocke($compte->compt_cod);
                        $verif_auth  = true;
                        $normal_auth = true;
                        $compt_nom   = $compte->compt_nom;
                        $compt_cod   = $compte->compt_cod;
                        if (isset($change_perso))
                        {

                            $req  = "update compte set compt_der_perso_cod = ? where compt_cod = ?";
                            $stmt = $pdo->prepare($req);
                            $stmt = $pdo->execute(array($change_perso, $compt_cod), $stmt);
                        }
                        //-----------------------------------------------------------------------------------//
                        // à partir d'ici, on va initialiser les variables nécessaires à la poursuite du jeu //
                        //-----------------------------------------------------------------------------------//
                        $type_perso       = 'joueur';
                        $is_admin_monstre = false;
                        $is_admin         = false;
                        if ($compte->compt_monstre == 'O')
                        {
                            $type_perso       = 'monstre';
                            $is_admin_monstre = true;
                        }
                        if ($compte->compt_admin == 'O')
                        {
                            $type_perso = 'admin';
                            $is_admin   = true;
                        }
                        $req  = "select perso_nom,perso_cod from perso,compte
					where perso_cod = compt_der_perso_cod
					and compt_cod = ?";
                        $stmt = $pdo->prepare($req);
                        $stmt = $pdo->execute(array($compt_cod), $stmt);
                        $row  = $stmt->fetch();

                        $perso_nom = $row['perso_nom'];
                        $perso_cod = $row['perso_cod'];

                        $myAuth->perso_cod = $perso_cod;
                        $myAuth->compt_cod = $compt_cod;
                    }
                    else if (!empty($_POST['username']))
                    {
                        echo 'Authentification échouée';
                    }
                }
            }
            else
            {
                // on est déjà authentifié !
                $verif_auth  = true;
                $normal_auth = true;
                $compt_cod   = $myAuth->id;
                $compte      = new compte;
                $compte->charge($compt_cod);
                $compt_nom   = $compte->compt_nom;
                if (isset($change_perso))
                {

                    $req  = "update compte set compt_der_perso_cod = ? where compt_cod = ?";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($change_perso, $compt_cod), $stmt);
                }
                //-----------------------------------------------------------------------------------//
                // à partir d'ici, on va initialiser les variables nécessaires à la poursuite du jeu //
                //-----------------------------------------------------------------------------------//
                // compte
                $type_perso       = 'joueur';
                $is_admin_monstre = false;
                $is_admin         = false;
                if ($compte->compt_monstre == 'O')
                {
                    $type_perso       = 'monstre';
                    $is_admin_monstre = true;
                }
                if ($compte->compt_admin == 'O')
                {
                    $type_perso = 'admin';
                    $is_admin   = true;
                }
                $req  = "select perso_nom,perso_cod from perso,compte
					where perso_cod = compt_der_perso_cod
					and compt_cod = ?";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($compt_cod), $stmt);
                $row  = $stmt->fetch();

                $perso_nom = $row['perso_nom'];
                $perso_cod = $row['perso_cod'];

                $myAuth->perso_cod = $perso_cod;
                $myAuth->compt_cod = $compt_cod;
            }




            //$verif_auth = false;
        }
        break;
    case "programme":
        // uniquement prévu pour les connections locales (127.0.0.1)
        //echo $_SERVER["REMOTE_ADDR"];
        //if($_SERVER["REMOTE_ADDR"] != '88.191.130.220')
        //	die('IP refusée');
        /* if(!defined(AUTHINT))
          die('Variable'); */
        if ($cle_connect != apc_fetch('cle_connec'))
        {
            //echo $cle_connect . '<br>';
            //echo apc_fetch('cle_connec') . '<br>';
            die('Clé de connexion');
        }

        
        // on regarde le type de perso choisi
        $req  = "select perso_type_perso from perso where perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($ext_perso_cod), $stmt);

        $row        = $stmt->fetch();
        $type_perso = $row['perso_type_perso'];
        switch ($type_perso)
        {
            case 1:
                $req       = "select pcompt_compt_cod,perso_nom
					from perso_compte,perso 
					where pcompt_perso_cod = :perso 
					and perso_cod = :perso ";
                $stmt      = $pdo->prepare($req);
                $stmt      = $pdo->execute(array(":perso" => $ext_perso_cod), $stmt);
                $row       = $stmt->fetch();
                $compt_cod = $row['pcompt_compt_cod'];
                break;
            case 2:
                // Pour les monstres joués par des admins monstres (et / ou controleurs), il faut que le compt_cod soit passé en GET
                $compt_cod = (isset($_GET['compt_cod'])) ? $_GET['compt_cod'] : '';
                $req       = "select perso_nom, '$compt_cod' as pcompt_compt_cod
					from perso 
					where perso_cod = ? ";
                $stmt      = $pdo->prepare($req);
                $stmt      = $pdo->execute(array($ext_perso_cod), $stmt);
                $row       = $stmt->fetch();
                $compt_cod = $row['pcompt_compt_cod'];
                break;
            case 3:
                $req       = "select pcompt_compt_cod,b.perso_nom
					from perso_compte,perso a, perso b, perso_familier
					where pfam_familier_cod = :perso 
					and pfam_perso_cod = a.perso_cod
					and pcompt_perso_cod = a.perso_cod
					and b.perso_cod = :perso ";
                $stmt      = $pdo->prepare($req);
                $stmt      = $pdo->execute(array(":perso" => $ext_perso_cod), $stmt);
                $row       = $stmt->fetch();
                $compt_cod = $row['pcompt_compt_cod'];
                break;
            default:
                // on ne peut pas diriger d'autre type de perso 
                die("Mauvais type de perso");
                break;
        }
        $verif_auth = true;
        $compt_cod  = $row['pcompt_compt_cod'];
        $perso_nom  = $row['perso_nom'];
        $perso_cod  = $ext_perso_cod;
        break;
    case "externe":
        // cette authentification doit passer les variables suivantes :
        // $type_auth = 'externe'
        // $ext_appli = num_appli
        // $ext_perso_cod = perso_cod
        // $ext_session_id = num_ de_session
        $verif_auth = false;  // par défaut, cette variable est false si pas authentifé, true si ok
        $mdb2       = & MDB2::connect($dsn);
        // on regarde le type de perso choisi
        $req        = "select perso_type_perso from perso where perso_cod = ?";
        $stmt       = $pdo->prepare($req);
        $stmt       = $pdo->execute(array($ext_perso_cod), $stmt);
        $row        = $stmt->fetch();
        $type_perso = $row['perso_type_perso'];
        // on va d'abord regarder dans la base si on a tout ce qu'il faut
        switch ($type_perso)
        {
            case 1:
                $req  = "select sess_key ,pcompt_compt_cod ,perso_nom
					from auth.session,auth.demande_temp,perso_compte,perso
					where sess_dtemp_cod = dtemp_cod
					and dtemp_appli_cod = ? 
					and dtemp_compt_cod = pcompt_compt_cod
					and pcompt_perso_cod = ?
					and perso_cod = pcompt_perso_cod ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($ext_appli, $ext_perso_cod), $stmt);

                $row     = $stmt->fetch();
                $sess_id = $row['sess_key'];
                break;
            case 3:
                $req     = "select sess_key,pcompt_compt_cod ,perso_nom
					from auth.session,auth.demande_temp,perso_compte,perso_familier,perso
					where sess_dtemp_cod = dtemp_cod
					and dtemp_appli_cod = :appli 
					and dtemp_compt_cod = pcompt_compt_cod
					and pcompt_perso_cod = pfam_perso_cod 
					and pfam_familier_cod = :perso_cod
					and perso_cod = :perso_cod ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":appli" => $ext_appli, ":perso_cod" => $ext_perso_cod), $stmt);

                $row     = $stmt->fetch();
                $sess_id = $row['sess_key'];
                break;
            default:
                // on ne peut pas diriger d'autre type de perso 
                die("Mauvais type de perso");
                break;
        }
        $mdb2->disconnect();
        if ($sess_id == $ext_session_id)
        {
            // L'auth a marché
            $verif_auth = true;
            $compt_cod  = $row['pcompt_compt_cod'];
            $perso_nom  = $row['perso_nom'];
            $perso_cod  = $ext_perso_cod;
        }
        break;
}
montre_formulaire_connexion($verif_auth);
