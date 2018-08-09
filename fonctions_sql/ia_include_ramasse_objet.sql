CREATE OR REPLACE FUNCTION public.ia_include_ramasse_objet(ia_donnees, integer, integer, integer, integer)
 RETURNS ia_donnees
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ia_include_ramasse_objet :                           */
/*      Procédure utilisée par les différentes IA pour gérer les */
/*      ramassages d’un type d’objet                             */
/* On passe en paramètres                                        */
/*    $1 = les données d’IA (de type ia_donnees)                 */
/*    $2 = la limite de vue du monstre                           */
/*    $3 = le type d’objets à ramasser                           */
/*    $4 = le nombre max à porter en inventaire                  */
/*    $5 = le nombre max à ramasser à chaque appel               */
/*****************************************************************/
/* Créé le 12/12/2012                                            */
/*****************************************************************/
declare
	donnees alias for $1;           -- l’ensemble des données d’IA à transmettre entre fonctions
	v_vue alias for $2;             -- la limite de vue du monstre
	v_tobj_cod alias for $3;        -- le type d’objets à ramasser
	v_inventaire_max alias for $4;  -- le nombre max à porter en inventaire
	v_ramassage_max alias for $5;   -- le nombre max à ramasser à chaque appel

	resultat ia_donnees; -- le résultat de la fonction

	pos_rune integer;             -- position de la rune à ramasser
	cod_rune integer;             -- code de la rune à ramasser
	v_nombre_portes integer;      -- Le nombre d’objets du type voulu actuellement portés par le perso

	compt_loop integer;           -- un compteur de boucle, permettant d’arrêter la boucle en cas de problème

	temp_txt text;                -- fourre-tout
begin
	resultat := donnees;
	resultat.code_retour := E'Ramassage d’objets ?<br />\
';

	compt_loop := 0;
	temp_txt := '';
	pos_rune := 0;

	-- on regarde si on n’a pas suffisamment d’objets de ce type en inventaire.
	select into v_nombre_portes count(*) from perso_objets
	inner join objets on obj_cod = perobj_obj_cod
	inner join objet_generique on gobj_cod = obj_gobj_cod
	where gobj_tobj_cod = v_tobj_cod and perobj_perso_cod = donnees.monstre_cod;
	
	if v_nombre_portes >= v_inventaire_max then
		resultat.code_retour := resultat.code_retour || E'Trop en inventaire !<br />\
';
		return resultat;
	end if;

	-- on récupère la position la plus proche en contenant
	select into pos_rune pobj_pos_cod
	from objet_position
	inner join objets on obj_cod = pobj_obj_cod
	inner join objet_generique on gobj_cod = obj_gobj_cod
	inner join positions on pos_cod = pobj_pos_cod
	where pos_x between (resultat.pos_x - v_vue) and (resultat.pos_x + v_vue)
		and pos_y between (resultat.pos_y - v_vue) and (resultat.pos_y + v_vue)
		and pos_etage = resultat.pos_etage
		and gobj_tobj_cod = v_tobj_cod
		and trajectoire_vue_murs(resultat.pos_cod, pos_cod) = 1
	order by max(abs(pos_x - resultat.pos_x), abs(pos_y - resultat.pos_y)), random()
	limit 1;

	if pos_rune > 0 then
		-- on se déplace vers cette position
		resultat.code_retour := resultat.code_retour || 'Objet repéré en ' || pos_rune::text || ' ! ';
		resultat := ia_include_deplacement(resultat, 1, v_vue, pos_rune, 0);
	end if;

	-- on ramasse jusqu’à X runes...
	compt_loop := 0;
	cod_rune := 0;
	while (resultat.perso_pa > 0) loop
		compt_loop := compt_loop + 1;
		exit when compt_loop > v_ramassage_max OR cod_rune = -1 OR v_nombre_portes + compt_loop > v_inventaire_max;

		select into cod_rune pobj_obj_cod
		from objet_position
		inner join objets on obj_cod = pobj_obj_cod
		inner join objet_generique on gobj_cod = obj_gobj_cod
		where pobj_pos_cod = resultat.pos_cod
			and gobj_tobj_cod = v_tobj_cod
		limit 1;
		if found then -- on ramasse !
			temp_txt := ramasse_objet(donnees.monstre_cod, cod_rune);
			resultat.code_retour := resultat.code_retour || temp_txt || E'<br />\
';
		else
			cod_rune := -1;
		end if;
		-- mise à jour des PA
		select into resultat.perso_pa perso_pa from perso where perso_cod = donnees.monstre_cod;
	end loop;
	return resultat;
end;$function$

