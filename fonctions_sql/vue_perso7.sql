--
-- Name: vue_perso7(integer); Type: FUNCTION; Schema: public; Owner: delain
--
DROP FUNCTION public.vue_perso7(integer)  ;
CREATE FUNCTION public.vue_perso7(integer) RETURNS SETOF public.type_vue7
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function vue_perso :Procédure de détermination de la zone     */
/*                        de vue du personnage                   */
/* On passe en paramètres                                        */
/*	$1 = perso ciblé                                         */
/* En sortie, on a la chaine exploitable par JS                  */
/*	ordre de sortie :                                        */
/*	1  - position                                            */
/*	2  - X                                                   */
/*	3  - Y                                                   */
/*	4  - Nb persos                                           */
/*	5  - Nb monstres                                         */
/*	6  - Type affichage                                      */
/*	7  - Objets                                              */
/*	8  - Or                                                  */
/*	9  - Distance                                            */
/*	10 - Type mur                                            */
/*	11 - Type case                                           */
/*	12 - type batiment                                       */
/*	13 - type terrain                                        */
/*****************************************************************/
/* Copie de vue_perso6 ajoutant l indicateur de bataille         */
/*****************************************************************/
declare
	ligne record;
	personnage alias for $1;
	texte_retour text;
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
	nb_lock integer;
	retour type_vue7%rowtype;

begin
	texte_retour := '';
	cpt := 0;
	select into x_actuel,y_actuel,etage_actuel,position_actuelle,d_vue
		pos_x,pos_y,pos_etage,pos_cod,distance_vue(personnage)
		from perso_position,positions
		where ppos_perso_cod = personnage
		and ppos_pos_cod = pos_cod;
	for ligne in SELECT 	pos_cod,
			pos_x,
			pos_y,
			pos_etage,
			pos_type_aff,
			pos_decor,
			pos_decor_dessus,
			dauto_valeur,
			dauto_type_bat,
			dauto_vue,
			distance(position_actuelle,pos_cod) AS distance,
			trajectoire_vue3(position_actuelle,pos_cod) AS trajectoire,
			coalesce(pos_ter_cod,0) as pos_ter_cod
	FROM 	positions,donnees_automap
	where dauto_pos_cod = pos_cod
	AND pos_etage = etage_actuel
	and pos_x between (x_actuel - d_vue) and (x_actuel + d_vue)
	AND pos_y between (y_actuel - d_vue) and (y_actuel + d_vue)
	ORDER BY pos_y desc,pos_x asc loop
		nb_obj := 0;
		nb_or := 0;
		nb_perso := 0;
		nb_monstre := 0;
		nb_lock := 0;
		if ligne.trajectoire != 0 then
			select into nb_obj count(distinct(pobj_cod)) from objet_position
				where pobj_pos_cod = ligne.pos_cod;
			select into nb_or count(distinct(por_cod)) from or_position
				where por_pos_cod = ligne.pos_cod;
			select into nb_perso COUNT(DISTINCT(perso_cod)) from perso_position,perso
				where ppos_pos_cod = ligne.pos_cod
				and ppos_perso_cod = perso_cod
				and perso_type_perso = 1
				and perso_actif = 'O';
			select into nb_monstre COUNT(DISTINCT(perso_cod)) from perso_position,perso
				where ppos_pos_cod = ligne.pos_cod
				and ppos_perso_cod = perso_cod
				and perso_type_perso in (2,3)
				and perso_actif = 'O';
			select into nb_lock COUNT(DISTINCT(lock_cible)) from perso_position, perso, lock_combat
				where ppos_pos_cod = ligne.pos_cod
				and ppos_perso_cod = perso_cod
				and perso_cod = lock_cible
				and perso_actif = 'O';
		end if;
		retour.tvue_num := cpt;
		retour.t_pos_cod := ligne.pos_cod;
		retour.t_x := ligne.pos_x;
		retour.t_y := ligne.pos_y;
		retour.t_nb_perso := nb_perso;
		retour.t_nb_monstre := nb_monstre;
		retour.t_nb_lock := nb_lock;
		retour.t_type_aff := ligne.dauto_valeur;
		retour.t_nb_obj := nb_obj;
		retour.t_or := nb_or;
		retour.t_dist := ligne.distance;
		retour.t_type_mur := ligne.dauto_vue;
		retour.t_type_case := ligne.pos_type_aff;
		retour.t_type_bat := ligne.dauto_type_bat;
		retour.t_decor := ligne.pos_decor;
		retour.t_traj := ligne.trajectoire;
		retour.t_decor_dessus := ligne.pos_decor_dessus;
		retour.t_pos_ter_cod := ligne.pos_ter_cod;
		return next retour;
cpt := cpt + 1;
	end loop;
	return;
end;
$_$;


ALTER FUNCTION public.vue_perso7(integer) OWNER TO delain;