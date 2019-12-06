<?php
/**
 * includes/class.objets_bm.php
 */

/**
 * Class objets_bm
 *
 * Gère les objets BDD de la table objets_bm
 */
class objets_bm
{
    var $objbm_cod;
    var $objbm_gobj_cod;
    var $objbm_obj_cod;
    var $objbm_tbonus_cod;
    var $objbm_nom;
    var $objbm_bonus_valeur;
    var $bonus_type;                // Le type de bonus de rattachement

    function __construct()
    {
        $this->bonus_type = new bonus_type();                // Le sort de rattachement est un objet (vide tant qu'on en à pas réellement besoin
    }

    /**
     * Charge dans la classe un enregistrement de objets_bm
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from objets_bm where objbm_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->objbm_cod = $result['objbm_cod'];
        $this->objbm_gobj_cod = $result['objbm_gobj_cod'];
        $this->objbm_obj_cod = $result['objbm_obj_cod'];
        $this->objbm_tbonus_cod = $result['objbm_tbonus_cod'];
        $this->objbm_nom = $result['objbm_nom'];
        $this->objbm_bonus_valeur = $result['objbm_bonus_valeur'];
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
            $req = "insert into objets_bm (
            objbm_gobj_cod,
            objbm_obj_cod,
            objbm_tbonus_cod,
            objbm_nom,
            objbm_bonus_valeur                     )
                    values
                    (
                        :objbm_gobj_cod,
                        :objbm_obj_cod,
                        :objbm_tbonus_cod,
                        :objbm_nom,
                        :objbm_bonus_valeur                    )
    returning objbm_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objbm_gobj_cod" => $this->objbm_gobj_cod,
                ":objbm_obj_cod" => $this->objbm_obj_cod,
                ":objbm_tbonus_cod" => $this->objbm_tbonus_cod,
                ":objbm_nom" => $this->objbm_nom,
                ":objbm_bonus_valeur" => $this->objbm_bonus_valeur,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update objets_bm
                    set
            objbm_gobj_cod = :objbm_gobj_cod,
            objbm_obj_cod = :objbm_obj_cod,
            objbm_tbonus_cod = :objbm_tbonus_cod,
            objbm_nom = :objbm_nom,
            objbm_bonus_valeur = :objbm_bonus_valeur                       where objbm_cod = :objbm_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objbm_cod" => $this->objbm_cod,
                ":objbm_gobj_cod" => $this->objbm_gobj_cod,
                ":objbm_obj_cod" => $this->objbm_obj_cod,
                ":objbm_tbonus_cod" => $this->objbm_tbonus_cod,
                ":objbm_nom" => $this->objbm_nom,
                ":objbm_bonus_valeur" => $this->objbm_bonus_valeur
            ),$stmt);
        }
    }

    /***
     * Retourne la liste des sorts d'un ojet
     * @return array|bool
     */
    function get_objets_bm(objets $objet)
    {
        $retour = array();
        $pdo = new bddpdo;
        // Les sorts, sont tous les générique de l'objet plus eventuellement des spécifiques
        $req = "select objbm_cod from objets_bm where objbm_gobj_cod=:gobj_cod or objbm_obj_cod=:obj_cod order by objbm_cod";

        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":gobj_cod" => $objet->obj_gobj_cod,
            ":obj_cod" => $objet->obj_cod
        ),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new objets_bm;
            $temp->charge($result["objbm_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /***
     * retourne le nom du sort
     * @return mixed
     */
    function getNom()
    {
        if ($this->objbm_nom!="") return $this->objbm_nom ;

        // Sinon le nom est celui du sorts rattaché
        if (!$this->bonus_type->tbonus_cod)
        {
            $this->bonus_type->charge($this->objsort_tbonus_cod);
        }

        return $this->bonus_type->tonbus_libelle;
    }

    /**
     * @param $code
     * @return bool
     * @throws Exception
     */
    function delete($code)
    {
        $pdo    = new bddpdo;
        $req    = "DELETE from objets_bm where objbm_cod = ?";
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
     * @return \objets_bm
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select objbm_cod  from objets_bm order by objbm_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new objets_bm;
            $temp->charge($result["objbm_cod"]);
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
                    $req = "select objbm_cod  from objets_bm where " . substr($name, 6) . " = ? order by objbm_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new objets_bm;
                        $temp->charge($result["objbm_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table objets_bm');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}