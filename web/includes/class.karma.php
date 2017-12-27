<?php
/**
 * includes/class.karma.php
 */

/**
 * Class karma
 *
 * Gère les objets BDD de la table karma
 */
class karma
{
    var $karma_cod;
    var $karma_min;
    var $karma_max;
    var $karma_libelle;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de karma
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from karma where karma_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->karma_cod = $result['karma_cod'];
        $this->karma_min = $result['karma_min'];
        $this->karma_max = $result['karma_max'];
        $this->karma_libelle = $result['karma_libelle'];
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
            $req = "insert into karma (
            karma_min,
            karma_max,
            karma_libelle                        )
                    values
                    (
                        :karma_min,
                        :karma_max,
                        :karma_libelle                        )
    returning karma_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":karma_min" => $this->karma_min,
                ":karma_max" => $this->karma_max,
                ":karma_libelle" => $this->karma_libelle,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update karma
                    set
            karma_min = :karma_min,
            karma_max = :karma_max,
            karma_libelle = :karma_libelle                        where karma_cod = :karma_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":karma_cod" => $this->karma_cod,
                ":karma_min" => $this->karma_min,
                ":karma_max" => $this->karma_max,
                ":karma_libelle" => $this->karma_libelle,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \karma
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select karma_cod  from karma order by karma_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new karma;
            $temp->charge($result["karma_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function charge_by_valeur($valeur)
    {
        $pdo = new bddpdo();
        $req = "select karma_cod from karma
          where karma_min <= :valeur
          and karma_max > :valeur";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":valeur" => floor($valeur)
        ),$stmt);
        $result = $stmt->fetch();
        $this->charge($result['karma_cod']);
    }

    public function __call($name, $arguments){
        switch(substr($name, 0, 6)){
            case 'getBy_':
                if(property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo = new bddpdo;
                    $req = "select karma_cod  from karma where " . substr($name, 6) . " = ? order by karma_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new karma;
                        $temp->charge($result["karma_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table karma');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}