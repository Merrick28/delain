<?php
/**
 * includes/class.lieu_type.php
 */

/**
 * Class lieu_type
 *
 * Gère les objets BDD de la table lieu_type
 */
class lieu_type
{
    var $tlieu_cod;
    var $tlieu_libelle;
    var $tlieu_url;

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
            $req  = "insert into lieu_type (
            tlieu_libelle,
            tlieu_url                        )
                    values
                    (
                        :tlieu_libelle,
                        :tlieu_url                        )
    returning tlieu_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tlieu_libelle" => $this->tlieu_libelle,
                ":tlieu_url" => $this->tlieu_url,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update lieu_type
                    set
            tlieu_libelle = :tlieu_libelle,
            tlieu_url = :tlieu_url                        where tlieu_cod = :tlieu_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tlieu_cod" => $this->tlieu_cod,
                ":tlieu_libelle" => $this->tlieu_libelle,
                ":tlieu_url" => $this->tlieu_url,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de lieu_type
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from lieu_type where tlieu_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tlieu_cod     = $result['tlieu_cod'];
        $this->tlieu_libelle = $result['tlieu_libelle'];
        $this->tlieu_url     = $result['tlieu_url'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \lieu_type
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select tlieu_cod  from lieu_type order by tlieu_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new lieu_type;
            $temp->charge($result["tlieu_cod"]);
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
                    $req    = "select tlieu_cod  from lieu_type where " . substr($name, 6) . " = ? order by tlieu_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new lieu_type;
                        $temp->charge($result["tlieu_cod"]);
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