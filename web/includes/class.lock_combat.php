<?php
/**
 * includes/class.lock_combat.php
 */

/**
 * Class lock_combat
 *
 * Gère les objets BDD de la table lock_combat
 */
class lock_combat
{
    var $lock_cod;
    var $lock_attaquant;
    var $lock_cible;
    var $lock_nb_tours;
    var $lock_date;

    function __construct()
    {

        $this->lock_date = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de lock_combat
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from lock_combat where lock_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->lock_cod = $result['lock_cod'];
        $this->lock_attaquant = $result['lock_attaquant'];
        $this->lock_cible = $result['lock_cible'];
        $this->lock_nb_tours = $result['lock_nb_tours'];
        $this->lock_date = $result['lock_date'];
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
            $req = "insert into lock_combat (
            lock_attaquant,
            lock_cible,
            lock_nb_tours,
            lock_date                        )
                    values
                    (
                        :lock_attaquant,
                        :lock_cible,
                        :lock_nb_tours,
                        :lock_date                        )
    returning lock_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":lock_attaquant" => $this->lock_attaquant,
                ":lock_cible" => $this->lock_cible,
                ":lock_nb_tours" => $this->lock_nb_tours,
                ":lock_date" => $this->lock_date,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update lock_combat
                    set
            lock_attaquant = :lock_attaquant,
            lock_cible = :lock_cible,
            lock_nb_tours = :lock_nb_tours,
            lock_date = :lock_date                        where lock_cod = :lock_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":lock_cod" => $this->lock_cod,
                ":lock_attaquant" => $this->lock_attaquant,
                ":lock_cible" => $this->lock_cible,
                ":lock_nb_tours" => $this->lock_nb_tours,
                ":lock_date" => $this->lock_date,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \lock_combat
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select lock_cod  from lock_combat order by lock_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new lock_combat;
            $temp->charge($result["lock_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function  getByCible($cible)
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select lock_cod  from lock_combat where lock_cible = :cible order by lock_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":cible" => $cible),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new lock_combat;
            $temp->charge($result["lock_cod"]);
            $perso = new perso;
            $perso->charge($temp->lock_attaquant);
            $temp->attaquant = $perso;
            unset($perso);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function  getByAttaquant($cible)
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select lock_cod  from lock_combat where lock_attaquant = :cible order by lock_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":cible" => $cible),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new lock_combat;
            $temp->charge($result["lock_cod"]);
            $perso = new perso;
            $perso->charge($temp->lock_cible);
            $temp->cible = $perso;
            unset($perso);
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
                    $req = "select lock_cod  from lock_combat where " . substr($name, 6) . " = ? order by lock_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new lock_combat;
                        $temp->charge($result["lock_cod"]);
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
                    die('Unknown variable ' . substr($name,6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}