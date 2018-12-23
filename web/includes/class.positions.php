<?php
/**
 * includes/class.positions.php
 */

/**
 * Class positions
 *
 * Gère les objets BDD de la table positions
 */
class positions
{
    var $pos_cod;
    var $pos_x;
    var $pos_y;
    var $pos_etage;
    var $pos_key;
    var $pos_type_aff = 1;
    var $pos_magie = 0;
    var $pos_decor = 0;
    var $pos_decor_dessus = 0;
    var $pos_fonction_arrivee;
    var $pos_passage_autorise = 1;
    var $pos_modif_pa_dep = 0;
    var $pos_fonction_dessus;
    var $pos_entree_arene = 'N';
    var $pos_anticipation = 0;
    var $pos_pvp = 'O';

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
            $req  = "insert into positions (
            pos_x,
            pos_y,
            pos_etage,
            pos_key,
            pos_type_aff,
            pos_magie,
            pos_decor,
            pos_decor_dessus,
            pos_fonction_arrivee,
            pos_passage_autorise,
            pos_modif_pa_dep,
            pos_fonction_dessus,
            pos_entree_arene,
            pos_anticipation,
            pos_pvp                        )
                    values
                    (
                        :pos_x,
                        :pos_y,
                        :pos_etage,
                        :pos_key,
                        :pos_type_aff,
                        :pos_magie,
                        :pos_decor,
                        :pos_decor_dessus,
                        :pos_fonction_arrivee,
                        :pos_passage_autorise,
                        :pos_modif_pa_dep,
                        :pos_fonction_dessus,
                        :pos_entree_arene,
                        :pos_anticipation,
                        :pos_pvp                        )
    returning pos_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pos_x" => $this->pos_x,
                ":pos_y" => $this->pos_y,
                ":pos_etage" => $this->pos_etage,
                ":pos_key" => $this->pos_key,
                ":pos_type_aff" => $this->pos_type_aff,
                ":pos_magie" => $this->pos_magie,
                ":pos_decor" => $this->pos_decor,
                ":pos_decor_dessus" => $this->pos_decor_dessus,
                ":pos_fonction_arrivee" => $this->pos_fonction_arrivee,
                ":pos_passage_autorise" => $this->pos_passage_autorise,
                ":pos_modif_pa_dep" => $this->pos_modif_pa_dep,
                ":pos_fonction_dessus" => $this->pos_fonction_dessus,
                ":pos_entree_arene" => $this->pos_entree_arene,
                ":pos_anticipation" => $this->pos_anticipation,
                ":pos_pvp" => $this->pos_pvp,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update positions
                    set
            pos_x = :pos_x,
            pos_y = :pos_y,
            pos_etage = :pos_etage,
            pos_key = :pos_key,
            pos_type_aff = :pos_type_aff,
            pos_magie = :pos_magie,
            pos_decor = :pos_decor,
            pos_decor_dessus = :pos_decor_dessus,
            pos_fonction_arrivee = :pos_fonction_arrivee,
            pos_passage_autorise = :pos_passage_autorise,
            pos_modif_pa_dep = :pos_modif_pa_dep,
            pos_fonction_dessus = :pos_fonction_dessus,
            pos_entree_arene = :pos_entree_arene,
            pos_anticipation = :pos_anticipation,
            pos_pvp = :pos_pvp                        where pos_cod = :pos_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pos_cod" => $this->pos_cod,
                ":pos_x" => $this->pos_x,
                ":pos_y" => $this->pos_y,
                ":pos_etage" => $this->pos_etage,
                ":pos_key" => $this->pos_key,
                ":pos_type_aff" => $this->pos_type_aff,
                ":pos_magie" => $this->pos_magie,
                ":pos_decor" => $this->pos_decor,
                ":pos_decor_dessus" => $this->pos_decor_dessus,
                ":pos_fonction_arrivee" => $this->pos_fonction_arrivee,
                ":pos_passage_autorise" => $this->pos_passage_autorise,
                ":pos_modif_pa_dep" => $this->pos_modif_pa_dep,
                ":pos_fonction_dessus" => $this->pos_fonction_dessus,
                ":pos_entree_arene" => $this->pos_entree_arene,
                ":pos_anticipation" => $this->pos_anticipation,
                ":pos_pvp" => $this->pos_pvp,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de positions
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from positions where pos_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pos_cod              = $result['pos_cod'];
        $this->pos_x                = $result['pos_x'];
        $this->pos_y                = $result['pos_y'];
        $this->pos_etage            = $result['pos_etage'];
        $this->pos_key              = $result['pos_key'];
        $this->pos_type_aff         = $result['pos_type_aff'];
        $this->pos_magie            = $result['pos_magie'];
        $this->pos_decor            = $result['pos_decor'];
        $this->pos_decor_dessus     = $result['pos_decor_dessus'];
        $this->pos_fonction_arrivee = $result['pos_fonction_arrivee'];
        $this->pos_passage_autorise = $result['pos_passage_autorise'];
        $this->pos_modif_pa_dep     = $result['pos_modif_pa_dep'];
        $this->pos_fonction_dessus  = $result['pos_fonction_dessus'];
        $this->pos_entree_arene     = $result['pos_entree_arene'];
        $this->pos_anticipation     = $result['pos_anticipation'];
        $this->pos_pvp              = $result['pos_pvp'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \positions
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pos_cod  from positions order by pos_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new positions;
            $temp->charge($result["pos_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function get_indice()
    {
        $pdo    = new bddpdo;
        $req    = "select indice_lieu(:position) as indice";

        $stmt           = $pdo->prepare($req);
        $stmt           = $pdo->execute(array(
            ':position'   => $this->pos_cod,

        ), $stmt);
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        return $result['indice'];
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
                    $req    = "select pos_cod  from positions where " . substr($name, 6) . " = ? order by pos_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new positions;
                        $temp->charge($result["pos_cod"]);
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