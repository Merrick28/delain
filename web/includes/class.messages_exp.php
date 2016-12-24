<?php

/**
 * includes/class.messages_exp.php
 */

/**
 * Class messages_exp
 *
 * Gère les objets BDD de la table messages_exp
 */
class messages_exp
{

    var $emsg_cod;
    var $emsg_msg_cod;
    var $emsg_perso_cod;
    var $emsg_archive = 'N';
    var $emsg_lu;

    function __construct()
    {
        
    }

    /**
     * Charge dans la classe un enregistrement de messages_exp
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo    = new bddpdo;
        $req    = "select * from messages_exp where emsg_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->emsg_cod       = $result['emsg_cod'];
        $this->emsg_msg_cod   = $result['emsg_msg_cod'];
        $this->emsg_perso_cod = $result['emsg_perso_cod'];
        $this->emsg_archive   = $result['emsg_archive'];
        $this->emsg_lu        = $result['emsg_lu'];
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
            $req  = "insert into messages_exp (
            emsg_msg_cod,
            emsg_perso_cod,
            emsg_archive,
            emsg_lu                        )
                    values
                    (
                        :emsg_msg_cod,
                        :emsg_perso_cod,
                        :emsg_archive,
                        :emsg_lu                        )
    returning emsg_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":emsg_msg_cod"   => $this->emsg_msg_cod,
               ":emsg_perso_cod" => $this->emsg_perso_cod,
               ":emsg_archive"   => $this->emsg_archive,
               ":emsg_lu"        => $this->emsg_lu,
               ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update messages_exp
                    set
            emsg_msg_cod = :emsg_msg_cod,
            emsg_perso_cod = :emsg_perso_cod,
            emsg_archive = :emsg_archive,
            emsg_lu = :emsg_lu                        where emsg_cod = :emsg_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":emsg_cod"       => $this->emsg_cod,
               ":emsg_msg_cod"   => $this->emsg_msg_cod,
               ":emsg_perso_cod" => $this->emsg_perso_cod,
               ":emsg_archive"   => $this->emsg_archive,
               ":emsg_lu"        => $this->emsg_lu,
               ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \messages_exp
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select emsg_cod  from messages_exp order by emsg_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp     = new messages_exp;
            $temp->charge($result["emsg_cod"]);
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
                    $req    = "select emsg_cod  from messages_exp where " . substr($name, 6) . " = ? order by emsg_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp     = new messages_exp;
                        $temp->charge($result["emsg_cod"]);
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
                    die('Unknown variable ' . substr($name,6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }

}
