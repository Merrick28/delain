CREATE OR REPLACE FUNCTION public.supprimer_ameliorations_perso(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function supprimer_ameliorations_perso :                      */
/*     Supprime toutes les amélio du perso                       */
/*    et le replace au niveau 1                                  */
/* On passe en paramètre le perso_cod                            */
/*****************************************************************/
/* Liste des modifications :                                     */
/*   le 23/01/2013 : création                                    */
/*****************************************************************/
declare
	personnage alias for $1;
	v_race integer;
begin
	-- réceptacles
	delete from recsort where recsort_perso_cod = personnage;
	
	update perso set
		perso_niveau = 1,
		perso_pv = (2 * perso_con_init),
		perso_pv_max = (2 * perso_con_init),
		perso_temps_tour = 720,
		perso_for = coalesce(perso_for_init, 0),
		perso_dex = coalesce(perso_dex_init, 0),
		perso_int = coalesce(perso_int_init, 0),
		perso_con = coalesce(perso_con_init, 0),
		perso_amelioration_vue = 0,
		perso_des_regen = 1,
		perso_amelioration_degats = 0,
		perso_amelioration_armure = 0,
		perso_amel_deg_dex = 0,
		perso_nb_amel_repar = 0,
		perso_nb_receptacle = 0,
		perso_amelioration_nb_sort = 0,
		perso_nb_amel_comp = 0
	where perso_cod = personnage;

	-- compétences
	select into v_race perso_race_cod
	from perso
	where perso_cod = personnage;
	if v_race in (1, 3) then
		delete from perso_competences where pcomp_perso_cod = personnage and pcomp_pcomp_cod IN (25,63,66,72,75,61,64,67,73,76,62,65,68,74,77);
	else
		delete from perso_competences where pcomp_perso_cod = personnage and pcomp_pcomp_cod IN (63,66,72,75,64,67,73,76,65,68,74,77);
		update perso_competences set pcomp_pcomp_cod = 25 where pcomp_perso_cod = personnage and pcomp_pcomp_cod IN (61, 62);
	end if;

	return 1;
end;$function$

