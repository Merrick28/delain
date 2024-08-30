--
-- Name: f_perso_protec_critique(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function f_perso_protec_critique(integer) RETURNS numeric
LANGUAGE plpgsql
AS $_$/*******************************************/
/* f_perso_protec_critique                      */
/*  params : $1 = perso_cod                */
/*******************************************/
declare

  personnage alias for $1;
  code_retour numeric;
  v_protec_critique integer;

begin
  code_retour := 0;

  select coalesce(sum(obj_critique),0) into v_protec_critique
    from perso_objets,objets
    where perobj_perso_cod = personnage
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and coalesce(obj_critique,0) != 0 ;

  code_retour := LEAST(100, GREATEST(0, v_protec_critique + valeur_bonus(personnage, 'PRO')));  -- ajout des bonus/malus de protection

  return code_retour;
end;$_$;


ALTER FUNCTION public.f_perso_protec_critique(integer) OWNER TO delain;
