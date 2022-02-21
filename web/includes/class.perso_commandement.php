<?php
/**
 * includes/class.perso_commandement.php
 */

/**
 * Class perso_commandement
 *
 * Gère les objets BDD de la table perso_commandement
 */
class perso_commandement
{
    var $perso_subalterne_cod;
    var $perso_superieur_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_commandement
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from perso_commandement where perso_subalterne_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->perso_subalterne_cod = $result['perso_subalterne_cod'];
        $this->perso_superieur_cod = $result['perso_superieur_cod'];
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
            $req = "insert into perso_commandement (
            perso_superieur_cod                        )
                    values
                    (
                        :perso_superieur_cod                        )
    returning perso_subalterne_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":perso_superieur_cod" => $this->perso_superieur_cod,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update perso_commandement
                    set
            perso_superieur_cod = :perso_superieur_cod                        where perso_subalterne_cod = :perso_subalterne_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":perso_subalterne_cod" => $this->perso_subalterne_cod,
                ":perso_superieur_cod"  => $this->perso_superieur_cod,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_commandement
     */
    function getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select perso_subalterne_cod  from perso_commandement order by perso_subalterne_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_commandement;
            $temp->charge($result["perso_subalterne_cod"]);
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
                    $pdo = new bddpdo;
                    $req = "select perso_subalterne_cod  from perso_commandement where " . substr($name, 6) . " = ? order by perso_subalterne_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_commandement;
                        $temp->charge($result["perso_subalterne_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_commandement');
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