CREATE OR REPLACE FUNCTION public.getparm_n(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function getparm_n : retourne la valeur du paramètre passé    */
/* en $1 si celui ci est numérique                               */
/* On passe en paramètre le param souhaité                       */
/* Le code sortie est :                                          */
/*    -1 = paramètre inexistant                                  */
/* autre = la valeur du paramètre                                */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	v_retour integer;        
   v_parametre alias for $1;
   compt integer;
        
begin
	select into v_retour parm_valeur from parametres
		where parm_cod = v_parametre;
	if not found then
		v_retour := -1;
		return v_retour;
	end if;
	return v_retour;
end;
$function$

