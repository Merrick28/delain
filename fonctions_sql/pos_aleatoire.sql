CREATE OR REPLACE FUNCTION public.pos_aleatoire(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function pos_aleatoire : retourne un pos_cod aléatoire en     */
/*    fonction de l etage passé en paramètre                     */
/* On passe en paramètres                                        */
/*    $1 = etage                                                 */
/* Le code sortie est un entier (pos_cod)                        */
/*****************************************************************/
/* Créé le 02/04/2003                                            */
/* Liste des modifications :                                     */
/*   06/05/2003 : ajout du contrôle pour les murs                */
/*   31/01/2013 : ajout du contrôle pour le zéro                 */
/*****************************************************************/
declare
	code_retour integer;
	v_etage alias for $1;
begin
	select into code_retour
		pos_cod
	from positions
	where pos_etage = v_etage
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
end;
$function$

