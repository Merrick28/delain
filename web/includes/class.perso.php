<?php
/**
 * includes/class.perso.php
 */

/**
 * Class perso
 *
 * Gère les objets BDD de la table perso
 */
class perso
{
    public $perso_cod;
    public $perso_for;
    public $perso_dex;
    public $perso_int;
    public $perso_con;
    public $perso_for_init;
    public $perso_dex_init;
    public $perso_int_init;
    public $perso_con_init;
    public $perso_sex;
    public $perso_race_cod;
    public $perso_pv                   = 0;
    public $perso_pv_max;
    public $perso_dlt;
    public $perso_temps_tour;
    public $perso_email;
    public $perso_dcreat;
    public $perso_validation;
    public $perso_actif                = 'N';
    public $perso_pa                   = 12;
    public $perso_der_connex;
    public $perso_des_regen            = 1;
    public $perso_valeur_regen         = 3;
    public $perso_vue                  = 3;
    public $perso_po                   = 0;
    public $perso_nb_esquive;
    public $perso_niveau               = 1;
    public $perso_type_perso           = 1;
    public $perso_amelioration_vue;
    public $perso_amelioration_regen;
    public $perso_amelioration_degats;
    public $perso_amelioration_armure;
    public $perso_nb_des_degats;
    public $perso_val_des_degats;
    public $perso_cible;
    public $perso_enc_max;
    public $perso_description;
    public $perso_nb_mort;
    public $perso_nb_monstre_tue;
    public $perso_nb_joueur_tue;
    public $perso_reputation           = 0;
    public $perso_avatar;
    public $perso_kharma;
    public $perso_amel_deg_dex;
    public $perso_nom;
    public $perso_gmon_cod;
    public $perso_renommee;
    public $perso_dirige_admin;
    public $perso_lower_perso_nom;
    public $perso_sta_combat;
    public $perso_sta_hors_combat;
    public $perso_utl_pa_rest          = 1;
    public $perso_tangible             = 'O';
    public $perso_nb_tour_intangible   = 0;
    public $perso_capa_repar;
    public $perso_nb_amel_repar        = 0;
    public $perso_amelioration_nb_sort = 0;
    public $perso_renommee_magie       = 0;
    public $perso_vampirisme           = 0;
    public $perso_niveau_vampire       = 0;
    public $perso_admin_echoppe;
    public $perso_nb_amel_comp         = 0;
    public $perso_nb_receptacle        = 0;
    public $perso_nb_amel_chance_memo  = 0;
    public $perso_priere               = 0;
    public $perso_dfin;
    public $perso_px                   = 0;
    public $perso_taille               = 3;
    public $perso_admin_echoppe_noir   = 'N';
    public $perso_use_repart_auto      = 1;
    public $perso_pnj                  = 0;
    public $perso_redispatch           = 'N';
    public $perso_nb_redist            = 0;
    public $perso_mcom_cod             = 0;
    public $perso_nb_ch_mcom           = 0;
    public $perso_piq_rap_env          = 1;
    public $perso_ancien_avatar;
    public $perso_nb_crap              = 0;
    public $perso_nb_embr              = 0;
    public $perso_crapaud              = 0;
    public $perso_dchange_mcom;
    public $perso_prestige             = 0;
    public $perso_av_mod               = 0;
    public $perso_mail_inactif_envoye;
    public $perso_test;
    public $perso_nb_spe               = 1;
    public $perso_compt_pvp            = 0;
    public $perso_dmodif_compt_pvp;
    public $perso_effets_auto          = 1;
    public $perso_quete;
    public $perso_tuteur               = false;
    public $perso_voie_magique         = 0;
    public $perso_energie              = 0;
    public $perso_desc_long;
    public $perso_nb_mort_arene        = 0;
    public $perso_nb_joueur_tue_arene  = 0;
    public $perso_dfin_tangible;
    public $perso_renommee_artisanat   = 0;
    public $perso_avatar_version       = 0;
    public $perso_etage_origine;
    public $perso_monstre_attaque_monstre;
    public $perso_mortel               = null;
    public $alterego                   = 0;
    public $perso_monture ;
    public $perso_misc_param ;
    //
    public $position;
    public $guilde;
    public $avatar;
    public $perso_vide = false;
    public $msg_non_lu;
    public $avatar_largeur;
    public $avatar_hauteur;
    public $barre_divine;
    //
    // Variables qui ne serviront que pour la vue
    //


    public function __construct()
    {
        $this->perso_dcreat           = date('Y-m-d H:i:s');
        $this->perso_der_connex       = date('Y-m-d H:i:s');
        $this->perso_dchange_mcom     = date('Y-m-d H:i:s');
        $this->perso_dmodif_compt_pvp = date('Y-m-d H:i:s');
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     * @global bddpdo $pdo
     */
    public function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req
                  = "insert into perso (
            perso_cod,
            perso_for,
            perso_dex,
            perso_int,
            perso_con,
            perso_for_init,
            perso_dex_init,
            perso_int_init,
            perso_con_init,
            perso_sex,
            perso_race_cod,
            perso_pv,
            perso_pv_max,
            perso_dlt,
            perso_temps_tour,
            perso_email,
            perso_dcreat,
            perso_validation,
            perso_actif,
            perso_pa,
            perso_der_connex,
            perso_des_regen,
            perso_valeur_regen,
            perso_vue,
            perso_po,
            perso_nb_esquive,
            perso_niveau,
            perso_type_perso,
            perso_amelioration_vue,
            perso_amelioration_regen,
            perso_amelioration_degats,
            perso_amelioration_armure,
            perso_nb_des_degats,
            perso_val_des_degats,
            perso_cible,
            perso_enc_max,
            perso_description,
            perso_nb_mort,
            perso_nb_monstre_tue,
            perso_nb_joueur_tue,
            perso_reputation,
            perso_avatar,
            perso_kharma,
            perso_amel_deg_dex,
            perso_nom,
            perso_gmon_cod,
            perso_renommee,
            perso_dirige_admin,
            perso_lower_perso_nom,
            perso_sta_combat,
            perso_sta_hors_combat,
            perso_utl_pa_rest,
            perso_tangible,
            perso_nb_tour_intangible,
            perso_capa_repar,
            perso_nb_amel_repar,
            perso_amelioration_nb_sort,
            perso_renommee_magie,
            perso_vampirisme,
            perso_niveau_vampire,
            perso_admin_echoppe,
            perso_nb_amel_comp,
            perso_nb_receptacle,
            perso_nb_amel_chance_memo,
            perso_priere,
            perso_dfin,
            perso_px,
            perso_taille,
            perso_admin_echoppe_noir,
            perso_use_repart_auto,
            perso_pnj,
            perso_redispatch,
            perso_nb_redist,
            perso_mcom_cod,
            perso_nb_ch_mcom,
            perso_piq_rap_env,
            perso_ancien_avatar,
            perso_nb_crap,
            perso_nb_embr,
            perso_crapaud,
            perso_dchange_mcom,
            perso_prestige,
            perso_av_mod,
            perso_mail_inactif_envoye,
            perso_test,
            perso_nb_spe,
            perso_compt_pvp,
            perso_dmodif_compt_pvp,
            perso_effets_auto,
            perso_quete,
            perso_tuteur,
            perso_voie_magique,
            perso_energie,
            perso_desc_long,
            perso_nb_mort_arene,
            perso_nb_joueur_tue_arene,
            perso_dfin_tangible,
            perso_renommee_artisanat,
            perso_avatar_version,
            perso_etage_origine,
            perso_monstre_attaque_monstre,
            perso_mortel,
            alterego,
            perso_monture,
            perso_misc_param                        )
                    values
                    (
                        nextval('seq_perso'),
                     :perso_for,
                        :perso_dex,
                        :perso_int,
                        :perso_con,
                        :perso_for_init,
                        :perso_dex_init,
                        :perso_int_init,
                        :perso_con_init,
                        :perso_sex,
                        :perso_race_cod,
                        :perso_pv,
                        :perso_pv_max,
                        :perso_dlt,
                        :perso_temps_tour,
                        :perso_email,
                        :perso_dcreat,
                        :perso_validation,
                        :perso_actif,
                        :perso_pa,
                        :perso_der_connex,
                        :perso_des_regen,
                        :perso_valeur_regen,
                        :perso_vue,
                        :perso_po,
                        :perso_nb_esquive,
                        :perso_niveau,
                        :perso_type_perso,
                        :perso_amelioration_vue,
                        :perso_amelioration_regen,
                        :perso_amelioration_degats,
                        :perso_amelioration_armure,
                        :perso_nb_des_degats,
                        :perso_val_des_degats,
                        :perso_cible,
                        :perso_enc_max,
                        :perso_description,
                        :perso_nb_mort,
                        :perso_nb_monstre_tue,
                        :perso_nb_joueur_tue,
                        :perso_reputation,
                        :perso_avatar,
                        :perso_kharma,
                        :perso_amel_deg_dex,
                        :perso_nom,
                        :perso_gmon_cod,
                        :perso_renommee,
                        :perso_dirige_admin,
                        :perso_lower_perso_nom,
                        :perso_sta_combat,
                        :perso_sta_hors_combat,
                        :perso_utl_pa_rest,
                        :perso_tangible,
                        :perso_nb_tour_intangible,
                        :perso_capa_repar,
                        :perso_nb_amel_repar,
                        :perso_amelioration_nb_sort,
                        :perso_renommee_magie,
                        :perso_vampirisme,
                        :perso_niveau_vampire,
                        :perso_admin_echoppe,
                        :perso_nb_amel_comp,
                        :perso_nb_receptacle,
                        :perso_nb_amel_chance_memo,
                        :perso_priere,
                        :perso_dfin,
                        :perso_px,
                        :perso_taille,
                        :perso_admin_echoppe_noir,
                        :perso_use_repart_auto,
                        :perso_pnj,
                        :perso_redispatch,
                        :perso_nb_redist,
                        :perso_mcom_cod,
                        :perso_nb_ch_mcom,
                        :perso_piq_rap_env,
                        :perso_ancien_avatar,
                        :perso_nb_crap,
                        :perso_nb_embr,
                        :perso_crapaud,
                        :perso_dchange_mcom,
                        :perso_prestige,
                        :perso_av_mod,
                        :perso_mail_inactif_envoye,
                        :perso_test,
                        :perso_nb_spe,
                        :perso_compt_pvp,
                        :perso_dmodif_compt_pvp,
                        :perso_effets_auto,
                        :perso_quete,
                        :perso_tuteur,
                        :perso_voie_magique,
                        :perso_energie,
                        :perso_desc_long,
                        :perso_nb_mort_arene,
                        :perso_nb_joueur_tue_arene,
                        :perso_dfin_tangible,
                        :perso_renommee_artisanat,
                        :perso_avatar_version,
                        :perso_etage_origine,
                        :perso_monstre_attaque_monstre,
                        :perso_mortel,
                        :alterego,
                        :perso_monture ,
                        :perso_misc_param                        )
    returning perso_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":perso_for"                     => $this->perso_for,
                                      ":perso_dex"                     => $this->perso_dex,
                                      ":perso_int"                     => $this->perso_int,
                                      ":perso_con"                     => $this->perso_con,
                                      ":perso_for_init"                => $this->perso_for_init,
                                      ":perso_dex_init"                => $this->perso_dex_init,
                                      ":perso_int_init"                => $this->perso_int_init,
                                      ":perso_con_init"                => $this->perso_con_init,
                                      ":perso_sex"                     => $this->perso_sex,
                                      ":perso_race_cod"                => $this->perso_race_cod,
                                      ":perso_pv"                      => $this->perso_pv,
                                      ":perso_pv_max"                  => $this->perso_pv_max,
                                      ":perso_dlt"                     => $this->perso_dlt,
                                      ":perso_temps_tour"              => $this->perso_temps_tour,
                                      ":perso_email"                   => $this->perso_email,
                                      ":perso_dcreat"                  => $this->perso_dcreat,
                                      ":perso_validation"              => $this->perso_validation,
                                      ":perso_actif"                   => $this->perso_actif,
                                      ":perso_pa"                      => $this->perso_pa,
                                      ":perso_der_connex"              => $this->perso_der_connex,
                                      ":perso_des_regen"               => $this->perso_des_regen,
                                      ":perso_valeur_regen"            => $this->perso_valeur_regen,
                                      ":perso_vue"                     => $this->perso_vue,
                                      ":perso_po"                      => $this->perso_po,
                                      ":perso_nb_esquive"              => $this->perso_nb_esquive,
                                      ":perso_niveau"                  => $this->perso_niveau,
                                      ":perso_type_perso"              => $this->perso_type_perso,
                                      ":perso_amelioration_vue"        => $this->perso_amelioration_vue,
                                      ":perso_amelioration_regen"      => $this->perso_amelioration_regen,
                                      ":perso_amelioration_degats"     => $this->perso_amelioration_degats,
                                      ":perso_amelioration_armure"     => $this->perso_amelioration_armure,
                                      ":perso_nb_des_degats"           => $this->perso_nb_des_degats,
                                      ":perso_val_des_degats"          => $this->perso_val_des_degats,
                                      ":perso_cible"                   => $this->perso_cible,
                                      ":perso_enc_max"                 => $this->perso_enc_max,
                                      ":perso_description"             => $this->perso_description,
                                      ":perso_nb_mort"                 => $this->perso_nb_mort,
                                      ":perso_nb_monstre_tue"          => $this->perso_nb_monstre_tue,
                                      ":perso_nb_joueur_tue"           => $this->perso_nb_joueur_tue,
                                      ":perso_reputation"              => $this->perso_reputation,
                                      ":perso_avatar"                  => $this->perso_avatar,
                                      ":perso_kharma"                  => $this->perso_kharma,
                                      ":perso_amel_deg_dex"            => $this->perso_amel_deg_dex,
                                      ":perso_nom"                     => $this->perso_nom,
                                      ":perso_gmon_cod"                => $this->perso_gmon_cod,
                                      ":perso_renommee"                => $this->perso_renommee,
                                      ":perso_dirige_admin"            => $this->perso_dirige_admin,
                                      ":perso_lower_perso_nom"         => $this->perso_lower_perso_nom,
                                      ":perso_sta_combat"              => $this->perso_sta_combat,
                                      ":perso_sta_hors_combat"         => $this->perso_sta_hors_combat,
                                      ":perso_utl_pa_rest"             => $this->perso_utl_pa_rest,
                                      ":perso_tangible"                => $this->perso_tangible,
                                      ":perso_nb_tour_intangible"      => $this->perso_nb_tour_intangible,
                                      ":perso_capa_repar"              => $this->perso_capa_repar,
                                      ":perso_nb_amel_repar"           => $this->perso_nb_amel_repar,
                                      ":perso_amelioration_nb_sort"    => $this->perso_amelioration_nb_sort,
                                      ":perso_renommee_magie"          => $this->perso_renommee_magie,
                                      ":perso_vampirisme"              => $this->perso_vampirisme,
                                      ":perso_niveau_vampire"          => $this->perso_niveau_vampire,
                                      ":perso_admin_echoppe"           => $this->perso_admin_echoppe,
                                      ":perso_nb_amel_comp"            => $this->perso_nb_amel_comp,
                                      ":perso_nb_receptacle"           => $this->perso_nb_receptacle,
                                      ":perso_nb_amel_chance_memo"     => $this->perso_nb_amel_chance_memo,
                                      ":perso_priere"                  => $this->perso_priere,
                                      ":perso_dfin"                    => $this->perso_dfin,
                                      ":perso_px"                      => $this->perso_px,
                                      ":perso_taille"                  => $this->perso_taille,
                                      ":perso_admin_echoppe_noir"      => $this->perso_admin_echoppe_noir,
                                      ":perso_use_repart_auto"         => $this->perso_use_repart_auto,
                                      ":perso_pnj"                     => $this->perso_pnj,
                                      ":perso_redispatch"              => $this->perso_redispatch,
                                      ":perso_nb_redist"               => $this->perso_nb_redist,
                                      ":perso_mcom_cod"                => $this->perso_mcom_cod,
                                      ":perso_nb_ch_mcom"              => $this->perso_nb_ch_mcom,
                                      ":perso_piq_rap_env"             => $this->perso_piq_rap_env,
                                      ":perso_ancien_avatar"           => $this->perso_ancien_avatar,
                                      ":perso_nb_crap"                 => $this->perso_nb_crap,
                                      ":perso_nb_embr"                 => $this->perso_nb_embr,
                                      ":perso_crapaud"                 => $this->perso_crapaud,
                                      ":perso_dchange_mcom"            => $this->perso_dchange_mcom,
                                      ":perso_prestige"                => $this->perso_prestige,
                                      ":perso_av_mod"                  => $this->perso_av_mod,
                                      ":perso_mail_inactif_envoye"     => $this->perso_mail_inactif_envoye,
                                      ":perso_test"                    => $this->perso_test,
                                      ":perso_nb_spe"                  => $this->perso_nb_spe,
                                      ":perso_compt_pvp"               => $this->perso_compt_pvp,
                                      ":perso_dmodif_compt_pvp"        => $this->perso_dmodif_compt_pvp,
                                      ":perso_effets_auto"             => $this->perso_effets_auto,
                                      ":perso_quete"                   => $this->perso_quete,
                                      ":perso_tuteur"                  => ($this->perso_tuteur ? 1 : 0),
                                      ":perso_voie_magique"            => $this->perso_voie_magique,
                                      ":perso_energie"                 => $this->perso_energie,
                                      ":perso_desc_long"               => $this->perso_desc_long,
                                      ":perso_nb_mort_arene"           => $this->perso_nb_mort_arene,
                                      ":perso_nb_joueur_tue_arene"     => $this->perso_nb_joueur_tue_arene,
                                      ":perso_dfin_tangible"           => $this->perso_dfin_tangible,
                                      ":perso_renommee_artisanat"      => $this->perso_renommee_artisanat,
                                      ":perso_avatar_version"          => $this->perso_avatar_version,
                                      ":perso_etage_origine"           => $this->perso_etage_origine,
                                      ":perso_monstre_attaque_monstre" => $this->perso_monstre_attaque_monstre,
                                      ":perso_mortel"                  => $this->perso_mortel,
                                      ":alterego"                      => $this->alterego,
                                      ":perso_monture"                 => $this->perso_monture,
                                      ":perso_misc_param"              => $this->perso_misc_param,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req
                  = "update perso
                    set
            perso_for = :perso_for,
            perso_dex = :perso_dex,
            perso_int = :perso_int,
            perso_con = :perso_con,
            perso_for_init = :perso_for_init,
            perso_dex_init = :perso_dex_init,
            perso_int_init = :perso_int_init,
            perso_con_init = :perso_con_init,
            perso_sex = :perso_sex,
            perso_race_cod = :perso_race_cod,
            perso_pv = :perso_pv,
            perso_pv_max = :perso_pv_max,
            perso_dlt = :perso_dlt,
            perso_temps_tour = :perso_temps_tour,
            perso_email = :perso_email,
            perso_dcreat = :perso_dcreat,
            perso_validation = :perso_validation,
            perso_actif = :perso_actif,
            perso_pa = :perso_pa,
            perso_der_connex = :perso_der_connex,
            perso_des_regen = :perso_des_regen,
            perso_valeur_regen = :perso_valeur_regen,
            perso_vue = :perso_vue,
            perso_po = :perso_po,
            perso_nb_esquive = :perso_nb_esquive,
            perso_niveau = :perso_niveau,
            perso_type_perso = :perso_type_perso,
            perso_amelioration_vue = :perso_amelioration_vue,
            perso_amelioration_regen = :perso_amelioration_regen,
            perso_amelioration_degats = :perso_amelioration_degats,
            perso_amelioration_armure = :perso_amelioration_armure,
            perso_nb_des_degats = :perso_nb_des_degats,
            perso_val_des_degats = :perso_val_des_degats,
            perso_cible = :perso_cible,
            perso_enc_max = :perso_enc_max,
            perso_description = :perso_description,
            perso_nb_mort = :perso_nb_mort,
            perso_nb_monstre_tue = :perso_nb_monstre_tue,
            perso_nb_joueur_tue = :perso_nb_joueur_tue,
            perso_reputation = :perso_reputation,
            perso_avatar = :perso_avatar,
            perso_kharma = :perso_kharma,
            perso_amel_deg_dex = :perso_amel_deg_dex,
            perso_nom = :perso_nom,
            perso_gmon_cod = :perso_gmon_cod,
            perso_renommee = :perso_renommee,
            perso_dirige_admin = :perso_dirige_admin,
            perso_lower_perso_nom = :perso_lower_perso_nom,
            perso_sta_combat = :perso_sta_combat,
            perso_sta_hors_combat = :perso_sta_hors_combat,
            perso_utl_pa_rest = :perso_utl_pa_rest,
            perso_tangible = :perso_tangible,
            perso_nb_tour_intangible = :perso_nb_tour_intangible,
            perso_capa_repar = :perso_capa_repar,
            perso_nb_amel_repar = :perso_nb_amel_repar,
            perso_amelioration_nb_sort = :perso_amelioration_nb_sort,
            perso_renommee_magie = :perso_renommee_magie,
            perso_vampirisme = :perso_vampirisme,
            perso_niveau_vampire = :perso_niveau_vampire,
            perso_admin_echoppe = :perso_admin_echoppe,
            perso_nb_amel_comp = :perso_nb_amel_comp,
            perso_nb_receptacle = :perso_nb_receptacle,
            perso_nb_amel_chance_memo = :perso_nb_amel_chance_memo,
            perso_priere = :perso_priere,
            perso_dfin = :perso_dfin,
            perso_px = :perso_px,
            perso_taille = :perso_taille,
            perso_admin_echoppe_noir = :perso_admin_echoppe_noir,
            perso_use_repart_auto = :perso_use_repart_auto,
            perso_pnj = :perso_pnj,
            perso_redispatch = :perso_redispatch,
            perso_nb_redist = :perso_nb_redist,
            perso_mcom_cod = :perso_mcom_cod,
            perso_nb_ch_mcom = :perso_nb_ch_mcom,
            perso_piq_rap_env = :perso_piq_rap_env,
            perso_ancien_avatar = :perso_ancien_avatar,
            perso_nb_crap = :perso_nb_crap,
            perso_nb_embr = :perso_nb_embr,
            perso_crapaud = :perso_crapaud,
            perso_dchange_mcom = :perso_dchange_mcom,
            perso_prestige = :perso_prestige,
            perso_av_mod = :perso_av_mod,
            perso_mail_inactif_envoye = :perso_mail_inactif_envoye,
            perso_test = :perso_test,
            perso_nb_spe = :perso_nb_spe,
            perso_compt_pvp = :perso_compt_pvp,
            perso_dmodif_compt_pvp = :perso_dmodif_compt_pvp,
            perso_effets_auto = :perso_effets_auto,
            perso_quete = :perso_quete,
            perso_tuteur = :perso_tuteur,
            perso_voie_magique = :perso_voie_magique,
            perso_energie = :perso_energie,
            perso_desc_long = :perso_desc_long,
            perso_nb_mort_arene = :perso_nb_mort_arene,
            perso_nb_joueur_tue_arene = :perso_nb_joueur_tue_arene,
            perso_dfin_tangible = :perso_dfin_tangible,
            perso_renommee_artisanat = :perso_renommee_artisanat,
            perso_avatar_version = :perso_avatar_version,
            perso_etage_origine = :perso_etage_origine,
            perso_monstre_attaque_monstre = :perso_monstre_attaque_monstre,
            perso_mortel = :perso_mortel,
            alterego = :alterego,
            perso_monture = :perso_monture  ,
            perso_misc_param = :perso_misc_param                        where perso_cod = :perso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":perso_cod"                     => $this->perso_cod,
                                      ":perso_for"                     => $this->perso_for,
                                      ":perso_dex"                     => $this->perso_dex,
                                      ":perso_int"                     => $this->perso_int,
                                      ":perso_con"                     => $this->perso_con,
                                      ":perso_for_init"                => $this->perso_for_init,
                                      ":perso_dex_init"                => $this->perso_dex_init,
                                      ":perso_int_init"                => $this->perso_int_init,
                                      ":perso_con_init"                => $this->perso_con_init,
                                      ":perso_sex"                     => $this->perso_sex,
                                      ":perso_race_cod"                => $this->perso_race_cod,
                                      ":perso_pv"                      => $this->perso_pv,
                                      ":perso_pv_max"                  => $this->perso_pv_max,
                                      ":perso_dlt"                     => $this->perso_dlt,
                                      ":perso_temps_tour"              => $this->perso_temps_tour,
                                      ":perso_email"                   => $this->perso_email,
                                      ":perso_dcreat"                  => $this->perso_dcreat,
                                      ":perso_validation"              => $this->perso_validation,
                                      ":perso_actif"                   => $this->perso_actif,
                                      ":perso_pa"                      => $this->perso_pa,
                                      ":perso_der_connex"              => $this->perso_der_connex,
                                      ":perso_des_regen"               => $this->perso_des_regen,
                                      ":perso_valeur_regen"            => $this->perso_valeur_regen,
                                      ":perso_vue"                     => $this->perso_vue,
                                      ":perso_po"                      => $this->perso_po,
                                      ":perso_nb_esquive"              => $this->perso_nb_esquive,
                                      ":perso_niveau"                  => $this->perso_niveau,
                                      ":perso_type_perso"              => $this->perso_type_perso,
                                      ":perso_amelioration_vue"        => $this->perso_amelioration_vue,
                                      ":perso_amelioration_regen"      => $this->perso_amelioration_regen,
                                      ":perso_amelioration_degats"     => $this->perso_amelioration_degats,
                                      ":perso_amelioration_armure"     => $this->perso_amelioration_armure,
                                      ":perso_nb_des_degats"           => $this->perso_nb_des_degats,
                                      ":perso_val_des_degats"          => $this->perso_val_des_degats,
                                      ":perso_cible"                   => $this->perso_cible,
                                      ":perso_enc_max"                 => $this->perso_enc_max,
                                      ":perso_description"             => $this->perso_description,
                                      ":perso_nb_mort"                 => $this->perso_nb_mort,
                                      ":perso_nb_monstre_tue"          => $this->perso_nb_monstre_tue,
                                      ":perso_nb_joueur_tue"           => $this->perso_nb_joueur_tue,
                                      ":perso_reputation"              => $this->perso_reputation,
                                      ":perso_avatar"                  => $this->perso_avatar,
                                      ":perso_kharma"                  => $this->perso_kharma,
                                      ":perso_amel_deg_dex"            => $this->perso_amel_deg_dex,
                                      ":perso_nom"                     => $this->perso_nom,
                                      ":perso_gmon_cod"                => $this->perso_gmon_cod,
                                      ":perso_renommee"                => $this->perso_renommee,
                                      ":perso_dirige_admin"            => $this->perso_dirige_admin,
                                      ":perso_lower_perso_nom"         => $this->perso_lower_perso_nom,
                                      ":perso_sta_combat"              => $this->perso_sta_combat,
                                      ":perso_sta_hors_combat"         => $this->perso_sta_hors_combat,
                                      ":perso_utl_pa_rest"             => $this->perso_utl_pa_rest,
                                      ":perso_tangible"                => $this->perso_tangible,
                                      ":perso_nb_tour_intangible"      => $this->perso_nb_tour_intangible,
                                      ":perso_capa_repar"              => $this->perso_capa_repar,
                                      ":perso_nb_amel_repar"           => $this->perso_nb_amel_repar,
                                      ":perso_amelioration_nb_sort"    => $this->perso_amelioration_nb_sort,
                                      ":perso_renommee_magie"          => $this->perso_renommee_magie,
                                      ":perso_vampirisme"              => $this->perso_vampirisme,
                                      ":perso_niveau_vampire"          => $this->perso_niveau_vampire,
                                      ":perso_admin_echoppe"           => $this->perso_admin_echoppe,
                                      ":perso_nb_amel_comp"            => $this->perso_nb_amel_comp,
                                      ":perso_nb_receptacle"           => $this->perso_nb_receptacle,
                                      ":perso_nb_amel_chance_memo"     => $this->perso_nb_amel_chance_memo,
                                      ":perso_priere"                  => $this->perso_priere,
                                      ":perso_dfin"                    => $this->perso_dfin,
                                      ":perso_px"                      => $this->perso_px,
                                      ":perso_taille"                  => $this->perso_taille,
                                      ":perso_admin_echoppe_noir"      => $this->perso_admin_echoppe_noir,
                                      ":perso_use_repart_auto"         => $this->perso_use_repart_auto,
                                      ":perso_pnj"                     => $this->perso_pnj,
                                      ":perso_redispatch"              => $this->perso_redispatch,
                                      ":perso_nb_redist"               => $this->perso_nb_redist,
                                      ":perso_mcom_cod"                => $this->perso_mcom_cod,
                                      ":perso_nb_ch_mcom"              => $this->perso_nb_ch_mcom,
                                      ":perso_piq_rap_env"             => $this->perso_piq_rap_env,
                                      ":perso_ancien_avatar"           => $this->perso_ancien_avatar,
                                      ":perso_nb_crap"                 => $this->perso_nb_crap,
                                      ":perso_nb_embr"                 => $this->perso_nb_embr,
                                      ":perso_crapaud"                 => $this->perso_crapaud,
                                      ":perso_dchange_mcom"            => $this->perso_dchange_mcom,
                                      ":perso_prestige"                => $this->perso_prestige,
                                      ":perso_av_mod"                  => $this->perso_av_mod,
                                      ":perso_mail_inactif_envoye"     => $this->perso_mail_inactif_envoye,
                                      ":perso_test"                    => $this->perso_test,
                                      ":perso_nb_spe"                  => $this->perso_nb_spe,
                                      ":perso_compt_pvp"               => $this->perso_compt_pvp,
                                      ":perso_dmodif_compt_pvp"        => $this->perso_dmodif_compt_pvp,
                                      ":perso_effets_auto"             => $this->perso_effets_auto,
                                      ":perso_quete"                   => $this->perso_quete,
                                      ":perso_tuteur"                  => ($this->perso_tuteur ? 1 : 0),
                                      ":perso_voie_magique"            => $this->perso_voie_magique,
                                      ":perso_energie"                 => $this->perso_energie,
                                      ":perso_desc_long"               => $this->perso_desc_long,
                                      ":perso_nb_mort_arene"           => $this->perso_nb_mort_arene,
                                      ":perso_nb_joueur_tue_arene"     => $this->perso_nb_joueur_tue_arene,
                                      ":perso_dfin_tangible"           => $this->perso_dfin_tangible,
                                      ":perso_renommee_artisanat"      => $this->perso_renommee_artisanat,
                                      ":perso_avatar_version"          => $this->perso_avatar_version,
                                      ":perso_etage_origine"           => $this->perso_etage_origine,
                                      ":perso_monstre_attaque_monstre" => $this->perso_monstre_attaque_monstre,
                                      ":perso_mortel"                  => $this->perso_mortel,
                                      ":alterego"                      => $this->alterego,
                                      ":perso_monture"                 => $this->perso_monture,
                                      ":perso_misc_param"              => $this->perso_misc_param,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de perso
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    public function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso where perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->perso_cod                     = $result['perso_cod'];
        $this->perso_for                     = $result['perso_for'];
        $this->perso_dex                     = $result['perso_dex'];
        $this->perso_int                     = $result['perso_int'];
        $this->perso_con                     = $result['perso_con'];
        $this->perso_for_init                = $result['perso_for_init'];
        $this->perso_dex_init                = $result['perso_dex_init'];
        $this->perso_int_init                = $result['perso_int_init'];
        $this->perso_con_init                = $result['perso_con_init'];
        $this->perso_sex                     = $result['perso_sex'];
        $this->perso_race_cod                = $result['perso_race_cod'];
        $this->perso_pv                      = $result['perso_pv'];
        $this->perso_pv_max                  = $result['perso_pv_max'];
        $this->perso_dlt                     = $result['perso_dlt'];
        $this->perso_temps_tour              = $result['perso_temps_tour'];
        $this->perso_email                   = $result['perso_email'];
        $this->perso_dcreat                  = $result['perso_dcreat'];
        $this->perso_validation              = $result['perso_validation'];
        $this->perso_actif                   = $result['perso_actif'];
        $this->perso_pa                      = $result['perso_pa'];
        $this->perso_der_connex              = $result['perso_der_connex'];
        $this->perso_des_regen               = $result['perso_des_regen'];
        $this->perso_valeur_regen            = $result['perso_valeur_regen'];
        $this->perso_vue                     = $result['perso_vue'];
        $this->perso_po                      = $result['perso_po'];
        $this->perso_nb_esquive              = $result['perso_nb_esquive'];
        $this->perso_niveau                  = $result['perso_niveau'];
        $this->perso_type_perso              = $result['perso_type_perso'];
        $this->perso_amelioration_vue        = $result['perso_amelioration_vue'];
        $this->perso_amelioration_regen      = $result['perso_amelioration_regen'];
        $this->perso_amelioration_degats     = $result['perso_amelioration_degats'];
        $this->perso_amelioration_armure     = $result['perso_amelioration_armure'];
        $this->perso_nb_des_degats           = $result['perso_nb_des_degats'];
        $this->perso_val_des_degats          = $result['perso_val_des_degats'];
        $this->perso_cible                   = $result['perso_cible'];
        $this->perso_enc_max                 = $result['perso_enc_max'];
        $this->perso_description             = $result['perso_description'];
        $this->perso_nb_mort                 = $result['perso_nb_mort'];
        $this->perso_nb_monstre_tue          = $result['perso_nb_monstre_tue'];
        $this->perso_nb_joueur_tue           = $result['perso_nb_joueur_tue'];
        $this->perso_reputation              = $result['perso_reputation'];
        $this->perso_avatar                  = $result['perso_avatar'];
        $this->perso_kharma                  = $result['perso_kharma'];
        $this->perso_amel_deg_dex            = $result['perso_amel_deg_dex'];
        $this->perso_nom                     = $result['perso_nom'];
        $this->perso_gmon_cod                = $result['perso_gmon_cod'];
        $this->perso_renommee                = $result['perso_renommee'];
        $this->perso_dirige_admin            = $result['perso_dirige_admin'];
        $this->perso_lower_perso_nom         = $result['perso_lower_perso_nom'];
        $this->perso_sta_combat              = $result['perso_sta_combat'];
        $this->perso_sta_hors_combat         = $result['perso_sta_hors_combat'];
        $this->perso_utl_pa_rest             = $result['perso_utl_pa_rest'];
        $this->perso_tangible                = $result['perso_tangible'];
        $this->perso_nb_tour_intangible      = $result['perso_nb_tour_intangible'];
        $this->perso_capa_repar              = $result['perso_capa_repar'];
        $this->perso_nb_amel_repar           = $result['perso_nb_amel_repar'];
        $this->perso_amelioration_nb_sort    = $result['perso_amelioration_nb_sort'];
        $this->perso_renommee_magie          = $result['perso_renommee_magie'];
        $this->perso_vampirisme              = $result['perso_vampirisme'];
        $this->perso_niveau_vampire          = $result['perso_niveau_vampire'];
        $this->perso_admin_echoppe           = $result['perso_admin_echoppe'];
        $this->perso_nb_amel_comp            = $result['perso_nb_amel_comp'];
        $this->perso_nb_receptacle           = $result['perso_nb_receptacle'];
        $this->perso_nb_amel_chance_memo     = $result['perso_nb_amel_chance_memo'];
        $this->perso_priere                  = $result['perso_priere'];
        $this->perso_dfin                    = $result['perso_dfin'];
        $this->perso_px                      = $result['perso_px'];
        $this->perso_taille                  = $result['perso_taille'];
        $this->perso_admin_echoppe_noir      = $result['perso_admin_echoppe_noir'];
        $this->perso_use_repart_auto         = $result['perso_use_repart_auto'];
        $this->perso_pnj                     = $result['perso_pnj'];
        $this->perso_redispatch              = $result['perso_redispatch'];
        $this->perso_nb_redist               = $result['perso_nb_redist'];
        $this->perso_mcom_cod                = $result['perso_mcom_cod'];
        $this->perso_nb_ch_mcom              = $result['perso_nb_ch_mcom'];
        $this->perso_piq_rap_env             = $result['perso_piq_rap_env'];
        $this->perso_ancien_avatar           = $result['perso_ancien_avatar'];
        $this->perso_nb_crap                 = $result['perso_nb_crap'];
        $this->perso_nb_embr                 = $result['perso_nb_embr'];
        $this->perso_crapaud                 = $result['perso_crapaud'];
        $this->perso_dchange_mcom            = $result['perso_dchange_mcom'];
        $this->perso_prestige                = $result['perso_prestige'];
        $this->perso_av_mod                  = $result['perso_av_mod'];
        $this->perso_mail_inactif_envoye     = $result['perso_mail_inactif_envoye'];
        $this->perso_test                    = $result['perso_test'];
        $this->perso_nb_spe                  = $result['perso_nb_spe'];
        $this->perso_compt_pvp               = $result['perso_compt_pvp'];
        $this->perso_dmodif_compt_pvp        = $result['perso_dmodif_compt_pvp'];
        $this->perso_effets_auto             = $result['perso_effets_auto'];
        $this->perso_quete                   = $result['perso_quete'];
        $this->perso_tuteur                  = $result['perso_tuteur'];
        $this->perso_voie_magique            = $result['perso_voie_magique'];
        $this->perso_energie                 = $result['perso_energie'];
        $this->perso_desc_long               = $result['perso_desc_long'];
        $this->perso_nb_mort_arene           = $result['perso_nb_mort_arene'];
        $this->perso_nb_joueur_tue_arene     = $result['perso_nb_joueur_tue_arene'];
        $this->perso_dfin_tangible           = $result['perso_dfin_tangible'];
        $this->perso_renommee_artisanat      = $result['perso_renommee_artisanat'];
        $this->perso_avatar_version          = $result['perso_avatar_version'];
        $this->perso_etage_origine           = $result['perso_etage_origine'];
        $this->perso_monstre_attaque_monstre = $result['perso_monstre_attaque_monstre'];
        $this->perso_mortel                  = $result['perso_mortel'];
        $this->alterego                      = $result['alterego'];
        $this->perso_monture                 = $result['perso_monture'];
        $this->perso_misc_param              = $result['perso_misc_param'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return perso
     * @global bdd_mysql $pdo
     */
    public function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select perso_cod  from perso order by perso_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso;
            $temp->charge($result["perso_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function getByNomLike($perso_nom, $perso_actif = 'O', $perso_type_perso = 1)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select perso_cod  from perso
          where perso_nom ilike :perso_nom
          and perso_actif = :perso_actif
          and perso_type_perso = :perso_type_perso
          order by perso_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(
            array(
                ":perso_nom"        => '%' . $perso_nom . '%',
                ":perso_actif"      => $perso_actif,
                ":perso_type_perso" => $perso_type_perso
            ),
            $stmt
        );
        while ($result = $stmt->fetch())
        {
            $temp = new perso;
            $temp->charge($result["perso_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function has_evt_non_lu()
    {
        $ligne_evt = new ligne_evt();
        $tab_evt   = $ligne_evt->getByPersoNonLu($this->perso_cod);
        if (count($tab_evt) != 0)
        {
            return true;
        }
        return false;
    }

    public function has_arme_distance()
    {
        $pdo      = new bddpdo;
        $req_arme = "select gobj_distance
            from objet_generique,objets,perso_objets
            where perobj_perso_cod = :perso
            and perobj_equipe = 'O'
            and perobj_obj_cod = obj_cod
            and obj_gobj_cod = gobj_cod
            and gobj_tobj_cod = 1
            and gobj_distance = 'O'";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(':perso' => $this->perso_cod), $stmt);
        if ($stmt->fetch())
        {
            return true;
        }
        return false;
    }

    function get_perso_quete()
    {
        $pdo       = new bddpdo;
        $req_quete = "select perso_quete,perso_cod from perso,perso_position
			where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = :perso)
				and perso_quete in ('quete_ratier.php','enchanteur.php','quete_alchimiste.php','quete_chasseur.php','quete_dispensaire.php',
				'quete_dame_cygne.php','quete_forgeron.php','quete_groquik.php')
				and perso_cod = ppos_perso_cod
			order by perso_quete";
        $stmt      = $pdo->prepare($req_quete);
        $stmt      = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $tab_quete = array();
        while ($result = $stmt->fetch())
        {
            $perso             = $result['perso_cod'];
            $tab_quete[$perso] = $result['perso_quete'];
        }
        return $tab_quete;
    }

    public function get_arme_equipee()
    {
        $pdo      = new bddpdo;
        $req_arme = "SELECT obj_cod
		  FROM objets
		  LEFT JOIN perso_objets ON perobj_obj_cod=obj_cod
		  LEFT JOIN objet_generique ON gobj_cod=obj_gobj_cod
		  LEFT JOIN type_objet ON tobj_cod=gobj_tobj_cod
		  WHERE perobj_equipe = 'O'
		  AND tobj_libelle = 'Arme'
		  AND perobj_perso_cod = :perso
		  ORDER BY obj_gobj_cod ASC, obj_cod ASC";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(':perso' => $this->perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $obj = new objets();
        if (!$obj->charge($result['obj_cod']))
        {
            return false;
        }
        return $obj;
    }

    public function get_mode_combat()
    {
        $pdo      = new bddpdo;
        $req_arme = "select gobj_distance
            from objet_generique,objets,perso_objets
            where perobj_perso_cod = :perso
            and perobj_equipe = 'O'
            and perobj_obj_cod = obj_cod
            and obj_gobj_cod = gobj_cod
            and gobj_tobj_cod = 1
            and gobj_distance = 'O'";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        if ($stmt->fetch())
        {
            return true;
        }
        return false;
    }

    public function get_pa_attaque()
    {
        $pdo      = new bddpdo;
        $req_arme = "select nb_pa_attaque(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    public function allonge_temps()
    {
        $pdo      = new bddpdo;
        $req_arme = "select allonge_temps(:perso) as allonge_temps";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['allonge_temps'];
    }

    public function allonge_temps_temps()
    {
        $pdo      = new bddpdo;
        $req_arme = "select allonge_temps_temps(:perso) as allonge_temps";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['allonge_temps'];
    }

    public function allonge_temps_poids()
    {
        $pdo      = new bddpdo;
        $req_arme = "select allonge_temps_poids(:perso) as allonge_temps_poids";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['allonge_temps_poids'];
    }

    public function allonge_temps_poids_temps()
    {
        $pdo      = new bddpdo;
        $req_arme = "select allonge_temps_poids_temps(:perso) as allonge_temps_poids";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['allonge_temps_poids'];
    }


    public function f_vue_renommee()
    {
        $pdo      = new bddpdo;
        $req_arme = "select f_vue_renommee(:perso) as f_vue_renommee";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['f_vue_renommee'];
    }

    function cree_objet($objet)
    {
        $pdo    = new bddpdo();
        $req    = "select cree_objet_perso(:objet,:perso_cod) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":objet" => $objet, ":perso_cod" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function get_nb_auberge()
    {
        $pdo    = new bddpdo();
        $req    = "select count(paub_visite) as nbre_visite from perso_auberge,quete_perso
 			where paub_perso_cod = :perso_cod
 				and paub_visite = 'O'
				and pquete_perso_cod = :perso_cod 
				and pquete_termine = 'N'
				and pquete_quete_cod = '6'";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['nbre_visite'];
    }

    public function get_renommee()
    {
        $pdo      = new bddpdo;
        $req_arme = "select get_renommee(:perso) as get_renommee";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_renommee), $stmt);
        $result   = $stmt->fetch();
        return $result['get_renommee'];
    }

    public function get_renommee_magie()
    {
        $pdo      = new bddpdo;
        $req_arme = "select get_renommee_magie(:perso) as get_renommee_magie";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_renommee_magie), $stmt);
        $result   = $stmt->fetch();
        return $result['get_renommee_magie'];
    }

    public function get_karma()
    {
        $pdo      = new bddpdo;
        $req_arme = "select get_karma(:perso::numeric) as get_karma";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_kharma), $stmt);
        $result   = $stmt->fetch();
        return $result['get_karma'];
    }

    public function get_renommee_artisanat()
    {
        $pdo      = new bddpdo;
        $req_arme = "select get_renommee_artisanat(:perso) as get_renommee_artisanat";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_renommee_artisanat), $stmt);
        $result   = $stmt->fetch();
        return $result['get_renommee_artisanat'];
    }

    public function portee_attaque()
    {
        $pdo      = new bddpdo;
        $req_arme = "select portee_attaque(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    public function distance_vue()
    {
        $pdo      = new bddpdo;
        $req_arme = "select distance_vue(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    public function type_arme()
    {
        $pdo      = new bddpdo;
        $req_arme = "select type_arme(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    public function get_pa_foudre()
    {
        $pdo      = new bddpdo;
        $req_arme = "select nb_pa_foudre(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    public function has_competence($competence)
    {
        $pcomp = new perso_competences();
        if ($pcomp->getByPersoComp($this->perso_cod, $competence))
        {
            return true;
        }
        return false;
    }

    public function getByComptDerPerso($vcompte)
    {
        $compte = new compte;
        $compte->charge($vcompte);
        return $this->charge($compte->compt_der_perso_cod);
    }

    public function get_pa_dep()
    {
        $pdo    = new bddpdo;
        $req    = 'select get_pa_dep(?) as pa';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['pa'];
    }

    public function is_milice()
    {
        $pdo    = new bddpdo;
        $req    = 'select is_milice(?) as ismilice';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['ismilice'];
    }

    public function isIntangible()
    {
        return $this->perso_tangible != 'O';
    }

    public function is_enlumineur()
    {
        $test1 = $this->existe_competence('91');
        $test2 = $this->existe_competence('92');
        $test3 = $this->existe_competence('93');
        return ($test1 || $test2 || $test3);
    }

    public function existe_competence($comp_cod)
    {
        $comp = new perso_competences();
        return $comp->getByPersoComp($this->perso_cod, $comp_cod);
    }

    public function is_potions()
    {
        $test1 = $this->existe_competence('97');
        $test2 = $this->existe_competence('100');
        $test3 = $this->existe_competence('101');
        return ($test1 || $test2 || $test3);
    }

    public function is_refuge()
    {
        $ppos = new perso_position();
        $ppos->getByPerso($this->perso_cod);
        $lpos = new lieu_position();
        $lpos->getByPos($ppos->ppos_pos_cod);
        $lieu = new lieu;
        if ($lieu->charge($lpos->lpos_lieu_cod))
        {
            if ($lieu->lieu_refuge == 'O')
            {
                return true;
            }
        }
        return false;
    }

    public function get_position()
    {
        $ppos = new perso_position();
        $ppos->getByPerso($this->perso_cod);
        $pos = new positions();
        $pos->charge($ppos->ppos_pos_cod);
        $etage = new etage();
        $etage->getByNumero($pos->pos_etage);
        $retour['pos']   = $pos;
        $retour['etage'] = $etage;

        return $retour;
    }

    public function get_position_object()
    {
        $ppos = new perso_position();
        $ppos->getByPerso($this->perso_cod);
        $pos = new positions();
        $pos->charge($ppos->ppos_pos_cod);
        return $pos;
    }

    public function get_favoris()
    {
        $pdo    = new bddpdo;
        $retour = array();

        $req  =
            "SELECT pfav_cod, pfav_type, pfav_misc_cod, pfav_nom, pfav_function_cout_pa, pfav_link FROM public.perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod order by pfav_nom";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pfav_perso_cod" => $this->perso_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $req     = "SELECT " . $result["pfav_function_cout_pa"] . " as cout_pa ";
            $stmt2   = $pdo->prepare($req);
            $stmt2   = $pdo->execute(array(), $stmt2);
            $result2 = $stmt2->fetch();

            //if ((int)$result2["cout_pa"]==20 && $result["pfav_type"]=="sort5")
            //{
            //    //Supression automatique des racourcis non-valides (si le perso n'a plus l'objet magique)
            //    $req  = "DELETE FROM public.perso_favoris WHERE pfav_cod=:pfav_cod  ";
            //    $stmt3 = $pdo->prepare($req);
            //    $pdo->execute(array(":pfav_cod" => $result["pfav_cod"]), $stmt3);
            //}

            $retour[] = array("pfav_cod"      => $result["pfav_cod"],
                              "nom"           => $result2["cout_pa"] > 12 ? $result["pfav_nom"] : $result["pfav_nom"] . " (" . $result2["cout_pa"] . " PA)",
                              "link"          => $result["pfav_link"],
                              "pfav_type"     => $result["pfav_type"],
                              "pfav_misc_cod" => $result["pfav_misc_cod"]);
        }
        return $retour;
    }

    public function get_cout_pa_magie($sort, $type_lance)
    {
        $pdo    = new bddpdo;
        $req    = "SELECT cout_pa_magie(:perso, :sort, :type_lance) as cout";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"      => $this->perso_cod,
                                    ":sort"       => $sort,
                                    ":type_lance" => $type_lance), $stmt);
        $result = $stmt->fetch();
        return $result['cout'];
    }

    public function get_nb_sort_memorisable()
    {
        $pdo    = new bddpdo;
        $req    = "SELECT nb_sort_memorisable(:perso) as nb_sort_memorisable";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['nb_sort_memorisable'];
    }

    public function get_nb_sort_appris()
    {
        $pdo    = new bddpdo;
        $req    = "select count(*) as nb_sorts_appris from perso_sorts where psort_perso_cod = :perso ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['nb_sorts_appris'];
    }

    public function is_fam()
    {
        if ($this->perso_type_perso == 3)
        {
            return true;
        }
        return false;
    }

    public function is_monstre()
    {
        if ($this->perso_type_perso == 2)
        {
            return true;
        }
        return false;
    }

    public function is_4eme_perso()
    {
        if ($this->perso_pnj == 2)
        {
            return true;
        }
        return false;
    }

    public function is_fam_4eme_perso()
    {
        if ($this->perso_type_perso == 3)
        {
            $p = new perso();
            $pf = new perso_familier();
            $pf->getByFamilier($this->perso_cod);
            $p->charge( $pf->pfam_perso_cod ) ;
            return $p->is_4eme_perso();
        }
        return false;
    }

    public function is_admin_dieu()
    {
        $dp  = new dieu_perso();
        $tab = $dp->getBy_dper_perso_cod($this->perso_cod);
        if ($tab === false)
        {
            return false;
        }
        foreach ($tab as $ddp)
        {
            if ($ddp->dper_niveau > 3)
            {
                return true;
            }
        }
        return false;
    }

    public function is_religion()
    {
        $dp  = new dieu_perso();
        $tab = $dp->getBy_dper_perso_cod($this->perso_cod);
        if ($tab === false)
        {
            return false;
        }
        foreach ($tab as $ddp)
        {
            if ($ddp->dper_niveau >= 2)
            {
                return true;
            }
        }
        return false;
    }

    public function is_fidele_gerant()
    {
        $tf  = new temple_fidele();
        $tab = $tf->getBy_tfid_perso_cod($this->perso_cod);
        if ($tab === false)
        {
            return false;
        }
        return true;
    }

    public function transactions()
    {
        $tran  = new transaction();
        $total = 0;
        $tabv  = $tran->getBy_tran_vendeur($this->perso_cod);
        if ($tabv !== false)
        {
            $total += count($tabv);
        }


        $taba = $tran->getBy_tran_acheteur($this->perso_cod);
        if ($taba !== false)
        {
            $total += count($taba);
        }
        return $total;
    }

    public function barre_hp()
    {
        if ($this->perso_pv_max == 0)
        {
            $barre_hp = 0;
        } else
        {
            // LAG: Affichage au % près (avec des bornes  >2% et <98% pour la lisibilité)
            $barre_hp = round(100 * $this->perso_pv / $this->perso_pv_max);
            if (($barre_hp >= 98) && ($this->perso_pv < $this->perso_pv_max))
            {
                $barre_hp = 98;
            } elseif (($barre_hp <= 2) && ($this->perso_pv > 0))
            {
                $barre_hp = 2;
            } elseif ($barre_hp < 0)
            {
                $barre_hp = 0;
            } elseif ($barre_hp >= 100)
            {
                $barre_hp = 100;
            }
        }
        return $barre_hp;
    }

    public function barre_energie()
    {
        if ($this->is_enchanteur())
        {
            $barre_energie = round($this->perso_energie);
            if ($barre_energie <= 0)
            {
                $barre_energie = 0;
            } elseif ($barre_energie >= 100)
            {
                $barre_energie = 100;
            } elseif ($barre_energie >= 98)
            {
                $barre_energie = 98;
            } elseif ($barre_energie <= 2)
            {
                $barre_energie = 2;
            }
            return $barre_energie;
        }
        return false;
    }

    public function is_enchanteur()
    {
        $test1 = $this->existe_competence('88');
        $test2 = $this->existe_competence('102');
        $test3 = $this->existe_competence('103');
        return ($test1 || $test2 || $test3);
    }

    public function barre_divin()
    {
        if ($this->is_fam_divin() == 1)
        {
            $energie_divine = $this->energie_divine();
            $barre_divine   = round(100 * $energie_divine / 200);
            if ($barre_divine <= 0)
            {
                $barre_divine = 0;
            } elseif ($barre_divine >= 100)
            {
                $barre_divine = 100;
            } elseif ($barre_divine >= 98)
            {
                $barre_divine = 98;
            } elseif ($barre_divine <= 2)
            {
                $barre_divine = 2;
            }
            return $barre_divine;
        }
        return false;
    }

    public function is_fam_divin()
    {
        $is_fam_divin = 0;
        if ($this->perso_gmon_cod == 441)
        {
            $is_fam_divin = 1;
        }
        return $is_fam_divin;
    }

    public function energie_divine()
    {
        if ($this->is_fam_divin() == 1)
        {
            $dp = new dieu_perso;
            $dp->getByPersoCod($this->perso_cod);
            $energie_divine = $dp->dper_points;
            return $energie_divine;
        }
        return false;
    }

    public function degats_perso()
    {
        $pdo    = new bddpdo;
        $req    = "select degats_perso(?) as degats_perso";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['degats_perso'];
    }

    public function relache_monstre_4e_perso()
    {
        $pdo    = new bddpdo;
        $req    = "select relache_monstre_4e_perso(?) as degats_perso";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['degats_perso'];
    }

    public function armure()
    {
        $pdo    = new bddpdo;
        $req    = "select f_armure_perso(?) as armure";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['armure'];
    }

    // Retourne vrai si le perso est sur un endroit permettant le démarrage d'une nouvelle quête (quete auto ou standard)
    public function is_perso_quete()
    {
        $pdo  = new bddpdo;
        $ppos = new perso_position;
        $ppos->getByPerso($this->perso_cod);

        $req
                = 'select count(perso_cod) as nombre from perso,perso_position
			where ppos_pos_cod = ?
				and perso_quete in (\'quete_ratier.php\',\'enchanteur.php\',\'quete_alchimiste.php\',\'quete_chasseur.php\',\'quete_dispensaire.php\',\'quete_dame_cygne.php\',\'quete_forgeron.php\',\'quete_groquik.php\')
				and perso_cod = ppos_perso_cod';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($ppos->ppos_pos_cod), $stmt);
        $result = $stmt->fetch();

        if ($result['nombre'] != 0)
        {
            return true;
        }        // il y a des quetes traditionnelles

        // Verification quete auto
        $quete     = new aquete;
        $tab_quete = $quete->get_debut_quete($this->perso_cod);
        return sizeof($tab_quete["quetes"]) > 0;
    }

    // Retourne vrai si le perso est sur un endroit permettant le démarrage d'une nouvelle quête (quete auto ou standard)
    public function perso_nb_demarrage_quete()
    {
        $pdo  = new bddpdo;
        $ppos = new perso_position;
        $ppos->getByPerso($this->perso_cod);

        $req
                = 'select count(perso_cod) as nombre from perso,perso_position
			where ppos_pos_cod = ?
				and perso_quete in (\'quete_ratier.php\',\'enchanteur.php\',\'quete_alchimiste.php\',\'quete_chasseur.php\',\'quete_dispensaire.php\',\'quete_dame_cygne.php\',\'quete_forgeron.php\',\'quete_groquik.php\')
				and perso_cod = ppos_perso_cod';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($ppos->ppos_pos_cod), $stmt);
        $result = $stmt->fetch();

        // Verification quete auto
        $quete     = new aquete;
        $tab_quete = $quete->get_debut_quete($this->perso_cod);
        return sizeof($tab_quete["quetes"]) + $result['nombre'] ;
    }

    // Retourne vrai si le perso a au moins une quete auto en cours de réalisation ou terminée.
    public function perso_nb_auto_quete()
    {
        $quete = new aquete_perso;
        return ($quete->get_perso_nb_quete($this->perso_cod));       // retourn un tableau nb_encours,nb_total
    }

    /**
     * @return array('lieu' => lieu,'lieu_type' => lieu_type,"lieu_position" => lieu_position)|false
     */
    public function get_lieu()
    {
        if ($this->is_lieu())
        {
            $ppos = new perso_position;
            $ppos->getByPerso($this->perso_cod);
            $lpos = new lieu_position();
            $lpos->getByPos($ppos->ppos_pos_cod);
            $lieu = new lieu;
            $lieu->charge($lpos->lpos_lieu_cod);
            $lt = new lieu_type();
            $lt->charge($lieu->lieu_tlieu_cod);
            $detail['lieu']          = $lieu;
            $detail['lieu_type']     = $lt;
            $detail['lieu_position'] = $lpos;
            return $detail;
        }
        return false;
    }

    function missions_du_perso($fac_cod = -1, $inclure_anciennes = FALSE, $tri = 'statut')
    {
        $pdo         = new bddpdo;
        $resultat    = array();
        $critere_tri = 'mpf_statut desc';
        switch ($tri)
        {
            case 'faction':
                $critere_tri = 'fac_nom';
                break;
            case 'statut':
                $critere_tri = 'mpf_statut desc';
                break;
            case 'date':
                $critere_tri = 'mpf_date_debut::date desc';
                break;
            default:
                break;
        }

        $req = "SELECT miss_nom, fac_nom, mpf_fac_cod, mission_texte(mpf_cod) as libelle,
				to_char(mpf_date_debut, 'DD/MM/YYYY') as mpf_date_debut,
				to_char(mpf_date_fin, 'DD/MM/YYYY') as mpf_date_fin,
				mpf_obj_cod, mpf_pos_cod, mpf_gobj_cod, mpf_cible_perso_cod, mpf_nombre, mpf_gmon_cod,
				miss_fonction_init, miss_fonction_valide, miss_fonction_releve, mpf_statut,
				mpf_cod, mpf_texte, mpf_delai, mpf_recompense
			FROM mission_perso_faction_lieu
			INNER JOIN factions ON fac_cod = mpf_fac_cod
			INNER JOIN missions ON miss_cod = mpf_miss_cod
			WHERE mpf_perso_cod = $this->perso_cod ";

        if (!$inclure_anciennes)    // Statut ni validé, ni échoué
            $req .= ' AND mpf_statut < 40';

        if ($fac_cod != -1)
            $req .= " AND mpf_fac_cod = $fac_cod";

        $req .= ' ORDER BY ' . $critere_tri;

        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $uneMission                      = array();
            $uneMission['Code']              = $result['mpf_cod'];
            $uneMission['Nom']               = $result['miss_nom'];
            $uneMission['Faction']           = $result['fac_nom'];
            $uneMission['FactionCod']        = $result['mpf_fac_cod'];
            $uneMission['Libellé']           = $result['libelle'];
            $uneMission['DateDébut']         = $result['mpf_date_debut'];
            $uneMission['DateFin']           = $result['mpf_date_fin'];
            $uneMission['Statut']            = $result['mpf_statut'];
            $uneMission['Objet']             = $result['mpf_obj_cod'];
            $uneMission['Position']          = $result['mpf_pos_cod'];
            $uneMission['PersoCible']        = $result['mpf_cible_perso_cod'];
            $uneMission['TypeObjet']         = $result['mpf_gobj_cod'];
            $uneMission['TypeMonstre']       = $result['mpf_gmon_cod'];
            $uneMission['Quantité']          = $result['mpf_nombre'];
            $uneMission['FctInit']           = $result['miss_fonction_init'];
            $uneMission['FctValide']         = $result['miss_fonction_valide'];
            $uneMission['FctReleve']         = $result['miss_fonction_releve'];
            $uneMission['Texte']             = $result['mpf_texte'];
            $uneMission['Délai']             = $result['mpf_delai'];
            $uneMission['Récompense']        = $result['mpf_recompense'];
            $statut                          = $result['mpf_statut'];
            $uneMission['MissionPassee']     = $statut >= 40;
            $uneMission['Relevée']           = $statut > 0;
            $uneMission['EnCours']           = $statut >= 10 && $statut < 20;
            $uneMission['Réussie']           = $statut == 20;
            $uneMission['Ratée']             = $statut >= 30 && $statut < 40;
            $uneMission['Validée']           = $statut == 40;
            $uneMission['Échouée']           = $statut >= 50;
            $uneMission['ÀValider']          = $statut >= 20 && $statut < 40;
            $uneMission['RéussitePartielle'] = $statut % 10 > 0;
            $resultat[]                      = $uneMission;
        }
        return $resultat;
    }

    public function getPersosActifs($type_joueur = 1)
    {
        $pdo    = new bddpdo;
        $retour = array();
        //$req_joueur = "select lower(perso_nom) as minusc,perso_cod,perso_nom,perso_nb_joueur_tue,perso_nb_monstre_tue,perso_nb_mort,get_renommee(perso_renommee) as renommee,get_karma(perso_kharma)as karma,perso_renommee,perso_kharma,get_renommee_magie(perso_renommee_magie) as renommee_magie,perso_renommee_magie,perso_nb_joueur_tue_arene,perso_nb_mort_arene, get_renommee_artisanat(perso_renommee_artisanat) as renommee_artisanat ";
        $req
              = "select perso_cod
          from perso
          where perso_actif = 'O'
          and perso_type_perso = :type_joueur
          and perso_cod not in (1,2,3) and perso_pnj != 1";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":type_joueur" => $type_joueur
                              ), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new perso;
            $temp->charge($result["perso_cod"]);

            $retour[] = $temp;
            //
            $renommee = new renommee();
            $renommee->charge_by_valeur($temp->perso_renommee);
            $temp->renommee = $renommee->renommee_libelle;
            //
            $grenommee = new renommee_magie();
            $grenommee->charge_by_valeur($temp->perso_renommee_magie);
            $temp->grenommee = $grenommee->grenommee_libelle;
            //
            $renart = new renommee_artisanat();
            $renart->charge_by_valeur($temp->perso_renommee_artisanat);
            $temp->renart = $renart->renart_libelle;
            //
            $karma = new karma;
            $karma->charge_by_valeur($temp->perso_kharma);
            $temp->karma = $karma->karma_libelle;
            unset($temp);
        }
        return $retour;
    }

    /* Retourne un tableau de 1 perso si le nom fourni est identique à $perso_nom,
       sinon retourne un tableau de perso dont le nom contien la chaine $perso_nom
       valeur possible pour $type_perso :
                du type entier exemple: 1 (type perso)
                du type array exemple: array(1,3) pour un recherche sur les perso et leurs familiers
    */
    public function getPersosByNameLike($perso_nom, $type_perso = 1)
    {
        $pdo    = new bddpdo;
        $retour = array();

        // Si on a pas un array on converti pour avoir un seul traitement
        if (!is_array($type_perso))
        {
            $type_perso = array($type_perso);
        }

        if (count($type_perso) == 0)
        {
            return $retour;
        }

        $list_types = array();
        foreach ($type_perso as $k => $type)
        {
            $list_types[':type' . $k] = intval($type);
        }

        // Recherche d'abord avec un nom exacte
        $req  =
            "select perso_cod from perso where perso_actif = 'O' and LOWER(perso_nom) = :perso_nom and perso_type_perso IN (" . implode(",", array_keys($list_types)) . ") and perso_pnj != 1 and perso_cod not in (1,2,3) ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array_merge(array(":perso_nom" => strtolower($perso_nom)), $list_types), $stmt);

        // Si on ne trouve rien avec une recherche exacte, on assouplie la règle de recherche
        if ($stmt->rowCount() == 0)
        {
            $req  =
                "select perso_cod from perso where perso_actif = 'O' and perso_nom ILIKE :perso_nom and perso_type_perso IN (" . implode(",", array_keys($list_types)) . ") and perso_pnj != 1 and perso_cod not in (1,2,3) ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array_merge(array(":perso_nom" => '%' . $perso_nom . '%'), $list_types), $stmt);
        }

        while ($result = $stmt->fetch())
        {
            $temp = new perso;
            $temp->charge($result["perso_cod"]);

            $retour[] = $temp;
        }
        return $retour;
    }


    public function is_lieu()
    {
        $ppos = new perso_position;
        $ppos->getByPerso($this->perso_cod);
        $lpos = new lieu_position();
        if (!$lpos->getByPos($ppos->ppos_pos_cod))
        {
            return false;
        }
        return true;
    }

    public function missions()
    {
        $pdo    = new bddpdo;
        $req    = "select missions_verifie(?) as missions";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['missions'];
    }

    public function quete_auto()
    {
        // Run all current queste !!!!
        $news = "";

        $quete_perso  = new aquete_perso();
        $quetes_perso = $quete_perso->get_perso_quete_en_cours($this->perso_cod);

        if ($quetes_perso && sizeof($quetes_perso) > 0)
        {
            foreach ($quetes_perso as $k => $q)
            {
                $nb_etapes = $q->run();

                if ($nb_etapes > 0)
                {
                    $quete = new aquete();
                    $quete->charge($q->aqperso_aquete_cod);

                    $pages = $q->journal_news();
                    // Ne mettre l'entête de la quête que s'il y a de nouvelles pages
                    if ($pages != "")
                    {
                        $news .= "<br>&rArr; <em><strong>" . $quete->aquete_nom . "</strong></em>:<br><br>" . $pages;
                    }
                }
            }
        }

        return $news;
    }

    /**
     * @return integer
     */
    public function getNbEvtNonLu()
    {
        $levt = new ligne_evt();
        return $levt->getNbEvtByPersoNonLu($this->perso_cod);
    }

    /**
     * @return ligne_evt[]
     */
    public function getEvtNonLu()
    {
        $levt = new ligne_evt();
        return $levt->getByPersoNonLu($this->perso_cod);
    }

    public function marqueEvtLus()
    {
        $levt = new ligne_evt();
        return $levt->marquePersoLu($this->perso_cod);
    }

    public function barre_xp()
    {
        $barre_xp    = '0';
        $limite      = $this->px_limite();
        $limite_actu = $this->px_limite_actuel();

        if (($this->perso_px - $limite_actu) < 0)
        {
            return 0;
        }
        $niveau_xp = ($this->perso_px - $limite_actu);
        $div_xp    = ($limite - $limite_actu);
        if ($div_xp == 0)
        {
            $barre_xp = 100;
        } else
        {
            $barre_xp = round(100 * $niveau_xp / $div_xp);
            if (($barre_xp >= 98) && ($niveau_xp < $div_xp))
            {
                $barre_xp = 98;
            } elseif (($barre_xp <= 2) && ($niveau_xp > 0))
            {
                $barre_xp = 2;
            } elseif ($barre_xp < 0)
            {
                $barre_xp = 0;
            } elseif ($barre_xp >= 100)
            {
                $barre_xp = 100;
            }
        }

        return $barre_xp;
    }

    public function px_limite()
    {
        $pdo    = new bddpdo;
        $req    = "select limite_niveau(?) as limite_niveau";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['limite_niveau'];
    }

    public function px_limite_actuel()
    {
        $pdo    = new bddpdo;
        $req    = "select limite_niveau_actuel(?) as limite_niveau";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['limite_niveau'];
    }

    public function dlt_passee()
    {
        $pdo    = new bddpdo;
        $req    = "select dlt_passee(?) as dlt_passee";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['dlt_passee'];
    }

    public function prochaine_dlt()
    {
        $pdo    = new bddpdo;
        $req    = "select prochaine_dlt(?) as prochaine_dlt";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['prochaine_dlt'];
    }

    public function get_poids()
    {
        $pdo    = new bddpdo;
        $req    = "select get_poids(?) as get_poids";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return (float)$result['get_poids'];
    }

    public function is_locked()
    {
        $lc  = new lock_combat();
        $tab = $lc->getBy_lock_cible($this->perso_cod);
        if ($tab !== false)
        {
            return true;
        }
        $tab = $lc->getBy_lock_attaquant($this->perso_cod);
        if ($tab !== false)
        {
            return true;
        }
    }

    public function nb_locks()
    {
        $locks = 0;
        $lc    = new lock_combat();
        $tab   = $lc->getBy_lock_cible($this->perso_cod);
        if ($tab !== false)
        {
            $locks += count($tab);
        }
        $tab = $lc->getBy_lock_attaquant($this->perso_cod);
        if ($tab !== false)
        {
            $locks += count($tab);
        }
        return $locks;
    }

    public function nb_obj_case()
    {
        $ppos = new perso_position;
        $ppos->getByPerso($this->perso_cod);
        $opos = new objet_position();
        $tab  = $opos->getBy_pobj_pos_cod($ppos->ppos_pos_cod);
        if ($tab === false)
        {
            return 0;
        }
        return count($tab);
    }

    public function nb_or_case()
    {
        $ppos = new perso_position;
        $ppos->getByPerso($this->perso_cod);
        $por = new or_position();
        $tab = $por->getBy_por_pos_cod($ppos->ppos_pos_cod);
        if ($tab === false)
        {
            return 0;
        }
        return count($tab);
    }

    public function sort_lvl5()
    {
        $pdo    = new bddpdo;
        $req
                = 'select count(1) as nv5 from perso, perso_nb_sorts_total, sorts
            where perso_cod = pnbst_perso_cod
            and pnbst_sort_cod = sort_cod
            and sort_niveau >= 5
            and pnbst_nombre > 0
            -- and perso_voie_magique = 0
            and perso_cod = ?';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['nv5'];
    }

    /**
     * Retourne la liste des sorts mémorisés
     * @return [perso_sorts]
     */
    public function sort_memo()
    {
        $ps  = new perso_sorts();
        $tab = $ps->getBy_psort_perso_cod($this->perso_cod);
        return $tab;
    }

    public function calcul_dlt()
    {
        $date                            = new DateTime();
        $this->perso_mail_inactif_envoye = 0;
        $this->perso_der_connex          = $date->format('Y-m-d H:i:s');
        $this->stocke();
        $pdo  = new bddpdo();
        $req  = "select calcul_dlt2(?) as dlt";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->perso_cod), $stmt);
        // beaucoup de choses ont pu changer suite à la requête précédente
        // du coup, on recharge tout
        $this->charge($this->perso_cod);
        $result = $stmt->fetch();
        return $result['dlt'];
    }

    public function get_guilde()
    {
        $pdo  = new bddpdo();
        $req  = "select pguilde_guilde_cod from guilde_perso where pguilde_perso_cod = ?
            and pguilde_valide = 'O'";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $guilde = new guilde();
        if (!$guilde->charge($result['pguilde_guilde_cod']))
        {
            return false;
        }
        return $guilde;
    }

    public function desengagement($cible)
    {
        $pdo    = new bddpdo();
        $req    = "select desengagement(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":cible" => $cible), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function cree_revolution($cible)
    {
        $pdo    = new bddpdo();
        $req    = "select cree_revolution(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":cible" => $cible), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function embr($cible)
    {
        $pdo    = new bddpdo();
        $req    = "select embr(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":cible" => $cible), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function donne_bonbon($cible)
    {
        $pdo    = new bddpdo();
        $req    = "select donne_bonbon(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":cible" => $cible), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function cree_groupe($nom_groupe)
    {
        $pdo    = new bddpdo();
        $req    = "select cree_groupe(:perso,:nom_groupe) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"      => $this->perso_cod,
                                    ":nom_groupe" => $nom_groupe), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function accepte_invitation($groupe)
    {
        $pdo    = new bddpdo();
        $req    = "select accepte_invitation(:perso,:groupe) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"  => $this->perso_cod,
                                    ":groupe" => $groupe), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function refuse_invitation($groupe)
    {
        $pdo    = new bddpdo();
        $req    = "select refuse_invitation(:perso,:groupe) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"  => $this->perso_cod,
                                    ":groupe" => $groupe), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function regle_groupe($pa, $pv, $dlt, $bonus, $matos, $messages, $messagemort, $champions)
    {
        $pdo    = new bddpdo();
        $req    =
            "select regle_groupe(:perso,:pa,:pv,:dlt,:bonus,:matos,:messages,:messagemort,:champions) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"       => $this->perso_cod,
                                    ":pa"          => $pa,
                                    ":pv"          => $pv,
                                    ":dlt"         => $dlt,
                                    ":bonus"       => $bonus,
                                    ":matos"       => $matos,
                                    ":messages"    => $messages,
                                    ":messagemort" => $messagemort,
                                    ":champions"   => $champions), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function coterie()
    {
        $pdo  = new bddpdo();
        $req  =
            "select pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod = :perso_cod and pgroupe_statut = 1 ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso_cod" => $this->perso_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return -1;
        }
        return $result['pgroupe_groupe_cod'];
    }

    /**
     * @param $nom
     * @return bool|perso
     * @throws Exception
     */
    public function f_cherche_perso($nom)
    {
        $pdo    = new bddpdo();
        $req    = "select f_cherche_perso(:nom) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":nom" => $nom), $stmt);
        $result = $stmt->fetch();


        $this_perso = new perso;

        if (!$this_perso->charge($result['resultat']))
        {
            return false;
        }
        return $this_perso;
    }

    public function teleportation_divine($dest)
    {
        $pdo    = new bddpdo();
        $req    = "select teleportation_divine(:perso,:dest) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":dest"  => $dest), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function invite_groupe($groupe, $invite)
    {
        $pdo    = new bddpdo();
        $req    = "select invite_groupe(:perso,:groupe,:invite) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"  => $this->perso_cod,
                                    ":groupe" => $groupe,
                                    ":invite" => $invite), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function f_enchantement($obj, $enc, $type_appel)
    {
        $pdo    = new bddpdo();
        $req    = "select f_enchantement(:perso,:obj,:enc,:type_appel) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"      => $this->perso_cod,
                                    ":obj"        => $obj,
                                    ":enc"        => $enc,
                                    ":type_appel" => $type_appel), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function offre_boire($cible)
    {
        $pdo    = new bddpdo();
        $req    = "select offre_boire(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":cible" => $cible), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function ouvre_cadeau()
    {
        $pdo    = new bddpdo();
        $req    = "select ouvre_cadeau(:perso) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function donne_rouge()
    {
        $pdo    = new bddpdo();
        $req    = "select donne_rouge(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function is_pnj()
    {
        return ($this->perso_pnj == 1);
    }

    public function donne_noir()
    {
        $pdo    = new bddpdo();
        $req    = "select donne_noir(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function carac_base_for()
    {
        $pdo    = new bddpdo;
        $req    = "select f_carac_base(?,'FOR') as perso_for";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['perso_for'];
    }

    public function carac_base_int()
    {
        $pdo    = new bddpdo;
        $req    = "select f_carac_base(?,'INT') as perso_int";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['perso_int'];
    }

    public function carac_base_con()
    {
        $pdo    = new bddpdo;
        $req    = "select f_carac_base(?,'CON') as perso_con";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['perso_con'];
    }

    public function carac_base_dex()
    {
        $pdo    = new bddpdo;
        $req    = "select f_carac_base(?,'DEX') as perso_dex";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['perso_dex'];
    }

    public function passe_niveau($amel)
    {
        $pdo    = new bddpdo();
        $req    = "select f_passe_niveau(:perso,:amel) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":amel"  => $amel), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function rituel_modif_caracs($demel, $amel)
    {
        $pdo    = new bddpdo();
        $req    = "select f_rituel_modif_caracs(:perso,:demel,:amel) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":demel" => $demel,
                                    ":amel"  => $amel), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function rituel_modif_voiemagique($voie)
    {
        $pdo    = new bddpdo();
        $req    = "select f_rituel_modif_voiemagique(:perso,:voie) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":voie"  => $voie), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function depose_objet($objet)
    {
        $pdo    = new bddpdo();
        $req    = "select depose_objet(:perso,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function vente_bat($objet)
    {
        $pdo    = new bddpdo();
        $req    = "select vente_bat(:perso,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function vote_revolution($revguilde, $vote)
    {
        $pdo    = new bddpdo();
        $req    = "select vote_revolution(:perso,:revguilde,:vote) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"     => $this->perso_cod,
                                    ":revguilde" => $revguilde,
                                    ":vote"      => $vote), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function magasin_achat_generique($lieu, $objet, $qte)
    {
        $retour = "";
        $pdo    = new bddpdo();

        // Avant de faire la boucle on vérifie que les objest sont bien dans les stocks du magasin
        $req  =
            "select sum(mgstock_nombre) count from stock_magasin_generique where mgstock_lieu_cod = :lieu and mgstock_gobj_cod = :objet and mgstock_vente_persos='O' ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":lieu"  => $lieu,
                                  ":objet" => $objet), $stmt);
        if (!$result = $stmt->fetch())
        {
            return '<p>Erreur ! Impossible de vérifier dans les stocks du magasin pour acheter cet objet  !';
        } elseif ((int)$result['count'] < $qte)
        {
            return '<p>Erreur ! Impossible les stocks du magasin sont insuffisants  !';
        }

        // Faire les achats :
        for ($i = 0; $i < $qte; $i++)
        {
            $req    = "select magasin_achat_generique(:perso,:lieu,:objet) as resultat ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(
                                        ":perso" => $this->perso_cod,
                                        ":lieu"  => $lieu,
                                        ":objet" => $objet), $stmt);
            $result = $stmt->fetch();
            $retour .= $result['resultat'];
        }

        return $retour;
    }

    function has_artefact($objet)
    {
        $pdo    = new bddpdo;
        $req    =
            'select count(*) as nombre from perso_objets where perobj_perso_cod =  :perso  and perobj_obj_cod = :objet';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $this->perso_cod,
                                      ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['nombre'] != 0;
    }

    public function magasin_achat($lieu, $objet)
    {
        $pdo    = new bddpdo();
        $req
                = "select magasin_achat(:perso,:lieu,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":lieu"  => $lieu,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function magasin_vente($lieu, $objet)
    {
        $pdo    = new bddpdo();
        $req
                = "select magasin_vente(:perso,:lieu,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":lieu"  => $lieu,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function magasin_vente_generique($lieu, $objet)
    {
        $pdo    = new bddpdo();
        $req
                = "select magasin_vente_generique(:perso,:lieu,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":lieu"  => $lieu,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function is_identifie_objet($v_objet)
    {
        $pdo      = new bddpdo();
        $req_comp =
            'select pio_perso_cod as test from perso_identifie_objet where pio_perso_cod = :perso and pio_obj_cod = :objet limit 1';
        $stmt     = $pdo->prepare($req_comp);
        $stmt     = $pdo->execute(array(
                                      ":perso" => $this->perso_cod,
                                      ":objet" => $v_objet), $stmt);

        return $stmt->rowCount() > 0;

    }

    /**
     * Retourne vrai si le l'objet passé en paramètre est ramassable par le perso, et false sinon
     * @return boolean
     */
    function is_ramasse_objet($v_objet)
    {
        $pdo = new bddpdo;
        $req = "select obj_verif_perso_condition_inv(:perso_cod, :obj_cod) as est_ramassable; ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":obj_cod" => $v_objet, ":perso_cod" => $this->perso_cod),$stmt);
        if (!$result = $stmt->fetch()) return false ;

        if ($result["est_ramassable"]==1) return true;

        return false ;
    }

    public function magasin_identifie($lieu, $objet)
    {
        $pdo    = new bddpdo();
        $req
                = "select magasin_identifie(:perso,:lieu,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":lieu"  => $lieu,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function magasin_repare($lieu, $objet)
    {
        $pdo    = new bddpdo();
        $req
                = "select magasin_repare(:perso,:lieu,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":lieu"  => $lieu,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function cree_receptacle($sort, $type_lance)
    {
        $pdo    = new bddpdo();
        $req
                = "select cree_receptacle(:perso,:sort,:type_lance) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"      => $this->perso_cod,
                                    ":sort"       => $sort,
                                    ":type_lance" => $type_lance), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function cree_parchemin($sort, $type_lance)
    {
        $pdo    = new bddpdo();
        $req
                = "select cree_parchemin(:perso,:sort,:type_lance) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso"      => $this->perso_cod,
                                    ":sort"       => $sort,
                                    ":type_lance" => $type_lance), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function cree_perso()
    {
        $pdo    = new bddpdo();
        $req
                = "select cree_perso(:perso) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        // les données ont été modifiées en base
        // on recharge pour ne pas écraser les valeurs par la suite
        $this->charge($this->perso_cod);
        return $result['resultat'];
    }

    public function don_br($dest, $qte)
    {
        $pdo    = new bddpdo();
        $req
                = "select don_br(:perso,:dest,:qte) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":dest"  => $dest,
                                    ":qte"   => $qte), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function milice_tel($dest)
    {
        $pdo    = new bddpdo();
        $req
                = "select milice_tel(:perso,:dest) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":dest"  => $dest), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function is_bernardo()
    {
        $pdo  = new bddpdo;
        $req  = "select valeur_bonus(perso_cod , 'BER') as nombre from perso where perso_cod = :perso";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);

        $result = $stmt->fetch();

        return $result['nombre'] != 0;
    }

    public function vente_auberge($objet)
    {
        $pdo    = new bddpdo();
        $req
                = "select vend_objet(:perso,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function change_mode_combat($mode)
    {
        $pdo    = new bddpdo();
        $req
                = "select change_mcom_cod(:perso,:mode) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":mode"  => $mode), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function detail_redispatch($amel)
    {
        $pdo    = new bddpdo();
        $req
                = "select detail_redispatch(:perso,:amel) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":amel"  => $amel), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function start_redispatch()
    {
        $pdo    = new bddpdo();
        $req
                = "select start_redispatch(:perso) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function achete_objet($objet)
    {
        $pdo    = new bddpdo();
        $req
                = "select achete_objet(:perso,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function prie_dieu($dieu)
    {
        $pdo    = new bddpdo();
        $req
                = "select prie_dieu(:perso,:dieu) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":dieu"  => $dieu), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function ceremonie_dieu($dieu)
    {
        $pdo    = new bddpdo();
        $req
                = "select ceremonie_dieu(:perso,:dieu) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":dieu"  => $dieu), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function change_grade($dieu)
    {
        $pdo    = new bddpdo();
        $req
                = "select change_grade(:perso,:dieu) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":dieu"  => $dieu), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function prie_dieu_ext($dieu)
    {
        $pdo    = new bddpdo();
        $req
                = "select prie_dieu_ext(:perso,:dieu) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":dieu"  => $dieu), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    public function niveau_blessures($pv = null, $pv_max = null)
    {
        // Sans paramètres on prends le perso courant
        if ($pv == null || $pv_max == null)
        {
            $pv     = $this->perso_pv;
            $pv_max = $this->perso_pv_max;
        }

        $niveau_blessures = "";
        if ($pv / $pv_max < 0.15)
        {
            $niveau_blessures = 'presque mort';
        } elseif ($pv / $pv_max < 0.25)
        {
            $niveau_blessures = 'gravement touché';
        } elseif ($pv / $pv_max < 0.5)
        {
            $niveau_blessures = 'blessé';
        } elseif ($pv / $pv_max < 0.75)
        {
            $niveau_blessures = 'touché';
        }
        return $niveau_blessures;
    }

    public function repare_objet($type_rep, $objet)
    {
        $pdo    = new bddpdo();
        $req
                = "select f_repare_" . $type_rep . "(:perso,:objet) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ":perso" => $this->perso_cod,
                                    ":objet" => $objet), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }


    public function prepare_for_tab_switch()
    {
        global $type_flux;
        $this->position = $this->get_position();
        $this->guilde   = $this->get_guilde();

        if ($this->perso_avatar == '')
        {
            $this->avatar = G_IMAGES . $this->perso_race_cod . "_" . $this->perso_sex . ".png";
        } else
        {
            $this->avatar = $type_flux . G_URL . "avatars/" . $this->perso_avatar;
        }

        try
        {
            $size = @getimagesize($this->avatar);
            if ($size !== false)
            {
                $this->avatar_largeur = $size[0];
                $this->avatar_hauteur = $size[1];
            }
        }
        catch (Exception $e)
        {
            unset($e);
        }

        $this->barre_divine = -1;
        if ($this->perso_gmon_cod == 441)
        {
            $barre_divine = floor(($this->energie_divine() / 200) * 10) * 10;
            if ($barre_divine >= 100)
            {
                $barre_divine = 100;
            }
            $this->barre_divine = $barre_divine;
        }

        $this->msg_non_lu = $this->getMsgNonLu();
    }

    /**
     * @return messages_dest[]
     */
    public function getMsgNonLu()
    {
        $msg_dest = new messages_dest();
        return $msg_dest->getByPersoNonLu($this->perso_cod);
    }

    public function get_valeur_bonus($bonus)
    {
        $pdo    = new bddpdo();
        $req    = "select valeur_bonus(:perso, '$bonus') as bonus";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                    ':perso' => $this->perso_cod
                                ), $stmt);
        $result = $stmt->fetch();
        return $result['bonus'];
    }

    public function prepare_get_vue()
    {
        $ppos  = new perso_position();
        $pos   = new positions();
        $etage = new etage();

        $ppos->getByPerso($this->perso_cod);
        $pos->charge($ppos->ppos_pos_cod);
        $this->pos = $pos;
        $etage->getByNumero($pos->pos_etage);

        $compte       = new compte;
        $perso_compte = new perso_compte();

        $compte->charge($perso_compte->pcompt_compt_cod);


        $distance_vue = $this->distance_vue();
        $portee       = $this->portee_attaque();
        if ($distance_vue <= $portee)
        {
            $portee = $distance_vue;
        }

        $this->x_min  = $pos->pos_x - $portee;
        $this->x_max  = $pos->pos_x + $portee;
        $this->y_min  = $pos->pos_y - $portee;
        $this->y_max  = $pos->pos_y + $portee;
        $this->compte = $compte;
    }

    public function get_vue_non_lock($compte)
    {
        $this->prepare_get_vue();

        $pdo            = new bddpdo();
        $req_vue_joueur = "select trajectoire_vue(:pos_cod,pos_cod) as traj,
          perso_nom,pos_x,pos_y,pos_etage,race_nom,distance(:pos_cod,pos_cod) as distance,
          pos_cod,perso_cod,case when perso_type_perso = 1 then 1 else 2 end as perso_type_perso,
          perso_pv,perso_pv_max,is_surcharge(perso_cod,:perso) as surcharge ,
          (select count(1) from trajectoire_perso(:pos_cod,pos_cod) as (nv_cible int, v_pos int, type_perso int)) as obstruction,
          (select pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1 ) coterie,
          perso_type_perso as type_perso
          from perso,positions,perso_position,race
          where pos_x between (:x_min) and (:x_max)
          and pos_y between (:y_min) and (:y_max)
          and pos_cod = ppos_pos_cod
          and pos_etage = :pos_etage
          and ppos_perso_cod = perso_cod
          and perso_cod != :perso
          and perso_actif = 'O'
          and perso_tangible = 'O'
          and perso_race_cod = race_cod
          and not exists
              (select 1 from lieu,lieu_position
              where lpos_pos_cod = ppos_pos_cod
              and lpos_lieu_cod = lieu_cod
              and lieu_refuge = 'O')
          and not exists
            (select 1 from perso_familier
            where pfam_perso_cod = :perso
            and pfam_familier_cod = perso_cod)
          and not exists
            (select 1 from perso_compte
            where pcompt_compt_cod = :compte
            and pcompt_perso_cod = perso_cod)
          and perso_cod not in
            ((select pfam_familier_cod from perso_compte join perso_familier on pfam_perso_cod=pcompt_perso_cod join perso on perso_cod=pfam_familier_cod  where pcompt_compt_cod = (select pcompt_compt_cod from perso_compte where pcompt_perso_cod = :perso)  and perso_actif='O')
        	union
        	(select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = (select pcompt_compt_cod from perso_compte where pcompt_perso_cod = :perso)  and csit_dfin > now() and csit_ddeb < now())
            union
            (select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = (select pcompt_compt_cod from perso_compte where pcompt_perso_cod = :perso)  and csit_dfin > now() and csit_ddeb < now())
            union
            (select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = (select pcompt_compt_cod from perso_compte where pcompt_perso_cod = :perso)  and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod)
            union
            (select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = (select pcompt_compt_cod from perso_compte where pcompt_perso_cod = :perso)  and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod))
            order by perso_type_perso desc, distance,pos_x,pos_y,perso_nom ";
        $stmt           = $pdo->prepare($req_vue_joueur);
        $stmt           = $pdo->execute(array(
                                            ':pos_cod'   => $this->pos->pos_cod,
                                            ':perso'     => $this->perso_cod,
                                            ':pos_etage' => $this->pos->pos_etage,
                                            ':x_min'     => $this->x_min,
                                            ':x_max'     => $this->x_max,
                                            ':y_min'     => $this->y_min,
                                            ':y_max'     => $this->y_max,
                                            ':compte'    => $this->compte->compt_cod
                                        ), $stmt);
        return $stmt->fetchAll();
    }


    public function get_vue_lock($compte)
    {
        // position
        $this->prepare_get_vue();


        $pdo            = new bddpdo();
        $req_vue_joueur = "select
              trajectoire_vue(:pos_cod,pos_cod) as traj,
              perso_nom,
              pos_x,
              pos_y,
              pos_etage,
              race_nom,
              distance(:pos_cod,pos_cod) as distance,
              pos_cod,
              perso_cod,
              case when perso_type_perso = 1 then 1 else 2 end as perso_type_perso,
              perso_pv,
              perso_pv_max,
              is_surcharge(perso_cod,:perso) as surcharge,
              0 as obstruction,
             (select pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1 ) coterie,
              perso_type_perso as type_perso
            from perso,positions,perso_position,race,lock_combat
              where pos_x between (:x_min) and (:x_max)
              and pos_y between (:y_min) and (:y_max)
              and pos_cod = ppos_pos_cod
              and pos_etage = :pos_etage
              and ppos_perso_cod = perso_cod
              and perso_cod != :perso
              and perso_actif = 'O'
              and perso_tangible = 'O'
              and perso_race_cod = race_cod
              and not exists
                (select 1 from lieu,lieu_position
                 where lpos_pos_cod = ppos_pos_cod
                 and lpos_lieu_cod = lieu_cod
                 and lieu_refuge = 'O')
              and not exists
                (select 1 from perso_familier
                where pfam_perso_cod = :perso
                and pfam_familier_cod = perso_cod)
              and not exists
                (select 1 from perso_compte
                where pcompt_compt_cod = :compte
                and pcompt_perso_cod = perso_cod)
              and lock_cible = :perso
              and lock_attaquant = perso_cod
            union
              select
                trajectoire_vue(:pos_cod,pos_cod) as traj,
                perso_nom,
                pos_x,
                pos_y,
                pos_etage,
                race_nom,
                distance(:pos_cod,pos_cod) as distance,
                pos_cod,
                perso_cod,
                perso_type_perso,
                perso_pv,
                perso_pv_max,
                is_surcharge(perso_cod,:perso ) as surcharge,
                0 as obstruction ,
                (select pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1 ) coterie,
              perso_type_perso as type_perso
              from perso,positions,perso_position,race,lock_combat
              where pos_x between (:x_min) and (:x_max)
                and pos_y between (:y_min) and (:y_max)
                and pos_cod = ppos_pos_cod
                and pos_etage = :pos_etage
                and ppos_perso_cod = perso_cod
                and perso_cod != :perso
                and perso_actif = 'O'
                and perso_tangible = 'O'
                and perso_race_cod = race_cod
                and not exists
                    (select 1 from lieu,lieu_position
                    where lpos_pos_cod = ppos_pos_cod
                    and lpos_lieu_cod = lieu_cod
                    and lieu_refuge = 'O')
                and not exists
                    (select 1 from perso_familier
                    where pfam_perso_cod = :perso
                    and pfam_familier_cod = perso_cod)
                and not exists
                    (select 1 from perso_compte
                    where pcompt_compt_cod = :compte
                    and pcompt_perso_cod = perso_cod)
                and lock_cible = perso_cod
                and lock_attaquant = :perso ";
        $stmt           = $pdo->prepare($req_vue_joueur);
        $stmt           = $pdo->execute(array(
                                            ':pos_cod'   => $this->pos->pos_cod,
                                            ':perso'     => $this->perso_cod,
                                            ':pos_etage' => $this->pos->pos_etage,
                                            ':x_min'     => $this->x_min,
                                            ':x_max'     => $this->x_max,
                                            ':y_min'     => $this->y_min,
                                            ':y_max'     => $this->y_max,
                                            ':compte'    => $this->compte->compt_cod
                                        ), $stmt);
        return $stmt->fetchAll();
    }

    public function race()
    {
        $pdo  = new bddpdo;
        $req  = "select race_nom from race where race_cod = ? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->perso_race_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return 'inconnue';
        }
        return $result["race_nom"];
    }

    /**
     * Pour garder la compatibilité avec l'ancien appel
     * dans phplib
     * @return mixed
     */
    function get_lieu_ancien()
    {
        $pdo = new bddpdo;
        // Lieu standard
        $tab_lieu['nom']         = "";
        $tab_lieu['description'] = "";
        $tab_lieu['url']         = "";
        $tab_lieu['libelle']     = "";
        $tab_lieu['type_lieu']   = "";
        $tab_lieu['position']    = "";
        $tab_lieu['lieu_cod']    = "";
        $tab_lieu['pos_cod']     = "";
        $tab_lieu['lieu_refuge'] = "";
        $tab_lieu['lieu_prelev'] = "";
        $tab_lieu['evo_niveau']  = "";

        $req_lieu =
            'select lieu_nom,lieu_description,lieu_url,tlieu_libelle,tlieu_cod,ppos_pos_cod,lieu_cod,lpos_pos_cod,lieu_refuge,lieu_prelev,lieu_levo_niveau ';
        $req_lieu = $req_lieu . 'from lieu,lieu_type,lieu_position,perso_position ';
        $req_lieu = $req_lieu . ' where ppos_perso_cod = ' . $this->perso_cod;
        $req_lieu = $req_lieu . ' and ppos_pos_cod = lpos_pos_cod ';
        $req_lieu = $req_lieu . ' and lpos_lieu_cod = lieu_cod ';
        $req_lieu = $req_lieu . ' and lieu_tlieu_cod = tlieu_cod ';
        $stmt     = $pdo->query($req_lieu);
        if ($result = $stmt->fetch())
        {
            $tab_lieu['nom']         = $result['lieu_nom'];
            $tab_lieu['description'] = $result['lieu_description'];
            $tab_lieu['url']         = $result['lieu_url'];
            $tab_lieu['libelle']     = $result['tlieu_libelle'];
            $tab_lieu['type_lieu']   = $result['tlieu_cod'];
            $tab_lieu['position']    = $result['ppos_pos_cod'];
            $tab_lieu['lieu_cod']    = $result['lieu_cod'];
            $tab_lieu['pos_cod']     = $result['lpos_pos_cod'];
            $tab_lieu['lieu_refuge'] = $result['lieu_refuge'];
            $tab_lieu['lieu_prelev'] = $result['lieu_prelev'];
            $tab_lieu['evo_niveau']  = $result['lieu_levo_niveau'];
            // Lieu avancé
            if (!empty($tab_lieu['type_lieu']))
            {
                $req_evo_lieu = 'SELECT levo_libelle, levo_url, levo_override 
					FROM lieu_evolution WHERE levo_tlieu_cod=' . $tab_lieu['type_lieu'] . ' 
						AND levo_niveau=' . $tab_lieu['evo_niveau'];
                $stmt         = $pdo->query($req_evo_lieu);
                if ($result = $stmt->fetch())
                {
                    $tab_lieu['evo_override'] = $result['levo_override'];
                    if ($tab_lieu['evo_override'] == 'O')
                    {
                        $tab_lieu['ini_libelle'] = $tab_lieu['libelle'];
                        $tab_lieu['ini_url']     = $tab_lieu['url'];
                        $tab_lieu['libelle']     = $result['levo_libelle'];
                        $tab_lieu['url']         = $result['levo_url'];
                    } else
                    {
                        $tab_lieu['evo_libelle'] = $result['levo_libelle'];
                        $tab_lieu['evo_url']     = $result['levo_url'];
                    }
                }
            }
        }
        // Retour
        return $tab_lieu;
    }

    /**
     * @param $field : champ au format perso.champ
     * @return string
     */
    public function get_champ($field)
    {
        // Traitements Spécifiques. --------------------------------------
        if ($field == "perso.sex")
        {
            return $this->perso_sex == "M" ? "Monsieur" : "Madame";
        } elseif ((substr($field, 0, 12) == "perso.genre(") && (substr($field, -1) == ")"))
        {
            $genre = explode(",", substr($field, 12, -1));
            return $this->perso_sex == "M" ? $genre[0] : $genre[1];
        }

        //Traitement générique. --------------------------------------
        if (substr($field, -2) == "()")
        {
            //Cas d'une methode----
            $field = substr($field, 6, -2);    //supression de "perso." et de "()"
            if (method_exists($this, $field))
            {
                return $this->$field();
            }
        } else
        {
            //Cas d'un propriétée---
            $field = str_replace(".", "_", $field);
            if (property_exists($this, $field))
            {
                return $this->$field;
            }

            $field = substr($field, 6);    //supression de "perso."
            if (property_exists($this, $field))
            {
                return $this->$field;
            }
        }
        return "";
    }

    /**
     * @param $objet
     * @return mixed
     * @throws Exception
     */
    function compte_objet($objet)
    {
        $pdo     = new bddpdo();
        $req_obj = 'select count(perobj_cod) as nombre from perso_objets,objets 
        where perobj_perso_cod = :perso
        and perobj_obj_cod = obj_cod and obj_gobj_cod = :objet';
        $stmt    = $pdo->prepare($req_obj);
        $stmt    = $pdo->execute(array(":perso" => $this->perso_cod, ":objet" => $objet), $stmt);
        $result  = $stmt->fetch();
        return $result['nombre'];
    }


    public function perso_malus()
    {
        $pdo  = new bddpdo;
        $req  = "select tbonus_libc,
                 tonbus_libelle,
                 case when bonus_mode = 'E' then 'Equipement' else bonus_nb_tours::text
                 end as bonus_nb_tours,
                 bonus_mode,
                 sum(bonus_valeur) as bonus_valeur
             from bonus
                  inner join bonus_type on tbonus_libc = bonus_tbonus_libc
                  where bonus_perso_cod = ?
                  and
                    (tbonus_gentil_positif = 't' and bonus_valeur < 0
                    or tbonus_gentil_positif = 'f' and bonus_valeur > 0)
                  group by tbonus_libc, tonbus_libelle, case when bonus_mode='E' then 'Equipement' else bonus_nb_tours::text end, bonus_mode
                  order by tbonus_libc";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->perso_cod), $stmt);
        if (!$result = $stmt->fetchAll(PDO::FETCH_ASSOC))
        {
            return array();
        }
        return $result;
    }

    public function perso_malus_equipement($equipement = false)
    {
        $pdo  = new bddpdo;
        $req  = "select tbonus_libc,
                 tonbus_libelle,
                 coalesce(tbonus_description, tonbus_libelle) as tbonus_description,
                 case when bonus_mode = 'E' then 'Equipement' else bonus_nb_tours::text
                 end as bonus_nb_tours,
                 bonus_mode,
                 sum(bonus_valeur) as bonus_valeur,
                 obj_cod, obj_nom
             from bonus
                  inner join bonus_type on tbonus_libc = bonus_tbonus_libc
                  left join objets on obj_cod = bonus_obj_cod
                  where bonus_perso_cod = ?
                  and
                    (tbonus_gentil_positif = 't' and bonus_valeur < 0
                    or tbonus_gentil_positif = 'f' and bonus_valeur > 0)
                    and bonus_mode " . ($equipement ? "=" : "!=") . " 'E'
                  group by obj_cod, obj_nom, tbonus_libc, tonbus_libelle, case when bonus_mode='E' then 'Equipement' else bonus_nb_tours::text end, bonus_mode, coalesce(tbonus_description, tonbus_libelle)
                  order by tbonus_libc";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->perso_cod), $stmt);
        if (!$result = $stmt->fetchAll(PDO::FETCH_ASSOC))
        {
            return array();
        }
        return $result;
    }


    public function perso_bonus_equipement($equipement = false)
    {
        $pdo  = new bddpdo;
        $req  = "select tbonus_libc,
                 tonbus_libelle,
                 coalesce(tbonus_description, tonbus_libelle) as tbonus_description,
                 case when bonus_mode='E' then 'Equipement' else bonus_nb_tours::text end as bonus_nb_tours,
                 bonus_mode,
                 sum(bonus_valeur) as bonus_valeur,
                 obj_cod, obj_nom
             from bonus
                  inner join bonus_type on tbonus_libc = bonus_tbonus_libc
                  left join objets on obj_cod = bonus_obj_cod
                  where bonus_perso_cod = ?
                  and
                    (tbonus_gentil_positif = 't' and bonus_valeur > 0
                    or tbonus_gentil_positif = 'f' and bonus_valeur < 0)
                    and bonus_mode " . ($equipement ? "=" : "!=") . " 'E'
                  group by obj_cod, obj_nom, tbonus_libc, tonbus_libelle, case when bonus_mode='E' then 'Equipement' else bonus_nb_tours::text end, bonus_mode, coalesce(tbonus_description, tonbus_libelle)
                  order by tbonus_libc";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->perso_cod), $stmt);
        if (!$result = $stmt->fetchAll(PDO::FETCH_ASSOC))
        {
            return array();
        }
        return $result;
    }

    function bonus_degats_melee()
    {
        $pdo    = new bddpdo;
        $req    = "select bonus_degats_melee(:perso) as bonus_degats_melee";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['bonus_degats_melee'];
    }

    public function get_familier()
    {
        $pdo  = new bddpdo;
        $req  = "select pfam_familier_cod from perso_familier join perso on perso_cod=pfam_familier_cod where pfam_perso_cod=:perso and perso_actif='O' order by perso_dcreat desc limit 1";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        if (!$result = $stmt->fetch()) return false;

        return $result["pfam_familier_cod"];
    }

    public function get_triplette($fam_inclus = true)
    {
        $pdo  = new bddpdo;
        $req  = "select array_to_string(f_perso_triplette(:perso, :fam_inclus),',') as triplette";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_cod, ":fam_inclus" => ($fam_inclus ? 1 : 0) ), $stmt);
        if (!$result = $stmt->fetch()) return "".$this->perso_cod ;

        return $result["triplette"];
    }

    public function efface()
    {
        $pdo  = new bddpdo;
        $req  = "select efface_perso(:perso)";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
    }

    /**
     *regarde si une monture est chevauché et retour ne perso_cod du cavalier?
     */
    public function est_chevauche()
    {
        $pdo  = new bddpdo;
        $req  = "select p.perso_cod
                    from perso m 
                    join perso p on p.perso_monture = m.perso_cod and p.perso_type_perso=1
                    join monstre_generique on gmon_cod = m.perso_gmon_cod
                    where m.perso_cod=:perso and m.perso_type_perso=2  and gmon_monture = 'O' ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        if (!$result = $stmt->fetch()) return false;
        return $result["perso_cod"];
    }

    /**
     *regarde si la monture est chevauché est ordonable par le joueur (le joueur peut lui donner des ordres)
     */
    public function monture_ordonable()
    {
        if ( !$this->perso_monture) return false ;

        $pdo  = new bddpdo;
        $req  = " select CASE WHEN perso_dirige_admin='N' and coalesce(pia_ia_type, gmon_type_ia) in (18,19,20,21) THEN 'O' ELSE 'N' END as ordonable
                         from perso 
                         join monstre_generique on gmon_cod=perso_gmon_cod
                         left join perso_ia on pia_perso_cod=perso_cod
                         where perso_cod=:perso ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_monture), $stmt);
        if (!$result = $stmt->fetch()) return false;
        return $result["ordonable"] == 'O';
    }


    /**
     *regarde si la monture est chevauché est controlable par le joueur (le joueur peut se déplacer la monture suivra) ou le joueur n'a pas de monture
     */
    public function monture_controlable()
    {
        if ( !$this->perso_monture) return true ;

        $pdo  = new bddpdo;
        $req  = " select CASE WHEN perso_dirige_admin='N' and coalesce(pia_ia_type, gmon_type_ia) in (18) THEN 'N' ELSE 'O' END as controlable
                         from perso 
                         join monstre_generique on gmon_cod=perso_gmon_cod
                         left join perso_ia on pia_perso_cod=perso_cod
                         where perso_cod=:perso ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_monture), $stmt);
        if (!$result = $stmt->fetch()) return false;
        return $result["controlable"] == 'O';
    }

    /**
     *regarde si une monture est disponible pour être montée?
     */
    public function monture_chevauchable()
    {
        $pdo  = new bddpdo;
        $req  = "select m.perso_cod, m.perso_nom, 0 as dist 
                    from perso p 
                    join perso_position pp on p.perso_cod=pp.ppos_perso_cod
                    join perso_position pm on pm.ppos_pos_cod = pp.ppos_pos_cod and  pm.ppos_perso_cod<>pp.ppos_perso_cod
                    join perso m on m.perso_cod=pm.ppos_perso_cod and m.perso_type_perso=2 and m.perso_actif='O'
                    join monstre_generique on gmon_cod = m.perso_gmon_cod
                    where p.perso_cod=:perso and gmon_monture = 'O' and not exists (select * from perso where perso_monture = m.perso_cod )";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetchAll();

        # monture à 1 case pouvant être monter
        $req  = "select m.perso_cod, m.perso_nom, 1 as dist 
                    from perso p 
                    join perso_position pp on p.perso_cod=pp.ppos_perso_cod
					join positions cp on cp.pos_cod=pp.ppos_pos_cod
					join positions cm on cm.pos_cod<>cp.pos_cod 
									  and cm.pos_x >= (cp.pos_x - 1) and cm.pos_x <= (cp.pos_x + 1)
									  and cm.pos_y >= (cp.pos_y - 1) and cm.pos_y <= (cp.pos_y + 1)
									  and cm.pos_etage = cp.pos_etage
                    join perso_position pm on pm.ppos_pos_cod = cm.pos_cod 
                    join perso m on m.perso_cod=pm.ppos_perso_cod and m.perso_type_perso=2 and m.perso_actif='O'
                    join monstre_generique on gmon_cod = m.perso_gmon_cod
                    where p.perso_cod=:perso and gmon_monture = 'O' and coalesce(f_to_numeric(((m.perso_misc_param->>'monture_cavalier')::jsonb)->>'perso_cod')::integer, 0)=p.perso_cod and not exists (select * from perso where perso_monture = m.perso_cod )";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result = array_merge($result, $stmt->fetchAll());

        return $result;
    }

    /**
     *donne la liste des couple/monture est disponible pour être désarconner?
     */
    public function monture_desarconnable()
    {
        $pdo  = new bddpdo;
        $req  = "select m.perso_cod, m.perso_nom , mm.perso_cod as monture_perso_cod, mm.perso_nom monture_perso_nom
                    from perso p 
                    join perso_position pp on p.perso_cod=pp.ppos_perso_cod
                    join perso_position pm on pm.ppos_pos_cod = pp.ppos_pos_cod and  pm.ppos_perso_cod<>pp.ppos_perso_cod
                    join perso m on m.perso_cod=pm.ppos_perso_cod and m.perso_type_perso=1 and m.perso_actif='O'
                    join perso mm on mm.perso_cod=m.perso_monture and mm.perso_type_perso=2 and mm.perso_actif='O'
                    where p.perso_cod=:perso";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     *regarde si une monture est disponible pour être montée?
     */
    public function monture_chevaucher($monture)
    {
        $pdo    = new bddpdo();
        $req    = "select monture_chevaucher(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array( ":perso" => $this->perso_cod, ":cible" => $monture), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    /**
     *regarde si une monture est disponible pour être montée?
     */
    public function monture_dechevaucher()
    {
        $pdo    = new bddpdo();
        $req    = "select monture_dechevaucher(:perso) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    /**
     *regarde si une monture est disponible pour être montée?
     */
    public function monture_desarconner($cavalier)
    {
        $pdo    = new bddpdo();
        $req    = "select monture_desarconner(:perso, :cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $this->perso_cod, ":cible" => $cavalier), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }


    /**
     * donne un ordre à la monture (l'ia se chargera de le réaliser)
     */
    public function monture_ordre($ordre, $parametres)
    {
        $pdo    = new bddpdo();
        $req    = "select monture_ordonner(:perso, :ordre, :param) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $this->perso_cod, ":ordre" => $ordre, ":param" => json_encode($parametres)), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }


    public function membreTriplette($perso_cod)
    {
        $pdo    = new bddpdo();

        // récupération du compte joueur
        if ($this->perso_type_perso == 1) {
            $req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod=:perso_cod";
        } else {
            $req = "select pcompt_compt_cod from perso_compte inner join perso_familier on pfam_perso_cod = pcompt_perso_cod where pfam_familier_cod = :perso_cod ";
        }

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        if (!$result) return false;

        $compte_cod = $result["pcompt_compt_cod"];

        $req = "select count(*) as count from (
                    select pcompt_perso_cod as perso_cod
                        from perso_compte 
                        where pcompt_compt_cod = :compt_cod
                    union all
                    select perso_cod  as perso_cod from perso_compte
                        inner join perso_familier on pfam_perso_cod = pcompt_perso_cod
                        inner join perso on perso_cod=pfam_familier_cod
                        where pcompt_compt_cod = :compt_cod and perso_actif='O'
            ) persos_du_compte where perso_cod = :perso_cod ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $perso_cod, ":compt_cod" => $compte_cod), $stmt);
        $result = $stmt->fetch();
        if (!$result) return false;

        return $result['count'] ==  0 ? false : true ;
    }

    /**
     * retourne le % visté par le perso autour de la positions donnée dans le rayon limite donné
     * Si la position est null, la position du perso est prise en compte
     * Si la limite est null, tout l'étage est prise en compte
     */
    public function visite_etage($position = null, $limite = null)
    {
        $pdo    = new bddpdo();
        $req    = "select f_perso_visite_etage(:perso, :pos_cod, :limite) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso" => $this->perso_cod, ":pos_cod" => $position, ":limite" => $limite), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
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
                    $req    = "select perso_cod  from perso where " . substr($name, 6) . " = ? order by perso_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso;
                        $temp->charge($result["perso_cod"]);
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
                    die('Unknown variable ' . substr($name, 6));
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
