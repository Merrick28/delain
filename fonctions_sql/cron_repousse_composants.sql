CREATE OR REPLACE FUNCTION public.cron_repousse_composants()
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cron_repousse_composants : gère la repousse des      */
/*                                         composants de potion  */
/*                                                               */
/*****************************************************************/
/* Créé le 14/01/2007                                            */
/*****************************************************************/
declare
		ligne record;

begin
	for ligne in select ingrpos_cod,ingrpos_chance_crea from ingredient_position where ingrpos_qte < ingrpos_max loop
	if lancer_des(1,100) <= ligne.ingrpos_chance_crea then
	update ingredient_position set ingrpos_qte = ingrpos_qte + 1 where ingrpos_cod = ligne.ingrpos_cod;
	end if;
	end loop;

end;$function$

