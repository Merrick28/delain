CREATE OR REPLACE FUNCTION public.depot_banque(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* fonction depot_banque                        */
/*   fait un dépot en banque                    */
/* on passe en paramètres :                     */
/*   $1 = perso_cod                             */
/*   $2 = qte or déposée                        */
/* on a en retour une chaine séparée par ;      */
/*   pos 0 = code retour (0 OK, -1 BAD)         */
/*   pos 1 = description de l erreur            */
/************************************************/
/* Créé le 26/05/2003                           */
/************************************************/
declare
	code_retour text;
	personnage alias for $1;
	qte_or alias for $2;
	texte_evt text;
	num_compte integer;
	or_actuel integer;
	nom_perso perso.perso_nom%type;

begin
	code_retour := '0';
/*************************************************/
/* Etape 1 : on récupère les infos               */
/*************************************************/
	if qte_or < 0 then
		code_retour := '-1;Montant négatif. Bien essayé.';
		return code_retour;
	end if;
	select into or_actuel,nom_perso perso_po,perso_nom from perso
		where perso_cod = personnage;
	if or_actuel < qte_or then
		code_retour := '-1;Vous n''avez pas assez d''or pour faire de dépot.';
		return code_retour;
	end if;
/*************************************************/
/* Etape 2 : on recherche le compte              */
/*************************************************/
	select into num_compte pbank_cod from perso_banque
		where pbank_perso_cod = personnage;
	if num_compte is null then
		num_compte := nextval('seq_pbank_cod');
		insert into perso_banque(pbank_cod,pbank_perso_cod,pbank_or)
			values(num_compte,personnage,qte_or);
	else
		update perso_banque
			set pbank_or = pbank_or + qte_or
			where pbank_cod = num_compte;
	end if;
/*************************************************/
/* Etape 3 : on vire l or du perso               */
/*************************************************/
	update perso set perso_po = or_actuel - qte_or
		where perso_cod = personnage;
/*************************************************/
/* Etape 4 : on met un évènement                 */
/*************************************************/
	texte_evt := nom_perso||' a déposé '||trim(to_char(qte_or,'99999999'))||' brouzoufs en banque.';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values(nextval('seq_levt_cod'),19,now(),1,personnage,texte_evt,'O','N');
	return code_retour;
end;
		$function$

