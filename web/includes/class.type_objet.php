<?php
/**
 * includes/class.type_objet.php
 */

/**
 * Class type_objet
 *
 * Gère les objets BDD de la table type_objet
 */
class type_objet
{
    var $tobj_cod;
    var $tobj_libelle;
    var $tobj_ident_comp_cod;
    var $tobj_chute;
    var $tobj_nettoyage;
    var $tobj_max_equip;
    var $tobj_identifie_auto;
    var $tobj_equipable;
    var $tobj_affichage_char = '+';
    var $tobj_degradation    = 0;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de type_objet
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from type_objet where tobj_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tobj_cod            = $result['tobj_cod'];
        $this->tobj_libelle        = $result['tobj_libelle'];
        $this->tobj_ident_comp_cod = $result['tobj_ident_comp_cod'];
        $this->tobj_chute          = $result['tobj_chute'];
        $this->tobj_nettoyage      = $result['tobj_nettoyage'];
        $this->tobj_max_equip      = $result['tobj_max_equip'];
        $this->tobj_identifie_auto = $result['tobj_identifie_auto'];
        $this->tobj_equipable      = $result['tobj_equipable'];
        $this->tobj_affichage_char = $result['tobj_affichage_char'];
        $this->tobj_degradation    = $result['tobj_degradation'];
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
            $req  = "insert into type_objet (
            tobj_libelle,
            tobj_ident_comp_cod,
            tobj_chute,
            tobj_nettoyage,
            tobj_max_equip,
            tobj_identifie_auto,
            tobj_equipable,
            tobj_affichage_char,
            tobj_degradation                        )
                    values
                    (
                        :tobj_libelle,
                        :tobj_ident_comp_cod,
                        :tobj_chute,
                        :tobj_nettoyage,
                        :tobj_max_equip,
                        :tobj_identifie_auto,
                        :tobj_equipable,
                        :tobj_affichage_char,
                        :tobj_degradation                        )
    returning tobj_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":tobj_libelle"        => $this->tobj_libelle,
                                      ":tobj_ident_comp_cod" => $this->tobj_ident_comp_cod,
                                      ":tobj_chute"          => $this->tobj_chute,
                                      ":tobj_nettoyage"      => $this->tobj_nettoyage,
                                      ":tobj_max_equip"      => $this->tobj_max_equip,
                                      ":tobj_identifie_auto" => $this->tobj_identifie_auto,
                                      ":tobj_equipable"      => $this->tobj_equipable,
                                      ":tobj_affichage_char" => $this->tobj_affichage_char,
                                      ":tobj_degradation"    => $this->tobj_degradation,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update type_objet
                    set
            tobj_libelle = :tobj_libelle,
            tobj_ident_comp_cod = :tobj_ident_comp_cod,
            tobj_chute = :tobj_chute,
            tobj_nettoyage = :tobj_nettoyage,
            tobj_max_equip = :tobj_max_equip,
            tobj_identifie_auto = :tobj_identifie_auto,
            tobj_equipable = :tobj_equipable,
            tobj_affichage_char = :tobj_affichage_char,
            tobj_degradation = :tobj_degradation                        where tobj_cod = :tobj_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":tobj_cod"            => $this->tobj_cod,
                                      ":tobj_libelle"        => $this->tobj_libelle,
                                      ":tobj_ident_comp_cod" => $this->tobj_ident_comp_cod,
                                      ":tobj_chute"          => $this->tobj_chute,
                                      ":tobj_nettoyage"      => $this->tobj_nettoyage,
                                      ":tobj_max_equip"      => $this->tobj_max_equip,
                                      ":tobj_identifie_auto" => $this->tobj_identifie_auto,
                                      ":tobj_equipable"      => $this->tobj_equipable,
                                      ":tobj_affichage_char" => $this->tobj_affichage_char,
                                      ":tobj_degradation"    => $this->tobj_degradation,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \type_objet
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select tobj_cod  from type_objet order by tobj_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new type_objet;
            $temp->charge($result["tobj_cod"]);
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
                    $req    = "select tobj_cod  from type_objet where " . substr($name, 6) . " = ? order by tobj_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new type_objet;
                        $temp->charge($result["tobj_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table type_objet');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}