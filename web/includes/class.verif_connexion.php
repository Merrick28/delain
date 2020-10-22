<?php

class verif_connexion
{
    var $verif_auth = false;
    var $perso_cod;
    var $compt_cod;
    /**
     * @var compte Compte du perso
     */
    var $compte = false;
    /**
     * @var bool|perso Objet perso courant
     */
    var $perso = false;

    function verif()
    {
        global $auth;
        global $type_flux;
        /**
         * Permet de setter la variable pdo qui va servir pour les autres pages
         */
        global $pdo;
        $this->ident();
        if (!$this->verif_auth)
        {
            /** @var string $type_flux */
            header('Location:' . $type_flux . G_URL . 'inter.php');
            die();
        }


        $perso_cod = $this->perso_cod;
        if (empty($perso_cod))
        {
            if (isset($auth))
            {
                $auth->logout();
            }
            header('Location:' . $type_flux . G_URL . 'inter.php');
            die();
        }

        if ($this->compte->compt_hibernation == 'O')
        {
            $phrase = 'Votre compte est en hibernation ! ';
            header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
            die();
        }
        if (!isset($pdo))
        {
            $pdo = new bddpdo();
        }

    }

    function ident($change_perso = false)
    {
        // on récupère les options twig pour les compléter
        global $options_twig_defaut;
        /**
         * Permet de setter la variable pdo qui va servir pour les autres pages
         */
        global $pdo;

        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
        $verif_auth = false;
        $compte     = new compte;
        $perso      = new perso;
        $myAuth     = new myauth;
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
                    $auth_token            = new auth_token();
                    $api_token             = $auth_token->create_token($compte);
                    $_SESSION['api_token'] = $api_token;
                    setcookie("api_token", $api_token, time() + 36000, "/", G_URL);

                    // est-ce qu'on change de perso ?
                    if ($change_perso !== false)
                    {
                        if ($compte->autoriseJouePerso($change_perso))
                        {
                            $compte->compt_der_perso_cod = $change_perso;
                            $compte->stocke();
                        } else
                        {
                            die('Accès interdit à ce perso');
                        }

                    }
                    //-----------------------------------------------------------------------------------//
                    // à partir d'ici, on va initialiser les variables nécessaires à la poursuite du jeu //
                    //-----------------------------------------------------------------------------------//
                    require "blocks/_verif_connexion.php";


                    $perso_nom = $perso->perso_nom;
                    $perso_cod = $perso->perso_cod;

                    $myAuth->perso_cod = $perso_cod;
                    $myAuth->compt_cod = $compt_cod;
                } else
                {
                    if (!empty($_POST['username']))
                    {
                        echo 'Authentification échouée';
                    }
                }
            }
        } else
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
                if ($change_perso !== false)
                {
                    if (!empty($change_perso))
                    {
                        if ($compte->autoriseJouePerso($change_perso))
                        {
                            $compte->compt_der_perso_cod = $change_perso;
                            $compte->stocke();
                        } else
                        {
                            die('Accès interdit à ce perso (debug ' . htmlspecialchars($change_perso) . ')');
                        }
                    }

                }

                //-----------------------------------------------------------------------------------//
                // à partir d'ici, on va initialiser les variables nécessaires à la poursuite du jeu //
                //-----------------------------------------------------------------------------------//
                // compte
                require "blocks/_verif_connexion.php";
                $perso->getByComptDerPerso($compte->compt_cod);

                $perso_nom = $perso->perso_nom;
                $perso_cod = $perso->perso_cod;

                $myAuth->perso_cod = $perso_cod;
                $myAuth->compt_cod = $compt_cod;
            }
        }

        // on met la variable ISAUTH dans options_tiwg_defaut
        // ca permettra de la passer automatiquement au template
        $temp_array          = array('ISAUTH' => $verif_auth);
        $options_twig_defaut = array_merge($options_twig_defaut, $temp_array);

        // on retourne maintenant les bonnes infos
        $this->verif_auth = $verif_auth;
        $this->perso_cod  = $perso->perso_cod;
        $this->compt_cod  = $compte->compt_cod;
        $this->compte     = $compte;
        $this->perso      = $perso;
        if (!isset($pdo))
        {
            $pdo = new bddpdo();
        }

    }

    /**
     * Fonction pour vérifier qu'un bloc a le droit
     * d'être appelé par une page
     */
    public static function verif_appel()
    {
        if (!defined("APPEL"))
        {
            die("Erreur d'appel de page !");
        }
    }

}