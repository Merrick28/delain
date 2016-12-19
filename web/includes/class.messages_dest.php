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

    var $dmsg_cod;
    var $dmsg_msg_cod;
    var $dmsg_perso_cod;
    var $dmsg_lu      = 'N';
    var $dmsg_archive = 'N';
    var $dmsg_efface  = 0;

    function __construct()
    {
        
    }

    /**
     * Charge dans la classe un enregistrement de messages_dest
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo    = new bddpdo;
        $req    = "select * from messages_dest where dmsg_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
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
        }
        else
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
     * @global bdd_mysql $pdo
     * @return \messages_dest
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select dmsg_cod  from messages_dest order by dmsg_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp     = new messages_dest;
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
                    $req    = "select dmsg_cod  from messages_dest where " . substr($name, 6) . " = ? order by dmsg_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp     = new messages_dest;
                        $temp->charge($result["dmsg_cod"]);
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
                    die('Unknown variable.');
                }
                break;

            default:
                die('Unknown method.');
        }
    }

}
