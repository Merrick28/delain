<?php
/**
 * includes/class.concentrations.php
 */

/**
 * Class concentrations
 *
 * Gère les objets BDD de la table concentrations
 */
class concentrations
{
    var $concentration_cod;
    var $concentration_perso_cod;
    var $concentration_nb_tours;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de concentrations
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from concentrations where concentration_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->concentration_cod       = $result['concentration_cod'];
        $this->concentration_perso_cod = $result['concentration_perso_cod'];
        $this->concentration_nb_tours  = $result['concentration_nb_tours'];
        return true;
    }

    function getByPerso($perso_cod)
    {
        $pdo  = new bddpdo;
        $req  = "select concentration_cod from concentrations where concentration_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }

        return $this->charge($result['concentration_cod']);
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
            $req  = "insert into concentrations (
            concentration_perso_cod,
            concentration_nb_tours                        )
                    values
                    (
                        :concentration_perso_cod,
                        :concentration_nb_tours                        )
    returning concentration_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":concentration_perso_cod" => $this->concentration_perso_cod,
                                      ":concentration_nb_tours"  => $this->concentration_nb_tours,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update concentrations
                    set
            concentration_perso_cod = :concentration_perso_cod,
            concentration_nb_tours = :concentration_nb_tours                        where concentration_cod = :concentration_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":concentration_cod"       => $this->concentration_cod,
                                      ":concentration_perso_cod" => $this->concentration_perso_cod,
                                      ":concentration_nb_tours"  => $this->concentration_nb_tours,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \concentrations
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select concentration_cod  from concentrations order by concentration_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new concentrations;
            $temp->charge($result["concentration_cod"]);
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
                    $req    =
                        "select concentration_cod  from concentrations where " . substr($name, 6) . " = ? order by concentration_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new concentrations;
                        $temp->charge($result["concentration_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table concentrations');
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