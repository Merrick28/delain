CREATE OR REPLACE FUNCTION public.reduc_compt_pvp()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* function reduc_compt_pvp                             */
/*   permet de réduire le compteur pvp si un temps      */
/*   égal à la dlt du perso est passé depuis la         */
/*   dernière modif du compteur                         */
/********************************************************/
declare
	ligne record;
	code_retour text;
	v_compt integer;
begin
	v_compt := 0;
	for ligne in select perso_cod
		from perso
		where perso_type_perso = 1
		and perso_actif = 'O'
		and perso_compt_pvp >= 1
		and perso_dmodif_compt_pvp + (to_char(perso_temps_tour,'99999999')||' minutes')::interval < now() loop
	--
	-- a priori, maintenant, on a les bons persos dont on doit baisser le compteur	
	--
		update perso
			set perso_compt_pvp = perso_compt_pvp -1,
			perso_dmodif_compt_pvp = now()
			where perso_cod = ligne.perso_cod;
		v_compt := v_compt + 1;
	end loop;
	code_retour := trim(to_char(v_compt,'999999999999999'))||' compteurs baissés';
	return code_retour;
end;$function$

