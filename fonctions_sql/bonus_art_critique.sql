--
-- Name: bonus_art_critique(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function bonus_art_critique(integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/*******************************************/
/* bonus_art_critique                      */
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
  select obj_cod,obj_critique
  from perso_objets,objets
  where perobj_perso_cod = personnage
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and coalesce(obj_critique,0) != 0 loop
    -- boucle
    code_retour := code_retour + coalesce(ligne.obj_critique,0);
    -- fin boucle
  end loop;
  return LEAST(100, GREATEST(0, code_retour + valeur_bonus(personnage, 'PRO')));  -- ajout des bonus/malus de protection (entre 0 et 100)
end;$_$;


ALTER FUNCTION public.bonus_art_critique(integer) OWNER TO delain;
