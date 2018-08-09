CREATE OR REPLACE FUNCTION public.prie_dieu(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction prie_dieu                                  */
/*  on passe en params                                 */
/*  $1 = perso_cod                                     */
/*  $2 = dieu_cod                                      */
/* on a en retour une chaine html exploitable          */
/*******************************************************/
declare
	code_retour text;             -- code_html de retour
-- personnage
	personnage alias for $1;      -- perso_cod du prieur
	v_pa integer;                 -- pa du perso
	v_nom_dieu text;              -- nom éventuel de l’ancien dieu
	v_dieu_cod integer;           -- dieu_cod éventuel de l’ancien dieu
	v_dieu_niveau integer;        -- niveau éventuel de l’ancien dieu
	v_grade text;                 -- nom du grade éventuel de l’ancien dieu
	v_duree_renegat interval;     -- durée de "rénégation"
	v_type_perso integer;
	v_perso_pos integer;          -- position du perso
	v_familier_divin integer;     -- perso_cod du familier divin (s’il existe)
	v_priere_familier integer;    -- points de prière du familier divin
	v_ferveur integer;            -- valeur calculée entre le rang et les PP du prêtre (rang * 2 + PP / 1000)
	v_avancement integer;         -- avancement du prêtre dans son niveau
	v_gain_familier integer;      -- gain en PP pour le familier
-- dieu
	v_dieu alias for $2;          -- dieu_cod
	texte_evt text;
	dieu_lieu integer;            -- Déterminer si on est sur un temple
	v_tlieu integer;            -- Type de lieu (temple, autel, etc)
	
begin
	code_retour := '';
	--if to_char(now(),'DD/MM/YYYY') in ('22/04/2011', '23/04/2011') then
	--	return 'Un silence pesant fait écho à votre prière...';
	--end if;
	-- on commence par faire les vérifs d’usage
	select into 
		v_pa, v_type_perso
		perso_pa, perso_type_perso
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := '<p>Anomalie ! Personnage non trouvé !</p>';
		return code_retour;
	end if;
	if v_pa < getparm_n(48) then
		code_retour := '<p>Anomalie ! Pas assez de PA pour prier ce dieu !</p>';
		return code_retour;
	end if;
	if v_type_perso = 3 then
		code_retour := '<p>Le dieu regarde d’un air méprisant la créature qui tente de le prier. Un familier est indigne de s’adresser directement à son dieu !</p>';
		return code_retour;
	end if;
	-- Vérification que le perso est bien sur un lieu sacré
	-- Temple = 17 / Autel = 33 / Auberge (Tonto) = 4
	select into dieu_lieu, v_tlieu lieu_dieu_cod, lieu_tlieu_cod from perso_position,lieu_position,lieu
		where lieu_cod = lpos_lieu_cod
		and lpos_pos_cod = ppos_pos_cod
		and ppos_perso_cod = personnage
		and lieu_tlieu_cod in (17,33,4);
	if not found or dieu_lieu = NULL then
		return '<p>Vous ne pouvez pas faire ce type de prière ici, vous n’êtes pas dans un temple ni devant un autel !</p>';
	end if;
	-- Si on tente de prier un autre dieu que celui correspondant au lieu
	if v_dieu != dieu_lieu then
		return '<p>Vous ne pouvez pas faire ce type de prière ici.</p>';
	end if;
	-- on regarde s’il avait déjà prié ici ou ailleurs
	select into	
		v_dieu_cod,
		v_dieu_niveau,
		v_nom_dieu,
		v_ferveur

		dper_dieu_cod,
		dper_niveau,
		dieu_nom,
		dper_niveau * 2 + floor(sqrt(dper_points) / 20)

		from dieu_perso,dieu
		where dper_perso_cod = personnage
		and dper_dieu_cod = dieu_cod;
	if found then
		if v_dieu_cod != v_dieu then
			-- attention, il fait une infidélité
			if v_dieu_niveau = 0 then
				-- pas trop grave, aucun grade pour l'ancien dieu
				code_retour := '<p>Vous aviez déjà prié pour le dieu <b>'||v_nom_dieu||'</b> auparavant. Toutes vos prières pour ce dieu sont maintenant passées dans l’oubli.<br>';
				delete from dieu_perso where dper_perso_cod = personnage;
				insert into dieu_perso
					(dper_perso_cod,dper_dieu_cod,dper_niveau,dper_points)
					values
					(personnage,v_dieu,0,10);
			else
				-- un grade chez l'ancien dieu, pas bon, on passe en renégat
				select into v_grade dniv_libelle from dieu_niveau
					where dniv_dieu_cod = v_dieu_cod
					and dniv_niveau = v_dieu_niveau;
				code_retour := '<p>Vous étiez '||v_grade||' pour le dieu <b>'||v_nom_dieu||'</b><br>';
				code_retour := code_retour||'Celui-ci vous considère maintenant comme un renégat, il vous est impossible de faire partie de ses fidèles pendant un certain temps.<br>';
				delete from dieu_perso where dper_perso_cod = personnage;
				insert into dieu_perso
					(dper_perso_cod,dper_dieu_cod,dper_niveau,dper_points)
					values
					(personnage,v_dieu,0,10);
				if v_dieu_niveau = 1 then
					v_duree_renegat := '1 month';
				end if;
				if v_dieu_niveau = 2 then
					v_duree_renegat := '3 month';
				end if;
				if v_dieu_niveau = 3 then
					v_duree_renegat := '6 month';
				end if;
				if v_dieu_niveau = 4 then
					v_duree_renegat := '9 month';
				end if;
				if v_dieu_niveau = 5 then
					v_duree_renegat := '1 year';
				end if;
				insert into dieu_renegat (dren_dieu_cod,dren_perso_cod,dren_datfin)
					values (v_dieu_cod,personnage,now()+v_duree_renegat);
			end if;
		else
			-- on a déjà un fidèle, on peut "augmenter" son compteur
			update dieu_perso
				set dper_points = dper_points + 10
				where dper_perso_cod = personnage;

			-- on ajoute des points au familier divin
			select into v_familier_divin, v_priere_familier perso_cod, dper_points
				from perso_familier
				inner join perso on perso_cod = pfam_familier_cod
				inner join dieu_perso on dper_perso_cod = pfam_familier_cod
				where pfam_perso_cod = personnage 
					and perso_gmon_cod = 441
					and dper_dieu_cod = v_dieu
					and perso_actif='O';
					
			if found then
				v_gain_familier := v_ferveur + 10;
				v_priere_familier := max(v_priere_familier, min(v_priere_familier + v_gain_familier, 200));
				update dieu_perso set dper_points = v_priere_familier where dper_perso_cod = v_familier_divin;
				if v_priere_familier = 200 then
					code_retour := code_retour||'Votre familier a bénéficié de cette action ; il ne pourra pas accumuler plus d’énergie divine par vos prières.<br />';
				elseif v_priere_familier > 200 then
					code_retour := code_retour||'Votre familier a trop d’énergie divine pour avoir pu bénéficier de cette action.<br />';
				else
					code_retour := code_retour||'Votre familier a bénéficié de cette action.<br />';
				end if;
			end if;
		end if;
	else
		insert into dieu_perso
			(dper_perso_cod,dper_dieu_cod,dper_niveau,dper_points)
			values
			(personnage,v_dieu,0,10);
	end if;
	-- maintenant, on rajoute du pouvoir au dieu en question
         -- pour remonter les points de pouvoir des dieux on profite de la période haloween 2009 pour passer le gain à 3
	update dieu set dieu_pouvoir = dieu_pouvoir + 2 + coalesce(v_dieu_niveau, 0)
		where dieu_cod = v_dieu;

	-- on enlève les PA au perso
	update perso set perso_pa = perso_pa - getparm_n(48),perso_priere = 1,perso_px = perso_px + 0.25
		where perso_cod = personnage;

	-- on regarde où en est le perso de son avancement
	select into v_avancement dieu_avancement(dper_niveau, dper_points)
	from dieu_perso
	where dper_perso_cod = personnage
		and dper_dieu_cod = v_dieu;

	-- événements
	texte_evt := '[perso_cod1] a prié';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
		values(nextval('seq_levt_cod'),29,now(),1,personnage,texte_evt,'O','O',personnage);

	-- on génère un code retour
	if v_avancement <= 50 then
		code_retour := code_retour || 'Votre prière a été entendue par votre dieu ; ';
	elsif v_avancement <= 80 then
		code_retour := code_retour || 'Votre dieu vous a écouté avec bienveillance ; ';
	else
		code_retour := code_retour || 'Vous sentez que votre dieu a été particulièrement intéressé par votre prière ; ';
	end if;

	code_retour := code_retour || ' il a gagné en puissance.<br>Vous gagnez 0,25 PX.';
	return code_retour;
end;$function$

