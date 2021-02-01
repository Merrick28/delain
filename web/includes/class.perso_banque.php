<?php
/**
 * includes/class.perso_banque.php
 */

/**
 * Class perso_banque
 *
 * Gère les objets BDD de la table perso_banque
 */
class perso_banque
{
    var $pbank_cod;
    var $pbank_perso_cod;
    var $pbank_or;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_banque
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_banque where pbank_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pbank_cod       = $result['pbank_cod'];
        $this->pbank_perso_cod = $result['pbank_perso_cod'];
        $this->pbank_or        = $result['pbank_or'];
        return true;
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
            $req  = "insert into perso_banque (
            pbank_perso_cod,
            pbank_or                        )
                    values
                    (
                        :pbank_perso_cod,
                        :pbank_or                        )
    returning pbank_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":pbank_perso_cod" => $this->pbank_perso_cod,
                                      ":pbank_or"        => $this->pbank_or,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update perso_banque
                    set
            pbank_perso_cod = :pbank_perso_cod,
            pbank_or = :pbank_or                        where pbank_cod = :pbank_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":pbank_cod"       => $this->pbank_cod,
                                      ":pbank_perso_cod" => $this->pbank_perso_cod,
                                      ":pbank_or"        => $this->pbank_or,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \perso_banque
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pbank_cod  from perso_banque order by pbank_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_banque;
            $temp->charge($result["pbank_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPerso($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pbank_cod  from perso_banque where pbank_perso_cod = :perso";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['pbank_cod']);
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
                        "select pbank_cod  from perso_banque where " . substr($name, 6) . " = ? order by pbank_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_banque;
                        $temp->charge($result["pbank_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_banque');
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