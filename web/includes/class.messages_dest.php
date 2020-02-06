<?php

/**
 * includes/class.messages_dest.php
 */

/**
 * Class messages_dest
 *
 * Gère les objets BDD de la table messages_dest
 */
class messages_dest
{
    public $dmsg_cod;
    public $dmsg_msg_cod;
    public $dmsg_perso_cod;
    public $dmsg_lu      = 'N';
    public $dmsg_archive = 'N';
    public $dmsg_efface  = 0;

    public function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de messages_dest
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    public function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from messages_dest where dmsg_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->dmsg_cod       = $result['dmsg_cod'];
        $this->dmsg_msg_cod   = $result['dmsg_msg_cod'];
        $this->dmsg_perso_cod = $result['dmsg_perso_cod'];
        $this->dmsg_lu        = $result['dmsg_lu'];
        $this->dmsg_archive   = $result['dmsg_archive'];
        $this->dmsg_efface    = $result['dmsg_efface'];
        return true;
    }

    public function getByPersoNonLu($perso)
    {
        $pdo    = new bddpdo;
        $retour = array();
        $req    =
            "select dmsg_cod from messages_dest where dmsg_perso_cod = ? and dmsg_lu = 'N' and dmsg_archive = 'N'";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($perso), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new messages_dest;
            $temp->charge($result["dmsg_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function getByMessage($msg)
    {
        $pdo    = new bddpdo;
        $retour = array();
        $req    = "select dmsg_cod from messages_dest where dmsg_msg_cod = :message and dmsg_efface = 0";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":message" => $msg), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new messages_dest;
            $temp->charge($result["dmsg_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /**
     * @param $perso_cod
     * @param $msg
     * @return messages_dest[] array
     * @throws Exception
     */
    public function getByPersoMessage($perso_cod, $msg)
    {
        $pdo    = new bddpdo;
        $retour = array();
        $req    = "select dmsg_cod from messages_dest 
                where dmsg_msg_cod = :message 
                  and dmsg_perso_cod = :perso
                  and dmsg_efface = 0";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":message" => $msg, ":perso" => $perso_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new messages_dest;
            $temp->charge($result["dmsg_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function getByPerso($perso, $offset = 0, $limit = 50)
    {
        $pdo    = new bddpdo;
        $retour = array();
        $req    = "select dmsg_cod from messages_dest where dmsg_perso_cod = ? limit $limit offset $offset";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($perso), $stmt);

        while ($result = $stmt->fetch())
        {
            $temp = new messages_dest;
            $temp->charge($result["dmsg_cod"]);
            // on va charger le message
            $message = new messages;
            $message->charge($temp->dmsg_msg_cod);
            $temp->message = $message;
            unset($message);

            // on va chercher l'expéditeur
            $messages_exp = new messages_exp;
            if ($messages_exp->getByMsg($temp->dmsg_msg_cod) !== false)
            {
                $temp->messages_exp = $messages_exp;
            }
            unset($messages_exp);

            // on va chercher les destinataires
            $messages_dest2      = new messages_dest;
            $temp->messages_dest = $messages_dest2->getByMessage($temp->dmsg_msg_cod);
            unset($messages_dest2);

            // on nettoie
            unset($temp->dmsg_cod);
            unset($temp->dmsg_msg_cod);
            unset($temp->dmsg_perso_cod);
            unset($temp->dmsg_lu);
            unset($temp->dmsg_archive);
            unset($temp->dmsg_efface);


            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     * @global bdd_mysql $pdo
     */
    public function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into messages_dest (
                                        dmsg_msg_cod,
                                        dmsg_perso_cod,
                                        dmsg_lu,
                                        dmsg_archive,
                                        dmsg_efface                                        )
                    values
                    (
                                        :dmsg_msg_cod,
                                        :dmsg_perso_cod,
                                        :dmsg_lu,
                                        :dmsg_archive,
                                        :dmsg_efface                                        )
                    returning dmsg_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":dmsg_msg_cod"   => $this->dmsg_msg_cod,
                                      ":dmsg_perso_cod" => $this->dmsg_perso_cod,
                                      ":dmsg_lu"        => $this->dmsg_lu,
                                      ":dmsg_archive"   => $this->dmsg_archive,
                                      ":dmsg_efface"    => $this->dmsg_efface,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update messages_dest
                    set
                                dmsg_msg_cod = :dmsg_msg_cod,
                                        dmsg_perso_cod = :dmsg_perso_cod,
                                        dmsg_lu = :dmsg_lu,
                                        dmsg_archive = :dmsg_archive,
                                        dmsg_efface = :dmsg_efface                                        where dmsg_cod = :dmsg_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":dmsg_cod"       => $this->dmsg_cod,
                                      ":dmsg_msg_cod"   => $this->dmsg_msg_cod,
                                      ":dmsg_perso_cod" => $this->dmsg_perso_cod,
                                      ":dmsg_lu"        => $this->dmsg_lu,
                                      ":dmsg_archive"   => $this->dmsg_archive,
                                      ":dmsg_efface"    => $this->dmsg_efface,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \messages_dest
     * @global bdd_mysql $pdo
     */
    public function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select dmsg_cod  from messages_dest order by dmsg_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new messages_dest;
            $temp->charge($result["dmsg_cod"]);
            $retour[] = $temp;
            unset($temp);
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
                    $req    =
                        "select dmsg_cod  from messages_dest where " . substr($name, 6) . " = ? order by dmsg_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new messages_dest;
                        $temp->charge($result["dmsg_cod"]);
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
