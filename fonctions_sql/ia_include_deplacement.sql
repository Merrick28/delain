CREATE OR REPLACE FUNCTION public.ia_include_deplacement(ia_donnees, integer, integer, integer, integer)
 RETURNS ia_donnees
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ia_include_deplacement :                             */
/*      Procédure utilisée par les différentes IA pour gérer les */
/*      déplacements                                             */
/* On passe en paramètres                                        */
/*    $1 = les données d’IA (de type ia_donnees)                 */
/*    $2 = le type de déplacement voulu :                        */
/*          1 = Déplacement vers une case ciblée                 */
/*          2 = Déplacement selon un cap aléatoire               */
/*          3 = Déplacement totalement aléatoire (brownien)      */
/*    $3 = le nombre de déplacements maximaux                    */
/*    $4 = la position ciblée                                    */
/*    $5 = la distance de la cible à laquelle s’arrêter          */
/*****************************************************************/
/* Créé le 07/01/2013                                            */
/*****************************************************************/
declare
	donnees alias for $1;           -- l’ensemble des données d’IA à transmettre entre fonctions
	v_type_dep alias for $2;      -- le type de déplacements
	v_depl_max alias for $3;      -- le nombre max de déplacements à effectuer
	v_cible alias for $4;         -- la position ciblée
	v_distance alias for $5;      -- le distance de la cible à laquelle s’arrêter

	resultat ia_donnees;          -- le résultat de la fonction

	compt_loop integer;           -- un compteur de boucle, permettant d’arrêter la boucle en cas de problème
	temp_txt text;                -- fourre-tout
	temp_pos integer;             -- position intermédiaire
	pos_aleatoire integer;        -- position aléatoire
begin
	resultat := donnees;

	compt_loop := 0;
	temp_txt := '';
	temp_pos := resultat.pos_cod;

	-- Premier cas : déplacement vers une case ciblée.
	if v_type_dep = 1 then
		while (resultat.perso_pa > 0)
		loop
			compt_loop := compt_loop + 1;
			exit when compt_loop >= v_depl_max OR temp_txt LIKE '1#Err%' OR distance(resultat.pos_cod, v_cible) <= v_distance;
			-- on récupère la case vers laquelle on se déplace
			-- on va sur cette nouvelle case
			-- on récupère les nouvelles infos
			temp_pos := dep_vers_cible(resultat.pos_cod, v_cible);
			temp_txt := deplace_code(donnees.monstre_cod, temp_pos);

			-- on récupère les nouvelles infos
			select into resultat.perso_pa perso_pa from perso where perso_cod = donnees.monstre_cod;

			select into resultat.pos_x, resultat.pos_y, resultat.pos_cod pos_x, pos_y, pos_cod
			from perso_position
			inner join positions on pos_cod = ppos_pos_cod
			where ppos_perso_cod = donnees.monstre_cod;
		end loop;
		temp_txt := ' (en mode ciblé) ';
	elsif v_type_dep = 2 then
		-- On choisit une cible aléatoire
		select into pos_aleatoire
			pos_cod
		from positions 
		where pos_x between (resultat.pos_x - 3) and (resultat.pos_x + 3)
			and pos_y between (resultat.pos_y - 3) and (resultat.pos_y + 3)
			and pos_etage = resultat.pos_etage
			and trajectoire_vue_murs(resultat.pos_cod, pos_cod) = 1
		order by distance(resultat.pos_cod, pos_cod) desc, random()
		limit 1;

		-- on y va
		while (resultat.perso_pa > 0)
		loop
			compt_loop := compt_loop + 1;
			exit when compt_loop >= v_depl_max OR temp_txt LIKE '1#Err%' OR distance(resultat.pos_cod, pos_aleatoire) <= v_distance;
			-- on récupère la case vers laquelle on se déplace
			-- on va sur cette nouvelle case
			-- on récupère les nouvelles infos
			temp_pos := dep_vers_cible(resultat.pos_cod, pos_aleatoire);
			temp_txt := deplace_code(donnees.monstre_cod, temp_pos);

			-- on récupère les nouvelles infos
			select into resultat.perso_pa perso_pa from perso where perso_cod = donnees.monstre_cod;

			select into resultat.pos_x, resultat.pos_y, resultat.pos_cod pos_x, pos_y, pos_cod
			from perso_position
			inner join positions on pos_cod = ppos_pos_cod
			where ppos_perso_cod = donnees.monstre_cod;
		end loop;
		temp_txt := ' (en mode directionnel aléatoire) ';
	elsif v_type_dep = 3 then
		while (resultat.perso_pa > 0)
		loop
			compt_loop := compt_loop + 1;
			exit when compt_loop >= v_depl_max;
			-- on récupère la case vers laquelle on se déplace
			-- on va sur cette nouvelle case
			-- on récupère les nouvelles infos
			temp_pos := f_deplace_aleatoire(donnees.monstre_cod, resultat.pos_cod);

			-- on récupère les nouvelles infos
			select into resultat.perso_pa perso_pa from perso where perso_cod = donnees.monstre_cod;

			select into resultat.pos_x, resultat.pos_y, resultat.pos_cod pos_x, pos_y, pos_cod
			from perso_position
			inner join positions on pos_cod = ppos_pos_cod
			where ppos_perso_cod = donnees.monstre_cod;
		end loop;
		temp_txt := ' (en mode aléatoire complet) ';
	end if;

	resultat.code_retour := resultat.code_retour || 'Déplacement' || temp_txt || 'de ' || donnees.pos_x::text || '/' || donnees.pos_y::text || ' vers ' || resultat.pos_x::text || '/' || resultat.pos_y::text || E'.<br />\
';

	return resultat;
end;$function$

