CREATE OR REPLACE FUNCTION public.dissipation(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction dissipation : dissipe une illusion         */
/*   on passe en paramètres :                          */
/*   $1 = perso_cod à dissiper                         */
/*******************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	texte_evt text;
	v_nom_perso text;
begin
	select into v_nom_perso perso_nom from perso
		where perso_cod = personnage;
	-- d'abord on met en inactif
	update perso set perso_actif = 'N' where perso_cod = personnage;
	-- on enlève les locks
	delete from lock_combat
		where lock_cible = personnage;
	delete from lock_combat
		where lock_attaquant = personnage;	
	-- on enlève des actions
	delete from action
		where act_perso1 = personnage;
	delete from action
		where act_perso2 = personnage;
	-- on enlève la relation familier/perso
	delete from perso_familier
		where pfam_familier_cod = personnage;
	-- on met un évènement
  texte_evt := v_nom_perso||' s''est dissipé !';
  insert into ligne_evt
   	(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
   	values
   	(31,now(),1,personnage,texte_evt,'O','O');
   return 0;
end;$function$

