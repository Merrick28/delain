<?php
/**
 * includes/class.objets_caracs.php
 */

/**
 * Class objets_caracs
 *
 * Gère les objets BDD de la table objets_caracs
 */
class objets_caracs
{
    var $obcar_cod;
    var $obcar_des_degats;
    var $obcar_val_des_degats;
    var $obcar_bonus_degats;
    var $obcar_armure;
    var $obcar_nom;
    var $obcar_distance;
    var $obcar_chute;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de objets_caracs
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from objets_caracs where obcar_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->obcar_cod            = $result['obcar_cod'];
        $this->obcar_des_degats     = $result['obcar_des_degats'];
        $this->obcar_val_des_degats = $result['obcar_val_des_degats'];
        $this->obcar_bonus_degats   = $result['obcar_bonus_degats'];
        $this->obcar_armure         = $result['obcar_armure'];
        $this->obcar_nom            = $result['obcar_nom'];
        $this->obcar_distance       = $result['obcar_distance'];
        $this->obcar_chute          = $result['obcar_chute'];
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
            $req  = "insert into objets_caracs (
            obcar_des_degats,
            obcar_val_des_degats,
            obcar_bonus_degats,
            obcar_armure,
            obcar_nom,
            obcar_distance,
            obcar_chute                        )
                    values
                    (
                        :obcar_des_degats,
                        :obcar_val_des_degats,
                        :obcar_bonus_degats,
                        :obcar_armure,
                        :obcar_nom,
                        :obcar_distance,
                        :obcar_chute                        )
    returning obcar_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":obcar_des_degats"     => $this->obcar_des_degats,
                                      ":obcar_val_des_degats" => $this->obcar_val_des_degats,
                                      ":obcar_bonus_degats"   => $this->obcar_bonus_degats,
                                      ":obcar_armure"         => $this->obcar_armure,
                                      ":obcar_nom"            => $this->obcar_nom,
                                      ":obcar_distance"       => $this->obcar_distance,
                                      ":obcar_chute"          => $this->obcar_chute,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update objets_caracs
                    set
            obcar_des_degats = :obcar_des_degats,
            obcar_val_des_degats = :obcar_val_des_degats,
            obcar_bonus_degats = :obcar_bonus_degats,
            obcar_armure = :obcar_armure,
            obcar_nom = :obcar_nom,
            obcar_distance = :obcar_distance,
            obcar_chute = :obcar_chute                        where obcar_cod = :obcar_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":obcar_cod"            => $this->obcar_cod,
                                      ":obcar_des_degats"     => $this->obcar_des_degats,
                                      ":obcar_val_des_degats" => $this->obcar_val_des_degats,
                                      ":obcar_bonus_degats"   => $this->obcar_bonus_degats,
                                      ":obcar_armure"         => $this->obcar_armure,
                                      ":obcar_nom"            => $this->obcar_nom,
                                      ":obcar_distance"       => $this->obcar_distance,
                                      ":obcar_chute"          => $this->obcar_chute,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \objets_caracs
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select obcar_cod  from objets_caracs order by obcar_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new objets_caracs;
            $temp->charge($result["obcar_cod"]);
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
                    $req    = "select obcar_cod  from objets_caracs where " . substr($name, 6) . " = ? order by obcar_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new objets_caracs;
                        $temp->charge($result["obcar_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table objets_caracs');
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