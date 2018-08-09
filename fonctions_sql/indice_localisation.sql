CREATE OR REPLACE FUNCTION public.indice_localisation(integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$/***********************************************************/
/* function indice_localisation                                  */
/* Donne une information textuelle sur la position géographique  */
/* de la case donnée au sein de son étage                        */
/* L’étage est quadrillé en 9 zones                              */
/*  ----------                                                   */
/*  |NO| N|NE| Nord-ouest, Nord, Nord-est                        */
/*  |--|--|--|                                                   */
/*  | O| C| E| Ouest, centre, est                                */
/*  |--|--|--|                                                   */
/*  |SO| S|SE| sud-ouest, sud, sud-est                           */
/*  ----------                                                   */
/* parametres :                                                  */
/*  $1 = pos_cod                                                 */
/* Sortie :                                                      */
/*  code_retour = texte informatif (est, nord-est, centre, etc.) */
/*****************************************************************/
/*****************************************************************/
/* Création - 09/10/2012 - Reivax                                */
/*****************************************************************/
declare
	code_position alias for $1;  -- code de la position
	code_etage integer;          -- code de l’étage
	posX integer;                -- valeur X de la position fournie en paramètre
	posY integer;                -- valeur Y de la position fournie en paramètre
	minX integer;                -- valeur X minimale de l’étage
	maxX integer;                -- valeur X maximale de l’étage
	minY integer;                -- valeur Y minimale de l’étage
	maxY integer;                -- valeur Y maximale de l’étage
	centreX integer;             -- valeur X du centre de l’étage
	centreY integer;             -- valeur Y du centre de l’étage
	distanceX integer;           -- distance sur les X de la case donnée au centre de l’étage
	distanceY integer;           -- distance sur les Y de la case donnée au centre de l’étage
	rayonX integer;              -- distance sur les X du centre de l’étage vers le bord de la zone centrale
	rayonY integer;              -- distance sur les Y du centre de l’étage vers le bord de la zone centrale
	resultat character varying;

begin
	select into code_etage, posX, posY
		pos_etage, pos_x, pos_y
	from positions
	where pos_cod = code_position;
	
	select into minX, maxX, minY, maxY
		min(pos_x), max(pos_x), min(pos_y), max(pos_y)
	from positions
	where pos_etage = code_etage;

	centreX := ((maxX + minX) / 2)::integer;
	centreY := ((maxY + minY) / 2)::integer;

	distanceX := abs(posX - centreX);
	distanceY := abs(posY - centreY);

	rayonX := (abs(maxX - centreX) / 3)::integer;
	rayonY := (abs(maxY - centreY) / 3)::integer;

	select into resultat
		case
			when distanceX <= rayonX AND distanceY <= rayonY then 'le centre'
			when distanceX <= rayonX AND distanceY > rayonY AND posY > centreY then 'le nord'
			when distanceX <= rayonX AND distanceY > rayonY AND posY < centreY then 'le sud'
			when distanceX > rayonX AND distanceY <= rayonY AND posX > centreX then 'l’est'
			when distanceX > rayonX AND distanceY <= rayonY AND posX < centreX then 'l’ouest'
			when distanceX > rayonX AND distanceY > rayonY AND posX < centreX AND posY > centreY then 'le nord-ouest'
			when distanceX > rayonX AND distanceY > rayonY AND posX < centreX AND posY < centreY then 'le sud-ouest'
			when distanceX > rayonX AND distanceY > rayonY AND posX > centreX AND posY > centreY then 'le nord-est'
			when distanceX > rayonX AND distanceY > rayonY AND posX > centreX AND posY < centreY then 'le sud-est'
			else 'une position indéterminée...'
		end;
	
	return resultat;
end;		$function$

