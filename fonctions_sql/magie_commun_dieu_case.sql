CREATE OR REPLACE FUNCTION public.magie_commun_dieu_case(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
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
	pos_cible alias for $2;			-- perso_cod de la cible
	nom_cible perso.perso_nom%type;
										-- nom de la cible
	v_bloque_magie integer;		-- variable pour savoir si on bloque
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
	select into nom_sort,cout_pa,aggressif,niveau_sort sort_nom,sort_cout,sort_bloquable,sort_niveau from sorts where sort_cod = num_sort;
	if not found then
		code_retour := code_retour||'0;<p>Erreur : sort non trouvé !';
		return code_retour;
	end if;
	temp_renommee := 0;
-- contrôles de lancement
	res_controle = controle_sort_dieu_case(num_sort,lanceur,pos_cible);
	deb_res_controle := substr(res_controle,1,1);
	if deb_res_controle != '0' then
		code_retour := code_retour||'0;<p>'||res_controle;
		return code_retour;
	end if;
------------------------------------------------------------
-- les controles semblent bons, on peut passer à la suite 
------------------------------------------------------------
	code_retour := code_retour||'<p>Vous avez lancé le sort <b>'||nom_sort||'</b>.<br>';
-- on rajoute le lancement du sort dans le total
-- on enlève les PA
	cout_pa := cout_pa + valeur_bonus(lanceur, 'PAM');

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
	code_retour := '1;0;'||code_retour;
	
----------------------------
-- mise à jour PP lanceur --
----------------------------
update dieu_perso set dper_points = dper_points - v_cout_puissance_perso where dper_perso_cod = lanceur;
	return code_retour;
end;
$function$

