<?php
/**
 * includes/class.transaction.php
 */

/**
 * Class transaction
 *
 * Gère les objets BDD de la table transaction
 */
class transaction
{
    var $tran_cod;
    var $tran_obj_cod;
    var $tran_vendeur;
    var $tran_acheteur;
    var $tran_nb_tours;
    var $tran_prix;
    var $tran_identifie;
    var $tran_quantite = 0;
    var $tran_type = 'PP';

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
            $req  = "insert into transaction (
            tran_obj_cod,
            tran_vendeur,
            tran_acheteur,
            tran_nb_tours,
            tran_prix,
            tran_identifie,
            tran_quantite,
            tran_type                        )
                    values
                    (
                        :tran_obj_cod,
                        :tran_vendeur,
                        :tran_acheteur,
                        :tran_nb_tours,
                        :tran_prix,
                        :tran_identifie,
                        :tran_quantite,
                        :tran_type                        )
    returning tran_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tran_obj_cod" => $this->tran_obj_cod,
                ":tran_vendeur" => $this->tran_vendeur,
                ":tran_acheteur" => $this->tran_acheteur,
                ":tran_nb_tours" => $this->tran_nb_tours,
                ":tran_prix" => $this->tran_prix,
                ":tran_identifie" => $this->tran_identifie,
                ":tran_quantite" => $this->tran_quantite,
                ":tran_type" => $this->tran_type,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update transaction
                    set
            tran_obj_cod = :tran_obj_cod,
            tran_vendeur = :tran_vendeur,
            tran_acheteur = :tran_acheteur,
            tran_nb_tours = :tran_nb_tours,
            tran_prix = :tran_prix,
            tran_identifie = :tran_identifie,
            tran_quantite = :tran_quantite,
            tran_type = :tran_type                        where tran_cod = :tran_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tran_cod" => $this->tran_cod,
                ":tran_obj_cod" => $this->tran_obj_cod,
                ":tran_vendeur" => $this->tran_vendeur,
                ":tran_acheteur" => $this->tran_acheteur,
                ":tran_nb_tours" => $this->tran_nb_tours,
                ":tran_prix" => $this->tran_prix,
                ":tran_identifie" => $this->tran_identifie,
                ":tran_quantite" => $this->tran_quantite,
                ":tran_type" => $this->tran_type,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de transaction
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from transaction where tran_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tran_cod       = $result['tran_cod'];
        $this->tran_obj_cod   = $result['tran_obj_cod'];
        $this->tran_vendeur   = $result['tran_vendeur'];
        $this->tran_acheteur  = $result['tran_acheteur'];
        $this->tran_nb_tours  = $result['tran_nb_tours'];
        $this->tran_prix      = $result['tran_prix'];
        $this->tran_identifie = $result['tran_identifie'];
        $this->tran_quantite  = $result['tran_quantite'];
        $this->tran_type      = $result['tran_type'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \transaction
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select tran_cod  from transaction order by tran_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new transaction;
            $temp->charge($result["tran_cod"]);
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
                    $req    = "select tran_cod  from transaction where " . substr($name, 6) . " = ? order by tran_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new transaction;
                        $temp->charge($result["tran_cod"]);
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