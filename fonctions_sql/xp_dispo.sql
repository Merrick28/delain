CREATE OR REPLACE FUNCTION public.xp_dispo(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function xp_dispo : donne le nombre d XP qu un joueur peut    */
/*   donner                                                      */
/* On passe en paramètres  :                                     */
/*    1 le perso cod                                             */
/* En sortie, on a le nombre d XP                                */
/*****************************************************************/
declare
	resultat integer;
	personnage alias for $1;
	niveau perso.perso_niveau%type;
	niv_actu integer;
	xp_niveau_actu integer;
	xp_actu numeric;
	xp_dispo integer;
begin
	select into niveau,xp_actu perso_niveau,perso_px from perso
		where perso_cod = personnage;
	niv_actu := niveau - 2;
	xp_niveau_actu := 5*((niv_actu*niv_actu)+(3*niv_actu)+2);
	xp_dispo := floor(xp_actu) - xp_niveau_actu;
	if xp_dispo < 0 then
		xp_dispo := 0;
	end if;
	return xp_dispo;	
end;
$function$

