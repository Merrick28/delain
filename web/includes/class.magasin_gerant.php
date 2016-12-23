<?php
/**
 * includes/class.magasin_gerant.php
 */

/**
 * Class magasin_gerant
 *
 * Gère les objets BDD de la table magasin_gerant
 */
class magasin_gerant
{
    var $mger_lieu_cod;
    var $mger_perso_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de magasin_gerant
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from magasin_gerant where mger_lieu_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->mger_lieu_cod = $result['mger_lieu_cod'];
        $this->mger_perso_cod = $result['mger_perso_cod'];
        return true;
    }

    function getByPersoCod($perso_cod)
    {
        $pdo = new bddpdo;
        $req = "select mger_lieu_cod from magasin_gerant where mger_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['mger_lieu_cod']);
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
            $req = "insert into magasin_gerant (
            mger_perso_cod                        )
                    values
                    (
                        :mger_perso_cod                        )
    returning mger_lieu_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":mger_perso_cod" => $this->mger_perso_cod,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update magasin_gerant
                    set
            mger_perso_cod = :mger_perso_cod                        where mger_lieu_cod = :mger_lieu_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":mger_lieu_cod" => $this->mger_lieu_cod,
                ":mger_perso_cod" => $this->mger_perso_cod,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \magasin_gerant
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select mger_lieu_cod  from magasin_gerant order by mger_lieu_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new magasin_gerant;
            $temp->charge($result["mger_lieu_cod"]);
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
                    $req = "select mger_lieu_cod  from magasin_gerant where " . substr($name, 6) . " = ? order by mger_lieu_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new magasin_gerant;
                        $temp->charge($result["mger_lieu_cod"]);
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
                    die('Unknown variable.');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}