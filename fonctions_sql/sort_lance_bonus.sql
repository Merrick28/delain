
--
-- Name: sort_lance_bonus(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION sort_lance_bonus(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function sort_lance_bonus : part commune à tous les sorts         */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = objet objsortbm_cod                                    */

declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;		-- chaine html de sortie
	texte_evt text;			-- texte pour évènements
	texte_memo text;		-- texte pour mémorisation
-------------------------------------------------------------
-- variables concernant le lanceur
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
	nb_sort_niveau integer; 	-- nombre de sorts du même niveau déjà lancés
	bonus_pa integer;
	v_reussite integer;		-- reussite ou non dans le cadre de la distorsion
	bonmal integer;			-- bonus malus au lancé de des
	malus integer;
	type_attaquant integer;		-- Aventurier ou monstre (ou familier)
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;		-- perso_cod de la cible
	pos_cible integer;		-- position de la cible
	nom_cible perso.perso_nom%type;	-- nom de la cible
	v_pos_protegee character;	-- la protection 'O' ou 'N' de la position ciblée
	type_cible integer;		-- Aventurier ou monstre (ou familier)
	v_pos_pvp character;		-- Si la cible est en zone de droit
	v_gmon_cod integer;		-- Type de monstre ciblé
	v_immunise character;		-- Si la cible est immunisé à ce sort
	v_immunise_valeur numeric;	-- Le taux d’immunité de la cible
	v_immunise_texte varchar(500);	-- Texte relatif à l’immunité
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	v_objsortbm_cod alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;		-- Cout en PA du sort
	nom_sort varchar(50);		-- nom du sort
	offensif varchar(2);		-- sort offensif ?
	temp integer;			-- fourre tout
	soi_meme text;			-- Détermine si on peut lancer le sort sur soi
	sur_perso text;			-- Détermine si on peut lancer le sort sur un autre perso
	sur_monstre text;		-- Détermine si on peut lancer le sort sur un monstre
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	deb_res_controle text;		-- partie 1 du controle sort
	res_controle text;		-- totalité du contrôle sort
	v_etage_lanceur integer;	-- distance entre lanceur et cible
	v_etage_cible integer;	-- distance entre lanceur et cible
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;		-- record des rune à dropper
	temp_ameliore_competence text;	-- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;			-- lancer de dés
	compt integer;			-- fourre tout
	facteur_reussite integer;
	facteur_reussite_pur integer;
	facteur_malchance numeric ;  -- facteur de malchance sur certains objets magiques
	v_special integer;
	resultat text;			-- recuperation du code de la fonction cout_pa
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';


-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
	-- sur le lanceur
	select into  type_attaquant,lanceur_pa,pos_lanceur,v_etage_lanceur
	  perso_type_persoperso_pa,ppos_pos_cod,pos_etage
    from perso,perso_position,positions
    where perso_cod = lanceur
    and ppos_perso_cod = lanceur
    and ppos_pos_cod = pos_cod;


	-- vérifier que le perso possède toujours l'objet
  select into nom_sort, cout_pa, sort_distance, offensif, soi_meme, sur_perso, sur_monstre, facteur_malchance
      coalesce(objsortbm_nom, tonbus_libelle), objsortbm_cout, objsortbm_bonus_distance, objsortbm_bonus_aggressif, objsortbm_bonus_soi_meme, objsortbm_bonus_joueur, objsortbm_bonus_monstre, objsortbm_malchance
  join objets on obj_cod=objsortbm_obj_cod
  join perso_objets on perobj_obj_cod=obj_cod and perobj_perso_cod=objsortm_perso_cod
  join bonus_type on tbonus_cod=objsortbm_tbonus_cod
  where objsortbm_cod = v_objsortbm_cod
    and perobj_identifie = 'O'
    and (perobj_equipe='O' or objsort_equip_requis=false)
    and (objsortbm_nb_utilisation_max>objsortbm_nb_utilisation or COALESCE(objsortbm_nb_utilisation_max,0) = 0)
    and not exists (select 1 from transaction where tran_obj_cod = perobj_obj_cod);
  if not found then
    code_retour := 'Erreur : vous ne possédez plus l''objet, ou il n''est plus équipé, ou il est engagé dans une transaction ou il ne dispose plus de charge.';
    return code_retour;
  end if;

	-- sur les conditions de cibles
	if soi_meme = 'O' and sur_perso = 'N' and sur_monstre = 'N' and lanceur != cible then
		code_retour := code_retour||'0;<p>Erreur : ce sort ne peut être lancé que sur soi-même !</p>';
		return code_retour;
	end if;


	bonus_pa := valeur_bonus(lanceur, 'PAM');

	-- sur la position du lanceur
	select into v_pos_protegee 	coalesce(lieu_refuge, 'N')
	from perso_position
	left outer join lieu_position ON lpos_pos_cod = ppos_pos_cod
	left outer join lieu ON lieu_cod = lpos_lieu_cod
	where ppos_perso_cod = lanceur;
	if v_pos_protegee = 'O' then
		code_retour := '0;<p>Erreur ! Vous êtes sur un lieu refuge et ne pouvez donc pas lancer de sorts.</p>';
		return code_retour;
	end if;

	-- sur la cible + zone de droit
	select into
		nom_cible, pos_cible, type_cible, v_pos_pvp, v_gmon_cod, v_pos_protegee
		perso_nom, ppos_pos_cod, perso_type_perso, pos_pvp, perso_gmon_cod, coalesce(lieu_refuge, 'N')
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod
	inner join positions on pos_cod = ppos_pos_cod
	left outer join lieu_position ON lpos_pos_cod = pos_cod
	left outer join lieu ON lieu_cod = lpos_lieu_cod
	where perso_cod = cible and perso_actif = 'O';
	if not found then
		code_retour := code_retour||'0;<p>Erreur : cible non trouvée !</p>';
		return code_retour;
	end if;
	if type_attaquant != 2 and type_cible != 2 and v_pos_pvp = 'N' and offensif = 'O' then
		code_retour := '0;<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de lui lancer un sort offensif car elle n’est pas une engeance de Malkiar !<br />La zone de droit couvre tout l’Ouest de l’étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n’importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)</p>';
		return code_retour;
	elsif type_attaquant != 2 and type_cible = 2 and v_pos_pvp = 'N' and offensif = 'N' then
		code_retour := '0;<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de lui lancer un sort de soutien car elle est une engeance de Malkiar !<br />La zone de droit couvre tout l’Ouest de l’étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n’importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)</p>';
		return code_retour;
	end if;
	if v_pos_protegee = 'O' and offensif = 'O' then
		code_retour := '0;<p>Erreur ! Cette cible est sur un lieu protégé ! Elle ne peut pas être la cible d’un sort offensif.</p>';
		return code_retour;
	end if;

	-- contrôles de lancement
-- on vérifie les distances
	select into pos_cible,v_etage_cible
		ppos_pos_cod,pos_etage
		from perso_position,sorts,positions
		where ppos_perso_cod = cible
		and ppos_pos_cod = pos_cod;
	if(v_etage_lanceur != v_etage_cible) then
		code_retour := 'Erreur : Etages différents !';
		return code_retour;
	end if;
	distance_cibles := distance(pos_lanceur,pos_cible);
	if distance_cibles > distance_sort then
		code_retour := 'Erreur : La cible est trop éloignée pour lancer le sort !';
		return code_retour;
	end if;

	if distance_sort > 1 and trajectoire_vue_murs(pos_lanceur,pos_cible) != 1 then
		code_retour := 'Votre sort arrive dans un mur.';
		return code_retour;
	end if;


------------------------------------------------------------
-- les controles semblent bons, on peut passer à la suite
------------------------------------------------------------
	code_retour := code_retour||'<p>Vous avez lancé le sort <b>'||nom_sort||'</b>, sur la cible <b>'||nom_cible||'</b>, ';
  code_retour := code_retour||'en utilisant un objet.<br><br>';

  -- pour les sorts lancés à partir d'objet on met a jour le compteur (et on supprime le sort préparé)
  update objets_sorts set objsort_nb_utilisation=objsort_nb_utilisation+1 from objets_sorts_magie where objsortm_perso_cod = lanceur  and objsortm_objsort_cod=objsort_cod;

  -- Il y a certains objets qui possède un facteur de malchance, faisant échoué le lancement du sort
  if facteur_malchance >0 then
      des := 100 * lancer_des(1,100);   -- facteur_malchance a une précision à 0.01 %
      if des <= 100 * facteur_malchance then
        code_retour := code_retour||'Vous n''avez pas réussi à utiliser l''objet, le sortilège à <b>échoué</b>.<br><br>';

        texte_evt := '[attaquant] a tenté de lancer '||nom_sort||' sur [cible] et a échoué.';

        insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
        values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);

        if (lanceur != cible) then
          insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
              values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
        end if;

        update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
        code_retour := '0;'||code_retour;
        return code_retour;
      end if;
  end if;


	-- La cible est sous défense magique ?
	if valeur_bonus(cible, 'DFM') != 0 then
		code_retour := '0;'||code_retour||'Votre sort est rejeté car la cible est sous le coup d’une protection magique.<br />';
		update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
		texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] qui est protégé par un Défense magique.';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
				values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
		if (lanceur != cible) then
			insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
			values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
		end if;
		return code_retour;
	end if;

-------------------------
-- Lancer le Bonus/Malus !!!!
---------------------------

-------------------------
-- les EA liés au lancement d'un sort (avec protagoniste cible+null+lanceur)
---------------------------
  code_retour := code_retour || execute_fonctions(lanceur, null, 'MAL', json_build_object('num_sort', num_sort) );
  code_retour := code_retour || execute_fonctions(lanceur, cible, 'MAL', json_build_object('num_sort', num_sort) );
  code_retour := code_retour || execute_fonctions(cible, lanceur, 'MAC', json_build_object('num_sort', num_sort) );

-- ---------------------------
	return code_retour;
end;$_$;


ALTER FUNCTION public.sort_lance_bonus(integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION sort_lance_bonus(integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION sort_lance_bonus(integer, integer, integer) IS 'Lance un bonus comme si c`etait un sort';
