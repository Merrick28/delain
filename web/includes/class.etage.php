<?php
/**
 * includes/class.etage.php
 */

/**
 * Class etage
 *
 * Gère les objets BDD de la table etage
 */
class etage
{
    var $etage_cod;
    var $etage_numero;
    var $etage_libelle;
    var $etage_reference;
    var $etage_description;
    var $etage_affichage;
    var $etage_mort;
    var $etage_arene = 'N';
    var $etage_mine = 5;
    var $etage_retour_rune_monstre = 50;
    var $etage_mine_type = 999;
    var $etage_mine_richesse = 1000;
    var $etage_quatrieme_perso = 'N';
    var $etage_quatrieme_mortel = 'N';
    var $etage_type_arene = 0;
    var $etage_familier_actif = 0;
    var $etage_duree_imp_p = 2;
    var $etage_duree_imp_f = 8;
    var $etage_autor_rappel_cot = 0;
    var $etage_autor_glyphe = 0;
    var $etage_perte_xp = 100;
    var $etage_mort_speciale = 0;
    var $etage_monture_ordre = 4;

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
            $req  = "insert into etage (
            etage_numero,
            etage_libelle,
            etage_reference,
            etage_description,
            etage_affichage,
            etage_mort,
            etage_arene,
            etage_mine,
            etage_retour_rune_monstre,
            etage_mine_type,
            etage_mine_richesse,
            etage_quatrieme_perso,
            etage_quatrieme_mortel,
            etage_type_arene,
            etage_familier_actif,
            etage_duree_imp_p,
            etage_duree_imp_f,
            etage_autor_rappel_cot,
            etage_autor_glyphe,
            etage_perte_xp,
            etage_mort_speciale,
            etage_monture_ordre                        )
                    values
                    (
                        :etage_numero,
                        :etage_libelle,
                        :etage_reference,
                        :etage_description,
                        :etage_affichage,
                        :etage_mort,
                        :etage_arene,
                        :etage_mine,
                        :etage_retour_rune_monstre,
                        :etage_mine_type,
                        :etage_mine_richesse,
                        :etage_quatrieme_perso,
                        :etage_quatrieme_mortel,
                        :etage_type_arene,
                        :etage_familier_actif,
                        :etage_duree_imp_p,
                        :etage_duree_imp_f,
                        :etage_autor_rappel_cot,
                        :etage_autor_glyphe,
                        :etage_perte_xp ,
                        :etage_mort_speciale,
                        :etage_monture_ordre                        )
    returning etage_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":etage_numero" => $this->etage_numero,
                ":etage_libelle" => $this->etage_libelle,
                ":etage_reference" => $this->etage_reference,
                ":etage_description" => $this->etage_description,
                ":etage_affichage" => $this->etage_affichage,
                ":etage_mort" => $this->etage_mort,
                ":etage_arene" => $this->etage_arene,
                ":etage_mine" => $this->etage_mine,
                ":etage_retour_rune_monstre" => $this->etage_retour_rune_monstre,
                ":etage_mine_type" => $this->etage_mine_type,
                ":etage_mine_richesse" => $this->etage_mine_richesse,
                ":etage_quatrieme_perso" => $this->etage_quatrieme_perso,
                ":etage_quatrieme_mortel" => $this->etage_quatrieme_mortel,
                ":etage_type_arene" => $this->etage_type_arene,
                ":etage_familier_actif" => $this->etage_familier_actif,
                ":etage_duree_imp_p" => $this->etage_duree_imp_p,
                ":etage_duree_imp_f" => $this->etage_duree_imp_f,
                ":etage_autor_rappel_cot" => $this->etage_autor_rappel_cot,
                ":etage_autor_glyphe" => $this->etage_autor_glyphe,
                ":etage_perte_xp" => $this->etage_perte_xp,
                ":etage_mort_speciale" => $this->etage_mort_speciale,
                ":etage_monture_ordre" => $this->etage_monture_ordre,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update etage
                    set
            etage_numero = :etage_numero,
            etage_libelle = :etage_libelle,
            etage_reference = :etage_reference,
            etage_description = :etage_description,
            etage_affichage = :etage_affichage,
            etage_mort = :etage_mort,
            etage_arene = :etage_arene,
            etage_mine = :etage_mine,
            etage_retour_rune_monstre = :etage_retour_rune_monstre,
            etage_mine_type = :etage_mine_type,
            etage_mine_richesse = :etage_mine_richesse,
            etage_quatrieme_perso = :etage_quatrieme_perso,
            etage_quatrieme_mortel = :etage_quatrieme_mortel,
            etage_type_arene = :etage_type_arene,
            etage_familier_actif = :etage_familier_actif,
            etage_duree_imp_p = :etage_duree_imp_p,
            etage_duree_imp_f = :etage_duree_imp_f,
            etage_autor_rappel_cot = :etage_autor_rappel_cot,
            etage_autor_glyphe = :etage_autor_glyphe,
            etage_perte_xp = :etage_perte_xp,
            etage_mort_speciale = :etage_mort_speciale,
            etage_monture_ordre = :etage_monture_ordre                        where etage_cod = :etage_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":etage_cod" => $this->etage_cod,
                ":etage_numero" => $this->etage_numero,
                ":etage_libelle" => $this->etage_libelle,
                ":etage_reference" => $this->etage_reference,
                ":etage_description" => $this->etage_description,
                ":etage_affichage" => $this->etage_affichage,
                ":etage_mort" => $this->etage_mort,
                ":etage_arene" => $this->etage_arene,
                ":etage_mine" => $this->etage_mine,
                ":etage_retour_rune_monstre" => $this->etage_retour_rune_monstre,
                ":etage_mine_type" => $this->etage_mine_type,
                ":etage_mine_richesse" => $this->etage_mine_richesse,
                ":etage_quatrieme_perso" => $this->etage_quatrieme_perso,
                ":etage_quatrieme_mortel" => $this->etage_quatrieme_mortel,
                ":etage_type_arene" => $this->etage_type_arene,
                ":etage_familier_actif" => $this->etage_familier_actif,
                ":etage_duree_imp_p" => $this->etage_duree_imp_p,
                ":etage_duree_imp_f" => $this->etage_duree_imp_f,
                ":etage_autor_rappel_cot" => $this->etage_autor_rappel_cot,
                ":etage_autor_glyphe" => $this->etage_autor_glyphe,
                ":etage_perte_xp" => $this->etage_perte_xp,
                ":etage_mort_speciale" => $this->etage_mort_speciale,
                ":etage_monture_ordre" => $this->etage_monture_ordre,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de etage
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from etage where etage_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->etage_cod                 = $result['etage_cod'];
        $this->etage_numero              = $result['etage_numero'];
        $this->etage_libelle             = $result['etage_libelle'];
        $this->etage_reference           = $result['etage_reference'];
        $this->etage_description         = $result['etage_description'];
        $this->etage_affichage           = $result['etage_affichage'];
        $this->etage_mort                = $result['etage_mort'];
        $this->etage_arene               = $result['etage_arene'];
        $this->etage_mine                = $result['etage_mine'];
        $this->etage_retour_rune_monstre = $result['etage_retour_rune_monstre'];
        $this->etage_mine_type           = $result['etage_mine_type'];
        $this->etage_mine_richesse       = $result['etage_mine_richesse'];
        $this->etage_quatrieme_perso     = $result['etage_quatrieme_perso'];
        $this->etage_quatrieme_mortel    = $result['etage_quatrieme_mortel'];
        $this->etage_type_arene          = $result['etage_type_arene'];
        $this->etage_familier_actif      = $result['etage_familier_actif'];
        $this->etage_duree_imp_p         = $result['etage_duree_imp_p'];
        $this->etage_duree_imp_f         = $result['etage_duree_imp_f'];
        $this->etage_autor_rappel_cot    = $result['etage_autor_rappel_cot'];
        $this->etage_autor_glyphe        = $result['etage_autor_glyphe'];
        $this->etage_perte_xp            = $result['etage_perte_xp'];
        $this->etage_mort_speciale       = $result['etage_mort_speciale'];
        $this->etage_monture_ordre       = $result['etage_monture_ordre'];
        return true;
    }

    function getByNumero($code)
    {
        $pdo  = new bddpdo;
        $req  = "select etage_cod from etage where etage_numero = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['etage_cod']);
    }


    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \etage
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select etage_cod  from etage order by etage_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new etage;
            $temp->charge($result["etage_cod"]);
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
                    $req    = "select etage_cod  from etage where " . substr($name, 6) . " = ? order by etage_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new etage;
                        $temp->charge($result["etage_cod"]);
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
                ob_start();
                debug_print_backtrace();
                $out = ob_get_contents();
                error_log($out);
                die('Unknown method.');
        }
    }
}
