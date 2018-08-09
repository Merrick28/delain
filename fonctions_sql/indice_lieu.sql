CREATE OR REPLACE FUNCTION public.indice_lieu(integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* function indice_lieu                                       */
/* Donne une phrase d’indice sur la localisation d’un lieu    */
/* proche de la position donnée en paramètre (étage adjacent) */
/* parametres :                                               */
/*  $1 = pos_cod                                              */
/* Sortie :                                                   */
/*  code_retour = table contenant la liste des étages         */
/**************************************************************/
/**************************************************************/
/* Création - 09/10/2012 - Reivax                             */
/**************************************************************/
declare
	code_position alias for $1;
	code_etage integer;
	position_du_lieu integer;
	resultat character varying;

begin
	select into code_etage pos_etage
	from positions
	where pos_cod = code_position;

	select into resultat, position_du_lieu
		'Vous trouverez ' ||
			case when tlieu_cod in (1, 4) then 'une ' else 'un ' end ||
			lower(tlieu_libelle) ||
			' du nom de « ' ||
			lieu_nom ||
			' » à l’étage nommé « ' ||
			etage_libelle ||
			' »',
		lpos_pos_cod
	from lieu
	inner join lieu_type on tlieu_cod = lieu_tlieu_cod
	inner join lieu_position on lpos_lieu_cod = lieu_cod
	inner join positions on pos_cod = lpos_pos_cod
	inner join etage on etage_numero = pos_etage
	inner join etages_adjacents(code_etage) as ea ON ea = etage_numero
	where tlieu_cod in (1, 2, 4, 6, 9, 11, 13, 14, 17, 21, 22, 33)
	order by random()
	limit 1;

	select into resultat
		resultat || ', plutôt vers ' || indice_localisation(position_du_lieu) || '.';

	return resultat;
end;		$function$

