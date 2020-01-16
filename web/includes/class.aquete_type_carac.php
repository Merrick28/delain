<?php
/**
 * includes/class.aquete_type_carac.php
 */

/**
 * Class aquete_type_carac
 *
 * Gère les objets BDD de la table aquete_type_carac
 */
class aquete_type_carac
{
    var $aqtypecarac_cod;
    var $aqtypecarac_nom;
    var $aqtypecarac_type;
    var $aqtypecarac_description;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de aquete_type_carac
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_type_carac where aqtypecarac_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqtypecarac_cod = $result['aqtypecarac_cod'];
        $this->aqtypecarac_nom = $result['aqtypecarac_nom'];
        $this->aqtypecarac_type = $result['aqtypecarac_type'];
        $this->aqtypecarac_description = $result['aqtypecarac_description'];
        $this->aqtypecarac_cod = $result['aqtypecarac_cod'];
        $this->aqtypecarac_nom = $result['aqtypecarac_nom'];
        $this->aqtypecarac_type = $result['aqtypecarac_type'];
        $this->aqtypecarac_description = $result['aqtypecarac_description'];
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
            $req = "insert into quetes.aquete_type_carac (
            aqtypecarac_nom,
            aqtypecarac_type,
            aqtypecarac_description,
            aqtypecarac_nom,
            aqtypecarac_type,
            aqtypecarac_description                        )
                    values
                    (
                        :aqtypecarac_nom,
                        :aqtypecarac_type,
                        :aqtypecarac_description,
                        :aqtypecarac_nom,
                        :aqtypecarac_type,
                        :aqtypecarac_description                        )
    returning aqtypecarac_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqtypecarac_nom" => $this->aqtypecarac_nom,
                ":aqtypecarac_type" => $this->aqtypecarac_type,
                ":aqtypecarac_description" => $this->aqtypecarac_description,
                ":aqtypecarac_nom" => $this->aqtypecarac_nom,
                ":aqtypecarac_type" => $this->aqtypecarac_type,
                ":aqtypecarac_description" => $this->aqtypecarac_description,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_type_carac
                    set
            aqtypecarac_nom = :aqtypecarac_nom,
            aqtypecarac_type = :aqtypecarac_type,
            aqtypecarac_description = :aqtypecarac_description,
            aqtypecarac_nom = :aqtypecarac_nom,
            aqtypecarac_type = :aqtypecarac_type,
            aqtypecarac_description = :aqtypecarac_description                        where aqtypecarac_cod = :aqtypecarac_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqtypecarac_cod" => $this->aqtypecarac_cod,
                ":aqtypecarac_nom" => $this->aqtypecarac_nom,
                ":aqtypecarac_type" => $this->aqtypecarac_type,
                ":aqtypecarac_description" => $this->aqtypecarac_description,
                ":aqtypecarac_cod" => $this->aqtypecarac_cod,
                ":aqtypecarac_nom" => $this->aqtypecarac_nom,
                ":aqtypecarac_type" => $this->aqtypecarac_type,
                ":aqtypecarac_description" => $this->aqtypecarac_description,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_type_carac
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqtypecarac_cod  from quetes.aquete_type_carac order by aqtypecarac_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_type_carac;
            $temp->charge($result["aqtypecarac_cod"]);
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
                    $req = "select aqtypecarac_cod  from quetes.aquete_type_carac where " . substr($name, 6) . " = ? order by aqtypecarac_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_type_carac;
                        $temp->charge($result["aqtypecarac_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_type_carac');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}