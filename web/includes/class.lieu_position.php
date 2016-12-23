<?php
/**
 * includes/class.lieu_position.php
 */

/**
 * Class lieu_position
 *
 * Gère les objets BDD de la table lieu_position
 */
class lieu_position
{
    var $lpos_cod;
    var $lpos_pos_cod;
    var $lpos_lieu_cod;

    function __construct()
    {
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
            $req = "insert into lieu_position (
            lpos_pos_cod,
            lpos_lieu_cod                        )
                    values
                    (
                        :lpos_pos_cod,
                        :lpos_lieu_cod                        )
    returning lpos_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":lpos_pos_cod" => $this->lpos_pos_cod,
                ":lpos_lieu_cod" => $this->lpos_lieu_cod,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update lieu_position
                    set
            lpos_pos_cod = :lpos_pos_cod,
            lpos_lieu_cod = :lpos_lieu_cod                        where lpos_cod = :lpos_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":lpos_cod" => $this->lpos_cod,
                ":lpos_pos_cod" => $this->lpos_pos_cod,
                ":lpos_lieu_cod" => $this->lpos_lieu_cod,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de lieu_position
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from lieu_position where lpos_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->lpos_cod = $result['lpos_cod'];
        $this->lpos_pos_cod = $result['lpos_pos_cod'];
        $this->lpos_lieu_cod = $result['lpos_lieu_cod'];
        return true;
    }

    function getByPos($pos_cod)
    {
        $pdo = new bddpdo;
        $req = "select lpos_cod from lieu_position where lpos_pos_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($pos_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['lpos_cod']);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \lieu_position
     */
    function getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select lpos_cod  from lieu_position order by lpos_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new lieu_position;
            $temp->charge($result["lpos_cod"]);
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
                    $pdo = new bddpdo;
                    $req = "select lpos_cod  from lieu_position where " . substr($name, 6) . " = ? order by lpos_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new lieu_position;
                        $temp->charge($result["lpos_cod"]);
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