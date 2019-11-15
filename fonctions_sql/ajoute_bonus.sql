--
-- Name: ajoute_bonus(integer, text, integer, numeric); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ajoute_bonus(integer, text, integer, numeric) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Rajoute un bonus à un perso
-- $1 = Le code du perso en question
-- $2 = Le type de bonus
-- $3 = La durée (DLT) du bonus
-- $3 = La valeur du bonus
-- Retourne 1 si le bonus est nouveau, 0 s'il en remplace un autre du même signe.
-- 13/02/2009 Bleda: Supprime les bonus existants du même type, avant de créer le nouveau
-- ??/??/???? ?????: Remplace les bonus de même signe, sans toucher à ceux de signe contraire
-- 07/12/2009 problème : remplace les bonus de meme signe, meme inférieur.

declare
  v_perso alias for $1;
  v_type alias for $2;
  v_duree alias for $3;
  v_valeur alias for $4;
  v_retour integer;
	temp integer;	-- variable fourre tout
  code_retour text;
begin
  v_retour := 1;

  -- 08/11/2019 - Marlyza - Ajout de bonus de carac (FOR/INT/DEX/CON) à l'aide de f_modif_carac

  if v_type in ('DEX', 'INT', 'FOR', 'CON')  THEN
    -- cas des bonus de carac --

    -- On vérifie si le perso avait déjà un bonus !!
    select into temp corig_carac_valeur_orig from carac_orig where corig_perso_cod = v_perso and corig_type_carac = v_type;
    if found then
      v_retour := 0;
    else
     v_retour := 1;
    end if;

    -- la fontion ajoute_bonus() retourne 0 ou 1, mais pas de code d'erreur, on va ignorer le resultat de f_modif_carac_base()
	  perform f_modif_carac_base(v_perso, v_type, 'T', v_duree, v_valeur::integer)	;

  ELSE

    -- cas des bonus standards --
    delete from bonus where
      bonus_perso_cod = v_perso and
      bonus_tbonus_libc = v_type and sign(bonus_valeur) = sign(v_valeur);
    if found then
      -- On a effacé un bonus existant pour le remplacer.
      v_retour := 0;
    end if;
    -- En attendant la version définitive, on supprime aussi les bonus de sens opposé
    -- AJout de Az le 07/12/2009.. on tente une mise en production définitive.. mise en commentaire des 3 lignes suivantes
    -- AJout de Bleda le 07/02/2011.. on tente une mise en production définitive.. encore. mise en commentaire des 3 lignes suivantes
    -- Bleda, 27/03/2011 : Désactivation du cumul de bonus pour PAA et DEP: Décommenter les 4 lignes suivantes
    -- delete from bonus where
    --   bonus_perso_cod = v_perso and
    --   bonus_tbonus_libc = v_type and sign(bonus_valeur) != sign(v_valeur);
    --   and tbonus_libc in ('PAA', 'DEP')
    -- Fin de la partie à décommenter pour les bonus sélectionnés
    insert into bonus (bonus_perso_cod, bonus_tbonus_libc, bonus_nb_tours, bonus_valeur)
    values (v_perso,         v_type,            v_duree,        v_valeur);

    -- 2019-05-28 - Marlyza - Pour le Jus de Chronomèetre on applique immédiatement
    if v_type='JDC' then
      update perso
        set perso_dlt = NOW()::timestamp + (to_char(v_valeur,'999999') || ' minutes')::interval
        where perso_cod = v_perso ;
    end if;

  end if;

  return v_retour;
end;$_$;


ALTER FUNCTION public.ajoute_bonus(integer, text, integer, numeric) OWNER TO delain;

--
-- Name: ajoute_bonus(integer, text, integer, numeric, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ajoute_bonus(integer, text, integer, numeric, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Rajoute un bonus à un perso
-- $1 = Le code du perso en question
-- $2 = Le type de bonus
-- $3 = La durée (DLT) du bonus
-- $4 = La valeur du bonus
-- $5 = La valeur de laquelle le bonus évolue à chaque DLT.

declare
  v_perso alias for $1;
  v_type alias for $2;
  v_duree alias for $3;
  v_valeur alias for $4;
  v_croissance alias for $5;
  v_retour integer;
begin
  v_retour := ajoute_bonus(v_perso, v_type, v_duree, v_valeur);

  update bonus set bonus_croissance = v_croissance
  where bonus_perso_cod = v_perso
        and bonus_tbonus_libc = v_type
        and bonus_valeur = v_valeur;

  return v_retour;
end;$_$;


ALTER FUNCTION public.ajoute_bonus(integer, text, integer, numeric, integer) OWNER TO delain;

--
-- Name: FUNCTION ajoute_bonus(integer, text, integer, numeric, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION ajoute_bonus(integer, text, integer, numeric, integer) IS 'Comme ajoute_bonus(int, text, int, num), mais rajoute une notion de croissance (ou décroissance) du bonus à chaque DLT.';