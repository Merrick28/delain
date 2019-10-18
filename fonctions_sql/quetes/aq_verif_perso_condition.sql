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
  (24, 'Niveau Enluminure', 'COMPETENCE');
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

  else
    return 0 ;    -- erreur dans les paramètres

  end if;


 if v_type_comparaison = 'NUM' then    -- Comparaison entre valeur NUMeric

    -- on a récupérr la carac du perso, on la compare à la consigne demandé
    if (v_param_txt_1 = '=') and (TO_NUMBER(v_perso_carac, '9999999999.99') = TO_NUMBER(v_param_txt_2, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '!=') and (TO_NUMBER(v_perso_carac, '9999999999.99') != TO_NUMBER(v_param_txt_2, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '<') and (TO_NUMBER(v_perso_carac, '9999999999.99') < TO_NUMBER(v_param_txt_2, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '<=') and (TO_NUMBER(v_perso_carac, '9999999999.99') <= TO_NUMBER(v_param_txt_2, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '>') and (TO_NUMBER(v_perso_carac, '9999999999.99') > TO_NUMBER(v_param_txt_2, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = '>=') and (TO_NUMBER(v_perso_carac, '9999999999.99') >= TO_NUMBER(v_param_txt_2, '9999999999.99')) then
      return 1;

    elsif (v_param_txt_1 = 'entre') and (TO_NUMBER(v_perso_carac, '9999999999.99') >= TO_NUMBER(v_param_txt_2, '9999999999.99')) and (TO_NUMBER(v_perso_carac, '9999999999.99') <= TO_NUMBER(v_param_txt_3, '9999999999.99')) then
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