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
    var $aqelem_type;
    var $aqelem_link_cod;
    var $aqelem_param_1;
    var $aqelem_param_2;
    var $aqelem_param_3;

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
        $req = "select * from aquete_element where aqelem_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqelem_cod = $result['aqelem_cod'];
        $this->aqelem_type = $result['aqelem_type'];
        $this->aqelem_link_cod = $result['aqelem_link_cod'];
        $this->aqelem_param_1 = $result['aqelem_param_1'];
        $this->aqelem_param_2 = $result['aqelem_param_2'];
        $this->aqelem_param_3 = $result['aqelem_param_3'];
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
            $req = "insert into aquete_element (
            aqelem_type,
            aqelem_link_cod,
            aqelem_param_1,
            aqelem_param_2,
            aqelem_param_3                        )
                    values
                    (
                        :aqelem_type,
                        :aqelem_link_cod,
                        :aqelem_param_1,
                        :aqelem_param_2,
                        :aqelem_param_3                        )
    returning aqelem_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqelem_type" => $this->aqelem_type,
                ":aqelem_link_cod" => $this->aqelem_link_cod,
                ":aqelem_param_1" => $this->aqelem_param_1,
                ":aqelem_param_2" => $this->aqelem_param_2,
                ":aqelem_param_3" => $this->aqelem_param_3,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update aquete_element
                    set
            aqelem_type = :aqelem_type,
            aqelem_link_cod = :aqelem_link_cod,
            aqelem_param_1 = :aqelem_param_1,
            aqelem_param_2 = :aqelem_param_2,
            aqelem_param_3 = :aqelem_param_3                        where aqelem_cod = :aqelem_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqelem_cod" => $this->aqelem_cod,
                ":aqelem_type" => $this->aqelem_type,
                ":aqelem_link_cod" => $this->aqelem_link_cod,
                ":aqelem_param_1" => $this->aqelem_param_1,
                ":aqelem_param_2" => $this->aqelem_param_2,
                ":aqelem_param_3" => $this->aqelem_param_3,
            ),$stmt);
        }
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
        $req = "select aqelem_cod  from aquete_element order by aqelem_cod";
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
                    $req = "select aqelem_cod  from aquete_element where " . substr($name, 6) . " = ? order by aqelem_cod";
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