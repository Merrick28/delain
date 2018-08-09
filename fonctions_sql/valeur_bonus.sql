CREATE OR REPLACE FUNCTION public.valeur_bonus(integer, text)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$-- Retourne la valeur numérique du bonus choisi pour le perso.
-- $1 = Le code du perso
-- $2 = Le type de bonus
-- Retour: La valeur numérique, somme des bonus positif et négatif

declare 
    v_perso alias for $1;
    v_type alias for $2;
    v_retour numeric;
begin
    select into v_retour coalesce(sum(bonus_valeur), 0) from bonus 
        where bonus_perso_cod = v_perso and bonus_tbonus_libc = v_type;
    if not found then
	v_retour := 0;
    end if;
    return v_retour;
end;$function$

