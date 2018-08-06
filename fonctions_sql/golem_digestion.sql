CREATE FUNCTION golem_digestion(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************/
/* fonction golem_digestion                          */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie un texte affichable         */
/* Cette fonction effectue la digestion d’objets...  */
/*****************************************************/
/* 20/12/2012                                        */
/* 06/08/2018 Les objets enchantés ne sont plus mangeables */
/*****************************************************/
declare
-------------------------------------------------------
-- variables E/S
-------------------------------------------------------
	code_retour text;          -- code sortie
-------------------------------------------------------
-- variables de renseignements du monstre
-------------------------------------------------------
	v_monstre alias for $1;    -- perso_cod du monstre
	v_exp numeric;             -- xp du monstre
	v_pa integer;              -- pa du monstre
-------------------------------------------------------
-- variables temporaires ou de calcul
-------------------------------------------------------
	temp integer;					-- fourre tout
	temp_txt text;					-- texte temporaire
	v_des integer;
	ligne record;
-------------------------------------------------------
-- variables pour cible
-------------------------------------------------------
	poids integer;
	poids_max integer;
	code_objet integer;
	nom_objet text;
	pos_actuelle integer;
	position_arrivee integer;

begin
	code_retour := '';
	-- Choix d’un objet parmi les 5 plus lourds
	select into code_objet, poids, nom_objet obj_cod, obj_poids::integer, obj_nom
	from 
		(select obj_cod, obj_poids, obj_nom
		from perso_objets
		inner join objets on obj_cod = perobj_obj_cod
		inner join objet_generique on gobj_cod = obj_gobj_cod
		where perobj_perso_cod = v_monstre
			and gobj_tobj_cod in (1, 2, 4)
			and obj_enchantable != 2
		order by obj_poids desc
		limit 5) t
	order by random()
	limit 1;
	if not found then
		return 'Rien à manger...';
	end if;

	poids := min(poids, 50);
	update perso set perso_px = perso_px + poids, perso_pa = perso_pa - 6 where perso_cod = v_monstre;

	code_retour := 'Recyclage de « ' || nom_objet || ' » en';
	--On va transformer l’objet et donc le détruire, et si le poids est trop important pour le monstre, il va le vomir
	poids := f_del_objet(code_objet);

	v_des := lancer_des(1,100);
	if v_des <= 10 then
		code_objet := 164;
	elsif v_des <= 45 then
		code_objet := 333;
	elsif v_des <= 65 then
		code_objet := 335;
	elsif v_des <= 85 then
		code_objet := 336;
	elsif v_des <= 86 then
		code_objet := 337;
	elsif v_des <= 87 then
		code_objet := 354;
	elsif v_des <= 88 then
		code_objet := 361;
	elsif v_des <= 89 then
		code_objet := 360;
	elsif v_des <= 90 then
		code_objet := 359;
	elsif v_des <= 91 then
		code_objet := 355;
	elsif v_des <= 92 then
		code_objet := 357;
	elsif v_des <= 93 then
		code_objet := 338;
	elsif v_des <= 94 then
		code_objet := 339;
	elsif v_des <= 95 then
		code_objet := 358;
	elsif v_des <= 96 then
		code_objet := 340;
	elsif v_des <= 97 then
		code_objet := 353;
	elsif v_des <= 98 then
		code_objet := 352;
	elsif v_des <= 99 then
		code_objet := 356;
	elsif v_des <= 100 then
		code_objet := 341;
	end if;
	poids := cree_objet_perso(code_objet, v_monstre);
	select into nom_objet gobj_nom from objet_generique where gobj_cod = code_objet;
	code_retour := code_retour || '« ' || nom_objet || ' »';

	--on regarde si le perso n’est pas trop chargé.		
	select into poids, poids_max get_poids(v_monstre)::integer, perso_enc_max::integer from perso where perso_cod = v_monstre;
	if poids > poids_max then 
		--On va vider l’inventaire du monstre en envoyant tous les objets dans l’étage à 5 cases autour
		select into pos_actuelle ppos_pos_cod from perso_position where ppos_perso_cod = v_monstre;
		code_retour := code_retour || '<br /><b>Explosion !</b> Objets balancés : <br />';
		for ligne in select perobj_obj_cod, obj_nom from perso_objets inner join objets on obj_cod = perobj_obj_cod where perobj_perso_cod = v_monstre loop
			select into position_arrivee lancer_position from lancer_position(pos_actuelle, 5) where lancer_position not in (select mur_pos_cod from murs) order by random() limit 1;
			delete from perso_objets where perobj_obj_cod = ligne.perobj_obj_cod;
			insert into objet_position (pobj_obj_cod, pobj_pos_cod)
				values (ligne.perobj_obj_cod, position_arrivee);
			code_retour := code_retour || ligne.obj_nom || '<br>';
		end loop;
		-- On va créer un élémentaire de terre
		--quantite := cree_monstre_pos(187,pos_actuelle);
		-- Un jet d'acide sur les persos autour
		-- A faire
	end if;
	return code_retour;
end;$_$;

ALTER FUNCTION public.golem_digestion(integer) OWNER TO delain;

COMMENT ON FUNCTION golem_digestion(integer) IS 'Cette fonction effectue la digestion d’objets hors enchantement.';
