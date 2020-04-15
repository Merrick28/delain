<?php
/**
 * includes/class.dieu_perso.php
 */

/**
 * Class dieu_perso
 *
 * Gère les objets BDD de la table dieu_perso
 */
class dieu_perso
{
    var $dper_cod;
    var $dper_dieu_cod;
    var $dper_perso_cod;
    var $dper_niveau;
    var $dper_points;

    function __construct()
    {
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
            $req  = "insert into dieu_perso (
            dper_dieu_cod,
            dper_perso_cod,
            dper_niveau,
            dper_points                        )
                    values
                    (
                        :dper_dieu_cod,
                        :dper_perso_cod,
                        :dper_niveau,
                        :dper_points                        )
    returning dper_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":dper_dieu_cod" => $this->dper_dieu_cod,
                ":dper_perso_cod" => $this->dper_perso_cod,
                ":dper_niveau" => $this->dper_niveau,
                ":dper_points" => $this->dper_points,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update dieu_perso
                    set
            dper_dieu_cod = :dper_dieu_cod,
            dper_perso_cod = :dper_perso_cod,
            dper_niveau = :dper_niveau,
            dper_points = :dper_points                        where dper_cod = :dper_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":dper_cod" => $this->dper_cod,
                ":dper_dieu_cod" => $this->dper_dieu_cod,
                ":dper_perso_cod" => $this->dper_perso_cod,
                ":dper_niveau" => $this->dper_niveau,
                ":dper_points" => $this->dper_points,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de dieu_perso
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from dieu_perso where dper_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->dper_cod       = $result['dper_cod'];
        $this->dper_dieu_cod  = $result['dper_dieu_cod'];
        $this->dper_perso_cod = $result['dper_perso_cod'];
        $this->dper_niveau    = $result['dper_niveau'];
        $this->dper_points    = $result['dper_points'];
        return true;
    }

    function getByPersoCod($perso_cod)
    {
        $pdo  = new bddpdo;
        $req  = "select dper_cod from dieu_perso where dper_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['dper_cod']);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \dieu_perso
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select dper_cod  from dieu_perso order by dper_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new dieu_perso;
            $temp->charge($result["dper_cod"]);
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
                    $req    = "select dper_cod  from dieu_perso where " . substr($name, 6) . " = ? order by dper_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new dieu_perso;
                        $temp->charge($result["dper_cod"]);
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
                    die('Unknown variable ' . substr($name,6));
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