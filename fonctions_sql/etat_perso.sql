CREATE OR REPLACE FUNCTION public.etat_perso(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************/
/* etat perso                                */
/* 29/06/2012 - Reivax - petite optimisation */
/*           mise en variable du ratio de PV */
/*           pour une fonction très appelée  */
/*********************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_pv integer;
	v_pv_max numeric;
	v_ratio numeric;
begin
	select into v_pv, v_pv_max
		perso_pv, perso_pv_max
	from perso where perso_cod = personnage;
	
	v_ratio := v_pv / v_pv_max;
	
	if (v_ratio = 1) then
		code_retour := 'indemne';
	elsif (v_ratio >= 0.75) then
		code_retour := 'égratigné';
	elsif (v_ratio >= 0.5) then
		code_retour := 'touché';
	elsif (v_ratio >= 0.25) then
		code_retour := 'blessé';
	elsif (v_ratio >= 0.15) then
		code_retour := 'gravement touché';
	else
		code_retour := 'presque mort';
	end if;

	return code_retour;
end;$function$

