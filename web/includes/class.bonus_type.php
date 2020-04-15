<?php
/**
 * includes/class.bonus_type.php
 */

/**
 * Class bonus_type
 *
 * Gère les objets BDD de la table bonus_type
 */
class bonus_type
{
    var $tbonus_cod;
    var $tbonus_libc;
    var $tonbus_libelle;
    var $tbonus_gentil_positif = true;
    var $tbonus_nettoyable = 'O';

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de bonus_type
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from bonus_type where tbonus_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tbonus_cod = $result['tbonus_cod'];
        $this->tbonus_libc = $result['tbonus_libc'];
        $this->tonbus_libelle = $result['tonbus_libelle'];
        $this->tbonus_gentil_positif = $result['tbonus_gentil_positif'];
        $this->tbonus_nettoyable = $result['tbonus_nettoyable'];
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
            $req = "insert into bonus_type (
            tbonus_libc,
            tonbus_libelle,
            tbonus_gentil_positif,
            tbonus_nettoyable                        )
                    values
                    (
                        :tbonus_libc,
                        :tonbus_libelle,
                        :tbonus_gentil_positif,
                        :tbonus_nettoyable                        )
    returning tbonus_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tbonus_libc" => $this->tbonus_libc,
                ":tonbus_libelle" => $this->tonbus_libelle,
                ":tbonus_gentil_positif" => $this->tbonus_gentil_positif,
                ":tbonus_nettoyable" => $this->tbonus_nettoyable,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update bonus_type
                    set
            tbonus_libc = :tbonus_libc,
            tonbus_libelle = :tonbus_libelle,
            tbonus_gentil_positif = :tbonus_gentil_positif,
            tbonus_nettoyable = :tbonus_nettoyable                        where tbonus_cod = :tbonus_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tbonus_cod" => $this->tbonus_cod,
                ":tbonus_libc" => $this->tbonus_libc,
                ":tonbus_libelle" => $this->tonbus_libelle,
                ":tbonus_gentil_positif" => $this->tbonus_gentil_positif,
                ":tbonus_nettoyable" => $this->tbonus_nettoyable,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \bonus_type
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select tbonus_cod  from bonus_type order by tbonus_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new bonus_type;
            $temp->charge($result["tbonus_cod"]);
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
                    $req = "select tbonus_cod  from bonus_type where " . substr($name, 6) . " = ? order by tbonus_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new bonus_type;
                        $temp->charge($result["tbonus_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table bonus_type');
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