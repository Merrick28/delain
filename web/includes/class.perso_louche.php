<?php
/**
 * includes/class.perso_louche.php
 */

/**
 * Class perso_louche
 *
 * Gère les objets BDD de la table perso_louche
 */
class perso_louche
{
    var $plouche_perso_cod;
    var $plouche_nb_tours = 3;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_louche
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_louche where plouche_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->plouche_perso_cod = $result['plouche_perso_cod'];
        $this->plouche_nb_tours  = $result['plouche_nb_tours'];
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
            $req  = "insert into perso_louche (
            plouche_nb_tours                        )
                    values
                    (
                        :plouche_nb_tours                        )
    returning plouche_perso_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":plouche_nb_tours" => $this->plouche_nb_tours,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update perso_louche
                    set
            plouche_nb_tours = :plouche_nb_tours                        where plouche_perso_cod = :plouche_perso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":plouche_perso_cod" => $this->plouche_perso_cod,
                                      ":plouche_nb_tours"  => $this->plouche_nb_tours,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_louche
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select plouche_perso_cod  from perso_louche order by plouche_perso_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_louche;
            $temp->charge($result["plouche_perso_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPerso($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select plouche_perso_cod  from perso_louche where plouche_perso_cod = :perso";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['plouche_perso_cod']);
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
                        "select plouche_perso_cod  from perso_louche where " . substr($name, 6) . " = ? order by plouche_perso_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_louche;
                        $temp->charge($result["plouche_perso_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_louche');
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