CREATE OR REPLACE FUNCTION public.bonus_art_critique(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*******************************************/
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
  return code_retour;
end;$function$

