<?php

/**
 * includes/class.compte.php
 */

/**
 * Class compte
 *
 * Gère les objets BDD de la table compte
 */
class compte
{

    var $compt_cod;
    var $compt_nom;
    var $compt_password;
    var $compt_mail;
    var $compt_validation;
    var $compt_actif                = 'O';
    var $compt_habilitation;
    var $compt_dcreat;
    var $compt_der_connex;
    var $compt_ip;
    var $compt_commentaire;
    var $compt_renvoye;
    var $compt_monstre              = 'N';
    var $compt_testeur;
    var $compt_admin                = 'N';
    var $compt_hibernation;
    var $compt_dfin_hiber;
    var $compt_acc_charte;
    var $compt_confiance            = 'N';
    var $compt_ddeb_hiber;
    var $compt_quete                = 'N';
    var $compt_envoi_mail           = 0;
    var $compt_envoi_mail_message   = 0;
    var $compt_der_news             = 1;
    var $compt_vue_desc             = 0;
    var $compt_ligne_perso          = 1;
    var $compt_wikidev;
    var $compt_compte_lie;
    var $compt_quatre_perso         = 'O';
    var $compt_der_perso_cod;
    var $compt_fb;
    var $compt_twitter;
    var $compt_google;
    var $compt_frameless            = 'O';
    var $compt_envoi_mail_frequence = 5;
    var $compt_envoi_mail_dernier;
    var $compt_type_quatrieme;
    var $compt_clef_forum           = NULL;
    var $compt_validite_clef_forum;
    var $compt_nombre_clef_forum    = 0;
    var $compt_phashword;
    var $compt_clef_reinit_mdp;
    var $compt_passwd_hash;

    function __construct()
    {
        $this->compt_der_connex         = date('Y-m-d H:i:s');
        $this->compt_envoi_mail_dernier = date('Y-m-d H:i:s');
    }

    function is_admin()
    {
        return $this->compt_admin == 'O';
    }

    function is_admin_monstre()
    {
        return $this->compt_monstre == 'O';
    }

    function autorise_4e_monstre()
    {
        $pdo    = new bddpdo;
        $req    = "select autorise_4e_monstre(?, ?) as autorise_monstre ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->compt_quatre_perso, $this->compt_dcreat), $stmt);
        $result = $stmt->fetch();
        return $result['autorise_monstre'];
    }

    function autorise_4e_perso()
    {
        $pdo    = new bddpdo;
        $req    = "select autorise_4e_perso(?, ?) as autorise_4e_perso ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->compt_quatre_perso, $this->compt_dcreat), $stmt);
        $result = $stmt->fetch();
        return $result['autorise_4e_perso'];
    }

    function autorise_4e_global()
    {
        return $this->autorise_4e_monstre() || $this->autorise_4e_perso();
    }

    function attribue_monstre_4e_perso()
    {
        $pdo    = new bddpdo;
        $req    = "select attribue_monstre_4e_perso(?) as attribue_monstre_4e_perso ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->compt_cod), $stmt);
        $result = $stmt->fetch();
        return $result['attribue_monstre_4e_perso'];
    }

    function fin_hibernation()
    {
        $pdo    = new bddpdo;
        $req    = "select fin_hibernation(?) as fin_hibernation ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->compt_cod), $stmt);
        $result = $stmt->fetch();
        return $result['fin_hibernation'];
    }

    /**
     * @param $perso_cod
     * @return bool
     */
    function autoriseJouePerso($perso_cod)
    {
        // cas particulier, les admins ont tous les droits
        if($this->is_admin())
        {
            return true;
        }
        // admin monstres
        if($this->is_admin_monstre())
        {
            $perso = new perso;
            if($perso->charge($perso_cod))
            {

                if($perso->perso_type_perso == 2)
                {
                    return true;
                }
                if($perso->perso_pnj == 1)
                {
                    return true;
                }
            }
            return false;
        }
        $pdo    = new bddpdo;
        // on regarde pour les joueurs
        $req
            = "SELECT pcompt_perso_cod FROM perso
						INNER JOIN perso_compte ON pcompt_perso_cod = perso_cod
						WHERE pcompt_compt_cod = ? 
						AND perso_actif = 'O'
						AND perso_cod = ? 
						ORDER BY pcompt_perso_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->compt_cod,$perso_cod), $stmt);
        if($stmt->fetch())
        {
            return true;
        }
        // pas trouvé, on regarde dans les familiers
        $req
            = "SELECT pfam_familier_cod,pfam_perso_cod FROM perso_familier,perso,perso_compte
          WHERE pcompt_compt_cod = ? 
          AND pcompt_perso_cod = pfam_perso_cod 
          AND pfam_familier_cod = perso_cod 
          AND perso_actif = 'O' 
          and pfam_familier_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->compt_cod,$perso_cod), $stmt);
        if($stmt->fetch())
        {
            return true;
        }
        // pas trouvé, on regarde dans les sittings
        $req
            = "select pcompt_perso_cod
            from perso,perso_compte,compte_sitting
            where csit_compte_sitteur = ?
            and csit_compte_sitte = pcompt_compt_cod
            and csit_ddeb <= now()
            and csit_dfin >= now()
            and pcompt_perso_cod = perso_cod
            and perso_actif = 'O'
            and perso_type_perso = 1
            and perso_cod = ? ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->compt_cod,$perso_cod), $stmt);
        if($stmt->fetch())
        {
            return true;
        }
        return false;

    }

    /**
     * Retourne les persos actifs d'un compte (y comris les 4e)
     * @return perso[]
     */
    function getPersosActifs()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req
                = "SELECT pcompt_perso_cod FROM perso
						INNER JOIN perso_compte ON pcompt_perso_cod = perso_cod
						WHERE pcompt_compt_cod = ? AND perso_actif = 'O' ORDER BY pcompt_perso_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->compt_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new perso;
            $temp->charge($result['pcompt_perso_cod']);
            $retour[] = $temp;
            unset($temp);
        }
        // familiers
        $req
              = "SELECT pfam_familier_cod,pfam_perso_cod FROM perso_familier,perso,perso_compte
          WHERE pcompt_compt_cod = ? 
          AND pcompt_perso_cod = pfam_perso_cod 
          AND pfam_familier_cod = perso_cod 
          AND perso_actif = 'O' order by pfam_perso_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->compt_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new perso;
            $temp->charge($result['pfam_familier_cod']);
            $retour[] = $temp;
            unset($temp);
        }

        return $retour;
    }

    /**
     * Retourne les persos sittés d'un compte
     * @return perso[]
     */
    function getPersosSittes()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req
                = "select pcompt_perso_cod
            from perso,perso_compte,compte_sitting
            where csit_compte_sitteur = ?
            and csit_compte_sitte = pcompt_compt_cod
            and csit_ddeb <= now()
            and csit_dfin >= now()
            and pcompt_perso_cod = perso_cod
            and perso_actif = 'O'
            and perso_type_perso = 1
            order by perso_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->compt_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new perso;
            $temp->charge($result['pcompt_perso_cod']);
            $retour[] = $temp;
            unset($temp);
        }
    }

    /**
     * Retourne le monstre joué par le compte s'il existe
     * false si rien
     * @return bool|perso
     */
    function getMonstreJoueur()
    {
        $pdo  = new bddpdo;
        $req
              = "SELECT perso_cod FROM perso INNER JOIN perso_compte ON pcompt_perso_cod = perso_cod
				WHERE pcompt_compt_cod = ? AND perso_type_perso = 2
				ORDER BY pcompt_date_attachement DESC LIMIT 1";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->compt_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $result['perso_cod'];
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \compte
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "SELECT compt_cod  FROM compte ORDER BY compt_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new compte;
            $temp->charge($result["compt_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /**
     * Charge dans la classe un enregistrement de compte
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM compte WHERE compt_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->compt_cod                  = $result['compt_cod'];
        $this->compt_nom                  = $result['compt_nom'];
        $this->compt_password             = $result['compt_password'];
        $this->compt_mail                 = $result['compt_mail'];
        $this->compt_validation           = $result['compt_validation'];
        $this->compt_actif                = $result['compt_actif'];
        $this->compt_habilitation         = $result['compt_habilitation'];
        $this->compt_dcreat               = $result['compt_dcreat'];
        $this->compt_der_connex           = $result['compt_der_connex'];
        $this->compt_ip                   = $result['compt_ip'];
        $this->compt_commentaire          = $result['compt_commentaire'];
        $this->compt_renvoye              = $result['compt_renvoye'];
        $this->compt_monstre              = $result['compt_monstre'];
        $this->compt_testeur              = $result['compt_testeur'];
        $this->compt_admin                = $result['compt_admin'];
        $this->compt_hibernation          = $result['compt_hibernation'];
        $this->compt_dfin_hiber           = $result['compt_dfin_hiber'];
        $this->compt_acc_charte           = $result['compt_acc_charte'];
        $this->compt_confiance            = $result['compt_confiance'];
        $this->compt_ddeb_hiber           = $result['compt_ddeb_hiber'];
        $this->compt_quete                = $result['compt_quete'];
        $this->compt_envoi_mail           = $result['compt_envoi_mail'];
        $this->compt_envoi_mail_message   = $result['compt_envoi_mail_message'];
        $this->compt_der_news             = $result['compt_der_news'];
        $this->compt_vue_desc             = $result['compt_vue_desc'];
        $this->compt_ligne_perso          = $result['compt_ligne_perso'];
        $this->compt_wikidev              = $result['compt_wikidev'];
        $this->compt_compte_lie           = $result['compt_compte_lie'];
        $this->compt_quatre_perso         = $result['compt_quatre_perso'];
        $this->compt_der_perso_cod        = $result['compt_der_perso_cod'];
        $this->compt_fb                   = $result['compt_fb'];
        $this->compt_twitter              = $result['compt_twitter'];
        $this->compt_google               = $result['compt_google'];
        $this->compt_frameless            = $result['compt_frameless'];
        $this->compt_envoi_mail_frequence = $result['compt_envoi_mail_frequence'];
        $this->compt_envoi_mail_dernier   = $result['compt_envoi_mail_dernier'];
        $this->compt_type_quatrieme       = $result['compt_type_quatrieme'];
        $this->compt_clef_forum           = $result['compt_clef_forum'];
        $this->compt_validite_clef_forum  = $result['compt_validite_clef_forum'];
        $this->compt_nombre_clef_forum    = $result['compt_nombre_clef_forum'];
        $this->compt_phashword            = $result['compt_phashword'];
        $this->compt_clef_reinit_mdp      = $result['compt_clef_reinit_mdp'];
        $this->compt_passwd_hash          = $result['compt_passwd_hash'];
        return true;
    }

    public function __call($name, $arguments)
    {
        switch (substr($name, 0, 6))
        {
            case 'getBy_':
                if (property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo    = new bddpdo;
                    $req    = "SELECT compt_cod  FROM compte WHERE " . substr($name, 6) . " = ? ORDER BY compt_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new compte;
                        $temp->charge($result["compt_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name, 6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }

    /**
     * Si authentification correcte, on retourne true
     * après avoir hydraté l'objet
     * Donc dans notre page, l'objet $compte contiendra
     * l'obet bdd attendu
     *
     * @param text $login
     * @param text $password
     * @return boolean
     */
    function getByLoginPassword($login, $password)
    {
        if (!$retour = $this->getBy_compt_nom($login))
        {
            return false;
        }
        $this->charge($retour[0]->compt_cod);
        if (empty($this->compt_password) || ($this->compt_password == NULL))
        {
            // password normal vide
            // on est sur du crypté
            if (crypt($password, $this->compt_passwd_hash) == $this->compt_passwd_hash)
            {
                $this->updateDateLogin();
                return true;
            }
            // on n'a pas fait de retour, on n'est donc pas bien authentifié
            return false;
        }
        else
        {
            // password normal non vide
            // on est pas encore sur du crypté
            if ($this->compt_password == $password)
            {
                // on se met à jour pour utiliser le crypté pour la fois suivante
                $this->compt_passwd_hash = crypt($this->compt_password);
                $this->compt_password    = '';
                $this->stocke();

                $this->updateDateLogin();
                return true;
            }
            // on n'a pas fait de retour, on n'est donc pas bien authentifié

            return false;
        }
    }

    function updateDateLogin()
    {
        $this->compt_der_connex = date('Y-m-d H:i:s');
        $this->stocke();
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @global bdd_mysql $pdo
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req
                  = "INSERT INTO compte (
                                        compt_nom,
                                        compt_password,
                                        compt_mail,
                                        compt_validation,
                                        compt_actif,
                                        compt_habilitation,
                                        compt_dcreat,
                                        compt_der_connex,
                                        compt_ip,
                                        compt_commentaire,
                                        compt_renvoye,
                                        compt_monstre,
                                        compt_testeur,
                                        compt_admin,
                                        compt_hibernation,
                                        compt_dfin_hiber,
                                        compt_acc_charte,
                                        compt_confiance,
                                        compt_ddeb_hiber,
                                        compt_quete,
                                        compt_envoi_mail,
                                        compt_envoi_mail_message,
                                        compt_der_news,
                                        compt_vue_desc,
                                        compt_ligne_perso,
                                        compt_wikidev,
                                        compt_compte_lie,
                                        compt_quatre_perso,
                                        compt_der_perso_cod,
                                        compt_fb,
                                        compt_twitter,
                                        compt_google,
                                        compt_frameless,
                                        compt_envoi_mail_frequence,
                                        compt_envoi_mail_dernier,
                                        compt_type_quatrieme,
                                        compt_clef_forum,
                                        compt_validite_clef_forum,
                                        compt_nombre_clef_forum,
                                        compt_phashword,
                                        compt_clef_reinit_mdp,
                                        compt_passwd_hash                                        )
                    VALUES
                    (
                                        :compt_nom,
                                        :compt_password,
                                        :compt_mail,
                                        :compt_validation,
                                        :compt_actif,
                                        :compt_habilitation,
                                        :compt_dcreat,
                                        :compt_der_connex,
                                        :compt_ip,
                                        :compt_commentaire,
                                        :compt_renvoye,
                                        :compt_monstre,
                                        :compt_testeur,
                                        :compt_admin,
                                        :compt_hibernation,
                                        :compt_dfin_hiber,
                                        :compt_acc_charte,
                                        :compt_confiance,
                                        :compt_ddeb_hiber,
                                        :compt_quete,
                                        :compt_envoi_mail,
                                        :compt_envoi_mail_message,
                                        :compt_der_news,
                                        :compt_vue_desc,
                                        :compt_ligne_perso,
                                        :compt_wikidev,
                                        :compt_compte_lie,
                                        :compt_quatre_perso,
                                        :compt_der_perso_cod,
                                        :compt_fb,
                                        :compt_twitter,
                                        :compt_google,
                                        :compt_frameless,
                                        :compt_envoi_mail_frequence,
                                        :compt_envoi_mail_dernier,
                                        :compt_type_quatrieme,
                                        :compt_clef_forum,
                                        :compt_validite_clef_forum,
                                        :compt_nombre_clef_forum,
                                        :compt_phashword,
                                        :compt_clef_reinit_mdp,
                                        :compt_passwd_hash                                        )
                    RETURNING compt_cod AS id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":compt_nom"                  => $this->compt_nom,
                ":compt_password"             => $this->compt_password,
                ":compt_mail"                 => $this->compt_mail,
                ":compt_validation"           => $this->compt_validation,
                ":compt_actif"                => $this->compt_actif,
                ":compt_habilitation"         => $this->compt_habilitation,
                ":compt_dcreat"               => $this->compt_dcreat,
                ":compt_der_connex"           => $this->compt_der_connex,
                ":compt_ip"                   => $this->compt_ip,
                ":compt_commentaire"          => $this->compt_commentaire,
                ":compt_renvoye"              => $this->compt_renvoye,
                ":compt_monstre"              => $this->compt_monstre,
                ":compt_testeur"              => $this->compt_testeur,
                ":compt_admin"                => $this->compt_admin,
                ":compt_hibernation"          => $this->compt_hibernation,
                ":compt_dfin_hiber"           => $this->compt_dfin_hiber,
                ":compt_acc_charte"           => $this->compt_acc_charte,
                ":compt_confiance"            => $this->compt_confiance,
                ":compt_ddeb_hiber"           => $this->compt_ddeb_hiber,
                ":compt_quete"                => $this->compt_quete,
                ":compt_envoi_mail"           => $this->compt_envoi_mail,
                ":compt_envoi_mail_message"   => $this->compt_envoi_mail_message,
                ":compt_der_news"             => $this->compt_der_news,
                ":compt_vue_desc"             => $this->compt_vue_desc,
                ":compt_ligne_perso"          => $this->compt_ligne_perso,
                ":compt_wikidev"              => $this->compt_wikidev,
                ":compt_compte_lie"           => $this->compt_compte_lie,
                ":compt_quatre_perso"         => $this->compt_quatre_perso,
                ":compt_der_perso_cod"        => $this->compt_der_perso_cod,
                ":compt_fb"                   => $this->compt_fb,
                ":compt_twitter"              => $this->compt_twitter,
                ":compt_google"               => $this->compt_google,
                ":compt_frameless"            => $this->compt_frameless,
                ":compt_envoi_mail_frequence" => $this->compt_envoi_mail_frequence,
                ":compt_envoi_mail_dernier"   => $this->compt_envoi_mail_dernier,
                ":compt_type_quatrieme"       => $this->compt_type_quatrieme,
                ":compt_clef_forum"           => $this->compt_clef_forum,
                ":compt_validite_clef_forum"  => $this->compt_validite_clef_forum,
                ":compt_nombre_clef_forum"    => $this->compt_nombre_clef_forum,
                ":compt_phashword"            => $this->compt_phashword,
                ":compt_clef_reinit_mdp"      => $this->compt_clef_reinit_mdp,
                ":compt_passwd_hash"          => $this->compt_passwd_hash,
            ), $stmt);

            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req
                  = "UPDATE compte
                    SET
                                compt_nom = :compt_nom,
                                        compt_password = :compt_password,
                                        compt_mail = :compt_mail,
                                        compt_validation = :compt_validation,
                                        compt_actif = :compt_actif,
                                        compt_habilitation = :compt_habilitation,
                                        compt_dcreat = :compt_dcreat,
                                        compt_der_connex = :compt_der_connex,
                                        compt_ip = :compt_ip,
                                        compt_commentaire = :compt_commentaire,
                                        compt_renvoye = :compt_renvoye,
                                        compt_monstre = :compt_monstre,
                                        compt_testeur = :compt_testeur,
                                        compt_admin = :compt_admin,
                                        compt_hibernation = :compt_hibernation,
                                        compt_dfin_hiber = :compt_dfin_hiber,
                                        compt_acc_charte = :compt_acc_charte,
                                        compt_confiance = :compt_confiance,
                                        compt_ddeb_hiber = :compt_ddeb_hiber,
                                        compt_quete = :compt_quete,
                                        compt_envoi_mail = :compt_envoi_mail,
                                        compt_envoi_mail_message = :compt_envoi_mail_message,
                                        compt_der_news = :compt_der_news,
                                        compt_vue_desc = :compt_vue_desc,
                                        compt_ligne_perso = :compt_ligne_perso,
                                        compt_wikidev = :compt_wikidev,
                                        compt_compte_lie = :compt_compte_lie,
                                        compt_quatre_perso = :compt_quatre_perso,
                                        compt_der_perso_cod = :compt_der_perso_cod,
                                        compt_fb = :compt_fb,
                                        compt_twitter = :compt_twitter,
                                        compt_google = :compt_google,
                                        compt_frameless = :compt_frameless,
                                        compt_envoi_mail_frequence = :compt_envoi_mail_frequence,
                                        compt_envoi_mail_dernier = :compt_envoi_mail_dernier,
                                        compt_type_quatrieme = :compt_type_quatrieme,
                                        compt_clef_forum = :compt_clef_forum,
                                        compt_validite_clef_forum = :compt_validite_clef_forum,
                                        compt_nombre_clef_forum = :compt_nombre_clef_forum,
                                        compt_phashword = :compt_phashword,
                                        compt_clef_reinit_mdp = :compt_clef_reinit_mdp,
                                        compt_passwd_hash = :compt_passwd_hash                                        WHERE compt_cod = :compt_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":compt_cod"                  => $this->compt_cod,
                ":compt_nom"                  => $this->compt_nom,
                ":compt_password"             => $this->compt_password,
                ":compt_mail"                 => $this->compt_mail,
                ":compt_validation"           => $this->compt_validation,
                ":compt_actif"                => $this->compt_actif,
                ":compt_habilitation"         => $this->compt_habilitation,
                ":compt_dcreat"               => $this->compt_dcreat,
                ":compt_der_connex"           => $this->compt_der_connex,
                ":compt_ip"                   => $this->compt_ip,
                ":compt_commentaire"          => $this->compt_commentaire,
                ":compt_renvoye"              => $this->compt_renvoye,
                ":compt_monstre"              => $this->compt_monstre,
                ":compt_testeur"              => $this->compt_testeur,
                ":compt_admin"                => $this->compt_admin,
                ":compt_hibernation"          => $this->compt_hibernation,
                ":compt_dfin_hiber"           => $this->compt_dfin_hiber,
                ":compt_acc_charte"           => $this->compt_acc_charte,
                ":compt_confiance"            => $this->compt_confiance,
                ":compt_ddeb_hiber"           => $this->compt_ddeb_hiber,
                ":compt_quete"                => $this->compt_quete,
                ":compt_envoi_mail"           => $this->compt_envoi_mail,
                ":compt_envoi_mail_message"   => $this->compt_envoi_mail_message,
                ":compt_der_news"             => $this->compt_der_news,
                ":compt_vue_desc"             => $this->compt_vue_desc,
                ":compt_ligne_perso"          => $this->compt_ligne_perso,
                ":compt_wikidev"              => $this->compt_wikidev,
                ":compt_compte_lie"           => $this->compt_compte_lie,
                ":compt_quatre_perso"         => $this->compt_quatre_perso,
                ":compt_der_perso_cod"        => $this->compt_der_perso_cod,
                ":compt_fb"                   => $this->compt_fb,
                ":compt_twitter"              => $this->compt_twitter,
                ":compt_google"               => $this->compt_google,
                ":compt_frameless"            => $this->compt_frameless,
                ":compt_envoi_mail_frequence" => $this->compt_envoi_mail_frequence,
                ":compt_envoi_mail_dernier"   => $this->compt_envoi_mail_dernier,
                ":compt_type_quatrieme"       => $this->compt_type_quatrieme,
                ":compt_clef_forum"           => $this->compt_clef_forum,
                ":compt_validite_clef_forum"  => $this->compt_validite_clef_forum,
                ":compt_nombre_clef_forum"    => $this->compt_nombre_clef_forum,
                ":compt_phashword"            => $this->compt_phashword,
                ":compt_clef_reinit_mdp"      => $this->compt_clef_reinit_mdp,
                ":compt_passwd_hash"          => $this->compt_passwd_hash,
            ), $stmt);
        }
    }

}
