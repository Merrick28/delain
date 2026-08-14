<?php
/**
 * includes/class.objets_usage_bm.php
 */

/**
 * Class objets_usage_bm
 *
 * Gère les objets BDD de la table objets_usage_bm
 */
class objets_usage_bm
{
    var $objusebm_cod;
    var $objusebm_parent_cod;
    var $objusebm_gobj_cod;
    var $objusebm_obj_cod;
    var $objusebm_tbonus_cod;
    var $objusebm_bonus_valeur;
    var $objusebm_bonus_nb_tours;
    var $objusebm_cout = 4;
    var $objusebm_malchance;
    var $objusebm_nb_utilisation_max;
    var $objusebm_nb_utilisation = 0;
    var $objusebm_bonus_distance = 0;
    var $objusebm_bonus_aggressif = 'N';
    var $objusebm_bonus_soutien = 'N';
    var $objusebm_bonus_soi_meme = 'O';
    var $objusebm_bonus_monstre = 'O';
    var $objusebm_bonus_joueur = 'O';
    var $objusebm_bonus_case = 'N';
    var $objusebm_bonus_mode = 'S';
    var $objusebm_bonus_familier = 'N';
    var $objusebm_vide_detruit = 'N';

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de objets_usage_bm
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from objets_usage_bm where objusebm_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->objusebm_cod = $result['objusebm_cod'];
        $this->objusebm_parent_cod = $result['objusebm_parent_cod'];
        $this->objusebm_gobj_cod = $result['objusebm_gobj_cod'];
        $this->objusebm_obj_cod = $result['objusebm_obj_cod'];
        $this->objusebm_tbonus_cod = $result['objusebm_tbonus_cod'];
        $this->objusebm_bonus_valeur = $result['objusebm_bonus_valeur'];
        $this->objusebm_bonus_nb_tours = $result['objusebm_bonus_nb_tours'];
        $this->objusebm_cout = $result['objusebm_cout'];
        $this->objusebm_malchance = $result['objusebm_malchance'];
        $this->objusebm_nb_utilisation_max = $result['objusebm_nb_utilisation_max'];
        $this->objusebm_nb_utilisation = $result['objusebm_nb_utilisation'];
        $this->objusebm_bonus_distance = $result['objusebm_bonus_distance'];
        $this->objusebm_bonus_aggressif = $result['objusebm_bonus_aggressif'];
        $this->objusebm_bonus_soutien = $result['objusebm_bonus_soutien'];
        $this->objusebm_bonus_soi_meme = $result['objusebm_bonus_soi_meme'];
        $this->objusebm_bonus_monstre = $result['objusebm_bonus_monstre'];
        $this->objusebm_bonus_joueur = $result['objusebm_bonus_joueur'];
        $this->objusebm_bonus_case = $result['objusebm_bonus_case'];
        $this->objusebm_bonus_mode = $result['objusebm_bonus_mode'];
        $this->objusebm_bonus_familier = $result['objusebm_bonus_familier'];
        $this->objusebm_vide_detruit = $result['objusebm_vide_detruit'];
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
            $req = "insert into objets_usage_bm (
            objusebm_parent_cod,
            objusebm_gobj_cod,
            objusebm_obj_cod,
            objusebm_tbonus_cod,
            objusebm_bonus_valeur,
            objusebm_bonus_nb_tours,
            objusebm_cout,
            objusebm_malchance,
            objusebm_nb_utilisation_max,
            objusebm_nb_utilisation,
            objusebm_bonus_distance,
            objusebm_bonus_aggressif,
            objusebm_bonus_soutien,
            objusebm_bonus_soi_meme,
            objusebm_bonus_monstre,
            objusebm_bonus_joueur,
            objusebm_bonus_case,
            objusebm_bonus_mode,
            objusebm_bonus_familier,
            objusebm_vide_detruit                        )
                    values
                    (
                        :objusebm_parent_cod,
                        :objusebm_gobj_cod,
                        :objusebm_obj_cod,
                        :objusebm_tbonus_cod,
                        :objusebm_bonus_valeur,
                        :objusebm_bonus_nb_tours,
                        :objusebm_cout,
                        :objusebm_malchance,
                        :objusebm_nb_utilisation_max,
                        :objusebm_nb_utilisation,
                        :objusebm_bonus_distance,
                        :objusebm_bonus_aggressif,
                        :objusebm_bonus_soutien,
                        :objusebm_bonus_soi_meme,
                        :objusebm_bonus_monstre,
                        :objusebm_bonus_joueur,
                        :objusebm_bonus_case,
                        :objusebm_bonus_mode,
                        :objusebm_bonus_familier,
                        :objusebm_vide_detruit                        )
    returning objusebm_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objusebm_parent_cod" => $this->objusebm_parent_cod,
                ":objusebm_gobj_cod" => $this->objusebm_gobj_cod,
                ":objusebm_obj_cod" => $this->objusebm_obj_cod,
                ":objusebm_tbonus_cod" => $this->objusebm_tbonus_cod,
                ":objusebm_bonus_valeur" => $this->objusebm_bonus_valeur,
                ":objusebm_bonus_nb_tours" => $this->objusebm_bonus_nb_tours,
                ":objusebm_cout" => $this->objusebm_cout,
                ":objusebm_malchance" => $this->objusebm_malchance,
                ":objusebm_nb_utilisation_max" => $this->objusebm_nb_utilisation_max,
                ":objusebm_nb_utilisation" => $this->objusebm_nb_utilisation,
                ":objusebm_bonus_distance" => $this->objusebm_bonus_distance,
                ":objusebm_bonus_aggressif" => $this->objusebm_bonus_aggressif,
                ":objusebm_bonus_soutien" => $this->objusebm_bonus_soutien,
                ":objusebm_bonus_soi_meme" => $this->objusebm_bonus_soi_meme,
                ":objusebm_bonus_monstre" => $this->objusebm_bonus_monstre,
                ":objusebm_bonus_joueur" => $this->objusebm_bonus_joueur,
                ":objusebm_bonus_case" => $this->objusebm_bonus_case,
                ":objusebm_bonus_mode" => $this->objusebm_bonus_mode,
                ":objusebm_bonus_familier" => $this->objusebm_bonus_familier,
                ":objusebm_vide_detruit" => $this->objusebm_vide_detruit,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update objets_usage_bm
                    set
            objusebm_parent_cod = :objusebm_parent_cod,
            objusebm_gobj_cod = :objusebm_gobj_cod,
            objusebm_obj_cod = :objusebm_obj_cod,
            objusebm_tbonus_cod = :objusebm_tbonus_cod,
            objusebm_bonus_valeur = :objusebm_bonus_valeur,
            objusebm_bonus_nb_tours = :objusebm_bonus_nb_tours,
            objusebm_cout = :objusebm_cout,
            objusebm_malchance = :objusebm_malchance,
            objusebm_nb_utilisation_max = :objusebm_nb_utilisation_max,
            objusebm_nb_utilisation = :objusebm_nb_utilisation,
            objusebm_bonus_distance = :objusebm_bonus_distance,
            objusebm_bonus_aggressif = :objusebm_bonus_aggressif,
            objusebm_bonus_soutien = :objusebm_bonus_soutien,
            objusebm_bonus_soi_meme = :objusebm_bonus_soi_meme,
            objusebm_bonus_monstre = :objusebm_bonus_monstre,
            objusebm_bonus_joueur = :objusebm_bonus_joueur,
            objusebm_bonus_case = :objusebm_bonus_case,
            objusebm_bonus_mode = :objusebm_bonus_mode,
            objusebm_bonus_familier = :objusebm_bonus_familier,
            objusebm_vide_detruit = :objusebm_vide_detruit                        where objusebm_cod = :objusebm_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objusebm_cod" => $this->objusebm_cod,
                ":objusebm_parent_cod" => $this->objusebm_parent_cod,
                ":objusebm_gobj_cod" => $this->objusebm_gobj_cod,
                ":objusebm_obj_cod" => $this->objusebm_obj_cod,
                ":objusebm_tbonus_cod" => $this->objusebm_tbonus_cod,
                ":objusebm_bonus_valeur" => $this->objusebm_bonus_valeur,
                ":objusebm_bonus_nb_tours" => $this->objusebm_bonus_nb_tours,
                ":objusebm_cout" => $this->objusebm_cout,
                ":objusebm_malchance" => $this->objusebm_malchance,
                ":objusebm_nb_utilisation_max" => $this->objusebm_nb_utilisation_max,
                ":objusebm_nb_utilisation" => $this->objusebm_nb_utilisation,
                ":objusebm_bonus_distance" => $this->objusebm_bonus_distance,
                ":objusebm_bonus_aggressif" => $this->objusebm_bonus_aggressif,
                ":objusebm_bonus_soutien" => $this->objusebm_bonus_soutien,
                ":objusebm_bonus_soi_meme" => $this->objusebm_bonus_soi_meme,
                ":objusebm_bonus_monstre" => $this->objusebm_bonus_monstre,
                ":objusebm_bonus_joueur" => $this->objusebm_bonus_joueur,
                ":objusebm_bonus_case" => $this->objusebm_bonus_case,
                ":objusebm_bonus_mode" => $this->objusebm_bonus_mode,
                ":objusebm_bonus_familier" => $this->objusebm_bonus_familier,
                ":objusebm_vide_detruit" => $this->objusebm_vide_detruit,
            ),$stmt);
        }
    }

    /***
     * Retourne la liste des utilisations d'un objet
     * @return objets|array
     *  $objet peut être une instance de la classe objets ou un tableau d'instances de la classe objets
     *      ou un tableau a 2 entrées avec les clés "obj_cod" et "obj_gobj_cod"
     */
    function get_objets_usage_bm( $objet)
    {
        $retour = array();
        $pdo = new bddpdo;
        // Les utilisations, sont tous les générique de l'objet plus eventuellement des spécifiques
        $req = "select objusebm_cod, 1 as ordre  from objets_usage_bm where objusebm_obj_cod=:obj_cod 
                    union 
                select objusebm_cod, 2 as ordre from objets_usage_bm where objusebm_gobj_cod=:gobj_cod and objusebm_cod not in (select objusebm_parent_cod from objets_usage_bm where objusebm_obj_cod=:obj_cod) 
                order by ordre, objusebm_cod ";


        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":gobj_cod" => is_array($objet) ? $objet['gobj_cod'] : $objet->obj_gobj_cod,
                                    ":obj_cod" => is_array($objet) ? $objet['obj_cod'] : $objet->obj_cod
                                ),$stmt);

        while($result = $stmt->fetch())
        {
            $temp = new objets_usage_bm();
            $temp->charge($result["objusebm_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /**
     * @param $code
     * @return bool
     * @throws Exception
     */
    function delete($code)
    {
        $pdo    = new bddpdo;
        $req    = "DELETE from objets_usage_bm where objusebm_cod = ?";
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
     * @return \objets_usage_bm
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select objusebm_cod  from objets_usage_bm order by objusebm_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new objets_usage_bm;
            $temp->charge($result["objusebm_cod"]);
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
                    $req = "select objusebm_cod  from objets_usage_bm where " . substr($name, 6) . " = ? order by objusebm_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new objets_usage_bm;
                        $temp->charge($result["objusebm_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table objets_usage_bm');
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