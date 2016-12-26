<?php
/**
 * includes/class.perso_sorts.php
 */

/**
 * Class perso_sorts
 *
 * Gère les objets BDD de la table perso_sorts
 */
class perso_sorts
{
    var $psort_cod;
    var $psort_sort_cod;
    var $psort_perso_cod;

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
            $req  = "insert into perso_sorts (
            psort_sort_cod,
            psort_perso_cod                        )
                    values
                    (
                        :psort_sort_cod,
                        :psort_perso_cod                        )
    returning psort_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":psort_sort_cod" => $this->psort_sort_cod,
                ":psort_perso_cod" => $this->psort_perso_cod,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update perso_sorts
                    set
            psort_sort_cod = :psort_sort_cod,
            psort_perso_cod = :psort_perso_cod                        where psort_cod = :psort_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":psort_cod" => $this->psort_cod,
                ":psort_sort_cod" => $this->psort_sort_cod,
                ":psort_perso_cod" => $this->psort_perso_cod,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de perso_sorts
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_sorts where psort_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->psort_cod       = $result['psort_cod'];
        $this->psort_sort_cod  = $result['psort_sort_cod'];
        $this->psort_perso_cod = $result['psort_perso_cod'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_sorts
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select psort_cod  from perso_sorts order by psort_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_sorts;
            $temp->charge($result["psort_cod"]);
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
                    $req    = "select psort_cod  from perso_sorts where " . substr($name, 6) . " = ? order by psort_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_sorts;
                        $temp->charge($result["psort_cod"]);
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