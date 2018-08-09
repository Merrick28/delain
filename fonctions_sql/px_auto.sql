CREATE OR REPLACE FUNCTION public.px_auto(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* fonction px_auto : répartition auto des px             */
/* on a en param :                                        */
/*   $1 = perso_cod tué                                   */
/*   $2 = px à répartir                                   */
/**********************************************************/
declare
	code_retour text;
	cible alias for $1;
	v_px alias for $2;
	ligne record;
	total_points integer;
	px_perso integer;
	nom_perso text;
	total_distrib integer;
	px_perso_n numeric;
	total_distrib_n numeric;
	px_theo integer;
	n1 integer;
	n2 integer;
	n1_r integer;
	n2_r integer;
	px1 numeric;
	px2 numeric;
begin
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := 'Total théorique de PX à distribuer : '||trim(to_char(v_px,'9999999999999'))||'.<br>';
	code_retour := code_retour||'<p class="titre">Methode 1 : par ligne avec arrondi au supérieur</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,act_donnee as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		union all
		select ta2.tact_libelle as libelle,t2.act_donnee as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		loop
		total_points := total_points + ligne.donnee + 1;
	end loop;
	for ligne in select tact_libelle as libelle,act_donnee as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		union all
		select ta2.tact_libelle as libelle,t2.act_donnee as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		px_perso := ceil(((ligne.donnee+1)/(total_points::numeric)) * v_px);	
		total_distrib := total_distrib + px_perso;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso,'9999999999999'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib,'99999999999'))||' ont été distribués.';
	--
	--
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := code_retour||'<p class="titre">Methode 2 : par ligne avec arrondi inférieur</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,act_donnee as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		union all
		select ta2.tact_libelle as libelle,t2.act_donnee as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		loop
		total_points := total_points + ligne.donnee + 1;
	end loop;
	for ligne in select tact_libelle as libelle,act_donnee as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		union all
		select ta2.tact_libelle as libelle,t2.act_donnee as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		px_perso := floor(((ligne.donnee+1)/(total_points::numeric)) * v_px);	
		total_distrib := total_distrib + px_perso;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso,'9999999999999'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib,'99999999999'))||' ont été distribués.';
	--
	--
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := code_retour||'<p class="titre">Methode 3 : par ligne avec arrondi mathématique</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,act_donnee as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		union all
		select ta2.tact_libelle as libelle,t2.act_donnee as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		loop
		total_points := total_points + ligne.donnee + 1;
	end loop;
	for ligne in select tact_libelle as libelle,act_donnee as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		union all
		select ta2.tact_libelle as libelle,t2.act_donnee as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		px_perso := round(((ligne.donnee+1)/(total_points::numeric)) * v_px);	
		total_distrib := total_distrib + px_perso;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso,'9999999999999'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib,'99999999999'))||' ont été distribués.';
	--
	--
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := code_retour||'<p class="titre">Methode 4 : global avec arrondi supérieur</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,sum(act_donnee + 1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
		total_points := total_points + ligne.donnee + 1;
	end loop;
	for ligne in select tact_libelle as libelle,sum(act_donnee+1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		px_perso := ceil(((ligne.donnee+1)/(total_points::numeric)) * v_px);	
		total_distrib := total_distrib + px_perso;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso,'9999999999999'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib,'99999999999'))||' ont été distribués.';
		--
	--
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := code_retour||'<p class="titre">Methode 5 : global avec arrondi inférieur</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,sum(act_donnee + 1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
		total_points := total_points + ligne.donnee + 1;
	end loop;
	for ligne in select tact_libelle as libelle,sum(act_donnee+1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		px_perso := floor(((ligne.donnee+1)/(total_points::numeric)) * v_px);	
		total_distrib := total_distrib + px_perso;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso,'9999999999999'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib,'99999999999'))||' ont été distribués.';

	--
	--
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := code_retour||'<p class="titre">Methode 6 : global avec arrondi mathématiques</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,sum(act_donnee + 1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
		total_points := total_points + ligne.donnee + 1;
	end loop;
	for ligne in select tact_libelle as libelle,sum(act_donnee+1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		px_perso := round(((ligne.donnee+1)/(total_points::numeric)) * v_px);	
		total_distrib := total_distrib + px_perso;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso,'9999999999999'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib,'99999999999'))||' ont été distribués.';
	--
	--
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := code_retour||'<p class="titre">Methode 7 : global avec PX en numérique</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,sum(act_donnee + 1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
		total_points := total_points + ligne.donnee;
	end loop;
	for ligne in select tact_libelle as libelle,sum(act_donnee+1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		px_perso_n := ((ligne.donnee+1)/(total_points::numeric)) * v_px;	
		total_distrib_n := total_distrib_n + px_perso_n;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso_n,'99999999999D99'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib_n,'99999999999D99'))||' ont été distribués.';

	--
	--
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := code_retour||'<p class="titre">Methode 8 : global avec PX en numérique, calul en niveau téhorique par participant</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,sum(act_donnee + 1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
		total_points := total_points + ligne.donnee;
	end loop;
	for ligne in select tact_libelle as libelle,sum(act_donnee+1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		select into n1,px1 perso_niveau,perso_px from perso where perso_cod = ligne.joueur;
		n1_r := niveau_relatif(px1);
		if n1_r > n1
			then n1 := n1_r;
		end if;
		select into n2,px2 perso_niveau,perso_px from perso where perso_cod = cible;
		n2_r := niveau_relatif(px2);
		if n2_r > n2
			then n2 := n2_r;
		end if;
		px_theo := 10 + 2*(n2 - n1) + n2;
		if px_theo < 0 then
			px_theo := 0;
		end if;
		px_perso_n := ((ligne.donnee+1)/(total_points::numeric)) * px_theo;	
		total_distrib_n := total_distrib_n + px_perso_n;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso_n,'99999999999D99'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib_n,'99999999999D99'))||' ont été distribués.';
		--
	--
	total_distrib := 0;
	total_distrib_n := 0;
	code_retour := code_retour||'<p class="titre">Methode 9 : global avec PX en numérique, calul en niveau téhorique par participant, en tenant compte des dégats reçus</p>';
	total_points := 0;
	for ligne in select tact_libelle as libelle,sum(act_donnee + 1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		union all
		select tact_libelle as libelle,sum(act_donnee + 1) as donnee,act_perso2 as joueur from action,action_type
		where act_perso1 = cible and act_tact_cod = tact_cod
		and act_tact_cod = 4
		and act_donnee >= 0
		group by libelle,joueur
		loop
		total_points := total_points + ligne.donnee;
	end loop;
	for ligne in select tact_libelle as libelle,sum(act_donnee+1) as donnee,act_perso1 as joueur from action,action_type
		where act_perso2 = cible and act_tact_cod = tact_cod
		and act_tact_cod in (1,2)
		group by libelle,joueur
		union all
		select ta2.tact_libelle as libelle,sum(t2.act_donnee+1) as donnee,t2.act_perso1 as joueur from action t2,action_type ta2
		where t2.act_perso2 in
		(select t1.act_perso1 from action t1
		where t1.act_perso2 = cible and act_tact_cod in (1,2))
		and t2.act_tact_cod = 3
		and t2.act_tact_cod = ta2.tact_cod
		and t2.act_perso1 != t2.act_perso2
		group by libelle,joueur
		union all
		select tact_libelle as libelle,sum(act_donnee + 1) as donnee,act_perso2 as joueur from action,action_type
		where act_perso1 = cible and act_tact_cod = tact_cod
		and act_tact_cod = 4
		and act_donnee >= 0
		group by libelle,joueur
		loop
			select into nom_perso perso_nom from perso where perso_cod = ligne.joueur;
		select into n1,px1 perso_niveau,perso_px from perso where perso_cod = ligne.joueur;
		n1_r := niveau_relatif(px1);
		if n1_r > n1
			then n1 := n1_r;
		end if;
		select into n2,px2 perso_niveau,perso_px from perso where perso_cod = cible;
		n2_r := niveau_relatif(px2);
		if n2_r > n2
			then n2 := n2_r;
		end if;
		px_theo := 10 + 2*(n2 - n1) + n2;
		if px_theo < 0 then
			px_theo := 0;
		end if;
		px_perso_n := ((ligne.donnee+1)/(total_points::numeric)) * px_theo;	
		total_distrib_n := total_distrib_n + px_perso_n;
		code_retour := code_retour||nom_perso||'('||trim(to_char(ligne.joueur,'99999999999'))||') a gagne '||trim(to_char(px_perso_n,'99999999999D99'))||' PX pour l''action : <b>'||ligne.libelle||'</b><br>';
	end loop;
	code_retour := code_retour||trim(to_char(total_distrib_n,'99999999999D99'))||' ont été distribués.';
	return code_retour;
end;

$function$

