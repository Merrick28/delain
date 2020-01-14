<?php
/**
 * includes/class.ligne_evt.php
 */

/**
 * Class ligne_evt
 *
 * Gère les objets BDD de la table ligne_evt
 */
class ligne_evt
{
    public $levt_cod;
    public $levt_tevt_cod;
    public $levt_date;
    public $levt_type_per1 = 1;
    public $levt_perso_cod1;
    public $levt_type_per2;
    public $levt_perso_cod2;
    public $levt_texte;
    public $levt_lu        = 'N';
    public $levt_visible;
    public $levt_attaquant;
    public $levt_cible;
    public $levt_nombre;
    public $levt_parametres;

    public function __construct()
    {
        $this->levt_date = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de ligne_evt
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    public function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM ligne_evt WHERE levt_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch()) {
            return false;
        }
        $this->levt_cod        = $result['levt_cod'];
        $this->levt_tevt_cod   = $result['levt_tevt_cod'];
        $this->levt_date       = $result['levt_date'];
        $this->levt_type_per1  = $result['levt_type_per1'];
        $this->levt_perso_cod1 = $result['levt_perso_cod1'];
        $this->levt_type_per2  = $result['levt_type_per2'];
        $this->levt_perso_cod2 = $result['levt_perso_cod2'];
        $this->levt_texte      = $result['levt_texte'];
        $this->levt_lu         = $result['levt_lu'];
        $this->levt_visible    = $result['levt_visible'];
        $this->levt_attaquant  = $result['levt_attaquant'];
        $this->levt_cible      = $result['levt_cible'];
        $this->levt_nombre     = $result['levt_nombre'];
        $this->levt_parametres = $result['levt_parametres'];
        return true;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @global bdd_mysql $pdo
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     */
    public function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new) {
            $req
                  = "INSERT INTO ligne_evt (
            levt_tevt_cod,
            levt_date,
            levt_type_per1,
            levt_perso_cod1,
            levt_type_per2,
            levt_perso_cod2,
            levt_texte,
            levt_lu,
            levt_visible,
            levt_attaquant,
            levt_cible,
            levt_nombre,
            levt_parametres                        )
                    VALUES
                    (
                        :levt_tevt_cod,
                        :levt_date,
                        :levt_type_per1,
                        :levt_perso_cod1,
                        :levt_type_per2,
                        :levt_perso_cod2,
                        :levt_texte,
                        :levt_lu,
                        :levt_visible,
                        :levt_attaquant,
                        :levt_cible,
                        :levt_nombre,
                        :levt_parametres                        )
    RETURNING levt_cod AS id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":levt_tevt_cod"   => $this->levt_tevt_cod,
                ":levt_date"       => $this->levt_date,
                ":levt_type_per1"  => $this->levt_type_per1,
                ":levt_perso_cod1" => $this->levt_perso_cod1,
                ":levt_type_per2"  => $this->levt_type_per2,
                ":levt_perso_cod2" => $this->levt_perso_cod2,
                ":levt_texte"      => $this->levt_texte,
                ":levt_lu"         => $this->levt_lu,
                ":levt_visible"    => $this->levt_visible,
                ":levt_attaquant"  => $this->levt_attaquant,
                ":levt_cible"      => $this->levt_cible,
                ":levt_nombre"     => $this->levt_nombre,
                ":levt_parametres" => $this->levt_parametres,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else {
            $req
                  = "UPDATE ligne_evt
                    SET
            levt_tevt_cod = :levt_tevt_cod,
            levt_date = :levt_date,
            levt_type_per1 = :levt_type_per1,
            levt_perso_cod1 = :levt_perso_cod1,
            levt_type_per2 = :levt_type_per2,
            levt_perso_cod2 = :levt_perso_cod2,
            levt_texte = :levt_texte,
            levt_lu = :levt_lu,
            levt_visible = :levt_visible,
            levt_attaquant = :levt_attaquant,
            levt_cible = :levt_cible,
            levt_nombre = :levt_nombre,
            levt_parametres = :levt_parametres                        WHERE levt_cod = :levt_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":levt_cod"        => $this->levt_cod,
                ":levt_tevt_cod"   => $this->levt_tevt_cod,
                ":levt_date"       => $this->levt_date,
                ":levt_type_per1"  => $this->levt_type_per1,
                ":levt_perso_cod1" => $this->levt_perso_cod1,
                ":levt_type_per2"  => $this->levt_type_per2,
                ":levt_perso_cod2" => $this->levt_perso_cod2,
                ":levt_texte"      => $this->levt_texte,
                ":levt_lu"         => $this->levt_lu,
                ":levt_visible"    => $this->levt_visible,
                ":levt_attaquant"  => $this->levt_attaquant,
                ":levt_cible"      => $this->levt_cible,
                ":levt_nombre"     => $this->levt_nombre,
                ":levt_parametres" => $this->levt_parametres,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \ligne_evt
     */
    public function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "SELECT levt_cod  FROM ligne_evt ORDER BY levt_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch()) {
            $temp = new ligne_evt;
            $temp->charge($result["levt_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /**
     * @param $perso_cod
     * @return ligne_evt[]
     */
    public function getByPersoNonLu($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req
                = "SELECT levt_cod  FROM ligne_evt
          WHERE levt_perso_cod1 = ?
          AND levt_lu = 'N'
          ORDER BY levt_cod DESC";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($perso_cod), $stmt);
        while ($result = $stmt->fetch()) {
            $temp = new ligne_evt;
            $temp->charge($result["levt_cod"]);
            $tevt = new type_evt();
            $tevt->charge($temp->levt_tevt_cod);
            $temp->tevt = $tevt;
            // on prend les evts liés
            if (!is_null(trim($this->levt_attaquant))) {
                $perso_attaquant = new perso;
                if ($perso_attaquant->charge($this->levt_attaquant)) {
                    $temp->perso_attaquant = $perso_attaquant;
                } else {
                    $temp->perso_attaquant = "erreur";
                }
                unset($perso_attaquant);
            }
            if (!is_null(trim($this->levt_cible))) {
                $perso_cible = new perso;
                $perso_cible->charge($this->levt_cible);
                $temp->perso_cible = $perso_cible;
                unset($perso_cible);
            }

            $retour[]   = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function marquePersoLu($perso_cod)
    {
        $pdo  = new bddpdo;
        $req  = "UPDATE ligne_evt SET levt_lu = 'O' WHERE levt_perso_cod1 = ? AND levt_lu = 'N'";
        $stmt = $pdo->prepare($req);
        $pdo->execute(array($perso_cod), $stmt);
        return true;
    }

    public function __call($name, $arguments)
    {
        switch (substr($name, 0, 6)) {
            case 'getBy_':
                if (property_exists($this, substr($name, 6))) {
                    $retour = array();
                    $pdo    = new bddpdo;
                    $req    = "SELECT levt_cod  FROM ligne_evt WHERE " . substr($name, 6) . " = ? ORDER BY levt_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch()) {
                        $temp = new ligne_evt;
                        $temp->charge($result["levt_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0) {
                        return false;
                    }
                    return $retour;
                } else {
                    die('Unknown variable ' . substr($name, 6) . ' in table ligne_evt');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}
