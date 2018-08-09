CREATE OR REPLACE FUNCTION public.delta_y(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
 IMMUTABLE
AS $function$/***************************************************/
/* fonction delta_y : donne l ecart en valeur      */
/*  absolue des Y entre deux positions             */
/* on passe en paramètres :                        */
/*  $1 = pos_cod 1                                 */
/*  $2 = pos_cod 2                                 */
/* on a en retour un entier                        */
/***************************************************/
declare 
	code_retour integer;
	pos1 alias for $1;
	pos2 alias for $2;
	y1 integer;
	y2 integer;
	
begin
	select into y1 pos_y from positions
		where pos_cod = pos1;
	select into y2 pos_y from positions
		where pos_cod = pos2;
	code_retour := @(y1 - y2);
	return code_retour;
end;$function$

