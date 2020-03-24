<?php
/**
 * includes/class.murs.php
 */

/**
 * Class murs
 *
 * Gère les objets BDD de la table murs
 */
class murs
{
    var $mur_pos_cod;
    var $mur_type      = 999;
    var $mur_tangible  = 'O';
    var $mur_creusable = 'N';
    var $mur_usure     = 1000;
    var $mur_richesse  = 100;

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
            $req  = "insert into murs (
            mur_type,
            mur_tangible,
            mur_creusable,
            mur_usure,
            mur_richesse                        )
                    values
                    (
                        :mur_type,
                        :mur_tangible,
                        :mur_creusable,
                        :mur_usure,
                        :mur_richesse                        )
    returning mur_pos_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":mur_type"      => $this->mur_type,
                                      ":mur_tangible"  => $this->mur_tangible,
                                      ":mur_creusable" => $this->mur_creusable,
                                      ":mur_usure"     => $this->mur_usure,
                                      ":mur_richesse"  => $this->mur_richesse,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update murs
                    set
            mur_type = :mur_type,
            mur_tangible = :mur_tangible,
            mur_creusable = :mur_creusable,
            mur_usure = :mur_usure,
            mur_richesse = :mur_richesse                        where mur_pos_cod = :mur_pos_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":mur_pos_cod"   => $this->mur_pos_cod,
                                      ":mur_type"      => $this->mur_type,
                                      ":mur_tangible"  => $this->mur_tangible,
                                      ":mur_creusable" => $this->mur_creusable,
                                      ":mur_usure"     => $this->mur_usure,
                                      ":mur_richesse"  => $this->mur_richesse,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de murs
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from murs where mur_pos_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->mur_pos_cod   = $result['mur_pos_cod'];
        $this->mur_type      = $result['mur_type'];
        $this->mur_tangible  = $result['mur_tangible'];
        $this->mur_creusable = $result['mur_creusable'];
        $this->mur_usure     = $result['mur_usure'];
        $this->mur_richesse  = $result['mur_richesse'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return murs
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select mur_pos_cod  from murs order by mur_pos_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new murs;
            $temp->charge($result["mur_pos_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPos($position)
    {
        $pdo  = new bddpdo;
        $req  = "select mur_pos_cod  from murs where mur_pos_cod = :pos";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pos" => $position), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['mur_pos_cod']);
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
                    $req    = "select mur_pos_cod  from murs where " . substr($name, 6) . " = ? order by mur_pos_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new murs;
                        $temp->charge($result["mur_pos_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table murs');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}