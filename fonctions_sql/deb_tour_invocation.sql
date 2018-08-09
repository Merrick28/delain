CREATE OR REPLACE FUNCTION public.deb_tour_invocation(integer, integer, integer, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Invocation de monstre                        */
/*  $1 = monstre invocateur                     */
/*  $2 = monstre invoqué			*/
/*  $3 = chance d’invocation	                */
/*  $4 = texte d’invocation pour les évènements */
/************************************************/
declare
	code_retour text;
	monstre alias for $1;
	v_invocation alias for $2;
	v_chance alias for $3;
	v_texte alias for $4;
	v_cible integer;
	v_des integer;
	v_des2 numeric;
	v_pos integer;
	v_monstre integer;
	v_nom_monstre text;
	ligne record;

begin
	code_retour := '';
	select into v_nom_monstre gmon_nom from monstre_generique where gmon_cod = v_invocation;
	v_des := lancer_des(1,100); 
	if v_des < v_chance then
		select into v_cible perso_cible from perso where perso_cod = monstre;
		/*if v_cible is null then
			v_cible := monstre;
		end if;*/
	        if v_cible is not null then
			code_retour := code_retour || 'Invocation de ' || v_nom_monstre || '.';
			select into v_pos ppos_pos_cod
			from perso_position where ppos_perso_cod = v_cible;

			v_monstre := cree_monstre_pos(v_invocation, v_pos);
			insert into ligne_evt (levt_tevt_cod, levt_texte, levt_perso_cod1, levt_lu, levt_visible) values (53, v_texte, monstre, 'O', 'O');
		end if;
	else
		code_retour := code_retour || 'Pas d’invocation de ' || v_nom_monstre || '.';
	end if;
	return code_retour;
end;
$function$

CREATE OR REPLACE FUNCTION public.deb_tour_invocation(integer, integer, integer, text, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Invocation de monstre                        */
/*  $1 = monstre invocateur                     */
/*  $2 = monstre invoqué			*/
/*  $3 = chance d’invocation	                */
/*  $4 = texte d’invocation pour les évènements */
/************************************************/
declare
	code_retour text;
	monstre alias for $1;
	v_invocation alias for $2;
	v_chance alias for $3;
	v_texte alias for $4;
v_pos alias for $5;
	v_cible integer;
	v_des integer;
	v_des2 numeric;
	v_monstre integer;
	v_nom_monstre text;
	ligne record;

begin
	code_retour := '';
	select into v_nom_monstre gmon_nom from monstre_generique where gmon_cod = v_invocation;
	v_des := lancer_des(1,100); 
	if v_des < v_chance then
		select into v_cible perso_cible from perso where perso_cod = monstre;
		code_retour := code_retour || 'Invocation de ' || v_nom_monstre || '.';
		v_monstre := cree_monstre_pos(v_invocation, v_pos);
		insert into ligne_evt (levt_tevt_cod, levt_texte, levt_perso_cod1, levt_lu, levt_visible) values (53, v_texte, monstre, 'O', 'O');
		
	else
		code_retour := code_retour || 'Pas d’invocation de ' || v_nom_monstre || '.';
	end if;
	return code_retour;
end;
$function$

CREATE OR REPLACE FUNCTION public.deb_tour_invocation(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Invocation de monstre                        */
/*  $1 = monstre invocateur                     */
/*  $2 = monstre invoqué			*/
/*  $3 = chance d'invocation	                */
/*  $4 = texte d'invocation pour les évènements */
/************************************************/
declare
	code_retour text;
	monstre alias for $1;
	v_invocation alias for $2;
	v_chance alias for $3;
	v_texte alias for $4;
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
	code_retour := code_retour || 'lancer: ' || v_des;
		if v_des < v_chance then
				select into v_cible perso_cible from perso where perso_cod = monstre;
				code_retour := code_retour || 'Cible: ' || v_cible;
			        if v_cible is not null then
				select into v_pos
					ppos_pos_cod
					from perso_position
					where ppos_perso_cod = v_cible;
				code_retour := code_retour || 'Position: ' || v_pos;
					v_monstre := cree_monstre_pos(v_invocation,v_pos);
				code_retour := code_retour || 'v_invocation';
				texte_evt := 'v_texte';
				insert into ligne_evt (levt_tevt_cod,levt_texte,levt_perso_cod1,levt_lu,levt_visible)
											values (53,texte_evt,monstre,'O','O');
			  			end if;
		end if;
    return code_retour;
end;$function$

