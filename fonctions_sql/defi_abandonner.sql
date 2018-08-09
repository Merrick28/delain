CREATE OR REPLACE FUNCTION public.defi_abandonner(integer, character)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction defi_abandonner                                  */
/*   Actions à exécuter lorsqu’un défi est rejeté            */
/*   on passe en paramètres :                                */
/*   $1 = v_defi : le numéro de défi                         */
/*   $2 = v_role : le rôle de celui qui abandonne le défi    */
/*        'L' = lanceur ; 'C' = cible ; '2' = commun accord  */
/* Sortie : la perte de renommée constatée, ou -1 en cas     */
/*    d’erreur.                                              */
/*************************************************************/
/* Créé le 10/01/2014                                        */
/*************************************************************/
declare
	-- Entrées de la fonction
	v_defi alias for $1;        -- Le code du défi rejeté
	v_role alias for $2;        -- Le rôle de celui qui abandonne le défi : 'L' = lanceur ; 'C' = cible ; '2' = commun accord

	-- Données de base
	donnees_defi record;        -- Les données du défi
	donnees_initiateur record;  -- Les données de l’initiateur de l’abandon
	donnees_adversaire record;  -- Les données de son adversaire
	
	-- Données de calcul
	v_diff_niveau integer;    -- La différence de rangs de renommée entre les protagonistes
	v_perte_renommee numeric;   -- La perte de renommée de la cible
	v_vainqueur character(1);   -- Le vainqueur du défi

	-- Événement
	v_texte text;               -- Le texte de l’événement à rajouter
begin
	-- Récupération des données du défi
	select into donnees_defi * from defi where defi_cod = v_defi;

	-- Vérification de son existence, et de son statut (pour être abandonné, un défi ne doit pas être terminé !)
	if not found or donnees_defi.defi_statut > 1 or v_role not in ('C', 'L', '2') then
		return -1;
	end if;

	-- Données de l’initiateur : code, nom, rang de renommée (magique ou guerrière, au mieux)
	select into donnees_initiateur
		perso_cod, perso_nom, perso_dlt, niveau_relatif(perso_px) as niveau
	from perso
	where perso_cod = (case v_role when 'L' then donnees_defi.defi_lanceur_cod
	                               when 'C' then donnees_defi.defi_cible_cod
	                               when '2' then donnees_defi.defi_lanceur_cod
	                               else NULL end);

	-- Données de son adversaire : code, nom, rang de renommée (magique ou guerrière, au mieux)
	select into donnees_adversaire
		perso_cod, perso_nom, niveau_relatif(perso_px) as niveau
	from perso
	where perso_cod = (case v_role when 'L' then donnees_defi.defi_cible_cod
	                               when 'C' then donnees_defi.defi_lanceur_cod
	                               when '2' then donnees_defi.defi_cible_cod
	                               else NULL end);

	-- TRAITEMENTS DE FIN DE DÉFI
	v_perte_renommee := 0;

	-- Malus à la renommée pour le perso qui abandonne le défi.
	-- Ce malus est proportionnel à la différence de rangs de renommée entre les deux protagonistes.
	if v_role <> '2' then
		v_diff_niveau := donnees_initiateur.niveau + 1 - donnees_adversaire.niveau;
		if v_diff_niveau > 0 then
			v_perte_renommee := v_diff_niveau / 30.0;
		end if;

		-- Perte de renommée limitée à 1.
		if v_perte_renommee > 1 then
			v_perte_renommee := 1;
		end if;

		-- Perte de renommée doublée si c’est l’initiateur du défi qui l’annule
		if donnees_initiateur.perso_cod = donnees_defi.defi_lanceur_cod then
			v_perte_renommee := v_perte_renommee * 2;
		end if;

		-- Perte de renommée nulle si la personne qui abandonne ne joue plus
		if donnees_initiateur.perso_dlt < donnees_defi.defi_date_debut then
			v_perte_renommee := 0;
		end if;

		if v_perte_renommee > 0 then
			update perso set perso_renommee = perso_renommee - v_perte_renommee where perso_cod = donnees_initiateur.perso_cod;
			update perso set perso_renommee = perso_renommee + v_perte_renommee where perso_cod = donnees_adversaire.perso_cod;
		end if;
	end if;

	-- Mise à jour du défi.
	v_vainqueur := case v_role when 'L' then 'C'
	                           when 'C' then 'L' end;

	-- Libération de la zone de combat
	update defi_zone set zone_libre = 'O' where zone_cod = donnees_defi.defi_zone_cod;

	-- Cas particulier : rejet du défi. Les protagonistes n’ont pas encore été déplacés.
	if donnees_defi.defi_statut = 0 then
		-- Mise à jour du défi
		update defi set defi_statut = 2, defi_date_fin = now(), defi_vainqueur = v_vainqueur where defi_cod = v_defi;
	else
		-- Autres cas : le défi est déjà commencé
		if v_role = '2' then
			update defi set defi_statut = 5, defi_date_fin = now() where defi_cod = v_defi;
		else
			update defi set defi_statut = 4, defi_date_fin = now(), defi_vainqueur = v_vainqueur where defi_cod = v_defi;
		end if;
		-- Remise à jour des persos
		perform defi_reinitialise_persos(v_defi);
	end if;

	-- On place un événement
	if v_role = 'L' and donnees_defi.defi_statut = 0 then
		v_texte := '[attaquant] a annulé son défi contre [cible].';
		perform insere_evenement(donnees_initiateur.perso_cod, donnees_adversaire.perso_cod, 99, v_texte, 'O', '[defi_cod]=' || donnees_defi.defi_cod::text);
	elsif v_role = 'C' and donnees_defi.defi_statut = 0 then
		v_texte := '[attaquant] a rejeté un défi de [cible].';
		perform insere_evenement(donnees_initiateur.perso_cod, donnees_adversaire.perso_cod, 96, v_texte, 'O', '[defi_cod]=' || donnees_defi.defi_cod::text);
	elsif v_role = '2' then
		v_texte := 'Le défi opposant [attaquant] et [cible] s’est soldé par un match nul.';
		perform insere_evenement(donnees_adversaire.perso_cod, donnees_initiateur.perso_cod, 98, v_texte, 'O', '[defi_cod]=' || donnees_defi.defi_cod::text);
	else
		v_texte := '[attaquant] a remporté son défi contre [cible].';
		perform insere_evenement(donnees_adversaire.perso_cod, donnees_initiateur.perso_cod, 97, v_texte, 'O', '[defi_cod]=' || donnees_defi.defi_cod::text);
	end if;

	-- Et on termine;
	return v_perte_renommee;
end;$function$

