CREATE OR REPLACE FUNCTION public.joueur_inactif(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/********************************************/
/* Fonction joueur_inactif                  */
/*   transforme les joueurs inactifs en     */
/*   monstres                               */
/********************************************/
/* On passe en paramètre un entier          */
/********************************************/
/* le code retour est un texte              */
/********************************************/
declare
	code_retour text;
	ligne record;
	ligne2 record;
	compt integer;
	delai interval;
	texte_admin text;
	temp integer;
	v_etage integer;
begin
	compt := 0;
	code_retour := '';
	delai := trim(to_char(getparm_n(14),'9999'))||' days';
	-- 1 : persos inactifs 
	for ligne in select * from perso
		where perso_type_perso = 1
		and perso_actif = 'O'
		and perso_der_connex + delai < now()
		and perso_pnj != 1
		loop
		temp := monstrifie_perso(ligne.perso_cod);
	end loop;
	code_retour := trim(to_char(compt,'999999'))||' persos transformés en monstres';
	compt := 0;
	-- 2 : perso inactifs hibernés 
	for ligne in select * from perso
		where perso_type_perso = 1
		and perso_actif = 'H'
		and perso_der_connex + '360 days'::interval < now()
		loop
		temp := monstrifie_perso(ligne.perso_cod);
	end loop;
	-- 3 : on supprime l'impalpabilité
	for ligne in 
		select * from perso
		where perso_dfin_tangible <= now()
		and perso_type_perso = 2 loop
		-- on prend l'étage
		select pos_etage into v_etage
			from perso_position,positions
			where ppos_perso_cod = ligne.perso_cod
			and ppos_pos_cod = pos_cod;
		-- on les réactive
		update perso set perso_tangible = 'O', perso_dfin_tangible = null
			where perso_cod = ligne.perso_cod;
		-- on dispatche l'inventaire
		for ligne2 in 
			select * from objets,perso_objets,objet_generique
			where perobj_perso_cod = v_perso
			and perobj_obj_cod = obj_cod
			and obj_gobj_cod = gobj_cod
			loop
			-- on supprime l'objet de l'inventaire
			delete from perso_objets
				where perobj_cod = ligne2.perobj_cod;
			-- on le met quelque part dans l'étage
			insert into objet_position
				(pobj_cod,pobj_pos_cod)
				values
				(ligne2.obj_cod,pos_aleatoire_ref(v_etage));
		end loop;
	end loop;
	code_retour := 0;
	return code_retour;

end;
$function$

