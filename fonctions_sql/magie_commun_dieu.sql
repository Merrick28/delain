--
-- Name: magie_commun_dieu(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.magie_commun_dieu(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function magie_commun_dieu : part commune à tous les sorts    */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = numéro du sort lancé                                   */
/* Le code sortie est une chaine séparée par ;                   */
/*  1 = sort réussi ?                                            */
/*      0 = non                                                  */
/*      1 = oui                                                  */
/*  2 = sort résisté ?                                           */
/*      0 = pas de résistance ou N/A                             */
/*      1 = résistance                                           */
/*  3 = chaine html de sortie                                    */
/*****************************************************************/
/* Créé le 21/09/2004                                            */
/*   21/03/2011 : Bleda: Zone de droit                           */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
	texte_evt text;				-- texte pour évènements
	texte_memo text;				-- texte pour mémorisation
-------------------------------------------------------------
-- variables concernant le lanceur
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
	lanceur_pa integer;			-- pa du lanceur
	pos_lanceur integer;			-- position du lanceur
	v_comp integer;				-- valeur de compétence initiale
	v_comp_modifie integer;		-- valeur de compétence modifiée
	v_comp_cod integer;			-- comp_cod utilisée
	nom_comp text;					-- nom de la compétence utilisée
	px_gagne integer;				-- px gagnes pour ce sort
	temp_renommee numeric;		-- calcul pour renommee
	pa_magie integer;				-- bonus en cout de lancer de sort
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	pos_cible integer;			-- position de la cible
	nom_cible perso.perso_nom%type;
	type_cible integer;			-- Aventurier ou monstre (ou familier)
										-- nom de la cible
	v_bloque_magie integer;		-- variable pour savoir si on bloque
	v_pos_pvp character;	        -- Si la cible est en zone de droit
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort alias for $3;		-- numéro du sort à lancer
	cout_pa integer;				-- Cout en PA du sort
	distance_sort integer;		-- portée du sort
	nom_sort varchar(50);		-- nom du sort
	niveau_sort integer;			-- niveau du sort
	aggressif varchar(2);		-- sort aggressif ?
	temp integer;					-- fourre tout
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	deb_res_controle text;		-- partie 1 du controle sort
	res_controle text;			-- totalité du contrôle sort
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
	v_puissance_dieu integer;	-- PP du dieu
	v_puissance_dieu_perso integer;	-- PP du perso
	v_cout_puissance integer;	-- cout en PP
	v_cout_puissance_perso integer;	-- cout en PP pour le perso
	v_dieu_cod integer;
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	niveau_religion integer;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
-- sur le sort
	select into nom_sort,cout_pa,aggressif,niveau_sort sort_nom,sort_cout,sort_aggressif,sort_niveau from sorts where sort_cod = num_sort;
	if not found then
		code_retour := code_retour||'0;<p>Erreur : sort non trouvé !';
		return code_retour;
	end if;
	temp_renommee := 0;
-- sur la cible
	select into nom_cible perso_nom from perso
		where perso_cod = cible;
	if not found then
		code_retour := code_retour||'0;<p>Erreur : cible non trouvée !';
		return code_retour;
	end if;
-- contrôles de lancement
	res_controle = controle_sort_dieu(num_sort,lanceur,cible);
	deb_res_controle := substr(res_controle,1,1);
	if deb_res_controle != '0' then
		code_retour := code_retour||'0;<p>'||res_controle;
		return code_retour;
	end if;
	select into pos_cible, type_cible, v_pos_pvp ppos_pos_cod, perso_type_perso, pos_pvp
		from perso, perso_position, positions
		where ppos_perso_cod = cible
		and ppos_perso_cod = perso_cod
		and ppos_pos_cod = pos_cod;
	if type_cible != 2 and v_pos_pvp = 'N' and aggressif = 'O' then
		code_retour := '0;<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de lui lancer un sort offensif car elle n''est pas une engeance de Malkiar !<br /> La zone de droit couvre tout l''Ouest de l''étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n''importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)';
		return code_retour;
	elsif type_cible = 2 and v_pos_pvp = 'N' and aggressif = 'N' then
		code_retour := '0;<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de lui lancer un sort de soutien car elle est une engeance de Malkiar !<br /> La zone de droit couvre tout l''Ouest de l''étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n''importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)';
		return code_retour;
	end if;
------------------------------------------------------------
-- les controles semblent bons, on peut passer à la suite
------------------------------------------------------------
	code_retour := code_retour||'<p>Vous avez lancé le sort <b>'||nom_sort||'</b>, sur la cible <b>'||nom_cible||'</b>.<br>';
-- on rajoute le lancement du sort dans le total
-- on enlève les PA
	cout_pa := cout_pa + valeur_bonus(lanceur, 'PAM');
-- on regarde s il y a concentration
	if valeur_bonus(cible, 'DFM') != 0 then
		code_retour := '0;'||code_retour||'Votre sort est rejeté car la cible est sous le coup d''une protection magique.';
		return code_retour;
	end if;
-- on regarde si le dieu a assez de puissance
	v_cout_puissance := niveau_sort * (1 + niveau_sort);
	v_cout_puissance_perso := niveau_sort * (niveau_sort - 1);
       	select into v_puissance_dieu,v_dieu_cod,v_puissance_dieu_perso dieu_pouvoir,dieu_cod,dper_points
		from dieu,dieu_perso
		where dper_perso_cod = lanceur
		and dper_dieu_cod = dieu_cod;
	--on regarde si le perso a suffisamment de points aussi
	if v_puissance_dieu_perso < v_cout_puissance_perso then
		code_retour := 'Votre dieu ne peut répondre à votre demande car vous n''êtes pas suffisamment fidèle à ses enseignements. Priez au lieu de chercher à vous servir de sa puissance !';
		code_retour := '0;'||code_retour;
		cout_pa := ceil(cout_pa*0.5);
		update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
		return code_retour;
	end if;
	if v_puissance_dieu < v_cout_puissance then
		code_retour := 'Votre dieu ne peut répondre à votre demande car il n''a pas assez de puissance !';
		v_cout_puissance_perso := v_cout_puissance_perso + v_cout_puissance;
		v_cout_puissance := 0;
		if v_puissance_dieu_perso > v_cout_puissance_perso then
			code_retour := code_retour||'<br>Néanmoins, vous êtes un fidèle accompli, et votre dieu continue à vous accompagner et à vous aider. Il accepte exceptionnellement votre requête, mais n''en abusez pas !';
		else
			code_retour := code_retour||'<br>Vous n''êtes pas non plus un modèle de ferveur. Votre appel reste dans le vide. Pensez à prier pour la prochaine fois !';
			code_retour := '0;'||code_retour;
			cout_pa := ceil(cout_pa*0.5);
			update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
			return code_retour;
		end if;
	end if;

	update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
	update dieu set dieu_pouvoir = dieu_pouvoir - v_cout_puissance where dieu_cod = v_dieu_cod;
	----------------------------
	-- mise à jour PP lanceur --
	----------------------------
	update dieu_perso set dper_points = dper_points - v_cout_puissance_perso where dper_perso_cod = lanceur;
-- on regarde si bloque magie
	if aggressif = 'O' then
		select into v_bloque_magie
			pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = cible
			and pcomp_pcomp_cod = 27;
		if found then
			v_bloque_magie := bloque_magie(cible,niveau_sort);
			if v_bloque_magie != 0 then
				code_retour := code_retour||'Votre adversaire <b>bloque</b> le sort.<br><br>';
				code_retour := code_retour||'Vous gagnez '||trim(to_char(px_gagne,'999'))||' PX pour cette action.<br>';
				texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] qui a bloqué le sort.';
   			insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
	     			values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   			insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
	     			values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
     			code_retour := '0;'||code_retour;
     			return code_retour;
			end if;
		end if;
             -- ajout azaghal, on utilise la nouvelle resistance magique en considérant une marge de réussite de 50).
		v_bloque_magie := resiste_magie(cible,lanceur,niveau_sort,60);
	else
		v_bloque_magie := 0;
	end if;
	if v_bloque_magie = 0 then
------------------------
-- magie non résistée --
------------------------
		code_retour := '1;0;'||code_retour;
	else
--------------------
-- magie résistée --
--------------------
		code_retour := '1;1;'||code_retour;
	end if;

	return code_retour;
end;
$_$;


ALTER FUNCTION public.magie_commun_dieu(integer, integer, integer) OWNER TO delain;