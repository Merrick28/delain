<?php

/**
 * includes/class.messages.php
 */

/**
 * Class messages
 *
 * Gère les objets BDD de la table messages
 */
class messages
{

    var $msg_cod;
    var $msg_date;
    var $msg_titre;
    var $msg_corps;
    var $msg_date2;
    var $msg_guilde = 'N';
    var $msg_guilde_cod;
    var $msg_init;
    // champs hors database
    var $exp_perso_cod;
    var $tabDest;

    function __construct()
    {
        $this->msg_date  = date('Y-m-d H:i:s');
        $this->msg_date2 = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de messages
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from messages where msg_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->msg_cod        = $result['msg_cod'];
        $this->msg_date       = $result['msg_date'];
        $this->msg_titre      = $result['msg_titre'];
        $this->msg_corps      = $result['msg_corps'];
        $this->msg_date2      = $result['msg_date2'];
        $this->msg_guilde     = $result['msg_guilde'];
        $this->msg_guilde_cod = $result['msg_guilde_cod'];
        $this->msg_init       = $result['msg_init'];
        return true;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     * @global bdd_mysql $pdo
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into messages (
                                        msg_date,
                                        msg_titre,
                                        msg_corps,
                                        msg_date2,
                                        msg_guilde,
                                        msg_guilde_cod,
                                        msg_init                                        )
                    values
                    (
                                        :msg_date,
                                        :msg_titre,
                                        :msg_corps,
                                        :msg_date2,
                                        :msg_guilde,
                                        :msg_guilde_cod,
                                        :msg_init                                        )
                    returning msg_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":msg_date"       => $this->msg_date,
                                      ":msg_titre"      => $this->msg_titre,
                                      ":msg_corps"      => $this->msg_corps,
                                      ":msg_date2"      => $this->msg_date2,
                                      ":msg_guilde"     => $this->msg_guilde,
                                      ":msg_guilde_cod" => $this->msg_guilde_cod,
                                      ":msg_init"       => $this->msg_init,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
            // on enregistre l'expéditeur
            $exp                 = new messages_exp;
            $exp->emsg_msg_cod   = $this->msg_cod;
            $exp->emsg_perso_cod = $this->exp_perso_cod;
            $exp->emsg_lu        = 0;
            $exp->stocke(true);
            // on enregistre les destinataires
            foreach ($this->tabDest as $perso_dest)
            {
                $dest                 = new messages_dest;
                $dest->dmsg_lu        = 'N';
                $dest->dmsg_perso_cod = $perso_dest;
                $dest->dmsg_msg_cod   = $this->msg_cod;
                $dest->stocke(true);

                unset($dest);
            }
        } else
        {
            $req  = "update messages
                    set
                                msg_date = :msg_date,
                                        msg_titre = :msg_titre,
                                        msg_corps = :msg_corps,
                                        msg_date2 = :msg_date2,
                                        msg_guilde = :msg_guilde,
                                        msg_guilde_cod = :msg_guilde_cod,
                                        msg_init = :msg_init                                        where msg_cod = :msg_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":msg_cod"        => $this->msg_cod,
                                      ":msg_date"       => $this->msg_date,
                                      ":msg_titre"      => $this->msg_titre,
                                      ":msg_corps"      => $this->msg_corps,
                                      ":msg_date2"      => $this->msg_date2,
                                      ":msg_guilde"     => $this->msg_guilde,
                                      ":msg_guilde_cod" => $this->msg_guilde_cod,
                                      ":msg_init"       => $this->msg_init,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \messages
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select msg_cod  from messages order by msg_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new messages;
            $temp->charge($result["msg_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function envoi_simple($dest_cod, $exp_cod)
    {
        // il faut que le message soit déjà enregistré
        // expéditeur
        $msg_exp                 = new messages_exp();
        $msg_exp->emsg_msg_cod   = $this->msg_cod;
        $msg_exp->emsg_perso_cod = $exp_cod;
        print_r($msg_exp);
        $msg_exp->stocke(true);
        //destinaire
        $msg_dest                 = new messages_dest();
        $msg_dest->dmsg_msg_cod   = $this->msg_cod;
        $msg_dest->dmsg_perso_cod = $dest_cod;
        $msg_dest->stocke(true);

    }

    function is_auth_msg($perso_cod)
    {
        $pdo    = new bddpdo;
        $retour = false;
        $req    = 'select dmsg_cod from messages_dest where dmsg_msg_cod = :msg and dmsg_perso_cod = :perso';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $perso_cod,
                                      ":msg"   => $this->msg_cod), $stmt);

        if ($stmt->fetch())
        {
            $retour = true;
        }

        $req  = 'select emsg_cod from messages_exp where emsg_msg_cod = :msg and emsg_perso_cod = :perso';
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $perso_cod,
                                    ":msg"   => $this->msg_cod), $stmt);

        if ($stmt->fetch())
        {
            $retour = true;
        }
        return $retour;
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
                    $req    = "select msg_cod  from messages where " . substr($name, 6) . " = ? order by msg_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new messages;
                        $temp->charge($result["msg_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                } else
                {
                    die('Unknown variable ' . substr($name, 6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }

}
