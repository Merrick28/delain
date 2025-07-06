<?php
/**
 * includes/class.compteur.php
 */

/**
 * Class compteur
 *
 * Gère les objets BDD de la table compteur
 */
class compteur
{
    var $compteur_cod;
    var $compteur_libelle;
    var $compteur_type = 0;
    var $compteur_init = 0;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de compteur
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from compteur where compteur_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->compteur_cod = $result['compteur_cod'];
        $this->compteur_libelle = $result['compteur_libelle'];
        $this->compteur_type = $result['compteur_type'];
        $this->compteur_init = $result['compteur_init'];
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
            $req = "insert into compteur (
            compteur_libelle,
            compteur_type,
            compteur_init                        )
                    values
                    (
                        :compteur_libelle,
                        :compteur_type,
                        :compteur_init                        )
    returning compteur_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":compteur_libelle" => $this->compteur_libelle,
                ":compteur_type" => $this->compteur_type,
                ":compteur_init" => $this->compteur_init,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update compteur
                    set
            compteur_libelle = :compteur_libelle,
            compteur_type = :compteur_type,
            compteur_init = :compteur_init                        where compteur_cod = :compteur_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":compteur_cod" => $this->compteur_cod,
                ":compteur_libelle" => $this->compteur_libelle,
                ":compteur_type" => $this->compteur_type,
                ":compteur_init" => $this->compteur_init,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \compteur
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select compteur_cod  from compteur order by compteur_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new compteur;
            $temp->charge($result["compteur_cod"]);
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
                    $req = "select compteur_cod  from compteur where " . substr($name, 6) . " = ? order by compteur_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new compteur;
                        $temp->charge($result["compteur_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table compteur');
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