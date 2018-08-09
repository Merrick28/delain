CREATE OR REPLACE FUNCTION public.sitting(integer, integer, timestamp with time zone, timestamp with time zone)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function sitting : Déclaration de sitting                     */
/* On passe en paramètres                                        */
/*   $1 = compte sitteur                                         */
/*   $2 = compte sitté                                           */
/*   $3 = Durée du sitting, convertit en heure                   */
/*   $4 = Date de debut du sitting                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créée le 07/12/2006                                           */
/* Liste des modifications :                                     */
/*****************************************************************/

declare
-------------------------------------------------------------
-- variables d'entrée
-------------------------------------------------------------
	compte_sitteur alias for $1;		-- Compte du sitteur ==> Compte_cod
	compte_sit alias for $2;				-- Compte du sitté ==> Compte_nom
	date_deb alias for $3;					-- Date de début du sitting
	date_fin alias for $4;					-- Date de fin du sitting
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	sitting_encours integer; 				-- si le compte est déjà sitté ou prévu de l'être
	sitteur_encours integer;				-- si le sitteur a déjà un sitting en cours
	compte_exist integer;						-- test d'existance du compte sitteur inscrit
	code_retour text;
begin

/*Prévoir interface pour connaitre les sittings déclarés*/
/*Prévoir une interface pour annuler un sitting programmé, mais pas un sitting en cours*/

/* Première étape : controle du temps annoncé de sitting.
Si supérieur à 5 jours, message d'anomalie */

/*calcul de la date de fin théorique*/
code_retour := '';

select into sitting_encours csit_compte_sitte from compte_sitting 
			where date_deb >= csit_ddeb 
			and date_deb <= csit_dfin 
			and compte_sit = csit_compte_sitte;
select into sitteur_encours csit_compte_sitteur from compte_sitting,compte 
			where date_deb < csit_ddeb 
			and date_deb > csit_dfin 
			and compte_sitteur = compt_nom
			and csit_compte_sitteur = compt_cod;
select into compte_exist compt_cod from compte 
		where compt_cod = compte_sitteur;
/*Vérification que le compte sitteur existe :*/
if compte_exist is null then
		code_retour := code_retour||'********************************************<br><b>Le compte sitteur que vous indiquez n''existe pas</b><br>********************************************';
		return code_retour;
/* Vérification de la durée de sitting */
/*Elsif duree > 120 then
		code_retour := code_retour||'********************************************<br><b>Vous ne pouvez pas déclarer une durée de sitting supérieure à 5 jours</b><br>********************************************';
		return code_retour;*/
/* Deuxième étape : vérif que le compte ne doit pas déjà être sitté dans la période mentionnée*/		
elsif sitting_encours is not null  then
		code_retour := code_retour||'********************************************<br><b>Ce compte doit déjà être sitté dans la période mentionnée.<br>Un seul sitting est autorisé par compte.</b><br>********************************************';
		return code_retour;
/*Quatrième étape : vérif que le sitteur n'a pas déjà un autre compte en sitting*/		
elsif sitteur_encours is not null then
		code_retour := code_retour||'********************************************<br><b>Ce compte doit déjà sitter un autre compte que le votre, et il n''est pas autorisé de sitter plus d''un compte à la fois.</b><br>********************************************';
		return code_retour;
/* Cinquième étape : intégration dans la table de sitting */		
else 		
		insert into compte_sitting (csit_compte_sitte,csit_compte_sitteur,csit_ddeb,csit_dfin,csit_ddemande) 
		values (compte_sit,compte_sitteur,date_deb,date_fin,now());
		code_retour := code_retour||'Votre demande de sitting a bien été prise en compte<br>Un message a été envoyé à l''intention de votre sitteur.';
		return code_retour;
end if;
end;$function$

