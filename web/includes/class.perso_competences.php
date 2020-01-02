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
     * @global bdd_mysql $pdo
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req = "insert into perso_competences (
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
                ":pcomp_perso_cod" => $this->pcomp_perso_cod,
                ":pcomp_pcomp_cod" => $this->pcomp_pcomp_cod,
                ":pcomp_modificateur" => $this->pcomp_modificateur,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update perso_competences
                    set
            pcomp_perso_cod = :pcomp_perso_cod,
            pcomp_pcomp_cod = :pcomp_pcomp_cod,
            pcomp_modificateur = :pcomp_modificateur                        where pcomp_cod = :pcomp_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pcomp_cod" => $this->pcomp_cod,
                ":pcomp_perso_cod" => $this->pcomp_perso_cod,
                ":pcomp_pcomp_cod" => $this->pcomp_pcomp_cod,
                ":pcomp_modificateur" => $this->pcomp_modificateur,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de perso_competences
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from perso_competences where pcomp_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pcomp_cod = $result['pcomp_cod'];
        $this->pcomp_perso_cod = $result['pcomp_perso_cod'];
        $this->pcomp_pcomp_cod = $result['pcomp_pcomp_cod'];
        $this->pcomp_modificateur = $result['pcomp_modificateur'];
        return true;
    }

    function getByPersoComp($perso, $comp)
    {
        $pdo = new bddpdo;
        $req = "select pcomp_cod from perso_competences where pcomp_perso_cod = ? and pcomp_pcomp_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso,$comp), $stmt);
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
        $pdo = new bddpdo;
        $req = "select pcomp_cod from perso_competences,competences
            where pcomp_perso_cod = ? 
            and pcomp_pcomp_cod = comp_cod
            and comp_typc_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso,$typecomp), $stmt);


        while ($result = $stmt->fetch())
        {
            $temp = new perso_competences();
            $temp->charge($result['pcomp_cod']);
            $return[] = $temp;
            unset($temp);
        }
        return $result;

    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_competences
     */
    function getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select pcomp_cod  from perso_competences order by pcomp_cod";
        $stmt = $pdo->query($req);
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
                    $pdo = new bddpdo;
                    $req = "select pcomp_cod  from perso_competences where " . substr($name, 6) . " = ? order by pcomp_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
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
                }
                else
                {
                    die('Unknown variable ' . substr($name,6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}