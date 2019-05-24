
--
-- Name: tue_perso_final(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION tue_perso_final(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction tue_perso_final                                  */
/*   accomplit les actions consécutives à la mort d’un perso */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod attaquant                                */
/*   $2 = perso_cod cible                                    */
/* on a en sortie une chaine séparée par ;                   */
/*   1 = px gagnés par l’attaquant                           */
/*   2 = texte du partage de px                              */
/*   3 = texte éventuel de la milice                         */
/*   4 = texte complet des gains                             */
/*************************************************************/
/* Créé le 22/05/2003                                        */
/*	14/12/2007 : pas mal de correction sur la renommée       */
/*	et la gestion du kharma			                             */
/* Modif Blade 06/11/2009 : suppression des empoisonnements  */
/* Modif Az 01/04/2010 : assouplissment de la mort           */
/* Modif Reivax 15/10/2012 : pas de pertes de karma en cas   */
/*                           de suicide                      */
/* Modif Reivax 19/11/2012 : monstres trahissant Malkiar     */
/* Modif Reivax 18/01/2013 : externalisation perte d’objets  */
/*                           Rajout de la mort définitive    */
/* Modif Kahlann 23/01/2015 : suppression BRU et MDS         */
/*                            mort en arène familier reste   */
/*                            impalpable 4T                  */
/* Modif Kahlann 10/06/2015 : prise en compte du paramétrage */
/*                            du taux de perte d'xp de       */
/*                            l'étage                        */
/* Modif Marlyza 16/05/2018 : ajout de la fonction rappel    */
/*                            du familier                    */
/*************************************************************/
declare
	code_retour text;
	text_retour text;
	v_attaquant alias for $1;
	v_cible alias for $2;
-- variables de la cible
	pos_cible integer;
	type_cible integer;
	cible_pv_max integer;
	nouveau_pv numeric;
	niveau_cible integer;
	nom_cible perso.perso_nom%type;
	kharma_cible numeric;
	dieu_cible text;                -- Divinité de la cible
	v_familier integer;
	v_prop_familier integer;
	coterie integer;                -- coterie de la cible
	chef integer;                   -- chef de la coterie de la cible
	nom_groupe text;                -- nom de la coterie de la cible
	nom_nouveau_chef text;          -- Nouveau nom du chef
	nouveau_chef integer;           -- Nouveau chef de la coterie désigné automatiquement
	v_envoie_message integer;       -- Indique si le perso envoie un message à sa coterie en mourrant
	v_perso_actif text;
	v_perso_mortel text;
        v_tangible text;
-- variables attaquant
	niveau_attaquant integer;
	kharma_attaquant numeric;
	type_attaquant integer;
	titre_malediction text;         --Malédiction qui sera visible dans les titres du perso
-- variables de calcul
	ligne_groupe record;
	texte_evt text;
	px_gagnes numeric;
	total_px_gagnes numeric;
	px_perdus numeric;
	px_cible numeric;
	nouveau_px numeric;
	cible_x integer;
	cible_y integer;
	cible_etage integer;
	variation_kharma numeric;
	modif_renommee real;
	modif_renommee_m real;
	v_gmon_cod integer;
	v_cree_monstre integer;
	v_perso_vampire integer;
	v_perso_kharma_vampire integer;
	partage_joueur integer;
	partage_fait integer;
	malediction integer;         -- Détermine si une malédiction sera lancé sur les tueurs
	nb_trans integer;            -- Variable de calcul pour les transactions
	temp_int integer;            -- Variable de calcul pour les transactions
	texte text;                  -- Variable pour le texte de l'évènement des transaction
	v_type_arene integer;        -- Type d'arene
	v_imp_F integer;             -- durée impalpabilité d'un familier dans l'étage
        v_imp_P integer;             -- durée impalpabilité d'un personnage dans l'étage
        v_taux_xp integer;           -- taux de perte d'xps de l'étage
-- MILICE
	mode_milice integer;
	en_prison integer;
	texte_prison text;
	type_peine integer;
	numero_peine bigint;
	milice_texte text;
-- evenements de morts
	v_fonction_mort text;
	v_fonction_mort_modif text;
	temp_act integer;
-- partage auto des px
	texte_partage_px text;
	ligne_px record;
	nom_perso_px text;
	n1 integer;
	n1_r integer;
	px1 numeric;
	n2 integer;
	n2_r integer;
	px2 numeric;
	px_theo numeric;
	px_perso_n numeric;
	px_perso_attaquant numeric;
	total_points numeric;
	fonction_debut text;
	v_compteur_partage integer;
	texte_karma text;
	karma_perso_cod integer;
	cpl_txt_karma text;
	nb_part integer;
-- Messages
	v_mes integer;              -- Numéro du message de coterie
	v_corps text;               -- Contenu du message
	v_titre text;
	v_tueur_avatar integer;     -- Titre du message
-- variables pour une mort moins pénalisante
	v_gravité integer;          -- Niveau de gravité de la mort
	v_cible_riposte integer;    -- test pour savoir si légitime défense
	v_comp1 integer;            -- variable four tout pour ce calcul
	v_comp2 integer;            -- variable four tout pour ce calcul
	v_comp3 integer;
	v_etage_arene text;         -- variable four tout pour ce calcul
	v_defi record;              -- données sur l’éventuel défi en cours
begin
	code_retour := '';
	text_retour := '';
	total_px_gagnes := 0;
        v_compteur_partage := 0;
	partage_joueur := 0;
	partage_fait := 0;

if v_attaquant != v_cible then
	/**************************************************/
	/* on regarde si une fonction doit être exécutée  */
	/**************************************************/
	-- on exécute les fonctions déclenchées par le tueur (nécromanciens...)
	code_retour := code_retour || execute_fonctions(v_attaquant, v_cible, 'T');

	-- on exécute les fonctions déclenchées par la mort de la cible (gelées, avatar, etc.)
	code_retour := code_retour || execute_fonctions(v_cible, v_attaquant, 'M');
end if;
	/**************************************************/
	/* Etape 1 : on récupère les infos de la cible    */
	/**************************************************/
	texte_prison := '';
	select into pos_cible, type_cible, cible_pv_max, niveau_cible, px_cible, nom_cible, kharma_cible, v_gmon_cod, v_etage_arene, v_perso_mortel, v_type_arene
		ppos_pos_cod, perso_type_perso, perso_pv_max, perso_niveau, perso_px, perso_nom, perso_kharma, perso_gmon_cod, etage_arene, perso_mortel, etage_type_arene
	from perso_position, perso, positions, etage
	where ppos_perso_cod = v_cible
		and perso_cod = v_cible
		and ppos_pos_cod = pos_cod
		and pos_etage = etage_numero;

	select into malediction ptitre_type
	from perso_titre
	where ptitre_type = 1
		and ptitre_perso_cod = v_cible;
	if found then 					-- Une malédiction sera lancée sur chaque participant au meurtre
		malediction := 3;			-- Le code du titre de malédiction

		select into dieu_cible dieu_nom
		from dieu_perso, dieu
		where dper_perso_cod = v_cible
			and dper_dieu_cod = dieu_cod;
	end if;

	select into kharma_attaquant, v_perso_vampire
		perso_kharma, perso_niveau_vampire
	from perso
	where perso_cod = v_attaquant;

	select into cible_x, cible_y, cible_etage pos_x, pos_y, pos_etage
	from positions
	where pos_cod = pos_cible;

	select into niveau_attaquant perso_niveau
	from perso
	where perso_cod = v_attaquant;

        select into v_imp_F, v_imp_P, v_taux_xp etage_duree_imp_f, etage_duree_imp_p, etage_perte_xp from etage where etage_numero = cible_etage;

--
-- compteurs de morts
--
	if type_cible = 1 then
		update parametres set parm_valeur = parm_valeur + 1
		where parm_cod = 64;
	elsif type_cible = 2 then
		update parametres set parm_valeur = parm_valeur + 1
		where parm_cod = 65;
	elsif type_cible = 3 then
		update parametres set parm_valeur = parm_valeur + 1
		where parm_cod = 66;
	end if;
--
-- fin compteurs
--

--
-- Récupération des monstres traîtres à la cause de Malkiar -- initialisation de la table
--
	create temp table temp_traitres(traitres_perso_cod integer not null) on commit drop;

	/*********************************/
	/* DEBUT : CHUTE DES EQUIPEMENTS */
	/*********************************/
---2.0 : on détermine le niveau de gravité de la mort
	select into v_cible_riposte riposte_nb_tours
	from riposte
	where riposte_attaquant = v_cible
		and riposte_cible = v_attaquant;
	if v_cible_riposte >= 1 then
		-- on est dans le cadre d'une légitime défense, la gravité de la mort est nulle
		v_gravité := 0;
	else
		-- on est pas dans le cadre d'une légitime défense, on va donc déterminer une gravité que l'on testera
		-- pour déterminer si on perds de l'équipement et où on replace la cible
		-- on ne traite que pour les persos non monstres
		if type_cible <> 1 then
			v_gravité := 0;
			-- uniquement si l'attaquant est niv > cible
		else
			if niveau_attaquant < niveau_cible then
				v_gravité := 0;
			else
				v_comp1 :=0;
				v_comp2 :=0;
				v_comp3 :=0;
				v_comp1 := niveau_attaquant - niveau_cible;
				v_comp1 := floor(v_comp1 / 5);
				v_comp2 := floor(v_comp1 * (niveau_attaquant / niveau_cible));
				v_comp3 := lancer_des(1, 10);
				v_comp3 := v_comp3 + v_comp2;
			end if;
		end if;
	end if;
	--  on peut déterminer enfin le niveau gravité

	if v_comp3 > 20 then
		v_gravité := 2;
	else
		if v_comp3 > 11 then
			v_gravité := 1;
		else
			v_gravité := 0;
		end if;
	end if;
       -- 2.1 : la cible est un monstre :
	if type_cible = 2 then
		-- Gestion de la perte des objets (1 == mort définitive)
		v_corps := tue_perso_perd_objets(v_cible, 1);

		/* Marlyza: On garde dans perso_cible la trace de son tueur, ça peut servir pour les quêtes auto (à la place de fouiller dans les events) */
    update perso set perso_actif = 'N', perso_cible=v_attaquant WHERE perso_cod = v_cible;

		/*******************************************************/
		/* DEBUT : GESTION DES FONCTIONS DES MORTS DE MONSTRES */
		/*******************************************************/

		select into v_fonction_mort gmon_fonction_mort from monstre_generique
			where gmon_cod = v_gmon_cod;
		if found then
			if v_fonction_mort is not null then
				select into temp_act act_perso_cod from action_monstre
					where act_perso_cod = v_cible;
				if not found then
					v_fonction_mort_modif := 'select mort_monstre(' || trim(to_char(v_cible, '9999999999')) || ', ' || trim(to_char(v_attaquant, '99999999')) || ', ';
					v_fonction_mort_modif := v_fonction_mort_modif || trim(to_char(pos_cible, '9999999999')) || ', ''' || v_fonction_mort || ''')';
					execute v_fonction_mort_modif;
				end if;
			end if;
		end if;
		/*******************************************************/
		/* FIN   : GESTION DES FONCTIONS DES MORTS DE MONSTRES */
		/*******************************************************/
	elsif type_cible = 3 then -- on a affaire à un familier
                v_corps := type_cible;
		if v_etage_arene = 'O'  then
			-- mort du familier dans une arène : familier passe impalpable, pas de perte d'objets en arène
			update perso set perso_tangible = 'N', perso_nb_tour_intangible = v_imp_F
				where perso_cod = v_cible;
		else
			v_corps := 'À la suite du décès de votre familier, les objets suivants viennent de tomber au sol : ';

			-- Gestion de la perte des objets (1 == mort définitive)
			v_corps := v_corps || tue_perso_perd_objets(v_cible, 1);
                        update perso set perso_actif = 'N' WHERE perso_cod = v_cible;

			select into v_prop_familier
				pfam_perso_cod
				from perso_familier
				where pfam_familier_cod = v_cible; --On sélectionne le propriétaire du familier
			if found then
				v_mes := nextval('seq_msg_cod');
				v_titre := 'Perte d’équipement de votre familier décédé';
				v_titre := substring(v_titre from 1 for 50);
				insert into messages (msg_cod, msg_date2, msg_date, msg_titre, msg_corps)
				values (v_mes, now(), now(), v_titre, v_corps);

				insert into messages_exp (emsg_msg_cod, emsg_perso_cod, emsg_archive)
				values (v_mes, v_cible, 'N');

				insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu, dmsg_archive)
				values (v_mes, v_prop_familier, 'N', 'N');
			end if;
		end if;
	-- 2.2 : la cible est un joueur
	elsif type_cible = 1 then
                /* Modif Kahlann */
		-- si un perso meurt en arène, on remet familier impalpable de la durée d'impalpabilité parametrée pour l'étage
		if v_etage_arene = 'O' then
			select into v_familier max(pfam_familier_cod) from perso_familier inner join perso on perso_cod = pfam_familier_cod
			where pfam_perso_cod = v_cible and perso_actif = 'O';   --- Marlyza - 16/05/2018 - prendre seulement le fam actif !

			if found then
                                select into v_tangible perso_tangible from perso where perso_cod = v_familier;
                                if v_tangible = 'O' then
				     update perso set perso_tangible = 'N', perso_nb_tour_intangible = v_imp_F
				     where perso_cod = v_familier;
                                end if;
			end if;
		end if;

		if v_etage_arene = 'N' then -- pas de perte en arène
			-- Un perso mortel meurt définitivement (même en cas de grosse différence de niveau)
			if v_perso_mortel = 'O' then
			  -- seulement si on n'est pas dans le cas d'une mort grave!!
        if v_gravité = 0 then
          -- Gestion de la perte des objets (1 == mort définitive)
          v_corps := tue_perso_perd_objets(v_cible, 1);
          -- On indique que le perso est Mort (il sera désactivé à la prochaine activation de DLT)
          update perso set perso_mortel = 'M' where perso_cod = v_cible;
        end if;
			else
				-- gravité nulle on applique la perte de matos
				if v_gravité = 0 then
					v_corps := 'À la suite de votre renvoi au dispensaire, vous venez de perdre les objets suivants : ';
					-- Gestion de la perte des objets (0 == mort standard)
					v_corps := v_corps || tue_perso_perd_objets(v_cible, 0);

					v_mes := nextval('seq_msg_cod');
					v_titre := 'Perte d’équipement';
					v_titre := substring(v_titre from 1 for 50);

					insert into messages (msg_cod, msg_date2, msg_date, msg_titre, msg_corps)
					values (v_mes, now(), now(), v_titre, v_corps);

					insert into messages_exp (emsg_msg_cod, emsg_perso_cod, emsg_archive)
					values (v_mes, v_cible, 'N');

					insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu, dmsg_archive)
					values (v_mes, v_cible, 'N', 'N');
				end if;
			end if;-- mort définitive / classique
		end if; -- end pas de perte en arène
	end if; -- end type perso

	/********************************/
	/* FIN  : CHUTE DES EQUIPEMENTS */
	/********************************/
	-- etape 6.1 en cas de forte gravité on execute automatiquement un sort de resurrection afin que la cible reste sur place
	-- etape 6.2 : la cible est tuée, on la replace
	en_prison := 0;
	if type_cible = 1 then
	-- MILICE
		if v_etage_arene = 'N' then -- pas de perte de familier en arène
			select into v_familier
				pfam_familier_cod
			from perso_familier inner join perso on perso_cod = pfam_familier_cod   -- Marlyza - 2018-05-16 --  il peut-y avoir plusieurs rattachement, on doit prendre l'actif!
			where pfam_perso_cod = v_cible and perso_actif = 'O';
			if found then
			    --  Marlyza - 2018-05-19 - ancien code avec le décès du familier
          --update perso set perso_type_perso = 2 where perso_cod = v_familier;
          --delete from perso_familier where pfam_perso_cod = v_cible;

		      -- Marlyza - 2018-05-02 -- Désormais le familier survi à la mort de son maitre (avec perte objets/px/impalpabilité)
          text_retour := text_retour || tue_perso_rappel_familier(v_cible, v_familier);
			end if;
		end if;
		if getparm_n(69) = 1 then
			if is_milice(v_attaquant) = 1 then
				select into mode_milice
					pguilde_mode_milice
				from guilde_perso
				where pguilde_perso_cod = v_attaquant;
				if mode_milice = 3 then
					en_prison := 1;
					texte_prison := '<p>Voux avez envoyé <b>' || nom_cible || '</b> en prison !<br>';
				end if;
				if mode_milice = 2 then
					select into type_peine, numero_peine
						peine_type, peine_cod
					from peine
					where peine_perso_cod = v_cible
						and peine_faite < 2;
					if found then
						update peine set peine_faite = 2, peine_dexec = now() where peine_cod = numero_peine;
						if type_peine != 0 then
							en_prison := 1;
							texte_prison := '<p>Vous avez envoyé <b>' || nom_cible || '</b> en prison !<br>';
						end if;
					end if;
				end if;
			end if;
		end if; -- MILICE
		if v_gravité != 2 then
			milice_texte := replace_mort(v_cible, v_attaquant, en_prison);
		end if;
	end if;

-- etape 6.3 : on le remet au tiers de ses PV
	nouveau_pv := cible_pv_max / 3;
	update perso
	set perso_pv = coalesce(nouveau_pv, 5), perso_tangible = 'N', perso_nb_tour_intangible = v_imp_P
	where perso_cod = v_cible;

	-- etape 6.3 bis : on enlève les locks
	delete from lock_combat where lock_attaquant = v_cible;
	delete from lock_combat where lock_cible = v_cible;
-- etape 6.4 : on balance le code retour

	/* calcul des px gagnés */
	/**********************/
	/* DEBUT PARTAGE AUTO */
	/**********************/
	texte_partage_px := '<p><b>Résumé du partage automatique de PX :</b><br>';
	px_perso_attaquant := 0;
	total_points := 0;
	-- debut calcul nb points
	nb_part := 0;

	--Debut Modif Blade 03/01/06
	-- Intégration du cas 5 avec sorts non pris en compte dans la répart karma, mais pris en compte dans le partage Pxs
	for ligne_px in
		select tact_libelle as libelle, sum(act_donnee) as donnee, act_perso1 as joueur from action, action_type
		where act_perso2 = v_cible and act_tact_cod = tact_cod
			and act_tact_cod in (1, 2)
		group by libelle, joueur

		union all

		select ta2.tact_libelle as libelle, sum(t2.act_donnee) as donnee, t2.act_perso1 as joueur from action t2, action_type ta2
		where t2.act_perso2 in
			(select t1.act_perso1 from action t1
				where t1.act_perso2 = v_cible and act_tact_cod in (1, 2))
			and t2.act_tact_cod = 3
			and t2.act_tact_cod = ta2.tact_cod
			--and t2.act_perso1 != t2.act_perso2
		group by libelle, joueur

		union all

		select ta3.tact_libelle as libelle, sum(t3.act_donnee) as donnee, t3.act_perso1 as joueur from action t3, action_type ta3
		where t3.act_perso2 in
			(select t1.act_perso1 from action t1
				where t1.act_perso2 = v_cible and act_tact_cod in (1, 2))
			and t3.act_tact_cod = 5
			and t3.act_tact_cod = ta3.tact_cod
			--and t3.act_perso1 != t3.act_perso2
		group by libelle, joueur

		union all

		select tact_libelle as libelle, round(sum(act_donnee)/2) as donnee, act_perso2 as joueur from action, action_type
		where act_perso1 = v_cible and act_tact_cod = tact_cod
			and act_tact_cod in (1, 2)
			and act_donnee >= 0
		group by libelle, joueur
	loop
		nb_part := nb_part + 1;
		total_points := total_points + ligne_px.donnee;
		if ligne_px.joueur  != v_attaquant then
			v_compteur_partage :=  2;
		end if;
	end loop;

	if total_points = 0 then
		total_points := 1;
	end if;

	-- fin calcul nb points
	-- debut partage
	-- Intégration du cas 5 avec sorts non pris en compte dans la répart karma, mais pris en compte dans le partage Pxs
	for ligne_px in
		-- attaques ou sorts offensifs
		select tact_libelle as libelle, sum(act_donnee) as donnee, act_perso1 as joueur, act_tact_cod as taction, 1 as vtype from action, action_type
		where act_perso2 = v_cible and act_tact_cod = tact_cod
			and act_tact_cod in (1, 2)
		group by libelle, joueur, taction, vtype

		union all

		-- sorts de soutien sur quelqu’un ayant fait une attaque ou sort offensif
		select ta2.tact_libelle as libelle, sum(t2.act_donnee) as donnee, t2.act_perso1 as joueur, act_tact_cod as taction, 2 as vtype  from action t2, action_type ta2
		where t2.act_perso2 in
			(select t1.act_perso1 from action t1
				where t1.act_perso2 = v_cible and act_tact_cod in (1, 2))
			and t2.act_tact_cod = 3
			and t2.act_tact_cod = ta2.tact_cod
			--and t2.act_perso1 != t2.act_perso2
		group by libelle, joueur, taction, vtype

		union all

		-- sorts de soutien hors karma sur quelqu’un ayant fait une attaque ou sort offensif
		select ta3.tact_libelle as libelle, sum(t3.act_donnee) as donnee, t3.act_perso1 as joueur, act_tact_cod as taction, 2 as vtype  from action t3, action_type ta3
		where t3.act_perso2 in
			(select t1.act_perso1 from action t1
				where t1.act_perso2 = v_cible and act_tact_cod in (1, 2))
			and t3.act_tact_cod = 5
			and t3.act_tact_cod = ta3.tact_cod
			--and t3.act_perso1 != t3.act_perso2
		group by libelle, joueur, taction, vtype

		union all

		-- attaque ou sorts offensifs subis de la part de la victime
		select 'Dégâts reçus' as libelle, round(sum(act_donnee)/2) as donnee, act_perso2 as joueur, act_tact_cod as taction, 3 as vtype  from action, action_type
		where act_perso1 = v_cible
			and act_tact_cod in (1, 2)
			and act_donnee >= 0
			and act_tact_cod = tact_cod
		group by libelle, joueur, taction, vtype
		order by joueur

	loop
		--
		-- debut du loop sur les persos pour le partage auto
		--
		cpl_txt_karma := '.';
		texte_karma := '';
		partage_joueur := ligne_px.joueur;
		if (partage_fait != partage_joueur) then
			partage_fait := 0;
		end if;

		select into nom_perso_px, n1, px1, type_attaquant
			perso_nom, perso_niveau, perso_px, perso_type_perso
		from perso where perso_cod = ligne_px.joueur;

		n1_r := niveau_relatif(px1);
		if n1_r > n1
			then n1 := n1_r;
		end if;
		select into n2, px2 perso_niveau, perso_px from perso where perso_cod = v_cible;
		n2_r := niveau_relatif(px2);
		if n2_r > n2
			then n2 := n2_r;
		end if;
		if type_cible = 1 then
			px_theo := 20 + 3*(n2 - n1) + n2;
		else
			px_theo := 10 + 2*(n2 - n1) + n2;
		end if;
		--
		-- on met les px à 0 s’ils sont en négatif en cas de monstre ou de légitime défense
		--
		if px_theo < 0 then
			if type_cible != 1 or is_riposte(v_cible, ligne_px.joueur) = 0 then
				px_theo := 0;
			else
				px_theo := px_theo * 0;
			end if;
		end if;
		--
		-- fin de mise à 0 des px
		--

		px_perso_n := ((ligne_px.donnee)/(total_points::numeric)) * px_theo;

		-- cas des arènes : on multiplie par 2 les pxs gagnés
		if v_etage_arene = 'O' and v_type_arene = 2 then
			px_perso_n := px_perso_n * 1;
		end if; -- fin coeff arène
		texte_partage_px := texte_partage_px || '<b>' || nom_perso_px || '</b> a gagne ' || trim(to_char(px_perso_n, '99999999990.99')) || ' PX pour l’action : <b>' || ligne_px.libelle || '</b><br>';
		-- ajout des px
		update perso
			set perso_px = perso_px + px_perso_n
			where perso_cod = ligne_px.joueur;

		if ligne_px.joueur = v_attaquant then
			px_perso_attaquant := px_perso_attaquant + px_perso_n;
		end if;
		total_px_gagnes := total_px_gagnes + px_perso_n;


		-- Récupération des monstres traîtres à la cause de Malkiar -- insertion des données
		if type_cible = 2 AND type_attaquant = 2 then
			insert into temp_traitres(traitres_perso_cod) values (ligne_px.joueur);
		end if;

		--
		-- renommee et renommee magique
		--
		modif_renommee := 0;
		modif_renommee_m := 0;
		if ligne_px.taction in (1, 4) then
			modif_renommee := n2/n1::numeric;
			modif_renommee := (modif_renommee*ligne_px.donnee)/(total_points::numeric);
			if modif_renommee > 2 then
				modif_renommee := 2;
			end if;
		elsif (ligne_px.taction = 2 and ligne_px.vtype = 3) then
			modif_renommee := n2/n1::numeric;
			modif_renommee := (modif_renommee*ligne_px.donnee)/(total_points::numeric);
			if modif_renommee > 2 then
				modif_renommee := 2;
			end if;
		else
			modif_renommee_m := n2/n1::numeric;
			modif_renommee_m := (modif_renommee_m*ligne_px.donnee)/(total_points::numeric);
			if modif_renommee_m > 2 then
				modif_renommee_m := 2;
			end if;
		end if;
		update perso set perso_renommee = perso_renommee + modif_renommee, perso_renommee_magie = perso_renommee_magie + modif_renommee_m where perso_cod = ligne_px.joueur;

		/*************************/
		/* DEBUT CALCUL DE KARMA */
		/*************************/
		-- pas de modif de karma dans les arènes
		if v_etage_arene = 'N' then
			select into kharma_attaquant, karma_perso_cod, v_perso_kharma_vampire
				perso_kharma, perso_cod, perso_niveau_vampire from perso
			where perso_cod = ligne_px.joueur;

			-- Intégration du cas 5 avec sorts non pris en compte dans la répart karma, mais pris en compte dans le partage Pxs
			if ligne_px.vtype != 3 and ligne_px.taction != 5 and partage_joueur != partage_fait then
				partage_fait := partage_joueur;

				if type_cible in (1, 3) then
					-- attaque joueur ou familier
					kharma_attaquant := round(kharma_attaquant, 4);
					kharma_cible := round(kharma_cible, 4);
					if (is_riposte(karma_perso_cod, v_cible) = 0) then
						if kharma_cible < 0 then
							variation_kharma := kharma_attaquant - 10;
						else
							if kharma_attaquant >= 0 then
								variation_kharma := (kharma_attaquant * 2 / 3::numeric) - 10;
							else
								if (kharma_attaquant <= ((kharma_cible+5)*2)) then
									variation_kharma := (kharma_attaquant * (1+abs(kharma_cible*0.2/kharma_attaquant::numeric))) - 2;
								else
									variation_kharma := kharma_attaquant;
								end if;
							end if;
						end if;
						-- on positionne la malédiction si celle-ci doit exister
/*							if malediction = 3 then
							titre_malediction := '[Maudit par ' || dieu_cible || ']';
							insert into perso_titre (ptitre_perso_cod, ptitre_titre, ptitre_type) values (ligne_px.joueur, titre_malediction, '3');
						end if;*/
					else
						texte_karma := ' (légitime défense)';
						variation_kharma := kharma_attaquant;
					end if;
					if v_cible = v_attaquant and ligne_px.taction = 3 and variation_kharma < kharma_attaquant then
						-- en cas de suicide, on ne pénalise pas ceux qui ont soutenu le meurtrier (qui est aussi la victime)
						-- (poison, disparition de familier périmé...)
						variation_kharma := kharma_attaquant;
						texte_karma := ' (mort naturelle)';
					end if;
				else
					-- attaque autre
					variation_kharma := kharma_attaquant + greatest(1/nb_part::numeric, 0.4);
					-- Correction Iuchi 28-05-2007: kharma des vampires
					if v_perso_kharma_vampire != 0 then
						if kharma_attaquant > 0 then
							variation_kharma := 0.0;
						else
							variation_kharma := kharma_attaquant;
						end if;
					end if;
					--  fin correction Iuchi
				end if;
			else
				variation_kharma := kharma_attaquant;
			end if; -- end if ligne_px.taction != 3


			if variation_kharma <> kharma_attaquant then
				update perso set perso_kharma = variation_kharma where perso_cod = karma_perso_cod;
			end if;
			variation_kharma := variation_kharma - kharma_attaquant;
			cpl_txt_karma := ', ' || trim(to_char(variation_kharma, '99999990.99')) || ' en karma' || texte_karma || '.';
		else-- else gestion arène
			cpl_txt_karma := ', 0.0 en karma (arène).';
		end if; -- fin de la non gestion de karma pour les arènes

		-- ajout des évènements
		-- ancienne ligne
		-- texte_evt := '[cible] a reçu ' || trim(to_char(px_perso_n, '99999999999D99')) || ' PX pour la mort de [attaquant] (action ' || ligne_px.libelle || ')';
		-- nouvelle ligne
		texte_evt := 'Pour la mort de [attaquant], [cible] a gagné ' || trim(to_char(px_perso_n, '99999999990.99')) || ' PX, ' || trim(to_char(modif_renommee, '99999999990.99')) || ' en renommée, ' || trim(to_char(modif_renommee_m, '99999999990D99')) || ' en renommée magique ' || cpl_txt_karma || ' - action ' || ligne_px.libelle;
		insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
			values(48, now(), 1, v_cible, texte_evt, 'O', 'O', v_cible, ligne_px.joueur);
		insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
			values(48, now(), 1, ligne_px.joueur, texte_evt, 'N', 'O', v_cible, ligne_px.joueur);

	--
	-- fin du loop
	--
	end loop;

-- on regarde si il y a des actions groupées à effacer
	for ligne_px in
		select act_numero as numero from action
		where act_perso2 = v_cible
			and act_tact_cod in (1, 2)
		union all
		select act_numero as numero from action t2
		where t2.act_perso2 in
			(select t1.act_perso1 from action t1
			where t1.act_perso2 = v_cible and act_tact_cod in (1, 2))
			and t2.act_tact_cod = 3
			and t2.act_perso1 != t2.act_perso2
		union all
		select act_numero as numero from action t3
		where t3.act_perso2 in
			(select t1.act_perso1 from action t1
			where t1.act_perso2 = v_cible and act_tact_cod in (1, 2))
			and t3.act_tact_cod = 5
			and t3.act_perso1 != t3.act_perso2
		union all
		select act_numero as numero from action
		where act_perso1 = v_cible and act_tact_cod = 4
	loop
		if ligne_px.numero != 0 then
			delete from action where act_numero = ligne_px.numero;
		end if;
	end loop;
	-- on efface les actions concernées
	delete from action
	where act_perso2 = v_cible
		and act_tact_cod in (1, 2);
	/*delete from action
		where act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = v_cible and act_tact_cod in (1, 2))
		and t2.act_tact_cod = 3
		and t2.act_perso1 != t2.act_perso2;*/
	delete from action
	where act_perso1 = v_cible and act_tact_cod = 4
		and act_donnee >= 0;

--Fin Modif Blade 03/01/06
	texte_partage_px := texte_partage_px || '<hr>';
	px_gagnes := px_perso_attaquant;

	/* evts pour mort */
	if v_attaquant = v_cible then
		texte_evt := '[attaquant] est mort en position x=' || trim(to_char(cible_x, '999')) || ', y=' || trim(to_char(cible_y, '999')) || ', etage=' || trim(to_char(cible_etage, '999')) || ', gagnant ' || trim(to_char(px_gagnes, '999999')) || ' PX.';
		insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
			values(nextval('seq_levt_cod'), 10, now(), 1, v_attaquant, texte_evt, 'O', 'O', v_attaquant, v_cible);
	else
		texte_evt := '[attaquant] a tué [cible] en position x=' || trim(to_char(cible_x, '999')) || ', y=' || trim(to_char(cible_y, '999')) || ', etage=' || trim(to_char(cible_etage, '999')) || ', gagnant ' || trim(to_char(px_gagnes, '999999')) || ' PX.';
		insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
			values(nextval('seq_levt_cod'), 10, now(), 1, v_attaquant, texte_evt, 'O', 'O', v_attaquant, v_cible);
		texte_evt := '[attaquant] a tué [cible] en position x=' || trim(to_char(cible_x, '999')) || ', y=' || trim(to_char(cible_y, '999')) || ', etage=' || trim(to_char(cible_etage, '999')) || '.';
		insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
			values(nextval('seq_levt_cod'), 10, now(), 1, v_cible, texte_evt, 'N', 'O', v_attaquant, v_cible);
	end if;
/* Modif kahlann 10/06/2015 : prise en compte du taux de perte d'xp de l'étage */
	px_perdus := round(total_px_gagnes * v_taux_xp / 300);
	nouveau_px := coalesce (px_cible - px_perdus, 0);

	if nouveau_px < 0 then
		nouveau_px := 0;
	end if;

	update perso
	set perso_px = nouveau_px
	where perso_cod = v_cible;

	texte_evt := '[cible] a perdu ' || trim(to_char(px_perdus, '9999999')) || ' px.';
	insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
		values(nextval('seq_levt_cod'), 10, now(), 1, v_cible, texte_evt, 'N', 'N', v_attaquant, v_cible);
	if v_etage_arene = 'O' then
		update perso set perso_nb_mort_arene = perso_nb_mort_arene + 1 where perso_cod = v_cible;
	else
		update perso set perso_nb_mort = perso_nb_mort + 1 where perso_cod = v_cible;
	end if;
	if v_cible != v_attaquant then
		if type_cible = 1 then
			if v_etage_arene = 'O' then
				update perso set perso_nb_joueur_tue_arene = perso_nb_joueur_tue_arene + 1 where perso_cod = v_attaquant;
			else
				update perso set perso_nb_joueur_tue = perso_nb_joueur_tue + 1 where perso_cod = v_attaquant;
			end if;
		else
			update perso set perso_nb_monstre_tue = perso_nb_monstre_tue + 1 where perso_cod = v_attaquant;
		end if;
	end if;
	if v_compteur_partage > 1 then
		texte_evt := ajout_tableau_chasse (v_attaquant, v_gmon_cod, 1, 0, v_cible);
	else
		texte_evt := ajout_tableau_chasse (v_attaquant, v_gmon_cod, 1, 1, v_cible);
	end if;

	code_retour := trim(to_char(px_gagnes, '99999')) || ';' || code_retour; -- pos 1
	/* reputation */
	delete from riposte where riposte_attaquant = v_cible;
	delete from concentrations where concentration_perso_cod = v_cible;
	delete from riposte where riposte_cible = v_cible;
	if v_perso_vampire != 0 then
		select into kharma_attaquant perso_kharma from perso
			where perso_cod = v_attaquant;
		if kharma_attaquant > 0 then
			update perso set perso_kharma = 0 where perso_cod = v_attaquant;
		end if;
	end if;
	/*On supprime les empoisonnements si il y en a */
	delete from bonus where bonus_perso_cod = v_cible and bonus_tbonus_libc = 'POI';
	delete from bonus where bonus_perso_cod = v_cible and bonus_tbonus_libc = 'VEN';
        /* Modif kahlann : suppression brulure et maladie du Sang */
        delete from bonus where bonus_perso_cod = v_cible and bonus_tbonus_libc = 'BRU';
        delete from bonus where bonus_perso_cod = v_cible and bonus_tbonus_libc = 'MDS';

	/* Suppression des transaction en cours, pour le perso ou son familier*/
	delete from transaction
		where tran_vendeur = v_familier;
	get diagnostics temp_int = row_count;
	delete from transaction
		where tran_vendeur = v_cible;
	get diagnostics nb_trans = row_count;
	if (nb_trans+temp_int) != 0 then
		texte := 'Les transactions en cours en tant que vendeur ont été annulées y compris pour votre familier !';
		insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
			values (nextval('seq_levt_cod'), 17, 'now()', 1, v_cible, texte, 'O', 'N');
	end if;
	delete from transaction
		where tran_acheteur = v_familier;
	get diagnostics temp_int = row_count;
	delete from transaction
		where tran_acheteur = v_cible;
	get diagnostics nb_trans = row_count;
	if (nb_trans+temp_int) != 0 then
		texte := 'Les transactions en cours en tant qu’acheteur ont été annulées, y compris pour votre familier !';
		insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
			values (nextval('seq_levt_cod'), 17, 'now()', 1, v_cible, texte, 'O', 'N');
	end if;

	/* actions */
	delete from action
		where act_perso1 = v_cible;
	delete from action
		where act_perso2 = v_cible;
	/* groupes */
	/* Gestion de la coterie : Message envoyé à la  coterie, on nomme un nouveau chef */
	select into coterie, chef, nom_groupe, v_envoie_message groupe_cod, groupe_chef, groupe_nom, pgroupe_message_mort
	from groupe, groupe_perso
	where pgroupe_perso_cod = v_cible
		and pgroupe_groupe_cod = groupe_cod
		and pgroupe_statut = 1;
	if found and v_etage_arene <> 'O' then
		v_corps := 'Le perso ' || nom_cible || ' vient d’être éloigné de votre coterie.<br>';
		/*On nomme un nouveau chef aléatoirement si le mort était chef*/
		if chef = v_cible then
			select into nouveau_chef, nom_nouveau_chef, v_perso_actif
				pgroupe_perso_cod, perso_nom, lancer_des(1, 1000) as num, perso_actif
			from groupe_perso, perso
			where pgroupe_groupe_cod = coterie
				and pgroupe_statut = 1
 and perso_cod != v_cible
				and pgroupe_perso_cod = perso_cod
				and perso_actif in ('O', 'H')
			order by perso_actif desc, num asc limit 1;
			if found then /* Si on trouve un autre membre actif, on le désigne comme chef*/
				update groupe set groupe_chef = nouveau_chef where groupe_cod = coterie;
				update groupe_perso set pgroupe_chef = 1 where pgroupe_groupe_cod = coterie and pgroupe_perso_cod = nouveau_chef;
				v_corps := v_corps || '<br>Un nouveau chef a été promu à la tête de la coterie ' || nom_groupe || ', il s’agit de ' || nom_nouveau_chef || '.';
			end if;
		end if;
		if v_envoie_message = 1 then
			v_mes := nextval('seq_msg_cod');
			v_titre := nom_cible || ' a été éloigné de la coterie';
			v_titre := substring(v_titre from 1 for 50);
			insert into messages (msg_cod, msg_date2, msg_date, msg_titre, msg_corps)
				values (v_mes, now(), now(), v_titre, v_corps);
			insert into messages_exp (emsg_msg_cod, emsg_perso_cod, emsg_archive)
				values (v_mes, v_cible, 'N');
			for ligne_groupe in select pgroupe_perso_cod, pgroupe_groupe_cod from groupe_perso
				where pgroupe_groupe_cod = coterie
					and pgroupe_messages = 1
					and pgroupe_statut = 1
			loop
				insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu, dmsg_archive)
					values (v_mes, ligne_groupe.pgroupe_perso_cod, 'N', 'N');
			end loop;
		end if;
		update groupe_perso set pgroupe_statut = 2, pgroupe_valeur_rappel = 0, pgroupe_chef = 0
		where pgroupe_perso_cod = v_cible AND pgroupe_groupe_cod = coterie;
	end if;

	-- Récupération des monstres traîtres à la cause de Malkiar -- mise à jour des monstres
	update perso
	set perso_monstre_attaque_monstre = coalesce(perso_monstre_attaque_monstre, 0) + 1
	where perso_cod in (select distinct traitres_perso_cod from temp_traitres);
	drop table temp_traitres;

	-- Gestion des défis
	select into v_defi * from defi where defi_statut = 1 and v_cible in (defi_lanceur_cod, defi_cible_cod);
	if found then
		update defi set
			defi_statut = 3,
			defi_vainqueur = case v_cible when defi_lanceur_cod then 'C'
			                              when defi_cible_cod then 'L' end,
			defi_date_fin = now()
		where defi_cod = v_defi.defi_cod;
		perform defi_reinitialise_persos(v_defi.defi_cod);

		-- Libération de la zone de combat
		update defi_zone set zone_libre = 'O' where zone_cod = v_defi.defi_zone_cod;

		-- Événement
		texte := '[attaquant] a remporté son défi contre [cible].';
		perform insere_evenement(v_attaquant, v_cible, 97, texte, 'O', '[defi_cod]=' || v_defi.defi_cod::text);
	end if;

	code_retour := code_retour || texte_partage_px || ';' || texte_prison || ';' || text_retour || ';' ;
	return code_retour;
end;$_$;


ALTER FUNCTION public.tue_perso_final(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION tue_perso_final(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION tue_perso_final(integer, integer) IS 'Fonction gérant l’ensemble des actions à faire pour la mort d’un personnage ou monstre.';
