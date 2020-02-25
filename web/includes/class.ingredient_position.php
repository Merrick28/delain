<?php
/**
 * includes/class.ingredient_position.php
 */

/**
 * Class ingredient_position
 *
 * Gère les objets BDD de la table ingredient_position
 */
class ingredient_position
{
    var $ingrpos_cod;
    var $ingrpos_pos_cod;
    var $ingrpos_gobj_cod;
    var $ingrpos_max;
    var $ingrpos_chance_crea;
    var $ingrpos_qte = 1;

    function __construct()
    {
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
            $req  = "insert into ingredient_position (
            ingrpos_pos_cod,
            ingrpos_gobj_cod,
            ingrpos_max,
            ingrpos_chance_crea,
            ingrpos_qte                        )
                    values
                    (
                        :ingrpos_pos_cod,
                        :ingrpos_gobj_cod,
                        :ingrpos_max,
                        :ingrpos_chance_crea,
                        :ingrpos_qte                        )
    returning ingrpos_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":ingrpos_pos_cod"     => $this->ingrpos_pos_cod,
                                      ":ingrpos_gobj_cod"    => $this->ingrpos_gobj_cod,
                                      ":ingrpos_max"         => $this->ingrpos_max,
                                      ":ingrpos_chance_crea" => $this->ingrpos_chance_crea,
                                      ":ingrpos_qte"         => $this->ingrpos_qte,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update ingredient_position
                    set
            ingrpos_pos_cod = :ingrpos_pos_cod,
            ingrpos_gobj_cod = :ingrpos_gobj_cod,
            ingrpos_max = :ingrpos_max,
            ingrpos_chance_crea = :ingrpos_chance_crea,
            ingrpos_qte = :ingrpos_qte                        where ingrpos_cod = :ingrpos_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":ingrpos_cod"         => $this->ingrpos_cod,
                                      ":ingrpos_pos_cod"     => $this->ingrpos_pos_cod,
                                      ":ingrpos_gobj_cod"    => $this->ingrpos_gobj_cod,
                                      ":ingrpos_max"         => $this->ingrpos_max,
                                      ":ingrpos_chance_crea" => $this->ingrpos_chance_crea,
                                      ":ingrpos_qte"         => $this->ingrpos_qte,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de ingredient_position
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from ingredient_position where ingrpos_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->ingrpos_cod         = $result['ingrpos_cod'];
        $this->ingrpos_pos_cod     = $result['ingrpos_pos_cod'];
        $this->ingrpos_gobj_cod    = $result['ingrpos_gobj_cod'];
        $this->ingrpos_max         = $result['ingrpos_max'];
        $this->ingrpos_chance_crea = $result['ingrpos_chance_crea'];
        $this->ingrpos_qte         = $result['ingrpos_qte'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return ingredient_position
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select ingrpos_cod  from ingredient_position order by ingrpos_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new ingredient_position;
            $temp->charge($result["ingrpos_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPos($pos_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select ingrpos_cod  from ingredient_position where ingrpos_pos_cod = :pos";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":pos" => $pos_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new ingredient_position;
            $temp->charge($result["ingrpos_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        if (count($retour) == 0)
        {
            return false;
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
                    $req    =
                        "select ingrpos_cod  from ingredient_position where " . substr($name, 6) . " = ? order by ingrpos_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new ingredient_position;
                        $temp->charge($result["ingrpos_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table ingredient_position');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}