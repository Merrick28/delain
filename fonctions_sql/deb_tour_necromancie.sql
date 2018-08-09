CREATE OR REPLACE FUNCTION public.deb_tour_necromancie(integer, numeric, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Nécromancie                                  */
/*  $1 = monstre invocateur                     */
/*  $2 = facteur de puissance de nécromancie    */
/*  $3 = chance de necromancie                  */
/************************************************/
declare
	code_retour text;
	monstre alias for $1;
	v_necro alias for $2;
	v_chance_necro alias for $3;
	v_cible integer;
	v_des integer;
	v_des2 numeric;
	v_pos integer;
	v_monstre integer;
	texte_evt text;
	ligne record;


begin
	code_retour := '';
	v_des := lancer_des(1,100); 
	code_retour := code_retour || 'lancer: ' || trim(to_char(v_des,'99999999'));
	if v_des < v_chance_necro then
	select into v_cible perso_cible from perso where perso_cod = monstre;
	code_retour := code_retour || 'Cible: ' || trim(to_char(v_cible,'99999999'));
        if v_cible is not null then
	select into v_pos
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = v_cible;
	code_retour := code_retour || 'Position: ' || trim(to_char(v_pos,'99999999'));
	v_des2 := (lancer_des(1,100) * v_necro);

	code_retour := code_retour || 'Dé corrigé: ' || trim(to_char(v_des2,'99999999'));

	if v_des2 < 20 then
		v_monstre := cree_monstre_pos(19,v_pos);
	code_retour := code_retour || 'M19';
	elsif v_des2 < 50 then
		v_monstre := cree_monstre_pos(207,v_pos);
	code_retour := code_retour || 'M207';
	elsif v_des2 < 65 then
		v_monstre := cree_monstre_pos(210,v_pos);
	code_retour := code_retour || 'M210';
	elsif v_des2 < 75 then
		v_monstre := cree_monstre_pos(28,v_pos);
	code_retour := code_retour || 'M28';
	elsif v_des2 < 80 then
		v_monstre := cree_monstre_pos(170,v_pos);
	code_retour := code_retour || 'M170';
	elsif v_des2 < 85 then
		v_monstre := cree_monstre_pos(175,v_pos);
	code_retour := code_retour || 'M175';
	elsif v_des2 < 90 then
		v_monstre := cree_monstre_pos(181,v_pos);	
	code_retour := code_retour || 'M181';
	elsif v_des2 >= 90 then
		v_monstre := cree_monstre_pos(253,v_pos);	
	code_retour := code_retour || 'M253';
	end if;
	texte_evt := 'Face à la menace, [perso_cod1] a invoqué un mort vivant.';
	
	insert into ligne_evt (levt_tevt_cod,levt_texte,levt_perso_cod1,levt_lu,levt_visible)
		values
		(53,texte_evt,monstre,'O','O');
    end if;
end if;
    return code_retour;
end;$function$

