--
-- Name: magie_commun(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION magie_commun(integer, integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function magie_commun : part commune à tous les sorts         */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*       -1 = EA                                                 */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/*        2 = réceptacle                                         */
/*        3 = magie divince                                      */
/*        4 = parcho                                             */
/*        5 = objet                                              */
/*   $4 = numéro du sort lancé                                   */
/* Le code sortie est une chaine séparée par ;                   */
/*  1 = sort réussi ?                                            */
/*      0 = non                                                  */
/*      1 = oui                                                  */
/*  2 = sort résisté ?                                           */
/*      0 = pas de résistance ou N/A                             */
/*      1 = résistance                                           */
/*  3 = chaine html de sortie                                    */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*   05/10/2004 : rajout des modificateurs de niveau             */
/*                changement du cout en PA si raté               */
/*   14/04/2005 : nouveau système de PX                          */
/*   21/03/2011 : Bleda: Zone de droit                           */
/*   30/05/2012 : Reivax: lieu protégé                           */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;		-- chaine html de sortie
	texte_evt text;			-- texte pour évènements
	texte_memo text;		-- texte pour mémorisation
-------------------------------------------------------------
-- variables concernant le lanceur
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
	v_comp integer;			-- valeur de compétence initiale
	v_comp_modifie integer;		-- valeur de compétence modifiée
	v_comp_cod integer;		-- comp_cod utilisée
	nom_comp text;			-- nom de la compétence utilisée
	px_gagne numeric;		-- px gagnes pour ce sort
	temp_renommee numeric;		-- calcul pour renommee
	v_malus_niveau integer;		-- malus lié au niveau
	nb_sort_niveau integer; 	-- nombre de sorts du même niveau déjà lancés
	bonus_pa integer;
	v_chances_runes integer;	-- chances de conserver ses runes
	v_bonus_magie integer;		-- Bonus ou malus au lancement de sort fourni par les potions
	v_perso_int integer;		-- int du lanceur pour impact sur le bloque magie
	v_reussite integer;		-- reussite ou non dans le cadre de la distorsion
	v_voie_magique integer;		-- pour tester les sorts reservés
	bonmal integer;			-- bonus malus au lancé de des
	malus integer;
	type_attaquant integer;		-- Aventurier ou monstre (ou familier)
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;		-- perso_cod de la cible
	pos_cible integer;		-- position de la cible
	nom_cible perso.perso_nom%type;	-- nom de la cible
	v_pos_protegee character;	-- la protection 'O' ou 'N' de la position ciblée
	type_cible integer;		-- Aventurier ou monstre (ou familier)
	v_bloque_magie integer;		-- variable pour savoir si on bloque
	v_bonus_dlt numeric;		-- bonus distorsion temporelle
	v_malus_dlt numeric;		-- malus distorsion temporelle
	v_pos_pvp character;		-- Si la cible est en zone de droit
	v_gmon_cod integer;		-- Type de monstre ciblé
	v_immunise character;		-- Si la cible est immunisé à ce sort
	v_resistance character;		-- Si la cible a une resitance a ce sort à ce sort
	v_resiste character;		-- resultat sur la resitance du monstre generique
	v_immunise_valeur numeric;	-- Le taux d’immunité de la cible
	v_immunise_rune character;	-- Si l’immunité vient du fait que le sort soit lancé sans runes
	v_immunise_texte varchar(500);	-- Texte relatif à l’immunité
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort alias for $4;		-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;		-- Cout en PA du sort
	nom_sort varchar(50);		-- nom du sort
	niveau_sort integer;		-- niveau du sort
	aggressif varchar(2);		-- sort bloquable ?
	offensif varchar(2);		-- sort offensif ?
	temp integer;			-- fourre tout
	soi_meme text;			-- Détermine si on peut lancer le sort sur soi
	sur_perso text;			-- Détermine si on peut lancer le sort sur un autre perso
	sur_monstre text;		-- Détermine si on peut lancer le sort sur un monstre
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	deb_res_controle text;		-- partie 1 du controle sort
	res_controle text;		-- totalité du contrôle sort
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;		-- record des rune à dropper
	temp_ameliore_competence text;	-- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;			-- lancer de dés
	compt integer;			-- fourre tout
	niveau_religion integer;
	facteur_reussite integer;
	facteur_reussite_pur integer;
	facteur_malchance numeric ;  -- facteur de malchance sur certains objets magiques
	v_special integer;
	resultat text;			-- recuperation du code de la fonction cout_pa
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
	-- sur le lanceur
	select into v_voie_magique, type_attaquant, v_perso_int perso_voie_magique, perso_type_perso, perso_int
	from perso
	where perso_cod = lanceur;

	-- sur le sort
	select into nom_sort, cout_pa, aggressif, offensif, niveau_sort, soi_meme, sur_perso, sur_monstre, v_comp_cod
		sort_nom, sort_cout, sort_bloquable, sort_aggressif, sort_niveau, sort_soi_meme, sort_monstre, sort_joueur, sort_comp_cod
	from sorts where sort_cod = num_sort;
	if not found then
		code_retour := code_retour||'0;<p>Erreur : sort non trouvé !</p>';
		return code_retour;
	end if;
	if soi_meme = 'O' and sur_perso = 'N' and sur_monstre = 'N' and lanceur != cible then
		code_retour := code_retour||'0;<p>Erreur : ce sort ne peut être lancé que sur soi-même !</p>';
		return code_retour;
	end if;


-------------------------------------------------------------
-- Etape 2bis : vérification des pre-requis spécifique sur certaine map (comme la course
-------------------------------------------------------------
    -- on va refuser de faire se sort sur une course de monture (etage avec etage_mort_speciale=1)
    select etage_mort_speciale into temp from perso_position join positions on pos_cod=ppos_pos_cod join etage on etage_numero=pos_etage where ppos_perso_cod=cible;
    if temp = 1 and num_sort=39 then
        return  '0;<p>l''usage de la Défense magique n''est pas autorisé pendant les courses de monture!!</p>';
    elsif temp = 1 and num_sort=146 then
        return  '0;<p>l''usage de la Distorsion temporelle n''est plus autorisée pendant les courses de monture!!</p>';
    end if;

	-- sort distortion temporelle
	if num_sort = 146 then
		if valeur_bonus_hors_equip(lanceur, 'DIT') > 0 or
				valeur_bonus_hors_equip(lanceur, 'DIS') >0 or
				valeur_bonus_hors_equip(cible, 'DIT') >0 or
				valeur_bonus_hors_equip(cible, 'DIS') >0 then
			code_retour := code_retour||'0;<p>Erreur : vous ne pouvez pas lancer ce sort pour cause de distorsion temporelle lente ou rapide affectant déjà votre cible ou vous même.</p>';
			return code_retour;
		end if;
	end if;
	temp_renommee := ((niveau_sort - 1) * 0.1)::numeric;
	bonus_pa := valeur_bonus(lanceur, 'PAM');

	-- sur la position du lanceur
	select into v_pos_protegee
		coalesce(lieu_refuge, 'N')
	from perso_position
	left outer join lieu_position ON lpos_pos_cod = ppos_pos_cod
	left outer join lieu ON lieu_cod = lpos_lieu_cod
	where ppos_perso_cod = lanceur;
	if v_pos_protegee = 'O' then
		code_retour := '0;<p>Erreur ! Vous êtes sur un lieu refuge et ne pouvez donc pas lancer de sorts.</p>';
		return code_retour;
	end if;

	-- sur la cible + zone de droit
	select into
		nom_cible, pos_cible, type_cible, v_pos_pvp, v_gmon_cod, v_pos_protegee
		perso_nom, ppos_pos_cod, perso_type_perso, pos_pvp, perso_gmon_cod, coalesce(lieu_refuge, 'N')
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod
	inner join positions on pos_cod = ppos_pos_cod
	left outer join lieu_position ON lpos_pos_cod = pos_cod
	left outer join lieu ON lieu_cod = lpos_lieu_cod
	where perso_cod = cible
		and perso_actif = 'O';	-- Bleda 27/2/11 On ne cible pas les morts !
	if not found then
		code_retour := code_retour||'0;<p>Erreur : cible non trouvée !</p>';
		return code_retour;
	end if;
	if type_attaquant != 2 and type_cible != 2 and v_pos_pvp = 'N' and offensif = 'O' then
		code_retour := '0;<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de lui lancer un sort offensif car elle n’est pas une engeance de Malkiar !<br />La zone de droit couvre tout l’Ouest de l’étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n’importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)</p>';
		return code_retour;
	elsif type_attaquant != 2 and type_cible = 2 and v_pos_pvp = 'N' and offensif = 'N' then
		code_retour := '0;<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de lui lancer un sort de soutien car elle est une engeance de Malkiar !<br />La zone de droit couvre tout l’Ouest de l’étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n’importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)</p>';
		return code_retour;
	end if;
	if v_pos_protegee = 'O' and offensif = 'O' then
		code_retour := '0;<p>Erreur ! Cette cible est sur un lieu protégé ! Elle ne peut pas être la cible d’un sort offensif.</p>';
		return code_retour;
	end if;

	-- sur la compétence
	select into
		v_comp, nom_comp
		pcomp_modificateur, comp_libelle
	from perso_competences
	inner join competences on comp_cod = pcomp_pcomp_cod
	where pcomp_perso_cod = lanceur and pcomp_pcomp_cod = v_comp_cod;
	if not found and type_lancer != -1 then
		code_retour := code_retour||'0;<p>Erreur : infos compétence non trouvées !</p>';
		return code_retour;
	end if;

	-- contrôles de lancement
	res_controle = controle_sort(num_sort, lanceur, cible, type_lancer);
	deb_res_controle := substr(res_controle,1,1);
	if deb_res_controle != '0' then
		code_retour := code_retour||'0;<p>'||res_controle;
		return code_retour;
	end if;
	if type_lancer in (2,4,5) then
		facteur_reussite := to_number(split_part(res_controle,';',2),'9999999999999');
		facteur_reussite_pur:= facteur_reussite;
	end if;

------------------------------------------------------------
-- les controles semblent bons, on peut passer à la suite
------------------------------------------------------------
	code_retour := code_retour||'<p>Vous avez lancé le sort <b>'||nom_sort||'</b>, sur la cible <b>'||nom_cible||'</b>, ';
	if type_lancer = 2 then
		code_retour := code_retour||'en utilisant un réceptacle.<br><br>';
	elsif type_lancer = 4 then
		code_retour := code_retour||'en utilisant un parchemin.<br><br>';
	elsif type_lancer = 5 then
		code_retour := code_retour||'en utilisant un objet.<br><br>';
	elsif type_lancer = -1 then
		code_retour := code_retour||'à l''aide d''un effet-automatique.<br><br>';
	else
		code_retour := code_retour||'en utilisant la compétence <b>'||nom_comp||'</b>.<br><br>';
	end if;

-- on ajoute la magie à la position
	update positions
	set pos_magie = pos_magie + ((niveau_sort - 1) * 10)
	where pos_cod = pos_cible
		and pos_cod <> 152794;	-- == -6 / -7 dans la Halle Merveilleuse. C’est temporaire, pour le marché de Léno 2013 ;

-- on rajoute le lancement du sort dans le total
	select into niveau_religion dper_niveau
	from dieu_perso
		where dper_perso_cod = lanceur;
	if not found then
		niveau_religion := 0;
	end if;

-- on ajoute dans le total si pas parchemin ni objet
	if type_lancer not in (-1,4,5) then
		select into compt pnbst_cod from perso_nb_sorts_total
		where pnbst_perso_cod = lanceur
			and pnbst_sort_cod = num_sort;
		if not found then
			insert into perso_nb_sorts_total (pnbst_perso_cod,pnbst_sort_cod,pnbst_nombre)
			values (lanceur,num_sort,0);
		end if;
	end if;
	if type_lancer not in (-1,2,4,5) then
		if niveau_religion < 2 then
			update perso_nb_sorts_total
			set pnbst_nombre = pnbst_nombre + 1
			where pnbst_sort_cod = num_sort
				and pnbst_perso_cod = lanceur;
		end if;
	end if;

	if type_lancer = -1 then

	    -- cas des EA, le sort est toujours réussi (le test de proba de declencehement de l'EA est fait en amont)
	    facteur_reussite:=100;

  else

	    -- traitement que l'on applique pas aux sorts lancés par les EA: cout en pa, gains de px, chance de réussite, etc.....

      update perso_nb_sorts_total
      set pnbst_date_dernier_lancer = now()
      where pnbst_sort_cod = num_sort
        and pnbst_perso_cod = lanceur;

      -- appel de la fonction cout_pa_magie pour les calculs de cout de pa avec correlation pour l’affichage dans la page magie_php
      select into resultat cout_pa_magie(lanceur,num_sort,type_lancer);
      cout_pa := resultat;

      -- pour les sorts lancés à partir d'objet on met a jour le compteur (et on supprime le sort préparé)
      facteur_malchance :=0 ;
      if type_lancer = 5 then
        select into facteur_malchance objsort_malchance from objets_sorts join objets_sorts_magie on objsortm_objsort_cod=objsort_cod where objsortm_perso_cod = lanceur ;
        update objets_sorts set objsort_nb_utilisation=objsort_nb_utilisation+1 from objets_sorts_magie where objsortm_perso_cod = lanceur  and objsortm_objsort_cod=objsort_cod;
        --On fera le ménage en front, on a besoin de connaitre l'objet utilisé pour les option de "relancer"
        --delete from objets_sorts_magie where objsortm_perso_cod = lanceur ;
      end if;

      -- on regarde s il y a concentration
      if type_lancer not in (2,4,5) then
        select into compt concentration_perso_cod from concentrations
          where concentration_perso_cod = lanceur;
        if found then
          v_comp_modifie := v_comp + 20;
          delete from concentrations where concentration_perso_cod = lanceur;
        else
          v_comp_modifie = v_comp;
        end if;
      end if;

      --
      -- modificateurs en fonction du niveau
      --
      if type_lancer = 0 then
        v_malus_niveau := (2 - niveau_sort) * 10;

        select into nb_sort_niveau
          coalesce(sum(pnbst_nombre),0)
        from perso_nb_sorts_total,sorts
        where pnbst_perso_cod = lanceur
          and pnbst_sort_cod = sort_cod
          and sort_niveau = niveau_sort;

        if nb_sort_niveau is null then
          nb_sort_niveau := 0;
        end if;
        v_malus_niveau := v_malus_niveau + floor(nb_sort_niveau/15);
        if v_malus_niveau > 0 then
          v_malus_niveau := 0;
        end if;
        v_comp_modifie := v_comp_modifie + v_malus_niveau;
      end if;

      --On rajoute les bonus ou malus de lancer impactés par les potions
      v_comp_modifie := v_comp_modifie + valeur_bonus(lanceur, 'PMA');
      --
      -- fin modificateurs en fonction du niveau
      --
      if v_comp_modifie < 1 then
        v_comp_modifie := 1;
      end if;
      if type_lancer not in (2,4,5) then
        code_retour := code_retour||'Votre chance de réussir (en tenant compte des modificateurs) est de <b>'||trim(to_char(v_comp_modifie,'9999'))||'</b> ';
        -- on regarde si il y a un bonus pour avoir plus de chances de conserver ses runes
        v_chances_runes := 0;
        v_chances_runes := valeur_bonus(lanceur, 'PER');
        -- on regarde si le sort est lancé
        v_special := floor(v_comp_modifie/5);
        -- etape  on regarde si la cible est bénie ou maudite
        bonmal := valeur_bonus(lanceur, 'BEN') + valeur_bonus(lanceur, 'MAU');
        if bonmal <> 0 then
          des := lancer_des3(1,100,bonmal);
        else
          des := lancer_des(1,100);
        end if;
        code_retour := code_retour||'et votre lancer de dés est de <b>'||trim(to_char(des,'9999'))||'</b>.<br>';
        if des > 96 then
        -- echec critique
          if type_lancer = 0 then
          -- on enlève les runes
            for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
              compt := drop_rune(ligne_rune.srune_gobj_cod,lanceur);
            end loop;
          end if;
          texte_evt := '[attaquant] a tenté de lancer '||nom_sort||' sur [cible] et a échoué.';

          insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
          values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);

          if (lanceur != cible) then
            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
            values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
          end if;
          code_retour := code_retour||'Il s’agit donc d’un échec automatique.<br><br>';
          code_retour := '0;'||code_retour;

          -- 2024-09-10 Marlyza: bugfix: prise en compte des malus au PA et aussi des bonus supérieurs à 4 => perte entre 1 et 4 PA sur echec critique
          update perso set perso_renommee_magie = perso_renommee_magie - (temp_renommee*2),perso_pa = perso_pa - LEAST(4, GREATEST(1, (4 + bonus_pa))) where perso_cod = lanceur;
          return code_retour;
          -- renomme magique
        end if;
        if des > v_comp_modifie then
          -- sort loupé

          -- renomme magique / 2024-09-10 Marlyza: bugfix: prise en compte des malus au PA et aussi des bonus supérieurs à 4 => perte entre 1 et 4 PA sur echec)
          update perso set perso_renommee_magie = perso_renommee_magie - temp_renommee,perso_pa = perso_pa - LEAST(4, GREATEST(1, (4 + bonus_pa))) where perso_cod = lanceur;

          code_retour := code_retour||'Vous avez donc <b>échoué</b>.<br><br>';
          -- on regarde si on améliore la comp
          if v_comp <= getparm_n(1) then
            code_retour := code_retour||'Votre compétence est inférieure à '||trim(to_char(getparm_n(1),'9999'))||' %. Vous tentez une amélioration.<br>';
            temp_ameliore_competence := ameliore_competence_px(lanceur,v_comp_cod,v_comp);
            code_retour := code_retour||'Votre lancer de dés est de <b>'||split_part(temp_ameliore_competence,';',1)||'</b>, ';
            if split_part(temp_ameliore_competence,';',2) = '1' then
              code_retour := code_retour||'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>'||split_part(temp_ameliore_competence,';',3)||'</b><br><br>.';
            else
              code_retour := code_retour||'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
            end if;
          end if;
          if type_lancer = 0 then
            for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
              if lancer_des(1,100+v_chances_runes) <= getparm_n(33) then
                compt := drop_rune(ligne_rune.srune_gobj_cod,lanceur);
              end if;
            end loop;
          end if;
          texte_evt := '[attaquant] a tenté de lancer '||nom_sort||' sur [cible] et a échoué.';

          insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
          values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);

            if (lanceur != cible) then
            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
          end if;
          code_retour := '0;'||code_retour;
          return code_retour;
        end if;
        if des <= 5 then
          code_retour := code_retour||'il s’agit donc d’une <b>réussite critique</b>.<br><br>';
          px_gagne := px_gagne + 1;
          cout_pa := floor(cout_pa/2);
        else
          if des <= v_special then
            code_retour := code_retour||'il s’agit donc d’une <b>réussite spéciale</b>.<br><br>';
            cout_pa := GREATEST(0, cout_pa - 1);
          else
            code_retour := code_retour||'Vous avez donc <b>réussi</b>.<br><br>';
          end if;
        end if;
        facteur_reussite := v_comp_modifie - des;
        facteur_reussite_pur := v_comp_modifie - des;
        -- a partir d ici on est sur que le sort est porté.

        -- renomme magique
        update perso set perso_renommee_magie = perso_renommee_magie + temp_renommee where perso_cod = lanceur;

        -- px
        if (type_lancer = 0) then
          px_gagne := px_gagne + niveau_sort - 1;
        else
          px_gagne := px_gagne + ((niveau_sort - 1)/3.0::numeric);
        end if;
        -- on tente l amélioration
        temp_ameliore_competence := ameliore_competence_px(lanceur,v_comp_cod,v_comp);
        code_retour := code_retour||'Votre jet d’amélioration est de <b>'||split_part(temp_ameliore_competence,';',1)||'</b>, ';
        if split_part(temp_ameliore_competence,';',2) = '1' then
          code_retour := code_retour||'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>'||split_part(temp_ameliore_competence,';',3)||'</b>.<br><br>';
        else
          code_retour := code_retour||'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
        end if;
        -- on supprime les runes si besoin est
        if type_lancer = 0 then
          for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
            compt := drop_rune(ligne_rune.srune_gobj_cod,lanceur);
          end loop;
        end if;

        -- on attribue les PX
        update perso set perso_px = perso_px + px_gagne where perso_cod = lanceur;

        -- on regarde pour la mémorisation
        -- ajout azaghal on exclue le sort de résurection et résurection
        if num_sort = 161 or num_sort = 175 then
          code_retour := code_retour||'<p>Jusqu’à ce jour, nul n’a jamais trouvé le moyen de mémoriser ce sortilège. Vous même vous êtes décontenancé par la nature du sort et vous ne voyez pas comment faire</b>.</p>';
        else
          texte_memo := memo_sort(lanceur,num_sort);
          if split_part(texte_memo,';',1) = '-1' then
            code_retour := code_retour||'Vous ne pouvez pas mémoriser ce sort car vous avez atteint votre limite de mémorisation.<br>';
          end if;
          if split_part(texte_memo,';',1) = '-2' then
            code_retour := code_retour||'Un familier mineur ne peut pas mémoriser de sorts de niveau 3 ou plus.<br>';
          end if;
          if split_part(texte_memo,';',1) = '1' then
            code_retour := code_retour||'Vous tentez de mémoriser le sort. Votre probabilité de mémorisation est de <b>'||split_part(texte_memo,';',2)||'</b>. ';
            code_retour := code_retour||'Votre lancer des dés est de <b>'||split_part(texte_memo,';',3)||'</b>.<br>';

            if split_part(texte_memo,';',4) = '1' then
              code_retour := code_retour||'Vous avez donc <b>mémorisé</b> ce sort.<br><br>';
              px_gagne := px_gagne + 1;
            else
              code_retour := code_retour||'Vous n’avez pas réussi à mémoriser ce sort.<br><br>';
            end if;
          end if;
        end if;
      end if; -- fin réceptacle, parcho, objet

      -- Il y a certains objets qui possède un facteur de malchance, faisant échoué le lancement du sort
      if type_lancer = 5 and facteur_malchance >0 then
          des := 100 * lancer_des(1,100);   -- facteur_malchance a une précision à 0.01 %
          if des <= 100 * facteur_malchance then
            code_retour := code_retour||'Vous n''avez pas réussi à utiliser l''objet, le sortilège à <b>échoué</b>.<br><br>';

            texte_evt := '[attaquant] a tenté de lancer '||nom_sort||' sur [cible] et a échoué.';

            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
            values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);

            if (lanceur != cible) then
              insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                  values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
            end if;

            update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
            code_retour := '0;'||code_retour;
            return code_retour;
          end if;
      end if;

  end if;   -- fin traitement des non-EA

	-- immunité des monstres à certains sorts
	v_resiste := 'I' ;  -- par default pas de resitance I=ignore test de ressitance, N=raté le teste de resistance O=reussi le test de resistance
	if type_cible = 2 then
		select into v_immunise, v_resistance, v_immunise_valeur, v_immunise_rune
			case when random() < immun_valeur then 'O' else 'N' end,
			case when immun_resistance = 0 then 'I' when random() < abs(immun_resistance) then (case when immun_resistance>0 then 'O' else 'N' end) else (case when immun_resistance>0 then 'N' else 'O' end) end,
			immun_valeur, immun_runes
		from monstre_generique_immunite
		where immun_gmon_cod = v_gmon_cod
			and immun_sort_cod = num_sort;

      if found then
          -- Vérifier si Le monstre est immunisé.
          if v_immunise = 'O' and  (v_immunise_rune = 'O' or type_lancer <> 0) then
              if v_immunise_rune = 'O' and v_immunise_valeur = 1 then
                v_immunise_texte := 'Votre adversaire <b>est totalement immunisé</b> à ce sort.<br><br>';
              end if;
              if v_immunise_rune = 'O' and v_immunise_valeur < 1 then
                v_immunise_texte := 'L’<b>immunité partielle</b> de votre adversaire à ce sort lui permet de s’en tirer sans dommage.<br><br>';
              end if;
              if v_immunise_rune = 'N' and v_immunise_valeur = 1 and type_lancer <> 0 then
                v_immunise_texte := 'Votre adversaire <b>est totalement immunisé</b> à ce sort. Peut-être devriez-vous essayer de composer ce sort avec des runes ?<br><br>';
              end if;
              if v_immunise_rune = 'N' and v_immunise_valeur < 1 and type_lancer <> 0 then
                v_immunise_texte := 'L’<b>immunité partielle</b> de votre adversaire à ce sort lui permet de s’en tirer sans dommage. Peut-être devriez-vous essayer de composer ce sort avec des runes ?<br><br>';
              end if;
              code_retour := code_retour || v_immunise_texte;
              code_retour := code_retour||'Vous gagnez '||trim(to_char(px_gagne,'999'))||' PX pour cette action.<br>';
              texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] qui y est immunisé.';

              insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
              values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);

              if (lanceur != cible) then
                insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
              end if;
              code_retour := '0;'||code_retour;

              if type_lancer != -1 then   -- sauf EA
                  update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
              end if;
              return code_retour;

          -- Sinon vérifier si Le monstre est resistant.
          elsif v_resistance <> 'I' and  (v_immunise_rune = 'O' or type_lancer <> 0) then
     	        v_resiste := v_resistance ;  -- on force le jet de resistance réussi ou raté !
          end if;
      end if;
	end if;

	-- La cible est sous défense magique ?
	if valeur_bonus(cible, 'DFM') != 0 then
		code_retour := '0;'||code_retour||'Votre sort est rejeté car la cible est sous le coup d’une protection magique.<br />';
    if type_lancer != -1 then   -- sauf EA
		    update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
    end if;
		texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] qui est protégé par un Défense magique.';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
				values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
		if (lanceur != cible) then
			insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
			values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
		end if;
		return code_retour;
	end if;

	-- on regarde si bloque magie
	if aggressif = 'O' AND NOT (v_voie_magique = 3 AND num_sort=145) AND NOT (v_voie_magique = 4 AND num_sort=176) AND NOT (lanceur = cible) AND NOT (num_sort=30 AND f_perso_affinite(lanceur, cible) IN ('T', 'C'))  then
		select into v_bloque_magie
			pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = cible
			and pcomp_pcomp_cod = 27;
		if found then
			facteur_reussite := facteur_reussite + (2*v_perso_int);
			v_bloque_magie := bloque_magie(cible,niveau_sort,facteur_reussite);
			if v_bloque_magie != 0 then
				code_retour := code_retour||'Votre adversaire <b>bloque</b> le sort.<br><br>';
				code_retour := code_retour||'Vous gagnez '||trim(to_char(px_gagne,'999'))||' PX pour cette action.<br>';
				texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] qui a bloqué le sort.';
				insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
						values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
				if (lanceur != cible) then
					insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
							values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
				end if;
				code_retour := '0;'||code_retour;

        if type_lancer != -1 then   -- sauf EA
				    update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
        end if;
				return code_retour;
			end if;
		end if;
                -- ajout azaghal on utilise la nouvelle resistance magique
		v_bloque_magie := resiste_magie(cible,lanceur,niveau_sort,facteur_reussite_pur);
	else
		if (v_voie_magique = 3 AND num_sort=145) then
			code_retour := code_retour||'Toute résistance est futile face à la malédiction d’un véritable sorcier.<br><br>';
		elseif (v_voie_magique = 4 AND num_sort=176) then
			code_retour := code_retour||'Toute résistance est futile face au takatoukité d''un mage de guerre.<br><br>';
		end if;
		v_bloque_magie := 0;
	end if;

	if (v_bloque_magie = 0 and v_resiste = 'I') or (v_resiste = 'N') then
------------------------
-- magie non résistée --
------------------------
		code_retour := '1;0;'||code_retour;
	else
--------------------
-- magie résistée --
--------------------
		code_retour := '1;1;'||code_retour;
	end if;

  if type_lancer != -1 then   -- sauf EA

      -- on enlève les bonus existants
      update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
      if split_part(temp_ameliore_competence,';',2) = '1' then
        px_gagne := px_gagne + 1;
      end if;
  end if;

--
-------------------------
-- les EA liés au lancement d'un sort (avec protagoniste null)
---------------------------
  code_retour := code_retour || execute_fonctions(lanceur, null, 'MAL', json_build_object('num_sort', num_sort) );

-- ---------------------------
	code_retour := code_retour||';'||trim(to_char(px_gagne,'999999990.99'))||';'||trim(to_char(facteur_reussite,'99999999999'));
	return code_retour;
end;$_$;


ALTER FUNCTION public.magie_commun(integer, integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION magie_commun(integer, integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION magie_commun(integer, integer, integer, integer) IS 'Gère les parties communes à la magie (jets de compétence, quelques vérifications d’usage, runes...)';
