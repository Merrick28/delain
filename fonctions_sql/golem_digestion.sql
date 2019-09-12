CREATE OR REPLACE FUNCTION golem_digestion(integer) RETURNS text
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
	v_type_ia integer;
    quantite integer;

begin
	code_retour := '';

	-- détection du type de golem: Armes et armures ou Pierres précieuses
	select into v_type_ia gmon_type_ia from perso left join monstre_generique on gmon_cod=perso_gmon_cod where perso_cod = v_monstre ;

	-- Choix d’un objet parmi les 5 plus lourds
	select into code_objet, poids, nom_objet obj_cod, obj_poids::integer, obj_nom
	from 
		(select obj_cod, obj_poids, obj_nom
		from perso_objets
		inner join objets on obj_cod = perobj_obj_cod
		inner join objet_generique on gobj_cod = obj_gobj_cod
		where perobj_perso_cod = v_monstre
			and (
			  ( v_type_ia=13 and gobj_tobj_cod in (1,2,4,6,15,21,39,40,41) )
			  or
			  ( v_type_ia=16 and gobj_tobj_cod in (17, 19, 28, 42) )
      )
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


	if ( v_type_ia=13 ) then
		v_des := lancer_des(1,100);
	    -- digestion d'objet par le golem d'armes et d'armures
        if v_des <= 20 then
          quantite := cree_objet_perso(164,v_monstre);
        elsif v_des <= 26 then
          quantite := cree_objet_perso(341,v_monstre);
        elsif v_des <= 32 then
          quantite := cree_objet_perso(354,v_monstre);
        elsif v_des <= 38 then
          quantite := cree_objet_perso(361,v_monstre);
        elsif v_des <= 44 then
          quantite := cree_objet_perso(357,v_monstre);
        elsif v_des <= 50 then
          quantite := cree_objet_perso(338,v_monstre);
        elsif v_des <= 56 then
          quantite := cree_objet_perso(339,v_monstre);
        elsif v_des <= 62 then
          quantite := cree_objet_perso(358,v_monstre);
        elsif v_des <= 68 then
          quantite := cree_objet_perso(340,v_monstre);
        elsif v_des <= 74 then
          quantite := cree_objet_perso(353,v_monstre);
        elsif v_des <= 80 then
          quantite := cree_objet_perso(352,v_monstre);
        elsif v_des <= 83 then
          quantite := cree_objet_perso(342,v_monstre);
        elsif v_des <= 86 then
          quantite := cree_objet_perso(337,v_monstre);
        elsif v_des <= 88 then
          quantite := cree_objet_perso(360,v_monstre);
        elsif v_des <= 90 then
          quantite := cree_objet_perso(359,v_monstre);
        elsif v_des <= 92 then
          quantite := cree_objet_perso(335,v_monstre);
        elsif v_des <= 94 then
          quantite := cree_objet_perso(336,v_monstre);
        elsif v_des <= 96 then
          quantite := cree_objet_perso(438,v_monstre);
        elsif v_des <= 98 then
          quantite := cree_objet_perso(355,v_monstre);
        else
          quantite := cree_objet_perso(356,v_monstre);
        end if;
    else
        v_des := lancer_des(1,5040);
        -- digestion d'objet par le golem de pierres precieuses
        if v_des <= 20   then -- Mâle = 1/12
            quantite := cree_objet_perso(27,v_monstre);
        elsif v_des <= 40   then -- Femelle = 1/12
            quantite := cree_objet_perso(28,v_monstre);
        elsif v_des <= 120  then -- Minéral = 1/18
            quantite := cree_objet_perso(29,v_monstre);
        elsif v_des <= 1400 then -- Végétal = 1/18
            quantite := cree_objet_perso(30,v_monstre);
        elsif v_des <= 1680 then -- Animal = 1/18
            quantite := cree_objet_perso(31,v_monstre);
        elsif v_des <= 1890 then -- Terre = 1/24
            quantite := cree_objet_perso(32,v_monstre);
        elsif v_des <= 2100 then -- Eau = 1/24
            quantite := cree_objet_perso(33,v_monstre);
        elsif v_des <= 2310 then -- Feu = 1/24
            quantite := cree_objet_perso(34,v_monstre);
        elsif v_des <= 2520 then -- Air = 1/24
            quantite := cree_objet_perso(35,v_monstre);
        elsif v_des <= 2688 then -- Pouce = 1/30
            quantite := cree_objet_perso(36,v_monstre);
        elsif v_des <= 2856 then -- Index = 1/30
            quantite := cree_objet_perso(37,v_monstre);
        elsif v_des <= 3024 then -- Majeur = 1/30
            quantite := cree_objet_perso(38,v_monstre);
        elsif v_des <= 3192 then -- Annulaire = 1/30
            quantite := cree_objet_perso(39,v_monstre);
        elsif v_des <= 3360 then -- Auriculaire = 1/30
            quantite := cree_objet_perso(40,v_monstre);
        elsif v_des <= 3500 then -- Merle de Kilgwri = 1/36
            quantite := cree_objet_perso(41,v_monstre);
        elsif v_des <= 3640 then -- Cerf de Rhedynvre = 1/36
            quantite := cree_objet_perso(42,v_monstre);
        elsif v_des <= 3780 then -- Hibou de Cwm Cawlwyd = 1/36
            quantite := cree_objet_perso(43,v_monstre);
        elsif v_des <= 3920 then -- Aigle de Gwern Abwy = 1/36
            quantite := cree_objet_perso(44,v_monstre);
        elsif v_des <= 4060 then -- Saumon de Llyn Llyn = 1/36
            quantite := cree_objet_perso(45,v_monstre);
        elsif v_des <= 4200 then -- Sanglier de Yskithrwynn = 1/36
            quantite := cree_objet_perso(46,v_monstre);
        elsif v_des <= 4320 then -- Muladhara = 1/42
            quantite := cree_objet_perso(47,v_monstre);
        elsif v_des <= 4440 then -- Swadhistana = 1/42
            quantite := cree_objet_perso(48,v_monstre);
        elsif v_des <= 4560 then -- Manipura = 1/42
            quantite := cree_objet_perso(49,v_monstre);
        elsif v_des <= 4680 then -- Anahata = 1/42
            quantite := cree_objet_perso(51,v_monstre);
        elsif v_des <= 4800 then -- Vishudda = 1/42
            quantite := cree_objet_perso(51,v_monstre);
        elsif v_des <= 4920 then -- Ajna = 1/42
            quantite := cree_objet_perso(52,v_monstre);
        else
            quantite := cree_objet_perso(53,v_monstre);
        end if;
    end if;

    poids := cree_objet_perso(code_objet, v_monstre);
    select into nom_objet gobj_nom from objet_generique where gobj_cod = code_objet;
    code_retour := code_retour || '« ' || nom_objet || ' »';

    --on regarde si le perso n’est pas trop chargé.
    select into poids, poids_max get_poids(v_monstre)::integer, perso_enc_max::integer from perso where perso_cod = v_monstre;
    if poids > poids_max then
        --On va vider l’inventaire du monstre en envoyant tous les objets dans l’étage à 5 cases autour => 10/09/2019: Changement pour 3 cases autour
        select into pos_actuelle ppos_pos_cod from perso_position where ppos_perso_cod = v_monstre;
        code_retour := code_retour || '<br /><b>Explosion !</b> Objets balancés : <br />';
        for ligne in select perobj_obj_cod, obj_nom from perso_objets inner join objets on obj_cod = perobj_obj_cod where perobj_perso_cod = v_monstre loop
            select into position_arrivee lancer_position from lancer_position(pos_actuelle, 3) where lancer_position not in (select mur_pos_cod from murs) order by random() limit 1;
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
