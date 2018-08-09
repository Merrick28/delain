CREATE OR REPLACE FUNCTION public.variation_aleatoire(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function variation_aleatoire : Fait varier aléatoirement une valeur*/
/* On passe en paramètres  :                                     */
/*    1 valeur d'origine                                         */
/*    2 le pourcentage d'aléatoire                               */
/* En sortie, on a la valeur modifiée                            */
/*****************************************************************/
declare
	resultat integer;
	valeur_depart alias for $1;
	pourcentage_aleatoire alias for $2;
	variation integer;
begin
	variation := 100+lancer_des(1,2*pourcentage_aleatoire)-pourcentage_aleatoire;
	if valeur_depart <1 then
		-- CAS particulier ou valeurdepart = 0 ,+0 +1 +2 en fonction 
		resultat := trunc(10 * variation/100)-10;
		resultat := max(resultat,0);
		resultat := min(resultat,2);
	else
		resultat := trunc(valeur_depart * variation/100);
	end if;		
	return resultat;
end;$function$

