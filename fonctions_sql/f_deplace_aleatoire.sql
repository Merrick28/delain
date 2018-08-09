CREATE OR REPLACE FUNCTION public.f_deplace_aleatoire(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/***********************************************************/
/* procédure deplace_aleatoire : deplace un monstre sur    */
/*   une case à coté de lui                                */
/* on passe en params :                                    */
/*   $1 = perso_cod du monstre                             */
/*   $2 = pos_actuelle du monstre                          */
/***********************************************************/
declare
	code_retour integer;
	v_monstre alias for $1;
	pos_monstre alias for $2;
	compt_pos integer;
	v_etage integer;
	nouvelle_pos integer;
	deplace text;
	sens integer;
	v_x integer;
	v_y integer;
	x_min integer;
	x_max integer;
	y_min integer;
	y_max integer;
	
begin
	select into v_etage,v_x,v_y pos_etage,pos_x,pos_y from positions
		where pos_cod = pos_monstre;
	x_min = v_x - 1;
	x_max = v_x + 1;
	y_min = v_y - 1;
	y_max = v_y + 1;
	
	select into nouvelle_pos pos_cod
		from positions
		where pos_etage = v_etage
		and pos_x between x_min and x_max
		and pos_y between y_min and y_max
		and not exists (select 1 from murs where mur_pos_cod = pos_cod)
		and not exists (select 1 from lieu,lieu_position where lpos_pos_cod = pos_cod and lpos_lieu_cod = lieu_cod and lieu_refuge = 'O')
		order by random()
		limit 1;
		
/*	select into compt_pos count(pos_cod)
		from positions
		where pos_etage = v_etage
		and pos_x between x_min and x_max
		and pos_y between y_min and y_max
		and not exists (select 1 from murs where mur_pos_cod = pos_cod)
		and not exists (select 1 from lieu,lieu_position where lpos_pos_cod = pos_cod and lpos_lieu_cod = lieu_cod and lieu_refuge = 'O');

	if compt_pos = 0 then
		code_retour := 0;
		
	end if;
	compt_pos := lancer_des(1,compt_pos);
	compt_pos := compt_pos - 1;
	sens := lancer_des(1,10);
	if sens <=5 then
		select into nouvelle_pos pos_cod
			from positions
			where pos_etage = v_etage
			and pos_x between x_min and x_max
		and pos_y between y_min and y_max
			and not exists (select 1 from murs where mur_pos_cod = pos_cod)
			and not exists (select 1 from lieu,lieu_position where lpos_pos_cod = pos_cod and lpos_lieu_cod = lieu_cod and lieu_refuge = 'O')
			order by pos_cod asc
			offset compt_pos
			limit 1;
	else
		select into nouvelle_pos pos_cod
			from positions
			where pos_etage = v_etage
			and pos_x between x_min and x_max
		and pos_y between y_min and y_max
			and not exists (select 1 from murs where mur_pos_cod = pos_cod)
			and not exists (select 1 from lieu,lieu_position where lpos_pos_cod = pos_cod and lpos_lieu_cod = lieu_cod and lieu_refuge = 'O')
			order by pos_cod desc
			offset compt_pos
			limit 1;
	end if;*/
	deplace = deplace_code(v_monstre,nouvelle_pos);
	code_retour := nouvelle_pos;
	return 0;
end;$function$

