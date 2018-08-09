CREATE OR REPLACE FUNCTION public.possede_4e_perso(integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* function  possede_4e_perso                                 */
/* Indique si le compte donné possède un quatrième perso      */
/* parametres :                                               */
/*  $1 = compt_cod du compte à tester                         */
/* Sortie :                                                   */
/*  code_retour = True ou False                               */
/**************************************************************/
/**************************************************************/
/* Création - 27/09/2012 - Reivax                             */
/**************************************************************/
declare
	num_compte alias for $1;      -- compt_cod
	resultat boolean;             -- le résultat

begin
/*********************************************************/
/*             E X É C U T I O N                         */
/*********************************************************/

	-- On recherche si le compte n’aurait pas un 4e perso de type aventurier
	select into resultat (count(*) > 0)
	from perso_compte
	inner join perso on perso_cod = pcompt_perso_cod
	where pcompt_compt_cod = num_compte
		and perso_actif = 'O'
		and perso_pnj = 2;

	-- On recherche si le compte n’aurait pas un 4e perso de type monstre
	if not resultat then
		select into resultat (count(*) > 0)
		from perso_compte
		inner join perso on perso_cod = pcompt_perso_cod
		where pcompt_compt_cod = num_compte
			and perso_actif = 'O'
			and perso_type_perso = 2;
	end if;
	return resultat;	
end;		$function$

