<?php
/**
 * includes/class.faq.php
 */

/**
 * Class faq
 *
 * Gère les objets BDD de la table faq
 */
class faq
{
    var $faq_cod;
    var $faq_tfaq_cod;
    var $faq_question;
    var $faq_reponse;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de faq
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from faq where faq_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->faq_cod = $result['faq_cod'];
        $this->faq_tfaq_cod = $result['faq_tfaq_cod'];
        $this->faq_question = $result['faq_question'];
        $this->faq_reponse = $result['faq_reponse'];
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
            $req = "insert into faq (
            faq_tfaq_cod,
            faq_question,
            faq_reponse                        )
                    values
                    (
                        :faq_tfaq_cod,
                        :faq_question,
                        :faq_reponse                        )
    returning faq_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":faq_tfaq_cod" => $this->faq_tfaq_cod,
                ":faq_question" => $this->faq_question,
                ":faq_reponse"  => $this->faq_reponse,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req = "update faq
                    set
            faq_tfaq_cod = :faq_tfaq_cod,
            faq_question = :faq_question,
            faq_reponse = :faq_reponse                        where faq_cod = :faq_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":faq_cod"      => $this->faq_cod,
                ":faq_tfaq_cod" => $this->faq_tfaq_cod,
                ":faq_question" => $this->faq_question,
                ":faq_reponse"  => $this->faq_reponse,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \faq
     */
    function getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select faq_cod  from faq order by faq_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new faq;
            $temp->charge($result["faq_cod"]);
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
                    $req = "select faq_cod  from faq where " . substr($name, 6) . " = ? order by faq_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new faq;
                        $temp->charge($result["faq_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table faq');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}