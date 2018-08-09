CREATE OR REPLACE FUNCTION public.dieu_avancement(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction dieu_avancement                            */
/*  on passe en params                                 */
/*  $1 = le rang                                       */
/*  $2 = les points de prière                          */
/* on a en retour un entier compris entre 0 et 100     */
/*******************************************************/
declare
	v_rang alias for $1;          -- Rang actuel
	v_points alias for $2;        -- Points de prière actuels
	v_avancement integer;         -- avancement du prêtre dans son niveau
	v_limite_inf integer;         -- seuil inférieur du grade du prêtre
	v_limite_sup integer;         -- seuil supérieur du grade du prêtre
	
begin
	v_limite_inf := case when v_rang = 0 then 0
			when v_rang = 1 then getparm_n(51)
			when v_rang = 2 then getparm_n(52)
			when v_rang = 3 then getparm_n(53)
			when v_rang = 4 then getparm_n(54)
			else -1 end;
	v_limite_sup := case when v_rang = 0 then getparm_n(51)
			when v_rang = 1 then getparm_n(52)
			when v_rang = 2 then getparm_n(53)
			when v_rang = 3 then getparm_n(54)
			else -1 end;
	if (v_limite_sup = v_limite_inf) then 
		v_avancement := 0;
	else
		v_avancement := ((v_points - v_limite_inf)::numeric / (v_limite_sup - v_limite_inf)::numeric * 100)::integer;
	end if;

	v_avancement := min(100, v_avancement);
	v_avancement := max(0, v_avancement);

	v_avancement := case when v_limite_inf = -1 OR v_limite_sup = -1 then 0 else v_avancement end;

	return v_avancement;
end;$function$

