CREATE OR REPLACE FUNCTION public.trajectoire_perso2(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/***************************************************/
/* fonction trajectoire_perso : calcule une        */
/* trajectoire et récupère les num de persos dessus*/
/*  pour une arme à distance                       */
/* on passe en paramètres :                        */
/*  $1 = pos_cod 1                                 */
/*  $2 = pos_cod 2                                 */
/* on a en retour :                                */
/* liste des perso_co                              */
/* pour appeler la fonction :                      */ 
/*select * from trajectoire_perso as (personnage int,*/
/*   v_pos int, type_perso int)                    */
/* On peut évidemment sélectionner les champs de   */
/* sortie                                          */
/***************************************************/
/* donné le 11/05/2005                             */
/* © mnibjb@free.fr - utilisation libre pour Delain*/ 
/***************************************************/

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
xx integer;
yy integer;
A integer;
v_Ax integer;
v_Ay integer;
v_Axy integer;
proc_pos integer;
	v_req text;
	curs2 refcursor;
	v_pos integer;
	personnage integer;
	type_perso integer;
	x_calcul integer;
	y_calcul integer;
	compt_perso integer;
	v_compt_perso integer;
	v_refuge integer;


ligne record;

begin
	compt_perso := 0;
	xx := 0;
	yy := 0;
	if pos1 = pos2 then
		return 0;
	end if;
	select into etage1,x1,y1 pos_etage,pos_x,pos_y from positions
		where pos_cod = pos1;
if not found then
return 0;
end if;
	select into etage2,x2,y2 pos_etage,pos_x,pos_y from positions
		where pos_cod = pos2;
if not found then
return 0;
end if;
	if etage1 != etage2 then
		return 0;
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

	A = d_x - d_y ;
	v_Ax := -2*d_y ;
	v_Ay := 2*d_x ;
	v_Axy := 2*A ;


loop
   if A>0 then
      xx := xx + 1 ;
      A=A+v_Ax ;
   elsif A<0 then
      yy := yy + 1;
      A=A+v_Ay ;
   else
      xx := xx + 1 ;
     	yy := yy + 1;
      A=A+v_Axy ;
   end if;

   /* while(xx!=d_x OR yy!=d_y) */
  if (xx=d_x AND yy=d_y) then 
   	return compt_perso;
	end if;
	x_calcul := x1+(xx*signe_x);
	y_calcul := y1+(yy*signe_y);
	select into proc_pos
	      mur_pos_cod
	      from positions,murs
	      where pos_x = x_calcul
	      and pos_y =   y_calcul
	      and pos_etage = etage1
	      and mur_pos_cod = pos_cod
	      and mur_tangible = 'O';
   if found then
      return 0;
   else
		 select count(*) into v_compt_perso 
		 	from positions,perso_position,perso
			where pos_x = x_calcul
			and pos_y =   y_calcul
			and pos_etage = etage1
			and ppos_pos_cod = pos_cod
			and perso_actif = 'O'
			and perso_tangible = 'O'
			and perso_cod = ppos_perso_cod;
		--
		-- on regarde si ce n'est pas un refuge
		--
		select lieu_cod into v_refuge
			from lieu,lieu_position,positions
			where pos_x = x_calcul
			and pos_y =   y_calcul
			and pos_etage = etage1
			and lpos_pos_cod = pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_refuge = 'O';
		if found then
			v_compt_perso := 0;
		end if;
		 compt_perso := compt_perso + v_compt_perso;
		  
		  
		end if;
end loop;
return compt_perso;
end;$function$

