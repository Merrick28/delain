<?php
/**
 * includes/class.bonus.php
 */

/**
 * Class bonus
 *
 * Gère les objets BDD de la table bonus
 */
class bonus
{
    var $bonus_cod;
    var $bonus_perso_cod;
    var $bonus_nb_tours   = 2;
    var $bonus_tbonus_libc;
    var $bonus_valeur;
    var $bonus_croissance = 0;
    var $bonus_mode       = 'S';
    var $bonus_degressivite;
    var $bonus_obj_cod;
    var $bonus_objbm_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de bonus
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from bonus where bonus_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->bonus_cod          = $result['bonus_cod'];
        $this->bonus_perso_cod    = $result['bonus_perso_cod'];
        $this->bonus_nb_tours     = $result['bonus_nb_tours'];
        $this->bonus_tbonus_libc  = $result['bonus_tbonus_libc'];
        $this->bonus_valeur       = $result['bonus_valeur'];
        $this->bonus_croissance   = $result['bonus_croissance'];
        $this->bonus_mode         = $result['bonus_mode'];
        $this->bonus_degressivite = $result['bonus_degressivite'];
        $this->bonus_obj_cod      = $result['bonus_obj_cod'];
        $this->bonus_objbm_cod    = $result['bonus_objbm_cod'];
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
            $req  = "insert into bonus (
            bonus_perso_cod,
            bonus_nb_tours,
            bonus_tbonus_libc,
            bonus_valeur,
            bonus_croissance,
            bonus_mode,
            bonus_degressivite,
            bonus_obj_cod,
            bonus_objbm_cod                        )
                    values
                    (
                        :bonus_perso_cod,
                        :bonus_nb_tours,
                        :bonus_tbonus_libc,
                        :bonus_valeur,
                        :bonus_croissance,
                        :bonus_mode,
                        :bonus_degressivite,
                        :bonus_obj_cod,
                        :bonus_objbm_cod                        )
    returning bonus_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":bonus_perso_cod"    => $this->bonus_perso_cod,
                                      ":bonus_nb_tours"     => $this->bonus_nb_tours,
                                      ":bonus_tbonus_libc"  => $this->bonus_tbonus_libc,
                                      ":bonus_valeur"       => $this->bonus_valeur,
                                      ":bonus_croissance"   => $this->bonus_croissance,
                                      ":bonus_mode"         => $this->bonus_mode,
                                      ":bonus_degressivite" => $this->bonus_degressivite,
                                      ":bonus_obj_cod"      => $this->bonus_obj_cod,
                                      ":bonus_objbm_cod"    => $this->bonus_objbm_cod,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update bonus
                    set
            bonus_perso_cod = :bonus_perso_cod,
            bonus_nb_tours = :bonus_nb_tours,
            bonus_tbonus_libc = :bonus_tbonus_libc,
            bonus_valeur = :bonus_valeur,
            bonus_croissance = :bonus_croissance,
            bonus_mode = :bonus_mode,
            bonus_degressivite = :bonus_degressivite,
            bonus_obj_cod = :bonus_obj_cod,
            bonus_objbm_cod = :bonus_objbm_cod                        where bonus_cod = :bonus_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":bonus_cod"          => $this->bonus_cod,
                                      ":bonus_perso_cod"    => $this->bonus_perso_cod,
                                      ":bonus_nb_tours"     => $this->bonus_nb_tours,
                                      ":bonus_tbonus_libc"  => $this->bonus_tbonus_libc,
                                      ":bonus_valeur"       => $this->bonus_valeur,
                                      ":bonus_croissance"   => $this->bonus_croissance,
                                      ":bonus_mode"         => $this->bonus_mode,
                                      ":bonus_degressivite" => $this->bonus_degressivite,
                                      ":bonus_obj_cod"      => $this->bonus_obj_cod,
                                      ":bonus_objbm_cod"    => $this->bonus_objbm_cod,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \bonus
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select bonus_cod  from bonus order by bonus_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new bonus;
            $temp->charge($result["bonus_cod"]);
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
                    $req    = "select bonus_cod  from bonus where " . substr($name, 6) . " = ? order by bonus_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new bonus;
                        $temp->charge($result["bonus_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table bonus');
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