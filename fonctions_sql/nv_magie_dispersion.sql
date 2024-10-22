--
-- Name: nv_magie_dispersion(integer, integer, integer, json); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION nv_magie_dispersion(integer, integer, integer, json DEFAULT null) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function lancement : Dispersion                               */
/*  magique                                                      */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 22/09/2008                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
	nom_sort text;					-- nom du sort

-------------------------------------------------------------------------------
-- renseignements du lanceur
--------------------------------------------------------------------------------
	Lanceur alias for $1;
        v_perso_int integer;   -- intelligence du perso
        v_voie_magique integer; -- voie magique du lanceur
        pos_attaquant integer;
	bonus_total integer; /*Correspond au calcul des bonus de dégâts en fonction du monstre et de l'arme portée*/
	degats_portes integer;
	v_pv_attaquant integer;
	v_pvmax_attaquant integer;
	x_attaquant integer;
	y_attaquant integer;
	e_attaquant integer;
--------------------------------------------------------------------------------
-- renseignements de la cible
--------------------------------------------------------------------------------
	Cible alias for $2;			-- perso_cod cible initiale
         nb_sort_tour integer;
	v_dex_cible integer;
	v_con_cible integer;
	nouveau_pv_cible integer;
	etat_armure integer;
	armure_cible integer;			--Armure physique de la cible
	impact_armure numeric;			--Facteur lié à l'armure physique
	v_type_cible integer;			--détermine si monstre perso ou familier
	v_nom_cible text;
	v_pv_cible integer;
	compte_cible integer; 			--Numéro de compte associé à la cible
	lien_perso_fam integer;			--Familier de la cible
	v_obj integer;				--Equipement (arme) porté par la cible
	v_obj_deposable text;			--Equipement (arme) porté par la cible
	v_gobj_cod integer;			--Equipement (arme) porté par la cible
        v_cible_nouvelle integer;
--------------------------------------------------------------------------------
-- variables évènements
--------------------------------------------------------------------------------
	texte_evt text;
	texte_mort text;
	texte_mort_px text;
	compteur integer;
	position_arrivee integer;	--Détermine la position d'arrivée dans le cadre du jeu de trolls
	position_arrivee2 integer;	--Détermine la position d'arrivée dans le cadre du jeu de trolls en tenant compte des murs
	v_order integer;		--variable de calcul pour détermine un ordre aléatoire dans un select
	presence_mur integer;	        --Détermine la présence d'un mur sur la trajectoire (attaque de lancement)
	compt integer; 			--fourre tout
        v_distance integer;        -- distance de la projection
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;		-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;		-- Cout en PA du sort
	px_gagne numeric;		-- PX gagnes
	niveau_sort integer;	        -- niveau du sort
	v_params alias for $4;	-- dans certain cas on peut donner orienter la projection en donnant une direction
	v_direction varchar(2);     -- direction de la projection si donné par les parametres

-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;   -- chaine temporaire pour amélioration
        des integer;
        ligne record;
         v_reussite integer;
-------------------------------------------------------------

begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort
  v_direction :=  coalesce(v_params->>'direction'::text, '') ;    -- par defaut pojection dans une direction aléatoire
  if v_direction != '' then
    	num_sort := 178;    -- le cod du sort de projection avec une orientation
  else
	    num_sort := 142;    -- le code de sort projection simple
  end if;

-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
select into niveau_sort,nom_sort sort_niveau,sort_nom from sorts where sort_cod = num_sort;
select into v_nom_cible,v_pv_cible,v_type_cible perso_nom,perso_pv_max,perso_type_perso from perso where perso_cod = cible;
select into v_perso_int,v_voie_magique perso_int,perso_voie_magique from perso where perso_cod = lanceur;

-------------------------------------------------------------
-- prerequis
if cible = f_perso_monture(Lanceur)  then
		return 'Vous ne pouvez pas cibler votre propre monture..';
end if;

if v_type_cible = 3 then
		return 'Vous ne pouvez pas cibler un familier..';
end if;

-------------------------------------------------------------
-- magie commun
magie_commun_txt := magie_commun(lanceur,cible,type_lancer,num_sort);
res_commun := split_part(magie_commun_txt,';',1);
if res_commun = 0 then
   code_retour := split_part(magie_commun_txt,';',2);
   return code_retour;
end if;
code_retour := split_part(magie_commun_txt,';',3);
px_gagne := to_number(split_part(magie_commun_txt,';',4),'99999999999999D99');
v_reussite := to_number(split_part(magie_commun_txt,';',5),'99999999999999D99');

-------------------------------------
-- init des facteurs
-------------------------------------
select into pos_attaquant,v_pv_attaquant,v_pvmax_attaquant,x_attaquant,y_attaquant,e_attaquant ppos_pos_cod,perso_pv,perso_pv_max,pos_x,pos_y,	pos_etage
				from perso,perso_position,positions
				where perso_cod = Lanceur
				and ppos_perso_cod = Lanceur
				and ppos_pos_cod = pos_cod;

-----------    Début du bloc pour le jeu de Troll   -------------------

		-- on commence à générer un code retour
		code_retour := code_retour||'Vous avez projeté le ';
		if v_type_cible = 1 then
		code_retour := code_retour||'perso ';
		else
		code_retour := code_retour||'monstre ';
		end if;
		code_retour := code_retour||' <b>'||v_nom_cible||'</b> grace à votre magie<br>';

    ----------- calcul de la distance de projection
		des := lancer_des(1,100);
    if des < 50 or v_direction != '' then
        v_distance := 1;
    else
        v_distance := 2;
    end if;

    --- impacte de la voie magique sur la distance
    if v_voie_magique = 4 and v_direction = '' then
        v_distance := v_distance + 1;
        code_retour := code_retour||'votre connaissance magique vous permet d''accroitre la distance de projection';
    end if;

    /*********************************************/
    /* DEBUT : Lancement projection du perso     */
    /*********************************************/
		/* on sélectionne aléatoirement la case d arrivée,a v_distance max du lanceur*/

    if v_direction = '' then
        select into position_arrivee,v_order lancer_position,lancer_des(1,1000) from lancer_position(pos_attaquant,v_distance) order by v_order limit 1;
    elseif v_direction = 'NE' then -- nord/est
        select pos_cod into position_arrivee from positions where pos_etage = e_attaquant and pos_x = x_attaquant + 1 and pos_y = y_attaquant + 1 ;
    elseif v_direction = 'E' then -- est
        select pos_cod into position_arrivee from positions where pos_etage = e_attaquant and pos_x = x_attaquant + 1 and pos_y = y_attaquant ;
    elseif v_direction = 'SE' then -- sud/est
        select pos_cod into position_arrivee from positions where pos_etage = e_attaquant and pos_x = x_attaquant + 1 and pos_y = y_attaquant - 1;
    elseif v_direction = 'S' then -- sud
        select pos_cod into position_arrivee from positions where pos_etage = e_attaquant and pos_x = x_attaquant and pos_y = y_attaquant - 1 ;
    elseif v_direction = 'SO' then -- sud/ouest
        select pos_cod into position_arrivee from positions where pos_etage = e_attaquant and pos_x = x_attaquant -1 and pos_y = y_attaquant - 1 ;
    elseif v_direction = 'O' then -- ouest
        select pos_cod into position_arrivee from positions where pos_etage = e_attaquant and pos_x = x_attaquant -1 and pos_y = y_attaquant ;
    elseif v_direction = 'NO' then -- nord/ouest
        select pos_cod into position_arrivee from positions where pos_etage = e_attaquant and pos_x = x_attaquant -1 and pos_y = y_attaquant + 1 ;
    elseif v_direction = 'N' then -- nord
        select pos_cod into position_arrivee from positions where pos_etage = e_attaquant and pos_x = x_attaquant and pos_y = y_attaquant + 1 ;
    end if;

    if coalesce(position_arrivee,0) = 0 then
        position_arrivee :=  pos_attaquant; -- case par défaut si on ne trouve pas de case cible !
    end if;

		/* On vérifie qu'il n'y a pas un mur entre */
		if trajectoire_vue(pos_attaquant,position_arrivee) = 0 then /* il y a un mur sur le chemin ... ==> Dégâts supplémentaire et autre message*/
				presence_mur := 1;
		else
				presence_mur := 0;
		end if;
		position_arrivee2 := trajectoire_position(pos_attaquant,position_arrivee);
		if position_arrivee2 != 2 then
        update perso_position set ppos_pos_cod = position_arrivee2
                              where ppos_perso_cod = cible;
        delete from lock_combat where lock_cible =cible;
        delete from lock_combat where lock_attaquant = cible;

        select into lien_perso_fam max(pfam_familier_cod) from perso_familier INNER JOIN perso ON perso_cod=pfam_familier_cod WHERE perso_actif='O'
                    and pfam_perso_cod =cible;
        if found then
            update perso_position	set ppos_pos_cod = position_arrivee2
                                  where ppos_perso_cod = lien_perso_fam;

        end if;
		else
        code_retour := code_retour||'<br>Pb de position ! valeur de position_arrivee : '||to_char(coalesce(position_arrivee,0),'999999999')||',
                    valeur de position_attaquant : '||to_char(coalesce(pos_attaquant,0),'999999999')||',
                    valeur de position_arrivee2 : '||to_char(coalesce(position_arrivee2,0),'999999999');
		end if;


    /*********************************************/
    /* DEBUT : calcul des dégats portes          */
    /*********************************************/
    des := lancer_des(2,5);
    degats_portes := des;
    if presence_mur = 1 then
		    degats_portes := floor(degats_portes * v_distance);
    end if;
    code_retour := code_retour||'Vous projetez votre cible, qui subit alors <b>'||trim(to_char(degats_portes,'9999'))||'</b> points de dégâts.';
    if presence_mur = 1 then
        code_retour := code_retour||'<br>Elle vient s''écraser contre un mur';
    end if;
    etat_armure := f_use_armure(cible,degats_portes);
    insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee)
      values (1,lanceur,cible,degats_portes);
    if etat_armure = 2 then
        code_retour := code_retour||'Vous avez <b>brisé</b> l''armure de votre adversaire !<br>';
    end if;
    nouveau_pv_cible := v_pv_cible - degats_portes;

    /*****************************************/
    /* DEBUT : coup porté : cible morte      */
    /*****************************************/
    if nouveau_pv_cible <= 0 then -- la cible a été tuée......

        code_retour := code_retour||'Vous avez <b>tué</b> votre adversaire.<br>';
        texte_evt := '[attaquant] a projeté [cible] grace à sa magie lui infligeant '||trim(to_char(degats_portes,'9999'))||' points de dégats, le tuant sur le coup !';

        /* evts pour coup porté */
        insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                            values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
        if (lanceur != cible) then
            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
              values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
        end if;

        texte_mort := tue_perso_final(lanceur,cible);
        /*px_gagne := px_gagne + to_number(split_part(texte_mort,';',1),'9999999D99');*/
        /*code_retour := code_retour||'Vous gagnez '||trim(to_char(px_gagne,'99999'))||' PX pour cette action.';*/
        texte_mort_px := split_part(texte_mort,';',2);
        if (select perso_use_repart_auto from perso where perso_cod = lanceur) != 0 then
            if trim(texte_mort_px) is not null then
                code_retour := code_retour||texte_mort_px;
            end if;
        end if;
    /*****************************************/
    /* FIN   : coup porté : cible morte      */
    /*****************************************/

    /*****************************************/
    /* DEBUT : coup porté : cible pas morte  */
    /*****************************************/
	  else -- cible pas tuée
        texte_evt := '[attaquant] a projeté [cible] grace a sa magie lui infligeant '||trim(to_char(degats_portes,'9999'))||' points de dégats !';
          /* evts pour coups portes */
        insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                                  values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
           if (lanceur != cible) then
            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
              values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
           end if;

                        /*Action pour les PXs */
        insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee)
          values (4,cible,lanceur,degats_portes);

          /*MAJ des PV désactivé à retravailler pour cette attaque*/
        -- modif azaghal suite à bug..

        update perso set perso_pv = perso_pv - degats_portes where perso_cod = cible;

          -- update perso set perso_pv = nouveau_pv_cible where perso_cod = cible;
        code_retour := code_retour||'Votre adversaire a survécu à cette attaque. Il est maintenant <b>'||etat_perso(cible)||'</b>.<br>';

        /*code_retour := code_retour||'Vous gagnez '||trim(to_char(px_gagne,'99999'))||' PX pour cette action.';*/
        if code_retour is null then
            code_retour := 'erreur sur code_retour';
        end if;
    /*****************************************/
    /* FIN   : coup porté : cible pas morte  */
    /*****************************************/
    end if;
/*********************************************/
/* FIN : calcul des dégats portes          */
/*********************************************/

/*****************************************/
/* DEBUT : Calcul des dégâts collatéraux */
/*****************************************/
/* On va boucler sur les persos présents dans l alignement, en limitant à 7 max*/
for ligne in select personnage,lancer_des(1,1000),type_perso as num
	from trajectoire_perso(pos_attaquant,position_arrivee2) as (personnage int,v_pos int,type_perso int)
	                                where type_perso in (1,3)
                                        and not exists (select 1 from lieu, lieu_position
                                                       where lpos_pos_cod = v_pos
                                                       and lpos_lieu_cod = lieu_cod
                                                       and lieu_refuge = 'O')
					order by num limit 7 loop
					degats_portes := 0;
					degats_portes := floor((v_perso_int * v_con_cible) / 20);
					select into v_pv_cible perso_pv from perso
					where perso_cod = ligne.personnage;
				nouveau_pv_cible := v_pv_cible - degats_portes;

						if nouveau_pv_cible <= 0 then -- la cible a été tuée......
texte_evt := '[cible] a été touché par un projectile humain, subissant '||trim(to_char(degats_portes,'9999'))||' points de dégats, le tuant sur le coup !';
							   /* evts pour coup porté */
insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
												values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,ligne.personnage);
insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
												values(nextval('seq_levt_cod'),14,now(),1,ligne.personnage,texte_evt,'N','O',lanceur,ligne.personnage);
texte_mort := tue_perso_final(v_attaquant,ligne.personnage);
/*px_gagne := px_gagne + to_number(split_part(texte_mort,';',1),'9999999D99');*/
/*code_retour := code_retour||'Vous gagnez '||trim(to_char(px_gagne,'99999'))||' PX pour cette action.';*/
texte_mort_px := split_part(texte_mort,';',2);
if (select perso_use_repart_auto from perso where perso_cod = lanceur) != 0 then
   if trim(texte_mort_px) is not null then
   code_retour := code_retour||texte_mort_px;
   end if;
end if;

else -- cible pas tuée
texte_evt := '[cible] a été touché par un projectile humain, subissant  '||trim(to_char(degats_portes,'9999'))||' points de dégats !';
				/* evts pour coups portes */
insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
													values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,ligne.personnage);
   if (lanceur != cible) then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
   end if;

								/*Action pour les PXs */
insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee)
															values (4,v_cible_nouvelle,lanceur,degats_portes);

/*MAJ des PV désactivé à retravailler pour cette attaque*/
-- modif azaghal suite à bug..

update perso set perso_pv = perso_pv - degats_portes where perso_cod = ligne.personnage;
-- update perso set perso_pv = nouveau_pv_cible
																-- where perso_cod = ligne.personnage;
if code_retour is null then
code_retour := 'erreur sur code_retour';
end if;

end if;
end loop;
/*****************************************/
/* Fin : Calcul des dégâts collatéraux   */
/*****************************************/

		-----------      Fin du bloc pour le jeu de troll   -------------------

    ---------------------------
    -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#
    ---------------------------
    -- cas particulier: on ne déclenche que si le sort n'a pas ciblé un familier, les dommages collatéraux ne compte pas comme cible du sort
    code_retour := code_retour || execute_fonctions(lanceur, cible, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(cible, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

    ---------------------------
    -- les EA liés au déplacement (la projection est considéré comme un déplacement)
    ---------------------------
    code_retour := code_retour || execute_fonctions(cible, lanceur, 'DEP', json_build_object('ancien_pos_cod',pos_attaquant,'ancien_etage',e_attaquant, 'nouveau_pos_cod',position_arrivee2,'nouveau_etage',e_attaquant)) ;


return code_retour;
end;$_$;


ALTER FUNCTION public.nv_magie_dispersion(integer, integer, integer, json) OWNER TO delain;
