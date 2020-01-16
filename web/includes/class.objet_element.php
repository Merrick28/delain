<?php
/**
 * includes/class.objet_element.php
 */

/**
 * Class objet_element
 *
 * Gère les objets BDD de la table objet_element
 */
class objet_element
{
    var $objelem_cod;
    var $objelem_gobj_cod;
    var $objelem_obj_cod;
    var $objelem_param_id;
    var $objelem_type;
    var $objelem_misc_cod;
    var $objelem_param_num_1;
    var $objelem_param_num_2;
    var $objelem_param_num_3;
    var $objelem_param_txt_1;
    var $objelem_param_txt_2;
    var $objelem_param_txt_3;
    var $objelem_param_ordre;
    var $objelem_nom;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de objet_element
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from objet_element where objelem_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->objelem_cod = $result['objelem_cod'];
        $this->objelem_gobj_cod = $result['objelem_gobj_cod'];
        $this->objelem_obj_cod = $result['objelem_obj_cod'];
        $this->objelem_param_id = $result['objelem_param_id'];
        $this->objelem_type = $result['objelem_type'];
        $this->objelem_misc_cod = $result['objelem_misc_cod'];
        $this->objelem_param_num_1 = $result['objelem_param_num_1'];
        $this->objelem_param_num_2 = $result['objelem_param_num_2'];
        $this->objelem_param_num_3 = $result['objelem_param_num_3'];
        $this->objelem_param_txt_1 = $result['objelem_param_txt_1'];
        $this->objelem_param_txt_2 = $result['objelem_param_txt_2'];
        $this->objelem_param_txt_3 = $result['objelem_param_txt_3'];
        $this->objelem_param_ordre = $result['objelem_param_ordre'];
        $this->objelem_nom = $result['objelem_nom'];
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
            $req = "insert into objet_element (
            objelem_gobj_cod,
            objelem_obj_cod,
            objelem_param_id,
            objelem_type,
            objelem_misc_cod,
            objelem_param_num_1,
            objelem_param_num_2,
            objelem_param_num_3,
            objelem_param_txt_1,
            objelem_param_txt_2,
            objelem_param_txt_3,
            objelem_param_ordre,
            objelem_nom                        )
                    values
                    (
                        :objelem_gobj_cod,
                        :objelem_obj_cod,
                        :objelem_param_id,
                        :objelem_type,
                        :objelem_misc_cod,
                        :objelem_param_num_1,
                        :objelem_param_num_2,
                        :objelem_param_num_3,
                        :objelem_param_txt_1,
                        :objelem_param_txt_2,
                        :objelem_param_txt_3,
                        :objelem_param_ordre,
                        :objelem_nom                        )
    returning objelem_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objelem_gobj_cod" => $this->objelem_gobj_cod,
                ":objelem_obj_cod" => $this->objelem_obj_cod,
                ":objelem_param_id" => $this->objelem_param_id,
                ":objelem_type" => $this->objelem_type,
                ":objelem_misc_cod" => $this->objelem_misc_cod,
                ":objelem_param_num_1" => $this->objelem_param_num_1,
                ":objelem_param_num_2" => $this->objelem_param_num_2,
                ":objelem_param_num_3" => $this->objelem_param_num_3,
                ":objelem_param_txt_1" => $this->objelem_param_txt_1,
                ":objelem_param_txt_2" => $this->objelem_param_txt_2,
                ":objelem_param_txt_3" => $this->objelem_param_txt_3,
                ":objelem_param_ordre" => $this->objelem_param_ordre,
                ":objelem_nom" => $this->objelem_nom,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update objet_element
                    set
            objelem_gobj_cod = :objelem_gobj_cod,
            objelem_obj_cod = :objelem_obj_cod,
            objelem_param_id = :objelem_param_id,
            objelem_type = :objelem_type,
            objelem_misc_cod = :objelem_misc_cod,
            objelem_param_num_1 = :objelem_param_num_1,
            objelem_param_num_2 = :objelem_param_num_2,
            objelem_param_num_3 = :objelem_param_num_3,
            objelem_param_txt_1 = :objelem_param_txt_1,
            objelem_param_txt_2 = :objelem_param_txt_2,
            objelem_param_txt_3 = :objelem_param_txt_3,
            objelem_param_ordre = :objelem_param_ordre,
            objelem_nom = :objelem_nom                        where objelem_cod = :objelem_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":objelem_cod" => $this->objelem_cod,
                ":objelem_gobj_cod" => $this->objelem_gobj_cod,
                ":objelem_obj_cod" => $this->objelem_obj_cod,
                ":objelem_param_id" => $this->objelem_param_id,
                ":objelem_type" => $this->objelem_type,
                ":objelem_misc_cod" => $this->objelem_misc_cod,
                ":objelem_param_num_1" => $this->objelem_param_num_1,
                ":objelem_param_num_2" => $this->objelem_param_num_2,
                ":objelem_param_num_3" => $this->objelem_param_num_3,
                ":objelem_param_txt_1" => $this->objelem_param_txt_1,
                ":objelem_param_txt_2" => $this->objelem_param_txt_2,
                ":objelem_param_txt_3" => $this->objelem_param_txt_3,
                ":objelem_param_ordre" => $this->objelem_param_ordre,
                ":objelem_nom" => $this->objelem_nom,
            ),$stmt);
        }
    }

    /**
     * supprime tous les éléments d'un objet generique qui ne sont pas dans la liste des elements
     * @global bdd_mysql $pdo
     * @return boolean => false pas réussi a supprimer
     */
    function clean($objelem_gobj_cod, $element_list)
    {
        $where = "";
        if (sizeof($element_list)>0)
        {
            foreach ($element_list as $k => $e) $where .= (1*$e)."," ;
            $where = " and objelem_cod not in (". substr($where, 0, -1) .") ";
        }

        $pdo    = new bddpdo;
        $retour = array();
        $req    = "SELECT * from objet_element where objelem_gobj_cod = ?  $where ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($objelem_gobj_cod), $stmt);
        while($result = $stmt->fetch())
        {
            $temp = new objet_element;
            $temp->charge($result["objelem_cod"]);
            $retour[] = $temp;
            unset($temp);
        }

        $req    = "DELETE from objet_element where objelem_gobj_cod = ? $where ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($objelem_gobj_cod), $stmt);

        return (count($retour) == 0) ?  false : $retour ;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \objet_element
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select objelem_cod  from objet_element order by objelem_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new objet_element;
            $temp->charge($result["objelem_cod"]);
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
                    $req = "select objelem_cod  from objet_element where " . substr($name, 6) . " = ? order by objelem_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new objet_element;
                        $temp->charge($result["objelem_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table objet_element');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}