CREATE OR REPLACE FUNCTION public.monstrifie_perso(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/****************************************************/
/* monstrifie_perso                                 */
/*  gère la monstrification d’un perso donné        */
/*  en paramètres                                   */
/****************************************************/
-- 02/08/2014 Fonction plante. Désactivation temporaire (?)
declare
	v_perso alias for $1;
	v_niveau integer;
	v_familier integer;
	v_etage integer;
	ligne record;
begin
	select into v_niveau 
		perso_niveau
		from perso
		where perso_cod = v_perso;
	select pos_etage into v_etage
			from perso_position,positions
			where ppos_perso_cod = v_perso
			and ppos_pos_cod = pos_cod;
/*	if v_niveau <= 15 then
		if v_etage = 0 then
			-- on supprime le perso
			update perso set perso_actif = 'N' where perso_cod = v_perso;
			delete from perso_compte where pcompt_perso_cod = v_perso;
			delete from messages_dest where dmsg_perso_cod = v_perso;
			delete from guilde_perso where pguilde_perso_cod = v_perso;
		else
			-- on monstrifie
			update perso set perso_type_perso = 2,perso_dirige_admin = 'N', perso_sta_combat = 'N',perso_sta_hors_combat = 'N',perso_tangible = 'O' where perso_cod = v_perso;
			delete from perso_compte where pcompt_perso_cod = v_perso;
			delete from messages_dest where dmsg_perso_cod = v_perso;
			delete from guilde_perso where pguilde_perso_cod = v_perso;
		end if;
	else
		update perso set perso_type_perso = 2,perso_actif='O',perso_dirige_admin = 'N', perso_sta_combat = 'N',perso_sta_hors_combat = 'N',perso_tangible = 'N',
			perso_dfin_tangible = now() + '3 months'::interval where perso_cod = v_perso;
		delete from perso_compte where pcompt_perso_cod = v_perso;
		delete from messages_dest where dmsg_perso_cod = v_perso;
		delete from guilde_perso where pguilde_perso_cod = v_perso;
	end if;
	select into v_familier
		pfam_familier_cod
		from perso_familier
		where pfam_perso_cod = v_perso;
	if found then
		update perso set perso_type_perso = 2,perso_dirige_admin = 'N', perso_sta_combat = 'N',perso_sta_hors_combat = 'N',perso_tangible = 'O' where perso_cod = ligne.perso_cod;
		delete from perso_compte where pcompt_perso_cod = v_familier;
		delete from messages_dest where dmsg_perso_cod = v_familier;
		delete from guilde_perso where pguilde_perso_cod = v_familier;
	end if;
	--
	-- on passe au dispatch d’objets
	--
	for ligne in 
		select * from objets,perso_objets,objet_generique
		where perobj_perso_cod = v_perso
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod in (11,13) loop
		-- on supprime l’objet de l’inventaire
		delete from perso_objets
			where perobj_cod = ligne.perobj_cod;
		-- on le met quelque part dans l’étage
		insert into objet_position
			(pobj_cod,pobj_pos_cod)
			values
			(ligne.obj_cod,pos_aleatoire_ref(v_etage));
	end loop;
*/
	return v_perso;
end;
	
	
	$function$

