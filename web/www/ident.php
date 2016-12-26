<?php

session_start();

if (!isset($type_auth))
{
    $type_auth = 'normal';
}
$dsn = 'pgsql://' . SERVER_USERNAME . ':' . SERVER_PASSWORD . '@' . SERVER_HOST . '/' . SERVER_DBNAME;


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

$pdo = new bddpdo;

$compte = new compte;
$perso = new perso;

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
        $myAuth = new myauth;
        $myAuth->start();
        if (!$myAuth->verif_auth)
        {
            // est-ce qu'on vient de recevoir des infos de formulaire ?
            if (isset($_POST['username']) && isset($_POST['password']))
            {
                // si oui, on checke
                if ($compte->getByLoginPassword($_POST['username'], $_POST['password']))
                {
                    // le check est bien passé, on stocke la session
                    $myAuth->stocke($compte->compt_cod);
                    $verif_auth = true;
                    $normal_auth = true;
                    $compt_nom = $compte->compt_nom;
                    $compt_cod = $compte->compt_cod;
                    // est-ce qu'on change de perso ?
                    if (isset($change_perso))
                    {
                        $compte->compt_der_perso_cod = $change_perso;
                        $compte->stocke();
                    }
                    //-----------------------------------------------------------------------------------//
                    // à partir d'ici, on va initialiser les variables nécessaires à la poursuite du jeu //
                    //-----------------------------------------------------------------------------------//
                    $type_perso = 'joueur';
                    $is_admin_monstre = false;
                    $is_admin = false;
                    if ($compte->compt_monstre == 'O')
                    {
                        $type_perso = 'monstre';
                        $is_admin_monstre = true;
                    }
                    if ($compte->compt_admin == 'O')
                    {
                        $type_perso = 'admin';
                        $is_admin = true;
                    }
                    $perso->getByComptDerPerso($compte->compt_cod);

                    $perso_nom = $perso->perso_nom;
                    $perso_cod = $perso->perso_cod;

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
            $verif_auth = true;
            $normal_auth = true;
            $compt_cod = $myAuth->id;
            $compte = new compte;
            $compte->charge($compt_cod);
            $compt_nom = $compte->compt_nom;
            // est-ce qu'on change de perso ?
            if (isset($change_perso))
            {
                $compte->compt_der_perso_cod = $change_perso;
                $compte->stocke();
            }
            //-----------------------------------------------------------------------------------//
            // à partir d'ici, on va initialiser les variables nécessaires à la poursuite du jeu //
            //-----------------------------------------------------------------------------------//
            // compte
            $type_perso = 'joueur';
            $is_admin_monstre = false;
            $is_admin = false;
            if ($compte->compt_monstre == 'O')
            {
                $type_perso = 'monstre';
                $is_admin_monstre = true;
            }
            if ($compte->compt_admin == 'O')
            {
                $type_perso = 'admin';
                $is_admin = true;
            }
            $perso->getByComptDerPerso($compte->compt_cod);

            $perso_nom = $perso->perso_nom;
            $perso_cod = $perso->perso_cod;

            $myAuth->perso_cod = $perso_cod;
            $myAuth->compt_cod = $compt_cod;
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
        $req = "select perso_type_perso from perso where perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($ext_perso_cod), $stmt);

        $row = $stmt->fetch();
        $type_perso = $row['perso_type_perso'];
        switch ($type_perso)
        {
            case 1:
                $req = "select pcompt_compt_cod,perso_nom
					from perso_compte,perso 
					where pcompt_perso_cod = :perso 
					and perso_cod = :perso ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":perso" => $ext_perso_cod), $stmt);
                $row = $stmt->fetch();
                $compt_cod = $row['pcompt_compt_cod'];
                break;
            case 2:
                // Pour les monstres joués par des admins monstres (et / ou controleurs), il faut que le compt_cod soit passé en GET
                $compt_cod = (isset($_GET['compt_cod'])) ? $_GET['compt_cod'] : '';
                $req = "select perso_nom, '$compt_cod' as pcompt_compt_cod
					from perso 
					where perso_cod = ? ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($ext_perso_cod), $stmt);
                $row = $stmt->fetch();
                $compt_cod = $row['pcompt_compt_cod'];
                break;
            case 3:
                $req = "select pcompt_compt_cod,b.perso_nom
					from perso_compte,perso a, perso b, perso_familier
					where pfam_familier_cod = :perso 
					and pfam_perso_cod = a.perso_cod
					and pcompt_perso_cod = a.perso_cod
					and b.perso_cod = :perso ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":perso" => $ext_perso_cod), $stmt);
                $row = $stmt->fetch();
                $compt_cod = $row['pcompt_compt_cod'];
                break;
            default:
                // on ne peut pas diriger d'autre type de perso
                die("Mauvais type de perso");
                break;
        }
        $verif_auth = true;
        $compt_cod = $row['pcompt_compt_cod'];
        $perso_nom = $row['perso_nom'];
        $perso_cod = $ext_perso_cod;
        break;
}
montre_formulaire_connexion($verif_auth);
