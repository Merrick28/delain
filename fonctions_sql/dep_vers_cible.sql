CREATE OR REPLACE FUNCTION public.dep_vers_cible(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*************************************************/
/* fonction dep_vers_cible                       */
/*   reçoit en paramètre :                       */
/*   $1 = position actuelle                      */
/*   $2 = position vers laquelle on veut aller   */
/* On a en sortie :                              */
/*   -1 si pas de position plus proche           */
/*   sinon le pos_cod touchant la position       */
/*      actuelle et qui se rapproche du $2       */
/*************************************************/
/* Créé le 16/05/2003                            */
/* Modif Blade le 14/11/2009  : mise en          */
/* commentaire lieu refuge, pour éviter les      */
/* blocage de l’IA                               */
/*************************************************/
declare
-- variables E/S
	code_retour integer;
	pos_actuelle alias for $1;
	pos_dest alias for $2;
-- variables de calcul
	distance_init integer;
	distance_temp integer;
	nouvelle_pos integer;
	r_pos record;
	etage integer;
	v_x integer;
	v_y integer;

begin
	select into etage,v_x,v_y pos_etage,pos_x,pos_y from positions where pos_cod = pos_actuelle;
	distance_init := distance(pos_actuelle,pos_dest);
	nouvelle_pos := -1;
	for r_pos in select pos_cod from positions
		where pos_x >= (v_x - 1) and pos_x <= (v_x + 1)
		and pos_y >= (v_y - 1) and pos_y <= (v_y + 1)
		and pos_etage = etage
		and not exists (select 1 from murs where mur_pos_cod = pos_cod)
		/*and not exists
			(select 1 from lieu,lieu_position
			where lpos_pos_cod = pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_refuge = 'O')*/
			loop
		distance_temp := distance(r_pos.pos_cod,pos_dest);
		if distance_temp < distance_init then
			nouvelle_pos := r_pos.pos_cod;
			distance_init := distance_temp;
		end if;
	end loop;
	return  nouvelle_pos;
end;			$function$

