<?php
/**
 * includes/class.perso_auberge.php
 */

/**
 * Class perso_auberge
 *
 * Gère les objets BDD de la table perso_auberge
 */
class perso_auberge
{
    var $paub_perso_cod;
    var $paub_lieu_cod;
    var $paub_nombre = 0;
    var $paub_visite = 'N';

    function __construct()
    {
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
            $req  = "insert into perso_auberge (
            paub_lieu_cod,
            paub_nombre,
            paub_visite                        )
                    values
                    (
                        :paub_lieu_cod,
                        :paub_nombre,
                        :paub_visite                        )
    returning paub_perso_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":paub_lieu_cod" => $this->paub_lieu_cod,
                                      ":paub_nombre"   => $this->paub_nombre,
                                      ":paub_visite"   => $this->paub_visite,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update perso_auberge
                    set
            paub_lieu_cod = :paub_lieu_cod,
            paub_nombre = :paub_nombre,
            paub_visite = :paub_visite                        where paub_perso_cod = :paub_perso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":paub_perso_cod" => $this->paub_perso_cod,
                                      ":paub_lieu_cod"  => $this->paub_lieu_cod,
                                      ":paub_nombre"    => $this->paub_nombre,
                                      ":paub_visite"    => $this->paub_visite,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de perso_auberge
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_auberge where paub_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->paub_perso_cod = $result['paub_perso_cod'];
        $this->paub_lieu_cod  = $result['paub_lieu_cod'];
        $this->paub_nombre    = $result['paub_nombre'];
        $this->paub_visite    = $result['paub_visite'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \perso_auberge
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select paub_perso_cod  from perso_auberge order by paub_perso_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_auberge;
            $temp->charge($result["paub_perso_cod"]);
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
                    $req    =
                        "select paub_perso_cod  from perso_auberge where " . substr($name, 6) . " = ? order by paub_perso_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_auberge;
                        $temp->charge($result["paub_perso_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_auberge');
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