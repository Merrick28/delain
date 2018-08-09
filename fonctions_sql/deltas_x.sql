CREATE OR REPLACE FUNCTION public.deltas_x(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/***************************************************/
/* fonction delta_x : donne l ecart en valeur      */
/*  absolue des X entre deux positions             */
/* on passe en paramètres :                        */
/*  $1 = pos_cod 1                                 */
/*  $2 = pos_cod 2                                 */
/* on a en retour un entier                        */
/***************************************************/
declare
	code_retour integer;
	pos1 alias for $1;
	pos2 alias for $2;
	x1 integer;
	x2 integer;
	
begin 
	select into x1 pos_x from positions
		where pos_cod = pos1;
	select into x2 pos_x from positions
		where pos_cod = pos2;
	code_retour := x1 - x2;
	return code_retour;
end;$function$

