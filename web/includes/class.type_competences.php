<?php
/**
 * includes/class.type_competences.php
 */

/**
 * Class type_competences
 *
 * Gère les objets BDD de la table type_competences
 */
class type_competences
{
    var $typc_cod;
    var $typc_libelle;
    var $typc_description;
    var $typc_mod_for;
    var $typc_mod_dex;
    var $typc_mod_int;
    var $typc_mod_con;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de type_competences
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from type_competences where typc_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->typc_cod         = $result['typc_cod'];
        $this->typc_libelle     = $result['typc_libelle'];
        $this->typc_description = $result['typc_description'];
        $this->typc_mod_for     = $result['typc_mod_for'];
        $this->typc_mod_dex     = $result['typc_mod_dex'];
        $this->typc_mod_int     = $result['typc_mod_int'];
        $this->typc_mod_con     = $result['typc_mod_con'];
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
            $req  = "insert into type_competences (
            typc_libelle,
            typc_description,
            typc_mod_for,
            typc_mod_dex,
            typc_mod_int,
            typc_mod_con                        )
                    values
                    (
                        :typc_libelle,
                        :typc_description,
                        :typc_mod_for,
                        :typc_mod_dex,
                        :typc_mod_int,
                        :typc_mod_con                        )
    returning typc_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":typc_libelle"     => $this->typc_libelle,
                                      ":typc_description" => $this->typc_description,
                                      ":typc_mod_for"     => $this->typc_mod_for,
                                      ":typc_mod_dex"     => $this->typc_mod_dex,
                                      ":typc_mod_int"     => $this->typc_mod_int,
                                      ":typc_mod_con"     => $this->typc_mod_con,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update type_competences
                    set
            typc_libelle = :typc_libelle,
            typc_description = :typc_description,
            typc_mod_for = :typc_mod_for,
            typc_mod_dex = :typc_mod_dex,
            typc_mod_int = :typc_mod_int,
            typc_mod_con = :typc_mod_con                        where typc_cod = :typc_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":typc_cod"         => $this->typc_cod,
                                      ":typc_libelle"     => $this->typc_libelle,
                                      ":typc_description" => $this->typc_description,
                                      ":typc_mod_for"     => $this->typc_mod_for,
                                      ":typc_mod_dex"     => $this->typc_mod_dex,
                                      ":typc_mod_int"     => $this->typc_mod_int,
                                      ":typc_mod_con"     => $this->typc_mod_con,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \type_competences
     * @global bdd_mysql $pdo
     * @return type_competences[]
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select typc_cod  from type_competences order by typc_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new type_competences;
            $temp->charge($result["typc_cod"]);
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
                        "select typc_cod  from type_competences where " . substr($name, 6) . " = ? order by typc_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new type_competences;
                        $temp->charge($result["typc_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table type_competences');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}