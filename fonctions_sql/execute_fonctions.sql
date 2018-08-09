CREATE OR REPLACE FUNCTION public.execute_fonctions(integer, integer, character)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction execute_fonctions                                */
/*   Exécute les fonctions spécifiques liées à un monstre    */
/*    et/ou un personnage                                    */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod de la source              */
/*   $2 = cible_cod : si nécessaire, le numéro de la cible   */
/*   $3 = événement : D pour début de tour, M pour mort,     */
/*                    T pour Tueur, A pour Attaque           */
/* on a en sortie les sorties concaténées des fonctions.     */
/*************************************************************/
/* Créé le 19/05/2014                                        */
/*************************************************************/
declare
	v_perso_cod alias for $1;  -- Le code de la source
	v_cible_cod alias for $2;  -- Le numéro de la cible
	v_evenement alias for $3;  -- L’événement déclencheur

	code_retour text;          -- Le retour de la fonction
	retour_fonction text;      -- Le résultat de l’exécution d’une fonction
	ligne_fonction record;     -- Les données de la fonction
	code_fonction text;        -- Le code SQL lançant la fonction
	v_gmon_cod integer;        -- Le code du monstre générique
v_pos integer;    -- Le code de la position où se déroule l'effet

begin

if v_cible_cod is null and v_evenement != 'D' then
		v_cible_cod := v_perso_cod;
	end if;
	
        select into v_pos ppos_pos_cod
		from perso_position where ppos_perso_cod = v_cible_cod;


	code_retour := '';
	select into v_gmon_cod perso_gmon_cod from perso where perso_cod = v_perso_cod;

	for ligne_fonction in (
		select * from fonction_specifique
		where (fonc_gmon_cod = coalesce(v_gmon_cod, -1) OR (fonc_perso_cod = v_perso_cod))
			and fonc_type = v_evenement
			and (fonc_date_limite >= now() OR fonc_date_limite IS NULL)
		)
	loop
		code_fonction := ligne_fonction.fonc_nom;
		retour_fonction := '';

		if code_fonction = 'deb_tour_generique' then
			select into retour_fonction deb_tour_generique(v_perso_cod,
				ligne_fonction.fonc_effet,
				ligne_fonction.fonc_force,
				ligne_fonction.fonc_portee,
				ligne_fonction.fonc_type_cible,
				ligne_fonction.fonc_nombre_cible,
				ligne_fonction.fonc_proba,
				ligne_fonction.fonc_duree,
				ligne_fonction.fonc_message,
				v_cible_cod
			);

		elsif code_fonction = 'deb_tour_degats' then
			select into retour_fonction deb_tour_degats(v_perso_cod,
				ligne_fonction.fonc_force,
				ligne_fonction.fonc_portee,
				ligne_fonction.fonc_type_cible,
				ligne_fonction.fonc_nombre_cible,
				ligne_fonction.fonc_proba,
				ligne_fonction.fonc_message,
				v_cible_cod
			);

		elsif code_fonction = 'titre' then
			select into retour_fonction effet_auto_ajout_titre(v_cible_cod, ligne_fonction.fonc_message, v_perso_cod);

		elsif code_fonction = 'necromancie' then
			select into retour_fonction necromancie(v_perso_cod, v_cible_cod);

		elsif code_fonction = 'deb_tour_rouille' then
			select into retour_fonction deb_tour_rouille(v_perso_cod, ligne_fonction.fonc_proba::integer, ligne_fonction.fonc_force::integer, ligne_fonction.fonc_effet::integer);

		elsif code_fonction = 'deb_tour_invocation' then
			if v_pos is not null then
select into retour_fonction deb_tour_invocation(v_perso_cod, ligne_fonction.fonc_effet::integer, ligne_fonction.fonc_proba::integer, ligne_fonction.fonc_message, v_pos);
end if;

		elsif code_fonction = 'poison_araignee' then
			select into retour_fonction poison_araignee(v_perso_cod);

		elsif code_fonction = 'deb_tour_degats_case' then
			select into retour_fonction deb_tour_degats_case(v_perso_cod, ligne_fonction.fonc_force::integer, ligne_fonction.fonc_proba::integer, ligne_fonction.fonc_message);

		elsif code_fonction = 'deb_tour_esprit_damne' then
			select into retour_fonction deb_tour_esprit_damne(v_perso_cod);

		elsif code_fonction = 'trans_crap' then
			select into retour_fonction trans_crap(v_perso_cod);

		elsif code_fonction = 'deb_tour_necromancie' then
			select into retour_fonction deb_tour_necromancie(v_perso_cod, ligne_fonction.fonc_force::numeric, ligne_fonction.fonc_proba::integer);

		elsif code_fonction = 'deb_tour_haloween' then
			select into retour_fonction deb_tour_haloween(v_perso_cod, ligne_fonction.fonc_nombre_cible::integer, ligne_fonction.fonc_proba::integer);

		elsif code_fonction = 'valide_quete_avatar' then
			select into retour_fonction valide_quete_avatar(v_perso_cod, v_cible_cod);

		elsif code_fonction = 'invoque_rejetons' then
			select into retour_fonction invoque_rejetons(v_perso_cod, ligne_fonction.fonc_nombre_cible::integer, ligne_fonction.fonc_effet::integer);

		elsif code_fonction = 'resurrection_monstre' then
			select into retour_fonction resurrection_monstre(v_perso_cod, ligne_fonction.fonc_nombre_cible::integer, ligne_fonction.fonc_effet::integer, ligne_fonction.fonc_proba::integer);
		end if;

		if coalesce(retour_fonction, '') != '' then
			-- code_retour := code_retour || code_fonction || ' : ' || coalesce(retour_fonction, '') || '<br />';
			code_retour := code_retour || coalesce(retour_fonction, '') || '<br />';
		end if;
	end loop;

	if code_retour != '' then
		code_retour := replace('<br /><b>Effets automatiques :</b><br />' || code_retour, '<br /><br />', '<br />') || '<br />';
	end if;

	return code_retour;
end;$function$

