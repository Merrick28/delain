CREATE OR REPLACE FUNCTION public.cree_monstre_invasion(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
 STRICT
AS $function$/*****************************************************************/
/* function cree_monstre_invasion:Procédure de création de monstre */
/* en position aléatoire, en ajustant son niveau à l’étage       */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gmon_cod (monstre générique)                        */
/*   $2 = le niveau ou il doit apparaitre                        */
/* Le code sortie est :                                          */
/*    le numéro de monstre                                       */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer;
	v_gmon alias for $1;
	v_level alias for $2;
-- récupération des données génériques
	v_num_monstre integer;
	pos_portail integer;
	niveau_moyen integer;
	v_amelioration_degats integer;
	v_amelioration_degats_distance integer;
	v_amelioration_armure integer;
	v_niveau integer;
	v_pv integer;
	v_temps_tour integer;
	v_modif_carac decimal;
	v_con integer;


begin
/**********************************************/
/* Etape 1 : on insère dans perso les valeurs */
/**********************************************/
	code_retour := 0;
	v_num_monstre := cree_monstre(v_gmon,v_level);
	pos_portail := pos_aleatoire(v_level);
	update perso_position set ppos_pos_cod = pos_portail where ppos_perso_cod = v_num_monstre;
	code_retour := v_num_monstre;

/**********************************************/
/* Etape 2 : on retouche le monstre           */
/**********************************************/
	select into niveau_moyen sum(perso_niveau)/count(perso_niveau)  from perso, perso_position, positions
		where perso_cod = ppos_perso_cod and ppos_pos_cod = pos_cod
		and pos_etage = (select etage_reference from etage where etage_numero = v_level )
		and perso_type_perso = 1 and perso_actif = 'O' and perso_pnj != 1;

	-- Armure : Niveau moyen * armure / 6
	-- Niveau : Niveau_moyen * niveau / 6 (armure = niveau / 2)
	-- PX : 0 
	-- Dégâts : Pas besoin en 2011.
	-- Caracs/Comps : Pas besoin non plus
	-- pv_max : 
	-- Temps de tour : dlt - 5*Niveau_moyen
	-- caracs : carac * niveau_moyen / 30.

	v_modif_carac := niveau_moyen / 30::numeric;

	select into v_amelioration_degats, v_amelioration_degats_distance, v_amelioration_armure, v_niveau, v_temps_tour, v_con
		gmon_amelioration_degats * niveau_moyen/6,
		gmon_amel_deg_dist * niveau_moyen/6,
		gmon_amelioration_armure * niveau_moyen/6,
		gmon_niveau * niveau_moyen / 6,
		gmon_temps_tour - 5 * niveau_moyen,
		(gmon_con * v_modif_carac)::integer
		from monstre_generique where gmon_cod = v_gmon;

	v_pv := v_con * 2 + v_niveau - 1 + lancer_des(v_niveau - 1, cast((v_con/4) as integer));

	update perso set 
		perso_amelioration_degats = v_amelioration_degats,
		perso_amel_deg_dex = v_amelioration_degats_distance,
		perso_amelioration_armure = v_amelioration_armure,
		perso_niveau = v_niveau,
		perso_pv_max = v_pv,
		perso_pv = v_pv,
		perso_temps_tour = v_temps_tour,
		perso_con = (perso_con * v_modif_carac)::integer,
		perso_dex = (perso_dex * v_modif_carac)::integer,
		perso_for = (perso_for * v_modif_carac)::integer,
		perso_int = (perso_int * v_modif_carac)::integer
	where perso_cod = v_num_monstre;

	update perso
	set perso_px = limite_niveau_actuel(perso_cod)
	where perso_cod = v_num_monstre;

	update perso_competences
	set pcomp_modificateur = pcomp_modificateur + v_niveau
	where pcomp_perso_cod = v_num_monstre;

	return code_retour;
end;
 $function$

