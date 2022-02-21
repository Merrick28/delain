--
-- Name: bonus_art_reg(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function bonus_art_reg(integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/*******************************************/
/* bonus_art_reg                           */
/*  params : $1 = perso_cod                */
/*******************************************/
declare
  code_retour integer;
  personnage alias for $1;
  ligne record;
  temp integer;
begin
  code_retour := 0;
  for ligne in
  select obj_cod,obj_regen
  from perso_objets,objets
  where perobj_perso_cod = personnage
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and coalesce(obj_regen,0) != 0 loop
    -- boucle
    code_retour := code_retour + coalesce(ligne.obj_regen,0);
    temp := use_artefact(ligne.obj_cod);
    -- fin boucle
  end loop;
  return code_retour;
end;$_$;


ALTER FUNCTION public.bonus_art_reg(integer) OWNER TO delain;