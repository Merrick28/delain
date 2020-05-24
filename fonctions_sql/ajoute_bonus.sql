--
-- Name: ajoute_bonus(integer, text, integer, numeric); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ajoute_bonus(integer, text, integer, numeric) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Rajoute un bonus à un perso
-- $1 = Le code du perso en question
-- $2 = Le type de bonus (si le bonus est cumulatif, alors le type est suivi du suffix "+")
-- $3 = La durée (DLT) du bonus
-- $4 = La valeur du bonus
-- Retourne 1 si le bonus est nouveau, 0 s'il en remplace un autre du même signe.
-- 13/02/2009 Bleda: Supprime les bonus existants du même type, avant de créer le nouveau
-- ??/??/???? ?????: Remplace les bonus de même signe, sans toucher à ceux de signe contraire
-- 07/12/2009 problème : remplace les bonus de meme signe, meme inférieur.
-- 02/12/2019 - Marlyza - cette fonction ne gère que les B/M Standards et les cumulatifs, les bonus d'équipement seront géré par une autre fonction.

declare
  v_perso alias for $1;
  v_bonus alias for $2;
  v_duree alias for $3;
  v_valeur alias for $4;
  v_retour integer;
  v_type text;
  v_cumulatif text;
  v_degressivite integer;  -- pour la dégressivité
  v_nb integer;  -- pour la dégressivité
  v_i integer;  -- pour les boucles
	temp integer;	-- variable fourre tout
	v_valeur_avant integer;	-- BM avant l'ajout
	v_valeur_apres integer;	-- BM après l'ajout
  code_retour text;
begin
  v_retour := 1;    -- par defaut on considère avoir appliqué un bonus

  -- decomposition du bonus en type + cumulatif
  v_type := SUBSTR(v_bonus, 1, 3);
  if  v_type != v_bonus THEN
    v_cumulatif := 'C';
  else
    v_cumulatif := 'S';
  end if;

  -- Pour déclenchement d'EA sur changement de BM, on mémorise la valeur avant modification.
  v_valeur_avant := valeur_bonus(v_perso, v_type);

  -- 08/11/2019 - Marlyza - Ajout de bonus de carac (FOR/INT/DEX/CON) à l'aide de f_modif_carac_base
  IF v_type in ('DEX', 'INT', 'FOR', 'CON')  THEN

    -- cas des bonus de carac -------------------------------------------------------------------------
	  temp := f_modif_carac_base(v_perso, v_type, 'T', v_duree, v_valeur::integer, v_cumulatif)	;

    -- la fontion ajoute_bonus() doit retourner un boolean 0/1, on va convertir le resultat de f_modif_carac_base()
	  if temp = 0 then
	    v_retour := 0;
    else
      v_retour := 1;
    end if;

  ELSE

    -- cas des bonus standards -------------------------------------------------------------------------

    -- vérifiation si cumulatif et bonus/malus cumulable !
    select into v_degressivite tbonus_degressivite from bonus_type where v_cumulatif='C' and tbonus_cumulable='O' and tbonus_libc=v_type ;
    if found then
      --
      -- cas d'un fonctionnement avec bonus/malus cumulable et systeme de degressivite!
      --

      -- récupération du nombre de bonus/malus du même type
      select count(*) into v_nb from bonus where bonus_perso_cod = v_perso and bonus_tbonus_libc = v_type and sign(bonus_valeur) = sign(v_valeur) and bonus_mode='C' ;

      -- application de la dégressivité sur la valeur
      v_i := 0;
      while v_i < v_nb loop
        v_valeur := v_valeur * v_degressivite / 100 ;
        v_i := v_i + 1;
      end loop;
      v_valeur := sign(v_valeur) * floor(abs(v_valeur)) ;   -- arrondi à l'inférieur pour les positif ou au supérieur pour les négatifs

      if v_valeur = 0 then
        v_retour := 0;
      else
        v_retour := 1;

        insert into bonus (bonus_perso_cod, bonus_tbonus_libc, bonus_nb_tours, bonus_valeur, bonus_mode, bonus_degressivite)
        values (v_perso, v_type, v_duree, v_valeur, 'C', v_degressivite);

      end if;

    else
      --
      -- cas du fonctionnement normal (mode cumulatif = 'S') (celui utilisé depuis toujours)
      --

      delete from bonus where
        bonus_perso_cod = v_perso and
        bonus_tbonus_libc = v_type and sign(bonus_valeur) = sign(v_valeur) and bonus_mode='S';
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

      -- 2019-05-28 - Marlyza - Pour le Jus de Chronomètre on applique immédiatement (ne sera jamais cumulatif :-)
      if v_type='JDC' then
        update perso
          set perso_dlt = NOW()::timestamp + (to_char(v_valeur,'999999') || ' minutes')::interval
          where perso_cod = v_perso ;
      end if;

    end if;

  END IF;

  -- On vérifie s'il y a un changement de BM, si oui on verifie le déclenchement des EA
  v_valeur_apres := valeur_bonus(v_perso, v_type);
  if v_valeur_apres != v_valeur_avant then
      -- perform execute_effet_auto_bmc(v_perso, v_type, v_valeur_avant, v_valeur_apres);
      perform execute_fonctions(v_perso, null, 'BMC', json_build_object('bonus_type', v_type, 'valeur_avant', v_valeur_avant, 'valeur_apres', v_valeur_apres) );
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
        and bonus_valeur = v_valeur
        and bonus_mode = 'S';

  return v_retour;
end;$_$;


ALTER FUNCTION public.ajoute_bonus(integer, text, integer, numeric, integer) OWNER TO delain;

--
-- Name: FUNCTION ajoute_bonus(integer, text, integer, numeric, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION ajoute_bonus(integer, text, integer, numeric, integer) IS 'Comme ajoute_bonus(int, text, int, num), mais rajoute une notion de croissance (ou décroissance) du bonus à chaque DLT.';