<?php
/**
 * includes/class.finances.php
 */

/**
 * Class finances
 *
 * Gère les objets BDD de la table finances
 */
class finances
{
    var $fin_cod;
    var $fin_date;
    var $fin_desc;
    var $fin_montant;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de finances
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM finances WHERE fin_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->fin_cod     = $result['fin_cod'];
        $this->fin_date    = $result['fin_date'];
        $this->fin_desc    = $result['fin_desc'];
        $this->fin_montant = $result['fin_montant'];
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
            $req
                  = "INSERT INTO finances (
            fin_date,
            fin_desc,
            fin_montant                        )
                    VALUES
                    (
                        :fin_date,
                        :fin_desc,
                        :fin_montant                        )
    RETURNING fin_cod AS id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":fin_date"    => $this->fin_date,
                                      ":fin_desc"    => $this->fin_desc,
                                      ":fin_montant" => $this->fin_montant,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req
                  = "UPDATE finances
                    SET
            fin_date = :fin_date,
            fin_desc = :fin_desc,
            fin_montant = :fin_montant                        WHERE fin_cod = :fin_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":fin_cod"     => $this->fin_cod,
                                      ":fin_date"    => $this->fin_date,
                                      ":fin_desc"    => $this->fin_desc,
                                      ":fin_montant" => $this->fin_montant,
                                  ), $stmt);
        }
    }

    function getMinDate()
    {
        $pdo    = new bddpdo;
        $req    = "SELECT min(fin_date) AS fin_date FROM finances";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(), $stmt);
        $result = $stmt->fetch();
        return $result['fin_date'];
    }

    function getMaxDate()
    {
        $pdo    = new bddpdo;
        $req    = "SELECT max(fin_date) AS fin_date FROM finances";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(), $stmt);
        $result = $stmt->fetch();
        return $result['fin_date'];
    }

    function getByDate($month, $year)
    {
        $date_min = $year . '-' . $month . '-01';
        $date_max = date('Y-m-d', (strtotime('+1 month', strtotime($date_min)) - 1));
        $pdo      = new bddpdo;
        $req      = "SELECT fin_cod  FROM finances WHERE fin_date >= ?  AND fin_date <= ? ORDER BY fin_date";
        $stmt     = $pdo->prepare($req);
        $stmt     = $pdo->execute(array($date_min,
                                        $date_max), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new finances;
            $temp->charge($result["fin_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getSyntheseByDate($month, $year)
    {
        $retour   = array();
        $total    = 0;
        $date_min = $year . '-' . $month . '-01';
        $date_max = date('Y-m-d', (strtotime('+1 month', strtotime($date_min)) - 1));
        $pdo      = new bddpdo;
        $req
                  = "SELECT fin_desc,sum(fin_montant) AS fin_montant FROM finances WHERE fin_date >= ?  AND fin_date <= ? 
          GROUP BY fin_desc";
        $stmt     = $pdo->prepare($req);
        $stmt     = $pdo->execute(array($date_min,
                                        $date_max), $stmt);
        while ($result = $stmt->fetch())
        {

            $temp['montant'] = $result['fin_montant'];
            $temp['desc']    = $result['fin_desc'];
            $retour[]        = $temp;
        }
        return $retour;
    }

    function getTotalByDate($month, $year, $sens = 0)
    {
        $retour   = array();
        $total    = 0;
        $date_min = $year . '-' . $month . '-01';
        $date_max = date('Y-m-d', (strtotime('+1 month', strtotime($date_min)) - 1));
        $pdo      = new bddpdo;
        if($sens == 0)
        {
            $req
                = "SELECT coalesce(sum(fin_montant),0) AS fin_montant FROM finances WHERE fin_date >= ?  AND fin_date <= ? ";
        }
        if($sens == -1)
        {
            $req
                = "SELECT coalesce(sum(fin_montant),0) AS fin_montant FROM finances WHERE fin_date >= ?  AND fin_date <= ? 
                    and fin_montant < 0";
        }
        if($sens == 1)
        {
            $req
                = "SELECT coalesce(sum(fin_montant),0) AS fin_montant FROM finances WHERE fin_date >= ?  AND fin_date <= ? 
                    and fin_montant >= 0";
        }
        $stmt     = $pdo->prepare($req);
        $stmt     = $pdo->execute(array($date_min,
                                        $date_max), $stmt);
        $result   = $stmt->fetch();
        return $result['fin_montant'];

    }

    function getDateUpdate()
    {
        $pdo    = new bddpdo;
        $req
                = "SELECT max(fin_date) AS fin_date FROM finances where fin_date <= now() ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(), $stmt);
        $result = $stmt->fetch();
        return $result['fin_date'];
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \finances
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "SELECT fin_cod  FROM finances ORDER BY fin_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new finances;
            $temp->charge($result["fin_cod"]);
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
                    $req    = "SELECT fin_cod  FROM finances WHERE " . substr($name, 6) . " = ? ORDER BY fin_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new finances;
                        $temp->charge($result["fin_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table finances');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}