
--
-- Name: passage(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION passage(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function passage : permet de prendre un passage (escalier ou  */
/*     autre).                                                   */
/*    est passé en paramètre                                     */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/* Le code sortie est une chaine html                            */
/*****************************************************************/
/* Créé le 14/10/2004                                            */
/* Liste des modifications :                                     */
/* 14/12/07 : Détourage de la fuite dans une fonction annexe 		*/
/* 17/01/08 : Rajout de passage à coût en pa variable						*/
/* 02/05/18 : interdire passage donjon/arene et inversement			*/
/*****************************************************************/
declare
------------------------------------------------
-- variables de retour
------------------------------------------------
	code_retour text;				-- chaine de retour
------------------------------------------------
-- variables concernant la nouvelle position
------------------------------------------------
	v_pos_dest integer;			-- v_pos_dest de destination
------------------------------------------------
-- variables du perso
------------------------------------------------
	personnage alias for $1;	-- perso_cod
	familier integer;	-- familier rattaché au perso
	pa integer;						-- PA du perso
	nb_lock integer;			-- nombre de locks
	nb_lock_attaquant integer;
									-- nombre de locks attaquant
	nb_lock_cible integer;	-- nombre de locks cible
	texte_fuite text;				-- Texte pour la fuite
	nb_concentrations integer;
									-- nombre de concentrations
	v_poids_max integer;
	v_poids_actu numeric;
	v_poids_objet numeric;
	v_tangible text; 			-- détermine si un perso est tangible ou non
	ancien_x integer;
	ancien_y integer;
	ancien_etage integer;
	x integer;
	y integer;
	e integer;
	f_deplace_arrivee text;
	result_deplace_arrivee text;
	ancien_code_pos integer;
	trace_texte text;
	v_cout_pa integer;			-- coût du passage en pa pour les nouveaux passages ondulants (ou autres d'ailleurs)
	type_arene_depart text;
	type_arene_arrivee text;    -- vérifier que le depart et l'arrivée sont de même type, les transferts donjon/arene doivent se faire par bat admin.
------------------------------------------------
-- variables fourre tout
------------------------------------------------
	texte text;					-- texte pour évènement
	nb_trans integer;			-- nombre de transaction effacées
	des integer;				-- lancer de dés pour fuite
	tmp_txt text;				-- texte pour améliore (fuite)
	v_type_evt integer;		-- type d'évènement à inscrire (escalier ou passage)
	v_type_lieu integer;		-- type de lieu sur lequel on est
	temp integer;		-- variable temporaire

begin
	v_type_evt := 32;
	code_retour := '<p>'; -- on débute un paragraphe
--------------------------
-- Etape 1 : controles
--------------------------
	select into ancien_x,ancien_y,ancien_etage,ancien_code_pos
		pos_x,pos_y,pos_etage,pos_cod
		from perso_position,positions
		where ppos_perso_cod = personnage
		and ppos_pos_cod = pos_cod;
	if not found then
		code_retour := code_retour||'Erreur : Position départ non trouvée !#1#';
		return code_retour;
	end if;

	select into pa,v_poids_max,v_poids_actu,v_tangible perso_pa,perso_enc_max,get_poids(perso_cod),perso_tangible from perso where perso_cod = personnage;
	if not found then
		code_retour := code_retour||'Erreur : Perso non trouvé !#1#';
		return code_retour;
	end if;
	if (v_poids_actu >= (v_poids_max * 2)) then
		code_retour := code_retour||'Erreur : Vous êtes trop encombré pour vous déplacer !#1#';
		return code_retour;
	end if;
	if exists (select 1 from murs where mur_pos_cod = v_pos_dest) then
		code_retour := code_retour||'Erreur : la destination est un mur.....#1#';
		return code_retour;
	end if;

	select into v_pos_dest,v_type_lieu,v_cout_pa
		lieu_dest,lieu_tlieu_cod,lieu_prelev
		from lieu,lieu_position,perso_position
		where ppos_perso_cod = personnage
		and ppos_pos_cod = lpos_pos_cod
		and lpos_lieu_cod = lieu_cod;
	if not found then
		code_retour := code_retour||'Erreur sur le calcul de la position de destination.#1#'; /* pas assez de pa */
		return code_retour;
	end if;
	if v_type_lieu = 29 and v_tangible = '1' then
		code_retour := code_retour||'Ce type de passage ne peut pas être pris si vous n''êtes pas tangible.#1#';
		return code_retour;
	end if;
	if v_type_lieu in (29,30) then
		if pa < v_cout_pa then
			code_retour := code_retour||'Erreur : Pas assez de PA pour effectuer ce déplacement.#1#'; /* pas assez de pa */
			return code_retour;
		end if;
        else
		if pa < getparm_n(13) then
			code_retour := code_retour||'Erreur : Pas assez de PA pour effectuer ce déplacement.#1#'; /* pas assez de pa */
			return code_retour;
		end if;
        end if;

	select into x,y,e,f_deplace_arrivee
 			pos_x,pos_y,pos_etage,trim(pos_fonction_arrivee)
		from positions where pos_cod = v_pos_dest;
	if not found then
		code_retour := code_retour||'Erreur : Position arrivée non trouvée !#1#';
		return code_retour;
	end if;
	if v_type_lieu = 3 then
		v_type_evt := 33;
	end if;
	if v_type_lieu = 16 then
		v_type_evt := 33;
	end if;

  /* Marlyza - 02/05/18 : interdire passage donjon/arene et inversement			*/
	select into type_arene_depart
	  etage_arene
    from perso
    inner join perso_position on ppos_perso_cod = perso_cod
    inner join positions on pos_cod = ppos_pos_cod
    inner join etage on etage_numero = pos_etage
    where perso_cod = personnage;

  select into type_arene_arrivee
    etage_arene
    from positions
    inner join etage on etage_numero = pos_etage
    where pos_cod=v_pos_dest;

  if ((type_arene_depart='N') and ( type_arene_arrivee != type_arene_depart)) then
		code_retour := code_retour||'Erreur : Vous ne pouvez pas utiliser de passage pour entrer dans une arène de combat !#1#';
		return code_retour;
  end if;

  if ((type_arene_depart='O') and ( type_arene_arrivee != type_arene_depart)) then
		code_retour := code_retour||'Erreur : Vous ne pouvez pas utiliser de passage pour sortir d''une arène de combat !#1#';
		return code_retour;
  end if;

---------------------------
-- on regarde si lock
---------------------------
	select count(lock_cod) into nb_lock_cible from lock_combat where lock_cible = personnage;
	select count(lock_cod) into nb_lock_attaquant from lock_combat where lock_attaquant = personnage;
	nb_lock := nb_lock_cible + nb_lock_attaquant;
---------------------------
-- si lock on passe à la fuite
---------------------------
	if nb_lock != 0 then
		select into texte_fuite fuite(personnage);
		code_retour := code_retour||split_part(texte_fuite,'#',2);
		if split_part(texte_fuite,'#',1) = '1' then
				return code_retour;
		end if;
	end if;
	code_retour := code_retour||'Déplacement effectué !';
---------------------------
-- si pas le même étage on met à jour la liste des étages
---------------------------
	if ancien_etage != e then
		perform update_etage_visite(personnage,e);
	end if;
---------------------------
-- on déplace
---------------------------
	if v_pos_dest = 119834 then
		delete from perso_plan_parallele where ppp_perso_cod = personnage;
		insert into perso_plan_parallele (ppp_perso_cod, ppp_pos_cod) Values (personnage, ancien_code_pos);
	end if;
	update perso_position
		set ppos_pos_cod = v_pos_dest
		where ppos_perso_cod = personnage;
---------------------------
-- on enlève les PA
---------------------------
	if v_type_lieu in (29,30) then
			update perso
				set perso_pa = pa - v_cout_pa
				where perso_cod = personnage;
	else
			update perso
				set perso_pa = pa - getparm_n(13)
				where perso_cod = personnage;
	end if;
---------------------------
-- on met un évènement
---------------------------
	texte := 'Déplacement de '||trim(to_char(ancien_x,'9999999'))||','||trim(to_char(ancien_y,'9999999'))||','||trim(to_char(ancien_etage,'9999999'))||' vers '||trim(to_char(x,'9999999'))||','||trim(to_char(y,'9999999'))||','||trim(to_char(e,'9999999'));
	insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values (nextval('seq_levt_cod'),v_type_evt,'now()',1,personnage,texte,'O','O');
---------------------------
-- on enlève les transactions
---------------------------
	delete from transaction
		where tran_vendeur = personnage;
	get diagnostics nb_trans = row_count;
	if nb_trans != 0 then
		texte := 'Les transactions en cours en tant que vendeur ont été annulées !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values (nextval('seq_levt_cod'),17,'now()',1,personnage,texte,'O','N');
	end if;
	delete from transaction
		where tran_acheteur = personnage;
	get diagnostics nb_trans = row_count;
	if nb_trans != 0 then
		texte := 'Les transactions en cours en tant qu acheteur ont été annulées !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values (nextval('seq_levt_cod'),17,'now()',1,personnage,texte,'O','N');
	end if;

	select into familier pfam_familier_cod from perso_familier where pfam_perso_cod = personnage;
	delete from transaction
		where tran_vendeur = familier;
	get diagnostics temp = row_count;
	delete from transaction
		where tran_vendeur = personnage;
	get diagnostics nb_trans = row_count;
	if (nb_trans+temp) != 0 then
		texte := 'Les transactions en cours en tant que vendeur ont été annulées y compris pour votre familier !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values (nextval('seq_levt_cod'),17,'now()',1,personnage,texte,'O','O');
	end if;
	delete from transaction
		where tran_acheteur = familier;
	get diagnostics temp = row_count;
	delete from transaction
		where tran_acheteur = personnage;
	get diagnostics nb_trans = row_count;
	if (nb_trans+temp) != 0 then
		texte := 'Les transactions en cours en tant qu''acheteur ont été annulées, y compris pour votre familier !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values (nextval('seq_levt_cod'),17,'now()',1,personnage,texte,'O','N');
	end if;
---------------------------
-- on regarde si il n'y a pas une fonction d'arrivée qui traine
---------------------------
	if trim(f_deplace_arrivee) != '' then
		f_deplace_arrivee := 'select '||replace(f_deplace_arrivee,'[perso]',trim(to_char(personnage,'99999999999999')));
		f_deplace_arrivee := replace(f_deplace_arrivee,'[position]',trim(to_char(v_pos_dest,'99999999999999')));
		--open curs1 for execute f_deplace_arrivee;
		if trim(f_deplace_arrivee) is not null then
			execute f_deplace_arrivee into result_deplace_arrivee;
			if trim(result_deplace_arrivee) != '' then
				code_retour := code_retour||'<hr>'||result_deplace_arrivee;
			end if;
		end if;
	end if;
---------------------------
-- on enlève les locks
---------------------------
	delete from lock_combat where lock_attaquant = personnage;
	delete from lock_combat where lock_cible = personnage;
---------------------------
-- on enlève les ripostes
---------------------------
	delete from riposte where riposte_attaquant = personnage;
---------------------------
-- changement des pos deja vues
---------------------------
	code_retour := code_retour||'#0#';
	return code_retour;
end;$_$;


ALTER FUNCTION public.passage(integer) OWNER TO delain;
