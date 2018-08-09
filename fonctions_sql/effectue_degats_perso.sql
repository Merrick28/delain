CREATE OR REPLACE FUNCTION public.effectue_degats_perso(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*********************************************/
/* effectue_degats_perso                     */
/*  $1 = perso sur lequel on fait les dégats */
/*  $2 = degats de base                      */
/*  $3 = perso effectuant les degs           */
/* Retour : dégats rééls                     */
/*********************************************/
/* créé le 12/06/2007 par Merrick            */
/*********************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_degats alias for $2;
	v_attaquant alias for $3;
	v_type_att integer;
begin
	select into v_type_att
		perso_type_perso
		from perso
		where perso_cod = v_attaquant;
	return effectue_degats(personnage , v_degats , v_type_att);
end;$function$

