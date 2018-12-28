<?php
/**
 * includes/class.pos_sort_interdit.php
 */

/**
 * Class pos_sort_interdit
 *
 * Gère les objets BDD de la table pos_sort_interdit
 */
class pos_sort_interdit
{
    var $sinterd_cod;
    var $sinterd_pos_cod;
    var $sinterd_sort_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de pos_sort_interdit
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from pos_sort_interdit where sinterd_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->sinterd_cod      = $result['sinterd_cod'];
        $this->sinterd_pos_cod  = $result['sinterd_pos_cod'];
        $this->sinterd_sort_cod = $result['sinterd_sort_cod'];
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
            $req  = "insert into pos_sort_interdit (
            sinterd_pos_cod,
            sinterd_sort_cod                        )
                    values
                    (
                        :sinterd_pos_cod,
                        :sinterd_sort_cod                        )
    returning sinterd_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":sinterd_pos_cod"  => $this->sinterd_pos_cod,
                ":sinterd_sort_cod" => $this->sinterd_sort_cod,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update pos_sort_interdit
                    set
            sinterd_pos_cod = :sinterd_pos_cod,
            sinterd_sort_cod = :sinterd_sort_cod                        where sinterd_cod = :sinterd_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":sinterd_cod"      => $this->sinterd_cod,
                ":sinterd_pos_cod"  => $this->sinterd_pos_cod,
                ":sinterd_sort_cod" => $this->sinterd_sort_cod,
            ), $stmt);
        }
    }

    function is_sort_interdit($sort, $pos)
    {
        $pdo  = new bddpdo;
        $req  = 'select sinterd_pos_cod, 
          sinterd_sort_cod 
          from pos_sort_interdit 
          where sinterd_sort_cod = :sort and sinterd_pos_cod = :pos_cod';
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":sort"    => $sort,
            ":pos_cod" => $pos->pos_cod
        ), $stmt);
        if($stmt->fecth())
        {
            return true;
        }
        return false;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \pos_sort_interdit
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select sinterd_cod  from pos_sort_interdit order by sinterd_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new pos_sort_interdit;
            $temp->charge($result["sinterd_cod"]);
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
                    $req    = "select sinterd_cod  from pos_sort_interdit where " . substr($name, 6) . " = ? order by sinterd_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new pos_sort_interdit;
                        $temp->charge($result["sinterd_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table pos_sort_interdit');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}