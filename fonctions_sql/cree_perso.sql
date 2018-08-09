CREATE OR REPLACE FUNCTION public.cree_perso(integer)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_perso :Procédure de création de personnage      */
/* Cela suppose un personnage fraichement créé qui n a aucun     */
/* enregistrement en perso_type_competences ou perso_competences */
/*                                                               */
/* On passe en paramètre le perso_cod                            */
/* Le code sortie est :                                          */
/*    0 = Tout s est bien passé                                  */
/*    1 = il existe déjà un perso_type_competences               */
/*    2 = il existe déjà un perso_competences                    */
/*****************************************************************/
/* Liste des modifications :                                     */
/*   le 23/01/2003 : calcul des PV et PV max                     */
/*   le 03/03/2003 : changement du calcul des compétences        */
/*                   en fonction des races                       */
/*****************************************************************/
declare
        	personnage alias for $1;
        /* Variables pour la DLT */
        	date_lt timestamp;
         code_erreur numeric;
        /* Variables pour les modif de type de compétences */
        	compt_typc integer;
        	mod_for numeric;
        	mod_dex numeric;
        	mod_int numeric;
        	perso_for_base integer;
        	perso_dex_base integer;
        	perso_int_base integer;
        /* Variables pour les modif de compétences */
         race_perso perso.perso_race_cod%type;

        	compt_comp perso_competences%rowtype;
        	ligne_competences record;
        	modif_type integer;
        	modif_competences competences.comp_modificateur%type;
begin
	code_erreur := 0; -- par défaut, tout se passe bien
/*-------------------------------------
On met les infos de base
---------------------------------------*/
	update perso
		set perso_po = 150, perso_dcreat = now(), perso_px = 0,perso_nb_esquive = 0, perso_niveau = 1,perso_amelioration_vue = 0, perso_amelioration_regen = 0,perso_amelioration_degats = 0, perso_amelioration_armure = 0,perso_enc_max = (3*perso_for),perso_nb_des_degats = 1,perso_val_des_degats = 3,perso_nb_mort = 0,perso_nb_monstre_tue = 0,perso_nb_joueur_tue = 0,perso_amel_deg_dex = 0,perso_lower_perso_nom = ltrim(rtrim(lower(perso_nom)))
		where perso_cod = personnage;
/*-------------------------------------
On calcule les PV et PV max
---------------------------------------*/
	update perso
		set perso_pv = (2*perso_con),perso_pv_max = (2*perso_con)
		where perso_cod = personnage;
/*-------------------------------------
On calcule les dates limites d action
---------------------------------------*/
	update perso
		set perso_temps_tour = 720,perso_renommee = 0,perso_kharma = 0
		where perso_cod = $1; -- par défaut, le temps d action est de 12 heures
	date_lt := now() + '720minutes';
	update perso
		set perso_dlt = date_lt
		where perso_cod = personnage;
/*-------------------------------------
On passe aux modificateurs de type de compétences
---------------------------------------*/
/*-------------------------------------
On passe aux compétences
---------------------------------------*/
	/* Competences connues "classiques" */
	for ligne_competences in select * from competences where comp_connu = 'O' loop
		/*select into modif_type ptypc_modificateur
			from competences,perso_type_competences
			where comp_cod = ligne_competences.comp_cod
			and comp_typc_cod = ptypc_typc_cod
			and ptypc_perso_cod = personnage;
		select into modif_competences comp_modificateur from competences
			where comp_cod = ligne_competences.comp_cod;*/
		insert into perso_competences values
			(nextval('seq_pcomp'),personnage,ligne_competences.comp_cod,valeur_comp_init(personnage,ligne_competences.comp_cod));
	end loop;
	/* Competences de race */
	select into race_perso perso_race_cod from perso where perso_cod = personnage;
	for ligne_competences in select * from race_comp where racecomp_race_cod = race_perso loop
		insert into perso_competences values
			(nextval('seq_pcomp'),personnage,ligne_competences.racecomp_comp_cod,valeur_comp_init(personnage,ligne_competences.racecomp_comp_cod));				
	end loop;
	if race_perso = 1 then
		insert into perso_sorts (psort_perso_cod,psort_sort_cod)
			values (personnage,6);
	end if;
	if race_perso = 3 then
		insert into perso_sorts (psort_perso_cod,psort_sort_cod)
			values (personnage,5);
	end if;
return code_erreur;
end;
$function$

