<?php
/**
 * includes/class.aquete_element.php
 */

/**
 * Class aquete_element
 *
 * Gère les objets BDD de la table aquete_element
 */
class aquete_element
{
    var $aqelem_cod;
    var $aqelem_aquete_cod;
    var $aqelem_aqetape_cod;
    var $aqelem_param_id;
    var $aqelem_type;
    var $aqelem_misc_cod;
    var $aqelem_param_num_1;
    var $aqelem_param_num_2;
    var $aqelem_param_num_3;
    var $aqelem_param_txt_1;
    var $aqelem_param_txt_2;
    var $aqelem_param_txt_3;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de aquete_element
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_element where aqelem_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqelem_cod = $result['aqelem_cod'];
        $this->aqelem_aquete_cod = $result['aqelem_aquete_cod'];
        $this->aqelem_aqetape_cod = $result['aqelem_aqetape_cod'];
        $this->aqelem_param_id = $result['aqelem_param_id'];
        $this->aqelem_type = $result['aqelem_type'];
        $this->aqelem_misc_cod = $result['aqelem_misc_cod'];
        $this->aqelem_param_txt_1 = $result['aqelem_param_num_1'];
        $this->aqelem_param_txt_2 = $result['aqelem_param_num_2'];
        $this->aqelem_param_txt_3 = $result['aqelem_param_num_3'];
        $this->aqelem_param_txt_1 = $result['aqelem_param_txt_1'];
        $this->aqelem_param_txt_2 = $result['aqelem_param_txt_2'];
        $this->aqelem_param_txt_3 = $result['aqelem_param_txt_3'];
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
            $req = "insert into quetes.aquete_element (
            aqelem_aquete_cod,
            aqelem_aqetape_cod,
            aqelem_param_id,
            aqelem_type,
            aqelem_misc_cod,
            aqelem_param_num_1,
            aqelem_param_num_2,
            aqelem_param_num_3,
            aqelem_param_txt_1,
            aqelem_param_txt_2,
            aqelem_param_txt_3                        )
                    values
                    (
                        :aqelem_aquete_cod,
                        :aqelem_aqetape_cod,
                        :aqelem_param_id,
                        :aqelem_type,
                        :aqelem_misc_cod,
                        :aqelem_param_num_1,
                        :aqelem_param_num_2,
                        :aqelem_param_num_3,
                        :aqelem_param_txt_1,
                        :aqelem_param_txt_2,
                        :aqelem_param_txt_3                        )
    returning aqelem_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqelem_aquete_cod" => $this->aqelem_aquete_cod,
                ":aqelem_aqetape_cod" => $this->aqelem_aqetape_cod,
                ":aqelem_param_id" => $this->aqelem_param_id,
                ":aqelem_type" => $this->aqelem_type,
                ":aqelem_misc_cod" => $this->aqelem_misc_cod,
                ":aqelem_param_num_1" => $this->aqelem_param_num_1,
                ":aqelem_param_num_2" => $this->aqelem_param_num_2,
                ":aqelem_param_num_3" => $this->aqelem_param_num_3,
                ":aqelem_param_txt_1" => $this->aqelem_param_txt_1,
                ":aqelem_param_txt_2" => $this->aqelem_param_txt_2,
                ":aqelem_param_txt_3" => $this->aqelem_param_txt_3,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_element
                    set
            aqelem_aquete_cod = :aqelem_aquete_cod,
            aqelem_aqetape_cod = :aqelem_aqetape_cod,
            aqelem_param_id = :aqelem_param_id,
            aqelem_type = :aqelem_type,
            aqelem_misc_cod = :aqelem_misc_cod,
            aqelem_param_num_1 = :aqelem_param_num_1,
            aqelem_param_num_2 = :aqelem_param_num_2,
            aqelem_param_num_3 = :aqelem_param_num_3,
            aqelem_param_txt_1 = :aqelem_param_txt_1,
            aqelem_param_txt_2 = :aqelem_param_txt_2,
            aqelem_param_txt_3 = :aqelem_param_txt_3                        where aqelem_cod = :aqelem_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqelem_cod" => $this->aqelem_cod,
                ":aqelem_aqetape_cod" => $this->aqelem_aqetape_cod,
                ":aqelem_aquete_cod" => $this->aqelem_aquete_cod,
                ":aqelem_param_id" => $this->aqelem_param_id,
                ":aqelem_type" => $this->aqelem_type,
                ":aqelem_misc_cod" => $this->aqelem_misc_cod,
                ":aqelem_param_num_1" => $this->aqelem_param_num_1,
                ":aqelem_param_num_2" => $this->aqelem_param_num_2,
                ":aqelem_param_num_3" => $this->aqelem_param_num_3,
                ":aqelem_param_txt_1" => $this->aqelem_param_txt_1,
                ":aqelem_param_txt_2" => $this->aqelem_param_txt_2,
                ":aqelem_param_txt_3" => $this->aqelem_param_txt_3,
            ),$stmt);
        }
    }

    /**
     * supprime tous les éléments d'une étapes
     * @global bdd_mysql $pdo
     * @return boolean => false pas réussi a supprimer
     */
    function deleteBy_aqetape_cod($aqetape_cod)
    {
        $pdo    = new bddpdo;
        $req    = "DELETE from quetes.aquete_element where aqelem_aqetape_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqetape_cod), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }

        return true;
    }


    /**
     * recherche les éléments d'une étapes par son n°
     * @global bdd_mysql $pdo
     * @return boolean => false pas trouvé
     */
    function getBy_etape_param_id($aqetape_cod, $param_id)
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqelem_cod  from quetes.aquete_element where aqelem_aqetape_cod = ? and aqelem_param_id = ? order by aqelem_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($aqetape_cod, $param_id),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_element;
            $temp->charge($result["aqelem_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        if(count($retour) == 0)
        {
            return false;
        }
        return $retour;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_element
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqelem_cod  from quetes.aquete_element order by aqelem_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_element;
            $temp->charge($result["aqelem_cod"]);
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
                    $req = "select aqelem_cod  from quetes.aquete_element where " . substr($name, 6) . " = ? order by aqelem_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_element;
                        $temp->charge($result["aqelem_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_element');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}