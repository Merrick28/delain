<?php
/**
 * includes/class.compt_droit.php
 */

/**
 * Class compt_droit
 *
 * Gère les objets BDD de la table compt_droit
 */
class compt_droit
{
    var $dcompt_compt_cod;
    var $dcompt_modif_perso = 'N';
    var $dcompt_modif_gmon = 'N';
    var $dcompt_controle = 'N';
    var $dcompt_acces_log = 'N';
    var $dcompt_monstre_automap = 'N';
    var $dcompt_etage = 'N';
    var $dcompt_gere_droits = 'N';
    var $dcompt_modif_carte = 'N';
    var $dcompt_controle_admin = 'N';
    var $dcompt_monstre_carte = 'N';
    var $dcompt_objet = 'N';
    var $dcompt_enchantements = 'N';
    var $dcompt_potions = 'N';
    var $dcompt_sondage = 'N';
    var $dcompt_news = 'N';
    var $dcompt_animations = 'N';
    var $dcompt_creer_monstre = 'N';
    var $dcompt_magie = 'N';
    var $dcompt_factions = 'N';

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
            $req  = "insert into compt_droit (
            dcompt_modif_perso,
            dcompt_modif_gmon,
            dcompt_controle,
            dcompt_acces_log,
            dcompt_monstre_automap,
            dcompt_etage,
            dcompt_gere_droits,
            dcompt_modif_carte,
            dcompt_controle_admin,
            dcompt_monstre_carte,
            dcompt_objet,
            dcompt_enchantements,
            dcompt_potions,
            dcompt_sondage,
            dcompt_news,
            dcompt_animations,
            dcompt_creer_monstre,
            dcompt_magie,
            dcompt_factions                        )
                    values
                    (
                        :dcompt_modif_perso,
                        :dcompt_modif_gmon,
                        :dcompt_controle,
                        :dcompt_acces_log,
                        :dcompt_monstre_automap,
                        :dcompt_etage,
                        :dcompt_gere_droits,
                        :dcompt_modif_carte,
                        :dcompt_controle_admin,
                        :dcompt_monstre_carte,
                        :dcompt_objet,
                        :dcompt_enchantements,
                        :dcompt_potions,
                        :dcompt_sondage,
                        :dcompt_news,
                        :dcompt_animations,
                        :dcompt_creer_monstre,
                        :dcompt_magie,
                        :dcompt_factions                        )
    returning dcompt_compt_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":dcompt_modif_perso" => $this->dcompt_modif_perso,
                ":dcompt_modif_gmon" => $this->dcompt_modif_gmon,
                ":dcompt_controle" => $this->dcompt_controle,
                ":dcompt_acces_log" => $this->dcompt_acces_log,
                ":dcompt_monstre_automap" => $this->dcompt_monstre_automap,
                ":dcompt_etage" => $this->dcompt_etage,
                ":dcompt_gere_droits" => $this->dcompt_gere_droits,
                ":dcompt_modif_carte" => $this->dcompt_modif_carte,
                ":dcompt_controle_admin" => $this->dcompt_controle_admin,
                ":dcompt_monstre_carte" => $this->dcompt_monstre_carte,
                ":dcompt_objet" => $this->dcompt_objet,
                ":dcompt_enchantements" => $this->dcompt_enchantements,
                ":dcompt_potions" => $this->dcompt_potions,
                ":dcompt_sondage" => $this->dcompt_sondage,
                ":dcompt_news" => $this->dcompt_news,
                ":dcompt_animations" => $this->dcompt_animations,
                ":dcompt_creer_monstre" => $this->dcompt_creer_monstre,
                ":dcompt_magie" => $this->dcompt_magie,
                ":dcompt_factions" => $this->dcompt_factions,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update compt_droit
                    set
            dcompt_modif_perso = :dcompt_modif_perso,
            dcompt_modif_gmon = :dcompt_modif_gmon,
            dcompt_controle = :dcompt_controle,
            dcompt_acces_log = :dcompt_acces_log,
            dcompt_monstre_automap = :dcompt_monstre_automap,
            dcompt_etage = :dcompt_etage,
            dcompt_gere_droits = :dcompt_gere_droits,
            dcompt_modif_carte = :dcompt_modif_carte,
            dcompt_controle_admin = :dcompt_controle_admin,
            dcompt_monstre_carte = :dcompt_monstre_carte,
            dcompt_objet = :dcompt_objet,
            dcompt_enchantements = :dcompt_enchantements,
            dcompt_potions = :dcompt_potions,
            dcompt_sondage = :dcompt_sondage,
            dcompt_news = :dcompt_news,
            dcompt_animations = :dcompt_animations,
            dcompt_creer_monstre = :dcompt_creer_monstre,
            dcompt_magie = :dcompt_magie,
            dcompt_factions = :dcompt_factions                        where dcompt_compt_cod = :dcompt_compt_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":dcompt_compt_cod" => $this->dcompt_compt_cod,
                ":dcompt_modif_perso" => $this->dcompt_modif_perso,
                ":dcompt_modif_gmon" => $this->dcompt_modif_gmon,
                ":dcompt_controle" => $this->dcompt_controle,
                ":dcompt_acces_log" => $this->dcompt_acces_log,
                ":dcompt_monstre_automap" => $this->dcompt_monstre_automap,
                ":dcompt_etage" => $this->dcompt_etage,
                ":dcompt_gere_droits" => $this->dcompt_gere_droits,
                ":dcompt_modif_carte" => $this->dcompt_modif_carte,
                ":dcompt_controle_admin" => $this->dcompt_controle_admin,
                ":dcompt_monstre_carte" => $this->dcompt_monstre_carte,
                ":dcompt_objet" => $this->dcompt_objet,
                ":dcompt_enchantements" => $this->dcompt_enchantements,
                ":dcompt_potions" => $this->dcompt_potions,
                ":dcompt_sondage" => $this->dcompt_sondage,
                ":dcompt_news" => $this->dcompt_news,
                ":dcompt_animations" => $this->dcompt_animations,
                ":dcompt_creer_monstre" => $this->dcompt_creer_monstre,
                ":dcompt_magie" => $this->dcompt_magie,
                ":dcompt_factions" => $this->dcompt_factions,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de compt_droit
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from compt_droit where dcompt_compt_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->dcompt_compt_cod       = $result['dcompt_compt_cod'];
        $this->dcompt_modif_perso     = $result['dcompt_modif_perso'];
        $this->dcompt_modif_gmon      = $result['dcompt_modif_gmon'];
        $this->dcompt_controle        = $result['dcompt_controle'];
        $this->dcompt_acces_log       = $result['dcompt_acces_log'];
        $this->dcompt_monstre_automap = $result['dcompt_monstre_automap'];
        $this->dcompt_etage           = $result['dcompt_etage'];
        $this->dcompt_gere_droits     = $result['dcompt_gere_droits'];
        $this->dcompt_modif_carte     = $result['dcompt_modif_carte'];
        $this->dcompt_controle_admin  = $result['dcompt_controle_admin'];
        $this->dcompt_monstre_carte   = $result['dcompt_monstre_carte'];
        $this->dcompt_objet           = $result['dcompt_objet'];
        $this->dcompt_enchantements   = $result['dcompt_enchantements'];
        $this->dcompt_potions         = $result['dcompt_potions'];
        $this->dcompt_sondage         = $result['dcompt_sondage'];
        $this->dcompt_news            = $result['dcompt_news'];
        $this->dcompt_animations      = $result['dcompt_animations'];
        $this->dcompt_creer_monstre   = $result['dcompt_creer_monstre'];
        $this->dcompt_magie           = $result['dcompt_magie'];
        $this->dcompt_factions        = $result['dcompt_factions'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \compt_droit
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select dcompt_compt_cod  from compt_droit order by dcompt_compt_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new compt_droit;
            $temp->charge($result["dcompt_compt_cod"]);
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
                    $req    = "select dcompt_compt_cod  from compt_droit where " . substr($name, 6) . " = ? order by dcompt_compt_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new compt_droit;
                        $temp->charge($result["dcompt_compt_cod"]);
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