CREATE OR REPLACE FUNCTION public.cron_dissip_monstre()
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cron_dissip_monstre : détruit les squelettes du -8   */
/*            qui resterait trop longtemps faute d'avoir été tués*/
/*                                                               */
/*****************************************************************/
/* Créé le 03/06/2006                                            */
/*****************************************************************/
declare
			perso_evt record;

begin


for perso_evt in select perso_cod from perso,perso_position,positions,race 
			where ppos_pos_cod = pos_cod 
			and pos_etage in (-8,8,9,10,11,12,19)
			and perso_type_perso = 2 
			and ppos_perso_cod = perso_cod 
			and perso_race_cod = race_cod 
			and race_cod = 17 
			and not exists (select levt_perso_cod1,levt_tevt_cod from ligne_evt where levt_tevt_cod != 2 and levt_perso_cod1 = perso_cod) 
			group by perso_cod
			
			loop
			perform dissipation(perso_evt.perso_cod);
			end loop;
end;			$function$

