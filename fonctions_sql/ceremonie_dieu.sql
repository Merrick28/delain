--
-- Name: ceremonie_dieu(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ceremonie_dieu(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*******************************************************/
/* fonction ceremonie_dieu                             */
/*  on passe en params                                 */
/*  $1 = perso_cod                                     */
/*  $2 = dieu_cod                                      */
/* on a en retour une chaine html exploitable          */
/*******************************************************/
declare
	code_retour text;         -- code_html de retour
-- personnage
	personnage alias for $1;  -- perso_cod du prieur
	v_pa integer;             -- pa du perso
	v_nom_dieu text;          -- nom éventuel de l’ancien dieu
	v_dieu_cod integer;       -- dieu_cod éventuel de l’ancien dieu
	v_dieu_niveau integer;    -- niveau éventuel de l’ancien dieu
	v_grade text;             -- nom du grade éventuel de l’ancien dieu
	v_duree_renegat interval; -- durée de "rénégation"
	v_po integer;             -- brouzoufs du prieur
	texte_evt text;
	v_type_perso integer;
	v_perso_pos integer;      -- position du perso
	v_avancement integer;     -- avancement du prêtre dans son niveau
	v_familier_divin integer; -- perso_cod du familier divin (s’il existe)
	v_priere_familier integer;-- points de prière du familier divin
	v_ferveur integer;        -- valeur calculée entre le rang et les PP du prêtre (rang * 2 + PP / 1000)
	v_gain_familier integer;  -- gain en PP pour le familier
-- dieu
	v_dieu alias for $2;      -- dieu_cod

begin
	code_retour := '';
	-- on commence par faire les vérifs d'usage
	select into
		v_pa,v_po,v_type_perso
		perso_pa,perso_po,perso_type_perso
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := '<p>Anomalie ! Personnage non trouvé !</p>';
		return code_retour;
	end if;
	if v_type_perso = 3 then
		code_retour := '<p>Le dieu regarde d’un air méprisant la créature qui tente de le prier. Un familier est indigne d’être un fidèle !</p>';
		return code_retour;
	end if;
	if v_pa < getparm_n(49) then
		code_retour := '<p>Anomalie ! Pas assez de PA pour organiser cette cérémonie !</p>';
		return code_retour;
	end if;
	if v_po < getparm_n(50) then
		code_retour := '<p>Anomalie ! Pas assez de brouzoufs pour organiser cette cérémonie !</p>';
		return code_retour;
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
				code_retour := '<p>Vous aviez déjà prié pour le dieu <b>'||v_nom_dieu||'</b> auparavant. Toutes vos prières pour ce dieu sont maintenant passées dans l’oubli.</p>';
				delete from dieu_perso where dper_perso_cod = personnage;
				insert into dieu_perso
					(dper_perso_cod,dper_dieu_cod,dper_niveau,dper_points)
					values
					(personnage,v_dieu,0,40);
			else
				-- un grade chez l’ancien dieu, pas bon, on passe en renégat
				select into v_grade dniv_libelle from dieu_niveau
					where dniv_dieu_cod = v_dieu_cod
					and dniv_niveau = v_dieu_niveau;
				code_retour := '<p>Vous étiez '||v_grade||' pour le dieu <b>'||v_nom_dieu||'</b><br>';
				code_retour := code_retour||'<p>Celui-ci vous considère maintenant comme un renégat, il vous est impossible de faire partie de ses fidèles pendant un certain temps.</p>';
				delete from dieu_perso where dper_perso_cod = personnage;
				insert into dieu_perso
					(dper_perso_cod,dper_dieu_cod,dper_niveau,dper_points)
					values
					(personnage,v_dieu,0,40);
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
				set dper_points = dper_points + 60
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
				v_gain_familier := (v_ferveur + 10) * 5;
				v_priere_familier := min(v_priere_familier + 50 + v_dieu_niveau * 8, 300);
				update dieu_perso set dper_points = v_priere_familier where dper_perso_cod = v_familier_divin;
				if v_priere_familier = 250 then
					code_retour := code_retour||'<p>Votre familier a grandement bénéficié de cette action ; il ne pourra pas accumuler plus d’énergie divine.</p>';
				else
					code_retour := code_retour||'<p>Votre familier a grandement bénéficié de cette action.</p>';
				end if;
			end if;
		end if;
	else
		insert into dieu_perso
			(dper_perso_cod,dper_dieu_cod,dper_niveau,dper_points)
			values
			(personnage,v_dieu,0,60);
	end if;
	-- maintenant, on rajoute du pouvoir au dieu en question
 ------ MàJ Maverick : Animation de la cathédrale de Balgur -- Le pouvoir est augmenté de 6 à la position 7327 (temple au -3) ------
	select into v_perso_pos ppos_pos_cod from perso_position where ppos_perso_cod=personnage;
	if v_perso_pos=7327 then
		update dieu set dieu_pouvoir = dieu_pouvoir + 15 + coalesce(v_dieu_niveau, 0) * 2
		where dieu_cod = v_dieu;
	else
		update dieu set dieu_pouvoir = dieu_pouvoir + 6 + coalesce(v_dieu_niveau, 0) * 2
		where dieu_cod = v_dieu;
	end if;
 ------ Fin màJ ------

	-- on enlève les PA au perso
	update perso set perso_pa = perso_pa - getparm_n(49),perso_priere = 1,perso_po = perso_po - getparm_n(50),perso_px = perso_px + 1
		where perso_cod = personnage;

	-- on regarde où en est le perso de son avancement
	select into v_avancement dieu_avancement(dper_niveau, dper_points)
	from dieu_perso
	where dper_perso_cod = personnage
		and dper_dieu_cod = v_dieu;

	-- évènements
	texte_evt := '[perso_cod1] a organisé une cérémonie pour son Dieu';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
		values(nextval('seq_levt_cod'),29,now(),1,personnage,texte_evt,'O','O',personnage);

	-- on génère un code retour
	if v_avancement <= 50 then
		code_retour := code_retour || 'Votre dieu a assisté à votre cérémonie ; ';
	elsif v_avancement <= 80 then
		code_retour := code_retour || 'Votre cérémonie a plu à votre dieu ; ';
	else
		code_retour := code_retour || 'Votre dieu s’est particulièrement réjoui de cette cérémonie, et vous porte en son estime ; ';
	end if;

	code_retour := code_retour || ' il a gagné en puissance.<br>Vous gagnez 1 PX.';

	return code_retour;
end;$_$;


ALTER FUNCTION public.ceremonie_dieu(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION ceremonie_dieu(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION ceremonie_dieu(integer, integer) IS 'Le personnage donné effectue une cérémonie, en temple, à l’adresse du dieu donné.';