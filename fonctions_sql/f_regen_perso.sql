-- FUNCTION: public.f_regen_perso(integer)

-- DROP FUNCTION public.f_regen_perso(integer);

CREATE OR REPLACE FUNCTION public.f_regen_perso(
	integer)
    RETURNS integer
    LANGUAGE 'plpgsql'

    COST 100
    VOLATILE 
AS $BODY$/*****************************************************************/
/* function f_regen_perso : retourne la valeur de l armure du   */
/*   perso passé en $1                                           */
/* Le code sortie est un entier                                  */
/*****************************************************************/
/* Créé le 06/05/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour text;
	v_personnage alias for $1;
  v_regen integer;
  v_perso_pv_max integer;
  v_perso_des_regen integer;
  v_niveau_vampire integer;

begin
  v_regen := 0;

  -- le BM ORG, bloque la Régéen
  IF valeur_bonus(v_personnage, '0RG') = 0 THEN

      select into v_perso_pv_max, v_niveau_vampire, v_perso_des_regen
        perso_pv_max, perso_niveau_vampire, perso_des_regen
      from perso
      where perso_cod = v_personnage;

      -- les bonus/malus
      v_regen := v_regen + valeur_bonus(v_personnage, 'REG') + valeur_bonus(v_personnage, 'PRG');

      -- l'équipement
      v_regen := v_regen + bonus_art_reg(v_personnage) ;

      -- bonus sur les PV (sauf vampire)
      if v_niveau_vampire = 0 then
        v_regen := v_regen + LEAST(25, FLOOR(v_perso_des_regen * v_perso_pv_max / 100));
      end if;

      --
      if v_regen < 0 then
        v_regen := 0;
      end if;

	end if;

	code_retour := v_regen ;
	return code_retour;
end;
$BODY$;

ALTER FUNCTION public.f_regen_perso(integer)
    OWNER TO delain;

COMMENT ON FUNCTION public.f_regen_perso(integer)
    IS 'Calcule le bonus de régene totale d’un personnage';
