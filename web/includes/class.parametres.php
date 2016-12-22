<?php
/**
 * includes/class.parametres.php
 */

/**
 * Class parametres
 *
 * Gère les objets BDD de la table parametres
 */
class parametres
{
    var $parm_cod;
    var $parm_type;
    var $parm_desc;
    var $parm_valeur;
    var $parm_valeur_texte;

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
            $req = "insert into parametres (
            parm_type,
            parm_desc,
            parm_valeur,
            parm_valeur_texte                        )
                    values
                    (
                        :parm_type,
                        :parm_desc,
                        :parm_valeur,
                        :parm_valeur_texte                        )
    returning parm_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":parm_type" => $this->parm_type,
                ":parm_desc" => $this->parm_desc,
                ":parm_valeur" => $this->parm_valeur,
                ":parm_valeur_texte" => $this->parm_valeur_texte,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update parametres
                    set
            parm_type = :parm_type,
            parm_desc = :parm_desc,
            parm_valeur = :parm_valeur,
            parm_valeur_texte = :parm_valeur_texte                        where parm_cod = :parm_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":parm_cod" => $this->parm_cod,
                ":parm_type" => $this->parm_type,
                ":parm_desc" => $this->parm_desc,
                ":parm_valeur" => $this->parm_valeur,
                ":parm_valeur_texte" => $this->parm_valeur_texte,
            ), $stmt);
        }
    }

    /**
     * @param $code Code du parametre à obtenir
     * @param bool $refresh True pour forcer le refresh
     * @return bool|mixed
     */
    function getparm($code,$refresh = false)
    {
        $m = new memcached();
        if(!$refresh)
        {
            // on n'est pas en refresh
            // on peut prendre la varaible du cache si besoin
            if(!$retour = $m->get('parm_' . $code))
            {
                // on n'a pas la variable en memcached
                $retour = $this->detail_getparm($code);
            }
            return $retour;
        }
        else
        {
            $retour = $this->detail_getparm($code);
            return $retour;
        }
    }

    /**
     * Charge le détail d'un parametre
     * @param $code Code du parametre à obtenir
     * @return mixed
     */
    function detail_getparm($code)
    {
        $m = new memcached();
        $this->charge($code);
        if(empty($this->parm_valeur_texte))
        {
            $retour = $this->parm_valeur;
        }
        else
        {
            $retour = $this->parm_valeur_texte;
        }
        // on la met dans le memcached
        $m->put('parm_' . $code,$retour);
        return $retour;
    }

    /**
     * Charge dans la classe un enregistrement de parametres
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from parametres where parm_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->parm_cod = $result['parm_cod'];
        $this->parm_type = $result['parm_type'];
        $this->parm_desc = $result['parm_desc'];
        $this->parm_valeur = $result['parm_valeur'];
        $this->parm_valeur_texte = $result['parm_valeur_texte'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return array|parametres
     */
    function getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select parm_cod  from parametres order by parm_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new parametres;
            $temp->charge($result["parm_cod"]);
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
                    $req = "select parm_cod  from parametres where " . substr($name, 6) . " = ? order by parm_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new parametres;
                        $temp->charge($result["parm_cod"]);
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
                    die('Unknown variable.');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}