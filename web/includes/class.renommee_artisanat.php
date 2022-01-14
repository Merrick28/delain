<?php
/**
 * includes/class.renommee_artisanat.php
 */

/**
 * Class renommee_artisanat
 *
 * Gère les objets BDD de la table renommee_artisanat
 */
class renommee_artisanat
{
    var $renart_cod;
    var $renart_min;
    var $renart_max;
    var $renart_libelle;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de renommee_artisanat
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from renommee_artisanat where renart_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->renart_cod = $result['renart_cod'];
        $this->renart_min = $result['renart_min'];
        $this->renart_max = $result['renart_max'];
        $this->renart_libelle = $result['renart_libelle'];
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
            $req = "insert into renommee_artisanat (
            renart_min,
            renart_max,
            renart_libelle                        )
                    values
                    (
                        :renart_min,
                        :renart_max,
                        :renart_libelle                        )
    returning renart_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":renart_min" => $this->renart_min,
                ":renart_max" => $this->renart_max,
                ":renart_libelle" => $this->renart_libelle,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update renommee_artisanat
                    set
            renart_min = :renart_min,
            renart_max = :renart_max,
            renart_libelle = :renart_libelle                        where renart_cod = :renart_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":renart_cod" => $this->renart_cod,
                ":renart_min" => $this->renart_min,
                ":renart_max" => $this->renart_max,
                ":renart_libelle" => $this->renart_libelle,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \renommee_artisanat
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select renart_cod  from renommee_artisanat order by renart_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new renommee_artisanat;
            $temp->charge($result["renart_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function charge_by_valeur($valeur)
    {
        $pdo = new bddpdo();
        $req = "select renart_cod from renommee_artisanat
          where renart_min <= :valeur
          and renart_max > :valeur";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":valeur" => floor($valeur)
        ),$stmt);
        $result = $stmt->fetch();
        $this->charge($result['renart_cod']);
    }

    public function __call($name, $arguments){
        switch(substr($name, 0, 6)){
            case 'getBy_':
                if(property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo = new bddpdo;
                    $req = "select renart_cod  from renommee_artisanat where " . substr($name, 6) . " = ? order by renart_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new renommee_artisanat;
                        $temp->charge($result["renart_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table renommee_artisanat');
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