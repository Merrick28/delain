<?php

/**
 * includes/auth.php
 *
 * Sert à gérer toutes les authentifications
 * @author Stephane DEWITTE <stephane.dewitte@gmail.com>
 * @version 1.0
 * @filesource
 * @package default
 */

/**
 * @package default
 */
class myauth
{

    /**
     * @var verif_auth
     * true si authentifie
     * false sinon
     */
    var $verif_auth = false;

    /**
     * @var integer 
     * L'ID de la personne loguée
     */
    var $id;
    var $hash       = "normal";
    var $admin;
    
    var $perso_cod;
    var $compt_cod;

    /**
     * Fonction de démarrage de l'authentification
     */
    function start()
    {
        $pdo = new bddpdo;
        // on commence par nettoyer les sessions
       
        $vsession = '';
        if (isset($_COOKIE['passsession']))
        {
            $vsession = $_COOKIE['passsession'];
            $vhash    = $_COOKIE['passhash'];
        }
        if (isset($_SESSION['id']))
        {
            $vsession = $_SESSION['id'];
            $vhash    = $_SESSION['hash'];
        }
        if ($vsession != '')
        {
            $session = new sessions;
            $session->charge($vsession);
            // on vérifie que le hash de la session est égale au hash de la bdd
            if ($session->hash != $vhash)
            {
                echo 'Fin sessions ' . $session->hash . " - " . $_SESSION['hash'];
                $this->verif_auth = false;
                // on détruit la session
                session_unset();
                session_destroy();
            } //$session->hash != $_SESSION['hash']
            else
            {
                $this->id         = $vsession;
                $this->verif_auth = true;
                // on va chercher les infos de la personne
                $compte             = new compte;
                $compte->charge($this->id);
     
                // on update le timestamp de la session
                $req  = "update sessions
					set sess_date = now()
					where sess_user_cod = ?";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(
                   $this->id
                   ), $stmt);
            }
        } //isset($_SESSION['id'])
        else
        {
            $this->verif_auth = false;
        }
        return $this->verif_auth;
    }

    /**
     * Fonction de stockage en bdd, quand une session démarre
     * (juste après l'authentification)
     */
    function stocke($user_id)
    {
        $pdo = new bddpdo;
        $this->id         = $user_id;
        // on commence par effacer les sessions s'il en existe
        $req              = "delete from sessions
			where sess_user_cod = ? ";
        $stmt             = $pdo->prepare($req);
        $stmt             = $pdo->execute(array(
           $this->id
           ), $stmt);
        // on crée maintenant une sessions
        $hash_session     = uniqid('', true);
        $session          = new sessions;
        $session->id      = $user_id;
        $session->hash    = $hash_session;
        $session->store();
        // on met maintenant tout ça en variables de session
        $_SESSION['id']   = $session->id;
        $_SESSION['hash'] = $session->hash;
        // on définit les cookies
        setcookie("passsession", $session->id, time() + 36000, "/", G_URL);
        setcookie("passhash", $session->hash, time() + 36000, "/", G_URL);
    }

    /**
     * Fonction logout
     * Permet de déconnecter l'utilisateur courant
     */
    function logout()
    {
        $pdo = new bddpdo;
        $req        = "delete from sessions
			where sess_user_cod = ? ";
        $stmt       = $pdo->prepare($req);
        $stmt       = $pdo->execute(array(
           $this->id
           ), $stmt);
        session_unset();
        session_destroy();
        setcookie("passsession", "", time() - 36000, "/", G_URL);
        setcookie("passhash", "", time() - 36000, "/", G_URL);
    }
}
