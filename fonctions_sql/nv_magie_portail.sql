CREATE OR REPLACE FUNCTION public.nv_magie_portail(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function magie_passage : lance le sort     Portail            */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible   (qui est la case de destination                */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*   19/04/2006 : Rajout condition sur la position du passage    */
/*                égale à la position du perso                   */
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
	v_perso_niveau integer;		-- niveau du lanceur
	pos_lanceur integer;		-- position du lanceur
        v_voie_magique  integer;                 -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	v_pos alias for $2;			-- position du passage
	nom_cible text;				-- nom de la cible
	pv_cible integer;				-- pv de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne text;				-- PX gagnes
        v_px_gagne integer;             -- bonus de px pour la voie magique
	v_bonus_toucher integer;	-- bonus toucher
	drain_pv integer;				-- nombre de PV retirés
	pv_lanceur integer;			-- pv du lanceur
	pv_max_lanceur integer;		-- pv max du lanceur
	diff_pv integer;				-- différence de pv
	v_delai interval;				-- durée de validité du portail
	v_lieu integer;				-- numero du lieu
        v_bonus_tour integer;                       -- durée des bonus
        v_bonus_valeur integer;                    -- valeur des bonus
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
	v_bloque_magie integer;		-- vérif si résistance magique
	v_monstre integer;			--numéro du monstre créé
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	tmp_annule text;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 144;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
        select into v_voie_magique perso_voie_magique from perso
                where perso_cod = lanceur;
	select into nom_sort sort_nom from sorts
		where sort_cod = num_sort;
	select into compt lpos_pos_cod from lieu_position
		where lpos_pos_cod = v_pos;
	if found then
		code_retour := 'Erreur: Un lieu se trouve sur la case de destination.<br>';
		return code_retour;
	end if;
	select into compt mur_pos_cod from murs
		where mur_pos_cod = v_pos;
	if found then
		code_retour := 'Erreur: Un mur se trouve sur la case de destination.<br>';
		return code_retour;
	end if;
	select into compt lpos_lieu_cod from lieu_position,perso_position
		where ppos_perso_cod = lanceur
		and ppos_pos_cod = lpos_pos_cod;
	if found then
		code_retour := 'Erreur: Vous vous trouvez sur un lieu.<br>';
		return code_retour;
	end if;
	select into pos_lanceur
		ppos_pos_cod from perso_position
		where ppos_perso_cod = lanceur;
	if pos_lanceur=v_pos then
		code_retour := 'Vous cherchez à lancer un passage sur votre propre position. Est-ce bien judicieux ?<br>';
		return code_retour;
	end if;
	magie_commun_txt := magie_commun_case(lanceur,v_pos,type_lancer,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	px_gagne := split_part(magie_commun_txt,';',4);

  
-- tout semble OK, on peut passer à la suite
	v_delai := trim(to_char(getparm_n(45),'9999999'))||' days';
	select into pos_lanceur
		ppos_pos_cod from perso_position
		where ppos_perso_cod = lanceur;
-- lieu d'origine
	v_lieu := nextval('seq_lieu_cod');
	insert into lieu
		(lieu_cod,lieu_tlieu_cod,lieu_nom,lieu_description,lieu_refuge,lieu_url,lieu_dest,lieu_port_dfin)
		values
		(v_lieu,10,'Passage magique','Un passage crée par la magie...<br><br>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;<img src="../avatars/passage_entree.gif"><br>','N','passage.php',v_pos,now()+v_delai);
	insert into lieu_position (lpos_pos_cod,lpos_lieu_cod)
		values (pos_lanceur,v_lieu);
-- lieu destination
	v_lieu := nextval('seq_lieu_cod');
	insert into lieu
		(lieu_cod,lieu_tlieu_cod,lieu_nom,lieu_description,lieu_refuge,lieu_url,lieu_dest,lieu_port_dfin)
		values
		(v_lieu,10,'Passage magique','Un passage crée par la magie. Il est fermé et ne peut être pris dans ce sens<br><br>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;<img src="../avatars/passage_sortie.gif"><br>','N','passage_b.php',0,now()+v_delai);
	insert into lieu_position (lpos_pos_cod,lpos_lieu_cod)
		values (v_pos,v_lieu);
-- automap
	compt := init_automap_pos(v_pos);
	compt := init_automap_pos(pos_lanceur);
-- voie magique
   If v_voie_magique = 2 then
   select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,bonus_valeur from bonus
		where bonus_perso_cod = lanceur
                and bonus_valeur < 0
		and bonus_tbonus_libc = 'VUE';
	        if not found then
                   v_bonus_tour := 2;
                   v_bonus_valeur := 1;
               	   perform ajoute_bonus(lanceur, 'VUE', v_bonus_tour, v_bonus_valeur);
   code_retour := code_retour||'<br>Vous béneficiez en tant que maîtres des arcanes d''un léger bonus en vue qui ne vient pas écraser un bonus existant <br>';
                end if;
   end if;
	code_retour := code_retour||'<br>Vous avez créé avec succès un passage magique.';
	code_retour := code_retour||'<br>Vous gagnez '||px_gagne||' PX pour cette action.<br>';
	texte_evt := '[attaquant] a lancé '||nom_sort||'.';
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur);
	return code_retour;
end;
$function$

