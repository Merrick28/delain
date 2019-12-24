
--
-- Name: sortir_gm(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION sortir_gm(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/* ***************************************************** */
/* Fonction sortir_gm					 */
/* 							 */
/* Permet de faire sortir du sac un aventurier		 */
/*   ayant subit l attaque spéciale Garde Manger.	 */
/* 							 */
/* Paramètres :						 */
/*   $1 = perso_cod (integer)				 */
/* ***************************************************** */
/* Créé le 08/11/2010					 */
/* ***************************************************** */

declare
	in_perso_cod	alias for $1;	-- Numéro de perso
	v_perso_pa	integer;	-- Points d action restant
	v_perso_for	numeric;	-- Niveau de force
	v_perso_dex	numeric;	-- Niveau de dexterité
	v_perso_pv	numeric;	-- Niveau de vie
	v_perso_pv_max	integer;	-- Valeur plafond de la vie
	v_pos_etage	integer;	-- Etage actuellement visité
	v_back_pos	integer;	-- Précédente position
	v_type_perso    integer;	-- Type de perso. Les familiers ne peuvent sortir seuls
	etage_gm	integer;	-- Valeur de l étage du garde manger
	cout_pa		integer;	-- Nombre de PA à utiliser pour sortir
	for_max		integer;	-- Valeur plafond de la force
	dex_max		integer;	-- Valeur plafond de la dextérité
	lvl_difficult	integer;	-- Niveau de difficulté (facile=3, difficile=6)
	lvl_reussite	numeric;	-- Seuil de réussite
	res_des		integer;	-- Résultat des dés
	code_retour	text;		-- Texte à afficher (1 en début de chaine pour masquer le menu d action)
	texte_evt	text;		-- Texte à placer dans les évènements

begin
	-- Initialisation
	etage_gm	:= 90;
	cout_pa		:= 6;
	for_max		:= 20;
	dex_max		:= 20;
	lvl_difficult	:= 3;

	-- Récupération des données
	select into
		v_perso_pa,
		v_perso_for,
		v_perso_dex,
		v_perso_pv,
		v_perso_pv_max,
		v_type_perso,
		v_pos_etage
		perso_pa,
		perso_for,
		perso_dex,
		perso_pv,
		perso_pv_max,
		perso_type_perso,
		pos_etage
	from	perso,
		perso_position,
		positions
	where	ppos_perso_cod = perso_cod
	and	pos_cod = ppos_pos_cod
	and	perso_cod = in_perso_cod;

	if found then
		-- Vérif de la position du perso
		if (v_pos_etage = etage_gm) then

			-- Vérif du nombre de PA
			if (v_perso_pa >= cout_pa) then

				-- Calcul
				lvl_reussite := floor( ((v_perso_for / for_max) + (v_perso_dex / dex_max) + (v_perso_pv / v_perso_pv_max)) * 100 / lvl_difficult );

				-- Lancer de dés
				res_des := lancer_des(1,100);
				code_retour := '<p>Vos chances d&apos;en ressortir sont de '||trim(to_char(lvl_reussite,'999'))||'%.<br />';
				code_retour := code_retour||'Votre r&eacute;sultat est de '||trim(to_char(res_des,'999'))||'.</p>';

				if (v_type_perso != 3 and res_des <= lvl_reussite) then
					-- Réussite : Téléportation de retour

					-- Récupération de la position avant la capture
					select into
						v_back_pos
						pgm_pos_cod
					from	perso_gmanger
					where	pgm_perso_cod = in_perso_cod;

					-- Mise à jour de la position du perso
					update	perso_position
					set	ppos_pos_cod = v_back_pos
					where	ppos_perso_cod = in_perso_cod;

					-- Elimination des traces du perso dans le garde manger
					delete
					from	perso_gmanger
					where	pgm_perso_cod = in_perso_cod;

					-- Suppression du malus DGM (asphyxie)
					delete
					from	bonus
					where	bonus_perso_cod = in_perso_cod
					and (	bonus_tbonus_libc = 'DGM'
					  or	bonus_tbonus_libc = 'VUE'
					);

					-- Message de réussite
					code_retour := '1'||code_retour||'<p>Vous voila enfin &agrave; l&apos;air libre !</p>';
					texte_evt := '[perso_cod1] a r&eacute;ussi &agrave; sortir du sac dans lequel il &eacute;tait enferm&eacute;.';

				else
					-- Message d échec
					code_retour := '0'||code_retour||'<p>Vous n&apos;avez pas r&eacute;ussi &agrave; sortir.</p>';
					if (res_des <= lvl_reussite) then
						code_retour := code_retour||'<p>Il semblerait que votre ma&icirc;tre vous ait retenu au dernier moment. Vous ne vous affranchirez pas aussi facilement...</p>';
					end if;
					texte_evt := '[perso_cod1] a tent&eacute; en vain de sortir du sac.';

				end if;

				-- Retirer les PA
				update	perso
				set	perso_pa = (perso_pa - cout_pa)
				where	perso_cod = in_perso_cod;

				-- MaJ des évènements
				insert into ligne_evt(
					levt_cod,
					levt_tevt_cod,
					levt_date,
					levt_type_per1,
					levt_perso_cod1,
					levt_texte,
					levt_lu,
					levt_visible,
					levt_attaquant
				) values(
					nextval('seq_levt_cod'),
					79,
					now(),
					1,
					in_perso_cod,
					texte_evt,
					'O',
					'O',
					in_perso_cod
				);

			else
				code_retour := '1<p>Votre personnage n&apos;a pas suffisamment de points d&apos;action pour tenter de sortir ('||trim(to_char(cout_pa,'999'))||' PA).</p>';
			end if;

		else
			code_retour := '1<p>Votre personnage ne se trouve pas au bon &eacute;tage.</p>';
		end if;
	end if;

	return code_retour;
end;$_$;


ALTER FUNCTION public.sortir_gm(integer) OWNER TO delain;