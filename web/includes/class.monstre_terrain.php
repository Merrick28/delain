<?php
/**
 * includes/class.monstre_terrain.php
 */

/**
 * Class monstre_terrain
 *
 * Gère les objets BDD de la table monstre_terrain
 */
class monstre_terrain
{
    var $tmon_cod;
    var $tmon_gmon_cod;
    var $tmon_ter_cod;
    var $tmon_accessible = 'O';
    var $tmon_terrain_pa;
    var $tmon_event_chance;
    var $tmon_event_pa = '0';
    var $tmon_message;
    var $tmon_chevauchable = 'O';

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de monstre_terrain
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from monstre_terrain where tmon_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tmon_cod = $result['tmon_cod'];
        $this->tmon_gmon_cod = $result['tmon_gmon_cod'];
        $this->tmon_ter_cod = $result['tmon_ter_cod'];
        $this->tmon_accessible = $result['tmon_accessible'];
        $this->tmon_terrain_pa = $result['tmon_terrain_pa'];
        $this->tmon_event_chance = $result['tmon_event_chance'];
        $this->tmon_event_pa = $result['tmon_event_pa'];
        $this->tmon_message = $result['tmon_message'];
        $this->tmon_chevauchable = $result['tmon_chevauchable'];
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
            $req = "insert into monstre_terrain (
            tmon_gmon_cod,
            tmon_ter_cod,
            tmon_accessible,
            tmon_terrain_pa,
            tmon_event_chance,
            tmon_event_pa,
            tmon_message,
            tmon_chevauchable                        )
                    values
                    (
                        :tmon_gmon_cod,
                        :tmon_ter_cod,
                        :tmon_accessible,
                        :tmon_terrain_pa,
                        :tmon_event_chance,
                        :tmon_event_pa,
                        :tmon_message,
                        :tmon_chevauchable                        )
    returning tmon_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tmon_gmon_cod" => $this->tmon_gmon_cod,
                ":tmon_ter_cod" => $this->tmon_ter_cod,
                ":tmon_accessible" => $this->tmon_accessible,
                ":tmon_terrain_pa" => $this->tmon_terrain_pa,
                ":tmon_event_chance" => $this->tmon_event_chance,
                ":tmon_event_pa" => $this->tmon_event_pa,
                ":tmon_message" => $this->tmon_message,
                ":tmon_chevauchable" => $this->tmon_chevauchable,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update monstre_terrain
                    set
            tmon_gmon_cod = :tmon_gmon_cod,
            tmon_ter_cod = :tmon_ter_cod,
            tmon_accessible = :tmon_accessible,
            tmon_terrain_pa = :tmon_terrain_pa,
            tmon_event_chance = :tmon_event_chance,
            tmon_event_pa = :tmon_event_pa,
            tmon_message = :tmon_message,
            tmon_chevauchable = :tmon_chevauchable                        where tmon_cod = :tmon_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tmon_cod" => $this->tmon_cod,
                ":tmon_gmon_cod" => $this->tmon_gmon_cod,
                ":tmon_ter_cod" => $this->tmon_ter_cod,
                ":tmon_accessible" => $this->tmon_accessible,
                ":tmon_terrain_pa" => $this->tmon_terrain_pa,
                ":tmon_event_chance" => $this->tmon_event_chance,
                ":tmon_event_pa" => $this->tmon_event_pa,
                ":tmon_message" => $this->tmon_message,
                ":tmon_chevauchable" => $this->tmon_chevauchable,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \monstre_terrain
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select tmon_cod  from monstre_terrain order by tmon_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new monstre_terrain;
            $temp->charge($result["tmon_cod"]);
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
                    $req = "select tmon_cod  from monstre_terrain where " . substr($name, 6) . " = ? order by tmon_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new monstre_terrain;
                        $temp->charge($result["tmon_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table monstre_terrain');
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