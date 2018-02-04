--
-- Name: calcul_dlt2(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function calcul_dlt2(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* Fonction calcul_dlt2 : procédure de recalcul de la dlt        */
/* On passe en paramètres :                                      */
/*    1 : 1 pour un joueur, 2 pour un monstre                    */
/*    2 : le perso_cod si joueur, monstre_cod si monstre         */
/* Met à jour les tables avec les nouvelles dlt                  */
/* On a en sortie une chaine html utilisable                    */
/*****************************************************************/
/* Liste des modifications :                                     */
/*    06/03/2003 : ajout du malus dégats                         */
/*                 ajout de la régénération                      */
/*    02/04/2003 : changement du type de sortie  pour pouvoir    */
/*                 avoir plus d infos                            */
/*    20/06/2003 : malus lié à l encombrement                    */
/*    14/04/2004 : changement du code sortie                     */
/*    16/04/2004 : utilisation des PA restants pour raccourcir   */
/*    18/05/2004 : rajout de l intangibilité                     */
/*    17/09/2004 : ajout de la religion (actions génériques et   */
/*            persos)                                            */
/*    30/05/2007 : ajoute des compteurs pvp                      */
/*    14/01/2010 : ajoute des fonctions sur case                 */
/*    07/04/2010 : test sur la position des persos accompagnateurs*/
/*    11/02/2012 : nouvelle gestion familiers divins             */
/*    xx/xx/2012 : gestion des familiers à durée de vie          */
/*    27/06/2012 : limitation d’abus sur la mort des familiers   */
/*    25/09/2012 : limitation des 4e persos (désactivée mais codée)*/
/*    31/10/2012 : gestion des durées de vie des monstres        */
/*    18/01/2013 : màj des limitations des 4e persos             */
/*    26/01/2015 : BRU/VEN/MDS avec valeur négative soignent     */
/*                 désormais (cumul possible)                    */
/*****************************************************************/
declare
  dlt_actuelle perso.perso_dlt%type;
  temps_tour integer;
  nouvelle_dlt timestamp;
  personnage alias for $1;
  code_retour text;
  code_retour2 text;
  message_quatrieme text;
  temp_ajout_temps numeric;
  temp_ajout_temps_poids numeric;
  temp_ajout_temps2 integer;
  temp_ajout_temps_distortion numeric;
  temp_raccourci_temps numeric;
  temp_raccourci_temps2 numeric;
  raccourci_temps integer;
  raccourci_temps2 integer;
  entier_ajout_temps_poids integer;
  ajout_temps interval;
  ajout_temps_poids interval;
  format_num varchar;
  pv_actuel perso.perso_pv%type;
  pv_max perso.perso_pv_max%type;
  des_regen perso.perso_des_regen%type;
  valeur_regen perso.perso_valeur_regen%type;
  total_regen integer;
  regeneration integer;
  temps_blessures integer;
  temps_poids integer;
  temps_poids_max integer;
  v_amelioration_regen integer;
  compt integer;
  poids_actuel numeric;
  poids_max integer;
  nb_concentration integer;
  nv_nb_concentration integer;
  code_concentration integer;
  total_temps interval;
  l_objet record;
  bonus_regen integer;
  pa_restant integer;
  util_pa_restant integer;
  v_nb_intangible integer;
  v_niveau_vampire integer;
  v_perso_pnj integer;
  v_niveau integer;		--test sur l’étage adéquat des persos accompagnateurs
  temp_tue text;
  ligne record;
  v_type_perso integer;
  v_perso_mortel character;
  v_perso_px numeric;
  v_competence_max integer;
  v_actif text;
  temp_ia text;
  v_bonus_reg2 integer ;   	-- ajout d’un bonus en % sur les pvs max
  v_perso_priere integer;
  v_niveau_religion integer;
  v_points_divinite integer;
  v_familier_cod integer;		-- perso_cod du familier (s’il existe)
  v_priere_familier integer;	-- points de prière du familier divin
  v_vie_familier integer;		-- durée de vie restante du familier
  artefact_regen integer;
  louche_texte text;
  nb_objet integer;
  fonction_debut text;
  v_fonction_dessus text;
  v_fonction_dessus_result text;
  v_bonus_dlt integer;            --Bonus évitant le décalage de dlt à cause des blessures
  nb_potions integer;
  risque_tox integer;
  des_toxic integer;
  v_compt_pvp integer;
  v_dist_bonus numeric;
  v_dist_malus numeric;
  v_pa integer;			-- nombre de PA pour la nouvelle dlt afin de gerer hypnoptisme
  v_hyp integer;  		-- malus lie a l’hypnotpisme
  v_effets_auto integer; 	-- Active ou désactive les effets automatiques
  v_int integer;			-- Intelligence pour le calcul de l’énergie des enchanteurs
  v_titre text;
  v_corps text;
  v_mes integer;
  v_dgm numeric; 			-- valeur du garde manger
  v_dgm_int integer; 		-- valeur integer garde manger
  v_mds integer;                  -- Maladie du sang
  v_venin integer;                -- venin dans le sang
  v_brulure integer;              -- Brulure profonde
  v_bonus_regen integer;          -- Bonus cumulé (BRU/MDS/VEN)
  v_pv_actuel integer;            -- points de vie actuels
  v_memo_pv integer;              -- memorisation des pvs
  v_pv_max integer;               -- points de vie max
  summum_tox numeric;             -- Limite de toxicité
  malus_pa numeric;               -- malus aux pas si HYPNOTISME
  --Variables 1° avril snorkys
  etage_monstre integer;
  monstre integer;
  v_mon integer;
  resultat_intermediaire integer;
  v_limite_quatriemes smallint;   -- Vaut 1 pour limiter les quatrième persos, 0 sinon.
  v_duree_vie interval;   -- Durée de vie, utilisé pour les monstres.

begin
  v_limite_quatriemes := 1;
  message_quatrieme := '';
  format_num := '999';
  code_retour := '0;'; -- par défaut, tout est OK
  -----------------------------------------------------------------
  -- calcul dlt
  -----------------------------------------------------------------
  select into dlt_actuelle,v_niveau_vampire,v_type_perso,v_perso_priere,v_effets_auto,v_perso_pnj,v_actif,v_perso_mortel,v_perso_px
    perso_dlt,perso_niveau_vampire,perso_type_perso,perso_priere,perso_effets_auto,perso_pnj,perso_actif,perso_mortel,perso_px
  from perso
  where perso_cod = personnage for update;

  select into v_fonction_dessus,v_niveau pos_fonction_dessus,pos_etage
  from positions,perso_position
  where ppos_pos_cod = pos_cod
        and ppos_perso_cod = personnage;

  -- Tests spécifiques aux quatrièmes personnages
  if v_type_perso = 1 and v_perso_pnj = 2 then

    -- Rabotage des quatrièmes perso (PX, compétences)
    if v_limite_quatriemes = 1 then
      -- Première fois que le 4e perso se retrouve en position d’être raboté
      select into v_competence_max max(pcomp_modificateur) from perso_competences where pcomp_perso_cod = personnage;
      if v_perso_mortel is null and (v_perso_px > 1000 or v_competence_max > 85) then
        code_retour := '<hr /><p>Votre 4e personnage commence à atteindre un bon niveau.<br />
					Il a plus de 1000 PX, ou l’une de ses compétences a dépassé le seuil de 85 %.<br />
					<b>Félicitations !</b><br />Néanmoins, comme nous vous l’avons signalé lors de sa création, <b>ce personnage n’a pas vocation à devenir trop fort.</b><br />
					Nous vous laissons donc un choix : soit votre personnage vivra comme il l’a toujours fait, mais <b>ses PX seront limités à 1000, et ses compétences à 85</b> ;
					soit il ne sera pas limité, mais <b>deviendra mortel, de sorte que sa prochaine mort sera définitive.</b></p><br />
					<br /><p>Quel est votre choix ?</p>
					<p><a href="jeu_test/options_quatrieme_perso.php?methode=limitation&mortel=N">Je souhaite brider l’évolution de mon personnage.</a></p>
					<p><a href="jeu_test/options_quatrieme_perso.php?methode=limitation&mortel=O">Je souhaite rendre mon personnage mortel.</a></p>
					<p><br />Ce choix peut être modifié à tout moment dans la page « Gestion compte », rubrique « Paramétrer son 4e personnage ».</p>
					<p><br /><b>Attention</b> Si vous êtes de niveau égal ou supérieur à 15, brider signifiera perdre des améliorations de niveau. Le système ne pouvant pas deviner à votre place lesquelles perdre, elle seront toutes réinitialisées et vous devrez repasser vos niveaux.</p><hr />';
        return code_retour;
      elsif v_perso_mortel = 'N' then
        update perso_competences set pcomp_modificateur = min(85, pcomp_modificateur) where pcomp_perso_cod = personnage;
        update perso set perso_px = case when perso_px > 1000 then 1000 else perso_px end where perso_cod = personnage;
      end if;
    end if;

    -- Tests des intéractions 4e perso / triplette
    if exists (select * from ligne_evt where levt_perso_cod1 != levt_cible and levt_perso_cod1 = personnage and levt_cible in (select pcompt_perso_cod from perso_compte a where pcompt_compt_cod = (select pcompt_compt_cod from perso_compte b where b.pcompt_perso_cod = levt_perso_cod1))
               UNION ALL
               select * from ligne_evt where levt_perso_cod1 != levt_attaquant and levt_perso_cod1 = personnage and levt_attaquant in (select pcompt_perso_cod from perso_compte a where pcompt_compt_cod = (select pcompt_compt_cod from perso_compte b where b.pcompt_perso_cod = levt_perso_cod1)))
    then
      -- Interactions dans le compte !
      insert into triche (triche_perso_cod1, triche_perso_cod2, triche_cas_cod) values (personnage, 'Interaction détectée !', 7);
    end if;

    -- Tests de proximité 4e perso / triplette
    if exists (select 1 from perso triplette
      inner join perso_compte pc1 on pc1.pcompt_perso_cod = triplette.perso_cod
      inner join perso_compte pc2 on pc2.pcompt_compt_cod = pc1.pcompt_compt_cod
      inner join perso quatrieme on quatrieme.perso_cod = pc2.pcompt_perso_cod
      inner join compte on compt_cod = pc1.pcompt_compt_cod
    where triplette.perso_pnj <> 2 and quatrieme.perso_pnj = 2
          and triplette.perso_actif = 'O' and quatrieme.perso_actif = 'O'
          and controle_persos_proches(triplette.perso_cod, quatrieme.perso_cod, 10)
          and quatrieme.perso_cod = personnage)
    then
      message_quatrieme := '<hr><FONT SIZE=5><p><b>Attention !</b></p></FONT><p>Votre quatrième perso est dans les mêmes environs que l’un de vos personnages principaux.<br />
				Ce n’est pas répréhensible en soi, mais nous vous rappelons que les intéractions entre personnages principaux et quatrièmes personnages sont INTERDITES et sont surveillées.</p><hr>';
    end if;

    -- Tests de l’étage du quatrième perso
    if v_niveau not in (select etage_numero from etage
    where (etage_quatrieme_mortel = 'O' and COALESCE(v_perso_mortel, 'N') IN ('A', 'M', 'O'))
          OR (etage_quatrieme_perso = 'O' and COALESCE(v_perso_mortel, 'N') IN ('A', 'M', 'N')))
    then
      code_retour := '<hr><b><FONT SIZE=5><p>Vous n’êtes pas dans un étage autorisé !<br></FONT></b><hr>';
      update perso_position set ppos_pos_cod = 883 where ppos_perso_cod = personnage;

      v_corps := 'Le perso ' || personnage::text || ' a dépassé ses prérogatives, il a été renvoyé dans les extérieurs.<br>';
      v_mes := nextval('seq_msg_cod');
      v_titre := personnage::text || ' renvoyé aux extérieurs';
      v_titre := substring(v_titre from 1 for 50);

      insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
      values (v_mes,now(),now(),v_titre,v_corps);

      insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)
      values (v_mes,personnage,'N');

      insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
      values (v_mes,61,'N','N');

      insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
      values (v_mes,1740,'N','N');

      insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
      values (v_mes,personnage,'N','N');
      --insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_type) values (personnage,'[Mauvais joueur, frise l’exclusion]',6);
      return code_retour;
    end if;
  end if;

  if dlt_actuelle >= now() then
    /* La DLT n’est pas encore passée */
    code_retour := message_quatrieme;
  else
    --Mise en place des fonctions sur une case lors de l’activation du perso
    code_retour2 := '';

    if v_fonction_dessus is not null and v_fonction_dessus != '' then
      code_retour2 := '<p>';
      for ligne in (select foo from regexp_split_to_table(v_fonction_dessus,'#') as foo)
      loop
        v_fonction_dessus_result := '';
        v_fonction_dessus := 'select ' || replace(ligne.foo,'[perso]',to_char(personnage,'99999999999999'));
        execute v_fonction_dessus into v_fonction_dessus_result;
        code_retour2 := code_retour2 || v_fonction_dessus_result;
      end loop;
      code_retour2 := code_retour2 || '</p>';
    end if;
    if v_effets_auto = 1 and v_actif = 'O' then
      code_retour2 := code_retour2 || execute_fonctions(personnage, null, 'D');
    /*
          select into fonction_debut
            gmon_fonction_debut_dlt
          from perso,monstre_generique
          where perso_cod = personnage
            and perso_gmon_cod = gmon_cod;
          if found then
            if trim(fonction_debut) is not null then
              if trim(fonction_debut) != '' then
                fonction_debut := 'select ' || replace(fonction_debut,'[monstre]',trim(to_char(personnage,'99999999999999')));
                execute fonction_debut;
              end if;
            end if;
          end if;
    */
    end if;

    select into nb_objet count(perobj_cod) from perso_objets,objets
    where perobj_perso_cod = personnage
          and perobj_obj_cod = obj_cod
          and obj_gobj_cod = 186;

    if nb_objet = 0 then
      louche_texte := is_louche(personnage);
    end if;
    code_retour := '<p>Votre date limite de tour a été calculée<br>';
    code_retour := code_retour || message_quatrieme || code_retour2;

    /* la dlt est passée, on bosse */
    /* on récupère les infos */
    select into temps_tour, pv_actuel, pv_max, des_regen, valeur_regen, v_amelioration_regen,
      poids_actuel, poids_max, pa_restant, util_pa_restant, v_nb_intangible, v_int
      perso_temps_tour, perso_pv, perso_pv_max, perso_des_regen, perso_valeur_regen, perso_amelioration_regen,
      get_poids(personnage), perso_enc_max, perso_pa, perso_utl_pa_rest, perso_nb_tour_intangible, perso_int
    from perso
    where perso_cod = personnage;

    /* religion */
    select into v_niveau_religion,v_points_divinite, temp_tue
      dper_niveau,dper_points, dieu_nom
    from dieu_perso, dieu
    where dper_perso_cod = personnage and dper_dieu_cod = dieu_cod;
    if found then
      if v_niveau_religion >= 3 then
        if v_points_divinite >= getparm_n(53) then
          if v_perso_priere = 0 then
            update dieu_perso
            set dper_points = dper_points - 1
            where dper_perso_cod = personnage;
          end if;
        end if;
      end if;
      -- Bénédiction
      if v_niveau_religion > 1 then
        if lancer_des(1, 100) <= 3 * v_niveau_religion then
          compt := 2;
          perform ajoute_bonus(personnage, 'BEN'::text, ceil(v_niveau_religion/2::numeric)::integer, -5);
          code_retour := code_retour || temp_tue || ' vous bénit pour vos actions à venir. Vos chances de réussite sont donc accrues.<br />';
        end if;
      end if;
    end if;

    /* raccourci temps pour pa restants */
    if util_pa_restant = 1 then
      temp_raccourci_temps := (temps_tour*pa_restant)/12;
      temp_raccourci_temps := temp_raccourci_temps / 2;
      raccourci_temps := round(temp_raccourci_temps);
      if raccourci_temps != 0 then
        code_retour := code_retour || 'Il vous reste ' || trim(to_char(pa_restant,'999')) || ' PA. ';
        code_retour := code_retour || 'Votre date limite de tour a été raccourcie de <b>' || split_part(calcul_temps(raccourci_temps),';',1) || ' heures ';
        code_retour := code_retour || 'et ' || split_part(calcul_temps(raccourci_temps),';',2) || ' minutes</b>.<br>';
      end if;
    else
      raccourci_temps := 0;
    end if;

    /* régénération */
    if (pv_actuel < pv_max) then
      artefact_regen := bonus_art_reg(personnage);
      total_regen := lancer_des(des_regen,valeur_regen);
      total_regen := total_regen + v_amelioration_regen;
      total_regen := total_regen + valeur_bonus(personnage, 'REG');

      -- bonus de x ou x est le nombre de dés de regen/100 * pv max, limité à 25%
      select into v_bonus_reg2 perso_des_regen
      from perso
      where perso_cod = personnage;

      bonus_regen := floor((pv_max*v_bonus_reg2)/100);
      if bonus_regen > 0 then
        if bonus_regen > 25 then
          bonus_regen := 25;
        end if;
        total_regen := total_regen + bonus_regen;
      end if;

      total_regen := total_regen + artefact_regen + valeur_bonus(personnage, 'PRG');
      if total_regen < 0 then
        total_regen := 0;
      end if;
      if valeur_bonus(personnage, '0RG') != 0 then
        total_regen := 0;
      end if;
      regeneration := pv_actuel + total_regen;
      if total_regen > (pv_max - pv_actuel) then
        total_regen := pv_max - pv_actuel;
      end if;
      if v_niveau_vampire != 0 then
        total_regen := 0;
      end if;
      code_retour := code_retour || 'Vous avez régénéré <b>' || trim(to_char(total_regen,'99999')) || '</b> points de vie. ';
      if (regeneration > pv_max) then
        regeneration = pv_max;
      end if;
      code_retour := code_retour || 'Vous avez maintenant <b>' || trim(to_char(regeneration,'99999')) || '</b> points de vie.<br />';

      update perso
      set perso_pv = regeneration
      where perso_cod = personnage;

      pv_actuel := regeneration;
    end if;

    /* on calcule les dégats */
    temp_ajout_temps := (temps_tour*(pv_max-pv_actuel)/pv_max); /* dégats */
    temp_ajout_temps := temp_ajout_temps / 4;
    temps_blessures:= round(temp_ajout_temps);
    if valeur_bonus(personnage, 'PDL') != 0 then
      temps_blessures := 0;
      code_retour := code_retour || 'Grâce à l’ingestion d’une potion, votre temps de tour n’est pas décalé par vos blessures';
    end if;
    if temps_blessures != 0 then
      code_retour := code_retour || 'Votre date limite de tour a été repoussée de <b>' || split_part(calcul_temps(temps_blessures),';',1) || ' heures ';
      code_retour := code_retour || 'et ' || split_part(calcul_temps(temps_blessures),';',2) || ' minutes</b> à cause de vos blessures.<br>';
    end if;
    /* on calcule le temps lié au poids */
    if poids_actuel > poids_max then
      temp_ajout_temps_poids := (temps_tour*poids_actuel)/poids_max;
      temp_ajout_temps_poids := temp_ajout_temps_poids - temps_tour;
      temp_ajout_temps_poids := temp_ajout_temps_poids / 2;
      temp_ajout_temps_poids := round(temp_ajout_temps_poids);
      /* Modif Kahlann 23/12/2015 : malus temps lié au poids limité */
      temps_poids_max := getparm_n(127);
      if (temp_ajout_temps_poids > temps_poids_max)
      then temp_ajout_temps_poids := temps_poids_max;
      end if;
      /* Fin modif Kahlann */
      code_retour := code_retour || 'Votre date limite de tour a été repoussée de <b>' || split_part(calcul_temps(temp_ajout_temps_poids),';',1) || ' heures ';
      code_retour := code_retour || 'et ' || split_part(calcul_temps(temp_ajout_temps_poids),';',2) || ' minutes</b> à cause du poids transporté.<br>';
    else
      temp_ajout_temps_poids := 0;
    end if;
    -- distortion temporelle acceleration
    if valeur_bonus(personnage, 'DIT') > 0 then
      temp_raccourci_temps2 := valeur_bonus(personnage, 'DIT');
      raccourci_temps2 := round(temp_raccourci_temps2);
      if raccourci_temps2 != 0 then
        code_retour := code_retour || 'Votre date limite de tour a été raccourcie de <b>' || split_part(calcul_temps(raccourci_temps2),';',1) || ' heures ';
        code_retour := code_retour || 'et ' || split_part(calcul_temps(raccourci_temps2),';',2) || ' minutes</b> grâce à une distorsion temporelle.<br>';
      end if;
    else
      raccourci_temps2 := 0;
    end if;

    -- distortion temporelle ralentissement
    if valeur_bonus(personnage, 'DIS') > 0 then
      temp_ajout_temps_distortion := valeur_bonus(personnage, 'DIS');
      temp_ajout_temps2 := round(temp_ajout_temps_distortion);
      if temp_ajout_temps2 != 0 then
        code_retour := code_retour || 'Votre date limite de tour a été repoussée de <b>' || split_part(calcul_temps(temp_ajout_temps2),';',1) || ' heures ';
        code_retour := code_retour || 'et ' || split_part(calcul_temps(temp_ajout_temps2),';',2) || ' minutes</b> à cause d’une distorsion temporelle.<br>';
      end if;
    else
      temp_ajout_temps2 := 0;
    end if;
    temps_blessures:= temps_tour + temps_blessures + temp_ajout_temps_poids + temp_ajout_temps2 - raccourci_temps - raccourci_temps2 ;
    ajout_temps := to_char(temps_blessures,'999999') || ' minutes';
    nouvelle_dlt := ((dlt_actuelle)::timestamp + (ajout_temps)::interval);
    while nouvelle_dlt < now() loop
      nouvelle_dlt := ((nouvelle_dlt)::timestamp + (ajout_temps)::interval);
    end loop;
    /* intangibilité */
    if v_nb_intangible > 1 then
      update perso set perso_nb_tour_intangible = perso_nb_tour_intangible - 1
      where perso_cod = personnage;
    end if;
    if v_nb_intangible <= 1 then
      update perso set perso_nb_tour_intangible = 0, perso_tangible = 'O'
      where perso_cod = personnage;

      delete from groupe_perso where pgroupe_perso_cod = personnage and pgroupe_statut = 2;
    end if;
    /* garde manger */
    if valeur_bonus(personnage, 'DGM') != 0 then
      v_dgm := valeur_bonus(personnage, 'DGM');
      v_dgm_int := floor(v_dgm);
      update perso set perso_pv = perso_pv - v_dgm
      where perso_cod = personnage;
      code_retour := code_retour || '<br>Vous perdez <b>' || trim(to_char(v_dgm_int,'99999')) || '</b> points de vie. Sortez de ce garde-manger.<br> ';
      select into pv_actuel perso_pv from perso
      where perso_cod = personnage;
      if pv_actuel <= 0 then
        perform ajoute_bonus(personnage, 'DGM', 0, v_dgm);
        temp_tue := tue_perso_final(personnage,personnage);
        code_retour := code_retour || 'Vous êtes <b>mort !</b><br><br>';
      else
        v_dgm := v_dgm + 10;
        perform ajoute_bonus(personnage, 'DGM', 2, v_dgm);
        code_retour := code_retour || '<br>';
      end if;
    end if;

    /* poison- brûlure */
    select into artefact_regen coalesce(sum(obj_poison),0)
    from perso_objets,objets,objet_generique,objets_caracs
    where perobj_perso_cod = personnage
          and perobj_equipe = 'O'
          and perobj_obj_cod = obj_cod
          and obj_gobj_cod = gobj_cod
          and gobj_obcar_cod = obcar_cod;
    if not found then
      artefact_regen := 0;
    end if;
    v_brulure := valeur_bonus(personnage, 'BRU');
    v_mds := valeur_bonus(personnage, 'MDS');
    v_venin := valeur_bonus(personnage, 'VEN');
    bonus_regen := v_brulure + v_mds + v_venin + valeur_bonus(personnage, 'POI') + artefact_regen;


    if bonus_regen > 0 then -- POISON !!!!
      update perso set perso_pv = perso_pv - bonus_regen
      where perso_cod = personnage;

      code_retour := code_retour || '<br>Vous perdez <b>' || trim(to_char(bonus_regen,'99999')) || '</b> points de vie à cause du poison ou de vos brûlures.<br> ';

      select into pv_actuel perso_pv from perso
      where perso_cod = personnage;

      if pv_actuel <= 0 then
        temp_tue := tue_perso_final(personnage,personnage);
        code_retour := code_retour || 'Vous êtes <b>mort !</b><br><br>';
      else
        code_retour := code_retour || '<br>';
      end if;
    end if;
    /**********************************************************/
    -- Modif Kahlann 26/01/2015
    if bonus_regen < 0 then -- SOIN !!!!
      v_bonus_regen := abs(bonus_regen);
      select into pv_actuel perso_pv from perso
      where perso_cod = personnage;
      select into v_memo_pv perso_pv from perso
      where perso_cod = personnage;
      v_pv_actuel := pv_actuel + v_bonus_regen;
      select into v_pv_max perso_pv_max from perso
      where perso_cod = personnage;

      if v_pv_actuel > v_pv_max then
        v_pv_actuel := v_pv_max;
        v_bonus_regen := v_pv_max - v_memo_pv;
      end if;
      update perso set perso_pv = v_pv_actuel
      where perso_cod = personnage;
      code_retour := code_retour || '<br>Vous gagnez <b>' || trim(to_char(v_bonus_regen,'99999')) || '</b> points de vie grâce à une chaleur réconfortante.<br> ';
    end if;
    /**********************************************************/

    /* potions */
    /* on commence par la dissipation de la potion */
    update potions.perso_toxic
    set ptox_toxicite = ptox_toxicite - 5
    where ptox_perso_cod = personnage;

    delete from potions.perso_toxic
    where ptox_toxicite <= 0
          and ptox_perso_cod = personnage;

    /* ensuite, on recherche s’il ya plusieurs potions actives */
    select into nb_potions count(ptox_cod)
    from potions.perso_toxic
    where ptox_perso_cod = personnage;

    if nb_potions >= 2 then
      -- à partir d’ici, on a un risque de toxicité
      -- on calcule déjà le risque
      select into risque_tox
        sum(ptox_toxicite)
      from potions.perso_toxic
      where ptox_perso_cod = personnage;

      des_toxic := lancer_des(1,100);
      if des_toxic <= risque_tox then
        -- à partir d’ici, la toxicité est en route....
        code_retour := code_retour || '<p>Les diverses potions que vous avez ingérées vous intoxiquent ! ';
        code_retour := code_retour || potions.potion_toxique(personnage);
        -- il faut maintenant fusionner les toxicités en une seule
        select into des_toxic max(ptox_cod)
        from potions.perso_toxic
        where ptox_perso_cod = personnage;

        delete from potions.perso_toxic
        where ptox_perso_cod = personnage
              and ptox_cod != des_toxic;


        summum_tox := min(risque_tox * 2, 200);

        update potions.perso_toxic
        set ptox_toxicite = summum_tox
        where ptox_cod = des_toxic;
      end if;
    end if;

    -- Effets de désorientation sur les monstres en ia => on change de cible à chaque dlt
    if valeur_bonus(personnage, 'DES') > 0 then
      update perso set perso_cible = null where perso_cod = personnage;
    end if;

    /* esquive et spéciaux */
    update perso set perso_nb_esquive = 0,perso_priere = 0,perso_nb_ch_mcom = 0,perso_nb_spe = 1 where perso_cod = personnage;

    /* concentrations */
    select into code_concentration,nb_concentration concentration_cod,concentration_nb_tours
    from concentrations
    where concentration_perso_cod = personnage;
    if found then
      nv_nb_concentration = nb_concentration - 1;
      if nv_nb_concentration = 0 then
        delete from concentrations where concentration_cod = code_concentration;
      else
        update concentrations set concentration_nb_tours = nv_nb_concentration where concentration_cod = code_concentration;
      end if;
    end if;

    /* locks combat */
    update lock_combat set lock_nb_tours = lock_nb_tours - 1 where lock_attaquant = personnage;

    /* ripostes */
    update riposte set riposte_nb_tours = riposte_nb_tours - 1
    where riposte_cible = personnage;

    /* nombre competences */
    update perso_nb_comp
    set pnb_nombre = 0
    where pnb_perso_cod = personnage;

    /* nombre sorts */
    update perso_nb_sorts
    set pnbs_nombre = 0
    where pnbs_perso_cod = personnage;

    /* transactions */
    update transaction
    set tran_nb_tours = tran_nb_tours - 1
    where tran_acheteur = personnage;

    delete from transaction
    where tran_acheteur = personnage
          and tran_nb_tours <= 0;

    /* bonus */
    update bonus
    set bonus_nb_tours = bonus_nb_tours - 1
    where bonus_perso_cod = personnage;

    delete from bonus where bonus_nb_tours <= 0;

    /* caracs origine */
    update carac_orig
    set corig_nb_tours = corig_nb_tours - 1
    where corig_perso_cod = personnage;

    /* louche */
    update perso_louche
    set plouche_nb_tours = plouche_nb_tours - 1
    where plouche_perso_cod = personnage;


    /* identification objets */
    update perso_identifie_objet
    set pio_nb_tours = pio_nb_tours - 1
    where pio_perso_cod = personnage;


    /* on remet les pa */
    -- pour tout le monde c’est 12 PA sauf pour ceux sous hypnotisme
    malus_pa := valeur_bonus(personnage, 'HYP');
    if malus_pa < 0 then
      malus_pa := malus_pa * (-1);
    end if;
    v_pa := 12 - malus_pa;
    update perso
    set perso_dlt = nouvelle_dlt, perso_pa = v_pa
    where perso_cod = personnage;


    /* familier divin : on diminue l’énergie du familier lors de l’activation de la DLT du maître*/
    select into v_familier_cod, v_priere_familier perso_cod, dper_points
    from perso_familier
      inner join perso on perso_cod = pfam_familier_cod
      inner join dieu_perso on dper_perso_cod = pfam_familier_cod
    where pfam_perso_cod = personnage
          and perso_gmon_cod = 441
          and perso_actif='O';

    if found then
      v_priere_familier := v_priere_familier + v_niveau_religion - 8;
      update dieu_perso set dper_points = v_priere_familier where dper_perso_cod = v_familier_cod;
      if v_priere_familier <= 0 then
        -- Ajout de dégâts fictifs infligés par le familier lui-même pour éviter les abus
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (1, v_familier_cod, v_familier_cod, 200);

        temp_tue := tue_perso_final(v_familier_cod, v_familier_cod);
        code_retour := code_retour || '<br><p>Le regard que votre familier vous porte est emprunt de tristesse... Puis, doucement, son être s’estompe jusqu’à disparaître complètement.</p>';
      else
        if v_priere_familier <= 20 then
          code_retour := code_retour || '<br><p>Vous sentez votre familier s’agiter ; si vous ne faites rien, il devra retourner dans les royaumes divins.</p>';
        end if;
      end if;
    end if;

    /* familier à durée de vie : on diminue la durée de vie restante du familier lors de l’activation de la DLT du maître*/
    select into v_familier_cod, v_vie_familier perso_cod, pfam_duree_vie
    from perso_familier
      inner join perso on perso_cod = pfam_familier_cod
    where pfam_perso_cod = personnage
          and perso_actif='O'
          and pfam_duree_vie IS NOT NULL;

    if found then
      v_vie_familier := v_vie_familier - 1;
      update perso_familier set pfam_duree_vie = v_vie_familier where pfam_familier_cod = v_familier_cod;
      if v_vie_familier <= 10 and v_vie_familier > 5 then
        code_retour := code_retour || '<br><p>Vous constatez que votre familier commence à s’estomper.</p>';
      elsif v_vie_familier <= 5 and v_vie_familier > 1 then
        code_retour := code_retour || '<br><p>Votre familier devient franchement transparent... Il semble qu’il n’en ait plus pour longtemps.</p>';
      elsif v_vie_familier = 1 then
        code_retour := code_retour || '<br><p>Votre familier est sur le point de disparaître !</p>';
      elsif v_vie_familier <= 0 then
        -- Ajout de dégâts fictifs infligés par le familier lui-même pour éviter les abus
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (1, v_familier_cod, v_familier_cod, 200);

        temp_tue := tue_perso_final(v_familier_cod, v_familier_cod);
        code_retour := code_retour || '<br><p>De votre familier, seul reste, quelques secondes, son sourire suspendu dans le vide. Puis lui aussi disparaît...</p>';
      end if;
    end if;

    /* monstre à durée de vie limite : si la durée est dépassée, le monstre disparaît */
    if v_type_perso = 2 and v_actif = 'O' then
      select into v_duree_vie
        case when coalesce(gmon_duree_vie, 0) = 0 then '1 day'::interval
        else (gmon_duree_vie::text || ' days')::interval - (now() - perso_dcreat) end
      from perso,monstre_generique
      where perso_cod = personnage
            and perso_gmon_cod = gmon_cod;
      if found and v_duree_vie < '0'::interval then
        -- Ajout de dégâts fictifs infligés par le monstre sur lui-même pour éviter les abus
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (1, personnage, personnage, 2000);

        temp_tue := tue_perso_final(personnage, personnage);
        code_retour := code_retour || '<br><p>Lassé de votre vie ici bas, vous sortez des souterrains...</p>';
      end if;
    end if;

    /* compteurs pvp */
    select into v_compt_pvp
      perso_compt_pvp
    from perso
    where perso_cod = personnage
          and perso_dmodif_compt_pvp + (to_char(temps_tour,'99999999') || ' minutes')::interval < now();
    if found then
      if v_compt_pvp > 0 then
        update perso
        set perso_compt_pvp = perso_compt_pvp - 1,
          perso_dmodif_compt_pvp = now()
        where perso_cod = personnage;
      end if;
    end if;

    /* Compteur d’énergie des enchanteurs */
    update perso
    set perso_energie = min((perso_energie + (v_int / 2)),100)
    where perso_cod = personnage;

    /* Mise à jour des bonus évolutifs, en empêchant les changements de signe et les annulations */
    update bonus set bonus_valeur = bonus_valeur + bonus_croissance
    where bonus_perso_cod = personnage
          and sign(bonus_valeur) = sign(bonus_valeur + bonus_croissance)
          and bonus_valeur + bonus_croissance <> 0;
  end if;

  if v_perso_mortel = 'M' then
    code_retour := code_retour || '<br><p><b>VOUS ÊTES MORT !</b> Cette mort est, dans votre condition, définitive.
			<br />Profitez une dernière fois de votre personnage, vous n’y aurez plus accès par la suite.</p>';
    update perso set perso_actif = 'N', perso_pa = 0 where perso_cod = personnage;
  end if;
  return code_retour;
end;$_$;


ALTER FUNCTION public.calcul_dlt2(integer) OWNER TO delain;

--
-- Name: FUNCTION calcul_dlt2(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION calcul_dlt2(integer) IS 'C’est LA fonction appelée lors de l’activation d’une DLT.';