CREATE OR REPLACE FUNCTION public.limite_niveau_actuel(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function limite_niveau_actuel : calcule le nombre d XP du     */
/*   niveau actuel                                               */
/* On passe en paramètres  :                                     */
/*    1 le perso cod                                             */
/* En sortie, on a le nombre d XP                                */
/*****************************************************************/
declare
	resultat integer;
	personnage alias for $1;
	niveau perso.perso_niveau%type;
	n_1 integer;
begin
	select into niveau perso_niveau from perso
		where perso_cod = personnage;
	n_1 := niveau - 2;
	resultat := 5*((n_1*n_1)+(3*n_1)+2);
	return resultat;
end;
$function$

