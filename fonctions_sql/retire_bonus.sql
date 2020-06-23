--
-- Name: retire_bonus(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function retire_bonus(integer, text, text, text) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Retire un bonus d'équipement à un perso
-- $1 = Le code du perso en question
-- $2 = le bonus a retirer
-- $3 = le bonus mode du bonus à supprimer 'S' standards ou C cumulatif ou SC pour les 2 (pour les équipements utiliser: retire_bonus_equipement)
-- $4 = Bonus (B), malus(M) ou les 2 Bonus et Malus (BM) ?

declare
  v_perso alias for $1;
  v_type alias for $2;
  v_mode alias for $3;
  v_bm alias for $4;

  ligne record;
  code_retour text;
begin



  IF v_type in ('DEX', 'INT', 'FOR', 'CON')  THEN

      -- pour les caracs on met à 0 et on
      update carac_orig set corig_nb_tours = 0
        from bonus_type
        where  tbonus_libc=carac_tbonus_libc
            and corig_perso_cod = v_perso
            and corig_type_carac=v_type
            and corig_mode!='E'
            and ( corig_mode=v_mode or v_mode='SC' or v_mode='CS')
            and (  (v_bm='BM')
                or (v_bm='B' and (tbonus_gentil_positif = 't' and bonus_valeur > 0 or tbonus_gentil_positif = 'f' and bonus_valeur < 0))
                or (v_bm='M' and (tbonus_gentil_positif = 't' and bonus_valeur < 0 or tbonus_gentil_positif = 'f' and bonus_valeur > 0))  ) ;
      perform f_remise_caracs(v_perso) ;

  ELSE

      -- supression des bonus et/ou malus normaux (sauf equipement)
      delete from bonus
        using bonus_type
        where  tbonus_libc=bonus_tbonus_libc
            and bonus_perso_cod=v_perso
            and bonus_tbonus_libc=v_type
            and bonus_mode!='E'
            and ( bonus_mode=v_mode or v_mode='SC' or v_mode='CS')
            and (  (v_bm='BM')
                or (v_bm='B' and (tbonus_gentil_positif = 't' and bonus_valeur > 0 or tbonus_gentil_positif = 'f' and bonus_valeur < 0))
                or (v_bm='M' and (tbonus_gentil_positif = 't' and bonus_valeur < 0 or tbonus_gentil_positif = 'f' and bonus_valeur > 0))  ) ;

  END IF;


  return 0;
end;$_$;


ALTER FUNCTION public.retire_bonus(integer, text, text, text) OWNER TO delain;
