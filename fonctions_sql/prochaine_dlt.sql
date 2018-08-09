CREATE OR REPLACE FUNCTION public.prochaine_dlt(integer)
 RETURNS timestamp with time zone
 LANGUAGE plpgsql
AS $function$declare
    v_perso alias for $1;
    v_temps_tour integer;
    v_pv integer;
    v_pv_max integer;
    v_min_pv_manquants integer;
    v_poids numeric;
    v_poids_max integer;
    v_pa integer;
    v_util_pa_restants integer;
    v_dlt timestamp;
    v_variation integer; -- Variation en minutes

    v_ignore_malus_blessures integer;

begin
    select into v_temps_tour,
        v_pv,
        v_pv_max,
        v_poids,
        v_poids_max,
        v_pa,
        v_util_pa_restants,
        v_dlt

        perso_temps_tour,
        perso_pv,
        perso_pv_max,
        get_poids(v_perso),
        perso_enc_max,
        perso_pa,
        perso_utl_pa_rest,
        perso_dlt
    from perso
    where perso_cod = v_perso;

    v_variation := 0;
    if ( v_util_pa_restants = 1 ) then
        v_variation := v_variation - round(v_temps_tour*v_pa/24);
    end if;
    
    v_min_pv_manquants := max(0, v_pv_max - v_pv - calcul_regen_max(v_perso));
    if ( valeur_bonus(v_perso, 'PDL') = 0 and v_min_pv_manquants != 0 ) then
        v_variation := v_variation + round(v_temps_tour*(v_min_pv_manquants)/(v_pv_max*4));
    end if;
    
    -- Bleda 6/2/11 Distortion temporelle
    v_variation := v_variation + valeur_bonus(v_perso, 'DIS');
    v_variation := v_variation - valeur_bonus(v_perso, 'DIT');
     
    if ( v_poids > v_poids_max) then
        v_variation := v_variation + round((v_temps_tour*v_poids/v_poids_max - v_temps_tour)/2);
    end if;

    return v_dlt::timestamp + (to_char(v_temps_tour + v_variation,'999999') || ' minutes')::interval;

end;
$function$

