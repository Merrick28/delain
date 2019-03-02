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
    var $perso_cod;
    var $perso_for;
    var $perso_dex;
    var $perso_int;
    var $perso_con;
    var $perso_for_init;
    var $perso_dex_init;
    var $perso_int_init;
    var $perso_con_init;
    var $perso_sex;
    var $perso_race_cod;
    var $perso_pv                   = 0;
    var $perso_pv_max;
    var $perso_dlt;
    var $perso_temps_tour;
    var $perso_email;
    var $perso_dcreat;
    var $perso_validation;
    var $perso_actif                = 'N';
    var $perso_pa                   = 12;
    var $perso_der_connex;
    var $perso_des_regen            = 1;
    var $perso_valeur_regen         = 3;
    var $perso_vue                  = 3;
    var $perso_po                   = 0;
    var $perso_nb_esquive;
    var $perso_niveau               = 1;
    var $perso_type_perso           = 1;
    var $perso_amelioration_vue;
    var $perso_amelioration_regen;
    var $perso_amelioration_degats;
    var $perso_amelioration_armure;
    var $perso_nb_des_degats;
    var $perso_val_des_degats;
    var $perso_cible;
    var $perso_enc_max;
    var $perso_description;
    var $perso_nb_mort;
    var $perso_nb_monstre_tue;
    var $perso_nb_joueur_tue;
    var $perso_reputation           = 0;
    var $perso_avatar;
    var $perso_kharma;
    var $perso_amel_deg_dex;
    var $perso_nom;
    var $perso_gmon_cod;
    var $perso_renommee;
    var $perso_dirige_admin;
    var $perso_lower_perso_nom;
    var $perso_sta_combat;
    var $perso_sta_hors_combat;
    var $perso_utl_pa_rest          = 1;
    var $perso_tangible             = 'O';
    var $perso_nb_tour_intangible   = 0;
    var $perso_capa_repar;
    var $perso_nb_amel_repar        = 0;
    var $perso_amelioration_nb_sort = 0;
    var $perso_renommee_magie       = 0;
    var $perso_vampirisme           = 0;
    var $perso_niveau_vampire       = 0;
    var $perso_admin_echoppe;
    var $perso_nb_amel_comp         = 0;
    var $perso_nb_receptacle        = 0;
    var $perso_nb_amel_chance_memo  = 0;
    var $perso_priere               = 0;
    var $perso_dfin;
    var $perso_px                   = 0;
    var $perso_taille               = 3;
    var $perso_admin_echoppe_noir   = 'N';
    var $perso_use_repart_auto      = 1;
    var $perso_pnj                  = 0;
    var $perso_redispatch           = 'N';
    var $perso_nb_redist            = 0;
    var $perso_mcom_cod             = 0;
    var $perso_nb_ch_mcom           = 0;
    var $perso_piq_rap_env          = 1;
    var $perso_ancien_avatar;
    var $perso_nb_crap              = 0;
    var $perso_nb_embr              = 0;
    var $perso_crapaud              = 0;
    var $perso_dchange_mcom;
    var $perso_prestige             = 0;
    var $perso_av_mod               = 0;
    var $perso_mail_inactif_envoye;
    var $perso_test;
    var $perso_nb_spe               = 1;
    var $perso_compt_pvp            = 0;
    var $perso_dmodif_compt_pvp;
    var $perso_effets_auto          = 1;
    var $perso_quete;
    var $perso_tuteur               = false;
    var $perso_voie_magique         = 0;
    var $perso_energie              = 0;
    var $perso_desc_long;
    var $perso_nb_mort_arene        = 0;
    var $perso_nb_joueur_tue_arene  = 0;
    var $perso_dfin_tangible;
    var $perso_renommee_artisanat   = 0;
    var $perso_avatar_version       = 0;
    var $perso_etage_origine;
    var $perso_monstre_attaque_monstre;
    var $perso_mortel               = NULL;
    var $alterego                   = 0;
    //
    var $position;
    var $guilde;
    var $avatar;
    var $perso_vide = false;
    var $msg_non_lu;
    var $avatar_largeur;
    var $avatar_hauteur;
    var $barre_divine;
    //
    // Variables qui ne serviront que pour la vue
    //


    function __construct()
    {

        $this->perso_dcreat           = date('Y-m-d H:i:s');
        $this->perso_der_connex       = date('Y-m-d H:i:s');
        $this->perso_dchange_mcom     = date('Y-m-d H:i:s');
        $this->perso_dmodif_compt_pvp = date('Y-m-d H:i:s');
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @global bddpdo $pdo
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req
                  = "insert into perso (
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
            alterego                        )
                    values
                    (
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
                        :alterego                        )
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
            alterego = :alterego                        where perso_cod = :perso_cod ";
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
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de perso
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
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
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso
     */
    function getAll()
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

    function has_evt_non_lu()
    {
        $ligne_evt = new ligne_evt();
        $tab_evt   = $ligne_evt->getByPersoNonLu($this->perso_cod);
        if (count($tab_evt) != 0)
        {
            return true;
        }
        return false;
    }

    function has_arme_distance()
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

    function get_arme_equipee()
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

    function get_mode_combat()
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

    function get_pa_attaque()
    {

        $pdo      = new bddpdo;
        $req_arme = "select nb_pa_attaque(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    function portee_attaque()
    {

        $pdo      = new bddpdo;
        $req_arme = "select portee_attaque(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    function distance_vue()
    {

        $pdo      = new bddpdo;
        $req_arme = "select distance_vue(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    function type_arme()
    {

        $pdo      = new bddpdo;
        $req_arme = "select type_arme(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    function get_pa_foudre()
    {

        $pdo      = new bddpdo;
        $req_arme = "select nb_pa_foudre(:perso) as pa";
        $stmt     = $pdo->prepare($req_arme);
        $stmt     = $pdo->execute(array(":perso" => $this->perso_cod), $stmt);
        $result   = $stmt->fetch();
        return $result['pa'];
    }

    function has_competence($competence)
    {
        $pcomp = new perso_competences();
        if ($pcomp->getByPersoComp($this->perso_cod, $competence))
        {
            return true;
        }
        return false;
    }

    function getByComptDerPerso($vcompte)
    {
        $compte = new compte;
        $compte->charge($vcompte);
        return $this->charge($compte->compt_der_perso_cod);
    }

    function get_pa_dep()
    {
        $pdo    = new bddpdo;
        $req    = 'select get_pa_dep(?) as pa';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['pa'];
    }

    function is_milice()
    {
        $pdo    = new bddpdo;
        $req    = 'select is_milice(?) as ismilice';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['ismilice'];
    }

    function isIntangible()
    {
        return $this->perso_tangible != 'O';
    }

    function is_enlumineur()
    {
        $test1 = $this->existe_competence('91');
        $test2 = $this->existe_competence('92');
        $test3 = $this->existe_competence('93');
        return ($test1 || $test2 || $test3);
    }

    function existe_competence($comp_cod)
    {
        $comp = new perso_competences();
        return $comp->getByPersoComp($this->perso_cod, $comp_cod);
    }

    function is_potions()
    {
        $test1 = $this->existe_competence('97');
        $test2 = $this->existe_competence('100');
        $test3 = $this->existe_competence('101');
        return ($test1 || $test2 || $test3);
    }

    function is_refuge()
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

    function get_position()
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

    function get_position_object()
    {
        $ppos = new perso_position();
        $ppos->getByPerso($this->perso_cod);
        $pos = new positions();
        $pos->charge($ppos->ppos_pos_cod);
        return $pos;
    }

    function get_favoris()
    {
        $pdo    = new bddpdo;
        $retour = array();

        $req  = "SELECT pfav_cod, pfav_type, pfav_misc_cod, pfav_nom, pfav_function_cout_pa, pfav_link FROM public.perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod order by pfav_nom";
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

            $retour[] = array(  "pfav_cod" => $result["pfav_cod"],
                                "nom" =>  $result2["cout_pa"]> 12 ? $result["pfav_nom"] : $result["pfav_nom"] . " (" . $result2["cout_pa"] . " PA)",
                                "link" => $result["pfav_link"],
                                "pfav_type" => $result["pfav_type"],
                                "pfav_misc_cod" => $result["pfav_misc_cod"]);
        }
        return $retour;
    }

    function get_cout_pa_magie($sort, $type_lance)
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

    function get_nb_sort_memorisable()
    {
        $pdo    = new bddpdo;
        $req    = "SELECT nb_sort_memorisable(:perso) as nb_sort_memorisable";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute( array(":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['nb_sort_memorisable'];
    }

    function get_nb_sort_appris()
    {
        $pdo    = new bddpdo;
        $req    = "select count(*) as nb_sorts_appris from perso_sorts where psort_perso_cod = :perso ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute( array(":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['nb_sorts_appris'];
    }

    function is_fam()
    {
        if ($this->perso_type_perso == 3)
        {
            return true;
        }
        return false;
    }

    function is_monstre()
    {
        if ($this->perso_type_perso == 2)
        {
            return true;
        }
        return false;
    }

    function is_4eme_perso()
    {
        if ($this->perso_pnj == 2)
        {
            return true;
        }
        return false;
    }

    function is_admin_dieu()
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

    function is_religion()
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

    function is_fidele_gerant()
    {
        $tf  = new temple_fidele();
        $tab = $tf->getBy_tfid_perso_cod($this->perso_cod);
        if ($tab === false)
        {
            return false;
        }
        return true;
    }

    function transactions()
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

    function barre_hp()
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
            } else if (($barre_hp <= 2) && ($this->perso_pv > 0))
            {
                $barre_hp = 2;
            } else if ($barre_hp < 0)
            {
                $barre_hp = 0;
            } else if ($barre_hp >= 100)
            {
                $barre_hp = 100;
            }
        }
        return $barre_hp;
    }

    function barre_energie()
    {
        if ($this->is_enchanteur())
        {
            $barre_energie = round($this->perso_energie);
            if ($barre_energie <= 0)
            {
                $barre_energie = 0;
            } else if ($barre_energie >= 100)
            {
                $barre_energie = 100;
            } else if ($barre_energie >= 98)
            {
                $barre_energie = 98;
            } else if ($barre_energie <= 2)
            {
                $barre_energie = 2;
            }
            return $barre_energie;
        }
        return false;
    }

    function is_enchanteur()
    {
        $test1 = $this->existe_competence('88');
        $test2 = $this->existe_competence('102');
        $test3 = $this->existe_competence('103');
        return ($test1 || $test2 || $test3);
    }

    function barre_divin()
    {
        if ($this->is_fam_divin() == 1)
        {
            $energie_divine = $this->energie_divine();
            $barre_divine   = round(100 * $energie_divine / 200);
            if ($barre_divine <= 0)
            {
                $barre_divine = 0;
            } else if ($barre_divine >= 100)
            {
                $barre_divine = 100;
            } else if ($barre_divine >= 98)
            {
                $barre_divine = 98;
            } else if ($barre_divine <= 2)
            {
                $barre_divine = 2;
            }
            return $barre_divine;
        }
        return false;
    }

    function is_fam_divin()
    {
        $is_fam_divin = 0;
        if ($this->perso_gmon_cod == 441)
        {
            $is_fam_divin = 1;
        }
        return $is_fam_divin;
    }

    function energie_divine()
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

    function degats_perso()
    {
        $pdo    = new bddpdo;
        $req    = "select degats_perso(?) as degats_perso";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['degats_perso'];
    }

    function relache_monstre_4e_perso()
    {
        $pdo    = new bddpdo;
        $req    = "select relache_monstre_4e_perso(?) as degats_perso";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['degats_perso'];
    }

    function armure()
    {
        $pdo    = new bddpdo;
        $req    = "select f_armure_perso(?) as armure";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['armure'];
    }

    // Retourne vrai si le perso est sur un endroit permettant le démarrage d'une nouvelle quête (quete auto ou standard)
    function is_perso_quete()
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

        if ($result['nombre'] != 0) return true;        // il y a des quetes traditionnelles

        // Verification quete auto
        $quete     = new aquete;
        $tab_quete = $quete->get_debut_quete($this->perso_cod);
        return sizeof($tab_quete["quetes"]) > 0;
    }

    // Retourne vrai si le perso a au moins une quete auto en cours de réalisation ou terminée.
    function perso_nb_auto_quete()
    {
        $quete = new aquete_perso;
        return ($quete->get_perso_nb_quete($this->perso_cod));       // retourn un tableau nb_encours,nb_total
    }

    function get_lieu()
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
            $detail['lieu']      = $lieu;
            $detail['lieu_type'] = $lt;
            return $detail;
        }
        return false;
    }

    function getPersosActifs($type_joueur = 1)
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
    function getPersosByNameLike($perso_nom, $type_perso = 1)
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
        $req  = "select perso_cod from perso where perso_actif = 'O' and LOWER(perso_nom) = :perso_nom and perso_type_perso IN (" . implode(",", array_keys($list_types)) . ") and perso_pnj != 1 and perso_cod not in (1,2,3) ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array_merge(array(":perso_nom" => strtolower($perso_nom)), $list_types), $stmt);

        // Si on ne trouve rien avec une recherche exacte, on assouplie la règle de recherche
        if ($stmt->rowCount() == 0)
        {
            $req  = "select perso_cod from perso where perso_actif = 'O' and perso_nom ILIKE :perso_nom and perso_type_perso IN (" . implode(",", array_keys($list_types)) . ") and perso_pnj != 1 and perso_cod not in (1,2,3) ";
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


    function is_lieu()
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

    function missions()
    {
        $pdo    = new bddpdo;
        $req    = "select missions_verifie(?) as missions";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['missions'];
    }

    /**
     * @return ligne_evt[]
     */
    function getEvtNonLu()
    {
        $levt = new ligne_evt();
        return $levt->getByPersoNonLu($this->perso_cod);
    }

    function marqueEvtLus()
    {
        $levt = new ligne_evt();
        return $levt->marquePersoLu($this->perso_cod);
    }

    function barre_xp()
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

        $barre_xp = round(100 * $niveau_xp / $div_xp);
        if (($barre_xp >= 98) && ($niveau_xp < $div_xp))
        {
            $barre_xp = 98;
        } else if (($barre_xp <= 2) && ($niveau_xp > 0))
        {
            $barre_xp = 2;
        } else if ($barre_xp < 0)
        {
            $barre_xp = 0;
        } else if ($barre_xp >= 100)
        {
            $barre_xp = 100;
        }
        return $barre_xp;
    }

    function px_limite()
    {
        $pdo    = new bddpdo;
        $req    = "select limite_niveau(?) as limite_niveau";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['limite_niveau'];
    }

    function px_limite_actuel()
    {
        $pdo    = new bddpdo;
        $req    = "select limite_niveau_actuel(?) as limite_niveau";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['limite_niveau'];
    }

    function dlt_passee()
    {
        $pdo    = new bddpdo;
        $req    = "select dlt_passee(?) as dlt_passee";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['dlt_passee'];
    }

    function prochaine_dlt()
    {
        $pdo    = new bddpdo;
        $req    = "select prochaine_dlt(?) as prochaine_dlt";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['prochaine_dlt'];
    }
    
    function get_poids()
    {
        $pdo    = new bddpdo;
        $req    = "select get_poids(?) as get_poids";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return (float)$result['get_poids'];
    }

    function is_locked()
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

    function nb_obj_case()
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

    function nb_or_case()
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

    function sort_lvl5()
    {
        $pdo    = new bddpdo;
        $req
                = 'select count(1) as nv5 from perso, perso_nb_sorts_total, sorts 
            where perso_cod = pnbst_perso_cod 
            and pnbst_sort_cod = sort_cod 
            and sort_niveau >= 5 
            and pnbst_nombre > 0 
            and perso_voie_magique = 0 
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
    function sort_memo()
    {
        $ps  = new perso_sorts();
        $tab = $ps->getBy_psort_perso_cod($this->perso_cod);
        return $tab;
    }

    function calcul_dlt()
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

    function get_guilde()
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

    function desengagement($cible)
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

    function cree_revolution($cible)
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

    function embr($cible)
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

    function donne_bonbon($cible)
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

    function cree_groupe($nom_groupe)
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

    function accepte_invitation($groupe)
    {
        $pdo    = new bddpdo();
        $req    = "select accepte_invitation(:perso,:groupe) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
            ":perso"      => $this->perso_cod,
            ":groupe" => $groupe), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function refuse_invitation($groupe)
    {
        $pdo    = new bddpdo();
        $req    = "select refuse_invitation(:perso,:groupe) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
            ":perso"      => $this->perso_cod,
            ":groupe" => $groupe), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function regle_groupe($pa, $pv, $dlt, $bonus, $messages, $messagemort, $champions)
    {
        $pdo    = new bddpdo();
        $req    = "select regle_groupe(:perso,:pa,:pv,:dlt,:bonus,:messages,:messagemort,:champions) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
            ":perso"       => $this->perso_cod,
            ":pa"          => $pa,
            ":pv"          => $pv,
            ":dlt"         => $dlt,
            ":bonus"       => $bonus,
            ":messages"    => $messages,
            ":messagemort" => $messagemort,
            ":champions"   => $champions), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    /**
     * @param $nom
     * @return bool|perso
     * @throws Exception
     */
    function f_cherche_perso($nom)
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

    function teleportation_divine($dest)
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

    function invite_groupe($groupe, $invite)
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

    function f_enchantement($obj, $enc, $type_appel)
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

    function offre_boire($cible)
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

    function ouvre_cadeau()
    {
        $pdo    = new bddpdo();
        $req    = "select ouvre_cadeau(:perso) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
            ":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function donne_rouge()
    {
        $pdo    = new bddpdo();
        $req    = "select donne_rouge(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
            ":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function donne_noir()
    {
        $pdo    = new bddpdo();
        $req    = "select donne_noir(:perso,:cible) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
            ":perso" => $this->perso_cod), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function passe_niveau($amel)
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

    function rituel_modif_caracs($demel, $amel)
    {
        $pdo    = new bddpdo();
        $req    = "select f_rituel_modif_caracs(:perso,:demel,:amel) as resultat";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
                                ":perso" => $this->perso_cod,
                                ":demel"  => $demel,
                                ":amel"  => $amel), $stmt);
        $result = $stmt->fetch();
        return $result['resultat'];
    }

    function rituel_modif_voiemagique($voie)
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

    function depose_objet($objet)
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

    function vente_bat($objet)
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

    function vote_revolution($revguilde, $vote)
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

    function magasin_achat_generique($lieu, $objet, $qte)
    {
        $retour = "" ;
        $pdo    = new bddpdo();

        // Avant de faire la boucle on vérifie que les objest sont bien dans les stocks du magasin
        $req = "select sum(mgstock_nombre) count from stock_magasin_generique where mgstock_lieu_cod = :lieu and mgstock_gobj_cod = :objet and mgstock_vente_persos='O' ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(
            ":lieu"  => $lieu,
            ":objet" => $objet), $stmt);
        if ( !$result = $stmt->fetch())
        {
            return  '<p>Erreur ! Impossible de vérifier dans les stocks du magasin pour acheter cet objet  !';
        }
        else if ((int)$result['count']<$qte)
        {
            return  '<p>Erreur ! Impossible les stocks du magasin sont insuffisants  !';
        }

        // Faire les achats :
        for ($i = 0; $i < $qte; $i++)
        {
            $req = "select magasin_achat_generique(:perso,:lieu,:objet) as resultat ";
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

    function magasin_achat($lieu, $objet)
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

    function magasin_vente($lieu, $objet)
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

    function magasin_vente_generique($lieu, $objet)
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

    function magasin_identifie($lieu, $objet)
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

    function magasin_repare($lieu, $objet)
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

    function cree_receptacle($sort, $type_lance)
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

    function cree_parchemin($sort, $type_lance)
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

    function don_br($dest, $qte)
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

    function milice_tel($dest)
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

    function vente_auberge($objet)
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

    function change_mode_combat($mode)
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

    function detail_redispatch($amel)
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

    function start_redispatch()
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

    function achete_objet($objet)
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

    function prie_dieu($dieu)
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

    function ceremonie_dieu($dieu)
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

    function change_grade($dieu)
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

    function prie_dieu_ext($dieu)
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

    function niveau_blessures($pv=null, $pv_max=null)
    {
        // Sans paramètres on prends le perso courant
        if ($pv==null || $pv_max==null)
        {
            $pv = $this->perso_pv ;
            $pv_max = $this->perso_pv_max ;
        }

        $niveau_blessures = "" ;
        if ($pv / $pv_max < 0.15)
        {
            $niveau_blessures = 'presque mort';
        }
        else if ($pv / $pv_max < 0.25)
        {
            $niveau_blessures = 'gravement touché';
        }
        else if ($pv / $pv_max < 0.5)
        {
            $niveau_blessures = 'blessé';
        }
        else if ($pv / $pv_max < 0.75)
        {
            $niveau_blessures = 'touché';
        }
        return $niveau_blessures;
    }

    function repare_objet($type_rep, $objet)
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


    function prepare_for_tab_switch()
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
        } catch (Exception $e)
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
    function getMsgNonLu()
    {
        $msg_dest = new messages_dest();
        return $msg_dest->getByPersoNonLu($this->perso_cod);

    }

    function get_valeur_bonus($bonus)
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

    function prepare_get_vue()
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


        if ($this->perso_type_perso != 3)
        {
            if (!$perso_compte->get_by_perso($this->perso_cod))
            {
                die('Erreur d appel de compte');
            }

        } else
        {
            if (!$perso_compte->get_by_perso_fam($this->perso_cod))
            {
                die('Erreur d appel de compte');
            }
        }


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

    function get_vue_non_lock()
    {
        $this->prepare_get_vue();


        $pdo            = new bddpdo();
        $req_vue_joueur = "select trajectoire_vue(:pos_cod,pos_cod) as traj,
          perso_nom,pos_x,pos_y,pos_etage,race_nom,distance(:pos_cod,pos_cod) as distance,
          pos_cod,perso_cod,case when perso_type_perso = 1 then 1 else 2 end as perso_type_perso,
          perso_pv,perso_pv_max,is_surcharge(perso_cod,:perso) as surcharge , 
          (select count(1) from trajectoire_perso(:pos_cod,pos_cod) as (nv_cible int, v_pos int, type_perso int)) as obstruction 
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


    function get_vue_lock()
    {
        // position
        $this->prepare_get_vue();


        $pdo            = new bddpdo();
        $req_vue_joueur = "select trajectoire_vue(:pos_cod,pos_cod) as traj,perso_nom,pos_x,pos_y,pos_etage,
              race_nom,distance(:pos_cod,pos_cod) as distance,pos_cod,perso_cod,
              case when perso_type_perso = 1 then 1 else 2 end as perso_type_perso,
              perso_pv,perso_pv_max,is_surcharge(perso_cod,:perso) as surcharge, 0 as obstruction 
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
              select trajectoire_vue(:pos_cod,pos_cod) as traj,perso_nom,pos_x,pos_y,pos_etage,race_nom,
                distance(:pos_cod,pos_cod) as distance,pos_cod,perso_cod,perso_type_perso,
                perso_pv,perso_pv_max,is_surcharge(perso_cod,:perso ) as surcharge, 0 as obstruction 
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
                die('Unknown method.');
        }
    }
}