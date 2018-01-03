<?php
/**
 * includes/class.guilde_rang.php
 */

/**
 * Class guilde_rang
 *
 * Gère les objets BDD de la table guilde_rang
 */
class guilde_rang
{
    var $rguilde_cod;
    var $rguilde_rang_cod;
    var $rguilde_guilde_cod;
    var $rguilde_libelle_rang;
    var $rguilde_admin = 'N';
    var $rguilde_solde = 0;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de guilde_rang
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from guilde_rang where rguilde_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->rguilde_cod          = $result['rguilde_cod'];
        $this->rguilde_rang_cod     = $result['rguilde_rang_cod'];
        $this->rguilde_guilde_cod   = $result['rguilde_guilde_cod'];
        $this->rguilde_libelle_rang = $result['rguilde_libelle_rang'];
        $this->rguilde_admin        = $result['rguilde_admin'];
        $this->rguilde_solde        = $result['rguilde_solde'];
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
        if ($new)
        {
            $req
                  = "insert into guilde_rang (
            rguilde_rang_cod,
            rguilde_guilde_cod,
            rguilde_libelle_rang,
            rguilde_admin,
            rguilde_solde                        )
                    values
                    (
                        :rguilde_rang_cod,
                        :rguilde_guilde_cod,
                        :rguilde_libelle_rang,
                        :rguilde_admin,
                        :rguilde_solde                        )
    returning rguilde_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":rguilde_rang_cod"     => $this->rguilde_rang_cod,
                ":rguilde_guilde_cod"   => $this->rguilde_guilde_cod,
                ":rguilde_libelle_rang" => $this->rguilde_libelle_rang,
                ":rguilde_admin"        => $this->rguilde_admin,
                ":rguilde_solde"        => $this->rguilde_solde,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req
                  = "update guilde_rang
                    set
            rguilde_rang_cod = :rguilde_rang_cod,
            rguilde_guilde_cod = :rguilde_guilde_cod,
            rguilde_libelle_rang = :rguilde_libelle_rang,
            rguilde_admin = :rguilde_admin,
            rguilde_solde = :rguilde_solde                        where rguilde_cod = :rguilde_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":rguilde_cod"          => $this->rguilde_cod,
                ":rguilde_rang_cod"     => $this->rguilde_rang_cod,
                ":rguilde_guilde_cod"   => $this->rguilde_guilde_cod,
                ":rguilde_libelle_rang" => $this->rguilde_libelle_rang,
                ":rguilde_admin"        => $this->rguilde_admin,
                ":rguilde_solde"        => $this->rguilde_solde,
            ), $stmt);
        }
    }

    function get_by_guilde_rang($guilde,$rang)
    {
        $pdo    = new bddpdo;
        $req = "select rguilde_cod from guilde_rang 
            where rguilde_guilde_cod = :guilde and rguilde_rang_cod = :rang";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":guilde" => $guilde,":rang" => $rang),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['rguilde_cod']);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \guilde_rang
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select rguilde_cod  from guilde_rang order by rguilde_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new guilde_rang;
            $temp->charge($result["rguilde_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function __call($name, $arguments)
    {
        switch (substr($name, 0, 6))
        {
            case 'getBy_':
                if (property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo    = new bddpdo;
                    $req    = "select rguilde_cod  from guilde_rang where " . substr($name, 6) . " = ? order by rguilde_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new guilde_rang;
                        $temp->charge($result["rguilde_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                } else
                {
                    die('Unknown variable ' . substr($name, 6) . ' in table guilde_rang');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}