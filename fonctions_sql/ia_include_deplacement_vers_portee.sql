CREATE OR REPLACE FUNCTION public.ia_include_deplacement_vers_portee(integer, integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ia_include_deplacement_vers_portee :                 */
/*      Procédure utilisée par les différentes IA pour gérer les */
/*      déplacements vers une case donnée, tout en restant à une */
/*      certaine distance                                        */
/* On passe en paramètres                                        */
/*    $1 = perso concerné                                        */
/*    $2 = sa position                                           */
/*    $3 = la position ciblée                                    */
/*    $4 = la distance à conserver                               */
/*****************************************************************/
/* Créé le 15/11/2012                                            */
/*****************************************************************/
declare
	monstre_cod alias for $1;  -- le perso_cod du monstre
	pos_actuelle alias for $2; -- le pos_cod actuel du monstre
	pos_cible alias for $3;    -- la position ciblée
	portee alias for $4;       -- la distance à laquelle le monstre veut se placer
	v_x integer;               -- la position X actuelle
	v_y integer;               -- la position Y actuelle
	v_etage integer;           -- l’étage actuel
	v_pa integer;              -- les PA du sujet
	code_retour integer;       -- le code retour (le nouveau pos_cod)
	compt_loop integer;        -- un compteur de boucle, permettant d’arrêter la boucle en cas de problème

	temp_txt text;             -- fourre-tout
	temp_pos integer;          -- position intermédiaire
begin

	compt_loop := 0;
	temp_pos := pos_actuelle;
	temp_txt := '';

	-- On récupère les coordonnées actuelles
	select into v_x, v_y, v_etage
		pos_x, pos_y, pos_etage
	from positions
	where pos_cod = pos_actuelle;

	-- On récupère les PA actuels
	select into v_pa
		perso_pa
	from perso
	where perso_cod = monstre_cod;

	while (v_pa > 0) loop
		compt_loop := compt_loop + 1;
		exit when compt_loop >= 6 OR temp_txt LIKE '1#Err%' OR distance(temp_pos, pos_cible) <= portee;
		-- on récupère la case vers laquelle on se déplace
		-- on va sur cette nouvelle case
		-- on récupère les nouvelles infos
		temp_pos := dep_vers_cible(temp_pos, pos_cible);
		temp_txt := deplace_code(monstre_cod, temp_pos);

		select into temp_pos, v_x, v_y ppos_pos_cod, pos_x, pos_y
		from perso_position
		inner join positions on pos_cod = ppos_pos_cod
		where ppos_perso_cod = monstre_cod;

		select into v_pa perso_pa from perso
		where perso_cod = monstre_cod;
	end loop;

	code_retour := temp_pos;
	return code_retour;
end;$function$

