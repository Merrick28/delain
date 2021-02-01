<?php
/**
 * includes/class.voie_magique.php
 */

/**
 * Class voie_magique
 *
 * Gère les objets BDD de la table voie_magique
 */
class voie_magique
{
    var $mvoie_cod;
    var $mvoie_libelle;
    var $mvoie_description;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de voie_magique
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from voie_magique where mvoie_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->mvoie_cod         = $result['mvoie_cod'];
        $this->mvoie_libelle     = $result['mvoie_libelle'];
        $this->mvoie_description = $result['mvoie_description'];
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
            $req  = "insert into voie_magique (
            mvoie_libelle,
            mvoie_description                        )
                    values
                    (
                        :mvoie_libelle,
                        :mvoie_description                        )
    returning mvoie_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":mvoie_libelle"     => $this->mvoie_libelle,
                ":mvoie_description" => $this->mvoie_description,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update voie_magique
                    set
            mvoie_libelle = :mvoie_libelle,
            mvoie_description = :mvoie_description                        where mvoie_cod = :mvoie_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":mvoie_cod"         => $this->mvoie_cod,
                ":mvoie_libelle"     => $this->mvoie_libelle,
                ":mvoie_description" => $this->mvoie_description,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \voie_magique
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select mvoie_cod  from voie_magique order by mvoie_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new voie_magique;
            $temp->charge($result["mvoie_cod"]);
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
                    $req    = "select mvoie_cod  from voie_magique where " . substr($name, 6) . " = ? order by mvoie_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new voie_magique;
                        $temp->charge($result["mvoie_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table voie_magique');
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