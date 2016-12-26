<?php
/**
 * includes/class.perso_compte.php
 */

/**
 * Class perso_compte
 *
 * Gère les objets BDD de la table perso_compte
 */
class perso_compte
{
    var $pcompt_cod;
    var $pcompt_compt_cod;
    var $pcompt_perso_cod;
    var $pcompt_date_attachement;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_compte
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM perso_compte WHERE pcompt_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pcompt_cod              = $result['pcompt_cod'];
        $this->pcompt_compt_cod        = $result['pcompt_compt_cod'];
        $this->pcompt_perso_cod        = $result['pcompt_perso_cod'];
        $this->pcompt_date_attachement = $result['pcompt_date_attachement'];
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
            $req
                  = "INSERT INTO perso_compte (
            pcompt_compt_cod,
            pcompt_perso_cod,
            pcompt_date_attachement                        )
                    VALUES
                    (
                        :pcompt_compt_cod,
                        :pcompt_perso_cod,
                        :pcompt_date_attachement                        )
    RETURNING pcompt_cod AS id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pcompt_compt_cod"        => $this->pcompt_compt_cod,
                ":pcompt_perso_cod"        => $this->pcompt_perso_cod,
                ":pcompt_date_attachement" => $this->pcompt_date_attachement,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req
                  = "UPDATE perso_compte
                    SET
            pcompt_compt_cod = :pcompt_compt_cod,
            pcompt_perso_cod = :pcompt_perso_cod,
            pcompt_date_attachement = :pcompt_date_attachement                        WHERE pcompt_cod = :pcompt_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pcompt_cod"              => $this->pcompt_cod,
                ":pcompt_compt_cod"        => $this->pcompt_compt_cod,
                ":pcompt_perso_cod"        => $this->pcompt_perso_cod,
                ":pcompt_date_attachement" => $this->pcompt_date_attachement,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_compte
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "SELECT pcompt_cod  FROM perso_compte ORDER BY pcompt_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_compte;
            $temp->charge($result["pcompt_cod"]);
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
                    $req    = "SELECT pcompt_cod  FROM perso_compte WHERE " . substr($name, 6) . " = ? ORDER BY pcompt_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_compte;
                        $temp->charge($result["pcompt_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_compte');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}