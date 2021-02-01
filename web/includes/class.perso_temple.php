<?php
/**
 * includes/class.perso_temple.php
 */

/**
 * Class perso_temple
 *
 * Gère les objets BDD de la table perso_temple
 */
class perso_temple
{
    var $ptemple_cod;
    var $ptemple_perso_cod;
    var $ptemple_pos_cod;
    var $ptemple_nombre = 0;
    var $ptemple_anc_pos_cod;
    var $ptemple_anc_nombre;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_temple
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_temple where ptemple_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->ptemple_cod         = $result['ptemple_cod'];
        $this->ptemple_perso_cod   = $result['ptemple_perso_cod'];
        $this->ptemple_pos_cod     = $result['ptemple_pos_cod'];
        $this->ptemple_nombre      = $result['ptemple_nombre'];
        $this->ptemple_anc_pos_cod = $result['ptemple_anc_pos_cod'];
        $this->ptemple_anc_nombre  = $result['ptemple_anc_nombre'];
        return true;
    }

    function efface()
    {
        $pdo  = new bddpdo;
        $req  = "delete from perso_temple where ptemple_cod = :pk";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":pk" => $this->ptemple_cod
                              ), $stmt);

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
            $req  = "insert into perso_temple (
            ptemple_perso_cod,
            ptemple_pos_cod,
            ptemple_nombre,
            ptemple_anc_pos_cod,
            ptemple_anc_nombre                        )
                    values
                    (
                        :ptemple_perso_cod,
                        :ptemple_pos_cod,
                        :ptemple_nombre,
                        :ptemple_anc_pos_cod,
                        :ptemple_anc_nombre                        )
    returning ptemple_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":ptemple_perso_cod"   => $this->ptemple_perso_cod,
                                      ":ptemple_pos_cod"     => $this->ptemple_pos_cod,
                                      ":ptemple_nombre"      => $this->ptemple_nombre,
                                      ":ptemple_anc_pos_cod" => $this->ptemple_anc_pos_cod,
                                      ":ptemple_anc_nombre"  => $this->ptemple_anc_nombre,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update perso_temple
                    set
            ptemple_perso_cod = :ptemple_perso_cod,
            ptemple_pos_cod = :ptemple_pos_cod,
            ptemple_nombre = :ptemple_nombre,
            ptemple_anc_pos_cod = :ptemple_anc_pos_cod,
            ptemple_anc_nombre = :ptemple_anc_nombre                        where ptemple_cod = :ptemple_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":ptemple_cod"         => $this->ptemple_cod,
                                      ":ptemple_perso_cod"   => $this->ptemple_perso_cod,
                                      ":ptemple_pos_cod"     => $this->ptemple_pos_cod,
                                      ":ptemple_nombre"      => $this->ptemple_nombre,
                                      ":ptemple_anc_pos_cod" => $this->ptemple_anc_pos_cod,
                                      ":ptemple_anc_nombre"  => $this->ptemple_anc_nombre,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return perso_temple
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select ptemple_cod  from perso_temple order by ptemple_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_temple;
            $temp->charge($result["ptemple_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPerso($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select ptemple_cod  from perso_temple where ptemple_perso_cod = :perso_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['ptemple_cod']);
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
                        "select ptemple_cod  from perso_temple where " . substr($name, 6) . " = ? order by ptemple_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_temple;
                        $temp->charge($result["ptemple_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_temple');
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