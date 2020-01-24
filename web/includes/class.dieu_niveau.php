<?php
/**
 * includes/class.dieu_niveau.php
 */

/**
 * Class dieu_niveau
 *
 * Gère les objets BDD de la table dieu_niveau
 */
class dieu_niveau
{
    var $dniv_cod;
    var $dniv_dieu_cod;
    var $dniv_niveau;
    var $dniv_libelle;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de dieu_niveau
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from dieu_niveau where dniv_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->dniv_cod      = $result['dniv_cod'];
        $this->dniv_dieu_cod = $result['dniv_dieu_cod'];
        $this->dniv_niveau   = $result['dniv_niveau'];
        $this->dniv_libelle  = $result['dniv_libelle'];
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
            $req  = "insert into dieu_niveau (
            dniv_dieu_cod,
            dniv_niveau,
            dniv_libelle                        )
                    values
                    (
                        :dniv_dieu_cod,
                        :dniv_niveau,
                        :dniv_libelle                        )
    returning dniv_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":dniv_dieu_cod" => $this->dniv_dieu_cod,
                                      ":dniv_niveau"   => $this->dniv_niveau,
                                      ":dniv_libelle"  => $this->dniv_libelle,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update dieu_niveau
                    set
            dniv_dieu_cod = :dniv_dieu_cod,
            dniv_niveau = :dniv_niveau,
            dniv_libelle = :dniv_libelle                        where dniv_cod = :dniv_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":dniv_cod"      => $this->dniv_cod,
                                      ":dniv_dieu_cod" => $this->dniv_dieu_cod,
                                      ":dniv_niveau"   => $this->dniv_niveau,
                                      ":dniv_libelle"  => $this->dniv_libelle,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \dieu_niveau
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select dniv_cod  from dieu_niveau order by dniv_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new dieu_niveau;
            $temp->charge($result["dniv_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByNiveauDieu($niveau, $dieu_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    =
            "select dniv_cod  from dieu_niveau where dniv_niveau = :niveau and dniv_dieu_cod = :dieu order by dniv_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":niveau" => $niveau, ":dieu" => $dieu_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        } else
        {
            return $this->charge($result['dniv_cod']);
        }

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
                    $req    = "select dniv_cod  from dieu_niveau where " . substr($name, 6) . " = ? order by dniv_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new dieu_niveau;
                        $temp->charge($result["dniv_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table dieu_niveau');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}