<?php
/**
 * includes/class.objet_generique.php
 */

/**
 * Class objet_generique
 *
 * Gère les objets BDD de la table objet_generique
 */
class objet_generique
{
    var $gobj_cod;
    var $gobj_nom;
    var $gobj_nom_generique;
    var $gobj_tobj_cod;
    var $gobj_bonus_cod;
    var $gobj_valeur;
    var $gobj_obcar_cod;
    var $gobj_comp_cod;
    var $gobj_poids;
    var $gobj_pa_normal;
    var $gobj_pa_eclair;
    var $gobj_description;
    var $gobj_frune_cod;
    var $gobj_rune_position;
    var $gobj_distance;
    var $gobj_nettoyage;
    var $gobj_deposable       = 'O';
    var $gobj_visible         = 'O';
    var $gobj_usure;
    var $gobj_echoppe         = 'O';
    var $gobj_vampire         = 0;
    var $gobj_absorbe         = 0;
    var $gobj_url;
    var $gobj_obon_cod;
    var $gobj_seuil_force     = 0;
    var $gobj_seuil_dex       = 0;
    var $gobj_nb_mains;
    var $gobj_poison;
    var $gobj_portee;
    var $gobj_regen;
    var $gobj_aura_feu;
    var $gobj_bonus_vue;
    var $gobj_critique;
    var $gobj_bonus_armure;
    var $gobj_sort_cod;
    var $gobj_chance_drop     = 100;
    var $gobj_chance_enchant  = 0;
    var $gobj_echoppe_vente   = 'N';
    var $gobj_echoppe_stock   = 'N';
    var $gobj_echoppe_destock = 'N';
    var $gobj_stabilite;
    var $gobj_arme_naturelle  = 'N';
    var $gobj_image;
    var $gobj_image_generique;
    var $gobj_niv_parchemin   = 1;
    var $gobj_niv_peau        = 1;
    var $gobj_perce_armure    = false;
    var $gobj_coeff_percearmure;
    var $gobj_niveau_min      = 0;
    var $gobj_desequipable    = 'O';
    var $gobj_postable        = 'N';
    var $gobj_chance_drop_monstre;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de objet_generique
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from objet_generique where gobj_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->gobj_cod                 = $result['gobj_cod'];
        $this->gobj_nom                 = $result['gobj_nom'];
        $this->gobj_nom_generique       = $result['gobj_nom_generique'];
        $this->gobj_tobj_cod            = $result['gobj_tobj_cod'];
        $this->gobj_bonus_cod           = $result['gobj_bonus_cod'];
        $this->gobj_valeur              = $result['gobj_valeur'];
        $this->gobj_obcar_cod           = $result['gobj_obcar_cod'];
        $this->gobj_comp_cod            = $result['gobj_comp_cod'];
        $this->gobj_poids               = $result['gobj_poids'];
        $this->gobj_pa_normal           = $result['gobj_pa_normal'];
        $this->gobj_pa_eclair           = $result['gobj_pa_eclair'];
        $this->gobj_description         = $result['gobj_description'];
        $this->gobj_frune_cod           = $result['gobj_frune_cod'];
        $this->gobj_rune_position       = $result['gobj_rune_position'];
        $this->gobj_distance            = $result['gobj_distance'];
        $this->gobj_nettoyage           = $result['gobj_nettoyage'];
        $this->gobj_deposable           = $result['gobj_deposable'];
        $this->gobj_visible             = $result['gobj_visible'];
        $this->gobj_usure               = $result['gobj_usure'];
        $this->gobj_echoppe             = $result['gobj_echoppe'];
        $this->gobj_vampire             = $result['gobj_vampire'];
        $this->gobj_absorbe             = $result['gobj_absorbe'];
        $this->gobj_url                 = $result['gobj_url'];
        $this->gobj_obon_cod            = $result['gobj_obon_cod'];
        $this->gobj_seuil_force         = $result['gobj_seuil_force'];
        $this->gobj_seuil_dex           = $result['gobj_seuil_dex'];
        $this->gobj_nb_mains            = $result['gobj_nb_mains'];
        $this->gobj_poison              = $result['gobj_poison'];
        $this->gobj_portee              = $result['gobj_portee'];
        $this->gobj_regen               = $result['gobj_regen'];
        $this->gobj_aura_feu            = $result['gobj_aura_feu'];
        $this->gobj_bonus_vue           = $result['gobj_bonus_vue'];
        $this->gobj_critique            = $result['gobj_critique'];
        $this->gobj_bonus_armure        = $result['gobj_bonus_armure'];
        $this->gobj_sort_cod            = $result['gobj_sort_cod'];
        $this->gobj_chance_drop         = $result['gobj_chance_drop'];
        $this->gobj_chance_enchant      = $result['gobj_chance_enchant'];
        $this->gobj_echoppe_vente       = $result['gobj_echoppe_vente'];
        $this->gobj_echoppe_stock       = $result['gobj_echoppe_stock'];
        $this->gobj_echoppe_destock     = $result['gobj_echoppe_destock'];
        $this->gobj_stabilite           = $result['gobj_stabilite'];
        $this->gobj_arme_naturelle      = $result['gobj_arme_naturelle'];
        $this->gobj_image               = $result['gobj_image'];
        $this->gobj_image_generique     = $result['gobj_image_generique'];
        $this->gobj_niv_parchemin       = $result['gobj_niv_parchemin'];
        $this->gobj_niv_peau            = $result['gobj_niv_peau'];
        $this->gobj_perce_armure        = $result['gobj_perce_armure'];
        $this->gobj_coeff_percearmure   = $result['gobj_coeff_percearmure'];
        $this->gobj_niveau_min          = $result['gobj_niveau_min'];
        $this->gobj_desequipable        = $result['gobj_desequipable'];
        $this->gobj_postable            = $result['gobj_postable'];
        $this->gobj_chance_drop_monstre = $result['gobj_chance_drop_monstre'];
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
            $req  = "insert into objet_generique (
            gobj_nom,
            gobj_nom_generique,
            gobj_tobj_cod,
            gobj_bonus_cod,
            gobj_valeur,
            gobj_obcar_cod,
            gobj_comp_cod,
            gobj_poids,
            gobj_pa_normal,
            gobj_pa_eclair,
            gobj_description,
            gobj_frune_cod,
            gobj_rune_position,
            gobj_distance,
            gobj_nettoyage,
            gobj_deposable,
            gobj_visible,
            gobj_usure,
            gobj_echoppe,
            gobj_vampire,
            gobj_absorbe,
            gobj_url,
            gobj_obon_cod,
            gobj_seuil_force,
            gobj_seuil_dex,
            gobj_nb_mains,
            gobj_poison,
            gobj_portee,
            gobj_regen,
            gobj_aura_feu,
            gobj_bonus_vue,
            gobj_critique,
            gobj_bonus_armure,
            gobj_sort_cod,
            gobj_chance_drop,
            gobj_chance_enchant,
            gobj_echoppe_vente,
            gobj_echoppe_stock,
            gobj_echoppe_destock,
            gobj_stabilite,
            gobj_arme_naturelle,
            gobj_image,
            gobj_image_generique,
            gobj_niv_parchemin,
            gobj_niv_peau,
            gobj_perce_armure,
            gobj_coeff_percearmure,
            gobj_niveau_min,
            gobj_desequipable,
            gobj_postable,
            gobj_chance_drop_monstre                        )
                    values
                    (
                        :gobj_nom,
                        :gobj_nom_generique,
                        :gobj_tobj_cod,
                        :gobj_bonus_cod,
                        :gobj_valeur,
                        :gobj_obcar_cod,
                        :gobj_comp_cod,
                        :gobj_poids,
                        :gobj_pa_normal,
                        :gobj_pa_eclair,
                        :gobj_description,
                        :gobj_frune_cod,
                        :gobj_rune_position,
                        :gobj_distance,
                        :gobj_nettoyage,
                        :gobj_deposable,
                        :gobj_visible,
                        :gobj_usure,
                        :gobj_echoppe,
                        :gobj_vampire,
                        :gobj_absorbe,
                        :gobj_url,
                        :gobj_obon_cod,
                        :gobj_seuil_force,
                        :gobj_seuil_dex,
                        :gobj_nb_mains,
                        :gobj_poison,
                        :gobj_portee,
                        :gobj_regen,
                        :gobj_aura_feu,
                        :gobj_bonus_vue,
                        :gobj_critique,
                        :gobj_bonus_armure,
                        :gobj_sort_cod,
                        :gobj_chance_drop,
                        :gobj_chance_enchant,
                        :gobj_echoppe_vente,
                        :gobj_echoppe_stock,
                        :gobj_echoppe_destock,
                        :gobj_stabilite,
                        :gobj_arme_naturelle,
                        :gobj_image,
                        :gobj_image_generique,
                        :gobj_niv_parchemin,
                        :gobj_niv_peau,
                        :gobj_perce_armure,
                        :gobj_coeff_percearmure,
                        :gobj_niveau_min,
                        :gobj_desequipable,
                        :gobj_postable,
                        :gobj_chance_drop_monstre                        )
    returning gobj_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":gobj_nom"                 => $this->gobj_nom,
                                      ":gobj_nom_generique"       => $this->gobj_nom_generique,
                                      ":gobj_tobj_cod"            => $this->gobj_tobj_cod,
                                      ":gobj_bonus_cod"           => $this->gobj_bonus_cod,
                                      ":gobj_valeur"              => $this->gobj_valeur,
                                      ":gobj_obcar_cod"           => $this->gobj_obcar_cod,
                                      ":gobj_comp_cod"            => $this->gobj_comp_cod,
                                      ":gobj_poids"               => $this->gobj_poids,
                                      ":gobj_pa_normal"           => $this->gobj_pa_normal,
                                      ":gobj_pa_eclair"           => $this->gobj_pa_eclair,
                                      ":gobj_description"         => $this->gobj_description,
                                      ":gobj_frune_cod"           => $this->gobj_frune_cod,
                                      ":gobj_rune_position"       => $this->gobj_rune_position,
                                      ":gobj_distance"            => $this->gobj_distance,
                                      ":gobj_nettoyage"           => $this->gobj_nettoyage,
                                      ":gobj_deposable"           => $this->gobj_deposable,
                                      ":gobj_visible"             => $this->gobj_visible,
                                      ":gobj_usure"               => $this->gobj_usure,
                                      ":gobj_echoppe"             => $this->gobj_echoppe,
                                      ":gobj_vampire"             => $this->gobj_vampire,
                                      ":gobj_absorbe"             => $this->gobj_absorbe,
                                      ":gobj_url"                 => $this->gobj_url,
                                      ":gobj_obon_cod"            => $this->gobj_obon_cod,
                                      ":gobj_seuil_force"         => $this->gobj_seuil_force,
                                      ":gobj_seuil_dex"           => $this->gobj_seuil_dex,
                                      ":gobj_nb_mains"            => $this->gobj_nb_mains,
                                      ":gobj_poison"              => $this->gobj_poison,
                                      ":gobj_portee"              => $this->gobj_portee,
                                      ":gobj_regen"               => $this->gobj_regen,
                                      ":gobj_aura_feu"            => $this->gobj_aura_feu,
                                      ":gobj_bonus_vue"           => $this->gobj_bonus_vue,
                                      ":gobj_critique"            => $this->gobj_critique,
                                      ":gobj_bonus_armure"        => $this->gobj_bonus_armure,
                                      ":gobj_sort_cod"            => $this->gobj_sort_cod,
                                      ":gobj_chance_drop"         => $this->gobj_chance_drop,
                                      ":gobj_chance_enchant"      => $this->gobj_chance_enchant,
                                      ":gobj_echoppe_vente"       => $this->gobj_echoppe_vente,
                                      ":gobj_echoppe_stock"       => $this->gobj_echoppe_stock,
                                      ":gobj_echoppe_destock"     => $this->gobj_echoppe_destock,
                                      ":gobj_stabilite"           => $this->gobj_stabilite,
                                      ":gobj_arme_naturelle"      => $this->gobj_arme_naturelle,
                                      ":gobj_image"               => $this->gobj_image,
                                      ":gobj_image_generique"     => $this->gobj_image_generique,
                                      ":gobj_niv_parchemin"       => $this->gobj_niv_parchemin,
                                      ":gobj_niv_peau"            => $this->gobj_niv_peau,
                                      ":gobj_perce_armure"        => $this->gobj_perce_armure,
                                      ":gobj_coeff_percearmure"   => $this->gobj_coeff_percearmure,
                                      ":gobj_niveau_min"          => $this->gobj_niveau_min,
                                      ":gobj_desequipable"        => $this->gobj_desequipable,
                                      ":gobj_postable"            => $this->gobj_postable,
                                      ":gobj_chance_drop_monstre" => $this->gobj_chance_drop_monstre,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update objet_generique
                    set
            gobj_nom = :gobj_nom,
            gobj_nom_generique = :gobj_nom_generique,
            gobj_tobj_cod = :gobj_tobj_cod,
            gobj_bonus_cod = :gobj_bonus_cod,
            gobj_valeur = :gobj_valeur,
            gobj_obcar_cod = :gobj_obcar_cod,
            gobj_comp_cod = :gobj_comp_cod,
            gobj_poids = :gobj_poids,
            gobj_pa_normal = :gobj_pa_normal,
            gobj_pa_eclair = :gobj_pa_eclair,
            gobj_description = :gobj_description,
            gobj_frune_cod = :gobj_frune_cod,
            gobj_rune_position = :gobj_rune_position,
            gobj_distance = :gobj_distance,
            gobj_nettoyage = :gobj_nettoyage,
            gobj_deposable = :gobj_deposable,
            gobj_visible = :gobj_visible,
            gobj_usure = :gobj_usure,
            gobj_echoppe = :gobj_echoppe,
            gobj_vampire = :gobj_vampire,
            gobj_absorbe = :gobj_absorbe,
            gobj_url = :gobj_url,
            gobj_obon_cod = :gobj_obon_cod,
            gobj_seuil_force = :gobj_seuil_force,
            gobj_seuil_dex = :gobj_seuil_dex,
            gobj_nb_mains = :gobj_nb_mains,
            gobj_poison = :gobj_poison,
            gobj_portee = :gobj_portee,
            gobj_regen = :gobj_regen,
            gobj_aura_feu = :gobj_aura_feu,
            gobj_bonus_vue = :gobj_bonus_vue,
            gobj_critique = :gobj_critique,
            gobj_bonus_armure = :gobj_bonus_armure,
            gobj_sort_cod = :gobj_sort_cod,
            gobj_chance_drop = :gobj_chance_drop,
            gobj_chance_enchant = :gobj_chance_enchant,
            gobj_echoppe_vente = :gobj_echoppe_vente,
            gobj_echoppe_stock = :gobj_echoppe_stock,
            gobj_echoppe_destock = :gobj_echoppe_destock,
            gobj_stabilite = :gobj_stabilite,
            gobj_arme_naturelle = :gobj_arme_naturelle,
            gobj_image = :gobj_image,
            gobj_image_generique = :gobj_image_generique,
            gobj_niv_parchemin = :gobj_niv_parchemin,
            gobj_niv_peau = :gobj_niv_peau,
            gobj_perce_armure = :gobj_perce_armure,
            gobj_coeff_percearmure = :gobj_coeff_percearmure,
            gobj_niveau_min = :gobj_niveau_min,
            gobj_desequipable = :gobj_desequipable,
            gobj_postable = :gobj_postable,
            gobj_chance_drop_monstre = :gobj_chance_drop_monstre                        where gobj_cod = :gobj_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":gobj_cod"                 => $this->gobj_cod,
                                      ":gobj_nom"                 => $this->gobj_nom,
                                      ":gobj_nom_generique"       => $this->gobj_nom_generique,
                                      ":gobj_tobj_cod"            => $this->gobj_tobj_cod,
                                      ":gobj_bonus_cod"           => $this->gobj_bonus_cod,
                                      ":gobj_valeur"              => $this->gobj_valeur,
                                      ":gobj_obcar_cod"           => $this->gobj_obcar_cod,
                                      ":gobj_comp_cod"            => $this->gobj_comp_cod,
                                      ":gobj_poids"               => $this->gobj_poids,
                                      ":gobj_pa_normal"           => $this->gobj_pa_normal,
                                      ":gobj_pa_eclair"           => $this->gobj_pa_eclair,
                                      ":gobj_description"         => $this->gobj_description,
                                      ":gobj_frune_cod"           => $this->gobj_frune_cod,
                                      ":gobj_rune_position"       => $this->gobj_rune_position,
                                      ":gobj_distance"            => $this->gobj_distance,
                                      ":gobj_nettoyage"           => $this->gobj_nettoyage,
                                      ":gobj_deposable"           => $this->gobj_deposable,
                                      ":gobj_visible"             => $this->gobj_visible,
                                      ":gobj_usure"               => $this->gobj_usure,
                                      ":gobj_echoppe"             => $this->gobj_echoppe,
                                      ":gobj_vampire"             => $this->gobj_vampire,
                                      ":gobj_absorbe"             => $this->gobj_absorbe,
                                      ":gobj_url"                 => $this->gobj_url,
                                      ":gobj_obon_cod"            => $this->gobj_obon_cod,
                                      ":gobj_seuil_force"         => $this->gobj_seuil_force,
                                      ":gobj_seuil_dex"           => $this->gobj_seuil_dex,
                                      ":gobj_nb_mains"            => $this->gobj_nb_mains,
                                      ":gobj_poison"              => $this->gobj_poison,
                                      ":gobj_portee"              => $this->gobj_portee,
                                      ":gobj_regen"               => $this->gobj_regen,
                                      ":gobj_aura_feu"            => $this->gobj_aura_feu,
                                      ":gobj_bonus_vue"           => $this->gobj_bonus_vue,
                                      ":gobj_critique"            => $this->gobj_critique,
                                      ":gobj_bonus_armure"        => $this->gobj_bonus_armure,
                                      ":gobj_sort_cod"            => $this->gobj_sort_cod,
                                      ":gobj_chance_drop"         => $this->gobj_chance_drop,
                                      ":gobj_chance_enchant"      => $this->gobj_chance_enchant,
                                      ":gobj_echoppe_vente"       => $this->gobj_echoppe_vente,
                                      ":gobj_echoppe_stock"       => $this->gobj_echoppe_stock,
                                      ":gobj_echoppe_destock"     => $this->gobj_echoppe_destock,
                                      ":gobj_stabilite"           => $this->gobj_stabilite,
                                      ":gobj_arme_naturelle"      => $this->gobj_arme_naturelle,
                                      ":gobj_image"               => $this->gobj_image,
                                      ":gobj_image_generique"     => $this->gobj_image_generique,
                                      ":gobj_niv_parchemin"       => $this->gobj_niv_parchemin,
                                      ":gobj_niv_peau"            => $this->gobj_niv_peau,
                                      ":gobj_perce_armure"        => $this->gobj_perce_armure,
                                      ":gobj_coeff_percearmure"   => $this->gobj_coeff_percearmure,
                                      ":gobj_niveau_min"          => $this->gobj_niveau_min,
                                      ":gobj_desequipable"        => $this->gobj_desequipable,
                                      ":gobj_postable"            => $this->gobj_postable,
                                      ":gobj_chance_drop_monstre" => $this->gobj_chance_drop_monstre,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return objet_generique[]
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select gobj_cod  from objet_generique order by gobj_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new objet_generique;
            $temp->charge($result["gobj_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByValeur($min, $max)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select gobj_cod  from objet_generique
            where gobj_valeur >= :min
             and gob_valeur <= :max 
             order by gobj_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":min" => $min,
                                    ":max" => $max
                                ), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new objet_generique;
            $temp->charge($result["gobj_cod"]);
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
                    $req    = "select gobj_cod  from objet_generique where " . substr($name, 6) . " = ? order by gobj_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new objet_generique;
                        $temp->charge($result["gobj_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table objet_generique');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}