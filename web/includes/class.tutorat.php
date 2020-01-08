<?php
/**
 * includes/class.tutorat.php
 */

/**
 * Class tutorat
 *
 * Gère les objets BDD de la table tutorat
 */
class tutorat
{
    var $tuto_tuteur;
    var $tuto_filleul;
    var $tuto_ddeb;

    function __construct()
    {

        $this->tuto_ddeb = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de tutorat
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from tutorat where tuto_tuteur = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tuto_tuteur  = $result['tuto_tuteur'];
        $this->tuto_filleul = $result['tuto_filleul'];
        $this->tuto_ddeb    = $result['tuto_ddeb'];
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
            $req  = "insert into tutorat (
                     tuto_tuteur,
            tuto_filleul,
            tuto_ddeb                        )
                    values
                    (
                     :tuto_tuteur,   
                     :tuto_filleul,
                        :tuto_ddeb                        )
    returning tuto_tuteur as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":tuto_tuteur"  => $this->tuto_tuteur,
                                      ":tuto_filleul" => $this->tuto_filleul,
                                      ":tuto_ddeb"    => $this->tuto_ddeb,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update tutorat
                    set
            tuto_filleul = :tuto_filleul,
            tuto_ddeb = :tuto_ddeb                        where tuto_tuteur = :tuto_tuteur ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":tuto_tuteur"  => $this->tuto_tuteur,
                                      ":tuto_filleul" => $this->tuto_filleul,
                                      ":tuto_ddeb"    => $this->tuto_ddeb,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \tutorat
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select tuto_tuteur  from tutorat order by tuto_tuteur";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new tutorat;
            $temp->charge($result["tuto_tuteur"]);
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
                        "select tuto_tuteur  from tutorat where " . substr($name, 6) . " = ? order by tuto_tuteur";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new tutorat;
                        $temp->charge($result["tuto_tuteur"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table tutorat');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}