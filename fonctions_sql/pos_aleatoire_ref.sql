CREATE OR REPLACE FUNCTION public.pos_aleatoire_ref(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function pos_aleatoire_ref : retourne un pos_cod aléatoire en */
/*    fonction de l etage passé en paramètre, sur les étages     */
/*    ayant la même référence.                                   */
/* On passe en paramètres                                        */
/*    $1 = etage                                                 */
/* Le code sortie est un entier (pos_cod)                        */
/*****************************************************************/
/* Créé le 02/04/2003                                            */
/* Liste des modifications :                                     */
/*   06/05/2003 : ajout du contrôle pour les murs                */
/*   06/05/2003 : ajout du contrôle pour le zéro                 */
/*****************************************************************/
declare
	code_retour integer;
	v_etage alias for $1;
begin
	code_retour := 62514; -- Au proving ground par défaut
	
	select into code_retour
		pos_cod
	from positions, etage
	where etage_reference = v_etage
		and etage_retour_rune_monstre != 100
		and pos_etage = etage_numero
		and (pos_etage <> 0
			or pos_x between -20 and 20
				and pos_y between -20 and 20)
		and not exists
			(select 1 from murs
			where mur_pos_cod = pos_cod)
		and not exists
			(select 1 from lieu_position
			where lpos_pos_cod = pos_cod)
	order by random()
	limit 1;

	return code_retour;
end;$function$

