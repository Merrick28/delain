
DROP FUNCTION potions.pot_antidote(integer);
DROP FUNCTION potions.pot_biafine(integer);
DROP FUNCTION potions.pot_bibliothequaire(integer);
DROP FUNCTION potions.pot_bip(integer);
DROP FUNCTION potions.pot_bloque_magie_faible(integer);
DROP FUNCTION potions.pot_bloque_magie_forte(integer);
DROP FUNCTION potions.pot_bloque_magie_moyen(integer);
DROP FUNCTION potions.pot_cachette(integer);
DROP FUNCTION potions.pot_con_faible(integer);
DROP FUNCTION potions.pot_con_forte(integer);
DROP FUNCTION potions.pot_con_moyenne(integer);
DROP FUNCTION potions.pot_creusage_fort(integer);
DROP FUNCTION potions.pot_creusage(integer);
DROP FUNCTION potions.pot_deadbull(integer);
DROP FUNCTION potions.pot_dex_faible(integer);
DROP FUNCTION potions.pot_dex_forte(integer);
DROP FUNCTION potions.pot_dex_moyenne(integer);
DROP FUNCTION potions.pot_enchanteur(integer);
DROP FUNCTION potions.pot_flash_halafish(integer);
DROP FUNCTION potions.pot_force_faible(integer);
DROP FUNCTION potions.pot_force_forte(integer);
DROP FUNCTION potions.pot_force_moyenne(integer);
DROP FUNCTION potions.pot_gant_dalga(integer);
DROP FUNCTION potions.pot_hortophilie_faible(integer);
DROP FUNCTION potions.pot_hortophilie_forte(integer);
DROP FUNCTION potions.pot_intelligence_faible(integer);
DROP FUNCTION potions.pot_intelligence_forte(integer);
DROP FUNCTION potions.pot_intelligence_moyenne(integer);
DROP FUNCTION potions.pot_langueur_duurstaf(integer);
DROP FUNCTION potions.pot_lithium(integer);
DROP FUNCTION potions.pot_mur_tour(integer);
DROP FUNCTION potions.pot_oeil_dalga(integer);
DROP FUNCTION potions.pot_hortophilie_moyenne(integer);
DROP FUNCTION potions.pot_hortophilie_ultime(integer);
DROP FUNCTION potions.pot_poing_duurstaf(integer);
DROP FUNCTION potions.pot_poulpe_halafish(integer);
DROP FUNCTION potions.pot_prosa_cola(integer);
DROP FUNCTION potions.pot_rage_durrstaf(integer);
DROP FUNCTION potions.pot_remede(integer);
DROP FUNCTION potions.pot_runes(integer);
DROP FUNCTION potions.pot_serum(integer);
DROP FUNCTION potions.pot_soufflet_dalga(integer);
DROP FUNCTION potions.pot_spray_mirreck(integer);
DROP FUNCTION potions.pot_test(integer);
DROP FUNCTION potions.pot_tour_cachee(integer);
DROP FUNCTION potions.pot_vie_de_sang(integer);
DROP FUNCTION potions.pot_vue_de_la_tour(integer);

INSERT INTO parametres(parm_type, parm_desc, parm_valeur, parm_valeur_texte) VALUES
( 'Integer', 'Distance max pour faire boire une potion.', 0, null),
( 'Text', 'A qui peut-on faire boire une potion (S=Soi-même, 3=Triplette, G=Groupe (coterie+triplette), P=Perso+fam, T=Tous)?', null, 'T');

INSERT INTO type_evt(tevt_libelle, tevt_texte) VALUES
( 'Potion', '[attaquant] a fait boire une potion à [cible].');
