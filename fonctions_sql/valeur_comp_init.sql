CREATE OR REPLACE FUNCTION public.valeur_comp_init(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************/
/* valeur_comp_init                      */
/*  $1 = perso_cod                       */
/*  $2 = comp_cod                        */
/*  retour = valeur de la comp initiale  */
/*****************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_comp alias for $2;
	v_for integer;
	v_dex integer;
	v_con integer;
	v_int integer;
	v_typc integer;
	v_typc_for integer;
	v_typc_int integer;
	v_typc_con integer;
	v_typc_dex integer;
begin
	select into v_for,v_dex,v_con,v_int
		perso_for,perso_dex,perso_con,perso_int
		from perso where perso_cod = personnage;
	select into v_typc_for,v_typc_int,v_typc_con,v_typc_dex
		typc_mod_for,typc_mod_int,typc_mod_con,typc_mod_dex
		from competences,type_competences
		where comp_cod = v_comp
		and comp_typc_cod = typc_cod;
	code_retour := 25 + (v_typc_for * (v_for - 10)) + (v_typc_int * (v_int - 10)) + (v_typc_con * ( v_con - 10)) + (v_typc_dex * (v_dex - 10));
	return code_retour;
end;
	$function$

