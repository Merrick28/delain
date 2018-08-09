CREATE OR REPLACE FUNCTION public.init_1_avril()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/****************************************/
/* init 1er avril                       */
/****************************************/
declare
	code_retour text;
	ligne record;
	v_pos integer;
	v_compt integer;
	v_temp integer;
	v_texte text;
begin
	v_compt := 0;
	for ligne in
		select * from perso
		where perso_type_perso = 2
		and perso_actif = 'O'
		and perso_dirige_admin = 'O' loop
	-- sauvegarde des positions des persos hors IA
		select into v_pos ppos_pos_cod
			from perso_position
			where ppos_perso_cod = ligne.perso_cod;
		insert into sauve_monstre_pos values( ligne.perso_cod,v_pos);
	-- on le déplace :
	-- 1 on vire les locks
		delete from lock_combat where lock_cible = ligne.perso_cod;
		delete from lock_combat where lock_attaquant = ligne.perso_cod;
	-- 2 on vire dans l'ile aux enfants
		update perso_position set ppos_pos_cod = pos_aleatoire(4)
			where ppos_perso_cod = ligne.perso_cod;
	-- 3 on remplace
		v_temp := cree_monstre_pos(236,v_pos);
		v_compt := v_compt + 1;	
	end loop;
	code_retour := trim(to_char(v_compt,'9999999999'))||' monstres hors IA déplacés et remplacés, ';
	v_compt := 0;
	for ligne in
		select perso_cod,ppos_pos_cod from perso,perso_position
		where perso_type_perso = 2
		and perso_actif = 'O'
		and (perso_dirige_admin = 'N' or perso_dirige_admin is null) and ppos_perso_cod = perso_cod loop	
	-- 1 on vire les locks
		delete from lock_combat where lock_cible = ligne.perso_cod;
		delete from lock_combat where lock_attaquant = ligne.perso_cod;
	-- 2 on supprime cette fois le perso
		update perso set perso_actif = 'N' where perso_cod = ligne.perso_cod;
	-- 3 on remplace
		v_temp := cree_monstre_pos(236,ligne.ppos_pos_cod);
		v_compt := v_compt + 1;	
	end loop;
	code_retour := code_retour||trim(to_char(v_compt,'9999999999'))||' monstres IA remplacés, ';
	v_compt := 0;
	for ligne in
		select * from perso
		where perso_type_perso = 1
		and perso_actif = 'O'
		loop	
		v_texte := cree_objet_perso_nombre(226,ligne.perso_cod,1);
		v_compt := v_compt + 1;
	end loop;
	code_retour := code_retour||trim(to_char(v_compt,'9999999999'))||' poissons ditribués.';
	return code_retour;
end;$function$

