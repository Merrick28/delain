CREATE OR REPLACE FUNCTION public.allonge_temps_poids(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* Fonction allonge_temps_poids : calcule l augmentation du      */
/*   temps de tour en fonction du poids                          */
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
  poids_actuel numeric;
  poids_max integer;
  compt integer;
begin
  code_retour := '0;'; -- par défaut, tout est OK
  select into poids_actuel,poids_max,temps_tour get_poids(personnage),perso_enc_max,perso_temps_tour from perso
  where perso_cod = personnage;
  if poids_actuel <= poids_max then
    code_retour := '0;0;';
    return code_retour;
  end if;
  temp_ajout_temps := (temps_tour*poids_actuel)/poids_max;
  temp_ajout_temps := temp_ajout_temps - temps_tour;
  temp_ajout_temps := temp_ajout_temps / 2;
  temp_ajout_temps := round(temp_ajout_temps);
  code_retour := calcul_temps(temp_ajout_temps);
  return code_retour;
end;
$function$

