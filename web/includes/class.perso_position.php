<?php
/**
 * includes/class.perso_position.php
 */

/**
 * Class perso_position
 *
 * Gère les objets BDD de la table perso_position
 */
class perso_position
{
    var $ppos_cod;
    var $ppos_pos_cod;
    var $ppos_perso_cod;

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
            $req = "insert into perso_position (
            ppos_pos_cod,
            ppos_perso_cod                        )
                    values
                    (
                        :ppos_pos_cod,
                        :ppos_perso_cod                        )
    returning ppos_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":ppos_pos_cod" => $this->ppos_pos_cod,
                ":ppos_perso_cod" => $this->ppos_perso_cod,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update perso_position
                    set
            ppos_pos_cod = :ppos_pos_cod,
            ppos_perso_cod = :ppos_perso_cod                        where ppos_cod = :ppos_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":ppos_cod" => $this->ppos_cod,
                ":ppos_pos_cod" => $this->ppos_pos_cod,
                ":ppos_perso_cod" => $this->ppos_perso_cod,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de perso_position
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from perso_position where ppos_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->ppos_cod = $result['ppos_cod'];
        $this->ppos_pos_cod = $result['ppos_pos_cod'];
        $this->ppos_perso_cod = $result['ppos_perso_cod'];
        return true;
    }

    function getByPerso($perso_cod)
    {
        $pdo = new bddpdo;
        $req = "select ppos_cod from perso_position where ppos_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['ppos_cod']);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_position
     */
    function getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select ppos_cod  from perso_position order by ppos_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_position;
            $temp->charge($result["ppos_cod"]);
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
                    $req = "select ppos_cod  from perso_position where " . substr($name, 6) . " = ? order by ppos_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_position;
                        $temp->charge($result["ppos_cod"]);
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
                ob_start();
                debug_print_backtrace();
                $out = ob_get_contents();
                error_log($out);
                die('Unknown method.');
        }
    }
}