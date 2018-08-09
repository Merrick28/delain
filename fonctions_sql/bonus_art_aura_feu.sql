CREATE OR REPLACE FUNCTION public.bonus_art_aura_feu(integer)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$/*******************************************/
/* bonus_art_aura_feu                      */
/*  params : $1 = perso_cod                */
/*******************************************/
declare
  code_retour numeric;
  personnage alias for $1;
  ligne record;
  temp integer;
begin
  code_retour := 0;
  for ligne in
  select obj_cod,obj_aura_feu
  from perso_objets,objets
  where perobj_perso_cod = personnage
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and coalesce(obj_aura_feu,0) != 0 loop
    -- boucle
    code_retour := code_retour + coalesce(ligne.obj_aura_feu,0);
    temp := use_artefact(ligne.obj_cod);
    -- fin boucle
  end loop;
  return code_retour;
end;$function$

