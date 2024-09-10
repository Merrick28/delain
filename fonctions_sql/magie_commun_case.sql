--
-- Name: magie_commun_case(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION magie_commun_case(integer, integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function magie_commun_case : commun à tous les sorts case     */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = pos_cod                                                */
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
/*   08/09/2003 : ajout d’un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*   07/12/2006 : intégration bonus PA en cas de ratage de sort  */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;         -- chaine html de sortie
	texte_evt text;           -- texte pour évènements
	texte_memo text;          -- texte pour mémorisation
-------------------------------------------------------------
-- variables concernant le lanceur
-------------------------------------------------------------
	lanceur alias for $1;     -- perso_cod du lanceur
	lanceur_pa integer;       -- pa du lanceur
	pos_lanceur integer;      -- position du lanceur
	v_comp integer;           -- valeur de compétence initiale
	v_comp_modifie integer;   -- valeur de compétence modifiée
	v_comp_cod integer;       -- comp_cod utilisée
	nom_comp text;            -- nom de la compétence utilisée
	px_gagne numeric;         -- px gagnes pour ce sort
	temp_renommee numeric;    -- calcul pour renommee
	pa_magie integer;         -- bonus en cout de lancer de sort
	v_malus_niveau integer;   -- malus lié au niveau
	v_bonus_malus integer;    -- limiteur au malus de niveau
	nb_sort_niveau integer;   -- nombre de sorts du même niveau déjà lancés
	bonus_pa integer;
	v_bonus_runes integer;    --chances de conserver ses runes
	v_chances_runes integer;
	v_bonus_magie integer;    --Bonus ou malus pour lancer un sort, fourni par les potions
	bonmal integer;           -- bonus malus lié au lancé de dé
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	v_pos alias for $2;       -- perso_cod de la cible
	v_bloque_magie integer;   -- variable pour savoir si on bloque
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort alias for $4;    -- numéro du sort à lancer
	type_lancer alias for $3; -- type de lancer (memo ou rune)
	cout_pa integer;          -- Cout en PA du sort
	distance_sort integer;    -- portée du sort
	nom_sort varchar(50);     -- nom du sort
	niveau_sort integer;      -- niveau du sort
	aggressif varchar(2);     -- sort aggressif ?
	temp integer;             -- fourre tout
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	deb_res_controle text;    -- partie 1 du controle sort
	res_controle text;        -- totalité du contrôle sort
	distance_cibles integer;  -- distance entre lanceur et cible
	ligne_rune record;        -- record des rune à dropper
	temp_ameliore_competence text;  -- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;              -- lancer de dés
	compt integer;            -- fourre tout
	niveau_religion integer;
	facteur_reussite integer;
	facteur_malchance numeric ;  -- facteur de malchance sur certains objets magiques
	v_special integer;
	resultat text;

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
-- sur le sort
	select into nom_sort, cout_pa, aggressif, niveau_sort
		sort_nom, sort_cout, sort_bloquable, sort_niveau
	from sorts
	where sort_cod = num_sort;
	if not found then
		code_retour := code_retour || '0;<p>Erreur : sort non trouvé !';
		return code_retour;
	end if;
	temp_renommee := ((niveau_sort-1)*0.1)::numeric;
	bonus_pa := valeur_bonus(lanceur, 'PAM');
-- sur la cible
	select into temp pos_cod from positions
	where pos_cod = v_pos;
	if not found then
		code_retour := code_retour || '0;<p>Erreur : position cible non trouvée !';
		return code_retour;
	end if;
-- sur la compétence
	select into v_comp, v_comp_cod, nom_comp
		pcomp_modificateur, pcomp_pcomp_cod, comp_libelle
	from perso_competences, sorts, competences
	where pcomp_perso_cod = lanceur
		and pcomp_pcomp_cod = sort_comp_cod
		and sort_cod = num_sort
		and pcomp_pcomp_cod = comp_cod;
	if not found and type_lancer != -1 then
		code_retour := code_retour || '0;<p>Erreur : infos compétence non trouvées !';
		return code_retour;
	end if;
-- contrôles de lancement
	res_controle = controle_sort_case(num_sort, lanceur, v_pos, type_lancer);
	deb_res_controle := substr(res_controle, 1, 1);
	if deb_res_controle != '0' then
		code_retour := code_retour || '0;<p>' || res_controle;
		return code_retour;
	end if;
	if type_lancer in (2, 4, 5) then
		facteur_reussite := to_number(split_part(res_controle, ';', 2), '9999999999999');
	end if;

	-------------------------------------------------------------
-- Vérification des pre-requis spécifique sur certaine map (comme la course
-------------------------------------------------------------
    -- on va refuser de faire se sort sur une course de monture (etage avec etage_mort_speciale=1, interdire les sorts générateur de monstres)
    select etage_mort_speciale into temp from perso_position join positions on pos_cod=ppos_pos_cod join etage on etage_numero=pos_etage where ppos_perso_cod=lanceur;
    if (temp = 1 or temp = 2 ) and ( num_sort=40 or num_sort=41 or num_sort=42 or num_sort=43 or num_sort=45 or num_sort=46 or num_sort=47 or num_sort=48 or num_sort=52) then
        return  '0;<p>l''usage de ce sort n''est pas autorisé pendant les courses de monture ou une partie de mout''ball!!</p>';
    end if;


------------------------------------------------------------
-- les controles semblent bons, on peut passer à la suite
------------------------------------------------------------
	code_retour := code_retour || '<p>Vous avez lancé le sort <b>' || nom_sort || '</b>, ';
	if type_lancer = 2 then
		code_retour := code_retour || 'en utilisant un réceptacle.<br><br>';
	elsif type_lancer = 4 then
		code_retour := code_retour || 'en utilisant un parchemin.<br><br>';
	elsif type_lancer = 5 then
		code_retour := code_retour||'en utilisant un objet.<br><br>';
	elsif type_lancer = -1 then
		code_retour := code_retour||'à l''aide d''un effet-automatique.<br><br>';
	else
		code_retour := code_retour || 'en utilisant la compétence <b>' || nom_comp || '</b>.<br><br>';
	end if;

	-- on ajoute la magie à la position
	update positions
	set pos_magie = pos_magie + ((niveau_sort - 1) * 10)
	where pos_cod = v_pos
		and pos_cod <> 152794;	-- == -6 / -7 dans la Halle Merveilleuse. C’est temporaire, pour le marché de Léno 2013;

-- on rajoute le lancement du sort dans le total
	select into niveau_religion dper_niveau
		from dieu_perso
		where dper_perso_cod = lanceur;
	if not found then
		niveau_religion := 0;
	end if;
	if type_lancer not in (4,5) then
		select into compt pnbst_cod from perso_nb_sorts_total
		where pnbst_perso_cod = lanceur
			and pnbst_sort_cod = num_sort;
		if not found then
			insert into perso_nb_sorts_total (pnbst_perso_cod, pnbst_sort_cod, pnbst_nombre)
			values (lanceur, num_sort, 0);
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
      -- cas d'un effet-auto (toujours réussi, pas de gains de PX, perte de PA, etc...)
      facteur_reussite:= 100 ;
      code_retour := '1;0;' || code_retour;

  else
      -- cas des sots normaux (non-EA)

      update perso_nb_sorts_total
      set pnbst_date_dernier_lancer = now()
      where pnbst_sort_cod = num_sort
        and pnbst_perso_cod = lanceur;

      -- on enlève les PA
      -- appel de la fonction cout_pa_magie pour les calculs de cout de pa avec correlation pour l’affichage dans la page magie_php
      select into resultat cout_pa_magie(lanceur, num_sort, type_lancer);
      cout_pa := resultat;

        -- pour les sorts lancés à partir d'objet on met a jour le compteur (et on supprime le sort préparé)
      facteur_malchance :=0 ;
      if type_lancer = 5 then
        select into facteur_malchance objsort_malchance from objets_sorts join objets_sorts_magie on objsortm_objsort_cod=objsort_cod where objsortm_perso_cod = lanceur ;
        update objets_sorts set objsort_nb_utilisation=objsort_nb_utilisation+1 from objets_sorts_magie where objsortm_perso_cod = lanceur  and objsortm_objsort_cod=objsort_cod;
        --On fera le ménage en front, on a besoin de connaitre l'objet utilisé pour les option de "relancer"
        --delete from objets_sorts_magie where objsortm_perso_cod = lanceur ;
      end if;

      -- on regarde s’il y a concentration
      if type_lancer not in (2, 4, 5) then
        select into compt concentration_perso_cod from concentrations
        where concentration_perso_cod = lanceur;
        if found then
          v_comp_modifie := v_comp + 20;
          delete from concentrations where concentration_perso_cod = lanceur;
        else
          v_comp_modifie = v_comp;
        end if;
      end if;

    -- modificateurs en fonction du niveau
    --
      if type_lancer = 0 then
        v_malus_niveau := (2 - niveau_sort) * 10;
        select into nb_sort_niveau
          coalesce(sum(pnbst_nombre), 0)
        from perso_nb_sorts_total, sorts
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
      if type_lancer not in (2, 4, 5) then
        code_retour := code_retour || 'Votre chance de réussir (en tenant compte des modificateurs) est de <b>' || trim(to_char(v_comp_modifie, '9999')) || '</b> ';
        -- on regarde si il y a un bonus pour avoir plus de chances de conserver ses runes
        v_chances_runes := valeur_bonus(lanceur, 'PER');
        -- on regarde si le sort est lancé
        v_special := floor(v_comp_modifie/5); --Calcul des spéciaux
        -- on regarde si la cible est bénie ou maudite
        bonmal := valeur_bonus(lanceur, 'BEN') + valeur_bonus(lanceur, 'MAU');
        if bonmal != 0 then
          des := lancer_des3(1, 100, bonmal);
        else
          des := lancer_des(1, 100);
        end if;
        code_retour := code_retour || 'et votre lancer de dés est de <b>' || trim(to_char(des, '9999')) || '</b>.<br>';
        if des > 96 then
          -- echec critique
          if type_lancer = 0 then
            -- on enlève les runes
            for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort
            loop
              compt := drop_rune(ligne_rune.srune_gobj_cod, lanceur);
            end loop;
          end if;
          texte_evt := '[attaquant] a tenté de lancer ' || nom_sort || ' et a échoué.';

          insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant)
          values(nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur);

          code_retour := code_retour || 'Il s’agit donc d’un échec automatique.<br><br>';
          code_retour := '0;' || code_retour;
          update perso set perso_renommee_magie = perso_renommee_magie - (temp_renommee*2), perso_pa = perso_pa - LEAST(4, GREATEST(1,(4 + bonus_pa))) where perso_cod = lanceur;
          return code_retour;
        end if;

        if des > v_comp_modifie then
          -- sort loupé
          -- renomme magique
          update perso set perso_renommee_magie = perso_renommee_magie - temp_renommee, perso_pa = perso_pa - LEAST(4, GREATEST(1,(4 + bonus_pa))) where perso_cod = lanceur;
          code_retour := code_retour || 'Vous avez donc <b>échoué</b>.<br><br>';
          -- on regarde si on améliore la comp
          if v_comp <= getparm_n(1) then
            code_retour := code_retour || 'Votre compétence est inférieure à ' || trim(to_char(getparm_n(1), '9999')) || ' %. Vous tentez une amélioration.<br>';
            temp_ameliore_competence := ameliore_competence_px(lanceur, v_comp_cod, v_comp);
            code_retour := code_retour || 'Votre lancer de dés est de <b>' || split_part(temp_ameliore_competence, ';', 1) || '</b>, ';
            if split_part(temp_ameliore_competence, ';', 2) = '1' then
              code_retour := code_retour || 'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>' || split_part(temp_ameliore_competence, ';', 3) || '</b><br><br>.';
              px_gagne := px_gagne + 1;
            else
              code_retour := code_retour || 'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
            end if;
          end if;
          if type_lancer = 0 then
            for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort
            loop
              if lancer_des(1, 100+v_chances_runes) <= getparm_n(33) then
                compt := drop_rune(ligne_rune.srune_gobj_cod, lanceur);
              end if;
            end loop;
          end if;
          texte_evt := '[attaquant] a tenté de lancer ' || nom_sort || ' et a échoué.';

          insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant)
          values(nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur);

          code_retour := '0;' || code_retour;
          return code_retour;
        end if;

        -- a partir d’ici on est sur que le sort est porté.
        if des <= 5 then
          code_retour := code_retour || 'il s’agit donc d’une <b>réussite critique</b>.<br><br>';
          cout_pa := floor(cout_pa / 2);
          px_gagne := px_gagne + 1;
        else
          if des <= v_special then
            code_retour := code_retour || 'il s’agit donc d’une <b>réussite spéciale</b>.<br><br>';
            cout_pa := cout_pa - 1;
          else
            code_retour := code_retour || 'Vous avez donc <b>réussi</b>.<br><br>';
          end if;
        end if;
        facteur_reussite := v_comp_modifie - des;
        -- renomme magique
        update perso set perso_renommee_magie = perso_renommee_magie + temp_renommee where perso_cod = lanceur;
        -- px
        if (type_lancer = 0) then
          px_gagne := px_gagne + niveau_sort - 1;
        else
          px_gagne := px_gagne + ((niveau_sort - 1)/3::numeric);
        end if;

        -- on tente l’amélioration
        temp_ameliore_competence := ameliore_competence_px(lanceur, v_comp_cod, v_comp);
        code_retour := code_retour || 'Votre jet d’amélioration est de <b>' || split_part(temp_ameliore_competence, ';', 1) || '</b>, ';
        if split_part(temp_ameliore_competence, ';', 2) = '1' then
          code_retour := code_retour || 'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>' || split_part(temp_ameliore_competence, ';', 3) || '</b>.<br><br>';
        else
          code_retour := code_retour || 'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
        end if;

        -- on supprime les runes si besoin est
        if type_lancer = 0 then
          for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort
          loop
            compt := drop_rune(ligne_rune.srune_gobj_cod, lanceur);
          end loop;
        end if;
        -- on attribue les PX
        update perso set perso_px = perso_px + px_gagne where perso_cod = lanceur;
        -- on regarde pour la mémorisation
        texte_memo := memo_sort(lanceur, num_sort);
        if split_part(texte_memo, ';', 1) = '-1' then
          code_retour := code_retour || 'Vous ne pouvez pas mémoriser ce sort car vous avez atteint votre limite de mémorisation.<br>';
        end if;
        if split_part(texte_memo, ';', 1) = '-2' then
          code_retour := code_retour || 'Un familier mineur ne peut pas mémoriser de sorts de niveau 3 ou plus.<br>';
        end if;
        if split_part(texte_memo, ';', 1) = '1' then
          code_retour := code_retour || 'Vous tentez de mémoriser le sort. Votre probabilité de mémorisation est de <b>' || split_part(texte_memo, ';', 2) || '</b>. ';
          code_retour := code_retour || 'Votre lancer des dés est de <b>' || split_part(texte_memo, ';', 3) || '</b>.<br>';
          if split_part(texte_memo, ';', 4) = '1' then
            code_retour := code_retour || 'Vous avez donc <b>mémorisé</b> ce sort.<br><br>';
            px_gagne := px_gagne + 1;
          else
            code_retour := code_retour || 'Vous n’avez pas réussi à mémoriser ce sort.<br><br>';
          end if;
        end if;
      end if; -- fin réceptacle, parcho, objet

      -- Il y a certains objets qui possède un facteur de malchance, faisant échoué le lancement du sort
      if type_lancer = 5 and facteur_malchance >0 then
          des := 100 * lancer_des(1,100);   -- facteur_malchance a une précision à 0.01 %
          if des <= 100 * facteur_malchance then
            code_retour := code_retour||'Vous n''avez pas réussi à utiliser l''objet, le sortilège à <b>échoué</b>.<br><br>';

            texte_evt := '[attaquant] a tenté de lancer ' || nom_sort || ' et a échoué.';

            insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant)
            values(nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur);

            update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
            code_retour := '0;'||code_retour;
            return code_retour;
          end if;
      end if;

      code_retour := '1;0;' || code_retour;
      update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;

      if split_part(temp_ameliore_competence, ';', 2) = '1' then
        px_gagne := px_gagne + 1;
      end if;

  end if;


  ---------------------------
  -- les EA liés au lancement d'un sort (avec protagoniste null)
  ---------------------------
  code_retour := code_retour || execute_fonctions(lanceur, null, 'MAL', json_build_object('num_sort', num_sort) );

	code_retour := code_retour || ';' || to_char(px_gagne, '999999990D99') || ';' || trim(to_char(facteur_reussite, '99999999999'));
	return code_retour;
end;
$_$;


ALTER FUNCTION public.magie_commun_case(integer, integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION magie_commun_case(integer, integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION magie_commun_case(integer, integer, integer, integer) IS 'Gère les vérifications relatives à la magie sur une case, ainsi que les jets de compétence, amélioration, apprentissage';
