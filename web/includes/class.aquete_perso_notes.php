<?php
/**
 * includes/class.quetes.aquete_perso_notes.php
 */

/**
 * Class quetes.aquete_perso_notes
 *
 * Gère les objets BDD de la table quetes.aquete_perso_notes
 */
class aquete_perso_notes
{
    var $aqperson_cod;
    var $aqperson_date;
    var $aqperson_perso_cod;
    var $aqperson_notes;
    function __construct()
    {
        $this->aqperson_date = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de quetes.aquete_perso_notes
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_perso_notes where aqperson_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqperson_cod = $result['aqperson_cod'];
        $this->aqperson_date = $result['aqperson_date'];
        $this->aqperson_perso_cod = $result['aqperson_perso_cod'];
        $this->aqperson_notes = $result['aqperson_notes'];
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
            $req = "insert into quetes.aquete_perso_notes (
            aqperson_date,
            aqperson_perso_cod,
            aqperson_notes                        )
                    values
                    (
                        :aqperson_date,
                        :aqperson_perso_cod,
                        :aqperson_notes                        )
    returning aqperson_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqperson_date" => $this->aqperson_date,
                ":aqperson_perso_cod" => $this->aqperson_perso_cod,
                ":aqperson_notes" => $this->aqperson_notes,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_perso_notes
                    set
            aqperson_date = :aqperson_date,
            aqperson_perso_cod = :aqperson_perso_cod,
            aqperson_notes = :aqperson_notes                        where aqperson_cod = :aqperson_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqperson_cod" => $this->aqperson_cod,
                ":aqperson_date" => $this->aqperson_date,
                ":aqperson_perso_cod" => $this->aqperson_perso_cod,
                ":aqperson_notes" => $this->aqperson_notes,
            ),$stmt);
        }
    }


    function charge_par_perso($perso_cod)
    {
        $pdo = new bddpdo;
        $req = "select aqperson_cod from quetes.aquete_perso_notes where aqperson_perso_cod = ? limit 1";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->charge($result['aqperson_cod']);
        return true;
    }


    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \quetes.aquete_perso_notes
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqperson_cod  from quetes.aquete_perso_notes order by aqperson_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_perso_notes();
            $temp->charge($result["aqperson_cod"]);
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
                    $req = "select aqperson_cod  from quetes.aquete_perso_notes where " . substr($name, 6) . " = ? order by aqperson_cod";

                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_perso_notes();
                        $temp->charge($result["aqperson_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table quetes.aquete_perso_notes');
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