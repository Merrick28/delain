<?php
/**
 * includes/class.objets_sorts.php
 */

/**
 * Class objets_sorts
 *
 * Gère les objets BDD de la table objets_sorts
 */
class objets_sorts
{
    var $objsort_cod;
    var $objsort_gobj_cod;
    var $objsort_obj_cod;
    var $objsort_sort_cod;
    var $objsort_nom;
    var $objsort_cout;
    var $objsort_malchance;
    var $objsort_nb_utilisation;
    var $objsort_equip_requis = false;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de objets_sorts
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from objets_sorts where objsort_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->objsort_cod = $result['objsort_cod'];
        $this->objsort_gobj_cod = $result['objsort_gobj_cod'];
        $this->objsort_obj_cod = $result['objsort_obj_cod'];
        $this->objsort_sort_cod = $result['objsort_sort_cod'];
        $this->objsort_nom = $result['objsort_nom'];
        $this->objsort_cout = $result['objsort_cout'];
        $this->objsort_malchance = $result['objsort_malchance'];
        $this->objsort_nb_utilisation = $result['objsort_nb_utilisation'];
        $this->objsort_equip_requis = $result['objsort_equip_requis'];
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
        if($new)
        {
            $req = "insert into objets_sorts (
            objsort_gobj_cod,
            objsort_obj_cod,
            objsort_sort_cod,
            objsort_nom,
            objsort_cout,
            objsort_malchance,
            objsort_nb_utilisation,
            objsort_equip_requis                        )
                    values
                    (
                        :objsort_gobj_cod,
                        :objsort_obj_cod,
                        :objsort_sort_cod,
                        :objsort_nom,
                        :objsort_cout,
                        :objsort_malchance,
                        :objsort_nb_utilisation,
                        :objsort_equip_requis                        )
    returning objsort_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objsort_gobj_cod" => $this->objsort_gobj_cod,
                ":objsort_obj_cod" => $this->objsort_obj_cod,
                ":objsort_sort_cod" => $this->objsort_sort_cod,
                ":objsort_nom" => $this->objsort_nom,
                ":objsort_cout" => $this->objsort_cout,
                ":objsort_malchance" => $this->objsort_malchance,
                ":objsort_nb_utilisation" => $this->objsort_nb_utilisation,
                ":objsort_equip_requis" => $this->objsort_equip_requis,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update objets_sorts
                    set
            objsort_gobj_cod = :objsort_gobj_cod,
            objsort_obj_cod = :objsort_obj_cod,
            objsort_sort_cod = :objsort_sort_cod,
            objsort_nom = :objsort_nom,
            objsort_cout = :objsort_cout,
            objsort_malchance = :objsort_malchance,
            objsort_nb_utilisation = :objsort_nb_utilisation,
            objsort_equip_requis = :objsort_equip_requis                        where objsort_cod = :objsort_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objsort_cod" => $this->objsort_cod,
                ":objsort_gobj_cod" => $this->objsort_gobj_cod,
                ":objsort_obj_cod" => $this->objsort_obj_cod,
                ":objsort_sort_cod" => $this->objsort_sort_cod,
                ":objsort_nom" => $this->objsort_nom,
                ":objsort_cout" => $this->objsort_cout,
                ":objsort_malchance" => $this->objsort_malchance,
                ":objsort_nb_utilisation" => $this->objsort_nb_utilisation,
                ":objsort_equip_requis" => $this->objsort_equip_requis,
            ),$stmt);
        }
    }

    /**
     * @param $code
     * @return bool
     * @throws Exception
     */
    function delete($code)
    {
        $pdo    = new bddpdo;
        $req    = "DELETE from objets_sorts where objsort_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }

        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \objets_sorts
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select objsort_cod  from objets_sorts order by objsort_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new objets_sorts;
            $temp->charge($result["objsort_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function __call($name, $arguments){
        switch(substr($name, 0, 6)){
            case 'getBy_':
                if(property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo = new bddpdo;
                    $req = "select objsort_cod  from objets_sorts where " . substr($name, 6) . " = ? order by objsort_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new objets_sorts;
                        $temp->charge($result["objsort_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if(count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name, 6) . ' in table objets_sorts');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}