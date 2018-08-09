CREATE OR REPLACE FUNCTION public.donne_bonbon(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction donne_bonbon                               */
/*  pour animation halloween 2006                      */
/* permet de donner des bonbons à des monstres         */
/* spécifiques                                         */
/* Paramètres :                                        */
/*  $1 = perso_cod donnant                             */
/*  $2 = perso_cod recevant (monstre)                  */
/* Sortie :                                            */
/*  texte complet                                      */
/*******************************************************/
/* Créé le 28/10/2006 par Merrick                      */
/*******************************************************/
/* Pour la petite anecdote, cette fonction est la première fonction
entièrement codée sous Linux (Fedora Core 6), sous gedit.
Je quitte doucement le monde Windows, je deviens un vrai geek :( */
declare
	code_retour text;
	personnage alias for $1;
	receveur alias for $2;
	--
	temp integer; 			-- fourre tout
	ligne record;			-- variable de type record pour la suppression des bonbons
	v_pos_personnage integer;	-- position donneur
	v_pos_receveur integer;		-- position receveur
	v_gmon_cod integer;			-- type du monstre
	v_sexe text;					-- sexe du donneur
	v_lancer_des integer;		-- lancer de dés pour surprise aléatoire
	v_br integer;
	texte_evt text;
begin
	--
	-- on commence bien sur par des controles ;)
	--
	-- existence des protagonistes 
	select into temp
		perso_cod from perso
		where perso_cod = personnage
		and perso_actif = 'O' ;
	if not found then
		return 'Personnage donneur non trouvé ou non actif !';
	end if;
	select into v_sexe 
		perso_sex from perso
		where perso_cod = personnage;
	select into temp
		perso_cod from perso
		where perso_cod = receveur
		and perso_actif = 'O' ;
	if not found then
		return 'Personnage receveur non trouvé ou non actif !';
	end if;
	-- positions correspondantes ?
	select into v_pos_personnage
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = personnage;
	select into v_pos_receveur
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = receveur;
	if v_pos_personnage != v_pos_receveur then
		return 'Vous ne pouvez pas donner de bonbons à un monstre qui n''est pas sur la même case que vous !';
	end if;
	-- Nombre de PA pour donner des bonbons ?
	select into temp
		perso_pa from perso
		where perso_cod = personnage;
	if temp < getparm_n(102) then
		return 'Vous n''avez pas assez de Pa pour donner des bonbons... ';
	end if;
	-- a-t-on assez de bonbons ?
	select into temp
		count(obj_cod)
		from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = 448;
	if temp < getparm_n(103) then
		return 'Vous n''avez pas assez de bonbons à donner....';
	end if;
	-- le receveur est il bien un monstre capable de recevoir les bonbons ?
	select into v_gmon_cod
		perso_gmon_cod
		from perso
		where perso_cod = receveur;
	if v_gmon_cod not in (30,331) then
		return 'Vous ne pouvez pas donner de bonbons à autre chose de Jack''O ou des sorcières !';
	end if;
/***************************************************/
/* Fin des controles, on passe à la réalisation    */
/***************************************************/
	-- on enlève les pa
	update perso
		set perso_pa = perso_pa - getparm_n(102)
		where perso_cod = personnage;
	-- on enlève les bonbons
	-- par facilité, on considère que le receveur les mange, ça évite une multiplication
	for ligne in select obj_cod
		from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = 448
		limit getparm_n(103) loop
		temp := f_del_objet(ligne.obj_cod);
	end loop; 	
	--
	-- a partir d'ici, on a tout fait, reste à voir le résultat de l'action
	-- on commence à regarder le titre
	select into temp
		ptitre_cod
		from perso_titre
		where ptitre_perso_cod = personnage
		and ptitre_titre ilike '%pue le poisson pourri%';
	if found then
		--
		-- on a un titre poisson pourri, on le supprime...
		--
		delete from perso_titre
			where ptitre_perso_cod = personnage
			and ptitre_titre ilike '%pue le poisson pourri%';
		--
		-- on génère un petit message...
		--
		if v_gmon_cod = 331 then
			-- sorciere
			if v_sexe = 'M' then
			-- un homme....
				code_retour := 'La sorcière accepte vos bonbons. Elle les prend, les ouvre, et les mange....<br>
					Soudain, elle se jette sur vous, attrape votre visage dans ses mains et vous embrasse.<br>
					Trop surpris pour réagir, vous remarquez au moment où ses lèvres quittent les votre que l''odeur qui vous suivait depuis tellement longtemps a maintenant disparu....';
			else
			-- une femme ....
				code_retour := 'La sorcière accepte vos bonbons. Elle les prend, les ouvre, et les mange....<br>
					Elle vous regarde, murmure quelque mots et agite les doigts en votre direction.<br>
					Vous remarquez à ce moment que l''odeur qui vous suivait depuis tellement longtemps a maintenant disparu....';
			end if;
		else
			-- Jack'o
			code_retour := 'Vous tendez les bonbons au monstre, qui tourne la tête vers vous. Vous apercez une lueur au fond des orbites creusées dans la citrouille.<br>
			Il semble se passer quelque chose, un vent se lève et tourbillonne autour de vous.<br>
			Vous remarquez à ce moment que l''odeur qui vous suivait depuis tellement longtemps a maintenant disparu....';
		end if;
	else
	--
	-- il n'y avait pas de titre de poisson pourri, on va générer autre chose
	--
		v_lancer_des := lancer_des(1,100);
		if v_lancer_des <= 35 then
			-- don de quelques brouzoufs
			v_br := lancer_des(1,10);
			update perso
				set perso_po = perso_po + v_br
				where perso_cod = personnage;
			if v_gmon_cod = 331 then
				code_retour := 'La sorcière ';
			else
				code_retour := 'Le monstre ';
			end if;
			code_retour := code_retour||'vous donne '||trim(to_char(v_br,'999'))||' brouzoufs en échange de vos bonbons;';
		elsif v_lancer_des <= 55 then
			-- don de quelques brouzoufs
			v_br := lancer_des(1,50) + 10;
			update perso
				set perso_po = perso_po + v_br
				where perso_cod = personnage;
			if v_gmon_cod = 331 then
				code_retour := 'La sorcière ';
			else
				code_retour := 'Le monstre ';
			end if;
			code_retour := code_retour||'vous donne '||trim(to_char(v_br,'999'))||' brouzoufs en échange de vos bonbons;';
		elsif v_lancer_des <= 65 then
			-- chapeau inutile :)
			code_retour := cree_objet_perso_nombre(70,personnage,1);
			if v_gmon_cod = 331 then
				code_retour := 'La sorcière ';
			else
				code_retour := 'Le monstre ';
			end if;
			code_retour := 'vous donne un joli chapeau pointu en échange de vos bonbons.';
			if v_gmon_cod = 331 then
				code_retour := code_retour||' La sorcière vous fait un sourire énigmatique...';
			else
				code_retour := code_retour||' Vous croyez déceler sur la face de citrouille un sourire moqueur.';
			end if;
		elsif v_lancer_des <= 75 then
			-- LE BAISER !! LE BAISER !! LE BAISER !!
			if v_gmon_cod = 331 then
					code_retour := 'La sorcière s''avance vers vous, accepte vos bonbons. Elle vous regarde et dépose doucement un baiser sur votre joue.';
			else
				code_retour := 'Le jack''O s''avance vers vous et prend vos bonbons. Avant que nous puissiez réagir, il ouvre ses bras, se jette sur vous et ...
				Vous vous demandiez depuis toujours quel effet pouvait faire l''accolade d''un monstre, vous avez maintenant la réponse....';
			end if;
		elsif v_lancer_des <= 80 then
			-- oeuf de baba 
			code_retour := cree_objet_perso_nombre(269,personnage,1);
			code_retour := 'Vos bonbons sont pris à peine vous les tendez. ';
			if v_gmon_cod = 331 then
				code_retour := code_retour||'La sorcière ';
			else
				code_retour := code_retour||'Le monstre ';
			end if;
			code_retour := code_retour||' regarde autour, vérifie que personne ne regarde, et met rapidement dans votre inventaire un objet';
		elsif v_lancer_des <= 85 then
			-- parchemin 
			temp := 362 + lancer_des(1,6);
			code_retour := cree_objet_perso_nombre(temp,personnage,1);
			code_retour := 'Vos bonbons sont pris à peine vous les tendez. ';
			if v_gmon_cod = 331 then
				code_retour := code_retour||'La sorcière ';
			else
				code_retour := code_retour||'Le monstre ';
			end if;
			code_retour := code_retour||' regarde autour, vérifie que personne ne regarde, et met rapidement dans votre inventaire un objet';
		elsif v_lancer_des <=90 then
			-- bipbip
			select into temp perso_pa
				from perso
				where perso_cod = receveur;
			if temp < 4 then
				update perso
					set perso_pa= perso_pa + 4
					where perso_cod = receveur;
				code_retour := nv_magie_bipbip(receveur,personnage,1);
			else
				code_retour := nv_magie_bipbip(receveur,personnage,1);
				update perso
					set perso_pa= perso_pa + 4
					where perso_cod = receveur;
			end if;			
			code_retour := 'Vos bonbons disparaissent immédiatement, et comme par magie, vous vous sentez soudain plus rapide !';
		elsif v_lancer_des <= 95 then
			-- defense
			select into temp perso_pa
				from perso
				where perso_cod = receveur;
			if temp < 4 then
				update perso
					set perso_pa= perso_pa + 4
					where perso_cod = receveur;
				code_retour := nv_magie_defense(receveur,personnage,1);
			else
				code_retour := nv_magie_defense(receveur,personnage,1);
				update perso
					set perso_pa= perso_pa + 4
					where perso_cod = receveur;
			end if;			
			code_retour := 'Vos bonbons disparaissent immédiatement, et comme par magie, vous vous sentez soudain plus résistant !';
		else 
			-- rune
			temp := 26 + lancer_des(1,5);
			code_retour := cree_objet_perso_nombre(temp,personnage,1);
			code_retour := 'Vos bonbons sont pris à peine vous les tendez. ';
			if v_gmon_cod = 331 then
				code_retour := code_retour||'La sorcière ';
			else
				code_retour := code_retour||'Le monstre ';
			end if;
			code_retour := code_retour||' regarde autour, vérifie que personne ne regarde, et met rapidement dans votre inventaire un objet';
		end if;
	-- insertion d'un évènement
	texte_evt := '[attaquant] a donné des bonbons à [cible] ';
		
	end if;
	insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
			values(76,now(),1,personnage,texte_evt,'O','O',personnage,receveur);
		insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
			values(76,now(),1,receveur,texte_evt,'N','O',personnage,receveur);
		insert into bonbons (bonbon_donneur,bonbon_receveur) values(personnage,receveur);
		return code_retour;
end;
	
	
$function$

