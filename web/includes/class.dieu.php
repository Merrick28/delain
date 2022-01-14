<?php
/**
 * includes/class.dieu.php
 */

/**
 * Class dieu
 *
 * Gère les objets BDD de la table dieu
 */
class dieu
{
    var $dieu_cod;
    var $dieu_nom;
    var $dieu_description;
    var $dieu_pouvoir = 0;
    var $dieu_ceremonie;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de dieu
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from dieu where dieu_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->dieu_cod         = $result['dieu_cod'];
        $this->dieu_nom         = $result['dieu_nom'];
        $this->dieu_description = $result['dieu_description'];
        $this->dieu_pouvoir     = $result['dieu_pouvoir'];
        $this->dieu_ceremonie   = $result['dieu_ceremonie'];
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
            $req  = "insert into dieu (
            dieu_nom,
            dieu_description,
            dieu_pouvoir,
            dieu_ceremonie                        )
                    values
                    (
                        :dieu_nom,
                        :dieu_description,
                        :dieu_pouvoir,
                        :dieu_ceremonie                        )
    returning dieu_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":dieu_nom"         => $this->dieu_nom,
                                      ":dieu_description" => $this->dieu_description,
                                      ":dieu_pouvoir"     => $this->dieu_pouvoir,
                                      ":dieu_ceremonie"   => $this->dieu_ceremonie,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update dieu
                    set
            dieu_nom = :dieu_nom,
            dieu_description = :dieu_description,
            dieu_pouvoir = :dieu_pouvoir,
            dieu_ceremonie = :dieu_ceremonie                        where dieu_cod = :dieu_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":dieu_cod"         => $this->dieu_cod,
                                      ":dieu_nom"         => $this->dieu_nom,
                                      ":dieu_description" => $this->dieu_description,
                                      ":dieu_pouvoir"     => $this->dieu_pouvoir,
                                      ":dieu_ceremonie"   => $this->dieu_ceremonie,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \dieu
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select dieu_cod  from dieu order by dieu_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new dieu;
            $temp->charge($result["dieu_cod"]);
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
                    $req    = "select dieu_cod  from dieu where " . substr($name, 6) . " = ? order by dieu_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new dieu;
                        $temp->charge($result["dieu_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table dieu');
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