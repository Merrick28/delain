CREATE OR REPLACE FUNCTION public.is_surcharge(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*********************************************/
/* fonction is_surcharge                     */
/*  on passe en params :                     */
/*     $1 = perso_cod cible                  */
/*     $21 = perso_cod attaquant             */
/*  on a en retour un entier :               */
/*     0 = pas surchargé                     */
/*     1 = partiellement                     */
/*     2 = totalement                        */
/*********************************************/
declare
	code_retour integer;
	attaquant alias for $2;
	cible alias for $1;
	taille_cible numeric;
	nb_lock integer;
	
begin
	code_retour := 0;
	select into taille_cible perso_taille
		from perso where perso_cod = cible;
	select into nb_lock
		count(lock_cod)
		from lock_combat
		where lock_cible = cible
		and lock_attaquant != attaquant;
	if nb_lock <= taille_cible then
		code_retour := 0;
		return code_retour;
	end if;
	if nb_lock >= (2*taille_cible) then
		code_retour := 2;
		return code_retour;
	end if;
	if nb_lock > taille_cible then
		code_retour := 1;
		return code_retour;
	end if;
end;
	$function$

