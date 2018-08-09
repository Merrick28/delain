CREATE OR REPLACE FUNCTION public.mission_releve_escorte(integer, integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_releve_escorte                           */
/*   Actions à exécuter lorsqu’une mission                   */
/*   de type « escorte » est relevée par un perso            */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de l’instance de mission   */
/*   $2 = perso_cod : le perso qui prend la mission          */
/* on a en sortie true ou false suivant que ça se soit bien  */
/* passé ou non.                                             */
/*************************************************************/
/* Créé le 13/12/2013                                        */
/*************************************************************/
declare
	v_mpf_cod alias for $1;      -- Le code de l’instance de mission
	v_personnage alias for $2;   -- Le perso qui relève la mission

	v_perso_cod integer;         -- Le perso_cod du perso à escorter
	v_race_cod integer;          -- La race du perso à escorter
	v_race_nom text;             -- La race du perso à escorter
	v_pv_max integer;            -- Les PV max
	v_armure integer;            -- L’armure
	v_sexe char;                 -- Le genre
	v_dlt integer;               -- La durée du tour
	v_pos_cod integer;           -- La position où apparaît le perso
	v_nom text;                  -- Le nom du perso
	v_desc text;                 -- La description du perso
	retour_cree_perso numeric;   -- Variable temporaire

begin
	v_race_cod := lancer_des(1, 3);
	v_pv_max := 100 + lancer_des(1, 100);
	v_armure := 5 + lancer_des(1, 10);
	v_dlt := 360 + lancer_des(1, 240);
	v_perso_cod := nextval('seq_perso');

	if random() < 0.5 then 
		v_sexe := 'F';
	else
		v_sexe := 'M';
	end if;

	select into v_pos_cod ppos_pos_cod from perso_position where ppos_perso_cod = v_personnage;
	select into v_race_nom race_nom from race where race_cod = v_race_cod;

	select into v_nom, v_desc
		'Émissaire de ' || fac_nom || ' (' || v_perso_cod::text || ')',
		'L’air important, cet émissaire ' || v_race_nom || ' porte les couleurs de ' || fac_nom || '.'
	from mission_perso_faction_lieu
	inner join factions on fac_cod = mpf_fac_cod
	where mpf_cod = v_mpf_cod;
	
	-- création du personnage
	insert into perso 
		(perso_cod, perso_sex, perso_nom, perso_for, perso_dex, perso_int, perso_con, perso_race_cod, perso_dlt,
		perso_temps_tour, perso_des_regen, perso_valeur_regen, perso_vue, perso_niveau, perso_amelioration_vue,
		perso_amelioration_regen, perso_amelioration_degats, perso_amelioration_armure, perso_actif, perso_type_perso,
		perso_dirige_admin, perso_sta_combat, perso_sta_hors_combat, perso_amel_deg_dex, perso_pnj, perso_quete,
		perso_description)
	values (v_perso_cod ,v_sexe, v_nom, 10, 10, 10, 10, v_race_cod, now(),
		v_dlt, 5, 1, 5, 1, 0,
		0, 2, v_armure, 'O', 1,
		'N', 'N', 'N', 0, 1, 'quete_accompagnateur.php',
		v_desc);

	retour_cree_perso := cree_perso(v_perso_cod);

	if retour_cree_perso <> 0 then
		return false;
	end if;

	-- Remise à niveau de certaines données écrasées
	update perso set perso_pv_max = v_pv_max, perso_pv = v_pv_max, perso_temps_tour = v_dlt
	where perso_cod = v_perso_cod;

	-- Ajout à un compte admin (pour ne pas se faire désactiver...)
	insert into perso_compte (pcompt_perso_cod, pcompt_compt_cod)
	values (v_perso_cod, 34416);

	-- Mise à jour de son IA
	insert into perso_ia (pia_perso_cod, pia_ia_type, pia_parametre, pia_msg_statut)
	values (v_perso_cod, 15, v_personnage, 0);

	-- Placement du PNJ
	insert into perso_position (ppos_pos_cod, ppos_perso_cod) 
	values (v_pos_cod, v_perso_cod);

	-- Mise à jour de la mission
	update mission_perso_faction_lieu
	set mpf_cible_perso_cod = v_perso_cod
	where mpf_cod = v_mpf_cod;
	
	return found;
end;$function$

