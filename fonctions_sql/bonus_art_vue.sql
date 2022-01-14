--
-- Name: bonus_art_vue(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function bonus_art_vue(integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/*******************************************/
/* bonus_art_vue                           */
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
  select obj_cod,obj_bonus_vue
  from perso_objets,objets
  where perobj_perso_cod = personnage
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and coalesce(obj_bonus_vue,0) != 0 loop
    -- boucle
    code_retour := code_retour + coalesce(ligne.obj_bonus_vue,0);
    -- fin boucle
  end loop;
  return code_retour;
end;$_$;


ALTER FUNCTION public.bonus_art_vue(integer) OWNER TO delain;