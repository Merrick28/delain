CREATE OR REPLACE FUNCTION public.trajectoire_vue2(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/**************************************************/
/* fonction trajectoire : calcule une trajectoire */
/*  pour une arme à distance                      */
/* on passe en paramètres :                       */
/*  $1 = pos_cod 1                                */
/*  $2 = pos_cod 2                                */
/* on a en retour :                               */
/*  0 = pas visible                               */
/*  1 = visible                                   */
/*  2 = anomalie                                  */
/**************************************************/
/* créé le 03/05/2005                             */
/**************************************************/
declare
	code_retour integer;
	pos1 alias for $1;
	pos2 alias for $2;
	d_x integer;
	d_y integer;
	signe_x integer;
	signe_y integer;
	x1 integer;
	x2 integer;
	y1 integer;
	y2 integer;
	etage1 integer;
	etage2 integer;
	cpt integer;
	dist_init integer;
	proc_x integer;
	proc_y integer;
	angle numeric;
	proc_pos integer;
	temp integer;
	j integer;
	anc_x integer;
	anc_y integer;
	liste_x integer[];
	liste_y integer[];
	liste_pos integer[];
	dim_tab integer;
	coef_dir numeric;
	x_actuel integer;
	y_actuel integer;
	pos_actuel integer;
	b integer;
begin
	temp := 0;
	if pos1 = pos2 then
		return 1;
	end if;
	select into etage1,x1,y1 pos_etage,pos_x,pos_y from positions
		where pos_cod = pos1;
	select into etage2,x2,y2 pos_etage,pos_x,pos_y from positions
		where pos_cod = pos2;	
	if etage1 != etage2 then
		return 2;
	end if;
	d_x := x2 - x1;
	d_y := y2 - y1;
	if d_x > 0 then
		signe_x := 1;
	else
		signe_x := -1;
	end if;
	if d_y > 0 then
		signe_y := 1;
	else
		signe_y := -1;
	end if;
	d_x := abs(d_x);
	d_y := abs(d_y);
	-- calcul du coefficient directeur de la droite
	if d_x != 0 then
		x_actuel := x1;
		coef_dir := (y2-y1)/(x2-x1)::numeric;
		b := round(y1 - (coef_dir*x1));
		while(x_actuel != x2) loop
			y_actuel := round((coef_dir*x_actuel)+b);
			select into pos_actuel
				pos_cod
				from positions
				where pos_x = x_actuel
				and pos_y = y_actuel
				and pos_etage = etage1;
			select into proc_pos
				mur_pos_cod
				from positions,murs
				where pos_x = x_actuel
				and pos_y = y_actuel
				and pos_etage = etage1
				and mur_pos_cod = pos_cod
				and mur_tangible = 'O';
			if found then
				return 0;
			end if;
			x_actuel := x_actuel + signe_x;
		end loop;
	end if;
	

	return 1;	
end;
$function$

