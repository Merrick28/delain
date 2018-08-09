CREATE OR REPLACE FUNCTION public.get_etat_objet(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************************/
/* fonction get_etat_objet : retourne l’état d’un objet en texte   */
/*   on passe en paramètres :                                      */
/*   $1 = état de l’objet analysé                                  */
/* on a en retour une chaine html                                  */
/*******************************************************************/
/* Création Blade 02/01/2010 							              						*/
/*******************************************************************/

declare
---------------------------------------------------------------------
-- variable de retour 
---------------------------------------------------------------------
	code_retour text;
---------------------------------------------------------------------
-- variables
---------------------------------------------------------------------	
	v_etat alias for $1;

begin

	code_retour := 'Comme neuf';
	if (v_etat < 10) then
		code_retour := 'Déplorable';
	elsif (v_etat < 35) then
		code_retour := 'Médiocre';
	elsif (v_etat < 50) then
		code_retour := 'Mauvais';
	elsif (v_etat < 70) then
		code_retour := 'Bon';
	elsif (v_etat < 90) then
		code_retour := 'Excellent';
	end if;
	return code_retour;
end;	$function$

CREATE OR REPLACE FUNCTION public.get_etat_objet(numeric)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************************/
/* fonction get_etat_objet : retourne l’état d’un objet en texte   */
/*   on passe en paramètres :                                      */
/*   $1 = état de l’objet analysé                                  */
/* on a en retour une chaine html                                  */
/*******************************************************************/
/* Création Blade 02/01/2010 							              						*/
/*******************************************************************/

declare
---------------------------------------------------------------------
-- variable de retour 
---------------------------------------------------------------------
	code_retour text;
---------------------------------------------------------------------
-- variables
---------------------------------------------------------------------	
	v_etat alias for $1;

begin

	code_retour := 'Comme neuf';
	if (v_etat < 10) then
		code_retour := 'Déplorable';
	elsif (v_etat < 35) then
		code_retour := 'Médiocre';
	elsif (v_etat < 50) then
		code_retour := 'Mauvais';
	elsif (v_etat < 70) then
		code_retour := 'Bon';
	elsif (v_etat < 90) then
		code_retour := 'Excellent';
	end if;
	return code_retour;
end;	$function$

