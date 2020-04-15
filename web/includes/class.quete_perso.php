<?php
/**
 * includes/class.quete_perso.php
 */

/**
 * Class quete_perso
 *
 * Gère les objets BDD de la table quete_perso
 */
class quete_perso
{
    var $pquete_cod;
    var $pquete_quete_cod;
    var $pquete_perso_cod;
    var $pquete_nombre;
    var $pquete_date_debut;
    var $pquete_date_fin;
    var $pquete_termine = 'N';
    var $pquete_param;
    var $pquete_param_texte;

    function __construct()
    {

        $this->pquete_date_debut = date('Y-m-d H:i:s');
        $this->pquete_date_fin   = date('Y-m-d H:i:s');
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
            $req  = "insert into quete_perso (
            pquete_quete_cod,
            pquete_perso_cod,
            pquete_nombre,
            pquete_date_debut,
            pquete_date_fin,
            pquete_termine,
            pquete_param,
            pquete_param_texte                        )
                    values
                    (
                        :pquete_quete_cod,
                        :pquete_perso_cod,
                        :pquete_nombre,
                        :pquete_date_debut,
                        :pquete_date_fin,
                        :pquete_termine,
                        :pquete_param,
                        :pquete_param_texte                        )
    returning pquete_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":pquete_quete_cod"   => $this->pquete_quete_cod,
                                      ":pquete_perso_cod"   => $this->pquete_perso_cod,
                                      ":pquete_nombre"      => $this->pquete_nombre,
                                      ":pquete_date_debut"  => $this->pquete_date_debut,
                                      ":pquete_date_fin"    => $this->pquete_date_fin,
                                      ":pquete_termine"     => $this->pquete_termine,
                                      ":pquete_param"       => $this->pquete_param,
                                      ":pquete_param_texte" => $this->pquete_param_texte,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update quete_perso
                    set
            pquete_quete_cod = :pquete_quete_cod,
            pquete_perso_cod = :pquete_perso_cod,
            pquete_nombre = :pquete_nombre,
            pquete_date_debut = :pquete_date_debut,
            pquete_date_fin = :pquete_date_fin,
            pquete_termine = :pquete_termine,
            pquete_param = :pquete_param,
            pquete_param_texte = :pquete_param_texte                        where pquete_cod = :pquete_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":pquete_cod"         => $this->pquete_cod,
                                      ":pquete_quete_cod"   => $this->pquete_quete_cod,
                                      ":pquete_perso_cod"   => $this->pquete_perso_cod,
                                      ":pquete_nombre"      => $this->pquete_nombre,
                                      ":pquete_date_debut"  => $this->pquete_date_debut,
                                      ":pquete_date_fin"    => $this->pquete_date_fin,
                                      ":pquete_termine"     => $this->pquete_termine,
                                      ":pquete_param"       => $this->pquete_param,
                                      ":pquete_param_texte" => $this->pquete_param_texte,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de quete_perso
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from quete_perso where pquete_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pquete_cod         = $result['pquete_cod'];
        $this->pquete_quete_cod   = $result['pquete_quete_cod'];
        $this->pquete_perso_cod   = $result['pquete_perso_cod'];
        $this->pquete_nombre      = $result['pquete_nombre'];
        $this->pquete_date_debut  = $result['pquete_date_debut'];
        $this->pquete_date_fin    = $result['pquete_date_fin'];
        $this->pquete_termine     = $result['pquete_termine'];
        $this->pquete_param       = $result['pquete_param'];
        $this->pquete_param_texte = $result['pquete_param_texte'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return quete_perso
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pquete_cod  from quete_perso order by pquete_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new quete_perso;
            $temp->charge($result["pquete_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPersoQuete($perso_cod, $quete)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pquete_cod  from quete_perso where pquete_perso_cod = :perso and pquete_quete_cod = :quete ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $perso_cod, ":quete" => $quete), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['pquete_cod']);
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
                    $req    =
                        "select pquete_cod  from quete_perso where " . substr($name, 6) . " = ? order by pquete_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new quete_perso;
                        $temp->charge($result["pquete_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table quete_perso');
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