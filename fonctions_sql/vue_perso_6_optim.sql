CREATE OR REPLACE FUNCTION public.vue_perso_6_optim(integer)
 RETURNS SETOF type_vue
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function vue_perso :Procédure de détermination de la zone     */
/*                        de vue du personnage                   */
/* On passe en paramètres                                        */
/*    $1 = perso ciblé                                           */
/* En sortie, on a la chaine exploitable par JS                  */
/*		ordre de sortie :                                          */
/*		1 - position                                               */
/*    2 - X                                                      */
/*    3 - Y                                                      */
/*    4 - Nb persos                                              */
/*    5 - Nb monstres                                            */
/*    6 - Type affichage                                         */
/*    7 - Objets                                                 */
/*    8 - Or                                                     */
/*    9 - Distance                                               */
/*   10 - Type mur                                               */
/*   11 - Type case                                              */
/*	  12 - type batiment                                          */
/*****************************************************************/
/* Créé le 07/03/2003                                            */
/* Liste des modifications : 19/01/2004 : adaptation pour JS     */
/*****************************************************************/
declare
	ligne record;
	personnage alias for $1;
	nb_monstre integer;
	cpt integer;
	position_actuelle integer;
	x_actuel integer;
	y_actuel integer;
	etage_actuel integer;
	d_vue integer;
	nb_obj integer;
	nb_or integer;
	nb_perso integer;
	retour type_vue%rowtype;
	
begin
	SELECT into x_actuel, y_actuel, etage_actuel, position_actuelle, d_vue
		pos_x, pos_y, pos_etage, pos_cod, distance_vue(personnage)
	FROM perso_position
	INNER JOIN positions on pos_cod = ppos_pos_cod
	WHERE ppos_perso_cod = personnage;

	-- Cases concernées
	DROP TABLE IF EXISTS temp_cases;
	CREATE TEMP TABLE temp_cases (
		case_numero integer,
		case_pos_cod integer,
		case_pos_x integer,
		case_pos_y integer,
		case_pos_etage integer,
		case_pos_type_aff integer,
		case_pos_decor integer,
		case_pos_decor_dessus integer
	);

	INSERT INTO temp_cases (case_numero, case_pos_cod, case_pos_x, case_pos_y, case_pos_etage, case_pos_type_aff, case_pos_decor, case_pos_decor_dessus)
	SELECT rank() over (partition by 1 order by pos_y desc, pos_x asc) - 1,
		pos_cod, pos_x, pos_y, pos_etage, pos_type_aff, pos_decor, pos_decor_dessus
	FROM positions
	WHERE pos_x between (x_actuel - d_vue) and (x_actuel + d_vue) 
		AND pos_y between (y_actuel - d_vue) and (y_actuel + d_vue)
		AND pos_etage = etage_actuel
	ORDER BY pos_y desc, pos_x asc;

	return query
	SELECT case_numero,
		case_pos_cod,
		case_pos_x,
		case_pos_y,
		coalesce(perso_nombre * traj_ok, 0) as perso_nombre,
		coalesce(monstre_nombre * traj_ok, 0) as monstre_nombre,
		dauto_valeur,
		coalesce(obj_nombre * traj_ok, 0) as obj_nombre,
		coalesce(bzf_nombre * traj_ok, 0) as bzf_nombre,
		distance,
		dauto_vue,
		case_pos_type_aff,
		dauto_type_bat,
		case_pos_decor,
		traj_ok,
		case_pos_decor_dessus
	FROM temp_cases
	INNER JOIN donnees_automap ON dauto_pos_cod = case_pos_cod

	INNER JOIN (
		SELECT case_pos_cod as traj_pos_cod,
			trajectoire_vue3(position_actuelle, case_pos_cod) as traj_ok,
			distance(position_actuelle, case_pos_cod) as distance
		FROM temp_cases
	) traj ON traj_pos_cod = case_pos_cod

	LEFT OUTER JOIN (
		-- Sous-requête brouzoufs
		SELECT por_pos_cod, count(*)::integer as bzf_nombre
		FROM or_position
		INNER JOIN temp_cases on case_pos_cod = por_pos_cod
		GROUP BY por_pos_cod
	) bzf ON por_pos_cod = case_pos_cod

	LEFT OUTER JOIN (
		-- sous-requête objets
		SELECT pobj_pos_cod, count(*)::integer as obj_nombre
		FROM objet_position
		INNER JOIN temp_cases on case_pos_cod = pobj_pos_cod
		GROUP BY pobj_pos_cod
	) obj ON pobj_pos_cod = case_pos_cod

	LEFT OUTER JOIN (
		-- sous-requête perso
		SELECT ppos_pos_cod as perso_pos, count(*)::integer as perso_nombre
		FROM perso_position
		INNER JOIN temp_cases on case_pos_cod = ppos_pos_cod
		INNER JOIN perso on perso_cod = ppos_perso_cod
		WHERE perso_actif = 'O' and perso_type_perso = 1
		GROUP BY ppos_pos_cod
	) per ON perso_pos = case_pos_cod

	LEFT OUTER JOIN (
		-- sous-requête perso
		SELECT ppos_pos_cod as monstre_pos, count(*)::integer as monstre_nombre
		FROM perso_position
		INNER JOIN temp_cases on case_pos_cod = ppos_pos_cod
		INNER JOIN perso on perso_cod = ppos_perso_cod
		WHERE perso_actif = 'O' and perso_type_perso IN (2, 3)
		GROUP BY ppos_pos_cod
	) mon ON monstre_pos = case_pos_cod
	ORDER BY case_pos_y desc, case_pos_x asc;
end;$function$

