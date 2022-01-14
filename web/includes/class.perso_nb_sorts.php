<?php
/**
 * includes/class.perso_nb_sorts.php
 */

/**
 * Class perso_nb_sorts
 *
 * Gère les objets BDD de la table perso_nb_sorts
 */
class perso_nb_sorts
{
    var $pnbs_cod;
    var $pnbs_perso_cod;
    var $pnbs_sort_cod;
    var $pnbs_nombre;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_nb_sorts
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_nb_sorts where pnbs_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pnbs_cod       = $result['pnbs_cod'];
        $this->pnbs_perso_cod = $result['pnbs_perso_cod'];
        $this->pnbs_sort_cod  = $result['pnbs_sort_cod'];
        $this->pnbs_nombre    = $result['pnbs_nombre'];
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
            $req  = "insert into perso_nb_sorts (
            pnbs_perso_cod,
            pnbs_sort_cod,
            pnbs_nombre                        )
                    values
                    (
                        :pnbs_perso_cod,
                        :pnbs_sort_cod,
                        :pnbs_nombre                        )
    returning pnbs_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pnbs_perso_cod" => $this->pnbs_perso_cod,
                ":pnbs_sort_cod"  => $this->pnbs_sort_cod,
                ":pnbs_nombre"    => $this->pnbs_nombre,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update perso_nb_sorts
                    set
            pnbs_perso_cod = :pnbs_perso_cod,
            pnbs_sort_cod = :pnbs_sort_cod,
            pnbs_nombre = :pnbs_nombre                        where pnbs_cod = :pnbs_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pnbs_cod"       => $this->pnbs_cod,
                ":pnbs_perso_cod" => $this->pnbs_perso_cod,
                ":pnbs_sort_cod"  => $this->pnbs_sort_cod,
                ":pnbs_nombre"    => $this->pnbs_nombre,
            ), $stmt);
        }
    }

    function getByPersoSort($perso, $sort)
    {
        $pdo  = new bddpdo;
        $req  = "select pnbs_cod from perso_nb_sorts where pnbs_perso_cod = :pnbs_perso_cod and pnbs_sort_cod = :pnbs_sort_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":pnbs_perso_cod" => $perso,
            ":pnbs_sort_cod"  => $sort
        ), $stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['pnbs_cod']);
   }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_nb_sorts
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pnbs_cod  from perso_nb_sorts order by pnbs_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_nb_sorts;
            $temp->charge($result["pnbs_cod"]);
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
                    $req    = "select pnbs_cod  from perso_nb_sorts where " . substr($name, 6) . " = ? order by pnbs_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_nb_sorts;
                        $temp->charge($result["pnbs_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_nb_sorts');
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