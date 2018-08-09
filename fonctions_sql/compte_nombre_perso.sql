CREATE OR REPLACE FUNCTION public.compte_nombre_perso(integer)
 RETURNS smallint
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* function  compte_nombre_perso                              */
/* Donne le nombre de perso actifs du compte                  */
/*  $1 = compt_cod du compte à tester                         */
/* Sortie :                                                   */
/*  code_retour = nombre de persos                            */
/**************************************************************/
/**************************************************************/
/* Création - 27/09/2012 - Reivax                             */
/**************************************************************/
declare
	num_compte alias for $1;      -- compt_cod
	resultat smallint;             -- le résultat

begin
/*********************************************************/
/*             E X É C U T I O N                         */
/*********************************************************/
	select into resultat count(*)
	from perso_compte
	inner join perso on perso_cod = pcompt_perso_cod
	where pcompt_compt_cod = num_compte
		and perso_actif = 'O';

	return coalesce(resultat, 0);
end;		$function$

