<?php
/**
 * includes/class.guilde_revolution.php
 */

/**
 * Class guilde_revolution
 *
 * Gère les objets BDD de la table guilde_revolution
 */
class guilde_revolution
{
    var $revguilde_cod;
    var $revguilde_guilde_cod;
    var $revguilde_lanceur;
    var $revguilde_cible;
    var $revguilde_datfin;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de guilde_revolution
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from guilde_revolution where revguilde_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->revguilde_cod        = $result['revguilde_cod'];
        $this->revguilde_guilde_cod = $result['revguilde_guilde_cod'];
        $this->revguilde_lanceur    = $result['revguilde_lanceur'];
        $this->revguilde_cible      = $result['revguilde_cible'];
        $this->revguilde_datfin     = $result['revguilde_datfin'];
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
            $req  = "insert into guilde_revolution (
            revguilde_guilde_cod,
            revguilde_lanceur,
            revguilde_cible,
            revguilde_datfin                        )
                    values
                    (
                        :revguilde_guilde_cod,
                        :revguilde_lanceur,
                        :revguilde_cible,
                        :revguilde_datfin                        )
    returning revguilde_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":revguilde_guilde_cod" => $this->revguilde_guilde_cod,
                ":revguilde_lanceur"    => $this->revguilde_lanceur,
                ":revguilde_cible"      => $this->revguilde_cible,
                ":revguilde_datfin"     => $this->revguilde_datfin,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update guilde_revolution
                    set
            revguilde_guilde_cod = :revguilde_guilde_cod,
            revguilde_lanceur = :revguilde_lanceur,
            revguilde_cible = :revguilde_cible,
            revguilde_datfin = :revguilde_datfin                        where revguilde_cod = :revguilde_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":revguilde_cod"        => $this->revguilde_cod,
                ":revguilde_guilde_cod" => $this->revguilde_guilde_cod,
                ":revguilde_lanceur"    => $this->revguilde_lanceur,
                ":revguilde_cible"      => $this->revguilde_cible,
                ":revguilde_datfin"     => $this->revguilde_datfin,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \guilde_revolution
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select revguilde_cod  from guilde_revolution order by revguilde_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new guilde_revolution;
            $temp->charge($result["revguilde_cod"]);
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
                    $req    = "select revguilde_cod  from guilde_revolution where " . substr($name, 6) . " = ? order by revguilde_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new guilde_revolution;
                        $temp->charge($result["revguilde_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table guilde_revolution');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}