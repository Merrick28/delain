<?php
/**
 * includes/class.perso_titre.php
 */

/**
 * Class perso_titre
 *
 * Gère les objets BDD de la table perso_titre
 */
class perso_titre
{
    public $ptitre_cod;
    public $ptitre_perso_cod;
    public $ptitre_titre;
    public $ptitre_date;
    public $ptitre_type;

    public function __construct()
    {
        $this->ptitre_date = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de perso_titre
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    public function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_titre where ptitre_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch()) {
            return false;
        }
        $this->ptitre_cod       = $result['ptitre_cod'];
        $this->ptitre_perso_cod = $result['ptitre_perso_cod'];
        $this->ptitre_titre     = $result['ptitre_titre'];
        $this->ptitre_date      = $result['ptitre_date'];
        $this->ptitre_type      = $result['ptitre_type'];
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
            $req  = "insert into perso_titre (
            ptitre_perso_cod,
            ptitre_titre,
            ptitre_date,
            ptitre_type                        )
                    values
                    (
                        :ptitre_perso_cod,
                        :ptitre_titre,
                        :ptitre_date,
                        :ptitre_type                        )
    returning ptitre_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":ptitre_perso_cod" => $this->ptitre_perso_cod,
                                      ":ptitre_titre"     => $this->ptitre_titre,
                                      ":ptitre_date"      => $this->ptitre_date,
                                      ":ptitre_type"      => $this->ptitre_type,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else {
            $req  = "update perso_titre
                    set
            ptitre_perso_cod = :ptitre_perso_cod,
            ptitre_titre = :ptitre_titre,
            ptitre_date = :ptitre_date,
            ptitre_type = :ptitre_type                        where ptitre_cod = :ptitre_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":ptitre_cod"       => $this->ptitre_cod,
                                      ":ptitre_perso_cod" => $this->ptitre_perso_cod,
                                      ":ptitre_titre"     => $this->ptitre_titre,
                                      ":ptitre_date"      => $this->ptitre_date,
                                      ":ptitre_type"      => $this->ptitre_type,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return perso_titre[]
     */
    public function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select ptitre_cod  from perso_titre order by ptitre_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch()) {
            $temp = new perso_titre;
            $temp->charge($result["ptitre_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function getByPerso($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select ptitre_cod  from perso_titre where ptitre_perso_cod = :perso";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $perso_cod), $stmt);
        while ($result = $stmt->fetch()) {
            $temp = new perso_titre;
            $temp->charge($result["ptitre_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function __call($name, $arguments)
    {
        switch (substr($name, 0, 6)) {
            case 'getBy_':
                if (property_exists($this, substr($name, 6))) {
                    $retour = array();
                    $pdo    = new bddpdo;
                    $req    =
                        "select ptitre_cod  from perso_titre where " . substr($name, 6) . " = ? order by ptitre_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch()) {
                        $temp = new perso_titre;
                        $temp->charge($result["ptitre_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0) {
                        return false;
                    }
                    return $retour;
                } else {
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_titre');
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
