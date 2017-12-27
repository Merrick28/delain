<?php
/**
 * includes/class.faq_type.php
 */

/**
 * Class faq_type
 *
 * Gère les objets BDD de la table faq_type
 */
class faq_type
{
    var $tfaq_cod;
    var $tfaq_libelle;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de faq_type
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from faq_type where tfaq_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tfaq_cod = $result['tfaq_cod'];
        $this->tfaq_libelle = $result['tfaq_libelle'];
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
            $req = "insert into faq_type (
            tfaq_libelle                        )
                    values
                    (
                        :tfaq_libelle                        )
    returning tfaq_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tfaq_libelle" => $this->tfaq_libelle,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req = "update faq_type
                    set
            tfaq_libelle = :tfaq_libelle                        where tfaq_cod = :tfaq_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tfaq_cod"     => $this->tfaq_cod,
                ":tfaq_libelle" => $this->tfaq_libelle,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \faq_type
     */
    function getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select tfaq_cod  from faq_type order by tfaq_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new faq_type;
            $temp->charge($result["tfaq_cod"]);
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
                    $req = "select tfaq_cod  from faq_type where " . substr($name, 6) . " = ? order by tfaq_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new faq_type;
                        $temp->charge($result["tfaq_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table faq_type');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}