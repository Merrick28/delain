CREATE OR REPLACE FUNCTION public.rappel_coterie(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
 STRICT
AS $function$/*****************************************************************/
/* function rappel_coterie :                                     */
/*  Ramène un personnage au dernier de ses compagnons ayant      */
/*     essayé de le rappeler                                     */
/* On passe en paramètres                                        */
/*   $1 = perso appelé                                           */
/*   $2 = perso à rejoindre                                      */
/* Le code sortie est la nouvelle position après la mort, ou -1  */
/*****************************************************************/
/* Créé le 21/04/2012                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour integer;               -- nouvelle position
-------------------------------------------------------------
-- variables concernant le lanceur  
-------------------------------------------------------------
	v_perso_cible 	alias for $1;	-- perso_cod du décédé
	v_perso_source 	alias for $2;	-- perso_cod du perso appeleur
	v_pos_cible		integer;		-- Position du perso cible
	v_pos_source	integer;		-- Position du perso source
	v_pos_source_x	integer;		-- Position X du perso source
	v_pos_source_y	integer;		-- Position Y du perso source
	v_distcarr_xy	integer;		-- distance horizontale (au carré)
	v_distcarr_n	integer;		-- distance verticale (au carré)
	v_distance		integer;		-- distance d’arrivée
	v_etage_source	integer;		-- etage de la case de destination
	etage_gm		integer;		-- Valeur de l’étage du garde manger

begin
	-- Initialisation
	etage_gm	:= 90;
    
	-- On récupère la position du perso source.
	select into v_pos_source, v_pos_source_x, v_pos_source_y, v_etage_source
			ppos_pos_cod, pos_x, pos_y, pos_etage
		from perso_position
		inner join positions on pos_cod = ppos_pos_cod
		where ppos_perso_cod = v_perso_source;

	-- Si le perso appelant est au garde-manger, il est inaccessible
	if v_etage_source = etage_gm then
		return -1;
	end if;

	-- On récupère la position du perso cible.
	select into v_pos_cible ppos_pos_cod from perso_position
		where ppos_perso_cod = v_perso_cible;

	-- On évalue leur distance (un étage compte pour 10).
	select into v_distcarr_xy, v_distcarr_n
			(p1.pos_x - p2.pos_x)*(p1.pos_x - p2.pos_x) + (p1.pos_y - p2.pos_y)*(p1.pos_y - p2.pos_y),
			(e1.etage_reference - e2.etage_reference) * (e1.etage_reference - e2.etage_reference) * 100
		from positions p1
		inner join etage e1 on e1.etage_numero = p1.pos_etage
		inner join positions p2 on p2.pos_cod = v_pos_cible
		inner join etage e2 on e2.etage_numero = p2.pos_etage
		where p1.pos_cod = v_pos_source;

	-- Choix de la case de téléportation, au hasard dans un rayon de v_distance / 5, mais toujours entre 2 et 10 cases.
	v_distance := min(max(cast(sqrt(v_distcarr_xy + v_distcarr_n) as integer) / 5, 2), 10);

	select into code_retour pos_cod from positions
		where pos_etage = v_etage_source
			and abs(pos_x - v_pos_source_x) <= v_distance and abs(pos_y - v_pos_source_y) <= v_distance
			and not exists (select 1 from murs where mur_pos_cod = pos_cod)
			and (pos_etage != 0 OR abs(pos_x) < 20 AND abs(pos_y) < 20)
		order by random()
		limit 1;
	if found then
		-- Trouvé une position
		return code_retour;
	else 
		-- Pas de position libre trouvée.
		return -1;
	end if;
end;
$function$

