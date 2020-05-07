--
-- Name: deb_tour_degats_case(integer, integer, integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE FUNCTION public.deb_tour_degats_case(integer, integer, integer, text) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function deb_tour_degats_case : dégâts sur une case en début de tour   */
/* On passe en paramètres                                        */
/*  $1 = lanceur                                                */
/*  $2 = puissance de la boule de feu (nombre de persos impactés */
/*				et dégâts occasionnés)																 */
/*  $3 = chance de provoquer la boule de feu   									 */
/*  $4 = texte pour les évènements 															 */
/*****************************************************************/
/* Créé le 27/12/2007                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	monstre alias for $1;
	puissance alias for $2;
	chance alias for $3;
	texte alias for $4;
	code_retour text;				-- chaine html de sortie
	texte_evt text;					--texte d'évènement complété des dégâts
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	nom_cible text;				-- nom de la cible
	pv_cible integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	px_gagne numeric;				-- PX gagnes
	ligne record;					-- enregistrements
	pos_lanceur integer;			-- pos_cod du lanceur
	x_lanceur integer;			-- x du lanceur
	y_lanceur integer;			-- y du lanceur
	e_lanceur integer;			-- etage du lanceur
	v_degats integer;				-- dégats effectués
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	distance_cibles integer;	-- distance entre lanceur et cible
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	v_act_numero integer;
	nb_cible integer;
	nb_cible2 integer;
	v_pv_cible integer;

begin

code_retour := 'pas d''action';
--On regarde si la fonction doit se déclencher
if  lancer_des(1,100) > chance then
	return code_retour;
end if;

-- on prend la position du monstre, pour trouver les cibles
	select into pos_lanceur,x_lanceur,y_lanceur,e_lanceur
		pos_cod,pos_x,pos_y,pos_etage
		from positions,perso_position
		where ppos_perso_cod = monstre
		and ppos_pos_cod = pos_cod;
	select into nb_cible count(perso_cod)
		from perso,perso_position
		where perso_actif = 'O'
		and perso_tangible = 'O'
		and ppos_perso_cod = perso_cod
		and perso_cod != monstre
		and ppos_pos_cod = pos_lanceur;

-- On détermine les CIBLES en fonction de la puissance
	for ligne in select perso_cod,perso_nom,perso_pv,perso_pv_max,lancer_des(1,1000) as num
		from perso,perso_position,positions
		where perso_actif = 'O'
		and perso_tangible = 'O'
		and ppos_perso_cod = perso_cod
		and ppos_pos_cod = pos_cod
		and pos_cod = pos_lanceur
		and perso_cod != monstre
		and perso_type_perso != 2
                order by num limit puissance loop
		v_degats := lancer_des(puissance,6);
				des := effectue_degats_perso(ligne.perso_cod,v_degats,monstre);
		if des != v_degats then
			code_retour := code_retour||'<br>Les dégats rééls liés à l''initiative sont de '||trim(to_char(des,'999999999')) || '.<br />';
			insert into trace (trc_texte) values ('att '||trim(to_char(monstre,'99999999'))||' cib '||trim(to_char(ligne.perso_cod,'99999999'))||' init '||trim(to_char(v_degats,'99999999'))||' fin '||trim(to_char(des,'99999999')));
		end if;
		v_degats := des;
		insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee)
			values (2,monstre,ligne.perso_cod,(0.5*ln(ligne.perso_pv_max)*v_degats)/nb_cible);
		code_retour := code_retour||'Sur <b>'||ligne.perso_nom||'</b>, vous provoquez '||trim(to_char(v_degats,'99999'))||' dégats.<br>';
		texte_evt := texte||' causant '||trim(to_char(v_degats,'999999'))||' dégats';
/*On intègre l'évènement qui va bien*/
		insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     				values(54,now(),1,monstre,texte_evt,'O','N',monstre,ligne.perso_cod);
   			insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     			values(54,now(),1,ligne.perso_cod,texte_evt,'N','N',monstre,ligne.perso_cod);
/* On gère la mort*/
		if ligne.perso_pv <= v_degats then
		-- on a tué l'adversaire !!
			px_gagne := px_gagne + to_number(split_part(tue_perso_final(monstre,ligne.perso_cod),';',1),'9999999999999999');
			code_retour := code_retour||'Vous avez <b>tué</b> '||ligne.perso_nom||'<br><br>';
		else
			code_retour := code_retour||ligne.perso_nom||' a survécu à votre attaque<br><br>';
			update perso set perso_pv = perso_pv - v_degats where perso_cod = ligne.perso_cod;
		end if;
	end loop;
	return code_retour;
end;$_$;


ALTER FUNCTION public.deb_tour_degats_case(integer, integer, integer, text) OWNER TO delain;