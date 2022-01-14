--
-- Name: allonge_temps(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function allonge_temps(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* Fonction allonge_temps : calcule l augmentation du temps de   */
/*   tour en fonction des blessures                              */
/* On passe en paramètres :                                      */
/*    1 : le perso_cod                                           */
/* On a en sortie une chaine séparée par des ;                   */
/*    1 = nb heures                                              */
/*    2 = nb minutes                                             */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
  temps_tour integer;
  personnage alias for $1;
  code_retour text;
  temp_ajout_temps numeric;
  temp2 integer;
  pv_actuel perso.perso_pv%type;
  pv_max perso.perso_pv_max%type;
  compt integer;
begin
  code_retour := '0;'; -- par défaut, tout est OK
  select into pv_actuel,pv_max,temps_tour perso_pv,perso_pv_max,perso_temps_tour from perso
  where perso_cod = personnage;
  temp_ajout_temps := (temps_tour*(pv_max-pv_actuel)/pv_max); /* dégats */
  temp_ajout_temps := temp_ajout_temps / 4;
  temp2 := round(temp_ajout_temps);
  code_retour := calcul_temps(temp2);
  return code_retour;
end;
$_$;


ALTER FUNCTION public.allonge_temps(integer) OWNER TO delain;