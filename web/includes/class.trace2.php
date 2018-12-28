<?php
/**
 * includes/class.trace2.php
 */

/**
 * Class trace2
 *
 * Gère les objets BDD de la table trace2
 */
class trace2
{
    var $trace2_cod;
    var $trace2_texte;
    var $trace2_date;

    function __construct()
    {

        $this->trace2_date = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de trace2
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from trace2 where trace2_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->trace2_cod   = $result['trace2_cod'];
        $this->trace2_texte = $result['trace2_texte'];
        $this->trace2_date  = $result['trace2_date'];
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
            $req  = "insert into trace2 (
            trace2_texte,
            trace2_date                        )
                    values
                    (
                        :trace2_texte,
                        :trace2_date                        )
    returning trace2_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":trace2_texte" => $this->trace2_texte,
                ":trace2_date"  => $this->trace2_date,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update trace2
                    set
            trace2_texte = :trace2_texte,
            trace2_date = :trace2_date                        where trace2_cod = :trace2_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":trace2_cod"   => $this->trace2_cod,
                ":trace2_texte" => $this->trace2_texte,
                ":trace2_date"  => $this->trace2_date,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \trace2
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select trace2_cod  from trace2 order by trace2_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new trace2;
            $temp->charge($result["trace2_cod"]);
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
                    $req    = "select trace2_cod  from trace2 where " . substr($name, 6) . " = ? order by trace2_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new trace2;
                        $temp->charge($result["trace2_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table trace2');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}