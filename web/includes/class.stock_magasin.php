<?php
/**
 * includes/class.stock_magasin.php
 */

/**
 * Class stock_magasin
 *
 * Gère les objets BDD de la table stock_magasin
 */
class stock_magasin
{
    var $mstock_lieu_cod;
    var $mstock_obj_cod;
    var $mstock_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de stock_magasin
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from stock_magasin where mstock_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->mstock_lieu_cod = $result['mstock_lieu_cod'];
        $this->mstock_obj_cod  = $result['mstock_obj_cod'];
        $this->mstock_cod      = $result['mstock_cod'];
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
            $req  = "insert into stock_magasin (
mstock_lieu_cod,
mstock_obj_cod                        )
values
(
:mstock_lieu_cod,
:mstock_obj_cod                        )
returning mstock_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":mstock_lieu_cod" => $this->mstock_lieu_cod,
                ":mstock_obj_cod"  => $this->mstock_obj_cod,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update stock_magasin
set
mstock_lieu_cod = :mstock_lieu_cod,
mstock_obj_cod = :mstock_obj_cod                        where mstock_cod = :mstock_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":mstock_lieu_cod" => $this->mstock_lieu_cod,
                ":mstock_obj_cod"  => $this->mstock_obj_cod,
                ":mstock_cod"      => $this->mstock_cod,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \stock_magasin
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select mstock_cod  from stock_magasin order by mstock_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new stock_magasin;
            $temp->charge($result["mstock_cod"]);
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
                    $req    = "select mstock_cod  from stock_magasin where " . substr($name, 6) . " = ? order by mstock_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new stock_magasin;
                        $temp->charge($result["mstock_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table stock_magasin');
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

    /**
     * Retourne la liste des objets ciblés dans un magasin
     * @param $gobj Objet générique
     * @param $bonus Bonus
     * @param $qte Quantité
     * @return objets[]
     * @throws Exception
     */
    function get_objets($lieu,$gobj, $bonus, $qte)
    {
        $retour = array();
        $pdo  = new bddpdo;
        $req  = 'select obj_cod
            from objets,stock_magasin,objet_generique 
            where mstock_lieu_cod = :lieu
            and mstock_obj_cod = obj_cod 
            and obj_gobj_cod = :gobj
            and coalesce(obj_obon_cod,0) = :bonus
            and obj_gobj_cod = gobj_cod 
            limit :qte';
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ':lieu'  => $lieu,
            ':gobj'  => $gobj,
            ':bonus' => $bonus,
            ':qte'   => $qte)
            , $stmt);
        while($result = $stmt->fetch())
        {
            $obj = new objets();
            $obj->charge($result['obj_cod']);
            $retour[] = $obj;
            unset($obj);
        }
        return $retour;

    }
}