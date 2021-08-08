<?php
/**
 * includes/class.meca.php
 */

/**
 * Class meca
 *
 * Gère les objets BDD de la table meca
 */
class meca
{
    var $meca_cod;
    var $meca_nom;
    var $meca_type = 'G';
    var $meca_pos_etage;
    var $meca_pos_type_aff;
    var $meca_pos_decor;
    var $meca_pos_decor_dessus;
    var $meca_pos_passage_autorise;
    var $meca_pos_modif_pa_dep;
    var $meca_pos_ter_cod;
    var $meca_mur_type;
    var $meca_mur_tangible = NULL;
    var $meca_mur_illusion = NULL;
    var $meca_si_active;
    var $meca_si_desactive;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de meca
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from meca where meca_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->meca_cod = $result['meca_cod'];
        $this->meca_nom = $result['meca_nom'];
        $this->meca_type = $result['meca_type'];
        $this->meca_pos_etage = $result['meca_pos_etage'];
        $this->meca_pos_type_aff = $result['meca_pos_type_aff'];
        $this->meca_pos_decor = $result['meca_pos_decor'];
        $this->meca_pos_decor_dessus = $result['meca_pos_decor_dessus'];
        $this->meca_pos_passage_autorise = $result['meca_pos_passage_autorise'];
        $this->meca_pos_modif_pa_dep = $result['meca_pos_modif_pa_dep'];
        $this->meca_pos_ter_cod = $result['meca_pos_ter_cod'];
        $this->meca_mur_type = $result['meca_mur_type'];
        $this->meca_mur_tangible = $result['meca_mur_tangible'];
        $this->meca_mur_illusion = $result['meca_mur_illusion'];
        $this->meca_si_active = $result['meca_si_active'];
        $this->meca_si_desactive = $result['meca_si_desactive'];
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
            $req = "insert into meca (
            meca_nom,
            meca_type,
            meca_pos_etage,
            meca_pos_type_aff,
            meca_pos_decor,
            meca_pos_decor_dessus,
            meca_pos_passage_autorise,
            meca_pos_modif_pa_dep,
            meca_pos_ter_cod,
            meca_mur_type,
            meca_mur_tangible,
            meca_mur_illusion,
            meca_si_active,
            meca_si_desactive                        )
                    values
                    (
                        :meca_nom,
                        :meca_type,
                        :meca_pos_etage,
                        :meca_pos_type_aff,
                        :meca_pos_decor,
                        :meca_pos_decor_dessus,
                        :meca_pos_passage_autorise,
                        :meca_pos_modif_pa_dep,
                        :meca_pos_ter_cod,
                        :meca_mur_type,
                        :meca_mur_tangible,
                        :meca_mur_illusion,
                        :meca_si_active,
                        :meca_si_desactive                        )
    returning meca_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":meca_nom" => $this->meca_nom,
                ":meca_type" => $this->meca_type,
                ":meca_pos_etage" => $this->meca_pos_etage,
                ":meca_pos_type_aff" => $this->meca_pos_type_aff,
                ":meca_pos_decor" => $this->meca_pos_decor,
                ":meca_pos_decor_dessus" => $this->meca_pos_decor_dessus,
                ":meca_pos_passage_autorise" => $this->meca_pos_passage_autorise,
                ":meca_pos_modif_pa_dep" => $this->meca_pos_modif_pa_dep,
                ":meca_pos_ter_cod" => $this->meca_pos_ter_cod,
                ":meca_mur_type" => $this->meca_mur_type,
                ":meca_mur_tangible" => $this->meca_mur_tangible,
                ":meca_mur_illusion" => $this->meca_mur_illusion,
                ":meca_si_active" => $this->meca_si_active,
                ":meca_si_desactive" => $this->meca_si_desactive,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update meca
                    set
            meca_nom = :meca_nom,
            meca_type = :meca_type,
            meca_pos_etage = :meca_pos_etage,
            meca_pos_type_aff = :meca_pos_type_aff,
            meca_pos_decor = :meca_pos_decor,
            meca_pos_decor_dessus = :meca_pos_decor_dessus,
            meca_pos_passage_autorise = :meca_pos_passage_autorise,
            meca_pos_modif_pa_dep = :meca_pos_modif_pa_dep,
            meca_pos_ter_cod = :meca_pos_ter_cod,
            meca_mur_type = :meca_mur_type,
            meca_mur_tangible = :meca_mur_tangible,
            meca_mur_illusion = :meca_mur_illusion,
            meca_si_active = :meca_si_active,
            meca_si_desactive = :meca_si_desactive                        where meca_cod = :meca_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":meca_cod" => $this->meca_cod,
                ":meca_nom" => $this->meca_nom,
                ":meca_type" => $this->meca_type,
                ":meca_pos_etage" => $this->meca_pos_etage,
                ":meca_pos_type_aff" => $this->meca_pos_type_aff,
                ":meca_pos_decor" => $this->meca_pos_decor,
                ":meca_pos_decor_dessus" => $this->meca_pos_decor_dessus,
                ":meca_pos_passage_autorise" => $this->meca_pos_passage_autorise,
                ":meca_pos_modif_pa_dep" => $this->meca_pos_modif_pa_dep,
                ":meca_pos_ter_cod" => $this->meca_pos_ter_cod,
                ":meca_mur_type" => $this->meca_mur_type,
                ":meca_mur_tangible" => $this->meca_mur_tangible,
                ":meca_mur_illusion" => $this->meca_mur_illusion,
                ":meca_si_active" => $this->meca_si_active,
                ":meca_si_desactive" => $this->meca_si_desactive,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \meca
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select meca_cod  from meca order by meca_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new meca;
            $temp->charge($result["meca_cod"]);
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
                    $req = "select meca_cod  from meca where " . substr($name, 6) . " = ? order by meca_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new meca;
                        $temp->charge($result["meca_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table meca');
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

    function set_positions($pos_liste)
    {
        $pdo = new bddpdo;

        // Préparation de la liste des positionen PDO
        $i = 0 ; $in_req = "";
        foreach ($pos_liste as $item)
        {
            if ((int)$item != 0)
            {
                $key = ":pmeca_pos_cod".$i++;
                $in_req .= ($in_req ? "," : "") . $key; // :id0,:id1,:id2
                $in_list[$key] = (int)$item; // collecting values into a key-value array
            }
        }

        // Un trigger "ON DELETE" désactive un eventuel mecanisme activé sur cette position et rétablit les caracs de base de la position
        $req = "delete from meca_position where pmeca_meca_cod=:meca_cod and pmeca_pos_cod not in ({$in_req})";
        $stmt = $pdo->prepare($req);
        $pdo->execute(array_merge([":meca_cod" => $this->meca_cod],$in_list),$stmt);

        foreach ($pos_liste as $pos_cod)
        {
            if ((int)$pos_cod != 0)
            {
                $pdo    = new bddpdo();
                $req = "select meca_ajout_pos(:meca_cod,:pos_cod) as resultat";
                $stmt   = $pdo->prepare($req);
                $pdo->execute(array( ":meca_cod" => $this->meca_cod, ":pos_cod" => (int)$pos_cod), $stmt);
            }
        }
    }
}