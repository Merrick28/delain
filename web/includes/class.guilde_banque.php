<?php
/**
 * includes/class.guilde_banque.php
 */

/**
 * Class guilde_banque
 *
 * Gère les objets BDD de la table guilde_banque
 */
class guilde_banque
{
    var $gbank_cod;
    var $gbank_guilde_cod;
    var $gbank_nom;
    var $gbank_or;
    var $gbank_date_creation;

    function __construct()
    {
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     * @global bdd_mysql $pdo
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into guilde_banque (
            gbank_guilde_cod,
            gbank_nom,
            gbank_or,
            gbank_date_creation                        )
                    values
                    (
                        :gbank_guilde_cod,
                        :gbank_nom,
                        :gbank_or,
                        :gbank_date_creation                        )
    returning gbank_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":gbank_guilde_cod"    => $this->gbank_guilde_cod,
                                      ":gbank_nom"           => $this->gbank_nom,
                                      ":gbank_or"            => $this->gbank_or,
                                      ":gbank_date_creation" => $this->gbank_date_creation,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update guilde_banque
                    set
            gbank_guilde_cod = :gbank_guilde_cod,
            gbank_nom = :gbank_nom,
            gbank_or = :gbank_or,
            gbank_date_creation = :gbank_date_creation                        where gbank_cod = :gbank_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":gbank_cod"           => $this->gbank_cod,
                                      ":gbank_guilde_cod"    => $this->gbank_guilde_cod,
                                      ":gbank_nom"           => $this->gbank_nom,
                                      ":gbank_or"            => $this->gbank_or,
                                      ":gbank_date_creation" => $this->gbank_date_creation,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de guilde_banque
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from guilde_banque where gbank_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->gbank_cod           = $result['gbank_cod'];
        $this->gbank_guilde_cod    = $result['gbank_guilde_cod'];
        $this->gbank_nom           = $result['gbank_nom'];
        $this->gbank_or            = $result['gbank_or'];
        $this->gbank_date_creation = $result['gbank_date_creation'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \guilde_banque
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select gbank_cod  from guilde_banque order by gbank_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new guilde_banque;
            $temp->charge($result["gbank_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByGuilde($guilde_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select gbank_cod  from guilde_banque where gbank_guilde_cod = :guilde";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":guilde" => $guilde_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['gbnak_cod']);

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
                    $req    =
                        "select gbank_cod  from guilde_banque where " . substr($name, 6) . " = ? order by gbank_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new guilde_banque;
                        $temp->charge($result["gbank_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table guilde_banque');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}