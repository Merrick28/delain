CREATE OR REPLACE FUNCTION public.temple_soins(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/********************************************/
/* fonction temple_soins                    */
/*   provoque des soins dans un temple      */
/* on passe en paramètres :                 */
/*   $1 = perso_cod                         */
/*   $2 = nombre de PV                      */
/*   $3 = cout                              */
/********************************************/
/* on a en sortie une chaine séparée par ;  */
/*   0 = retour (0 OK, -1 BAD)              */
/*   1 = description de l erreur si BAD     */
/********************************************/
/* Créé le 27/05/2003                       */
/********************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_soins alias for $2;
	v_cout alias for $3;
-- infos du joueur
	v_pv integer;
	v_pv_max integer;
	v_or integer;
	nouveau_pv integer;
	texte_evt text;
	
begin
/********************************************/
/* Etape 1 : on cherche les infos du perso  */
/********************************************/
	select into v_or,v_pv,v_pv_max perso_po,perso_pv,perso_pv_max from perso
		where perso_cod = personnage;
/********************************************/
/* Etape 2 : besoin de soins ?              */
/********************************************/
	if v_pv = v_pv_max then
		code_retour := '-1;Vous n''avez pas besoin de soins !';
		return code_retour;
	end if;
/********************************************/
/* Etape 3 : assez de brouzoufs ?           */
/********************************************/
	if v_or < v_cout then
		code_retour := '-1;Vous n''avez pas assez de brouzoufs pour vous payer ces soins.';
		return code_retour;
	end if;
/********************************************/
/* Etape 4 : on y va.                       */
/********************************************/
	nouveau_pv := v_pv + v_soins;
	if nouveau_pv > v_pv_max then
		nouveau_pv = v_pv_max;
	end if;
	update perso set perso_pv = nouveau_pv,perso_po = perso_po - v_cout where perso_cod = personnage;
	texte_evt := '[cible] s''est fait soigner dans un dispensaire.';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(nextval('seq_levt_cod'),20,now(),1,personnage,texte_evt,'O','O',personnage,personnage);

	return code_retour;
end;
$function$

