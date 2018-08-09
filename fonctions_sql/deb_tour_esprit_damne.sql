CREATE OR REPLACE FUNCTION public.deb_tour_esprit_damne(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* deb_tour_esprit_damne                        */
/************************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_pos integer;
	ligne record;
	has_bloque integer;
	v_bloque_magie integer;
	v_pa_attaque integer;
	v_malus_degats integer;
	v_chance_toucher integer;
	texte_evt text;
	v_pa_dep integer;
begin
	select into v_pos
		ppos_pos_cod
		from perso_position 
		where ppos_perso_cod = personnage;
	if not found then
		return 'Anomalie sur position !';
	end if;
	if lancer_des(1,100) < 100 then
	-- frayeur
		for ligne in
			select perso_cod,perso_nom
			from perso,perso_position
			where perso_type_perso in (1,3)
			and perso_actif = 'O'
			and perso_tangible = 'O'
			and ppos_perso_cod = perso_cod
			and ppos_pos_cod = v_pos
			and not exists
			(select 1 from lieu,lieu_position
			where lpos_pos_cod = ppos_pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_refuge = 'O')
			loop
                         -- ajout azaghal le 4/6, on a 50% par joueur d'être affecté
                        if lancer_des(1,100) < 50 then
                        v_bloque_magie := 0;
			v_bloque_magie := resiste_magie(ligne.perso_cod,personnage,3);
			if v_bloque_magie = 0 then
------------------------
-- magie non résistée --
------------------------
				v_chance_toucher  := -25;
				v_pa_attaque := +2;
				v_malus_degats := -3;
			else
--------------------
-- magie résistée --
--------------------		   
				v_chance_toucher := -12;
				v_pa_attaque := +1;
				v_malus_degats := -1;
			end if;	
			perform ajoute_bonus(ligne.perso_cod, 'PAA', 2, v_pa_attaque);
			perform ajoute_bonus(ligne.perso_cod, 'TOU', 2, v_chance_toucher);
			perform ajoute_bonus(ligne.perso_cod, 'DEG', 2, v_malus_degats);
			texte_evt := '[cible] a été effrayé par [attaquant]';
		 	insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     			values(54,now(),1,personnage,texte_evt,'O','N',personnage,ligne.perso_cod);
   		insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     			values(54,now(),1,ligne.perso_cod,texte_evt,'N','N',personnage,ligne.perso_cod);
                        else
                        end if;
		end loop;
	end if;
	if lancer_des(1,100) < 25 then
	-- melasse	
	for ligne in
			select perso_cod,perso_nom
			from perso,perso_position
			where perso_type_perso in (1,3)
			and perso_actif = 'O'
			and perso_tangible = 'O'
			and ppos_perso_cod = perso_cod
			and ppos_pos_cod = v_pos
			and not exists
			(select 1 from lieu,lieu_position
			where lpos_pos_cod = ppos_pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_refuge = 'O')
			loop
                        -- ajout azaghal le 4/6, on a 50% par joueur d'être affecté
                        if lancer_des(1,100) < 50 then
			v_bloque_magie := 0;
				v_bloque_magie := resiste_magie(ligne.perso_cod,personnage,3);
			if v_bloque_magie = 0 then
------------------------
-- magie non résistée --
------------------------
				v_pa_dep := 3;
			else
--------------------
-- magie résistée --
--------------------		   
				v_pa_dep := 2;
			end if;	
			perform ajoute_bonus(ligne.perso_cod, 'DEP', 3, v_pa_dep);
			texte_evt := '[cible] a été ralenti par [attaquant]';
		 	insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     			values(54,now(),1,personnage,texte_evt,'O','N',personnage,ligne.perso_cod);
   		insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     			values(54,now(),1,ligne.perso_cod,texte_evt,'N','N',personnage,ligne.perso_cod);
                        else
                        end if;
		end loop;
	end if;	
	return 'OK';
end;$function$

