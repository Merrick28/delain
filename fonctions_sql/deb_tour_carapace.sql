CREATE OR REPLACE FUNCTION public.deb_tour_carapace(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* deb_tour_carapace augmentant l armure        */
/************************************************/
declare
	code_retour text;
	personnage alias for $1; 
	v_pos integer;
	ligne record;
	has_bloque integer;
	v_bloque_magie integer;
	v_pa_attaque integer;
	v_malus_degats integer;
	v_chance_toucher integer;
	texte_evt text;
	v_pa_dep integer;
begin
	select into v_pos
		ppos_pos_cod
		from perso_position 
		where ppos_perso_cod = personnage;
	if not found then
		return 'Anomalie sur position !';
	end if;
	if lancer_des(1,100) < 30 then
		perform ajoute_bonus(personnage, 'ARM', 2, 3);
		texte_evt := 'Des veines de couleur sombre commence à strier la peau de [perso_cod1]. Son apparence de plus en plus foncée la fait ressembler à une sorte de carapace !';
		 	insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
     			values(54,now(),1,personnage,texte_evt,'O','O');	
	end if;

	return 'OK';
end;$function$

