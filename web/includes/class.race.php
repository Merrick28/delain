<?php
/**
 * includes/class.race.php
 */

/**
 * Class race
 *
 * Gère les objets BDD de la table race
 */
class race
{
    var $race_cod;
    var $race_nom;
    var $race_description;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de race
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from race where race_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->race_cod         = $result['race_cod'];
        $this->race_nom         = $result['race_nom'];
        $this->race_description = $result['race_description'];
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
                  = "insert into race (
            race_nom,
            race_description                        )
                    values
                    (
                        :race_nom,
                        :race_description                        )
    returning race_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":race_nom"         => $this->race_nom,
                ":race_description" => $this->race_description,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req
                  = "update race
                    set
            race_nom = :race_nom,
            race_description = :race_description                        where race_cod = :race_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":race_cod"         => $this->race_cod,
                ":race_nom"         => $this->race_nom,
                ":race_description" => $this->race_description,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \race
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select race_cod  from race order by race_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new race;
            $temp->charge($result["race_cod"]);
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
                    $req    = "select race_cod  from race where " . substr($name, 6) . " = ? order by race_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new race;
                        $temp->charge($result["race_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table race');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}