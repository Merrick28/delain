<?php
/**
 * includes/class.perso_plan_parallele.php
 */

/**
 * Class perso_plan_parallele
 *
 * Gère les objets BDD de la table perso_plan_parallele
 */
class perso_plan_parallele
{
    var $ppp_perso_cod;
    var $ppp_pos_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_plan_parallele
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_plan_parallele where ppp_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->ppp_perso_cod = $result['ppp_perso_cod'];
        $this->ppp_pos_cod   = $result['ppp_pos_cod'];
        return true;
    }


    function delete()
    {
        $pdo  = new bddpdo;
        $req  = "delete from perso_plan_parallele where ppp_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->ppp_perso_cod), $stmt);
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
            $req  = "insert into perso_plan_parallele (
            ppp_pos_cod                        )
                    values
                    (
                        :ppp_pos_cod                        )
    returning ppp_perso_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":ppp_pos_cod" => $this->ppp_pos_cod,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update perso_plan_parallele
                    set
            ppp_pos_cod = :ppp_pos_cod                        where ppp_perso_cod = :ppp_perso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":ppp_perso_cod" => $this->ppp_perso_cod,
                ":ppp_pos_cod"   => $this->ppp_pos_cod,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return perso_plan_parallele
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select ppp_perso_cod  from perso_plan_parallele order by ppp_perso_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_plan_parallele;
            $temp->charge($result["ppp_perso_cod"]);
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
                    $req    = "select ppp_perso_cod  from perso_plan_parallele where " . substr($name, 6) . " = ? order by ppp_perso_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_plan_parallele;
                        $temp->charge($result["ppp_perso_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_plan_parallele');
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