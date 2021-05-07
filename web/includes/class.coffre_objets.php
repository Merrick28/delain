<?php
/**
 * includes/class.coffre_objets.php
 */

/**
 * Class coffre_objets
 *
 * Gère les objets BDD de la table coffre_objets
 */
class coffre_objets
{
    var $coffre_cod;
    var $coffre_compt_cod;
    var $coffre_obj_cod;
    var $coffre_perso_cod;
    var $coffre_date_depot;

    function __construct()
    {

        $this->coffre_date_depot = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de coffre_objets
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from coffre_objets where coffre_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->coffre_cod = $result['coffre_cod'];
        $this->coffre_compt_cod = $result['coffre_compt_cod'];
        $this->coffre_obj_cod = $result['coffre_obj_cod'];
        $this->coffre_perso_cod = $result['coffre_perso_cod'];
        $this->coffre_date_depot = $result['coffre_date_depot'];
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
            $req = "insert into coffre_objets (
            coffre_compt_cod,
            coffre_obj_cod,
            coffre_perso_cod,
            coffre_date_depot                        )
                    values
                    (
                        :coffre_compt_cod,
                        :coffre_obj_cod,
                        :coffre_perso_cod,
                        :coffre_date_depot                        )
    returning coffre_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":coffre_compt_cod" => $this->coffre_compt_cod,
                ":coffre_obj_cod" => $this->coffre_obj_cod,
                ":coffre_perso_cod" => $this->coffre_perso_cod,
                ":coffre_date_depot" => $this->coffre_date_depot,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update coffre_objets
                    set
            coffre_compt_cod = :coffre_compt_cod,
            coffre_obj_cod = :coffre_obj_cod,
            coffre_perso_cod = :coffre_perso_cod,
            coffre_date_depot = :coffre_date_depot                        where coffre_cod = :coffre_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":coffre_cod" => $this->coffre_cod,
                ":coffre_compt_cod" => $this->coffre_compt_cod,
                ":coffre_obj_cod" => $this->coffre_obj_cod,
                ":coffre_perso_cod" => $this->coffre_perso_cod,
                ":coffre_date_depot" => $this->coffre_date_depot,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \coffre_objets
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select coffre_cod  from coffre_objets order by coffre_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new coffre_objets;
            $temp->charge($result["coffre_cod"]);
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
                    $req = "select coffre_cod  from coffre_objets where " . substr($name, 6) . " = ? order by coffre_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new coffre_objets;
                        $temp->charge($result["coffre_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table coffre_objets');
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