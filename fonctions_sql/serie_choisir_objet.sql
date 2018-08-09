CREATE OR REPLACE FUNCTION public.serie_choisir_objet(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function serie_choisir_objet : retourne un type d' objet      */
/*    aleatoire dans la serie passee en parametre                */
/* On passe en paramètres                                        */
/*    $1 = id de la serie                                        */
/* Le code sortie est un entier (gobj_cod)                       */
/*****************************************************************/
/* Créé le 07/07/2005                                            */
/* Liste des modifications :                                     */
/*                                                               */
/*****************************************************************/
declare
	code_retour integer;
	serie_id alias for $1;	
	chance integer;	
	cumul_chances integer;
	total_chances integer;
	chance_sans_obj integer;
	des integer;
	ligne record;
begin
	select into total_chances sum(seequo_proba) 
		from serie_equipement_objet 
		where seequo_seequ_cod = serie_id;
	select into chance_sans_obj seequ_proba_sans_objet
		from serie_equipement
		where seequ_cod = serie_id;
	total_chances := total_chances + chance_sans_obj;
	des := lancer_des(1,total_chances);
	cumul_chances := 0;
	for ligne in select seequo_proba,seequo_gobj_cod from serie_equipement_objet where seequo_seequ_cod = serie_id loop
		cumul_chances := cumul_chances + ligne.seequo_proba;
		if des < cumul_chances then
			return ligne.seequo_gobj_cod;
		end if;
	end loop;
	return null;	
end;
$function$

