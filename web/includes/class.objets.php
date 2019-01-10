<?php
/**
* includes/class.objets.php
 */
 
/**
* Class objets
*
 * Gère les objets BDD de la table objets
 */
class objets
{
        var $obj_cod;
        var $obj_gobj_cod;
        var $obj_etat = 100;
        var $obj_obon_cod;
        var $obj_nom;
        var $obj_nom_generique;
        var $obj_valeur;
        var $obj_des_degats;
        var $obj_val_des_degats;
        var $obj_bonus_degats;
        var $obj_armure;
        var $obj_distance;
        var $obj_chute;
        var $obj_poids;
        var $obj_description;
        var $obj_usure;
        var $obj_poison = 0;
        var $obj_vampire = 0;
        var $obj_degats;
        var $obj_regen = 0;
        var $obj_aura_feu = 0;
        var $obj_critique = 0;
        var $obj_seuil_force;
        var $obj_seuil_dex;
        var $obj_nom_porte;
        var $obj_bonus_vue;
        var $obj_modifie = 0;
        var $obj_sort_cod;
        var $obj_chance_drop;
        var $obj_enchantable = 0;
        var $obj_deposable = 'O';
        var $obj_stabilite;
        var $obj_portee;
        var $obj_etat_max = 100;
        var $obj_famille_rune;
        var $obj_frune_cod;
        var $obj_niveau_min = 0;
        var $obj_desequipable = 'O';
    
    function __construct()
{   
    }
  
    /**
     * Charge dans la classe un enregistrement de objets
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */   
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from objets where obj_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
                return false;
        }
            $this->obj_cod = $result['obj_cod'];
            $this->obj_gobj_cod = $result['obj_gobj_cod'];
            $this->obj_etat = $result['obj_etat'];
            $this->obj_obon_cod = $result['obj_obon_cod'];
            $this->obj_nom = $result['obj_nom'];
            $this->obj_nom_generique = $result['obj_nom_generique'];
            $this->obj_valeur = $result['obj_valeur'];
            $this->obj_des_degats = $result['obj_des_degats'];
            $this->obj_val_des_degats = $result['obj_val_des_degats'];
            $this->obj_bonus_degats = $result['obj_bonus_degats'];
            $this->obj_armure = $result['obj_armure'];
            $this->obj_distance = $result['obj_distance'];
            $this->obj_chute = $result['obj_chute'];
            $this->obj_poids = $result['obj_poids'];
            $this->obj_description = $result['obj_description'];
            $this->obj_usure = $result['obj_usure'];
            $this->obj_poison = $result['obj_poison'];
            $this->obj_vampire = $result['obj_vampire'];
            $this->obj_degats = $result['obj_degats'];
            $this->obj_regen = $result['obj_regen'];
            $this->obj_aura_feu = $result['obj_aura_feu'];
            $this->obj_critique = $result['obj_critique'];
            $this->obj_seuil_force = $result['obj_seuil_force'];
            $this->obj_seuil_dex = $result['obj_seuil_dex'];
            $this->obj_nom_porte = $result['obj_nom_porte'];
            $this->obj_bonus_vue = $result['obj_bonus_vue'];
            $this->obj_modifie = $result['obj_modifie'];
            $this->obj_sort_cod = $result['obj_sort_cod'];
            $this->obj_chance_drop = $result['obj_chance_drop'];
            $this->obj_enchantable = $result['obj_enchantable'];
            $this->obj_deposable = $result['obj_deposable'];
            $this->obj_stabilite = $result['obj_stabilite'];
            $this->obj_portee = $result['obj_portee'];
            $this->obj_etat_max = $result['obj_etat_max'];
            $this->obj_famille_rune = $result['obj_famille_rune'];
            $this->obj_frune_cod = $result['obj_frune_cod'];
            $this->obj_niveau_min = $result['obj_niveau_min'];
            $this->obj_desequipable = $result['obj_desequipable'];
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
                $req = "insert into objets (
            obj_gobj_cod,
            obj_etat,
            obj_obon_cod,
            obj_nom,
            obj_nom_generique,
            obj_valeur,
            obj_des_degats,
            obj_val_des_degats,
            obj_bonus_degats,
            obj_armure,
            obj_distance,
            obj_chute,
            obj_poids,
            obj_description,
            obj_usure,
            obj_poison,
            obj_vampire,
            obj_degats,
            obj_regen,
            obj_aura_feu,
            obj_critique,
            obj_seuil_force,
            obj_seuil_dex,
            obj_nom_porte,
            obj_bonus_vue,
            obj_modifie,
            obj_sort_cod,
            obj_chance_drop,
            obj_enchantable,
            obj_deposable,
            obj_stabilite,
            obj_portee,
            obj_etat_max,
            obj_famille_rune,
            obj_frune_cod,
            obj_niveau_min,
            obj_desequipable                        )
                    values
                    (
                        :obj_gobj_cod,
                        :obj_etat,
                        :obj_obon_cod,
                        :obj_nom,
                        :obj_nom_generique,
                        :obj_valeur,
                        :obj_des_degats,
                        :obj_val_des_degats,
                        :obj_bonus_degats,
                        :obj_armure,
                        :obj_distance,
                        :obj_chute,
                        :obj_poids,
                        :obj_description,
                        :obj_usure,
                        :obj_poison,
                        :obj_vampire,
                        :obj_degats,
                        :obj_regen,
                        :obj_aura_feu,
                        :obj_critique,
                        :obj_seuil_force,
                        :obj_seuil_dex,
                        :obj_nom_porte,
                        :obj_bonus_vue,
                        :obj_modifie,
                        :obj_sort_cod,
                        :obj_chance_drop,
                        :obj_enchantable,
                        :obj_deposable,
                        :obj_stabilite,
                        :obj_portee,
                        :obj_etat_max,
                        :obj_famille_rune,
                        :obj_frune_cod,
                        :obj_niveau_min,
                        :obj_desequipable                        )
    returning obj_cod as id";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(
                        ":obj_gobj_cod" => $this->obj_gobj_cod,
                        ":obj_etat" => $this->obj_etat,
                        ":obj_obon_cod" => $this->obj_obon_cod,
                        ":obj_nom" => $this->obj_nom,
                        ":obj_nom_generique" => $this->obj_nom_generique,
                        ":obj_valeur" => $this->obj_valeur,
                        ":obj_des_degats" => $this->obj_des_degats,
                        ":obj_val_des_degats" => $this->obj_val_des_degats,
                        ":obj_bonus_degats" => $this->obj_bonus_degats,
                        ":obj_armure" => $this->obj_armure,
                        ":obj_distance" => $this->obj_distance,
                        ":obj_chute" => $this->obj_chute,
                        ":obj_poids" => $this->obj_poids,
                        ":obj_description" => $this->obj_description,
                        ":obj_usure" => $this->obj_usure,
                        ":obj_poison" => $this->obj_poison,
                        ":obj_vampire" => $this->obj_vampire,
                        ":obj_degats" => $this->obj_degats,
                        ":obj_regen" => $this->obj_regen,
                        ":obj_aura_feu" => $this->obj_aura_feu,
                        ":obj_critique" => $this->obj_critique,
                        ":obj_seuil_force" => $this->obj_seuil_force,
                        ":obj_seuil_dex" => $this->obj_seuil_dex,
                        ":obj_nom_porte" => $this->obj_nom_porte,
                        ":obj_bonus_vue" => $this->obj_bonus_vue,
                        ":obj_modifie" => $this->obj_modifie,
                        ":obj_sort_cod" => $this->obj_sort_cod,
                        ":obj_chance_drop" => $this->obj_chance_drop,
                        ":obj_enchantable" => $this->obj_enchantable,
                        ":obj_deposable" => $this->obj_deposable,
                        ":obj_stabilite" => $this->obj_stabilite,
                        ":obj_portee" => $this->obj_portee,
                        ":obj_etat_max" => $this->obj_etat_max,
                        ":obj_famille_rune" => $this->obj_famille_rune,
                        ":obj_frune_cod" => $this->obj_frune_cod,
                        ":obj_niveau_min" => $this->obj_niveau_min,
                        ":obj_desequipable" => $this->obj_desequipable,
                        ),$stmt);
    
                
                $temp = $stmt->fetch();
                $this->charge($temp['id']);
        }
        else
        {
                $req = "update objets
                    set
            obj_gobj_cod = :obj_gobj_cod,
            obj_etat = :obj_etat,
            obj_obon_cod = :obj_obon_cod,
            obj_nom = :obj_nom,
            obj_nom_generique = :obj_nom_generique,
            obj_valeur = :obj_valeur,
            obj_des_degats = :obj_des_degats,
            obj_val_des_degats = :obj_val_des_degats,
            obj_bonus_degats = :obj_bonus_degats,
            obj_armure = :obj_armure,
            obj_distance = :obj_distance,
            obj_chute = :obj_chute,
            obj_poids = :obj_poids,
            obj_description = :obj_description,
            obj_usure = :obj_usure,
            obj_poison = :obj_poison,
            obj_vampire = :obj_vampire,
            obj_degats = :obj_degats,
            obj_regen = :obj_regen,
            obj_aura_feu = :obj_aura_feu,
            obj_critique = :obj_critique,
            obj_seuil_force = :obj_seuil_force,
            obj_seuil_dex = :obj_seuil_dex,
            obj_nom_porte = :obj_nom_porte,
            obj_bonus_vue = :obj_bonus_vue,
            obj_modifie = :obj_modifie,
            obj_sort_cod = :obj_sort_cod,
            obj_chance_drop = :obj_chance_drop,
            obj_enchantable = :obj_enchantable,
            obj_deposable = :obj_deposable,
            obj_stabilite = :obj_stabilite,
            obj_portee = :obj_portee,
            obj_etat_max = :obj_etat_max,
            obj_famille_rune = :obj_famille_rune,
            obj_frune_cod = :obj_frune_cod,
            obj_niveau_min = :obj_niveau_min,
            obj_desequipable = :obj_desequipable                        where obj_cod = :obj_cod ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(
                        ":obj_cod" => $this->obj_cod,
                        ":obj_gobj_cod" => $this->obj_gobj_cod,
                        ":obj_etat" => $this->obj_etat,
                        ":obj_obon_cod" => $this->obj_obon_cod,
                        ":obj_nom" => $this->obj_nom,
                        ":obj_nom_generique" => $this->obj_nom_generique,
                        ":obj_valeur" => $this->obj_valeur,
                        ":obj_des_degats" => $this->obj_des_degats,
                        ":obj_val_des_degats" => $this->obj_val_des_degats,
                        ":obj_bonus_degats" => $this->obj_bonus_degats,
                        ":obj_armure" => $this->obj_armure,
                        ":obj_distance" => $this->obj_distance,
                        ":obj_chute" => $this->obj_chute,
                        ":obj_poids" => $this->obj_poids,
                        ":obj_description" => $this->obj_description,
                        ":obj_usure" => $this->obj_usure,
                        ":obj_poison" => $this->obj_poison,
                        ":obj_vampire" => $this->obj_vampire,
                        ":obj_degats" => $this->obj_degats,
                        ":obj_regen" => $this->obj_regen,
                        ":obj_aura_feu" => $this->obj_aura_feu,
                        ":obj_critique" => $this->obj_critique,
                        ":obj_seuil_force" => $this->obj_seuil_force,
                        ":obj_seuil_dex" => $this->obj_seuil_dex,
                        ":obj_nom_porte" => $this->obj_nom_porte,
                        ":obj_bonus_vue" => $this->obj_bonus_vue,
                        ":obj_modifie" => $this->obj_modifie,
                        ":obj_sort_cod" => $this->obj_sort_cod,
                        ":obj_chance_drop" => $this->obj_chance_drop,
                        ":obj_enchantable" => $this->obj_enchantable,
                        ":obj_deposable" => $this->obj_deposable,
                        ":obj_stabilite" => $this->obj_stabilite,
                        ":obj_portee" => $this->obj_portee,
                        ":obj_etat_max" => $this->obj_etat_max,
                        ":obj_famille_rune" => $this->obj_famille_rune,
                        ":obj_frune_cod" => $this->obj_frune_cod,
                        ":obj_niveau_min" => $this->obj_niveau_min,
                        ":obj_desequipable" => $this->obj_desequipable,
                        ),$stmt);
        }
    }

    /**
     * Retourne une chaine de caractère qui est le libelle correspondant au type de l'objet
     * @return string
     */
    function get_type_libelle()
    {
        $pdo = new bddpdo;
        $req = "select tobj_libelle from objet_generique join type_objet on tobj_cod=gobj_tobj_cod where gobj_cod = :obj_gobj_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->obj_gobj_cod),$stmt);
        if (!$result = $stmt->fetch()) return "" ;

        return $result["tobj_libelle"];
    }

    /***
     * Retourne la liste des sorts attachés sur l'objet
     * @return array|bool
     */
    function get_sorts_attaches()
    {
        $retour = array();

        $objsort = new objets_sorts();
        $retour = $objsort->get_objets_sorts($this) ;

        if(count($retour) == 0)
        {
            return false;
        }
        return $retour;
    }

    /**
     * supprime l'enregistrement de objets
     * @global bdd_mysql $pdo
     * @param integer $code => PK (si non fournie alors suppression de l'ojet chargé)
     * @return boolean => false pas réussi a supprimer
     */
    function supprime($code="")
    {
        if ($code=="") $code = $this->obj_cod;
        $code = 1 * $code ; // on en prepare pas la requete, on s'assure quand même que code est un entier !
        if ($code==0) return false;

        $pdo    = new bddpdo;
        $req    = "select f_del_objet(:obj_cod); ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":obj_cod" => $code),$stmt);
        return true;
    }


    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \objets
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select obj_cod  from objets order by obj_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
                $temp = new objets;
                $temp->charge($result["obj_cod"]);
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
                    $req = "select obj_cod  from objets where " . substr($name, 6) . " = ? order by obj_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                            $temp = new objets;
                           $temp->charge($result["obj_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table objets');
                }
            break;
           
            default:
                die('Unknown method.');
        }
    }
}