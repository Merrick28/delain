INSERT INTO public.sorts (sort_combinaison, sort_nom, sort_fonction, sort_cout, sort_comp_cod, sort_distance,
  sort_description,
  sort_aggressif, sort_niveau, sort_soi_meme, sort_monstre, sort_joueur, sort_soutien,
  sort_bloquable, sort_case, sort_temps_recharge)
VALUES ('221113', 'Fontaine de jouvence', 'magie_jouvence', 12, 50, 1,
	'Ce vieux sort, au vieux style, est efficace davantage par les effets thérapeutique du liquide que dans son mode d''administration. En raison des liens si forts que vous entretenez avec vos camarades de voyage, vous pouvez d''un coup tous et toutes les soulager de leurs blessures, en invoquant tout un baquet d''eau magique et froide au dessus de leurs têtes. Pendant la durée du sort, le guérisseur cible INT compagnons (INT/2 pour le maître du Savoir), qu''il soigne selon l''urgence de leurs blessures. Soin important modifié de X. Note : Bien faire bouillir les runes avant de lancer le sort.',
  'N', 6, 'N', 'N', 'N', 'O',
	'N', 'O', 0);

INSERT INTO sort_rune(srune_sort_cod,srune_gobj_cod)
select 177,gobj_cod from objet_generique where gobj_rune_position = 2 and gobj_frune_cod = 1
UNION
select 177,gobj_cod from objet_generique where gobj_rune_position = 2 and gobj_frune_cod = 2
UNION
select 177,gobj_cod from objet_generique where gobj_rune_position = 1 and gobj_frune_cod = 3
UNION
select 177,gobj_cod from objet_generique where gobj_rune_position = 1 and gobj_frune_cod = 4
UNION
select 177,gobj_cod from objet_generique where gobj_rune_position = 1 and gobj_frune_cod = 5
UNION
select 177,gobj_cod from objet_generique where gobj_rune_position = 3 and gobj_frune_cod = 6