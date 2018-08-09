CREATE OR REPLACE FUNCTION public.est_dans_arene(integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/***********************************************/
/*  est_dans_arene : fonction déterminant si   */
/*  un perso est dans une arene de combat PVP  */
/*  $1 = perso_cod                             */
/*  return true si le perso est dans une arene */
/*  false sinon                                */
/*  Morgenese  le  17/09/2010                  */
/***********************************************/
declare 
        v_etage_arene char;
	v_perso alias for $1;
	
begin
	select into v_etage_arene
		etage_arene
		from etage, perso_position, positions
		where ppos_perso_cod = v_perso
		and ppos_pos_cod = pos_cod
		and pos_etage = etage_numero;

	if v_etage_arene = 'O' then
		return true;
	end if;
	
	return false;

end;$function$

