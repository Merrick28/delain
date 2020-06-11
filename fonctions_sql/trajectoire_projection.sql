-- FUNCTION: public.trajectoire_projection(integer, integer, integer, integer)

-- DROP FUNCTION public.trajectoire_projection(integer, integer, integer, integer);

CREATE OR REPLACE FUNCTION public.trajectoire_projection(integer, integer, integer, integer)
    RETURNS integer
    LANGUAGE plpgsql
    AS $_$/***************************************************/
/* fonction trajectoire_perso : calcule une        */
/* trajectoire et récupère les num de persos dessus*/
/*  pour une arme à distance                       */
/* on passe en paramètres :                        */
/*  $1 = pos_cod  de la case de départ             */
/*  $2 = pos_cod 1  (pour trajectoire)             */
/*  $3 = pos_cod 2  (pour trajectoire)             */
/*  $4 = distance de la projection                 */
/* on a en retour :                                */
/* le pos_cod de la case d'arrivée  en suivant la  */
/* trajectioire de pos1 vers pos2                  */
/* si le pos_cod retourné est négatif, c'est q'un  */
/* mur à stoppé la projection                      */
/* retourne la position de départ  en cas d'erreur */
/***************************************************/

declare
	code_retour integer;
	pos0 alias for $1;
	pos1 alias for $2;
	pos2 alias for $3;
	distance alias for $4;
	d_x integer;
	d_y integer;
	signe_x integer;
	signe_y integer;
	x0 integer;
	x1 integer;
	x2 integer;
	y0 integer;
	y1 integer;
	y2 integer;
	etage0 integer;
	etage1 integer;
	etage2 integer;
xx integer;
yy integer;
A integer;
v_Ax integer;
v_Ay integer;
v_Axy integer;
proc_pos integer;
	v_pos integer;
	x_calcul integer;
	y_calcul integer;
	compteur integer;

begin
	compteur := 0;
	xx := 0;
	yy := 0;
	if distance = 0 then
		return pos0;  -- toute projection à une distance de 0 ne change pas la position
	end if;
	if pos1 = pos2 then
		return pos0;
	end if;
	select into etage0,x0,y0 pos_etage,pos_x,pos_y from positions
		where pos_cod = pos0;
	if not found then
			return pos0;
	end if;
	select into etage1,x1,y1 pos_etage,pos_x,pos_y from positions
		where pos_cod = pos1;
	if not found then
			return pos0;
	end if;
	select into etage2,x2,y2 pos_etage,pos_x,pos_y from positions
					where pos_cod = pos2;
	if not found then
			return pos0;
	end if;
	if etage0 != etage1 or etage0 != etage2 or etage1 != etage2 then
			return pos0;
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
	x_calcul := x0+(xx*signe_x);
	y_calcul := y0+(yy*signe_y);

	select into proc_pos
	      mur_pos_cod
	      from positions,murs
	      where pos_x = x_calcul
	      and pos_y =   y_calcul
	      and pos_etage = etage1
	      and mur_pos_cod = pos_cod;
	  /* Si on tombe sur un mur, on renvoie la dernière position trouvée, sinon on continue */
   	if found or compteur=distance then
        if compteur = 0 then
            return -pos0;
        elseif compteur = distance then
            return v_pos;
        else
            return -v_pos;
        end if;
   	else
   		select into v_pos
	      pos_cod
	      from positions
	      where pos_x = x_calcul
	      and pos_y =   y_calcul
	      and pos_etage = etage1;
      -- on arrete aussi si on sort de la map
      if not found then
          if compteur = 0 then
              return -pos0;
          else
              return -v_pos;
          end if;
      end if;
      compteur := compteur + 1 ;

		end if;
end loop;
return v_pos;
end;$_$;

ALTER FUNCTION public.trajectoire_projection(integer, integer, integer, integer)
    OWNER TO delain;

COMMENT ON FUNCTION public.trajectoire_projection(integer, integer, integer, integer)
    IS 'Permet de récupérer la position suivant une trajectoire donnée par les 2 positions et une distance, on s''arrete avant de tomber sur un mur';
