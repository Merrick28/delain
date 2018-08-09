CREATE OR REPLACE FUNCTION public.etages_adjacents(integer)
 RETURNS SETOF integer
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* function etages_adjacents                                  */
/* Trouve les étages adjacents à celui fourni en paramètre    */
/* parametres :                                               */
/*  $1 = etage_num                                            */
/* Sortie :                                                   */
/*  code_retour = table contenant la liste des étages         */
/**************************************************************/
/**************************************************************/
/* Création - 09/10/2012 - Reivax                             */
/**************************************************************/
declare
	num_etage alias for $1;
begin
	return query (
		-- au départ de l’étage des personnages du compte ;
		SELECT p2.pos_etage as etage_num FROM etage
		inner join positions p1 on p1.pos_etage = etage_numero
		inner join lieu_position on lpos_pos_cod = p1.pos_cod
		inner join lieu on lieu_cod = lpos_lieu_cod
		inner join positions p2 on p2.pos_cod = lieu_dest
		where etage_numero = num_etage
		
		UNION
		-- et à l’arrivée.
		SELECT p2.pos_etage as etage_num FROM etage
		inner join positions p1 on p1.pos_etage = etage_numero
		inner join lieu on lieu_dest = p1.pos_cod
		inner join lieu_position on lpos_lieu_cod = lieu_cod
		inner join positions p2 on p2.pos_cod = lpos_pos_cod
		where etage_numero = num_etage

		UNION
		-- et pour finir, l’étage initial 
		--(rajouté pour uniformiser les résultats,
		--vu que la requête donne l’étage initial
		--pour peu qu’il y ait un passage magique
		--d’ouvert sur l’étage...
		SELECT num_etage as etage_num
	);
end;	$function$

