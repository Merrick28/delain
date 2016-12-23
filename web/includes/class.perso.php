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
    var $perso_pv = 0;
    var $perso_pv_max;
    var $perso_dlt;
    var $perso_temps_tour;
    var $perso_email;
    var $perso_dcreat;
    var $perso_validation;
    var $perso_actif = 'N';
    var $perso_pa = 12;
    var $perso_der_connex;
    var $perso_des_regen = 1;
    var $perso_valeur_regen = 3;
    var $perso_vue = 3;
    var $perso_po = 0;
    var $perso_nb_esquive;
    var $perso_niveau = 1;
    var $perso_type_perso = 1;
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
    var $perso_reputation = 0;
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
    var $perso_utl_pa_rest = 1;
    var $perso_tangible = 'O';
    var $perso_nb_tour_intangible = 0;
    var $perso_capa_repar;
    var $perso_nb_amel_repar = 0;
    var $perso_amelioration_nb_sort = 0;
    var $perso_renommee_magie = 0;
    var $perso_vampirisme = 0;
    var $perso_niveau_vampire = 0;
    var $perso_admin_echoppe;
    var $perso_nb_amel_comp = 0;
    var $perso_nb_receptacle = 0;
    var $perso_nb_amel_chance_memo = 0;
    var $perso_priere = 0;
    var $perso_dfin;
    var $perso_px = 0;
    var $perso_taille = 3;
    var $perso_admin_echoppe_noir = 'N';
    var $perso_use_repart_auto = 1;
    var $perso_pnj = 0;
    var $perso_redispatch = 'N';
    var $perso_nb_redist = 0;
    var $perso_mcom_cod = 0;
    var $perso_nb_ch_mcom = 0;
    var $perso_piq_rap_env = 1;
    var $perso_ancien_avatar;
    var $perso_nb_crap = 0;
    var $perso_nb_embr = 0;
    var $perso_crapaud = 0;
    var $perso_dchange_mcom;
    var $perso_prestige = 0;
    var $perso_av_mod = 0;
    var $perso_mail_inactif_envoye;
    var $perso_test;
    var $perso_nb_spe = 1;
    var $perso_compt_pvp = 0;
    var $perso_dmodif_compt_pvp;
    var $perso_effets_auto = 1;
    var $perso_quete;
    var $perso_tuteur = false;
    var $perso_voie_magique = 0;
    var $perso_energie = 0;
    var $perso_desc_long;
    var $perso_nb_mort_arene = 0;
    var $perso_nb_joueur_tue_arene = 0;
    var $perso_dfin_tangible;
    var $perso_renommee_artisanat = 0;
    var $perso_avatar_version = 0;
    var $perso_etage_origine;
    var $perso_monstre_attaque_monstre;
    var $perso_mortel = NULL;
    var $alterego = 0;

    function __construct()
    {

        $this->perso_dcreat = date('Y-m-d H:i:s');
        $this->perso_der_connex = date('Y-m-d H:i:s');
        $this->perso_dchange_mcom = date('Y-m-d H:i:s');
        $this->perso_dmodif_compt_pvp = date('Y-m-d H:i:s');
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
            $req = "insert into perso (
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
                ":perso_for" => $this->perso_for,
                ":perso_dex" => $this->perso_dex,
                ":perso_int" => $this->perso_int,
                ":perso_con" => $this->perso_con,
                ":perso_for_init" => $this->perso_for_init,
                ":perso_dex_init" => $this->perso_dex_init,
                ":perso_int_init" => $this->perso_int_init,
                ":perso_con_init" => $this->perso_con_init,
                ":perso_sex" => $this->perso_sex,
                ":perso_race_cod" => $this->perso_race_cod,
                ":perso_pv" => $this->perso_pv,
                ":perso_pv_max" => $this->perso_pv_max,
                ":perso_dlt" => $this->perso_dlt,
                ":perso_temps_tour" => $this->perso_temps_tour,
                ":perso_email" => $this->perso_email,
                ":perso_dcreat" => $this->perso_dcreat,
                ":perso_validation" => $this->perso_validation,
                ":perso_actif" => $this->perso_actif,
                ":perso_pa" => $this->perso_pa,
                ":perso_der_connex" => $this->perso_der_connex,
                ":perso_des_regen" => $this->perso_des_regen,
                ":perso_valeur_regen" => $this->perso_valeur_regen,
                ":perso_vue" => $this->perso_vue,
                ":perso_po" => $this->perso_po,
                ":perso_nb_esquive" => $this->perso_nb_esquive,
                ":perso_niveau" => $this->perso_niveau,
                ":perso_type_perso" => $this->perso_type_perso,
                ":perso_amelioration_vue" => $this->perso_amelioration_vue,
                ":perso_amelioration_regen" => $this->perso_amelioration_regen,
                ":perso_amelioration_degats" => $this->perso_amelioration_degats,
                ":perso_amelioration_armure" => $this->perso_amelioration_armure,
                ":perso_nb_des_degats" => $this->perso_nb_des_degats,
                ":perso_val_des_degats" => $this->perso_val_des_degats,
                ":perso_cible" => $this->perso_cible,
                ":perso_enc_max" => $this->perso_enc_max,
                ":perso_description" => $this->perso_description,
                ":perso_nb_mort" => $this->perso_nb_mort,
                ":perso_nb_monstre_tue" => $this->perso_nb_monstre_tue,
                ":perso_nb_joueur_tue" => $this->perso_nb_joueur_tue,
                ":perso_reputation" => $this->perso_reputation,
                ":perso_avatar" => $this->perso_avatar,
                ":perso_kharma" => $this->perso_kharma,
                ":perso_amel_deg_dex" => $this->perso_amel_deg_dex,
                ":perso_nom" => $this->perso_nom,
                ":perso_gmon_cod" => $this->perso_gmon_cod,
                ":perso_renommee" => $this->perso_renommee,
                ":perso_dirige_admin" => $this->perso_dirige_admin,
                ":perso_lower_perso_nom" => $this->perso_lower_perso_nom,
                ":perso_sta_combat" => $this->perso_sta_combat,
                ":perso_sta_hors_combat" => $this->perso_sta_hors_combat,
                ":perso_utl_pa_rest" => $this->perso_utl_pa_rest,
                ":perso_tangible" => $this->perso_tangible,
                ":perso_nb_tour_intangible" => $this->perso_nb_tour_intangible,
                ":perso_capa_repar" => $this->perso_capa_repar,
                ":perso_nb_amel_repar" => $this->perso_nb_amel_repar,
                ":perso_amelioration_nb_sort" => $this->perso_amelioration_nb_sort,
                ":perso_renommee_magie" => $this->perso_renommee_magie,
                ":perso_vampirisme" => $this->perso_vampirisme,
                ":perso_niveau_vampire" => $this->perso_niveau_vampire,
                ":perso_admin_echoppe" => $this->perso_admin_echoppe,
                ":perso_nb_amel_comp" => $this->perso_nb_amel_comp,
                ":perso_nb_receptacle" => $this->perso_nb_receptacle,
                ":perso_nb_amel_chance_memo" => $this->perso_nb_amel_chance_memo,
                ":perso_priere" => $this->perso_priere,
                ":perso_dfin" => $this->perso_dfin,
                ":perso_px" => $this->perso_px,
                ":perso_taille" => $this->perso_taille,
                ":perso_admin_echoppe_noir" => $this->perso_admin_echoppe_noir,
                ":perso_use_repart_auto" => $this->perso_use_repart_auto,
                ":perso_pnj" => $this->perso_pnj,
                ":perso_redispatch" => $this->perso_redispatch,
                ":perso_nb_redist" => $this->perso_nb_redist,
                ":perso_mcom_cod" => $this->perso_mcom_cod,
                ":perso_nb_ch_mcom" => $this->perso_nb_ch_mcom,
                ":perso_piq_rap_env" => $this->perso_piq_rap_env,
                ":perso_ancien_avatar" => $this->perso_ancien_avatar,
                ":perso_nb_crap" => $this->perso_nb_crap,
                ":perso_nb_embr" => $this->perso_nb_embr,
                ":perso_crapaud" => $this->perso_crapaud,
                ":perso_dchange_mcom" => $this->perso_dchange_mcom,
                ":perso_prestige" => $this->perso_prestige,
                ":perso_av_mod" => $this->perso_av_mod,
                ":perso_mail_inactif_envoye" => $this->perso_mail_inactif_envoye,
                ":perso_test" => $this->perso_test,
                ":perso_nb_spe" => $this->perso_nb_spe,
                ":perso_compt_pvp" => $this->perso_compt_pvp,
                ":perso_dmodif_compt_pvp" => $this->perso_dmodif_compt_pvp,
                ":perso_effets_auto" => $this->perso_effets_auto,
                ":perso_quete" => $this->perso_quete,
                ":perso_tuteur" => $this->perso_tuteur,
                ":perso_voie_magique" => $this->perso_voie_magique,
                ":perso_energie" => $this->perso_energie,
                ":perso_desc_long" => $this->perso_desc_long,
                ":perso_nb_mort_arene" => $this->perso_nb_mort_arene,
                ":perso_nb_joueur_tue_arene" => $this->perso_nb_joueur_tue_arene,
                ":perso_dfin_tangible" => $this->perso_dfin_tangible,
                ":perso_renommee_artisanat" => $this->perso_renommee_artisanat,
                ":perso_avatar_version" => $this->perso_avatar_version,
                ":perso_etage_origine" => $this->perso_etage_origine,
                ":perso_monstre_attaque_monstre" => $this->perso_monstre_attaque_monstre,
                ":perso_mortel" => $this->perso_mortel,
                ":alterego" => $this->alterego,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update perso
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
                ":perso_cod" => $this->perso_cod,
                ":perso_for" => $this->perso_for,
                ":perso_dex" => $this->perso_dex,
                ":perso_int" => $this->perso_int,
                ":perso_con" => $this->perso_con,
                ":perso_for_init" => $this->perso_for_init,
                ":perso_dex_init" => $this->perso_dex_init,
                ":perso_int_init" => $this->perso_int_init,
                ":perso_con_init" => $this->perso_con_init,
                ":perso_sex" => $this->perso_sex,
                ":perso_race_cod" => $this->perso_race_cod,
                ":perso_pv" => $this->perso_pv,
                ":perso_pv_max" => $this->perso_pv_max,
                ":perso_dlt" => $this->perso_dlt,
                ":perso_temps_tour" => $this->perso_temps_tour,
                ":perso_email" => $this->perso_email,
                ":perso_dcreat" => $this->perso_dcreat,
                ":perso_validation" => $this->perso_validation,
                ":perso_actif" => $this->perso_actif,
                ":perso_pa" => $this->perso_pa,
                ":perso_der_connex" => $this->perso_der_connex,
                ":perso_des_regen" => $this->perso_des_regen,
                ":perso_valeur_regen" => $this->perso_valeur_regen,
                ":perso_vue" => $this->perso_vue,
                ":perso_po" => $this->perso_po,
                ":perso_nb_esquive" => $this->perso_nb_esquive,
                ":perso_niveau" => $this->perso_niveau,
                ":perso_type_perso" => $this->perso_type_perso,
                ":perso_amelioration_vue" => $this->perso_amelioration_vue,
                ":perso_amelioration_regen" => $this->perso_amelioration_regen,
                ":perso_amelioration_degats" => $this->perso_amelioration_degats,
                ":perso_amelioration_armure" => $this->perso_amelioration_armure,
                ":perso_nb_des_degats" => $this->perso_nb_des_degats,
                ":perso_val_des_degats" => $this->perso_val_des_degats,
                ":perso_cible" => $this->perso_cible,
                ":perso_enc_max" => $this->perso_enc_max,
                ":perso_description" => $this->perso_description,
                ":perso_nb_mort" => $this->perso_nb_mort,
                ":perso_nb_monstre_tue" => $this->perso_nb_monstre_tue,
                ":perso_nb_joueur_tue" => $this->perso_nb_joueur_tue,
                ":perso_reputation" => $this->perso_reputation,
                ":perso_avatar" => $this->perso_avatar,
                ":perso_kharma" => $this->perso_kharma,
                ":perso_amel_deg_dex" => $this->perso_amel_deg_dex,
                ":perso_nom" => $this->perso_nom,
                ":perso_gmon_cod" => $this->perso_gmon_cod,
                ":perso_renommee" => $this->perso_renommee,
                ":perso_dirige_admin" => $this->perso_dirige_admin,
                ":perso_lower_perso_nom" => $this->perso_lower_perso_nom,
                ":perso_sta_combat" => $this->perso_sta_combat,
                ":perso_sta_hors_combat" => $this->perso_sta_hors_combat,
                ":perso_utl_pa_rest" => $this->perso_utl_pa_rest,
                ":perso_tangible" => $this->perso_tangible,
                ":perso_nb_tour_intangible" => $this->perso_nb_tour_intangible,
                ":perso_capa_repar" => $this->perso_capa_repar,
                ":perso_nb_amel_repar" => $this->perso_nb_amel_repar,
                ":perso_amelioration_nb_sort" => $this->perso_amelioration_nb_sort,
                ":perso_renommee_magie" => $this->perso_renommee_magie,
                ":perso_vampirisme" => $this->perso_vampirisme,
                ":perso_niveau_vampire" => $this->perso_niveau_vampire,
                ":perso_admin_echoppe" => $this->perso_admin_echoppe,
                ":perso_nb_amel_comp" => $this->perso_nb_amel_comp,
                ":perso_nb_receptacle" => $this->perso_nb_receptacle,
                ":perso_nb_amel_chance_memo" => $this->perso_nb_amel_chance_memo,
                ":perso_priere" => $this->perso_priere,
                ":perso_dfin" => $this->perso_dfin,
                ":perso_px" => $this->perso_px,
                ":perso_taille" => $this->perso_taille,
                ":perso_admin_echoppe_noir" => $this->perso_admin_echoppe_noir,
                ":perso_use_repart_auto" => $this->perso_use_repart_auto,
                ":perso_pnj" => $this->perso_pnj,
                ":perso_redispatch" => $this->perso_redispatch,
                ":perso_nb_redist" => $this->perso_nb_redist,
                ":perso_mcom_cod" => $this->perso_mcom_cod,
                ":perso_nb_ch_mcom" => $this->perso_nb_ch_mcom,
                ":perso_piq_rap_env" => $this->perso_piq_rap_env,
                ":perso_ancien_avatar" => $this->perso_ancien_avatar,
                ":perso_nb_crap" => $this->perso_nb_crap,
                ":perso_nb_embr" => $this->perso_nb_embr,
                ":perso_crapaud" => $this->perso_crapaud,
                ":perso_dchange_mcom" => $this->perso_dchange_mcom,
                ":perso_prestige" => $this->perso_prestige,
                ":perso_av_mod" => $this->perso_av_mod,
                ":perso_mail_inactif_envoye" => $this->perso_mail_inactif_envoye,
                ":perso_test" => $this->perso_test,
                ":perso_nb_spe" => $this->perso_nb_spe,
                ":perso_compt_pvp" => $this->perso_compt_pvp,
                ":perso_dmodif_compt_pvp" => $this->perso_dmodif_compt_pvp,
                ":perso_effets_auto" => $this->perso_effets_auto,
                ":perso_quete" => $this->perso_quete,
                ":perso_tuteur" => $this->perso_tuteur,
                ":perso_voie_magique" => $this->perso_voie_magique,
                ":perso_energie" => $this->perso_energie,
                ":perso_desc_long" => $this->perso_desc_long,
                ":perso_nb_mort_arene" => $this->perso_nb_mort_arene,
                ":perso_nb_joueur_tue_arene" => $this->perso_nb_joueur_tue_arene,
                ":perso_dfin_tangible" => $this->perso_dfin_tangible,
                ":perso_renommee_artisanat" => $this->perso_renommee_artisanat,
                ":perso_avatar_version" => $this->perso_avatar_version,
                ":perso_etage_origine" => $this->perso_etage_origine,
                ":perso_monstre_attaque_monstre" => $this->perso_monstre_attaque_monstre,
                ":perso_mortel" => $this->perso_mortel,
                ":alterego" => $this->alterego,
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
        $pdo = new bddpdo;
        $req = "select * from perso where perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->perso_cod = $result['perso_cod'];
        $this->perso_for = $result['perso_for'];
        $this->perso_dex = $result['perso_dex'];
        $this->perso_int = $result['perso_int'];
        $this->perso_con = $result['perso_con'];
        $this->perso_for_init = $result['perso_for_init'];
        $this->perso_dex_init = $result['perso_dex_init'];
        $this->perso_int_init = $result['perso_int_init'];
        $this->perso_con_init = $result['perso_con_init'];
        $this->perso_sex = $result['perso_sex'];
        $this->perso_race_cod = $result['perso_race_cod'];
        $this->perso_pv = $result['perso_pv'];
        $this->perso_pv_max = $result['perso_pv_max'];
        $this->perso_dlt = $result['perso_dlt'];
        $this->perso_temps_tour = $result['perso_temps_tour'];
        $this->perso_email = $result['perso_email'];
        $this->perso_dcreat = $result['perso_dcreat'];
        $this->perso_validation = $result['perso_validation'];
        $this->perso_actif = $result['perso_actif'];
        $this->perso_pa = $result['perso_pa'];
        $this->perso_der_connex = $result['perso_der_connex'];
        $this->perso_des_regen = $result['perso_des_regen'];
        $this->perso_valeur_regen = $result['perso_valeur_regen'];
        $this->perso_vue = $result['perso_vue'];
        $this->perso_po = $result['perso_po'];
        $this->perso_nb_esquive = $result['perso_nb_esquive'];
        $this->perso_niveau = $result['perso_niveau'];
        $this->perso_type_perso = $result['perso_type_perso'];
        $this->perso_amelioration_vue = $result['perso_amelioration_vue'];
        $this->perso_amelioration_regen = $result['perso_amelioration_regen'];
        $this->perso_amelioration_degats = $result['perso_amelioration_degats'];
        $this->perso_amelioration_armure = $result['perso_amelioration_armure'];
        $this->perso_nb_des_degats = $result['perso_nb_des_degats'];
        $this->perso_val_des_degats = $result['perso_val_des_degats'];
        $this->perso_cible = $result['perso_cible'];
        $this->perso_enc_max = $result['perso_enc_max'];
        $this->perso_description = $result['perso_description'];
        $this->perso_nb_mort = $result['perso_nb_mort'];
        $this->perso_nb_monstre_tue = $result['perso_nb_monstre_tue'];
        $this->perso_nb_joueur_tue = $result['perso_nb_joueur_tue'];
        $this->perso_reputation = $result['perso_reputation'];
        $this->perso_avatar = $result['perso_avatar'];
        $this->perso_kharma = $result['perso_kharma'];
        $this->perso_amel_deg_dex = $result['perso_amel_deg_dex'];
        $this->perso_nom = $result['perso_nom'];
        $this->perso_gmon_cod = $result['perso_gmon_cod'];
        $this->perso_renommee = $result['perso_renommee'];
        $this->perso_dirige_admin = $result['perso_dirige_admin'];
        $this->perso_lower_perso_nom = $result['perso_lower_perso_nom'];
        $this->perso_sta_combat = $result['perso_sta_combat'];
        $this->perso_sta_hors_combat = $result['perso_sta_hors_combat'];
        $this->perso_utl_pa_rest = $result['perso_utl_pa_rest'];
        $this->perso_tangible = $result['perso_tangible'];
        $this->perso_nb_tour_intangible = $result['perso_nb_tour_intangible'];
        $this->perso_capa_repar = $result['perso_capa_repar'];
        $this->perso_nb_amel_repar = $result['perso_nb_amel_repar'];
        $this->perso_amelioration_nb_sort = $result['perso_amelioration_nb_sort'];
        $this->perso_renommee_magie = $result['perso_renommee_magie'];
        $this->perso_vampirisme = $result['perso_vampirisme'];
        $this->perso_niveau_vampire = $result['perso_niveau_vampire'];
        $this->perso_admin_echoppe = $result['perso_admin_echoppe'];
        $this->perso_nb_amel_comp = $result['perso_nb_amel_comp'];
        $this->perso_nb_receptacle = $result['perso_nb_receptacle'];
        $this->perso_nb_amel_chance_memo = $result['perso_nb_amel_chance_memo'];
        $this->perso_priere = $result['perso_priere'];
        $this->perso_dfin = $result['perso_dfin'];
        $this->perso_px = $result['perso_px'];
        $this->perso_taille = $result['perso_taille'];
        $this->perso_admin_echoppe_noir = $result['perso_admin_echoppe_noir'];
        $this->perso_use_repart_auto = $result['perso_use_repart_auto'];
        $this->perso_pnj = $result['perso_pnj'];
        $this->perso_redispatch = $result['perso_redispatch'];
        $this->perso_nb_redist = $result['perso_nb_redist'];
        $this->perso_mcom_cod = $result['perso_mcom_cod'];
        $this->perso_nb_ch_mcom = $result['perso_nb_ch_mcom'];
        $this->perso_piq_rap_env = $result['perso_piq_rap_env'];
        $this->perso_ancien_avatar = $result['perso_ancien_avatar'];
        $this->perso_nb_crap = $result['perso_nb_crap'];
        $this->perso_nb_embr = $result['perso_nb_embr'];
        $this->perso_crapaud = $result['perso_crapaud'];
        $this->perso_dchange_mcom = $result['perso_dchange_mcom'];
        $this->perso_prestige = $result['perso_prestige'];
        $this->perso_av_mod = $result['perso_av_mod'];
        $this->perso_mail_inactif_envoye = $result['perso_mail_inactif_envoye'];
        $this->perso_test = $result['perso_test'];
        $this->perso_nb_spe = $result['perso_nb_spe'];
        $this->perso_compt_pvp = $result['perso_compt_pvp'];
        $this->perso_dmodif_compt_pvp = $result['perso_dmodif_compt_pvp'];
        $this->perso_effets_auto = $result['perso_effets_auto'];
        $this->perso_quete = $result['perso_quete'];
        $this->perso_tuteur = $result['perso_tuteur'];
        $this->perso_voie_magique = $result['perso_voie_magique'];
        $this->perso_energie = $result['perso_energie'];
        $this->perso_desc_long = $result['perso_desc_long'];
        $this->perso_nb_mort_arene = $result['perso_nb_mort_arene'];
        $this->perso_nb_joueur_tue_arene = $result['perso_nb_joueur_tue_arene'];
        $this->perso_dfin_tangible = $result['perso_dfin_tangible'];
        $this->perso_renommee_artisanat = $result['perso_renommee_artisanat'];
        $this->perso_avatar_version = $result['perso_avatar_version'];
        $this->perso_etage_origine = $result['perso_etage_origine'];
        $this->perso_monstre_attaque_monstre = $result['perso_monstre_attaque_monstre'];
        $this->perso_mortel = $result['perso_mortel'];
        $this->alterego = $result['alterego'];
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
        $pdo = new bddpdo;
        $req = "select perso_cod  from perso order by perso_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso;
            $temp->charge($result["perso_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByComptDerPerso($vcompte)
    {
        $compte = new compte;
        $compte->charge($vcompte);
        return $this->charge($compte->compt_der_perso_cod);
    }

    function get_pa_dep()
    {
        $pdo = new bddpdo;
        $req = 'select get_pa_dep(?) as pa';
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->perso_cod),$stmt);
        $result = $stmt->fetch();
        return $result['pa'];
    }

    function is_milice()
    {
        $pdo = new bddpdo;
        $req = 'select is_milice(?) as ismilice';
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->perso_cod),$stmt);
        $result = $stmt->fetch();
        return $result['ismilice'];
    }

    function isIntangible()
    {
        return $this->perso_tangible != 'O';
    }

    function existe_competence($comp_cod)
    {
        $comp = new perso_competences();
        return $comp->getByPersoComp($this->perso_cod,$comp);
    }

    function is_enchanteur()
    {
        $enchanteur1 = $this->existe_competence('88');
        $enchanteur2 = $this->existe_competence('102');
        $enchanteur3 = $this->existe_competence('103');
        return ($enchanteur1 || $enchanteur2 || $enchanteur3);
    }

    function is_enlumineur()
    {
        $enchanteur1 = $this->existe_competence('91');
        $enchanteur2 = $this->existe_competence('92');
        $enchanteur3 = $this->existe_competence('93');
        return ($enchanteur1 || $enchanteur2 || $enchanteur3);
    }

    function is_refuge()
    {
        $ppos = new perso_position();
        $ppos->getByPerso($this->perso_cod);
        $lpos = new lieu_position();
        $lpos->getByPos($ppos->ppos_pos_cod);
        $lieu = new lieu;
        if($lieu->charge($lpos->lpos_lieu_cod))
        {
            if($lieu->lieu_refuge == 'O')
            {
                return true;
            }
        }
        return false;
    }

    function is_fam()
    {
        if ($this->perso_type_perso == 3)
        {
            return true;
        }
        return false;
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
                    $req = "select perso_cod  from perso where " . substr($name, 6) . " = ? order by perso_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]), $stmt);
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