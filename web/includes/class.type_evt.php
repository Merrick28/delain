<?php
/**
 * includes/class.type_evt.php
 */

/**
 * Class type_evt
 *
 * Gère les objets BDD de la table type_evt
 */
class type_evt
{
    var $tevt_cod;
    var $tevt_libelle;
    var $tevt_texte;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de type_evt
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM type_evt WHERE tevt_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tevt_cod     = $result['tevt_cod'];
        $this->tevt_libelle = $result['tevt_libelle'];
        $this->tevt_texte   = $result['tevt_texte'];
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
                  = "INSERT INTO type_evt (
            tevt_libelle,
            tevt_texte                        )
                    VALUES
                    (
                        :tevt_libelle,
                        :tevt_texte                        )
    RETURNING tevt_cod AS id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tevt_libelle" => $this->tevt_libelle,
                ":tevt_texte"   => $this->tevt_texte,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req
                  = "UPDATE type_evt
                    SET
            tevt_libelle = :tevt_libelle,
            tevt_texte = :tevt_texte                        WHERE tevt_cod = :tevt_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":tevt_cod"     => $this->tevt_cod,
                ":tevt_libelle" => $this->tevt_libelle,
                ":tevt_texte"   => $this->tevt_texte,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \type_evt
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "SELECT tevt_cod  FROM type_evt ORDER BY tevt_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new type_evt;
            $temp->charge($result["tevt_cod"]);
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
                    $req    = "SELECT tevt_cod  FROM type_evt WHERE " . substr($name, 6) . " = ? ORDER BY tevt_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new type_evt;
                        $temp->charge($result["tevt_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table type_evt');
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