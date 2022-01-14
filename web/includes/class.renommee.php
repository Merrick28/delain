<?php
/**
 * includes/class.renommee.php
 */

/**
 * Class renommee
 *
 * Gère les objets BDD de la table renommee
 */
class renommee
{
    var $renommee_cod;
    var $renommee_min;
    var $renommee_max;
    var $renommee_libelle;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de renommee
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from renommee where renommee_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->renommee_cod     = $result['renommee_cod'];
        $this->renommee_min     = $result['renommee_min'];
        $this->renommee_max     = $result['renommee_max'];
        $this->renommee_libelle = $result['renommee_libelle'];
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
                  = "insert into renommee (
            renommee_min,
            renommee_max,
            renommee_libelle                        )
                    values
                    (
                        :renommee_min,
                        :renommee_max,
                        :renommee_libelle                        )
    returning renommee_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":renommee_min"     => $this->renommee_min,
                ":renommee_max"     => $this->renommee_max,
                ":renommee_libelle" => $this->renommee_libelle,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req
                  = "update renommee
                    set
            renommee_min = :renommee_min,
            renommee_max = :renommee_max,
            renommee_libelle = :renommee_libelle                        where renommee_cod = :renommee_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":renommee_cod"     => $this->renommee_cod,
                ":renommee_min"     => $this->renommee_min,
                ":renommee_max"     => $this->renommee_max,
                ":renommee_libelle" => $this->renommee_libelle,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \renommee
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select renommee_cod  from renommee order by renommee_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new renommee;
            $temp->charge($result["renommee_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function charge_by_valeur($valeur)
    {
        $pdo = new bddpdo();
        $req = "select renommee_cod from renommee 
          where renommee_min <= :valeur
          and renommee_max > :valeur";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":valeur" => floor($valeur)
        ),$stmt);
        $result = $stmt->fetch();
        $this->charge($result['renommee_cod']);
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
                    $req    = "select renommee_cod  from renommee where " . substr($name, 6) . " = ? order by renommee_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new renommee;
                        $temp->charge($result["renommee_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table renommee');
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