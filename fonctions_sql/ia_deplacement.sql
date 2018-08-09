CREATE OR REPLACE FUNCTION public.ia_deplacement(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************/
/* fonction ia_deplacement                           */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/* $2 = pos_cod de la destination                    */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
/* 12/08/2005 : création                             */
/* 05/06/2010 Modification Blade pour intégrer les 	 */
/* déplacements intelligents évitant les murs				 */
/*****************************************************/

declare
-------------------------------------------------------
-- variables E/S
-------------------------------------------------------
	code_retour text;			-- code sortie
	v_monstre alias for $1;		-- perso_cod du monstre
	v_dest alias for $2; 		--pos_cod de la destination.	
-------------------------------------------------------
-- variables de renseignements du monstre
-------------------------------------------------------
	fonction_sort text;			-- fonction à lancer
    compt_loop integer;
    pos_dest integer;
    pos_test integer;
	decision_deplacement integer;
	v_chance_bip_bip integer;
	v_pa integer;
	v_etage integer;			-- etage
	pos_actuelle integer;		-- position
	statique_combat text;		-- statique en combat ?
	statique_hors_combat text;	-- statique hors combat ?
	temp_cible integer;			-- cible temporaire
    temp_text text;				-- texte temporaire
    temp_etage integer;			-- etage temporaire
begin
-- RECHERCHE DES INFOS PERSO
	select into 		v_pa,
						pos_actuelle,
						v_etage,
						statique_combat,
						statique_hors_combat
					perso_pa,
					ppos_pos_cod,
					pos_etage,
					perso_sta_combat,
					perso_sta_hors_combat
		from perso,perso_position,positions
		where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod;

code_retour := 'IA deplacement<br>Monstre '||trim(to_char(v_monstre,'999999999999'))||'<br>';
decision_deplacement := 1;
-- SI PA >= 6 et BIP  BIP DISPO => lancer bip bip
if(v_pa > 5) then
	select into temp_cible psort_cod
		from perso_sorts
		where psort_perso_cod = v_monstre
		and psort_sort_cod = 3;
	if found then
		select into temp_cible bonus_cod 
			from bonus
			where bonus_perso_cod = v_monstre
			and bonus_tbonus_libc = 'DEP'
			and bonus_valeur = -2;
		if not found then
			select into v_chance_bip_bip pcomp_modificateur
				from perso_competences
				where pcomp_perso_cod = v_monstre
				and pcomp_pcomp_cod = 50;
			if lancer_des(1,100) < v_chance_bip_bip then
				fonction_sort := 'select nv_magie_bipbip ('||trim(to_char(v_monstre,'9999999999'))||','||trim(to_char(v_monstre,'99999999999'))||',1)';
				code_retour := code_retour||'Lancement bip bip.<br>';
				execute fonction_sort;
			end if;
		end if;
	end if;
end if;
-- VERIFICATIONS ETAGE PERSO = ETAGE CIBLE
select into temp_etage pos_etage from positions where pos_cod = v_dest;
if temp_etage != v_etage then
	decision_deplacement := 0;
end if;
-- VERIFICATION EN COMBAT
if statique_combat = 'O' then
	select into temp_cible count(lock_cod) from lock_combat
		where lock_cible = v_monstre;
	if found then
		decision_deplacement := 0;
	end if;
end if;
--VERIFICATION HORS COMBAT
if statique_hors_combat = 'O' then
	decision_deplacement := 0;
end if;


-- DEPLACEMENT VERS LA DESTINATION
if decision_deplacement = 1 then
	code_retour := code_retour||'Déplacement vers la cible.<br>';
	--On teste si il y a un mur sur la trajectoire
	pos_test := trajectoire_position (pos_actuelle, v_dest);
	if pos_test = v_dest then --Il n'y a pas de mur, on se déplace normalement
		compt_loop := 0;
		while (distance(pos_actuelle,v_dest) >= 0) and (v_pa >= getparm_n(9)) loop
			compt_loop := compt_loop + 1;
			exit when compt_loop >= 10;			
				-- on récupère la case vers laquelle on se déplace
				pos_dest := dep_vers_cible(pos_actuelle,v_dest);
				-- on va sur cette nouvelle case
				temp_text := deplace_code(v_monstre,pos_dest);
				-- on récupère les nouvelles infos
				select into v_pa,pos_actuelle
					perso_pa,ppos_pos_cod
					from perso,perso_position
					where perso_cod = v_monstre
					and ppos_perso_cod = perso_cod;
		end loop;
	else --Là on s'amuse, et on passe par l'algo A*
		-- On teste que la pos de destination est bien celle qui est renseignée dans le parcours pour ce perso
		select into pos_test lferm_pos_cod from liste_fermee where lferm_perso_cod = v_monstre order by lferm_compt desc limit 1;
		if not found or pos_test = pos_dest then -- Le schéma défini n'existe pas ou n'est pas le bon semble t'il. On le crée
			delete from liste_fermee where lferm_perso_cod = v_monstre;
			delete from liste_ouverte where louv_perso_cod = v_monstre;
			perform dep_pos_cod(pos_actuelle,v_dest,v_monstre); --On a créé le chemin à emprunter
		end if;
		compt_loop := 0;
		while (distance(pos_actuelle,v_dest) >= 0) and (v_pa >= getparm_n(9)) loop
			compt_loop := compt_loop + 1;
			exit when compt_loop >= 10;
				-- on récupère la case vers laquelle on se déplace
				select into pos_dest lferm_pos_cod from liste_fermee where lferm_parent = pos_actuelle and lferm_perso_cod = v_monstre and lferm_pos_cod != lferm_parent;
				-- on va sur cette nouvelle case
				if not found then --La position n'est pas trouvé dans le schéma de déplacement, on va donc le détruire et sortir
					delete from liste_fermee where lferm_perso_cod = v_monstre;
					delete from liste_ouverte where louv_perso_cod = v_monstre;
					exit;					
				end if;
				temp_text := deplace_code(v_monstre,pos_dest);
				-- on récupère les nouvelles infos
				select into v_pa,pos_actuelle
					perso_pa,ppos_pos_cod
					from perso,perso_position
					where perso_cod = v_monstre
					and ppos_perso_cod = perso_cod;
		end loop;	
	end if;
end if;
return code_retour;
end;


$function$

