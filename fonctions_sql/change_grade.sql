CREATE OR REPLACE FUNCTION public.change_grade(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* change_grade : fait un passage de grade               */
/*   on passe en params                                  */
/*   $1 = perso_cod                                      */
/*   $2 = dieu_cod (pour vérif)                          */
/* on a en retour une chaine html                        */
/*********************************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_dieu alias for $2;
	v_pa integer;				-- Pa du perso
	v_temp_dieu integer;
	v_niveau integer;
	v_points integer;
	v_limite integer;			-- nombre de points pour passer au dessus
	v_nom_grade text;
	texte_evt text;
	v_type_perso integer;

begin
	-- vérifs d'usage
	-- PA
	select into v_pa,v_type_perso
		perso_pa,perso_type_perso
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := '<p>Anomalie ! Perso non trouvé !';
		return code_retour;
	end if;
	if v_type_perso = 3 then
		code_retour := '<p>Sacrilège !!! Comment voulez vous qu''une créature aussi insignifiante puisse un jour devenir disciple d''un dieu !!!';
		return code_retour;
	end if;
	if v_pa < getparm_n(55) then
		code_retour := '<p>Anomalie ! Pas assez de PA !';
		return code_retour;
	end if;
	-- religion
	select into
		v_temp_dieu,
		v_niveau,
		v_points
		dper_dieu_cod,
		dper_niveau,
		dper_points
		from dieu_perso
		where dper_perso_cod = personnage;
	if not found then
		code_retour := '<p>Anomalie ! Religion non trouvée !';
		return code_retour;
	end if;
	if v_temp_dieu != v_dieu then
		code_retour := '<p>Anomalie entre le dieu en base et celui passé en paramètres !';
		return code_retour;
	end if;
	v_limite := 0;
	if v_niveau = 0 then
		v_limite := getparm_n(51);
		select into v_nom_grade dniv_libelle
			from dieu_niveau
			where dniv_dieu_cod = v_dieu
			and dniv_niveau = 1;
	end if;
	if v_niveau = 1 then
		v_limite := getparm_n(52);
		select into v_nom_grade dniv_libelle
			from dieu_niveau
			where dniv_dieu_cod = v_dieu
			and dniv_niveau = 2;
	end if;
	if v_niveau = 2 then
		v_limite := getparm_n(53);
		select into v_nom_grade dniv_libelle
			from dieu_niveau
			where dniv_dieu_cod = v_dieu
			and dniv_niveau = 3;
	end if;
	if v_niveau = 3 then
		v_limite := getparm_n(54);
		select into v_nom_grade dniv_libelle
			from dieu_niveau
			where dniv_dieu_cod = v_dieu
			and dniv_niveau = 4;
	end if;
	if v_points < v_limite then
		code_retour := '<p>Anomalie ! Nombre de points inférieur au requis !';
		return code_retour;
	end if;
-- OK, à partir d'ici tous les contrôles sont faits, on passe à la suite.
-- on enlève les PA
	update perso set perso_pa = perso_pa - getparm_n(55)
		where perso_cod = personnage;
-- on met le grade à jour
	update dieu_perso
		set dper_niveau = dper_niveau + 1
		where dper_perso_cod = personnage;
-- évènements
	texte_evt := '[perso_cod1] est devenu '||v_nom_grade;
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
		values(nextval('seq_levt_cod'),30,now(),1,personnage,texte_evt,'O','O',personnage);
-- on génère le code retour
	code_retour := '<p>Vous êtes à présent <b>'||v_nom_grade||'</b>.<br>';
	return code_retour;
end;$function$

