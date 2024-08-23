--
-- Name: aq_verif_perso_condition(integer,integer,text,text,text); Type: FUNCTION; Schema: potions; Owner: postgres
--

CREATE or REPLACE FUNCTION quetes.aq_verif_perso_condition(integer,integer,text,text,text) RETURNS integer
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function aq_verif_perso_condition						         */
/* parametres :                                          */
/*  $1 = personnage                                      */
/*  $2 = type de carac                                   */
/*  $3 = condition                                       */
/*  $4 = valeur de ref                                   */
/*  $5 = seconde valeur (pour condition "entre")        */
/* Sortie :                                              */
/*  code_retour = 1 si condition vérifiée 0 sinon        */
/*********************************************************/
declare
  v_perso_cod alias for $1;	    -- perso_cod
  v_carac_cod alias for $2;	    --  le code de la carac a vérifier
  v_param_txt_1 alias for $3;	  --  condition (=, <=, < etc....
  v_param_txt_2 alias for $4;	  --  valeur de référence
  v_param_txt_3 alias for $5;	  -- seconde valeur de référence si condition "entre"
  --
  v_perso_carac text;             -- la carac du perso a vérifier
  v_type_comparaison text;        -- comparaison NUM ou CHAR
  --

begin

/*
  == les caracs ================================== :
  (1, 'Force', 'CARAC'),
  (2, 'Dextérité', 'CARAC'),
  (3, 'Intelligence', 'CARAC'),
  (4, 'Constitution', 'CARAC'),
  (5, 'Sexe (M/F)', 'CARAC'),
  (6, 'Race (Nain, Elfe, Humain)', 'CARAC'),
  (7, 'VUE', 'CARAC'),
  (8, 'Nb de Bzf', 'VARIABLE'),
  (9, 'NB de PA', 'VARIABLE'),
  (10, 'Point de Vie MAX', 'CARAC'),
  (11, 'Point de Vie', 'VARIABLE'),
  (12, 'PV en % de Blessure', 'VARIABLE'),
  (13, 'Temps au Tour (en minutes)', 'CARAC'),
  (14, 'Niveau', 'CARAC'),
  (15, 'Intangibilité (en nb de tour)', 'VARIABLE'),
  (16, 'Voie Magique (de 0 à 7)', 'CARAC'),
  (17, 'Type perso (1=PJ, 2=Monstre, 3=Fam.)', 'CARAC'),
  (18, 'Perso PNJ (0=PJ, 1=PNJ, 3=4ème)', 'CARAC'),
  (19, 'Competence Alchimie (en %)', 'COMPETENCE'),
  (20, 'Competence Forgemage (en %)', 'COMPETENCE'),
  (21, 'Competence Enluminure (en %)', 'COMPETENCE'),
  (22, 'Niveau Alchimie', 'COMPETENCE'),
  (23, 'Niveau Forgemage', 'COMPETENCE'),
  (24, 'Niveau Enluminure', 'COMPETENCE'),
  (25, 'A terminé l''étape de QA', 'QUETE'),
  (26, 'Nombre de lock', 'VARIABLE'),
  (27, 'Code du perso', 'CARAC'),
  (28, 'Possède un type d’objet générique', 'OBJET'),
  (29, 'Chevauche une monture du type du monstre générique', 'VARIABLE'),
  (30, 'Monstre générique', 'MONSTRE'),
  (31, 'Renommée / Renommée magique', 'CARAC'),
  (32, 'Visite de l''étage (en %)', 'VARIABLE'),
  (33, 'Nombre de sorts connus', 'COMPETENCE'),
  (34, 'Nombre de sorts niveau 2 connus', 'COMPETENCE'),
  (35, 'Nombre de sorts niveau 3 connus', 'COMPETENCE'),
  (36, 'Nombre de sorts niveau 4 connus', 'COMPETENCE'),
  (37, 'Nombre de sorts niveau 5 connus', 'COMPETENCE'),
  (38, 'Nombre de sorts niveau 6 connus', 'COMPETENCE');
 */

  v_type_comparaison := 'NUM';  -- PAR Défaut comparaison en Intéger

  if (v_carac_cod = 1) then                       -- (1, 'Force', 'CARAC'),
    select into v_perso_carac perso_for::text from perso where perso_cod = v_perso_cod ;

  elsif (v_carac_cod = 2) then                 --   (2, 'Dextérité', 'CARAC'),
    select into v_perso_carac perso_dex::text from perso where perso_cod = v_perso_cod ;

  elsif (v_carac_cod = 3) then                 --   (3, 'Intelligence', 'CARAC'),
    select into v_perso_carac perso_int::text from perso where perso_cod = v_perso_cod ;

  elsif (v_carac_cod = 4) then                 --   (4, 'Constitution', 'CARAC'),
    select into v_perso_carac perso_con::text from perso where perso_cod = v_perso_cod ;

  elsif (v_carac_cod = 5) then                  -- (5, 'Sexe (M/F)', 'CARAC'),
    select into v_perso_carac perso_sex from perso where perso_cod = v_perso_cod ;
    v_type_comparaison := 'CHAR';  -- Comparaison en Type caractère

  elsif (v_carac_cod = 6) then                  -- (6, 'Race (Nain, Elfe, Humain)', 'CARAC'),
    select into v_perso_carac race_nom from perso join race on race_cod=perso_race_cod where perso_cod=v_perso_cod ;
    v_type_comparaison := 'CHAR';  -- Comparaison en Type caractère

  elsif (v_carac_cod = 7) then                   -- (7, 'VUE', 'CARAC'),
    select into v_perso_carac perso_vue::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 8) then                  -- (8, 'Nb de Bzf', 'VARIABLE'),
    select into v_perso_carac perso_po::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 9) then                   -- (9, 'NB de PA', 'VARIABLE'),
    select into v_perso_carac perso_pa::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 10) then                  -- (10, 'Point de Vie MAX', 'CARAC'),
    select into v_perso_carac perso_pv_max::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 11) then                  -- (11, 'Point de Vie', 'VARIABLE'),
    select into v_perso_carac perso_pv::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 12) then                  -- (12, 'PV en % de Blessure', 'VARIABLE'),
    select into v_perso_carac round(((100*perso_pv::numeric)/perso_pv_max),2)::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 11) then                  -- (13, 'Temps au Tour (en minutes)', 'CARAC'),
    select into v_perso_carac perso_temps_tour::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 14) then                   -- (14, 'Niveau', 'CARAC'),
    select into v_perso_carac perso_niveau::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 15) then                   -- (15, 'Intangibilité (en nb de tour)', 'VARIABLE'),
    select into v_perso_carac (CASE WHEN perso_tangible='O' THEN 0 ELSE perso_nb_tour_intangible END)::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 16) then                   -- (16, 'Voie Magique (de 0 à 7)', 'CARAC'),
    select into v_perso_carac perso_voie_magique::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 17) then                   -- (17, 'Type perso (1=PJ, 2=Monstre, 3=Fam.)', 'CARAC'),
    select into v_perso_carac perso_type_perso::text from perso where perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 18) then                 --   (18, 'Perso PNJ (0=PJ, 1=PNJ, 3=4ème)', 'CARAC'),
    select into v_perso_carac perso_pnj::text from perso where perso_cod = v_perso_cod ;

  elsif (v_carac_cod = 19) then                  --   (19, 'Competence Alchimie (en %)', 'COMPETENCE'),
    select into v_perso_carac pcomp_modificateur from perso_competences join competences on comp_cod = pcomp_pcomp_cod	where pcomp_perso_cod = v_perso_cod and pcomp_modificateur != 0 and comp_cod in (97,100,101) ;
    if not found then
      return 0;
    end if;

  elsif (v_carac_cod = 20) then                  --   (20, 'Competence Forgemage (en %)', 'COMPETENCE'),
    select into v_perso_carac pcomp_modificateur from perso_competences join competences on comp_cod = pcomp_pcomp_cod	where pcomp_perso_cod = v_perso_cod and pcomp_modificateur != 0 and comp_cod in (88,102,103) ;
    if not found then
      return 0;
    end if;

  elsif (v_carac_cod = 21) then                  -- (21, 'Competence Enluminure (en %)', 'COMPETENCE'),
    select into v_perso_carac pcomp_modificateur from perso_competences join competences on comp_cod = pcomp_pcomp_cod	where pcomp_perso_cod = v_perso_cod and pcomp_modificateur != 0 and comp_cod in (91,92,93) ;
    if not found then
      return 0;
    end if;

  elsif (v_carac_cod = 22) then                  --   (22, 'Niveau Alchimie', 'COMPETENCE'),
    select into v_perso_carac
          case when comp_cod=97 then 1
               when comp_cod=100 then 2
               when comp_cod=101 then 3
           else 0 end
      from perso_competences join competences on comp_cod = pcomp_pcomp_cod	where pcomp_perso_cod = v_perso_cod and pcomp_modificateur != 0 and comp_cod in (97,100,101) ;
    if not found then
      return 0;
    end if;

  elsif (v_carac_cod = 23) then                  --   (23, 'Niveau Forgemage', 'COMPETENCE'),
    select into v_perso_carac
          case when comp_cod=88 then 1
               when comp_cod=102 then 2
               when comp_cod=103 then 3
           else 0 end
      from perso_competences join competences on comp_cod = pcomp_pcomp_cod	where pcomp_perso_cod = v_perso_cod and pcomp_modificateur != 0 and comp_cod in (88,102,103) ;
    if not found then
      return 0;
    end if;

  elsif (v_carac_cod = 24) then                  --   (24, 'Niveau Enluminure', 'COMPETENCE');
    select into v_perso_carac
          case when comp_cod=91 then 1
               when comp_cod=92 then 2
               when comp_cod=93 then 3
           else 0 end
      from perso_competences join competences on comp_cod = pcomp_pcomp_cod	where pcomp_perso_cod = v_perso_cod and pcomp_modificateur != 0 and comp_cod in (91,92,93) ;
    if not found then
      return 0;
    end if;

  elsif (v_carac_cod = 25) then                  --    (25, 'A terminé l''étape de QA', 'QUETE');
    select into v_perso_carac aqpersoj_etape_cod from quetes.aquete_perso_journal join quetes.aquete_perso on aqperso_cod=aqpersoj_aqperso_cod and aqperso_perso_cod=v_perso_cod  and aqpersoj_etape_cod=TO_NUMBER(v_param_txt_2, '9999999999.99') limit 1 ;
    if not found then
      return 0;
    else
      return 1;
    end if;

  elsif (v_carac_cod = 26) then                  --    (26, 'Nombre de lock', 'VARIABLE')
    select into v_perso_carac count(*) from lock_combat where lock_cible = v_perso_cod or lock_attaquant = v_perso_cod ;

  elsif (v_carac_cod = 27) then                  --   (27, 'Code du perso', 'CARAC');
    select into v_perso_carac perso_cod::text from perso where perso_cod = v_perso_cod ;

  elsif (v_carac_cod = 28) then                  --   (28, 'Possède un type d’objet générique', 'OBJET')
    select obj_cod into v_perso_carac from perso_objets join objets on perobj_obj_cod=obj_cod where perobj_perso_cod = v_perso_cod and obj_gobj_cod = TO_NUMBER(v_param_txt_2, '9999999999.99') LIMIT 1 ;
    if found then
      if (v_param_txt_1 = '=') then
        return 1;
      else
        return 0;
      end if;
    else
      if (v_param_txt_1 = '!=') then
        return 1;
      else
        return 0;
      end if;
    end if;

  elsif (v_carac_cod = 29) then                  --  (29, 'Chevauche une monture du type du monstre générique', 'VARIABLE')
    -- le 12/16/2021: ajout d'un patch pour le doppelganger qui change de cod de monstre #1548 mais dont le nom commence par Doppelganger
    select coalesce(case when m.perso_nom ilike 'Doppelganger%' then 1548 else m.perso_gmon_cod end,0) into v_perso_carac from perso p left join perso m on m.perso_cod=p.perso_monture where p.perso_cod=v_perso_cod ;

  elsif (v_carac_cod = 30) then                  --  (30, 'Monstre générique', 'MONSTRE', 'Monstre générique');
    select into v_perso_carac COALESCE(perso_gmon_cod,0)::text from perso where perso_cod = v_perso_cod ;

  elsif (v_carac_cod = 31) then                  --  (31, 'Renommée / Renommée magique', 'CARAC', 'Renommée / Renommée magique');
    select into v_perso_carac abs(perso_renommee/perso_renommee_magie)::text from perso where perso_cod = v_perso_cod ;

  elsif (v_carac_cod = 32) then                  --  (32, 'RVisite de l'étage (en %)', 'VARIABLE', 'Visite de l'étage (en %)');
    select into v_perso_carac f_perso_visite_etage(v_perso_cod)::text ;

  elsif (v_carac_cod = 33) then                  --  (33, 'Nombre de sort connu', 'COMPETENCE', 'Vérification du nombre de sorts connus (sorts de tous niveaux confondu)');
    select into v_perso_carac count(*) from perso join perso_sorts on psort_perso_cod=perso_cod where  perso_cod = v_perso_cod  ;

  elsif (v_carac_cod = 34) then                  --  (34, 'Nombre de sort niveau 2 connu', 'COMPETENCE', 'Vérification du nombre de sort niveau 2 connu (sorts règne)');
    select into v_perso_carac count(*) from perso join perso_sorts on psort_perso_cod=perso_cod join sorts on sort_cod=psort_sort_cod where  perso_cod = v_perso_cod  and sort_niveau=2 ;

  elsif (v_carac_cod = 35) then                  --  (35, 'Nombre de sort niveau 3 connu', 'COMPETENCE', 'Vérification du nombre de sort niveau 3 connu (sorts élément)');
    select into v_perso_carac count(*) from perso join perso_sorts on psort_perso_cod=perso_cod join sorts on sort_cod=psort_sort_cod where  perso_cod = v_perso_cod  and sort_niveau=3 ;

  elsif (v_carac_cod = 36) then                  --  (36, 'Nombre de sort niveau 4 connu', 'COMPETENCE', 'Vérification du nombre de sort niveau 4 connu (sorts main)');
    select into v_perso_carac count(*) from perso join perso_sorts on psort_perso_cod=perso_cod join sorts on sort_cod=psort_sort_cod where  perso_cod = v_perso_cod  and sort_niveau=4 ;

  elsif (v_carac_cod = 37) then                  --  (37, 'Nombre de sort niveau 5 connu', 'COMPETENCE', 'Vérification du nombre de sort niveau 5 connu (sorts totem)');
    select into v_perso_carac count(*) from perso join perso_sorts on psort_perso_cod=perso_cod join sorts on sort_cod=psort_sort_cod where  perso_cod = v_perso_cod  and sort_niveau=5 ;

  elsif (v_carac_cod = 38) then                  --  (38, 'Nombre de sorts niveau 6 connu', 'COMPETENCE', 'Vérification du nombre de sort niveau 6 connu (sorts énergie)');
    select into v_perso_carac count(*) from perso join perso_sorts on psort_perso_cod=perso_cod join sorts on sort_cod=psort_sort_cod where  perso_cod = v_perso_cod  and sort_niveau=6 ;

  else
    return 0 ;    -- erreur dans les paramètres

  end if;


 if v_type_comparaison = 'NUM' then    -- Comparaison entre valeur NUMeric

    -- on a récupérr la carac du perso, on la compare à la consigne demandé
    if (v_param_txt_1 = '=') and (TO_NUMBER(v_perso_carac, '9999999999.99') = TO_NUMBER(f_to_numeric(v_param_txt_2)::text, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '!=') and (TO_NUMBER(v_perso_carac, '9999999999.99') != TO_NUMBER(f_to_numeric(v_param_txt_2)::text, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '<') and (TO_NUMBER(v_perso_carac, '9999999999.99') < TO_NUMBER(f_to_numeric(v_param_txt_2)::text, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '<=') and (TO_NUMBER(v_perso_carac, '9999999999.99') <= TO_NUMBER(f_to_numeric(v_param_txt_2)::text, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '>') and (TO_NUMBER(v_perso_carac, '9999999999.99') > TO_NUMBER(f_to_numeric(v_param_txt_2)::text, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '>=') and (TO_NUMBER(v_perso_carac, '9999999999.99') >= TO_NUMBER(f_to_numeric(v_param_txt_2)::text, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = 'entre') and (TO_NUMBER(v_perso_carac, '9999999999.99') >= TO_NUMBER(f_to_numeric(v_param_txt_2)::text, '9999999999.99')) and (TO_NUMBER(v_perso_carac, '9999999999.99') <= TO_NUMBER(f_to_numeric(v_param_txt_3)::text, '9999999999.99')) then
      return 1;

    end if;

 else -- Comparaison entre valeur CHAine de caRactère

    -- on a récupérr la carac du perso, on la compare à la consigne demandé
    if (v_param_txt_1 = '=') and (v_perso_carac = v_param_txt_2) then
      return 1;

    elsif (v_param_txt_1 = '!=') and (v_perso_carac != v_param_txt_2) then
      return 1;

    elsif (v_param_txt_1 = '<') and (v_perso_carac < v_param_txt_2) then
      return 1;

    elsif (v_param_txt_1 = '<=') and (v_perso_carac <= v_param_txt_2) then
      return 1;

    elsif (v_param_txt_1 = '>') and (v_perso_carac > v_param_txt_2) then
      return 1;

    elsif (v_param_txt_1 = '>=') and (v_perso_carac >= v_param_txt_2) then
      return 1;

    elsif (v_param_txt_1 = 'entre') and (v_perso_carac >= v_param_txt_2) and (v_perso_carac <= v_param_txt_3) then
      return 1;

    end if;

  end if;

  return 0;

end;
$_$;


ALTER FUNCTION quetes.aq_verif_perso_condition(integer,integer,text,text,text) OWNER TO delain;
