CREATE OR REPLACE FUNCTION public.is_visible_groupe(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*********************************************/
/* is_visible_groupe                         */
/*  $1 = groupe_cod                          */
/*  $2 = perso_cod                           */
/* retourne 1 si le perso est visible par    */
/*  le groupe                                */
/*********************************************/
declare
	code_retour integer;
	v_groupe alias for $1;
	v_perso alias for $2;
	v_chef integer;
	v_pos_chef integer;
	v_pos_perso integer;
	v_etage_chef integer;
	v_etage_perso integer;
begin
	select into v_chef
		groupe_chef
		from groupe,groupe_perso
		where pgroupe_perso_cod = v_perso
		and pgroupe_statut = 1
		and pgroupe_groupe_cod = groupe_cod;
	if not found then 
		return 0; -- pas de groupe associé au perso
	end if; 
	if v_chef = v_perso then
		return 1; -- le perso est le chef
	end if;
	select into v_pos_chef,v_etage_chef
		pos_cod,pos_etage
		from perso_position,positions
		where ppos_perso_cod = v_chef
		and ppos_pos_cod = pos_cod;
	if not found then 
		return 0;
	end if;
	select into v_pos_perso,v_etage_perso
		pos_cod,pos_etage
		from perso_position,positions
		where ppos_perso_cod = v_perso
		and ppos_pos_cod = pos_cod;
	if v_etage_perso != v_etage_chef then
		return 0;
	end if;
	if distance(v_pos_perso,v_pos_chef) <= 10 then
		return 1;
	end if;
	return 0;
end;$function$

