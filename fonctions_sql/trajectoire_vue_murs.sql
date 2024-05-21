
--
-- Name: trajectoire_vue_murs(integer, integer, boolean); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.trajectoire_vue_murs(integer, integer, boolean default true) RETURNS integer
    LANGUAGE plpgsql STABLE
    AS $_$/***************************************************/
/* fonction trajectoire : calcule une trajectoire  */
/*  pour une arme à distance                       */
/* on passe en paramètres :                        */
/*  $1 = pos_cod 1                                 */
/*  $2 = pos_cod 2                                 */
/*  $3 = voir au dela des murs intangibles         */
/* on a en retour :                                */
/*  0 = pas visible                                */
/*  1 = visible                                    */
/*  2 = anomalie                                   */
/***************************************************/
/* donné le 11/05/2005                             */
/* © mnibjb@free.fr - utilisation libre pour Delain*/
/***************************************************/
declare
	code_retour integer;
	pos1 alias for $1;
	pos2 alias for $2;
	v_intangible alias for $3;
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

begin
	xx := 0;
	yy := 0;
	if pos1 = pos2 then
		return 1;
	end if;
	select into etage1,x1,y1 pos_etage,pos_x,pos_y from positions
		where pos_cod = pos1;
if not found then
return 2;
end if;
	select into etage2,x2,y2 pos_etage,pos_x,pos_y from positions
		where pos_cod = pos2;
if not found then
return 2;
end if;
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
   	return 1;
	end if;
	 -- Marlyza 21/05/2024 : laisser la possibilité de traverser les murs intangibles (pour les sorts/attaques)
   select into proc_pos
      mur_pos_cod
      from positions,murs
      where pos_x = x1+(xx*signe_x)
      and pos_y =   y1+(yy*signe_y)
      and pos_etage = etage1
      and mur_pos_cod = pos_cod
      and ( mur_tangible = 'O' or (mur_tangible = 'N' and v_intangible is false) ) ;
   if found then
      return 0;
   end if;
end loop;

end;
$_$;


ALTER FUNCTION public.trajectoire_vue_murs(integer, integer, boolean) OWNER TO delain;
