CREATE OR REPLACE FUNCTION public.piege_param(integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Piège multi résultat                         */ 
/* les monstres ne sont pas affectés            */
/* 12/04/2006                                   */
/* perso : $1                                   */
/* malus de dlt : $2                            */
/* malus poison : $3                            */
/* malus déplacement : $4                       */
/* malus Esquive : $5                           */
/* malus dégâts : $6                            */
/* malus de vue : $7                            */
/* malus pour toucher : $8                      */
/* malus pour entendre : $9 comme ultrason,     */
/*                          mettre une valeur   */
/*                          positive            */
/* malus de PA par attaque : $10                */
/* dés de dégâts pour le piege : $11            */
/* chance de déclencher le piège : $12          */
/* Texte de code retour : $13                    */
/************************************************/

declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;			-- texte pour action
	texte_evt text;				-- texte pour évènements
-------------------------------------------------------------
-- variables concernant le perso
-------------------------------------------------------------
	personnage alias for $1;        -- perso_cod du perso
	type_perso integer;             -- perso_type_perso du perso ou monstre
	pv_perso integer;               -- PV du perso
	pos_perso integer;		-- position du perso
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	malus_dlt alias for $2;			-- temps de décalage de la prochaine dlt
	int_malus_dlt interval;			-- variable interne de concatenation de chaine
	mal_poison alias for $3;
	mal_deplacement alias for $4;
	mal_esquive alias for $5;
	mal_deg alias for $6;
	mal_vue alias for $7;
	mal_touche alias for $8;
	mal_son alias for $9;
	mal_attaque alias for $10;
	mal_blessure alias for $11;
	declenchement alias for $12;
	texte alias for $13;
	texte2 text;
	v_degats integer;
	des integer; 				-- correspond à l'aléatoire
	texte_mort text;

begin


                code_retour := '';		
                select into type_perso,pv_perso
			perso_type_perso,perso_pv
			from perso
			where perso_cod = personnage;
	if not found then
		return 'soucis sur la sélection de type de perso !';
	end if;
	if type_perso = 2             --On teste si il s'agit d'un monstre
			then return 'Sans conséquence';
	else  
                       --un perso est entré sur la case
            des := lancer_des(1,100);
	    if des < declenchement then 
	        if texte = ''
			then
		code_retour := 'Vos pieds ne vous ont pas porté dans un endroit hospitalier. Lors de l''un de vos pas, une flèchette empoisonnée provenant d''un piège est venue se planter dans votre jambe. Une douleur aiguë commence à se faire sentir. Vos mouvements sont plus lents, et vos réactions beaucoup moins aiguisées ... De drôles de sensations se font sentir. Les effets sont immédiats, et certains vous échappent pour le moment. Y survivrez vous ? ';
			else
		texte2 := replace(texte,'%','''');
		texte2 := replace(texte2,'#',',');
		code_retour := texte2;
		end if;
-------------------------------------------------------------
-- Dégâts directs, non paramétrables
-------------------------------------------------------------
		v_degats := lancer_des(mal_blessure,10);
		if pv_perso < v_degats then
		-- personnage tué
			code_retour := code_retour||'Vous venez de mourir dans d''attroces souffrances. Prenez garde la prochaine fois où vous mettez les pieds !';
			texte_mort := tue_perso_final(personnage,personnage);
			code_retour := code_retour||split_part(texte_mort,';',2);
			texte_evt := '[perso_cod1] ayant été trop négligeant n''a pu échapper à un piège.';
			insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
     			values(54,now(),1,personnage,texte_evt,'O','O');
			return code_retour;
		else
		-- personnage survit
			update perso set perso_pv = pv_perso - v_degats where perso_cod = personnage;
		end if;

-------------------------------------------------------------
-- Malus pour la dlt
-------------------------------------------------------------
		if malus_dlt != 0
		then
			int_malus_dlt := trim(to_char(malus_dlt,'999999999'))||' minutes';
			update perso 
			set perso_dlt = perso_dlt + int_malus_dlt::interval
			where perso_cod = personnage;
		end if;
-------------------------------------------------------------
-- Malus pour le poison
-------------------------------------------------------------
		if mal_poison != 0
		then
			perform ajoute_bonus(personnage, 'POI', 4, mal_poison);
		end if;
-------------------------------------------------------------
-- Malus pour le déplacement
-------------------------------------------------------------
		if mal_deplacement != 0
		then
			perform ajoute_bonus(personnage, 'DEP', 2, 1);
		end if;
-------------------------------------------------------------
-- Malus pour l'esquive
-------------------------------------------------------------
		if mal_esquive != 0
		then
			perform ajoute_bonus(personnage, 'MAE', 2, mal_esquive);
		end if;
-------------------------------------------------------------
-- Malus pour les dégâts
-------------------------------------------------------------
		if mal_deg != 0
		then
			perform ajoute_bonus(personnage, 'DEG', 2, mal_deg);
		end if;
-------------------------------------------------------------
-- Malus pour la vue
-------------------------------------------------------------
		if mal_vue != 0
		then
			perform ajoute_bonus(personnage, 'VUE', 2, mal_vue);
		end if;
-------------------------------------------------------------
-- Malus pour les chances de toucher
-------------------------------------------------------------
		if mal_touche != 0
		then
			perform ajoute_bonus(personnage, 'TOU', 2, mal_touche);
		end if;
-------------------------------------------------------------
-- Malus pour entendre les messages (ultrasons)
-------------------------------------------------------------
		if mal_son != 0
		then
			perform ajoute_bonus(personnage, 'UTL', 5, mal_son);
		end if;
-------------------------------------------------------------
-- Malus pour le nombre de PA par attaque
-------------------------------------------------------------
		if mal_attaque != 0
		then
			perform ajoute_bonus(personnage, 'PAA', 2, mal_attaque);
		end if;
-------------------------------------------------------------
-- Code retour des évènements et du texte
-------------------------------------------------------------

		texte_evt := '[perso_cod1] ayant été trop négligeant n''a pu échapper à un piège.';
		insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
     		values(54,now(),1,personnage,texte_evt,'O','O');
/* disparition du piege */
		des := lancer_des(1,100);
		if des < 5 then 
/* On sélectionne l'endroit où l'on est pour mettre à jour la fontion d'arrivée */		
			select into pos_perso
				ppos_pos_cod
				from perso_position
				where ppos_perso_cod = personnage;
/* on fait la mise à jour pour la supprimer */
			update positions Set pos_fonction_arrivee = null where pos_cod = pos_perso;
			code_retour := code_retour||'<br><br> Le piège semble s''être désactivé. Cela évitera que d''autres ne se fassent avoir aussi bêtement ...';
		end if;
	end if;
	return code_retour;
end if;
end;$function$

