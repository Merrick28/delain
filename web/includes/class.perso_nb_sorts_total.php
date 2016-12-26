<?php
/**
 * includes/class.perso_nb_sorts_total.php
 */

/**
 * Class perso_nb_sorts_total
 *
 * Gère les objets BDD de la table perso_nb_sorts_total
 */
class perso_nb_sorts_total
{
    var $pnbst_cod;
    var $pnbst_perso_cod;
    var $pnbst_sort_cod;
    var $pnbst_nombre;
    var $pnbst_date_dernier_lancer;

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
            $req  = "insert into perso_nb_sorts_total (
            pnbst_perso_cod,
            pnbst_sort_cod,
            pnbst_nombre,
            pnbst_date_dernier_lancer                        )
                    values
                    (
                        :pnbst_perso_cod,
                        :pnbst_sort_cod,
                        :pnbst_nombre,
                        :pnbst_date_dernier_lancer                        )
    returning pnbst_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pnbst_perso_cod" => $this->pnbst_perso_cod,
                ":pnbst_sort_cod" => $this->pnbst_sort_cod,
                ":pnbst_nombre" => $this->pnbst_nombre,
                ":pnbst_date_dernier_lancer" => $this->pnbst_date_dernier_lancer,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update perso_nb_sorts_total
                    set
            pnbst_perso_cod = :pnbst_perso_cod,
            pnbst_sort_cod = :pnbst_sort_cod,
            pnbst_nombre = :pnbst_nombre,
            pnbst_date_dernier_lancer = :pnbst_date_dernier_lancer                        where pnbst_cod = :pnbst_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pnbst_cod" => $this->pnbst_cod,
                ":pnbst_perso_cod" => $this->pnbst_perso_cod,
                ":pnbst_sort_cod" => $this->pnbst_sort_cod,
                ":pnbst_nombre" => $this->pnbst_nombre,
                ":pnbst_date_dernier_lancer" => $this->pnbst_date_dernier_lancer,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de perso_nb_sorts_total
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_nb_sorts_total where pnbst_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pnbst_cod                 = $result['pnbst_cod'];
        $this->pnbst_perso_cod           = $result['pnbst_perso_cod'];
        $this->pnbst_sort_cod            = $result['pnbst_sort_cod'];
        $this->pnbst_nombre              = $result['pnbst_nombre'];
        $this->pnbst_date_dernier_lancer = $result['pnbst_date_dernier_lancer'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_nb_sorts_total
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pnbst_cod  from perso_nb_sorts_total order by pnbst_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_nb_sorts_total;
            $temp->charge($result["pnbst_cod"]);
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
                    $req    = "select pnbst_cod  from perso_nb_sorts_total where " . substr($name, 6) . " = ? order by pnbst_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_nb_sorts_total;
                        $temp->charge($result["pnbst_cod"]);
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
                die('Unknown method.');
        }
    }
}