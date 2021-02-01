<?php
/**
 * includes/class.or_position.php
 */

/**
 * Class or_position
 *
 * Gère les objets BDD de la table or_position
 */
class or_position
{
    var $por_cod;
    var $por_pos_cod;
    var $por_qte;
    var $por_palpable = 'O';

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
            $req  = "insert into or_position (
            por_pos_cod,
            por_qte,
            por_palpable                        )
                    values
                    (
                        :por_pos_cod,
                        :por_qte,
                        :por_palpable                        )
    returning por_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":por_pos_cod" => $this->por_pos_cod,
                ":por_qte" => $this->por_qte,
                ":por_palpable" => $this->por_palpable,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update or_position
                    set
            por_pos_cod = :por_pos_cod,
            por_qte = :por_qte,
            por_palpable = :por_palpable                        where por_cod = :por_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":por_cod" => $this->por_cod,
                ":por_pos_cod" => $this->por_pos_cod,
                ":por_qte" => $this->por_qte,
                ":por_palpable" => $this->por_palpable,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de or_position
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from or_position where por_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->por_cod      = $result['por_cod'];
        $this->por_pos_cod  = $result['por_pos_cod'];
        $this->por_qte      = $result['por_qte'];
        $this->por_palpable = $result['por_palpable'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \or_position
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select por_cod  from or_position order by por_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new or_position;
            $temp->charge($result["por_cod"]);
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
                    $req    = "select por_cod  from or_position where " . substr($name, 6) . " = ? order by por_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new or_position;
                        $temp->charge($result["por_cod"]);
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