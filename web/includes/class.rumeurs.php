<?php
/**
 * includes/class.rumeurs.php
 */

/**
 * Class rumeurs
 *
 * Gère les objets BDD de la table rumeurs
 */
class rumeurs
{
    var $rum_cod;
    var $rum_perso_cod;
    var $rum_texte;
    var $rum_poids = 1;
    var $rum_date;
    var $rum_vu    = 0;

    function __construct()
    {

        $this->rum_date = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de rumeurs
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from rumeurs where rum_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->rum_cod       = $result['rum_cod'];
        $this->rum_perso_cod = $result['rum_perso_cod'];
        $this->rum_texte     = $result['rum_texte'];
        $this->rum_poids     = $result['rum_poids'];
        $this->rum_date      = $result['rum_date'];
        $this->rum_vu        = $result['rum_vu'];
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
            $req  = "insert into rumeurs (
            rum_perso_cod,
            rum_texte,
            rum_poids,
            rum_date,
            rum_vu                        )
                    values
                    (
                        :rum_perso_cod,
                        :rum_texte,
                        :rum_poids,
                        :rum_date,
                        :rum_vu                        )
    returning rum_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":rum_perso_cod" => $this->rum_perso_cod,
                ":rum_texte"     => $this->rum_texte,
                ":rum_poids"     => $this->rum_poids,
                ":rum_date"      => $this->rum_date,
                ":rum_vu"        => $this->rum_vu,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update rumeurs
                    set
            rum_perso_cod = :rum_perso_cod,
            rum_texte = :rum_texte,
            rum_poids = :rum_poids,
            rum_date = :rum_date,
            rum_vu = :rum_vu                        where rum_cod = :rum_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":rum_cod"       => $this->rum_cod,
                ":rum_perso_cod" => $this->rum_perso_cod,
                ":rum_texte"     => $this->rum_texte,
                ":rum_poids"     => $this->rum_poids,
                ":rum_date"      => $this->rum_date,
                ":rum_vu"        => $this->rum_vu,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \rumeurs
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select rum_cod  from rumeurs order by rum_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new rumeurs;
            $temp->charge($result["rum_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function get_rumeur()
    {
        $pdo    = new bddpdo;
        $req    = "select choix_rumeur() as rumeur";
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        return $result['rumeur'];
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
                    $req    = "select rum_cod  from rumeurs where " . substr($name, 6) . " = ? order by rum_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new rumeurs;
                        $temp->charge($result["rum_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table rumeurs');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}