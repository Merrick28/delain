CREATE OR REPLACE FUNCTION public.test_traj(integer, integer)
 RETURNS text
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
	code_retour text;
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
	angle = atan(d_y/d_x);
	liste_x := '{'||x1||'}';
	liste_y := '{'||y1||'}';
	liste_pos := '{'||pos1||'}';
	code_retour := to_char(pos1,'999999')||';';
	if d_x >= d_y then
		-- deplacement horizontal
		for cpt in 1..d_x loop
			proc_x := x1 + (cpt*signe_x);
			liste_x := array_append(liste_x,proc_x);
			if d_y != 0 then
				angle := mod(angle,(3.14159/4::numeric)) + atan((d_y - temp)/(d_x - cpt + 1)::numeric);
				if angle >= 3.14159/4::numeric then
					temp := temp + 1;
  				end if;
				proc_y := y1 + (temp * signe_y);
				liste_y := array_append(liste_y,proc_y);
			end if;
		end loop;
		If temp < d_y Then
  			j := 2;
  			While liste_y[j] != liste_y[j-1] loop
   			j := j + 1;
  			end loop;
  			For cpt in  j..d_y loop
   			liste_y[cpt] := liste_y[cpt] + signe_y;
 			end loop;
 		end if;
 	else
		-- deplacement vertical
		for cpt in 1..d_y loop
			proc_y := y1 + (cpt*signe_y);
			liste_y := array_append(liste_y,proc_y);
			if d_x != 0 then
				angle := mod(angle,(3.14159/4::numeric)) + atan((d_x - temp)/(d_y - cpt + 1)::numeric);
				if angle >= (3.14159/4::numeric) then
					temp := temp + 1;
  				end if;
				proc_x := x1 + (temp * signe_x);
				liste_x := array_append(liste_x,proc_x);
			end if;
		end loop;
		if temp < d_x then
  			j := 2;
  			While liste_x[j] !=  liste_x[j-1] loop
   			j := j + 1;
  			end loop;
  			For cpt in  j..d_x loop
   			liste_x[cpt] := liste_x[cpt] + signe_x;
 			end loop;
 		end if;
	end if;
	dim_tab := array_upper(liste_x,1);
	for cpt in 2..dim_tab loop
		select into proc_pos
			pos_cod
			from positions
			where pos_x = liste_x[cpt]
			and pos_y = liste_y[cpt]
			and pos_etage = etage1;
		liste_pos := array_append(liste_pos,proc_pos);
		
		
	end loop;
	
	
	return liste_pos;	
end;
$function$

