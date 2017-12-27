<?php
/**
 * includes/class.renommee_magie.php
 */

/**
 * Class renommee_magie
 *
 * Gère les objets BDD de la table renommee_magie
 */
class renommee_magie
{
    var $grenommee_cod;
    var $grenommee_min;
    var $grenommee_max;
    var $grenommee_libelle;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de renommee_magie
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from renommee_magie where  = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->grenommee_cod     = $result['grenommee_cod'];
        $this->grenommee_min     = $result['grenommee_min'];
        $this->grenommee_max     = $result['grenommee_max'];
        $this->grenommee_libelle = $result['grenommee_libelle'];
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
                  = "insert into renommee_magie (
            grenommee_cod,
            grenommee_min,
            grenommee_max,
            grenommee_libelle                        )
                    values
                    (
                        :grenommee_cod,
                        :grenommee_min,
                        :grenommee_max,
                        :grenommee_libelle                        )
    returning  as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":grenommee_cod"     => $this->grenommee_cod,
                ":grenommee_min"     => $this->grenommee_min,
                ":grenommee_max"     => $this->grenommee_max,
                ":grenommee_libelle" => $this->grenommee_libelle,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req
                  = "update renommee_magie
                    set
            grenommee_cod = :grenommee_cod,
            grenommee_min = :grenommee_min,
            grenommee_max = :grenommee_max,
            grenommee_libelle = :grenommee_libelle                        where  = : ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":grenommee_cod"     => $this->grenommee_cod,
                ":grenommee_min"     => $this->grenommee_min,
                ":grenommee_max"     => $this->grenommee_max,
                ":grenommee_libelle" => $this->grenommee_libelle,
            ), $stmt);
        }
    }

    function charge_by_valeur($valeur)
    {
        $pdo = new bddpdo();
        $req = "select grenommee_cod from renommee_magie
          where grenommee_min <= :valeur
          and grenommee_max > :valeur";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":valeur" => floor($valeur)
        ),$stmt);
        $result = $stmt->fetch();
        $this->charge($result['grenommee_cod']);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \renommee_magie
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select   from renommee_magie order by ";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new renommee_magie;
            $temp->charge($result[""]);
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
                    $req    = "select   from renommee_magie where " . substr($name, 6) . " = ? order by ";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new renommee_magie;
                        $temp->charge($result[""]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table renommee_magie');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}