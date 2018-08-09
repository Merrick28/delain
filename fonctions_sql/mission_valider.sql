CREATE OR REPLACE FUNCTION public.mission_valider(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_valider                                  */
/*   Valide une mission et en attribue la récompense         */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 14/10/2013                                        */
/*************************************************************/
declare
	code_mission alias for $1;    -- Le code de la mission à valider
	v_mission record;             -- Les infos de la mission
	v_avancement integer;         -- Le %age de réussite de la mission.
	resultat text;                -- Le retour de la fonction
	vt_temp text;                 -- Variable de calcul
	vi_temp integer;              -- Variable de calcul
	v_nouveau_statut integer;     -- Le nouveau statut de la mission
	v_points integer;             -- Le nombre de points engrangés par la réussite de la mission
	v_gain_brouzoufs integer;     -- Le gain en brouzoufs
	v_gain_px integer;            -- Le gain en PX
	v_gain_points integer;        -- Le gain en points pour la faction
begin
	resultat := '';
	-- On récupère les informations de la mission
	select into v_mission * from mission_perso_faction_lieu where mpf_cod = code_mission;

	-- On vérifie le statut de la mission
	if v_mission.mpf_statut < 20 OR v_mission.mpf_statut >= 40 then
		return resultat || 'Erreur ! Cette mission ne devrait pas être validée !';
	end if;

	-- Mission réussié
	if v_mission.mpf_statut = 20 then
		v_avancement := 0;
		v_nouveau_statut := 40;
		resultat := 'Félicitations ! Vous avez accompli votre tâche avec succès ! Continuez comme ça, et nous saurons vous récompenser.<br />';

		-- Calcul des gains
		v_gain_brouzoufs := v_mission.mpf_recompense;
		v_gain_px := v_gain_brouzoufs / 50;
		v_gain_points := max(1, v_gain_brouzoufs / 500);
		-- Événement aléatoire : gain supérieur à celui escompté !
		if random() < 0.1 then
			v_gain_brouzoufs := (v_gain_brouzoufs * 1.5)::integer;
			v_gain_px := (v_gain_px * 1.5)::integer;
			v_gain_points := (v_gain_points * 1.5)::integer;
			resultat := resultat || 'En fait, vous vous êtes tellement bien acquitté de votre tâche que nous vous offrons une petite prime.<br />';
		end if;

	-- Mission échouée
	elsif v_mission.mpf_statut = 30 then
		v_avancement := 0;
		v_nouveau_statut := 50;
		resultat := 'Nous sommes extrêmement déçus ! Ce n’était pourtant pas si compliqué que ça... Il vous faudra faire mieux si vous souhaitez continuer de traiter avec nous.<br />';

		-- Calcul des gains
		v_gain_brouzoufs := 0;
		v_gain_px := 0;
		v_gain_points := min(-1, v_mission.mpf_recompense / -1000);

	-- Mission partiellement réussie
	else
		v_avancement := (v_mission.mpf_statut - 30);
		v_nouveau_statut := 50;
		if v_avancement <= 5 then
			resultat := 'Mouais... Vous avez essayé, sans doute... Mais les résultats sont loin de nos attentes ! Il vous faudra faire mieux si vous souhaitez continuer de traiter avec nous.<br />';

			-- Calcul des gains
			v_gain_brouzoufs := 0;
			v_gain_px := 0;
			v_gain_points := v_mission.mpf_recompense / -1000;
		elsif v_avancement <= 7 then
			resultat := 'C’est pas la panacée. Vous comprenez qu’on ne peut pas vous récompenser. Mais nous ne ferons pas de rapport négatif...<br />';

			-- Calcul des gains
			v_gain_brouzoufs := 0;
			v_gain_px := 0;
			v_gain_points := 0;
		else
			resultat := 'Vous n’avez pas réussi, mais on va quand-même pouvoir en faire quelque chose...<br />';

			-- Calcul des gains
			v_gain_brouzoufs := v_mission.mpf_recompense / 2;
			v_gain_px := v_gain_brouzoufs / 100;
			v_gain_points := v_gain_brouzoufs / 1000;
		end if;
	end if;

	resultat := resultat || 'Vous gagnez <b>' || v_gain_brouzoufs::text
		|| '</b> brouzoufs, <b>' || v_gain_px::text
		|| '</b> PX, ainsi que <b>' || v_gain_points::text
		|| '</b> points de réputation envers notre faction.<br />';

	-- Mise à jour des récompenses
	update perso set perso_px = perso_px + v_gain_px, perso_po = perso_po + v_gain_brouzoufs
	where perso_cod = v_mission.mpf_perso_cod;

	update faction_perso set pfac_points = pfac_points + v_gain_points
	where pfac_perso_cod = v_mission.mpf_perso_cod AND pfac_fac_cod = v_mission.mpf_fac_cod;

	-- Mise à jour du statut
	perform mission_modifie_statut(code_mission, v_nouveau_statut, v_avancement);

	return resultat;
end;$function$

