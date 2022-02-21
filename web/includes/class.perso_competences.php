<?php
/**
 * includes/class.perso_competences.php
 */

/**
 * Class perso_competences
 *
 * Gère les objets BDD de la table perso_competences
 */
class perso_competences
{
    var $pcomp_cod;
    var $pcomp_perso_cod;
    var $pcomp_pcomp_cod;
    var $pcomp_modificateur;

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
            $req  = "insert into perso_competences (
            pcomp_perso_cod,
            pcomp_pcomp_cod,
            pcomp_modificateur                        )
                    values
                    (
                        :pcomp_perso_cod,
                        :pcomp_pcomp_cod,
                        :pcomp_modificateur                        )
    returning pcomp_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":pcomp_perso_cod"    => $this->pcomp_perso_cod,
                                      ":pcomp_pcomp_cod"    => $this->pcomp_pcomp_cod,
                                      ":pcomp_modificateur" => $this->pcomp_modificateur,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update perso_competences
                    set
            pcomp_perso_cod = :pcomp_perso_cod,
            pcomp_pcomp_cod = :pcomp_pcomp_cod,
            pcomp_modificateur = :pcomp_modificateur                        where pcomp_cod = :pcomp_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":pcomp_cod"          => $this->pcomp_cod,
                                      ":pcomp_perso_cod"    => $this->pcomp_perso_cod,
                                      ":pcomp_pcomp_cod"    => $this->pcomp_pcomp_cod,
                                      ":pcomp_modificateur" => $this->pcomp_modificateur,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de perso_competences
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_competences where pcomp_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pcomp_cod          = $result['pcomp_cod'];
        $this->pcomp_perso_cod    = $result['pcomp_perso_cod'];
        $this->pcomp_pcomp_cod    = $result['pcomp_pcomp_cod'];
        $this->pcomp_modificateur = $result['pcomp_modificateur'];
        return true;
    }

    function getByPersoComp($perso, $comp)
    {
        $pdo  = new bddpdo;
        $req  = "select pcomp_cod from perso_competences where pcomp_perso_cod = ? and pcomp_pcomp_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso, $comp), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['pcomp_cod']);

    }

    /**
     * Reccherche la compétence d'un perso indépendement du niveau de celle-ci par exemple
     * Si Forgeamage (niv. 2) est la compétence recherchée,
     * => un perso qui possède Forgeamage (niv. 3) aura le niveau
     * => avec seulement Forgeamage cela sera insuffisant
     * @param $perso
     * @param $comp
     * @return bool
     * @throws Exception
     */
    function getByPersoCompetenceNiveau($perso, $comp)
    {

        // Attaque foudroyante : 25, 61, 62
        // Coup de grâce : 66, 67, 68
        // Bout portant : 72, 73, 74
        // Tir précis : 75,76,77
        // Vol : 84, 85, 86
        // Enluminure : 91, 92, 93
        // Alchimie : 97, 100, 101
        // Forgemage : 88, 102, 103
        // Pour la compétence de plus haut niveau, il faut la posseder donc revient à une compétence de base
        $comp_level = [
            25 => "25, 61, 62",     // Attaque foudroyante : 25, 61, 62
            61 => "61, 62",
            66 => "66, 67, 68",     // Coup de grâce : 66, 67, 68
            67 => "67, 68",
            72 => "72, 73, 74",     // Bout portant : 72, 73, 74
            73 => "73, 74",
            75 => "75, 76, 77",     // Tir précis : 75,76,77
            76 => "76, 77",
            84 => "84, 85, 86",     // Vol : 84, 85, 86
            85 => "85, 86",
            91 => "91, 92, 93",     // Enluminure : 91, 92, 93
            92 => "92, 93",
            97 => "97, 100, 101",    // Alchimie : 97, 100, 101
            100=> "100, 101",
            88 => "88, 102, 103",   // Forgemage : 88, 102, 103
            102=> "102, 103"
        ];
        //vérifions qu'il s'agit d'un compétence à niveau, sinon on charge la compétence de base.
        if (! isset($comp_level[$comp])) {
            $competences = $comp ;
        } else {
            $competences = $comp_level[$comp] ;
        }


        $pdo  = new bddpdo;
        $req  = "select pcomp_cod from perso_competences where pcomp_perso_cod = ? and pcomp_pcomp_cod in ({$competences}) order by pcomp_pcomp_cod desc limit 1";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['pcomp_cod']);
    }

    /**
     * @param $perso
     * @param $typecomp
     * @return perso_competences[]
     * @throws Exception
     */
    function getByPersoTypeComp($perso, $typecomp)
    {
        $return = array();
        $pdo    = new bddpdo;
        $req    = "select pcomp_cod, competences from perso_competences,competences
            where pcomp_perso_cod = ? 
            and pcomp_pcomp_cod = comp_cod
            and comp_typc_cod = ? order by competences ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($perso, $typecomp), $stmt);


        while ($result = $stmt->fetch())
        {

            $temp = new perso_competences();
            $temp->charge($result['pcomp_cod']);
            $comp = new competences();
            $comp->charge($temp->pcomp_pcomp_cod);
            $temp->competence = $comp;
            unset($comp);
            $return[] = $temp;
            unset($temp);
        }
        return $return;

    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \perso_competences
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pcomp_cod  from perso_competences order by pcomp_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_competences;
            $temp->charge($result["pcomp_cod"]);
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
                        "select pcomp_cod  from perso_competences where " . substr($name, 6) . " = ? order by pcomp_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_competences;
                        $temp->charge($result["pcomp_cod"]);
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
                    die('Unknown variable ' . substr($name, 6));
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