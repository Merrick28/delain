<?php
/**
 * includes/class.objets_sorts_bm.php
 */

/**
 * Class objets_sorts_bm
 *
 * Gère les objets BDD de la table objets_sorts_bm
 */
class objets_sorts_bm
{
    var $objsortbm_cod;
    var $objsortbm_parent_cod;
    var $objsortbm_gobj_cod;
    var $objsortbm_obj_cod;
    var $objsortbm_tbonus_cod;
    var $objsortbm_bonus_valeur;
    var $objsortbm_bonus_nb_tours;
    var $objsortbm_nom;
    var $objsortbm_cout;
    var $objsortbm_malchance;
    var $objsortbm_nb_utilisation_max;
    var $objsortbm_nb_utilisation = 0;
    var $objsortbm_equip_requis = false;
    var $objsortbm_bonus_distance = 0;
    var $objsortbm_bonus_aggressif = 'N';
    var $objsortbm_bonus_soutien = 'N';
    var $objsortbm_bonus_soi_meme = 'O';
    var $objsortbm_bonus_monstre = 'O';
    var $objsortbm_bonus_joueur = 'O';
    var $objsortbm_bonus_case = 'N';
    var $objsortbm_bonus_mode = 'S';
    var $bonus_type;                // Le Type de bonus de rattachement

    function __construct()
    {
        $this->bonus_type = new bonus_type();                // Le Type de bonus de rattachement est un objet (vide tant qu'on en à pas réellement besoin
    }

    /**
     * Charge dans la classe un enregistrement de objets_sorts_bm
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from objets_sorts_bm where objsortbm_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->objsortbm_cod = $result['objsortbm_cod'];
        $this->objsortbm_parent_cod = $result['objsortbm_parent_cod'];
        $this->objsortbm_gobj_cod = $result['objsortbm_gobj_cod'];
        $this->objsortbm_obj_cod = $result['objsortbm_obj_cod'];
        $this->objsortbm_tbonus_cod = $result['objsortbm_tbonus_cod'];
        $this->objsortbm_bonus_valeur = $result['objsortbm_bonus_valeur'];
        $this->objsortbm_bonus_nb_tours = $result['objsortbm_bonus_nb_tours'];
        $this->objsortbm_nom = $result['objsortbm_nom'];
        $this->objsortbm_cout = $result['objsortbm_cout'];
        $this->objsortbm_malchance = $result['objsortbm_malchance'];
        $this->objsortbm_nb_utilisation_max = $result['objsortbm_nb_utilisation_max'];
        $this->objsortbm_nb_utilisation = $result['objsortbm_nb_utilisation'];
        $this->objsortbm_equip_requis = $result['objsortbm_equip_requis'];
        $this->objsortbm_bonus_distance = $result['objsortbm_bonus_distance'];
        $this->objsortbm_bonus_aggressif = $result['objsortbm_bonus_aggressif'];
        $this->objsortbm_bonus_soutien = $result['objsortbm_bonus_soutien'];
        $this->objsortbm_bonus_soi_meme = $result['objsortbm_bonus_soi_meme'];
        $this->objsortbm_bonus_monstre = $result['objsortbm_bonus_monstre'];
        $this->objsortbm_bonus_joueur = $result['objsortbm_bonus_joueur'];
        $this->objsortbm_bonus_case = $result['objsortbm_bonus_case'];
        $this->objsortbm_bonus_mode = $result['objsortbm_bonus_mode'];
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
            $req = "insert into objets_sorts_bm (
            objsortbm_parent_cod,
            objsortbm_gobj_cod,
            objsortbm_obj_cod,
            objsortbm_tbonus_cod,
            objsortbm_bonus_valeur,
            objsortbm_bonus_nb_tours,
            objsortbm_nom,
            objsortbm_cout,
            objsortbm_malchance,
            objsortbm_nb_utilisation_max,
            objsortbm_nb_utilisation,
            objsortbm_equip_requis,
            objsortbm_bonus_distance,
            objsortbm_bonus_aggressif,
            objsortbm_bonus_soutien,
            objsortbm_bonus_soi_meme,
            objsortbm_bonus_monstre,
            objsortbm_bonus_joueur,
            objsortbm_bonus_case ,
            objsortbm_bonus_mode                        )
                    values
                    (
                        :objsortbm_parent_cod,
                        :objsortbm_gobj_cod,
                        :objsortbm_obj_cod,
                        :objsortbm_tbonus_cod,
                        :objsortbm_bonus_valeur,
                        :objsortbm_bonus_nb_tours,
                        :objsortbm_nom,
                        :objsortbm_cout,
                        :objsortbm_malchance,
                        :objsortbm_nb_utilisation_max,
                        :objsortbm_nb_utilisation,
                        :objsortbm_equip_requis,
                        :objsortbm_bonus_distance,
                        :objsortbm_bonus_aggressif,
                        :objsortbm_bonus_soutien,
                        :objsortbm_bonus_soi_meme,
                        :objsortbm_bonus_monstre,
                        :objsortbm_bonus_joueur,
                        :objsortbm_bonus_case,
                        :objsortbm_bonus_mode                        )
    returning objsortbm_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objsortbm_parent_cod" => $this->objsortbm_parent_cod,
                ":objsortbm_gobj_cod" => $this->objsortbm_gobj_cod,
                ":objsortbm_obj_cod" => $this->objsortbm_obj_cod,
                ":objsortbm_tbonus_cod" => $this->objsortbm_tbonus_cod,
                ":objsortbm_bonus_valeur" => $this->objsortbm_bonus_valeur,
                ":objsortbm_bonus_nb_tours" => $this->objsortbm_bonus_nb_tours,
                ":objsortbm_nom" => $this->objsortbm_nom,
                ":objsortbm_cout" => $this->objsortbm_cout,
                ":objsortbm_malchance" => $this->objsortbm_malchance,
                ":objsortbm_nb_utilisation_max" => $this->objsortbm_nb_utilisation_max,
                ":objsortbm_nb_utilisation" => $this->objsortbm_nb_utilisation,
                ":objsortbm_equip_requis" => $this->objsortbm_equip_requis,
                ":objsortbm_bonus_distance" => $this->objsortbm_bonus_distance,
                ":objsortbm_bonus_aggressif" => $this->objsortbm_bonus_aggressif,
                ":objsortbm_bonus_soutien" => $this->objsortbm_bonus_soutien,
                ":objsortbm_bonus_soi_meme" => $this->objsortbm_bonus_soi_meme,
                ":objsortbm_bonus_monstre" => $this->objsortbm_bonus_monstre,
                ":objsortbm_bonus_joueur" => $this->objsortbm_bonus_joueur,
                ":objsortbm_bonus_case" => $this->objsortbm_bonus_case,
                ":objsortbm_bonus_mode" => $this->objsortbm_bonus_mode,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update objets_sorts_bm
                    set
            objsortbm_parent_cod = :objsortbm_parent_cod,
            objsortbm_gobj_cod = :objsortbm_gobj_cod,
            objsortbm_obj_cod = :objsortbm_obj_cod,
            objsortbm_tbonus_cod = :objsortbm_tbonus_cod,
            objsortbm_bonus_valeur = :objsortbm_bonus_valeur,
            objsortbm_bonus_nb_tours = :objsortbm_bonus_nb_tours,
            objsortbm_nom = :objsortbm_nom,
            objsortbm_cout = :objsortbm_cout,
            objsortbm_malchance = :objsortbm_malchance,
            objsortbm_nb_utilisation_max = :objsortbm_nb_utilisation_max,
            objsortbm_nb_utilisation = :objsortbm_nb_utilisation,
            objsortbm_equip_requis = :objsortbm_equip_requis,
            objsortbm_bonus_distance = :objsortbm_bonus_distance,
            objsortbm_bonus_aggressif = :objsortbm_bonus_aggressif,
            objsortbm_bonus_soutien = :objsortbm_bonus_soutien,
            objsortbm_bonus_soi_meme = :objsortbm_bonus_soi_meme,
            objsortbm_bonus_monstre = :objsortbm_bonus_monstre,
            objsortbm_bonus_joueur = :objsortbm_bonus_joueur,
            objsortbm_bonus_case = :objsortbm_bonus_case,
            objsortbm_bonus_mode = :objsortbm_bonus_mode                        where objsortbm_cod = :objsortbm_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objsortbm_cod" => $this->objsortbm_cod,
                ":objsortbm_parent_cod" => $this->objsortbm_parent_cod,
                ":objsortbm_gobj_cod" => $this->objsortbm_gobj_cod,
                ":objsortbm_obj_cod" => $this->objsortbm_obj_cod,
                ":objsortbm_tbonus_cod" => $this->objsortbm_tbonus_cod,
                ":objsortbm_bonus_valeur" => $this->objsortbm_bonus_valeur,
                ":objsortbm_bonus_nb_tours" => $this->objsortbm_bonus_nb_tours,
                ":objsortbm_nom" => $this->objsortbm_nom,
                ":objsortbm_cout" => $this->objsortbm_cout,
                ":objsortbm_malchance" => $this->objsortbm_malchance,
                ":objsortbm_nb_utilisation_max" => $this->objsortbm_nb_utilisation_max,
                ":objsortbm_nb_utilisation" => $this->objsortbm_nb_utilisation,
                ":objsortbm_equip_requis" => $this->objsortbm_equip_requis,
                ":objsortbm_bonus_distance" => $this->objsortbm_bonus_distance,
                ":objsortbm_bonus_aggressif" => $this->objsortbm_bonus_aggressif,
                ":objsortbm_bonus_soutien" => $this->objsortbm_bonus_soutien,
                ":objsortbm_bonus_soi_meme" => $this->objsortbm_bonus_soi_meme,
                ":objsortbm_bonus_monstre" => $this->objsortbm_bonus_monstre,
                ":objsortbm_bonus_joueur" => $this->objsortbm_bonus_joueur,
                ":objsortbm_bonus_case" => $this->objsortbm_bonus_case,
                ":objsortbm_bonus_mode" => $this->objsortbm_bonus_mode,
            ),$stmt);
        }
    }


    /***
     * Retourne la liste des sorts BM qu'un perso peut lancer via les ojets qu'il possède
     * @return array|bool
     */
    function get_perso_objets_sorts_bm($perso_cod)
    {
        $retour = array();
        $pdo = new bddpdo;

        // On commence par "ensorceler" les objets sur la base de leur générique (si cela n'avait pas été déjà fait)
        $req = "insert into objets_sorts_bm(objsortbm_gobj_cod, objsortbm_obj_cod, objsortbm_tbonus_cod, objsortbm_nom, objsortbm_bonus_valeur, objsortbm_bonus_nb_tours, objsortbm_cout, objsortbm_malchance, objsortbm_nb_utilisation_max, objsortbm_nb_utilisation, objsortbm_equip_requis, objsortbm_parent_cod)     
                select null as objsortbm_gobj_cod, obj_cod objsortbm_obj_cod, og.objsortbm_tbonus_cod, og.objsortbm_nom, og.objsortbm_bonus_valeur, og.objsortbm_bonus_nb_tours, og.objsortbm_cout, og.objsortbm_malchance, og.objsortbm_nb_utilisation_max, 0, og.objsortbm_equip_requis, og.objsortbm_cod as objsortbm_parent_cod
                from perso_objets
                join objets on obj_cod=perobj_obj_cod
                join objets_sorts_bm as og on og.objsortbm_gobj_cod=obj_gobj_cod
                left join objets_sorts_bm as oo on oo.objsortbm_obj_cod=obj_cod and oo.objsortbm_parent_cod=og.objsortbm_cod
                where oo.objsortbm_cod is null and  perobj_perso_cod=? and perobj_identifie = 'O' and (perobj_equipe='O' or og.objsortbm_equip_requis=false)
                ";
        $stmt = $pdo->prepare($req);
        $pdo->execute(array($perso_cod),$stmt);

        // On met a jour les objets enscorcelés si leur générique a été modifiés depuis l'ensorcellement(sauf nb utilisation)
        $req = "update objets_sorts_bm oo set 
                objsortbm_nom=og.objsortbm_nom, 
                objsortbm_cout=og.objsortbm_cout,
                objsortbm_malchance=og.objsortbm_malchance,
                objsortbm_nb_utilisation_max=og.objsortbm_nb_utilisation_max,
                objsortbm_equip_requis=og.objsortbm_equip_requis
                from objets_sorts_bm og, perso_objets, objets
                where oo.objsortbm_obj_cod=obj_cod and oo.objsortbm_parent_cod=og.objsortbm_cod and obj_cod=perobj_obj_cod and og.objsortbm_gobj_cod=obj_gobj_cod 
                and  perobj_perso_cod=? and perobj_identifie = 'O' 
                ";
        $stmt = $pdo->prepare($req);
        $pdo->execute(array($perso_cod),$stmt);

        // On ne prend les sorts que sur les objets rééls, pas de générique
        $req = "select objsortbm_cod from perso_objets
                join objets_sorts_bm on objsortbm_obj_cod=perobj_obj_cod
                join bonus_type on tbonus_cod=objsortbm_tbonus_cod
                where perobj_perso_cod=? and perobj_identifie = 'O' and (perobj_equipe='O' or objsortbm_equip_requis=false) and (objsortbm_nb_utilisation_max>objsortbm_nb_utilisation or COALESCE(objsortbm_nb_utilisation_max,0) = 0)
                order by objsortbm_cout, coalesce(objsortbm_nom, tonbus_libelle), objsortbm_obj_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new objets_sorts_bm();
            $temp->charge($result["objsortbm_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }


    /***
     * Retourne la liste des sorts d'un ojet
     * @return array|bool
     */
    function get_objets_sorts_bm(objets $objet)
    {
        $retour = array();
        $pdo = new bddpdo;
        // Les sorts, sont tous les générique de l'objet plus eventuellement des spécifiques
        //$req = "select objsort_cod from objets_sorts where objsort_gobj_cod=:gobj_cod or (objsort_obj_cod=:obj_cod and objsort_parent_cod is null) order by objsort_cod";
        $req = "select objsortbm_cod from objets_sorts_bm where objsortbm_obj_cod=:obj_cod 
                    union 
                select objsortbm_cod from objets_sorts_bm where objsortbm_gobj_cod=:gobj_cod and objsortbm_cod not in (select objsortbm_parent_cod from objets_sorts_bm where objsortbm_obj_cod=:obj_cod) 
                order by objsortbm_cod ";


        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":gobj_cod" => $objet->obj_gobj_cod,
            ":obj_cod" => $objet->obj_cod
        ),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new objets_sorts_bm();
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
        if ($this->objsortbm_nom!="") return $this->objsortbm_nom ;

        // Sinon le nom est celui du sorts rattaché
        if (!$this->bonus_type->tbonus_cod)
        {
            $this->bonus_type->charge($this->objsortbm_tbonus_cod);
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
        $req    = "DELETE from objets_sorts_bm where objsortbm_cod = ?";
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
     * @return \objets_sorts_bm
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select objsortbm_cod  from objets_sorts_bm order by objsortbm_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new objets_sorts_bm;
            $temp->charge($result["objsortbm_cod"]);
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
                    $req = "select objsortbm_cod  from objets_sorts_bm where " . substr($name, 6) . " = ? order by objsortbm_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new objets_sorts_bm;
                        $temp->charge($result["objsortbm_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table objets_sorts_bm');
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