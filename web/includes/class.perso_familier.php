<?php
/**
 * includes/class.perso_familier.php
 */

/**
 * Class perso_familier
 *
 * Gère les objets BDD de la table perso_familier
 */
class perso_familier
{
    var $pfam_perso_cod;
    var $pfam_familier_cod;
    var $pfam_duree_vie;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_familier
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code,$pfam_familier_cod)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM perso_familier WHERE pfam_perso_cod = ? and pfam_familier_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code, $pfam_familier_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pfam_perso_cod    = $result['pfam_perso_cod'];
        $this->pfam_familier_cod = $result['pfam_familier_cod'];
        $this->pfam_duree_vie    = $result['pfam_duree_vie'];
        return true;
    }

    function getByPerso($perso_cod)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM perso_familier WHERE pfam_perso_cod = ? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pfam_perso_cod    = $result['pfam_perso_cod'];
        $this->pfam_familier_cod = $result['pfam_familier_cod'];
        $this->pfam_duree_vie    = $result['pfam_duree_vie'];
        return true;
    }

    function getByFamilier($perso_cod)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM perso_familier WHERE pfam_familier_cod = ? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pfam_perso_cod    = $result['pfam_perso_cod'];
        $this->pfam_familier_cod = $result['pfam_familier_cod'];
        $this->pfam_duree_vie    = $result['pfam_duree_vie'];
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
            $req
                  = "INSERT INTO perso_familier (
            pfam_familier_cod,
            pfam_duree_vie                        )
                    VALUES
                    (
                        :pfam_familier_cod,
                        :pfam_duree_vie                        )
    RETURNING pfam_perso_cod AS id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pfam_familier_cod" => $this->pfam_familier_cod,
                ":pfam_duree_vie"    => $this->pfam_duree_vie,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req
                  = "UPDATE perso_familier
                    SET
            pfam_familier_cod = :pfam_familier_cod,
            pfam_duree_vie = :pfam_duree_vie                        WHERE pfam_perso_cod = :pfam_perso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pfam_perso_cod"    => $this->pfam_perso_cod,
                ":pfam_familier_cod" => $this->pfam_familier_cod,
                ":pfam_duree_vie"    => $this->pfam_duree_vie,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return perso_familier
     */


    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "SELECT pfam_perso_cod,pfam_familier_cod  FROM perso_familier ORDER BY pfam_perso_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_familier;
            $temp->charge($result["pfam_perso_cod"],$result['pfam_familier_cod']);
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
                    $req    = "SELECT pfam_perso_cod,pfam_familier_cod  FROM perso_familier WHERE " . substr($name, 6) . " = ? ORDER BY pfam_perso_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_familier;
                        $temp->charge($result["pfam_perso_cod"],$result['pfam_familier_cod']);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_familier');
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