<?php
/**
 * includes/class.aquete_etape_param.php
 */

/**
 * Class aquete_etape_param
 *
 * Gère les objets BDD de la table aquete_etape_param
 */
class aquete_etape_param
{
    var $aqetapparm_cod;
    var $aqetapparm_aqetape_cod;
    var $aqetapparm_id = 1;
    var $aqetapparm_type;
    var $aqetapparm_n = 1;
    var $aqetapparm_m = 1;
    var $aqetape_item = 0;
    var $aqetape_aqelem_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de aquete_etape_param
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from aquete_etape_param where aqetapparm_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqetapparm_cod = $result['aqetapparm_cod'];
        $this->aqetapparm_aqetape_cod = $result['aqetapparm_aqetape_cod'];
        $this->aqetapparm_id = $result['aqetapparm_id'];
        $this->aqetapparm_type = $result['aqetapparm_type'];
        $this->aqetapparm_n = $result['aqetapparm_n'];
        $this->aqetapparm_m = $result['aqetapparm_m'];
        $this->aqetape_item = $result['aqetape_item'];
        $this->aqetape_aqelem_cod = $result['aqetape_aqelem_cod'];
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
            $req = "insert into aquete_etape_param (
            aqetapparm_aqetape_cod,
            aqetapparm_id,
            aqetapparm_type,
            aqetapparm_n,
            aqetapparm_m,
            aqetape_item,
            aqetape_aqelem_cod                        )
                    values
                    (
                        :aqetapparm_aqetape_cod,
                        :aqetapparm_id,
                        :aqetapparm_type,
                        :aqetapparm_n,
                        :aqetapparm_m,
                        :aqetape_item,
                        :aqetape_aqelem_cod                        )
    returning aqetapparm_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetapparm_aqetape_cod" => $this->aqetapparm_aqetape_cod,
                ":aqetapparm_id" => $this->aqetapparm_id,
                ":aqetapparm_type" => $this->aqetapparm_type,
                ":aqetapparm_n" => $this->aqetapparm_n,
                ":aqetapparm_m" => $this->aqetapparm_m,
                ":aqetape_item" => $this->aqetape_item,
                ":aqetape_aqelem_cod" => $this->aqetape_aqelem_cod,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update aquete_etape_param
                    set
            aqetapparm_aqetape_cod = :aqetapparm_aqetape_cod,
            aqetapparm_id = :aqetapparm_id,
            aqetapparm_type = :aqetapparm_type,
            aqetapparm_n = :aqetapparm_n,
            aqetapparm_m = :aqetapparm_m,
            aqetape_item = :aqetape_item,
            aqetape_aqelem_cod = :aqetape_aqelem_cod                        where aqetapparm_cod = :aqetapparm_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetapparm_cod" => $this->aqetapparm_cod,
                ":aqetapparm_aqetape_cod" => $this->aqetapparm_aqetape_cod,
                ":aqetapparm_id" => $this->aqetapparm_id,
                ":aqetapparm_type" => $this->aqetapparm_type,
                ":aqetapparm_n" => $this->aqetapparm_n,
                ":aqetapparm_m" => $this->aqetapparm_m,
                ":aqetape_item" => $this->aqetape_item,
                ":aqetape_aqelem_cod" => $this->aqetape_aqelem_cod,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_etape_param
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqetapparm_cod  from aquete_etape_param order by aqetapparm_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_etape_param;
            $temp->charge($result["aqetapparm_cod"]);
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
                    $req = "select aqetapparm_cod  from aquete_etape_param where " . substr($name, 6) . " = ? order by aqetapparm_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_etape_param;
                        $temp->charge($result["aqetapparm_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_etape_param');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}