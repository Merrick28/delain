<?php
/**
 * includes/class.mode_combat.php
 */

/**
 * Class mode_combat
 *
 * Gère les objets BDD de la table mode_combat
 */
class mode_combat
{
    var $mcom_cod;
    var $mcom_nom;
    var $mcom_modif_att = 1;
    var $mcom_modif_def = 1;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de mode_combat
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from mode_combat where mcom_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->mcom_cod = $result['mcom_cod'];
        $this->mcom_nom = $result['mcom_nom'];
        $this->mcom_modif_att = $result['mcom_modif_att'];
        $this->mcom_modif_def = $result['mcom_modif_def'];
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
            $req = "insert into mode_combat (
            mcom_nom,
            mcom_modif_att,
            mcom_modif_def                        )
                    values
                    (
                        :mcom_nom,
                        :mcom_modif_att,
                        :mcom_modif_def                        )
    returning mcom_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":mcom_nom"       => $this->mcom_nom,
                ":mcom_modif_att" => $this->mcom_modif_att,
                ":mcom_modif_def" => $this->mcom_modif_def,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req = "update mode_combat
                    set
            mcom_nom = :mcom_nom,
            mcom_modif_att = :mcom_modif_att,
            mcom_modif_def = :mcom_modif_def                        where mcom_cod = :mcom_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":mcom_cod"       => $this->mcom_cod,
                ":mcom_nom"       => $this->mcom_nom,
                ":mcom_modif_att" => $this->mcom_modif_att,
                ":mcom_modif_def" => $this->mcom_modif_def,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \mode_combat
     */
    function getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select mcom_cod  from mode_combat order by mcom_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new mode_combat;
            $temp->charge($result["mcom_cod"]);
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
                    $req = "select mcom_cod  from mode_combat where " . substr($name, 6) . " = ? order by mcom_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new mode_combat;
                        $temp->charge($result["mcom_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table mode_combat');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}