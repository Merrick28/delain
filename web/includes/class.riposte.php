<?php
/**
 * includes/class.riposte.php
 */

/**
 * Class riposte
 *
 * Gère les objets BDD de la table riposte
 */
class riposte
{
    var $riposte_cod;
    var $riposte_attaquant;
    var $riposte_cible;
    var $riposte_nb_tours = 2;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de riposte
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from riposte where riposte_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->riposte_cod       = $result['riposte_cod'];
        $this->riposte_attaquant = $result['riposte_attaquant'];
        $this->riposte_cible     = $result['riposte_cible'];
        $this->riposte_nb_tours  = $result['riposte_nb_tours'];
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
            $req  = "insert into riposte (
            riposte_attaquant,
            riposte_cible,
            riposte_nb_tours                        )
                    values
                    (
                        :riposte_attaquant,
                        :riposte_cible,
                        :riposte_nb_tours                        )
    returning riposte_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":riposte_attaquant" => $this->riposte_attaquant,
                                      ":riposte_cible"     => $this->riposte_cible,
                                      ":riposte_nb_tours"  => $this->riposte_nb_tours,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update riposte
                    set
            riposte_attaquant = :riposte_attaquant,
            riposte_cible = :riposte_cible,
            riposte_nb_tours = :riposte_nb_tours                        where riposte_cod = :riposte_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":riposte_cod"       => $this->riposte_cod,
                                      ":riposte_attaquant" => $this->riposte_attaquant,
                                      ":riposte_cible"     => $this->riposte_cible,
                                      ":riposte_nb_tours"  => $this->riposte_nb_tours,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \riposte
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select riposte_cod  from riposte order by riposte_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new riposte;
            $temp->charge($result["riposte_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByCible($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select riposte_cod  from riposte where riposte_cible = :perso order by riposte_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $perso_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new riposte;
            $temp->charge($result["riposte_cod"]);
            $perso = new perso;
            $perso->charge($temp->riposte_attaquant);
            $temp->attaquant = $perso;
            unset($perso);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByAttaquant($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select riposte_cod  from riposte where riposte_attaquant = :perso order by riposte_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $perso_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new riposte;
            $temp->charge($result["riposte_cod"]);
            $perso = new perso;
            $perso->charge($temp->riposte_cible);
            $temp->cible = $perso;
            unset($perso);
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
                        "select riposte_cod  from riposte where " . substr($name, 6) . " = ? order by riposte_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new riposte;
                        $temp->charge($result["riposte_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table riposte');
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