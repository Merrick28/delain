-- FUNCTION: public.f_temps_tour_perso(integer)

-- DROP FUNCTION public.f_temps_tour_perso(integer);

CREATE OR REPLACE FUNCTION public.f_temps_tour_perso(
	integer)
    RETURNS integer
    LANGUAGE 'plpgsql'

    COST 100
    VOLATILE 
AS $BODY$/*****************************************************************/
/* function f_temps_tour_perso : retourne la valeur de l armure du   */
/*   perso passé en $1                                           */
/* Le code sortie est un entier                                  */
/*****************************************************************/
/* Créé le 06/05/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour text;
	v_personnage alias for $1;
  v_temps_tour integer;
  temps_tour integer;
  pv_max integer;
  pv_actuel integer;
  poids_actuel numeric;
  poids_max integer;

begin

  -- aject un Jus De Chronomètre, ma DLT est égale à la valeur du bonus
  v_temps_tour := valeur_bonus(v_personnage, 'JDC') ;

  if v_temps_tour = 0 then


      select into temps_tour, pv_max, pv_actuel, poids_actuel, poids_max
                  perso_temps_tour, perso_pv_max, perso_pv, get_poids(v_personnage), perso_enc_max
          from perso
          where perso_cod = v_personnage;

      -- temps de la DLT de base
      v_temps_tour := temps_tour ;

      /* on calcule du temps liés aux blessure (sauf si potion PDL)  */
      if valeur_bonus(v_personnage, 'PDL') = 0 then
        v_temps_tour := v_temps_tour + round( (temps_tour*(pv_max-pv_actuel)/pv_max) / 4 );
      end if;

      /* on calcule le temps lié au poids */
      if poids_actuel > poids_max then
        v_temps_tour := v_temps_tour + LEAST(getparm_n(127), round((((temps_tour*poids_actuel)/poids_max) - temps_tour) / 2));
      end if;

      -- distortion temporelle acceleration
      v_temps_tour := v_temps_tour +  round(valeur_bonus(v_personnage, 'DIT'));

      -- distortion temporelle ralentissement
      v_temps_tour := v_temps_tour +  round(valeur_bonus(v_personnage, 'DIS'));

      if v_temps_tour < 0 then
        v_temps_tour := 0;
      end if;

  end if;

	code_retour := v_temps_tour ;
	return code_retour;
end;
$BODY$;

ALTER FUNCTION public.f_temps_tour_perso(integer)
    OWNER TO delain;

COMMENT ON FUNCTION public.f_temps_tour_perso(integer)
    IS 'Calcule du temps du tour actuel d’un personnage';
