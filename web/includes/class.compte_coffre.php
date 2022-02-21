<?php
/**
 * includes/class.compte_coffre.php
 */

/**
 * Class compte_coffre
 *
 * Gère les objets BDD de la table compte_coffre
 */
class compte_coffre
{
    var $ccompt_cod;
    var $ccompt_compt_cod;
    var $ccompt_date_ouverture;
    var $ccompt_date_extension;
    var $ccompt_taille = 0;
    var $ccompt_cout = 0;

    function __construct()
    {

        $this->ccompt_date_ouverture = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de compte_coffre
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from compte_coffre where ccompt_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->ccompt_cod = $result['ccompt_cod'];
        $this->ccompt_compt_cod = $result['ccompt_compt_cod'];
        $this->ccompt_date_ouverture = $result['ccompt_date_ouverture'];
        $this->ccompt_date_extension = $result['ccompt_date_extension'];
        $this->ccompt_taille = $result['ccompt_taille'];
        $this->ccompt_cout = $result['ccompt_cout'];
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
            $req = "insert into compte_coffre (
            ccompt_compt_cod,
            ccompt_date_ouverture,
            ccompt_date_extension,
            ccompt_taille,
            ccompt_cout                        )
                    values
                    (
                        :ccompt_compt_cod,
                        :ccompt_date_ouverture,
                        :ccompt_date_extension,
                        :ccompt_taille,
                        :ccompt_cout                        )
    returning ccompt_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":ccompt_compt_cod" => $this->ccompt_compt_cod,
                ":ccompt_date_ouverture" => $this->ccompt_date_ouverture,
                ":ccompt_date_extension" => $this->ccompt_date_extension,
                ":ccompt_taille" => $this->ccompt_taille,
                ":ccompt_cout" => $this->ccompt_cout,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update compte_coffre
                    set
            ccompt_compt_cod = :ccompt_compt_cod,
            ccompt_date_ouverture = :ccompt_date_ouverture,
            ccompt_date_extension = :ccompt_date_extension,
            ccompt_taille = :ccompt_taille,
            ccompt_cout = :ccompt_cout                        where ccompt_cod = :ccompt_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":ccompt_cod" => $this->ccompt_cod,
                ":ccompt_compt_cod" => $this->ccompt_compt_cod,
                ":ccompt_date_ouverture" => $this->ccompt_date_ouverture,
                ":ccompt_date_extension" => $this->ccompt_date_extension,
                ":ccompt_taille" => $this->ccompt_taille,
                ":ccompt_cout" => $this->ccompt_cout,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \compte_coffre
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select ccompt_cod  from compte_coffre order by ccompt_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new compte_coffre;
            $temp->charge($result["ccompt_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function __call($name, $arguments)
    {

        if (substr($name, 0, 6) == 'getBy_' )
        {
            if(property_exists($this, substr($name, 6)))
            {
                $retour = array();
                $pdo = new bddpdo;
                $req = "select ccompt_cod  from compte_coffre where " . substr($name, 6) . " = ? order by ccompt_cod";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($arguments[0]),$stmt);
                while($result = $stmt->fetch())
                {
                    $temp = new compte_coffre;
                    $temp->charge($result["ccompt_cod"]);
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
                die('Unknown variable ' . substr($name, 6) . ' in table compte_coffre');
            }
        }
        else if (substr($name, 0, 7) == 'loadBy_' )
        {
                if(property_exists($this, substr($name, 7)))
                {
                    $pdo = new bddpdo;
                    $req = "select ccompt_cod  from compte_coffre where " . substr($name, 7) . " = ? order by ccompt_cod limit 1";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    if(! $result = $stmt->fetch())
                    {
                        return false;
                    }

                    $this->charge($result["ccompt_cod"]);
                    return $this;
                }
                else
                {
                    die('Unknown variable ' . substr($name, 7) . ' in table compte_coffre');
                }
        }
        else
        {

            ob_start();
            debug_print_backtrace();
            $out = ob_get_contents();
            error_log($out);
            die('Unknown method.');
        }
    }
}