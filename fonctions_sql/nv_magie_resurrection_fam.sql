
--
-- Name: nv_magie_resurrection_fam(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION nv_magie_resurrection_fam(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function transfert_pouvoir : résurrection  de familier        */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 02/05/2018 par Marlyza                                */
/* Liste des modifications :                                     */
/*   xx/xx/xxxx : xxxx                                           */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
	texte_evt text;				-- texte pour évènements
	nom_sort text;					-- nom du sort
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
  voie_magique integer;         -- voie magique du lanceur

-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	nom_cible text;				  -- nom de la cible
  pos_actuelle integer;   -- position de la cible
  cible_etage integer;    -- etage de la cible

-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				    -- numéro du sort à lancer
	type_lancer alias for $3;	  -- type de lancer (memo ou rune)
	cout_pa integer;				    -- Cout en PA du sort
	px_gagne text;				      -- PX gagnes
  familier_perso_cod integer; -- perso_cod du familer à réssuciter
  familier_perso_nom text;    -- nom du familier résuccité
  taux_perte_xp numeric;      -- taux de perte d'xp à la résurrection (dépendant de la voie magique)
  px_perdus numeric;				    -- PX perdus par la resurrection
  v_imp_F integer;            -- nombre de tour d'impalpabilité lié à l'étage de résurrection

-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
										-- chaine temporaire pour amélioration
	v_bloque_magie integer;		-- vérif si résistance magique
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout

begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort
	num_sort := 175;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------

	select into nom_sort sort_nom from sorts where sort_cod = num_sort;
	magie_commun_txt := magie_commun(lanceur,cible,type_lancer,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	px_gagne := split_part(magie_commun_txt,';',4);

---- minimum syndical on alimente les infos de positions actuelle

  select into
    pos_actuelle, cible_etage
    ppos_pos_cod, pos_etage
		from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on ppos_pos_cod = pos_cod
		where perso_cod = cible ;

	-- on regarde s’il n’y a pas déjà un familier, on ne peut en posséder qu'un seul
	select into familier_perso_cod
		pfam_familier_cod
	from perso_familier inner join perso on perso_cod = pfam_familier_cod
	where pfam_perso_cod = cible
		and perso_actif = 'O';
	if found then
		code_retour := '<p>Malgré la réussite de votre invocation rien ne se passe. Il est impossible de rappeler un familier alors que vous en avez déjà un.</p>';
		return code_retour;
	end if;

  -- récupérer la voie magique du lanceur, il y a un bonus pour les guerrisseurs
	select into
	  voie_magique
	  perso_voie_magique
	  from perso where perso_cod = lanceur;

  -- test sur la voie du guerrisseur
  if voie_magique = 1 then
    taux_perte_xp := 0.25 ;        -- perte de 25% des PX  actuels (pour les guérriseurs)
  else
    taux_perte_xp := 0.5 ;          -- perte de 50% des XP actuels
  end if;

  -- récupération du délai d'impalpabilité lié à l'étage
  select into
    v_imp_F
    etage_duree_imp_f
  from etage where etage_numero = cible_etage;

   ---- trouver le dernier fam à recussiter
   select into
    familier_perso_cod, familier_perso_nom, px_perdus
    perso_cod, perso_nom, taux_perte_xp * perso_px  FROM perso_familier
    INNER JOIN perso on perso_cod=pfam_familier_cod
    INNER JOIN (
    	SELECT MAX(perso_dcreat) perso_dcreat FROM perso_familier INNER JOIN perso on perso_cod=pfam_familier_cod WHERE pfam_perso_cod = cible AND perso_actif='N' AND perso_type_perso=3 AND perso_race_cod=37
    ) dernier_familier ON dernier_familier.perso_dcreat = perso.perso_dcreat
    WHERE pfam_perso_cod = cible;
  if not found then
		code_retour := '<p>Malgré la réussite de votre invocation rien ne se passe, l''âme du familier n''a pas été retrouvée.</p>';
		return code_retour;
  end if;

  ---- Recussiter le familier, avec malus de PX, et impalpabilité
 update perso set
  perso_actif = 'O',
  perso_px = perso_px - px_perdus ,
  perso_tangible='N',
  perso_nb_tour_intangible = v_imp_F
  where perso_cod = familier_perso_cod ;

 -- le positionner à coté de son maitre
 update perso_position set ppos_pos_cod = pos_actuelle where ppos_perso_cod = familier_perso_cod ;

  code_retour := code_retour||'<br>vous lancez le puissant sortilège de résurrection de familier, vous avez ramené <b>' || familier_perso_nom || '</b> du plan des morts.<br>';

	code_retour := code_retour||'<br>Vous gagnez '||px_gagne||' PX pour cette action.<br>';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] ';
      insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'N','O',lanceur,cible);
   if (lanceur != cible) then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
   end if;

	texte_evt := 'La résurrection depuis le plan des morts, fait perdre ' || trim(to_char(px_perdus, '9999999')) || ' px à [cible].';
	insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
		values(nextval('seq_levt_cod'), 10, now(), 1, familier_perso_cod, texte_evt, 'N', 'N', lanceur, familier_perso_cod);

  texte_evt := '[cible] a ramené [attaquant] du plan des morts.';
  insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'), 54, now(), 1, lanceur, texte_evt, 'N', 'O', familier_perso_cod, lanceur);

  texte_evt := '[cible] a été ramené par [attaquant] du plan des morts.';
  insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'), 54, now(), 1, familier_perso_cod, texte_evt, 'N', 'O', lanceur, familier_perso_cod);

	return code_retour;

	end;
$_$;


ALTER FUNCTION public.nv_magie_resurrection_fam(integer, integer, integer) OWNER TO delain;
