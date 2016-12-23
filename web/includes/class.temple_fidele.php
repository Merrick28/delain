<?php
/**
 * includes/class.temple_fidele.php
 */

/**
 * Class temple_fidele
 *
 * Gère les objets BDD de la table temple_fidele
 */
class temple_fidele
{
    var $tfid_lieu_cod;
    var $tfid_perso_cod;

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
            $req  = "insert into temple_fidele (
            tfid_perso_cod                        )
                    values
                    (
                        :tfid_perso_cod                        )
    returning tfid_lieu_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tfid_perso_cod" => $this->tfid_perso_cod,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update temple_fidele
                    set
            tfid_perso_cod = :tfid_perso_cod                        where tfid_lieu_cod = :tfid_lieu_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tfid_lieu_cod" => $this->tfid_lieu_cod,
                ":tfid_perso_cod" => $this->tfid_perso_cod,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de temple_fidele
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from temple_fidele where tfid_lieu_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tfid_lieu_cod  = $result['tfid_lieu_cod'];
        $this->tfid_perso_cod = $result['tfid_perso_cod'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \temple_fidele
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select tfid_lieu_cod  from temple_fidele order by tfid_lieu_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new temple_fidele;
            $temp->charge($result["tfid_lieu_cod"]);
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
                    $req    = "select tfid_lieu_cod  from temple_fidele where " . substr($name, 6) . " = ? order by tfid_lieu_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new temple_fidele;
                        $temp->charge($result["tfid_lieu_cod"]);
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
                    die('Unknown variable.');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}