--
-- Name: retire_bonus_equipement(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function retire_bonus_equipement(integer, integer, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Retire un bonus d'équipement à un perso
-- $1 = Le code du perso en question
-- $2 = le code de l'objet qui donne ce bonus
-- $3 = le code du objbm (si null on retire tous les BM donnés par l'objet)

declare
  v_perso alias for $1;
  v_obj_cod alias for $2;
  v_objbm_cod alias for $3;

  ligne record;
  code_retour text;
begin

  -- supression des bonus normaux (s'il y en a)
  delete from bonus where bonus_perso_cod=v_perso and bonus_obj_cod=v_obj_cod and (bonus_objbm_cod=v_objbm_cod or v_objbm_cod is null );

  -- supression des bonus de carac (s'il y en a)
  update carac_orig set corig_nb_tours=0 where corig_perso_cod=v_perso and corig_obj_cod=v_obj_cod  and (corig_objbm_cod=v_objbm_cod or v_objbm_cod is null);

  -- remise des caracs en état après la suppression  (s'il y a eu des supressions)
  perform f_remise_caracs(v_perso) ;

  return 0;
end;$_$;


ALTER FUNCTION public.retire_bonus_equipement(integer, integer, integer) OWNER TO delain;
