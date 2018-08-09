CREATE OR REPLACE FUNCTION public.mission_modifie_statut(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_modifie_statut                           */
/*   Modifie le statut d’une mission                         */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/*   $2 = nouveau statut                                     */
/*   $3 = avancement                                         */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 12/06/2013                                        */
/*************************************************************/
declare
	code_retour text;          -- Le résultat, affichable, de la fonction
	code_mission alias for $1; -- Le code de la mission à valider
	v_nouveau_statut alias for $2; -- Le nouveau statut de la mission
	v_avancement alias for $3; -- L’avancement de la mission, de 0 à 9, permettant de gérer les réussites partielles.
	
	v_ancien_statut integer;   -- Le statut actuel de la mission
	v_retard boolean;          -- La mission est-elle en retard ?
	v_statut_temp integer;     -- variable temporaire de statut
	v_avancement_temp integer; -- variable temporaire d’avancement
	v_fac_cod integer;         -- code de la faction
	v_faction text;            -- nom de la faction
begin
	code_retour := ''; -- Par défaut, aucun retour
	select into
		v_ancien_statut, v_fac_cod, v_retard
		mpf_statut, mpf_fac_cod, mpf_date_fin < now()
	from mission_perso_faction_lieu where mpf_cod = code_mission;

	-- On garde le meilleur avancement enregistré
	v_avancement_temp := max(v_avancement, v_ancien_statut % 10);
	v_statut_temp := v_nouveau_statut;

	-- En cas de retard alors que la mission est toujours en cours, on indique un échec
	if v_nouveau_statut = 10 AND v_ancien_statut / 10 = 1 AND v_retard then
		v_statut_temp := 30;

	-- On vérifie si le nouveau statut est bien supérieur à l’ancien.
	-- Sinon, on sort sans rien faire.
	elsif v_ancien_statut >= v_nouveau_statut + v_avancement then
		return code_retour;
	end if;

	-- Si la mission est validée, on ne peut plus la changer
	if v_ancien_statut >= 40 then
		return code_retour;
	end if;

	-- Si la mission est échouée, on ne peut pas changer son avancement
	if (v_ancien_statut >= 30 AND v_ancien_statut < 40) AND v_nouveau_statut = 30 then
		return code_retour;
	end if;
	
	-- Mission toujours en cours : en cas de non-retard, pas de changements au statut en entrée.

	-- En cas de réussite totale, on ramène l’avancement à 0.
	if v_statut_temp in (20, 40) then
		v_avancement_temp := 0;
	end if;

	update mission_perso_faction_lieu set mpf_statut = v_statut_temp + v_avancement_temp where mpf_cod = code_mission;

	-- On gère les différents cas pour écrire un message
	select into v_faction fac_nom from factions where fac_cod = v_fac_cod;
	if v_statut_temp = 10 and v_avancement_temp > 0 then
		code_retour := '<hr />Vous avez avancé dans la réalisation de votre mission pour « ' || v_faction || ' ».';
	elsif v_statut_temp = 20 then
		code_retour := '<hr />Félicitations ! Vous avez mené à bien votre mission au nom de la faction « ' || v_faction || ' ».<br />
			La récompense vous sera versée si vous vous rendez dans un des lieux où elle officie.';

	elsif v_statut_temp = 30 and v_avancement_temp = 0 then
		code_retour := '<hr />Vous avez échoué dans votre mission... Cela signifiera probablement une petite sanction dans votre 
			avancement auprès de « ' || v_faction || ' ».<br />Oserez-vous y retourner ?';

	elsif v_statut_temp = 30 and v_avancement_temp > 0 then
		code_retour := '<hr />Vous avez pu réaliser votre mission, mais seulement partiellement.
			Est-ce que ça va passer auprès des représentants de « ' || v_faction || ' » ?<br />Libre à vous de tenter le coup...';
	end if;
	
	return code_retour;
end;$function$

