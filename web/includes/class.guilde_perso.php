<?php
/**
 * includes/class.guilde_perso.php
 */

/**
 * Class guilde_perso
 *
 * Gère les objets BDD de la table guilde_perso
 */
class guilde_perso
{
    var $pguilde_cod;
    var $pguilde_guilde_cod;
    var $pguilde_perso_cod;
    var $pguilde_rang_cod;
    var $pguilde_valide        = 'N';
    var $pguilde_message       = 'O';
    var $pguilde_solde         = 0;
    var $pguilde_mode_milice   = 1;
    var $pguilde_dcreat;
    var $pguilde_meta_noir     = 'N';
    var $pguilde_meta_milice   = 'N';
    var $pguilde_meta_caravane = 'N';

    function __construct()
    {

        $this->pguilde_dcreat = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de guilde_perso
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from guilde_perso where pguilde_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pguilde_cod           = $result['pguilde_cod'];
        $this->pguilde_guilde_cod    = $result['pguilde_guilde_cod'];
        $this->pguilde_perso_cod     = $result['pguilde_perso_cod'];
        $this->pguilde_rang_cod      = $result['pguilde_rang_cod'];
        $this->pguilde_valide        = $result['pguilde_valide'];
        $this->pguilde_message       = $result['pguilde_message'];
        $this->pguilde_solde         = $result['pguilde_solde'];
        $this->pguilde_mode_milice   = $result['pguilde_mode_milice'];
        $this->pguilde_dcreat        = $result['pguilde_dcreat'];
        $this->pguilde_meta_noir     = $result['pguilde_meta_noir'];
        $this->pguilde_meta_milice   = $result['pguilde_meta_milice'];
        $this->pguilde_meta_caravane = $result['pguilde_meta_caravane'];
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
                  = "insert into guilde_perso (
            pguilde_guilde_cod,
            pguilde_perso_cod,
            pguilde_rang_cod,
            pguilde_valide,
            pguilde_message,
            pguilde_solde,
            pguilde_mode_milice,
            pguilde_dcreat,
            pguilde_meta_noir,
            pguilde_meta_milice,
            pguilde_meta_caravane                        )
                    values
                    (
                        :pguilde_guilde_cod,
                        :pguilde_perso_cod,
                        :pguilde_rang_cod,
                        :pguilde_valide,
                        :pguilde_message,
                        :pguilde_solde,
                        :pguilde_mode_milice,
                        :pguilde_dcreat,
                        :pguilde_meta_noir,
                        :pguilde_meta_milice,
                        :pguilde_meta_caravane                        )
    returning pguilde_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pguilde_guilde_cod"    => $this->pguilde_guilde_cod,
                ":pguilde_perso_cod"     => $this->pguilde_perso_cod,
                ":pguilde_rang_cod"      => $this->pguilde_rang_cod,
                ":pguilde_valide"        => $this->pguilde_valide,
                ":pguilde_message"       => $this->pguilde_message,
                ":pguilde_solde"         => $this->pguilde_solde,
                ":pguilde_mode_milice"   => $this->pguilde_mode_milice,
                ":pguilde_dcreat"        => $this->pguilde_dcreat,
                ":pguilde_meta_noir"     => $this->pguilde_meta_noir,
                ":pguilde_meta_milice"   => $this->pguilde_meta_milice,
                ":pguilde_meta_caravane" => $this->pguilde_meta_caravane,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req
                  = "update guilde_perso
                    set
            pguilde_guilde_cod = :pguilde_guilde_cod,
            pguilde_perso_cod = :pguilde_perso_cod,
            pguilde_rang_cod = :pguilde_rang_cod,
            pguilde_valide = :pguilde_valide,
            pguilde_message = :pguilde_message,
            pguilde_solde = :pguilde_solde,
            pguilde_mode_milice = :pguilde_mode_milice,
            pguilde_dcreat = :pguilde_dcreat,
            pguilde_meta_noir = :pguilde_meta_noir,
            pguilde_meta_milice = :pguilde_meta_milice,
            pguilde_meta_caravane = :pguilde_meta_caravane                        where pguilde_cod = :pguilde_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pguilde_cod"           => $this->pguilde_cod,
                ":pguilde_guilde_cod"    => $this->pguilde_guilde_cod,
                ":pguilde_perso_cod"     => $this->pguilde_perso_cod,
                ":pguilde_rang_cod"      => $this->pguilde_rang_cod,
                ":pguilde_valide"        => $this->pguilde_valide,
                ":pguilde_message"       => $this->pguilde_message,
                ":pguilde_solde"         => $this->pguilde_solde,
                ":pguilde_mode_milice"   => $this->pguilde_mode_milice,
                ":pguilde_dcreat"        => $this->pguilde_dcreat,
                ":pguilde_meta_noir"     => $this->pguilde_meta_noir,
                ":pguilde_meta_milice"   => $this->pguilde_meta_milice,
                ":pguilde_meta_caravane" => $this->pguilde_meta_caravane,
            ), $stmt);
        }
    }

    function get_by_perso($perso)
    {
        $pdo    = new bddpdo;
        $req = "select pguilde_cod from guilde_perso 
            where pguilde_perso_cod = :perso and pguilde_valide = 'O'";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $perso),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['pguilde_cod']);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \guilde_perso
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pguilde_cod  from guilde_perso order by pguilde_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new guilde_perso;
            $temp->charge($result["pguilde_cod"]);
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
                    $req    = "select pguilde_cod  from guilde_perso where " . substr($name, 6) . " = ? order by pguilde_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new guilde_perso;
                        $temp->charge($result["pguilde_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table guilde_perso');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}