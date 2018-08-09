CREATE OR REPLACE FUNCTION public.modif_change_cible(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* fonction modif_change_cible : donne le modificateur    */
/*   pour un projectile de changer de cible               */
/* on passe en paramètres :                               */
/*   $1 = perso_cod                                       */
/* on a en retour un entier                               */
/**********************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	dext integer;
begin
	select into dext perso_dex from perso
		where perso_cod = personnage;
	code_retour := (dext - 11)*2;
	return code_retour;
end;	
$function$

