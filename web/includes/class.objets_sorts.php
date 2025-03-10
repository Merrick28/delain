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
    var $objsort_parent_cod ;
    var $objsort_gobj_cod;
    var $objsort_obj_cod;
    var $objsort_sort_cod;
    var $objsort_nom;
    var $objsort_cout;
    var $objsort_malchance;
    var $objsort_nb_utilisation_max;
    var $objsort_nb_utilisation = 0;
    var $objsort_equip_requis = false;
    var $sort;                // Le sort de rattachement

    function __construct()
    {
        $this->sort = new sorts();                // Le sort de rattachement est un objet (vide tant qu'on en à pas réellement besoin
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
        $this->objsort_nb_utilisation_max = $result['objsort_nb_utilisation_max'];
        $this->objsort_nb_utilisation = $result['objsort_nb_utilisation'];
        $this->objsort_equip_requis = $result['objsort_equip_requis'];
        $this->objsort_parent_cod = $result['objsort_parent_cod'];
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
            objsort_nb_utilisation_max,
            objsort_nb_utilisation,
            objsort_equip_requis,
            objsort_parent_cod                        )
                    values
                    (
                        :objsort_gobj_cod,
                        :objsort_obj_cod,
                        :objsort_sort_cod,
                        :objsort_nom,
                        :objsort_cout,
                        :objsort_malchance,
                        :objsort_nb_utilisation_max,
                        :objsort_nb_utilisation,
                        :objsort_equip_requis,
                        :objsort_parent_cod                        )
    returning objsort_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objsort_gobj_cod" => $this->objsort_gobj_cod,
                ":objsort_obj_cod" => $this->objsort_obj_cod,
                ":objsort_sort_cod" => $this->objsort_sort_cod,
                ":objsort_nom" => $this->objsort_nom,
                ":objsort_cout" => $this->objsort_cout,
                ":objsort_malchance" => $this->objsort_malchance,
                ":objsort_nb_utilisation_max" => $this->objsort_nb_utilisation_max,
                ":objsort_nb_utilisation" => $this->objsort_nb_utilisation,
                ":objsort_equip_requis" => ($this->objsort_equip_requis && strtolower($this->objsort_equip_requis)!="false") ? "true" : "false" ,
                ":objsort_parent_cod" => $this->objsort_parent_cod,
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
            objsort_nb_utilisation_max = :objsort_nb_utilisation_max,
            objsort_nb_utilisation = :objsort_nb_utilisation,
            objsort_equip_requis = :objsort_equip_requis,
            objsort_parent_cod = :objsort_parent_cod                        where objsort_cod = :objsort_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objsort_cod" => $this->objsort_cod,
                ":objsort_gobj_cod" => $this->objsort_gobj_cod,
                ":objsort_obj_cod" => $this->objsort_obj_cod,
                ":objsort_sort_cod" => $this->objsort_sort_cod,
                ":objsort_nom" => $this->objsort_nom,
                ":objsort_cout" => $this->objsort_cout,
                ":objsort_malchance" => $this->objsort_malchance,
                ":objsort_nb_utilisation_max" => $this->objsort_nb_utilisation_max,
                ":objsort_nb_utilisation" => $this->objsort_nb_utilisation,
                ":objsort_equip_requis" => ($this->objsort_equip_requis && strtolower($this->objsort_equip_requis)!="false") ? "true" : "false" ,
                ":objsort_parent_cod" => $this->objsort_parent_cod,
            ),$stmt);
        }
    }

    /***
     * Retourne la liste des sorts qu'un perso peut lancer via les ojets qu'il possède
     * @return array|bool
     */
    function get_perso_objets_sorts($perso_cod)
    {
        $retour = array();
        $pdo = new bddpdo;

        // On commence par "ensorceler" les objets sur la base de leur générique (si cela n'avait pas été déjà fait)
        $req = "insert into objets_sorts(objsort_gobj_cod, objsort_obj_cod, objsort_sort_cod, objsort_nom, objsort_cout, objsort_malchance, objsort_nb_utilisation_max, objsort_nb_utilisation, objsort_equip_requis, objsort_parent_cod)     
                select null as objsort_gobj_cod, obj_cod objsort_obj_cod, og.objsort_sort_cod, og.objsort_nom, og.objsort_cout, og.objsort_malchance, og.objsort_nb_utilisation_max, 0, og.objsort_equip_requis, og.objsort_cod as objsort_parent_cod
                from perso_objets
                join objets on obj_cod=perobj_obj_cod
                join objets_sorts as og on og.objsort_gobj_cod=obj_gobj_cod
                left join objets_sorts as oo on oo.objsort_obj_cod=obj_cod and oo.objsort_parent_cod=og.objsort_cod
                where oo.objsort_cod is null and  perobj_perso_cod=? and perobj_identifie = 'O' and (perobj_equipe='O' or og.objsort_equip_requis=false)
                ";
        $stmt = $pdo->prepare($req);
        $pdo->execute(array($perso_cod),$stmt);

        // On met a jour les objets enscorcelés si leur générique a été modifiés depuis l'ensorcellement(sauf nb utilisation)
        $req = "update objets_sorts oo set 
                objsort_nom=og.objsort_nom, 
                objsort_cout=og.objsort_cout,
                objsort_malchance=og.objsort_malchance,
                objsort_nb_utilisation_max=og.objsort_nb_utilisation_max,
                objsort_equip_requis=og.objsort_equip_requis
                from objets_sorts og, perso_objets, objets
                where oo.objsort_obj_cod=obj_cod and oo.objsort_parent_cod=og.objsort_cod and obj_cod=perobj_obj_cod and og.objsort_gobj_cod=obj_gobj_cod 
                and  perobj_perso_cod=? and perobj_identifie = 'O' 
                ";
        $stmt = $pdo->prepare($req);
        $pdo->execute(array($perso_cod),$stmt);

        // On ne prend les sorts que sur les objets rééls, pas de générique
        $req = "select objsort_cod from perso_objets
                join objets_sorts on objsort_obj_cod=perobj_obj_cod
                join sorts on sort_cod=objsort_sort_cod
                where perobj_perso_cod=? and perobj_identifie = 'O' and (perobj_equipe='O' or objsort_equip_requis=false) and (objsort_nb_utilisation_max>objsort_nb_utilisation or COALESCE(objsort_nb_utilisation_max,0) = 0)
                order by sort_cout, coalesce(objsort_nom, sort_nom), objsort_obj_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new objets_sorts;
            $temp->charge($result["objsort_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }


    /***
     * Retourne la liste des sorts d'un ojet
     * @return array|bool
     */
    function get_objets_sorts(objets $objet)
    {
        $retour = array();
        $pdo = new bddpdo;
        // Les sorts, sont tous les générique de l'objet plus eventuellement des spécifiques
        //$req = "select objsort_cod from objets_sorts where objsort_gobj_cod=:gobj_cod or (objsort_obj_cod=:obj_cod and objsort_parent_cod is null) order by objsort_cod";
        $req = "select objsort_cod from objets_sorts where objsort_obj_cod=:obj_cod 
                    union 
                select objsort_cod from objets_sorts where objsort_gobj_cod=:gobj_cod and objsort_cod not in (select objsort_parent_cod from objets_sorts where objsort_obj_cod=:obj_cod) 
                order by objsort_cod ";


        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":gobj_cod" => $objet->obj_gobj_cod,
                                    ":obj_cod" => $objet->obj_cod
                                    ),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new objets_sorts;
            $temp->charge($result["objsort_cod"]);
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
        if ($this->objsort_nom!="") return $this->objsort_nom ;

        // Sinon le nom est celui du sorts rattaché
        if (!$this->sort->sort_cod)
        {
            $this->sort->charge($this->objsort_sort_cod);
        }

        return $this->sort->sort_nom;
    }

    /***
     * retourne le cout du sort(en PA)
     * @return mixed
     */
    function getCout()
    {
        if ($this->objsort_cout) return $this->objsort_cout ;

        // Sinon le nom est celui du sorts rattaché
        if (!$this->sort->sort_cod)
        {
            $this->sort->charge($this->objsort_sort_cod);
        }

        return $this->sort->sort_cout;
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
                ob_start();
                debug_print_backtrace();
                $out = ob_get_contents();
                error_log($out);
                die('Unknown method.');
        }
    }
}