CREATE OR REPLACE FUNCTION public.insere_evenement(integer, integer, integer, text, character varying, text)
 RETURNS void
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* insere_evenement : cree un evenement                   */
/* on passe en paramètres :                               */
/*   $1 = le perso_cod de la source                       */
/*   $2 = le perso_cod de la cible                        */
/*   $3 = le type d’event                                 */
/*   $4 = le texte de l’event                             */
/*   $5 = événement publique                              */
/*   $6 = paramètres supplémentaires                      */
/**********************************************************/

declare
	source alias for $1;   -- perso_cod de la source
	cible alias for $2;    -- perso_cod de la cible
	type_evt alias for $3; -- type d event
	texte_evt alias for $4;-- texte de l event
	visible alias for $5;  -- 'O' pour un événement publique, 'N' sinon
	parametres alias for $6;-- Paramètres supplémentaires
begin
	perform insere_evenement(source, cible, type_evt, texte_evt, visible, 'N', parametres);
end;$function$

CREATE OR REPLACE FUNCTION public.insere_evenement(integer, integer, integer, text, character varying, character varying, text)
 RETURNS void
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* insere_evenement : cree un evenement                   */
/* on passe en paramètres :                               */
/*   $1 = le perso_cod de la source                       */
/*   $2 = le perso_cod de la cible                        */
/*   $3 = le type d’event                                 */
/*   $4 = le texte de l’event                             */
/*   $5 = événement publique                              */
/*   $6 = masquer sur la cible ('O' ou 'N')               */
/*   $7 = paramètres supplémentaires                      */
/**********************************************************/

declare
	source alias for $1;    -- perso_cod de la source
	cible alias for $2;     -- perso_cod de la cible
	type_evt alias for $3;  -- type d event
	texte_evt alias for $4; -- texte de l event
	visible alias for $5;   -- 'O' pour un événement publique, 'N' sinon
	masquer alias for $6;   -- 'O' pour masquer l’événement à la cible, 'N' sinon
	parametres alias for $7;-- Paramètres supplémentaires
begin
	-- SOURCE
	insert into ligne_evt(levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
	values(type_evt, now(), source, texte_evt, 'O', visible, source, cible, parametres);
	-- CIBLE
	if (source != cible AND masquer = 'N') then
		insert into ligne_evt(levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
		values(type_evt, now(), cible, texte_evt, 'N', visible, source, cible, parametres);
	end if;	
end;$function$

