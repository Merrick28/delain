--
-- Name: f_perso_aura_feu(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function f_perso_aura_feu(integer) RETURNS numeric
LANGUAGE plpgsql
AS $_$/*******************************************/
/* f_perso_aura_feu                      */
/*  params : $1 = perso_cod                */
/*******************************************/
declare

  personnage alias for $1;
  code_retour numeric;
  v_aura_feu numeric;

begin
  code_retour := 0;

  select coalesce(sum(obj_aura_feu),0) into v_aura_feu
    from perso_objets,objets
    where perobj_perso_cod = personnage
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and coalesce(obj_aura_feu,0) != 0 ;

  code_retour := v_aura_feu;

  return LEAST(1, GREATEST(0, code_retour + valeur_bonus(personnage, 'ADF')/100));  -- ajout des bonus/malus de aura de feu (entre 0 et 1)

end;$_$;


ALTER FUNCTION public.f_perso_aura_feu(integer) OWNER TO delain;
