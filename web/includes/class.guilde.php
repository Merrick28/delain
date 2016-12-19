<?php

/**
 * includes/class.guilde.php
 */

/**
 * Class guilde
 *
 * Gère les objets BDD de la table guilde
 */
class guilde
{

    var $guilde_cod;
    var $guilde_nom;
    var $guilde_description;
    var $guilde_valide        = 'O';
    var $guilde_modif         = 0;
    var $guilde_modif_noir    = 0;
    var $guilde_meta_milice   = 'N';
    var $guilde_meta_noir     = 'N';
    var $guilde_meta_caravane = 'N';

    function __construct()
    {
        
    }

    /**
     * Charge dans la classe un enregistrement de guilde
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo    = new bddpdo;
        $req    = "select * from guilde where guilde_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->guilde_cod           = $result['guilde_cod'];
        $this->guilde_nom           = $result['guilde_nom'];
        $this->guilde_description   = $result['guilde_description'];
        $this->guilde_valide        = $result['guilde_valide'];
        $this->guilde_modif         = $result['guilde_modif'];
        $this->guilde_modif_noir    = $result['guilde_modif_noir'];
        $this->guilde_meta_milice   = $result['guilde_meta_milice'];
        $this->guilde_meta_noir     = $result['guilde_meta_noir'];
        $this->guilde_meta_caravane = $result['guilde_meta_caravane'];
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
            $req  = "insert into guilde (
            guilde_nom,
            guilde_description,
            guilde_valide,
            guilde_modif,
            guilde_modif_noir,
            guilde_meta_milice,
            guilde_meta_noir,
            guilde_meta_caravane                        )
                    values
                    (
                        :guilde_nom,
                        :guilde_description,
                        :guilde_valide,
                        :guilde_modif,
                        :guilde_modif_noir,
                        :guilde_meta_milice,
                        :guilde_meta_noir,
                        :guilde_meta_caravane                        )
    returning guilde_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":guilde_nom"           => $this->guilde_nom,
               ":guilde_description"   => $this->guilde_description,
               ":guilde_valide"        => $this->guilde_valide,
               ":guilde_modif"         => $this->guilde_modif,
               ":guilde_modif_noir"    => $this->guilde_modif_noir,
               ":guilde_meta_milice"   => $this->guilde_meta_milice,
               ":guilde_meta_noir"     => $this->guilde_meta_noir,
               ":guilde_meta_caravane" => $this->guilde_meta_caravane,
               ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update guilde
                    set
            guilde_nom = :guilde_nom,
            guilde_description = :guilde_description,
            guilde_valide = :guilde_valide,
            guilde_modif = :guilde_modif,
            guilde_modif_noir = :guilde_modif_noir,
            guilde_meta_milice = :guilde_meta_milice,
            guilde_meta_noir = :guilde_meta_noir,
            guilde_meta_caravane = :guilde_meta_caravane                        where guilde_cod = :guilde_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":guilde_cod"           => $this->guilde_cod,
               ":guilde_nom"           => $this->guilde_nom,
               ":guilde_description"   => $this->guilde_description,
               ":guilde_valide"        => $this->guilde_valide,
               ":guilde_modif"         => $this->guilde_modif,
               ":guilde_modif_noir"    => $this->guilde_modif_noir,
               ":guilde_meta_milice"   => $this->guilde_meta_milice,
               ":guilde_meta_noir"     => $this->guilde_meta_noir,
               ":guilde_meta_caravane" => $this->guilde_meta_caravane,
               ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \guilde
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select guilde_cod  from guilde order by guilde_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp     = new guilde;
            $temp->charge($result["guilde_cod"]);
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
                    $req    = "select guilde_cod  from guilde where " . substr($name, 6) . " = ? order by guilde_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp     = new guilde;
                        $temp->charge($result["guilde_cod"]);
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
