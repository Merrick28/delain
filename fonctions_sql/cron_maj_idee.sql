CREATE OR REPLACE FUNCTION public.cron_maj_idee()
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cron_maj_idee : modifie le statut idee_new		 */ 
/*			si la date de MAJ est  < à 10 jours      */
/*                                                               */
/*****************************************************************/
/* Créé le 29/01/2008                                            */
/*****************************************************************/
declare
		ligne record;

begin
	for ligne in select idee_cod from idees where idee_new = 'new' and idee_date_maj + '10 days'::interval < now() loop
	update idees set idee_new  = '' where idee_cod = ligne.idee_cod;
	end loop;

end;$function$

