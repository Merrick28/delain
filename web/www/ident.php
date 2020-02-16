<?php

session_start();
$verif_auth = false;

$pdo = new bddpdo;
$compte = new compte;
$perso  = new perso;

// si on change perso, il faut le faire tout de suite !
// on passe par du pg_query standard pour ne pas bousculer les classes


$normal_auth = false;
$myAuth      = new myauth;
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
            $verif_auth  = true;
            $normal_auth = true;
            $compt_nom   = $compte->compt_nom;
            $compt_cod   = $compte->compt_cod;

            // on ajoute le token pour la suite
            $auth_token = new auth_token();
            $api_token = $auth_token->create_token($compte);
            $_SESSION['api_token'] = $api_token;
            setcookie("api_token", $api_token, time() + 36000, "/", G_URL);

            // est-ce qu'on change de perso ?
            if (isset($change_perso))
            {
                if ($compte->autoriseJouePerso($change_perso))
                {
                    $compte->compt_der_perso_cod = $change_perso;
                    $compte->stocke();
                }
                else
                {
                    die('Accès interdit à ce perso');
                }

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
            /*if (!$perso->getByComptDerPerso($compte->compt_cod))
            {
                echo 'Authentification échouée, erreur sur le chargement de perso';
                $verif_auth = false;
            }*/

            $perso_nom = $perso->perso_nom;
            $perso_cod = $perso->perso_cod;

            $myAuth->perso_cod = $perso_cod;
            $myAuth->compt_cod = $compt_cod;
        }
        else
        {
            if (!empty($_POST['username']))
            {
                echo 'Authentification échouée';
            }
        }
    }
}
else
{
    // on est déjà authentifié !

    $compt_cod = $myAuth->id;
    $compte    = new compte;
    if ($compte->charge($compt_cod))
    {
        $verif_auth  = true;
        $normal_auth = true;
        $compt_nom   = $compte->compt_nom;
        // est-ce qu'on change de perso ?
        if (isset($change_perso))
        {
            if ($compte->autoriseJouePerso($change_perso))
            {
                $compte->compt_der_perso_cod = $change_perso;
                $compte->stocke();
            }
            else
            {
                die('Accès interdit à ce perso');
            }
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
        $perso->getByComptDerPerso($compte->compt_cod);

        $perso_nom = $perso->perso_nom;
        $perso_cod = $perso->perso_cod;

        $myAuth->perso_cod = $perso_cod;
        $myAuth->compt_cod = $compt_cod;
    }
}




//montre_formulaire_connexion($verif_auth);

// on met la variable ISAUTH dans options_tiwg_defaut
// ca permettra de la passer automatiquement au template
$temp_array = array('ISAUTH' => $verif_auth);
$options_twig_defaut = array_merge($options_twig_defaut,$temp_array);


