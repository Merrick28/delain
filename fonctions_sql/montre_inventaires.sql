CREATE OR REPLACE FUNCTION public.montre_inventaires()
 RETURNS integer
 LANGUAGE plpgsql
 STRICT
AS $function$-- Fonction montre_inventaires: Restaure les inventaires des joueurs cachés dans une autre table

declare
    ligne_inventaire record;
    
begin
    for ligne_inventaire in 
        select perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe, perobj_dfin
        from perso_objets_avril loop -- where perobj_perso_cod = 2926870 loop -- TODO Supprimer clause WHERE
        
        if not exists (select perso_cod from perso where perso_cod = ligne_inventaire.perobj_perso_cod) then
            insert into log_1_avr (nom, nombre) values ('Disparu', ligne_inventaire.perobj_perso_cod);
        else
        delete from perso_objets where perobj_obj_cod = ligne_inventaire.perobj_obj_cod;
        insert into perso_objets (perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe, perobj_dfin) 
            values (ligne_inventaire.perobj_perso_cod, ligne_inventaire.perobj_obj_cod,
                ligne_inventaire.perobj_identifie, ligne_inventaire.perobj_equipe,
                ligne_inventaire.perobj_dfin);
        end if;
    end loop;

    return 0;
end;$function$

