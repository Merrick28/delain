CREATE OR REPLACE FUNCTION public.cache_inventaires()
 RETURNS integer
 LANGUAGE plpgsql
 STRICT
AS $function$-- Fonction cache_inventaires: Cache les inventaires des joueurs dans une autre table

declare
  ligne_inventaire record;

begin
  return 0; -- C''est fini le 1er avril !
  for ligne_inventaire in
  select perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe,
    case when perobj_dfin is null then null else perobj_dfin + '24 hours'::interval end as perobj_nouvelle_date_fin
  from perso_objets where true loop -- perobj_perso_cod = 2926870 loop -- TODO Supprimer clause WHERE

    insert into perso_objets_avril (perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe, perobj_dfin)
    values (ligne_inventaire.perobj_perso_cod, ligne_inventaire.perobj_obj_cod,
            ligne_inventaire.perobj_identifie, ligne_inventaire.perobj_equipe,
            ligne_inventaire.perobj_nouvelle_date_fin);
  end loop;

  -- delete from perso_objets where perobj_perso_cod = 2926870; -- TODO Supprimer clause WHERE
  return 0;
end;$function$

