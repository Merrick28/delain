<?php
/**
 * includes/class.competences.php
 */

/**
 * Class competences
 *
 * Gère les objets BDD de la table competences
 */
class competences
{
    var $comp_cod;
    var $comp_typc_cod;
    var $comp_libelle;
    var $comp_modificateur;
    var $comp_connu = 'N';
    var $comp_nb_util_tour;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de competences
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from competences where comp_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->comp_cod          = $result['comp_cod'];
        $this->comp_typc_cod     = $result['comp_typc_cod'];
        $this->comp_libelle      = $result['comp_libelle'];
        $this->comp_modificateur = $result['comp_modificateur'];
        $this->comp_connu        = $result['comp_connu'];
        $this->comp_nb_util_tour = $result['comp_nb_util_tour'];
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
            $req  = "insert into competences (
            comp_typc_cod,
            comp_libelle,
            comp_modificateur,
            comp_connu,
            comp_nb_util_tour                        )
                    values
                    (
                        :comp_typc_cod,
                        :comp_libelle,
                        :comp_modificateur,
                        :comp_connu,
                        :comp_nb_util_tour                        )
    returning comp_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":comp_typc_cod"     => $this->comp_typc_cod,
                                      ":comp_libelle"      => $this->comp_libelle,
                                      ":comp_modificateur" => $this->comp_modificateur,
                                      ":comp_connu"        => $this->comp_connu,
                                      ":comp_nb_util_tour" => $this->comp_nb_util_tour,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update competences
                    set
            comp_typc_cod = :comp_typc_cod,
            comp_libelle = :comp_libelle,
            comp_modificateur = :comp_modificateur,
            comp_connu = :comp_connu,
            comp_nb_util_tour = :comp_nb_util_tour                        where comp_cod = :comp_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":comp_cod"          => $this->comp_cod,
                                      ":comp_typc_cod"     => $this->comp_typc_cod,
                                      ":comp_libelle"      => $this->comp_libelle,
                                      ":comp_modificateur" => $this->comp_modificateur,
                                      ":comp_connu"        => $this->comp_connu,
                                      ":comp_nb_util_tour" => $this->comp_nb_util_tour,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return competences[]
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select comp_cod  from competences order by comp_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new competences;
            $temp->charge($result["comp_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByTypeCompetence($typecompetence)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select comp_cod  from competences where comp_typc_cod = ? order by comp_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($typecompetence), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new competences;
            $temp->charge($result["comp_cod"]);
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
                    $req    = "select comp_cod  from competences where " . substr($name, 6) . " = ? order by comp_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new competences;
                        $temp->charge($result["comp_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table competences');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}