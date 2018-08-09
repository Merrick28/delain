CREATE OR REPLACE FUNCTION public.calcul_regen_max(integer)
 RETURNS integer
 LANGUAGE plpgsql
 STRICT
AS $function$-- Calcule la régénération maximale du perso

declare
    code_retour integer;
    personnage alias for $1;
    ligne record;
begin
    code_retour := 0;

    select into code_retour case when perso_niveau_vampire = 0 then
            perso_des_regen * perso_valeur_regen
            + perso_amelioration_regen + valeur_bonus(perso_cod, 'REG')
            -- bonus de x ou x est le nombre de dés de regen/100 * pv max
            + min(25, cast(floor((perso_pv_max * perso_des_regen) / 100) as int))
            + valeur_bonus(perso_cod, 'PRG')
       else 0 end
         from perso where perso_cod = personnage;
    if not found then
        return 0;
    end if;

    -- Artefacts de régénération
    for ligne in
        select obj_cod,obj_regen
        from perso_objets,objets
        where perobj_perso_cod = personnage
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and coalesce(obj_regen,0) != 0 loop
        code_retour := code_retour + coalesce(ligne.obj_regen,0);
    end loop;

    if valeur_bonus(personnage, '0RG') != 0 then
        code_retour := 0;
    end if;

    return code_retour;
end;
$function$

