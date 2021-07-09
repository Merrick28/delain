<?php
/**
 * includes/class.meca_position.php
 */

/**
 * Class meca_position
 *
 * Gère les objets BDD de la table meca_position
 */
class meca_position
{
    var $pmeca_cod;
    var $pmeca_meca_cod;
    var $pmeca_pos_cod;
    var $pmeca_pos_etage;
    var $pmeca_base_pos_type_aff;
    var $pmeca_base_pos_decor;
    var $pmeca_base_pos_decor_dessus;
    var $pmeca_base_pos_passage_autorise;
    var $pmeca_base_pos_modif_pa_dep;
    var $pmeca_base_pos_ter_cod;
    var $pmeca_base_mur_type;
    var $pmeca_base_mur_tangible = NULL;
    var $pmeca_base_mur_illusion = NULL;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de meca_position
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from meca_position where pmeca_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pmeca_cod = $result['pmeca_cod'];
        $this->pmeca_meca_cod = $result['pmeca_meca_cod'];
        $this->pmeca_pos_cod = $result['pmeca_pos_cod'];
        $this->pmeca_pos_etage = $result['pmeca_pos_etage'];
        $this->pmeca_base_pos_type_aff = $result['pmeca_base_pos_type_aff'];
        $this->pmeca_base_pos_decor = $result['pmeca_base_pos_decor'];
        $this->pmeca_base_pos_decor_dessus = $result['pmeca_base_pos_decor_dessus'];
        $this->pmeca_base_pos_passage_autorise = $result['pmeca_base_pos_passage_autorise'];
        $this->pmeca_base_pos_modif_pa_dep = $result['pmeca_base_pos_modif_pa_dep'];
        $this->pmeca_base_pos_ter_cod = $result['pmeca_base_pos_ter_cod'];
        $this->pmeca_base_mur_type = $result['pmeca_base_mur_type'];
        $this->pmeca_base_mur_tangible = $result['pmeca_base_mur_tangible'];
        $this->pmeca_base_mur_illusion = $result['pmeca_base_mur_illusion'];
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
        if($new)
        {
            $req = "insert into meca_position (
            pmeca_meca_cod,
            pmeca_pos_cod,
            pmeca_pos_etage,
            pmeca_base_pos_type_aff,
            pmeca_base_pos_decor,
            pmeca_base_pos_decor_dessus,
            pmeca_base_pos_passage_autorise,
            pmeca_base_pos_modif_pa_dep,
            pmeca_base_pos_ter_cod,
            pmeca_base_mur_type,
            pmeca_base_mur_tangible,
            pmeca_base_mur_illusion                        )
                    values
                    (
                        :pmeca_meca_cod,
                        :pmeca_pos_cod,
                        :pmeca_pos_etage,
                        :pmeca_base_pos_type_aff,
                        :pmeca_base_pos_decor,
                        :pmeca_base_pos_decor_dessus,
                        :pmeca_base_pos_passage_autorise,
                        :pmeca_base_pos_modif_pa_dep,
                        :pmeca_base_pos_ter_cod,
                        :pmeca_base_mur_type,
                        :pmeca_base_mur_tangible,
                        :pmeca_base_mur_illusion                        )
    returning pmeca_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pmeca_meca_cod" => $this->pmeca_meca_cod,
                ":pmeca_pos_cod" => $this->pmeca_pos_cod,
                ":pmeca_pos_etage" => $this->pmeca_pos_etage,
                ":pmeca_base_pos_type_aff" => $this->pmeca_base_pos_type_aff,
                ":pmeca_base_pos_decor" => $this->pmeca_base_pos_decor,
                ":pmeca_base_pos_decor_dessus" => $this->pmeca_base_pos_decor_dessus,
                ":pmeca_base_pos_passage_autorise" => $this->pmeca_base_pos_passage_autorise,
                ":pmeca_base_pos_modif_pa_dep" => $this->pmeca_base_pos_modif_pa_dep,
                ":pmeca_base_pos_ter_cod" => $this->pmeca_base_pos_ter_cod,
                ":pmeca_base_mur_type" => $this->pmeca_base_mur_type,
                ":pmeca_base_mur_tangible" => $this->pmeca_base_mur_tangible,
                ":pmeca_base_mur_illusion" => $this->pmeca_base_mur_illusion,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update meca_position
                    set
            pmeca_meca_cod = :pmeca_meca_cod,
            pmeca_pos_cod = :pmeca_pos_cod,
            pmeca_pos_etage = :pmeca_pos_etage,
            pmeca_base_pos_type_aff = :pmeca_base_pos_type_aff,
            pmeca_base_pos_decor = :pmeca_base_pos_decor,
            pmeca_base_pos_decor_dessus = :pmeca_base_pos_decor_dessus,
            pmeca_base_pos_passage_autorise = :pmeca_base_pos_passage_autorise,
            pmeca_base_pos_modif_pa_dep = :pmeca_base_pos_modif_pa_dep,
            pmeca_base_pos_ter_cod = :pmeca_base_pos_ter_cod,
            pmeca_base_mur_type = :pmeca_base_mur_type,
            pmeca_base_mur_tangible = :pmeca_base_mur_tangible,
            pmeca_base_mur_illusion = :pmeca_base_mur_illusion                        where pmeca_cod = :pmeca_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pmeca_cod" => $this->pmeca_cod,
                ":pmeca_meca_cod" => $this->pmeca_meca_cod,
                ":pmeca_pos_cod" => $this->pmeca_pos_cod,
                ":pmeca_pos_etage" => $this->pmeca_pos_etage,
                ":pmeca_base_pos_type_aff" => $this->pmeca_base_pos_type_aff,
                ":pmeca_base_pos_decor" => $this->pmeca_base_pos_decor,
                ":pmeca_base_pos_decor_dessus" => $this->pmeca_base_pos_decor_dessus,
                ":pmeca_base_pos_passage_autorise" => $this->pmeca_base_pos_passage_autorise,
                ":pmeca_base_pos_modif_pa_dep" => $this->pmeca_base_pos_modif_pa_dep,
                ":pmeca_base_pos_ter_cod" => $this->pmeca_base_pos_ter_cod,
                ":pmeca_base_mur_type" => $this->pmeca_base_mur_type,
                ":pmeca_base_mur_tangible" => $this->pmeca_base_mur_tangible,
                ":pmeca_base_mur_illusion" => $this->pmeca_base_mur_illusion,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \meca_position
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select pmeca_cod  from meca_position order by pmeca_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new meca_position;
            $temp->charge($result["pmeca_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function __call($name, $arguments){
        switch(substr($name, 0, 6)){
            case 'getBy_':
                if(property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo = new bddpdo;
                    $req = "select pmeca_cod  from meca_position where " . substr($name, 6) . " = ? order by pmeca_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new meca_position;
                        $temp->charge($result["pmeca_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if(count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name, 6) . ' in table meca_position');
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