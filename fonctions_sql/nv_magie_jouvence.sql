-- Name: nv_magie_jouvence(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION nv_magie_jouvence(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function magie_jouvence : lance le sort fontaine de jouvence  */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
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
	v_type_perso integer;		-- type du lanceur
  v_voie_magique integer;
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	v_pos alias for $2;			-- position de la cible
	x_cible integer;			-- x  de la cible
	y_cible integer;			-- y  de la cible
	e_cible integer;			-- etage  de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne text;				-- PX gagnes
	v_bonus_toucher integer;	-- bonus toucher
	drain_pv integer;				-- nombre de PV retirés
	pv_lanceur integer;			-- pv du lanceur
	pv_max_lanceur integer;		-- pv max du lanceur
	diff_pv integer;				-- différence de pv
	v_pv integer;
	v_pv_max integer;
	nouveau_pv integer;
	amel_pv integer;
  v_bonus_flux integer;                   -- pvies gagnés grace a conscience des flux
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	ligne record;
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
	v_bloque_magie integer;		-- vérif si résistance magique
	v_monstre integer;			--numéro du monstre créé
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	nb_des integer;					-- nombre de de dés
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	temp_bonus integer;
	nb_cible integer;
	nb_cible2 integer;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 177;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
	select into nom_sort sort_nom from sorts
		where sort_cod = num_sort;
	magie_commun_txt := magie_commun_case(lanceur,v_pos,type_lancer,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	px_gagne := split_part(magie_commun_txt,';',4);

	-- Info sur le lanceur
	select into v_perso_niveau, v_type_perso, v_voie_magique perso_niveau, perso_type_perso, perso_voie_magique from perso where perso_cod = lanceur;
	-- pour soins important le bonus est doublé sous CFS
	v_bonus_flux := 2 * valeur_bonus(lanceur, 'CFS');

  -- nombre de cible max:  INT (INT/2 pour le maitre du savoir) avec une limitation à 50 (25)
	select into nb_cible case when perso_voie_magique=1 then ROUND(LEAST(50,perso_int)) else ROUND(LEAST(25,perso_int/2)) end from perso where perso_cod = lanceur;

  -- la case ciblée
	select into x_cible,y_cible,e_cible pos_x,pos_y,pos_etage from positions where pos_cod = v_pos;


  -- SI sur toutes les cibles de la case
	for ligne in
		select pos_cod, perso_cod, perso_nom, perso_pv, perso_pv_max, perso_sex
      from perso,perso_position,positions
      where ppos_perso_cod = perso_cod
		  and ppos_pos_cod = pos_cod
      and pos_x between (x_cible - 1) and (x_cible + 1)
      and pos_y between (y_cible - 1) and (y_cible + 1)
      and pos_etage = e_cible
      and not exists (select 1 from lieu_position,lieu where lpos_pos_cod = pos_cod and lpos_lieu_cod = lieu_cod and lieu_refuge = 'O')
      and perso_actif = 'O'
      and ((v_type_perso!=2 and perso_type_perso!=2) or (v_type_perso=2 and perso_type_perso=2))
      and perso_pv < perso_pv_max
      order by (100*perso_pv/perso_pv_max)
      limit nb_cible

  loop

      -- Si on est sur la case ciblé alors faire un SI sinon Merchu
      if ligne.pos_cod = v_pos then

        -- ------------------------------- Cas d'un SI
        nb_des := case when v_voie_magique = 1 then least(11, v_perso_niveau) else least(8, v_perso_niveau) end ;
        temp_bonus := lancer_des(nb_des,4);

      else

        -- ------------------------------- Cas d'un Mercu
        nb_des := case when v_voie_magique = 1 then least(5, v_perso_niveau) else least(3, v_perso_niveau) end ;
        temp_bonus := lancer_des(nb_des,4);

      end if;

      -- on regarde si la personne est malade
      temp_bonus := greatest(0, temp_bonus - valeur_bonus(ligne.perso_cod, 'MAL'));

      -- ------------------------------- on passe à l'augmentation de pvies
      nouveau_pv := ligne.perso_pv + temp_bonus;
      if nouveau_pv > ligne.perso_pv_max then
        nouveau_pv := ligne.perso_pv_max ;
      end if;
      update perso set perso_pv = nouveau_pv where perso_cod = ligne.perso_cod;
      perform soin_compteur_pvp(ligne.perso_cod);
      amel_pv := nouveau_pv - ligne.perso_pv ;

      if amel_pv > 0 then
        if (ligne.pos_cod = v_pos) then
          -- ------------------------------- Cas d'un SI
          insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (3,lanceur,ligne.perso_cod,5*ln(amel_pv));
        else
          -- ------------------------------- Cas d'un Mercu
          insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (3,lanceur,ligne.perso_cod,1);
        end if;
      end if;

      code_retour := code_retour||'<br>'||ligne.perso_nom||' a regagné '||trim(to_char(amel_pv,'9999'))||' points de vie.<br>';
      if lanceur = ligne.perso_cod then
        code_retour := code_retour||'Vous êtes maintenant ';
      elsif ligne.perso_sex = 'M' then
        code_retour := code_retour||'Il est maintenant ';
      else
        code_retour := code_retour||'Elle est maintenant ';
      end if;
      code_retour := code_retour||'<b>'||etat_perso(ligne.perso_cod)||'</b>.<br>';

      texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible], lui faisant gagner '||trim(to_char(amel_pv,'9999'))||' points de vie.';
      insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
        values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,ligne.perso_cod);

      if (lanceur != ligne.perso_cod) then
        insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
        values(nextval('seq_levt_cod'),14,now(),1,ligne.perso_cod,texte_evt,'N','O',lanceur,ligne.perso_cod);
      end if;

  end loop;


  -- ------------------------------- Conscience des flux de soin
  -- on regarde si le lanceur est sous conscience des flux de soin
  if v_bonus_flux > 0 then

    -- sinon on augmente les pvies du lanceur
    select into v_pv,v_pv_max perso_pv,perso_pv_max from perso where perso_cod = lanceur;
    nouveau_pv := v_pv + v_bonus_flux;
    if nouveau_pv > v_pv_max then
      v_bonus_flux := v_pv_max - v_pv;
      nouveau_pv := v_pv_max;
    end if;

    update perso set perso_pv = nouveau_pv where perso_cod = lanceur;
    code_retour := code_retour||'<br>Vous gagnez '||(v_bonus_flux::text)||' points de vie grace à la conscience des flux de soins.<br>';

  end if;

  -- ------------------------------- Fin
	code_retour := code_retour||'<br>Vous gagnez '||px_gagne||' PX pour cette action.<br>';
	return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_jouvence(integer, integer, integer) OWNER TO delain;
