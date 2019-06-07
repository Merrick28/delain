INSERT INTO public.sorts (sort_combinaison, sort_nom, sort_fonction, sort_cout,
		sort_comp_cod, sort_distance, sort_description, sort_aggressif,
		sort_niveau, sort_soi_meme, sort_monstre, sort_joueur, sort_soutien,
		sort_bloquable, sort_case, sort_temps_recharge)
VALUES ('121243', 'Takatoukité', 'magie_takatokite', 12,
		51, 1, 'Ce sort retire l''armure équipée de la cible. Si l’armure est normale, elle tombe au sol pour les monstres ou retourne dans l''inventaire pour les aventuriers. Si il s''agit de l’armure naturelle (carapace, ...) d’un monstre, celle-ci est détruite. Si la cible ne porte pas d''armure équipée, rien ne se passe mais les PA sont perdus.', 'O',
		6, 'O', 'O', 'O', 'N',
		'O', 'N', 0);

INSERT INTO sort_rune(srune_sort_cod,srune_gobj_cod)
select 176,gobj_cod from objet_generique where gobj_rune_position = 1 and gobj_frune_cod = 1
UNION
select 176,gobj_cod from objet_generique where gobj_rune_position = 2 and gobj_frune_cod = 2
UNION
select 176,gobj_cod from objet_generique where gobj_rune_position = 1 and gobj_frune_cod = 3
UNION
select 176,gobj_cod from objet_generique where gobj_rune_position = 2 and gobj_frune_cod = 4
UNION
select 176,gobj_cod from objet_generique where gobj_rune_position = 4 and gobj_frune_cod = 5
UNION
select 176,gobj_cod from objet_generique where gobj_rune_position = 3 and gobj_frune_cod = 6 ;

