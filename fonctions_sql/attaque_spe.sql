CREATE OR REPLACE FUNCTION public.attaque_spe(
    integer,
    integer,
    integer)
  RETURNS text AS
$BODY$/*****************************************************************/
/* fonction attaque_spe : provoque une attaque spéciale provenant*/
/*                d’un monstre uniquement, fonction              */
/*                appelée par l’IA                               */
/* On passe en paramètres                                        */
/*    $1 = perso_cod attaquant                                   */
/*    $2 = perso_cod cible                                       */
/*    $3 = type d’attaque                                        */
/*      16 = balayage                                            */
/*      17 = garde manger                                        */
/*      18 = Hydre à neuf têtes                                  */
/*      19 = Jeu de Trolls                                       */
/*Toutes les autres attaques sont gérées dans la fonction normale*/
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 29/08/2006                                            */
/* Liste des modifications :                                     */
/*    2018-02-02 - LAG - correction du bug sur le decalage dlt   */
/*		+ Ajout du controle que l'attaquant possède la compétence*/
/*		+ Modification des degats du Balayage					 */
/*****************************************************************/
declare
  --------------------------------------------------------------------------------
  -- variables fourre tout
  --------------------------------------------------------------------------------
  code_retour text;
  des integer;
  ligne record;                  -- enregistrements
  --------------------------------------------------------------------------------
  -- renseignements de l attaquant
  --------------------------------------------------------------------------------
  v_attaquant alias for $1;
  v_type_attaque alias for $3;
  pos_attaquant integer;
  v_attaquant_dex integer;
  v_attaquant_for integer;
  comp_attaque integer;       -- niveau de réussite de la compétence calculé avec les carac de la cible
  comp_modificateur integer;  -- niveau de compétence de l'attaquant
  nb_des_attaque integer;
  valeur_des_attaque integer;
  bonus_attaque integer;
  nom_arme text;
  bonus_total integer;           --Correspond au calcul des bonus de dégâts en fonction du monstre et de l’arme portée*/
  degats_portes integer;
  v_amelioration_degats integer;
  v_type_arme integer;
  v_pv_attaquant integer;
  v_pvmax_attaquant integer;
  x_attaquant integer;
  y_attaquant integer;
  e_attaquant integer;
  tete text;                     --variable pour le paramétrage des attaques de l’hydre

  --------------------------------------------------------------------------------
  -- renseignements de la cible
  --------------------------------------------------------------------------------
  v_cible alias for $2;          -- perso_cod cible initiale
  v_cible_nouvelle integer;      -- Permet de calculer une nouvelle cible
  nb_cibles integer;             --nombre de cibles de l’attaque
  nombre_cible integer;          --nombre de cibles potentielles
  v_dex_cible integer;
  v_con_cible integer;
  v_aura_feu integer;
  v_degats_aura_feu integer;
  nouveau_pv_cible integer;
  etat_armure integer;
  armure_cible integer;          --Armure physique de la cible
  impact_armure numeric;         --Facteur lié à l’armure physique
  v_type_cible integer;          --détermine si monstre perso ou familier
  v_nom_cible text;
  v_pv_cible integer;
  lien_perso_fam integer;        --Familier de la cible
  v_obj integer;                 --Equipement (arme) porté par la cible
  v_obj_deposable text;          --Equipement (arme) porté par la cible
  --------------------------------------------------------------------------------
  -- variables évènements
  --------------------------------------------------------------------------------
  aura_texte text;
  texte_evt text;
  texte_mort text;
  px_gagne integer;
  texte_mort_px text;
  compteur integer;
  effet text;                    --effet produit par les têtes de l’hydre (poison ...)
  malus integer;                 --Détermine la valeur du malus appliqué suite à l’attaque de l’hydre
  position_arrivee integer;      --Détermine la position d’arrivée dans le cadre du jeu de trolls
  position_arrivee2 integer;     --Détermine la position d’arrivée dans le cadre du jeu de trolls en tenant compte des murs
  v_order integer;               --variable de calcul pour détermine un ordre aléatoire dans un select
  presence_mur integer;          --Détermine la présence d’un mur sur la trajectoire (attaque de lancement)
  decalage_dlt integer;          --Variable contenant le décalage de temps pour le balayage                                                                                                                                tempo integer;--integer pour le calcul de la fonction min du garde manger
  compt integer;                 --fourre tout
  num_comp_spe integer;          --code de la compétence pour vérifier que l'attaquant la possède
  v_type_de_monstre integer ;    -- pour la compétence multi-tête, les cthulhu ont des tentacules

begin
  code_retour := '';

  /********************************************************************************/
  /* DEBUT : Déterminer le type d'attaque                                         */
  /********************************************************************************/
  if v_type_attaque = 16 then
    num_comp_spe := 89;			---code du balayage
  elsif v_type_attaque = 17 then
    num_comp_spe := 94;			---code du garde manger
  elsif v_type_attaque = 18 then
    num_comp_spe := 95;			---code de l'Hydre à neuf têtes
  elsif v_type_attaque = 19 then
    num_comp_spe := 96;			---code du Jeu de Trolls
  else
    code_retour := '<p>Erreur ! Cette compétence est inconnue des attaques speciales!';
    return code_retour;
  end if;


  /********************************************************************************/
  -- Récupération des données de l’arme de l’attaquant
  select into comp_attaque, nb_des_attaque, valeur_des_attaque, bonus_attaque, nom_arme, v_type_de_monstre
    pcomp_modificateur, obj_des_degats, obj_val_des_degats, obj_bonus_degats, obj_nom_generique, case when perso_nom ilike '%cthulhu%' then 1 else 0 end
  from perso, perso_competences, perso_objets, objets, objet_generique, objets_caracs
  where perobj_perso_cod = v_attaquant
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = gobj_cod
        and gobj_tobj_cod = 1
        and gobj_obcar_cod = obcar_cod
        and gobj_comp_cod = pcomp_pcomp_cod
        and pcomp_perso_cod = v_attaquant
        and perso_cod = v_attaquant;

  if bonus_attaque is null then
    bonus_attaque := 0;
  end if;
  if v_amelioration_degats is null then
    v_amelioration_degats := 0;
  end if;

  -- Récupération des données de l’attaquant
  select into v_attaquant_dex, v_attaquant_for, v_amelioration_degats, pos_attaquant,
    v_pv_attaquant, v_pvmax_attaquant, x_attaquant, y_attaquant, e_attaquant
    perso_dex, perso_for, perso_amelioration_degats, ppos_pos_cod,
    perso_pv, perso_pv_max, pos_x, pos_y, pos_etage
  from perso, perso_position, positions
  where perso_cod = v_attaquant
        and ppos_perso_cod = v_attaquant
        and ppos_pos_cod = pos_cod;


  /********************************************************************************/
  /* DEBUT : Vérifier que l'attaquant possède la compétence                       */
  /********************************************************************************/
  select into comp_modificateur
    pcomp_modificateur
  from perso_competences
  where pcomp_perso_cod = v_attaquant
        and pcomp_pcomp_cod = num_comp_spe;
  if not found then
    code_retour := '<p>Erreur ! Vous n’avez la compétence requise pour ce type d’attaque !<br>';
    return code_retour;
  end if;
  /********************************************************************************/
  /* FIN   :  Vérifier que l'attaquant possède la compétence                      */
  /********************************************************************************/


  /********************************************************************************/
  /*           On détermine dans quel type d’attaque on se trouve.                */
  /*          On en déduit, soit attaque individuelle, soit multiple              */
  /********************************************************************************/

  -- Attaque par balayage --
  if v_type_attaque = 16 then
    --------------------------------------------------------------------------------
    --1° étape : On redétermine les cibles, car attaque multiple
    --------------------------------------------------------------------------------
    /*Détermination du nombre de cibles potentielles*/
    nb_cibles := v_attaquant_dex /3;
    code_retour := code_retour || '<b> Nombre de cibles maximum = ' || nb_cibles::text || '</b><br>';

    /*Sélection des n cibles en fonction de la contrainte*/
    for ligne in
    select perso_cod, perso_nom, perso_pv, perso_pv_max, perso_dex, perso_con, perso_type_perso, lancer_des(1, 1000) as num
    from perso, perso_position, positions
    where perso_actif = 'O'
          and perso_tangible = 'O'
          and ppos_perso_cod = perso_cod
          and ppos_pos_cod = pos_cod
          and pos_cod = pos_attaquant
          and perso_cod != v_attaquant
          and perso_type_perso != 2
    order by num limit nb_cibles
    loop
      /*On fait une boucle pour intervenir sur chaque cible*/
      /*Dans cette boucle, on lance les toucher, les dégâts ........ donc changement dans l’orga*/

      --------------------------------------------------------------------------------
      --2° étape : On attaque les différentes cibles
      --------------------------------------------------------------------------------
      /**************************************************/
      /* DEBUT : On cherche les comp à utiliser         */
      /**************************************************/
      if v_type_arme = 0 then         -- on teste l’arme équipée, car pour cette attaque, il faut une arme équipée
        code_retour := code_retour || 'Vous ne pouvez pas utiliser cette compétence sans arme<br>';
        return code_retour;
      else                                    -- arme équipée
        nom_arme := 'avec ' || nom_arme;
      end if;
      /**************************************************/
      /* FIN   : On cherche les comp à utiliser         */
      /**************************************************/

      /**************************************************/
      /* DEBUT : Modificateurs liés aux sorts ==> Non utilisé           */
      /**************************************************/
      comp_attaque := comp_attaque + valeur_bonus(v_attaquant, 'TOU');
      /**************************************************/
      /* FIN   : Modificateurs liés aux sorts           */
      /**************************************************/

      --------------------------------------------------------------------------------
      --Détermination pour chaque cible si elle est touchée
      --------------------------------------------------------------------------------
      /* la chance de réussite sur une cible, donc son esquive, est uniquement déterminée par la dextérité de la cible*/
      /* Ici la réussite de la compétence dépend de la cible, le niveau de competence de l'attaquant va influencer sur les dégats */
      comp_attaque := 100 - round((ligne.perso_dex * ligne.perso_dex * ligne.perso_dex) / 120);

      if comp_attaque < 10 then
        comp_attaque := 10;
      end if;

      -- on commence à générer un code retour
      code_retour := code_retour || 'Dex cible : ' || ligne.perso_dex::text || '<br>Chances de réussite' || comp_attaque::text || '<br>';
      code_retour := code_retour || 'Vous avez attaqué ';
      if ligne.perso_type_perso = 1 then
        code_retour := code_retour || 'l’aventurier ';
      elsif ligne.perso_type_perso = 2 then
        code_retour := code_retour || 'le monstre ';
      else
        code_retour := code_retour || 'le familier ';
      end if;

      code_retour := code_retour || ' <b>' || ligne.perso_nom || '</b> par un balayage.<br>';
      des := lancer_des(1, 100);

      /* DEBUT : attaque ratée sur échec critique               */
      if des > 96 then -- echec critique
        code_retour := code_retour || 'C’est un échec automatique !<br><br>';
        px_gagne := 0;
        texte_evt := '[attaquant] a lamentablement raté son balayage contre [cible]';
        perform insere_evenement(v_attaquant, ligne.perso_cod, 84, texte_evt, 'O', null);
        return code_retour;
      end if;
      /* FIN   : attaque ratée sur échec critique               */

      /* DEBUT : attaque réussie, on va résoudre l’ensemble     */
      if des < comp_attaque then
        code_retour := code_retour || 'Il n’a pas pu esquiver votre attaque, et se la prend de plein fouet<br>';

        /* DEBUT : aura de feu      */
        if v_type_arme != 2 then
          v_aura_feu := bonus_art_aura_feu(ligne.perso_cod);
          if v_aura_feu != 0 then
            v_degats_aura_feu := round(degats_portes * v_aura_feu);
            if v_degats_aura_feu < 1 then
              v_degats_aura_feu := 1;
            end if;
            code_retour := code_retour || '<br>L’aura de feu de votre adversaire vous provoque <b>' || trim(to_char(v_degats_aura_feu, '99999999')) || '</b> dégâts.<br>';
            if v_degats_aura_feu > v_pv_attaquant then
              code_retour := code_retour || 'Vous avez été <b>tué</b> par l’aura de feu de votre adversaire !<br>';
              aura_texte := tue_perso_final(ligne.perso_cod, v_attaquant);
            else
              update perso set perso_pv = perso_pv - v_degats_aura_feu where perso_cod = v_attaquant;
            end if;
            code_retour := code_retour || '<br>';
            texte_evt := 'L’aura de feu de [attaquant] a causé ' || trim(to_char(v_degats_aura_feu, '9999')) || ' dégâts à [cible].';

            perform insere_evenement(ligne.perso_cod, v_attaquant, 46, texte_evt, 'N', null);
          end if;
        end if;
        /* FIN   : aura de feu      */

        /* DEBUT : calcul des dégâts portés          */
        armure_cible := f_armure_perso_physique(ligne.perso_cod);
        if armure_cible < 11 then
          impact_armure := ((10-armure_cible) / 10) + 6;
        else
          impact_armure := 6;
        end if;

        degats_portes := floor(ligne.perso_con * impact_armure * comp_modificateur /100 ); --Calcul des dégâts

        -- 2019-05-19 - Marlyza: faire la moitié des dégats sur les familiers
        if ligne.perso_type_perso = 3 then
          code_retour := code_retour || '<br>Dégâts avant la réduction de familier : ' || trim(to_char(degats_portes, '9999')) ;
          degats_portes := floor(0.5 * degats_portes) ;
        end if;

        /***************Code d’analyse**********/
        code_retour := code_retour || '<br>Impact armure : ' || to_char(coalesce(impact_armure, 0), '999999999') || ',
					dégâts portés : ' || to_char(coalesce(degats_portes, 0), '999999999') || ',
					Constit de la cible : ' || to_char(coalesce(ligne.perso_con, 0), '999999999');
        /***************Code d’analyse**********/

        if degats_portes <= 0 or degats_portes is null then
          degats_portes := 0;
        end if; -- degats
        code_retour := code_retour || 'Vous portez une attaque de <b>' || trim(to_char(degats_portes, '9999')) || '</b> ';

        etat_armure := f_use_armure(ligne.perso_cod, degats_portes);
        if etat_armure = 2 then
          code_retour := code_retour || 'Vous avez <b>brisé</b> l’armure de votre adversaire !<br>';
        end if;

        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (1, v_attaquant, ligne.perso_cod, degats_portes);

        nouveau_pv_cible := ligne.perso_pv - degats_portes;

        /* DEBUT : coup porté : cible morte      */
        if nouveau_pv_cible <= 0 then -- la cible a été tuée......
          code_retour := code_retour || 'Vous avez <b>tué</b> votre adversaire.<br>';
          texte_evt := '[attaquant] a balayé [cible] , infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts.';
          /* evts pour coup porté */
          perform insere_evenement(v_attaquant, ligne.perso_cod, 84, texte_evt, 'O', null);
          texte_mort := tue_perso_final(v_attaquant, ligne.perso_cod);
          texte_mort_px := split_part(texte_mort, ';', 2);
          if (select perso_use_repart_auto from perso where perso_cod = v_attaquant) != 0 then
            if trim(texte_mort_px) is not null then
              code_retour := code_retour || texte_mort_px;
            end if;
          end if;
        /* FIN   : coup porté : cible morte      */

        /* DEBUT : coup porté : cible pas morte  */
        else -- cible pas tuée
          texte_evt := '[attaquant] a balayé [cible], infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts, et le sonnant légèrement.';
          /* evts pour coups portes */
          perform insere_evenement(v_attaquant, ligne.perso_cod, 84, texte_evt, 'O', null);

          insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
          values(4, ligne.perso_cod, v_attaquant, degats_portes);

          /*MAJ des PV */
          update perso set perso_pv = nouveau_pv_cible
          where perso_cod = ligne.perso_cod;
          code_retour := code_retour || 'Votre adversaire a survécu à cette attaque. Il est maintenant <b>' || etat_perso(ligne.perso_cod) || '</b>, et un peu sonné.<br>';

          /* on décale la dlt de x minutes */
          decalage_dlt := round((armure_cible * armure_cible + 5) / 2);
          update perso set perso_dlt = perso_dlt + (decalage_dlt::text || ' minutes')::interval
          where perso_cod = ligne.perso_cod;
          /***************Code d’analyse**********/
          code_retour := code_retour || '<br>Décalage Dlt : ' || to_char(coalesce(decalage_dlt, 0), '999999999');
          /***************Code d’analyse**********/

          if code_retour is null then
            code_retour := 'erreur sur code_retour';
          end if;
        /* FIN   : coup porté : cible pas morte  */
        end if; --fin coup porté non esquivé
      /* FIN : attaque réussie                              */
      else
        /* DEBUT : attaque esquivée par la cible                  */
        code_retour := code_retour || 'Très adroit, il a réussi à l’éviter<br>';
        texte_evt := '[cible] a adroitement réussi à éviter le balayage de [attaquant]';
        perform insere_evenement(v_attaquant, ligne.perso_cod, 84, texte_evt, 'O', null);
      /* FIN : attaque esquivée par la cible                    */
      end if;--fin de résolution de l’attaque

      --Fin de la boucle pour une cible
    end loop;
  -- FIN - Attaque par balayage --

  -- Attaque garde manger --
  elsif v_type_attaque = 17 then
    --------------------------------------------------------------------------------
    --Détermination si la cible est affectée
    -- la chance de réussite sur une cible, donc son esquive, est uniquement déterminée par la dextérité de la cible
    --------------------------------------------------------------------------------
    -- Données de la cible
    select into v_dex_cible, v_con_cible, v_type_cible, v_nom_cible, v_pv_cible
      perso_dex, perso_con, perso_type_perso, perso_nom, perso_pv
    from perso
    where perso_cod = v_cible and perso_pnj <> 1  ;  -- 2024-09-11 - Marlyza - annuler l'attaque si la cible est un PNJ
    if not found then
         return code_retour || 'Il n''est pas possible de mettre un PNJ dans le garde manger ';
    end if;

    comp_attaque := 100 - round((v_dex_cible * v_dex_cible * v_dex_cible) / 120);
    code_retour := code_retour || 'Chance de réussite de cette attaque : ' || comp_attaque::text || '<br>';
    if comp_attaque < 10 then
      comp_attaque := 10;
    end if;

    -- on commence à générer un code retour
    code_retour := code_retour || 'Vous avez attaqué ';
    if v_type_cible = 1 then
      code_retour := code_retour || 'l’aventurier ';
    else
      code_retour := code_retour || 'le monstre ';
    end if;
    code_retour := code_retour || ' <b>' || v_nom_cible || '</b> en l’attrapant pour le mettre dans votre sac<br>';

    des := lancer_des(1, 100);
    /* DEBUT : attaque ratée sur échec critique               */
    if des > 96 then -- echec critique
      code_retour := code_retour || 'C’est un échec automatique !<br><br>';
      px_gagne := 0;

      texte_evt := '[attaquant] a lamentablement raté l’attrapage de [cible]';
      perform insere_evenement(v_attaquant, v_cible, 78, texte_evt, 'O', null);

      return code_retour;
    end if;
    /* FIN   : attaque ratée sur échec critique               */

    /* DEBUT : attaque réussie, on va résoudre l’ensemble     */
    if des < comp_attaque then
      code_retour := code_retour || 'Il n’a pas pu esquiver votre attaque, et finit dans le sac.<br>';
      /* Pas d’aura de feu pour le garde manger */
      /* DEBUT : calcul des dégâts portes          */
      armure_cible := f_armure_perso_physique(v_cible);
      if armure_cible < 11 then
        impact_armure := (armure_cible / 10) + 1;
      else
        impact_armure := 2;
      end if;

      degats_portes := floor(v_attaquant_for * impact_armure); --Calcul des dégâts
      /***************Code d’analyse**********/
      code_retour := code_retour || '<br>Impact armure : ' || to_char(coalesce(impact_armure, 0), '999999999') || ',
				dégâts portés : ' || to_char(coalesce(degats_portes, 0), '999999999') || ',
				Constit de la cible : ' || to_char(coalesce(v_con_cible, 0), '999999999');
      /***************Code d’analyse**********/

      if degats_portes <= 0 or degats_portes is null then
        degats_portes := 0;
      end if; -- degats

      code_retour := code_retour || 'Vous écrasez votre cible, qui subit alors <b>' || trim(to_char(degats_portes, '9999')) || '</b> points de dégâts.';
      etat_armure := f_use_armure(v_cible, degats_portes);
      if etat_armure = 2 then
        code_retour := code_retour || 'Vous avez <b>brisé</b> l’armure de votre adversaire !<br>';
      end if;

      insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
      values (1, v_attaquant, v_cible, degats_portes);

      nouveau_pv_cible := v_pv_cible - degats_portes;

      /* DEBUT : coup porté : cible morte      */
      if nouveau_pv_cible <= 0 then -- la cible a été tuée......
        code_retour := code_retour || 'Vous avez <b>tué</b> votre adversaire.<br>';
        texte_evt := '[attaquant] a attrappé [cible] , infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts, mais surtout le fourrant dans son sac, et le tuant ! Son corps ne pourra rejoindre un dispensaire que plus tard';
        /* evts pour coup porté */
        perform insere_evenement(v_attaquant, v_cible, 78, texte_evt, 'O', null);
        texte_mort := tue_perso_final(v_attaquant, v_cible);
        texte_mort_px := split_part(texte_mort, ';', 2);
        if (select perso_use_repart_auto from perso where perso_cod = v_attaquant) != 0 then
          if trim(texte_mort_px) is not null then
            code_retour := code_retour || texte_mort_px;
          end if;
        end if;
      /* FIN   : coup porté : cible morte      */
      else -- cible pas tuée
        /* DEBUT : coup porté : cible pas morte  */
        if (v_type_cible != 3) then
          texte_evt := '[attaquant] a attrappé [cible] , infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts, mais surtout le fourrant dans son sac en le broyant au passage !';
        else -- Si de type familier
          texte_evt := '[attaquant] a mâchouillé [cible], lui infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts avant de le recracher au sol.';
        end if;
        /* evts pour coups portes */
        perform insere_evenement(v_attaquant, v_cible, 78, texte_evt, 'O', null);
        /*Action pour les PXs */
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (4, v_cible, v_attaquant, degats_portes);

        /*MAJ des PV */
        update perso set perso_pv = nouveau_pv_cible
        where perso_cod = v_cible;
        code_retour := code_retour || 'Votre adversaire a survécu à cette attaque. Il est maintenant <b>' || etat_perso(v_cible) || '</b>.<br>';
        if code_retour is null then
          code_retour := 'erreur sur code_retour';
        end if;

        /* DEBUT : Mise dans le sac car cible pas morte */
        if (v_type_cible != 3) then -- Un familier ne doit pas être séparé de son maître.
          perform entrer_gm(v_cible);
        end if;
      /* Fin : Mise dans le sac                */
      /* FIN   : coup porté : cible pas morte  */
      end if;
    /* FIN : attaque réussie                                        */
    else
      /* DEBUT : attaque esquivée par la cible                  */
      code_retour := code_retour || 'Très adroit, il a réussi à l’éviter<br>';
      texte_evt := '[cible] a adroitement réussi à éviter de se faire attraper par [attaquant]';
      perform insere_evenement(v_attaquant, v_cible, 78, texte_evt, 'O', null);
    /* FIN : attaque esquivée par la cible                    */
    end if; --fin d’attaque réussie
  -- FIN - Attaque garde manger --

  -- Attaque Hydre à neuf têtes --
  elsif v_type_attaque = 18 then
    /*Détermination du nombre de cibles potentielles*/
    if (v_pvmax_attaquant > 1000) then
      /* Les hydres de plus de 1000pv (cas de omega hydre), sont des hydres accomplies, même blessées elles restent dangeureuses */
      nb_cibles := 9 ;
    else
      nb_cibles := least(round((((v_pv_attaquant + 120) / v_pvmax_attaquant::numeric ) * 9), 0), 9);
    end if;
    code_retour := code_retour || '<b> Nombre de cibles maximum = ' || nb_cibles::text || '</b><br>';
    select into nombre_cible count(perso_cod)
    from perso, perso_position, positions
    where perso_actif = 'O'
          and ppos_perso_cod = perso_cod
          and ppos_pos_cod = pos_cod
          and perso_tangible = 'O'
          and pos_x between (x_attaquant - 1) and (x_attaquant + 1)
          and pos_y between (y_attaquant - 1) and (y_attaquant + 1)
          and pos_etage = e_attaquant
          and perso_cod != v_attaquant
          and perso_type_perso != 2 --------enlevé pour test
          and not exists(select 1 from lieu_position, lieu
    where lpos_pos_cod = pos_cod
          and lieu_refuge = 'O');
    code_retour := code_retour || '<b> Nombre de cibles potentielles = ' || nombre_cible::text || '</b><br>';
    /*Si pas assez de cibles potentielles, on limite le nombre pour limiter l’horreur*/
    if nombre_cible < nb_cibles and nombre_cible <= 4 then
      nb_cibles = nombre_cible + 2;
    end if;
    compteur := 0;
    code_retour := code_retour || '<b> Nombre de cibles réelles = ' || nb_cibles::text || '</b><br>';

    while compteur < nb_cibles loop
      /*Sélection des n cibles en fonction de la contrainte*/
      for ligne in
      select perso_cod, perso_nom, perso_pv, perso_pv_max, perso_dex, perso_con, perso_type_perso, lancer_des(1, 1000) as num, ppos_pos_cod
      from perso, perso_position, positions
      where perso_actif = 'O'
            and ppos_perso_cod = perso_cod
            and ppos_pos_cod = pos_cod
            and perso_tangible = 'O'
            and pos_x between (x_attaquant - 1) and (x_attaquant + 1)
            and pos_y between (y_attaquant - 1) and (y_attaquant + 1)
            and pos_etage = e_attaquant
            and perso_cod != v_attaquant
            and perso_type_perso != 2 --------enlevé pour test
            and not exists (select 1 from lieu_position inner join lieu on lpos_lieu_cod=lieu_cod
      where lpos_pos_cod = pos_cod
            and lieu_refuge = 'O')
      order by num limit 1
      loop
        /*On fait une boucle pour intervenir sur chaque cible*/
        /*Dans cette boucle, on lance les toucher, les dégâts ........ donc changement dans l’orga*/

        comp_attaque := 100 - round((ligne.perso_dex * ligne.perso_dex * ligne.perso_dex) / 120);
        code_retour := code_retour || 'Chance de réussite de cette attaque : ' || comp_attaque::text || '<br>Dex de la cible' || ligne.perso_dex::text || '<br>';
        if comp_attaque < 10 then
          comp_attaque := 10;
        end if;

        -- select perso_nom from perso where perso_nom ilike '%cthulhu%'
        -- pour les monstres cthulhu, on change la tête par un tentacule !


        /* on détermine les variables des têtes, pour les textes, et pour les effets*/
        tete := CASE WHEN v_type_de_monstre=1 THEN 'Le ' ELSE 'La ' END ; --initialisation de variable
        effet := '';
        texte_evt := '';
        if compteur = 0 then
          tete := tete || CASE WHEN v_type_de_monstre=1 THEN 'premier ' ELSE 'première ' END ;
        elsif compteur = 1 then
          tete := tete || CASE WHEN v_type_de_monstre=1 THEN 'second ' ELSE 'seconde ' END ;
          effet := 'POI';
        elsif compteur = 2 then
          tete := tete || 'troisième ';
        elsif compteur = 3 then
          tete := tete || 'quatrième ';
        elsif compteur = 4 then
          tete := tete || 'cinquième ';
          effet := 'ARME';
        elsif compteur = 5 then
          tete := tete || 'sixième ';
          effet := 'ROUILLE';
        elsif compteur = 6 then
          tete := tete || 'septième ';
          effet := 'RECUL';
        elsif compteur = 7 then
          tete := tete || 'huitième ';
          effet := 'POI';
        elsif compteur = 8 then
          tete := tete || 'neuvième ';
          effet := 'ARME';
        end if;

        tete := tete || CASE WHEN v_type_de_monstre=1 THEN 'tentacule ' ELSE 'tête' END ; --initialisation de variable

        -- on commence à générer un code retour
        code_retour := code_retour || tete || ' a attaqué ';

        if ligne.perso_type_perso = 1 then
          code_retour := code_retour || 'l’aventurier ';
        elsif ligne.perso_type_perso = 2 then
          code_retour := code_retour || 'le monstre ';
        else
          code_retour := code_retour || 'le familier ';
        end if;
        code_retour := code_retour || ' <b>' || ligne.perso_nom || '</b>.<br>';
        des := lancer_des(1, 100);

        if des > 96 then -- echec critique
          /* DEBUT : attaque ratée sur échec critique               */
          code_retour := code_retour || '<b>C’est un échec automatique !</b><br><br>';
          px_gagne := 0;
          texte_evt := '[attaquant] a lamentablement raté son attaque contre [cible]';
          perform insere_evenement(v_attaquant, ligne.perso_cod, 84, texte_evt, 'O', null);
        /* FIN   : attaque ratée sur échec critique               */
        elsif des < comp_attaque then
          /* DEBUT : attaque réussie, on va résoudre l’ensemble     */
          code_retour := code_retour || 'Il n’a pas pu esquiver votre attaque, et ' || (CASE WHEN  v_type_de_monstre=1 THEN 'le tentacule' ELSE 'la tête' END) || '  a plongé sur lui.<br>';

          /* DEBUT : aura de feu      */
          if v_type_arme != 2 then
            v_aura_feu := bonus_art_aura_feu(ligne.perso_cod);
            if v_aura_feu != 0 then
              v_degats_aura_feu := round(degats_portes * v_aura_feu);
              if v_degats_aura_feu < 1 then
                v_degats_aura_feu := 1;
              end if;
              code_retour := code_retour || '<br>L’aura de feu de votre adversaire vous provoque <b>' || trim(to_char(v_degats_aura_feu, '99999999')) || '</b> dégâts.<br>';
              if v_degats_aura_feu > v_pv_attaquant then
                code_retour := code_retour || 'Vous avez été <b>tué</b> par l’aura de feu de votre adversaire !<br>';
                aura_texte := tue_perso_final(ligne.perso_cod, v_attaquant);
              else
                update perso set perso_pv = perso_pv - v_degats_aura_feu where perso_cod = v_attaquant;
              end if;
              code_retour := code_retour || '<br>';
              texte_evt := 'L’aura de feu de [attaquant] a causé ' || trim(to_char(v_degats_aura_feu, '9999')) || ' dégâts à [cible].';
              perform insere_evenement(ligne.perso_cod, v_attaquant, 46, texte_evt, 'N', null);
            end if;
          end if;
          /* FIN   : aura de feu      */
          /* DEBUT : calcul des dégâts portés          */
          bonus_total := bonus_attaque + v_amelioration_degats + bonus_degats_melee(v_attaquant);
          degats_portes := 0; /*Initialisation des dégâts*/
          degats_portes := lancer_des(nb_des_attaque, valeur_des_attaque) + bonus_total + lancer_des(2, 10);
          degats_portes := degats_portes + valeur_bonus(v_attaquant, 'DEG');
          if degats_portes <= 0 or degats_portes is null then
            degats_portes := 0;
          end if; -- degats
          code_retour := code_retour || 'Vous portez une attaque de <b>' || trim(to_char(degats_portes, '9999')) || '</b> ';
          etat_armure := f_use_armure(ligne.perso_cod, degats_portes); /*L’armure s’use sous les coups*/

          if etat_armure = 2 then
            code_retour := code_retour || 'Vous avez <b>brisé</b> l’armure de votre adversaire !<br>';
          end if;
          insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
          values (1, v_attaquant, ligne.perso_cod, degats_portes);
          nouveau_pv_cible := ligne.perso_pv - degats_portes;

          /* DEBUT : coup porté : cible morte      */
          if nouveau_pv_cible <= 0 then -- la cible a été tuée......
            code_retour := code_retour || 'Vous avez <b>tué</b> votre adversaire.<br>';
            texte_evt := '[attaquant] a attaqué [cible] , infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts.';
            /* evts pour coup porté */
            perform insere_evenement(v_attaquant, ligne.perso_cod, 84, texte_evt, 'O', null);
            texte_mort := tue_perso_final(v_attaquant, ligne.perso_cod);
            texte_mort_px := split_part(texte_mort, ';', 2);
            if (select perso_use_repart_auto from perso where perso_cod = v_attaquant) != 0 then
              if trim(texte_mort_px) is not null then
                code_retour := code_retour || texte_mort_px;
              end if;
            end if;
          /* FIN   : coup porté : cible morte      */
          else -- cible pas tuée
            /* DEBUT : coup porté : cible pas morte  */
            texte_evt := tete || ' de [attaquant] a attaqué [cible], infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts.';

            /*MAJ des PV */
            update perso set perso_pv = nouveau_pv_cible
            where perso_cod = ligne.perso_cod;
            code_retour := code_retour || 'Votre adversaire a survécu à cette attaque. Il est maintenant <b>' || etat_perso(ligne.perso_cod) || '</b>.<br>';
            des := lancer_des(1, 100); /*Détermine pour certaines attaques la probabilité de l’effet */
            if effet = 'POI' then
              /*Ajout d’un effet poison de l’attaque */
              if ligne.perso_con >= 18 then
                malus := 2;
              else
                malus := 20 - ligne.perso_con;
              end if;
              perform ajoute_bonus(ligne.perso_cod, 'POI', 4, malus);
              code_retour := code_retour || 'Il a aussi été <b>empoisonné</b> par cette attaque<br>.';
              texte_evt := texte_evt || ' La victime a aussi été <b>empoisonnée. </b> !';
            end if;
            if effet = 'ARME' and des < 20 and ligne.perso_type_perso <> 3 then
              /* ajout effet désarmement cible */
              select into v_obj, v_obj_deposable
                perobj_obj_cod, gobj_deposable
              from perso_objets, objets, objet_generique
              where perobj_perso_cod = ligne.perso_cod
                    and perobj_obj_cod = obj_cod
                    and obj_gobj_cod = gobj_cod
                    and gobj_tobj_cod = 1
                    and perobj_equipe = 'O';
              if not found then
                code_retour := code_retour || '<p>Aucune arme n’a pu être arrachée, la cible n’en portant pas';
              else
                if v_obj_deposable = 'N' then
                  compt := f_del_objet(v_obj);
                  code_retour := code_retour || '<p>L’arme de votre cible a été détruite !<br>';
                  texte_evt := texte_evt || ' La victime a aussi été <b><font color="red">désarmée</font>. </b> !';
                else
                  delete from perso_objets
                  where perobj_perso_cod = ligne.perso_cod
                        and perobj_obj_cod = v_obj;
                  insert into objet_position (pobj_pos_cod, pobj_obj_cod)
                  values (ligne.ppos_pos_cod, v_obj);
                  code_retour := code_retour || '<p>L’arme de votre adversaire est tombée au sol !<br>';
                  texte_evt := texte_evt || ' La victime a aussi été <b><font color="red">désarmée</font>. </b> !';
                end if;
              end if;
            end if;
            if effet = 'RECUL' and des < 35 and ligne.perso_type_perso != 3 then
              /* on sélectionne aléatoirement la case d’arrivée, à une position max du lanceur*/
              select into position_arrivee, v_order lancer_position, lancer_des(1, 1000) from lancer_position(ligne.ppos_pos_cod, 1) order by v_order limit 1;
              /* On vérifie qu’il n’y a pas un mur entre */
              if trajectoire_vue(ligne.ppos_pos_cod, position_arrivee) = 0 then /* il y a un mur sur le chemin ... ==> Dégâts supplémentaire et autre message*/
                presence_mur := 1;
              else
                presence_mur := 0;
              end if;
              position_arrivee2 := trajectoire_position(ligne.ppos_pos_cod, position_arrivee);
              if position_arrivee2 != 2 then
                update perso_position set ppos_pos_cod = position_arrivee2
                where ppos_perso_cod = ligne.perso_cod;
                delete from lock_combat where lock_cible = ligne.perso_cod;
                delete from lock_combat where lock_attaquant = ligne.perso_cod;
                code_retour := code_retour || '<p>La victime a dû reculer sous l’effet de l’attaque. <br>';
                if presence_mur = 1 then
                  texte_evt := texte_evt || ' La victime a été acculée contre un mur, et se retrouve sonnée </b> !';
                  update perso set perso_dlt = perso_dlt + '20 minutes'::interval
                  where perso_cod = ligne.perso_cod;
                else
                  texte_evt := texte_evt || ' La victime a dû reculer sous l’effet de l’attaque. </b> !';
                end if;
              end if;
            end if;
            if effet = 'ROUILLE' and des < 35 then
              etat_armure := f_use_armure(ligne.perso_cod, 50);
              code_retour := code_retour || '<p>L’armure de la victime s’est déteriorée. <br>';
              texte_evt := texte_evt || ' L’armure de la victime a subit un choc plus violent que la normale. </b> !';
            end if;

            /* evts pour coups portes */
            perform insere_evenement(v_attaquant, ligne.perso_cod, 84, texte_evt, 'O', null);
            insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
            values(4, ligne.perso_cod, v_attaquant, degats_portes);

            if code_retour is null then
              code_retour := 'erreur sur code_retour';
            end if;
          /* FIN   : coup porté : cible pas morte  */
          end if; --fin coup porté non esquivé
        /* FIN : calcul des dégâts portes          */
        /* FIN : attaque réussie                                        */
        else
          /* DEBUT : attaque esquivée par la cible                  */
          code_retour := code_retour || 'Très adroit, il a réussi à l’éviter<br>';
          texte_evt := '[cible] a adroitement réussi à éviter l’attaque de [attaquant]';
          perform insere_evenement(v_attaquant, ligne.perso_cod, 84, texte_evt, 'O', null);
        /* FIN : attaque esquivée par la cible                    */
        end if;--fin de résolution de l’attaque
      end loop; /*Fin du traitement d’une tête en particulier*/
      compteur := compteur + 1; /*compteur d’incrémentation pour les attaques des différentes têtes */
    end loop;
  -- FIN - Attaque hydre à neuf têtes --

  -- Attaque Jeu de Troll --
  elsif v_type_attaque = 19 then
    --On reprend la cible initiale indiquée en entrée, et on vérifie qu’il s’agit d’un perso et non d’un familier. Si c’est le cas, on prend son "maître"
    select into v_type_cible perso_type_perso from perso
    where perso_cod = v_cible;
    if v_type_cible = 3 then
      select into v_cible_nouvelle pfam_perso_cod from perso_familier
      where pfam_familier_cod = v_cible;
    else
      v_cible_nouvelle := v_cible;
    end if;

    --Détermination si la cible est affectée
    /* la chance de réussite sur une cible, donc son esquive, est uniquement déterminée par la dextérité de la cible*/
    select into v_dex_cible, v_con_cible, v_type_cible, v_nom_cible, v_pv_cible
      perso_dex, perso_con, perso_type_perso, perso_nom, perso_pv
    from perso
    where perso_cod = v_cible_nouvelle;
    comp_attaque := 100 - round((v_dex_cible * v_dex_cible * v_dex_cible) / 120);
    code_retour := code_retour || 'Chance de réussite de cette attaque : ' || comp_attaque::text || '<br>';
    if comp_attaque < 10 then
      comp_attaque := 10;
    end if;

    -- on commence à générer un code retour
    code_retour := code_retour || 'Vous avez attaqué le ';
    if v_type_cible = 1 then
      code_retour := code_retour || 'perso ';
    else
      code_retour := code_retour || 'monstre ';
    end if;
    code_retour := code_retour || ' <b>' || v_nom_cible || '</b> en l’attrapant pour le lancer<br>';
    des := lancer_des(1, 100);
    /* DEBUT : attaque ratée sur échec critique               */
    if des > 96 then -- echec critique
      code_retour := code_retour || 'C’est un échec automatique !<br><br>';
      px_gagne := 0;
      texte_evt := '[attaquant] a lamentablement raté l’attrapage de [cible]';
      perform insere_evenement(v_attaquant, v_cible_nouvelle, 80, texte_evt, 'O', null);
      return code_retour;
    end if;
    /* FIN   : attaque ratée sur échec critique               */

    /* DEBUT : attaque réussie, on va résoudre l’ensemble     */
    if des < comp_attaque then
      code_retour := code_retour || 'Il n’a pas pu esquiver votre attaque, et se voit projeté dans les airs.<br>';
      /*Pas d’aura de feu pour le jeu de trolls*/
      /* DEBUT : Lancement du perso dans les airs  */
      /* on sélectionne aléatoirement la case d’arrivée, à deux positions max du lanceur*/
      select into position_arrivee, v_order lancer_position, lancer_des(1, 1000) from lancer_position(pos_attaquant, 2)
      order by v_order limit 1;
      /* On vérifie qu’il n’y a pas un mur entre */
      if trajectoire_vue(pos_attaquant, position_arrivee) = 0 then /* il y a un mur sur le chemin ... ==> Dégâts supplémentaire et autre message*/
        presence_mur := 1;
      else
        presence_mur := 0;
      end if;
      position_arrivee2 := trajectoire_position(pos_attaquant, position_arrivee);
      if position_arrivee2 != 2 then
        update perso_position set ppos_pos_cod = position_arrivee2
        where ppos_perso_cod = v_cible_nouvelle;
        delete from lock_combat where lock_cible = v_cible_nouvelle;
        delete from lock_combat where lock_attaquant = v_cible_nouvelle;

        select into lien_perso_fam pfam_familier_cod from perso_familier
        where pfam_perso_cod = v_cible_nouvelle;
        if found then
          update perso_position   set ppos_pos_cod = position_arrivee2
          where ppos_perso_cod = lien_perso_fam;
        end if;
      else
        code_retour := code_retour || '<br>Pb de position ! valeur de position_arrivee : ' || to_char(coalesce(position_arrivee, 0), '999999999') || ',
					valeur de position_attaquant : ' || to_char(coalesce(pos_attaquant, 0), '999999999') || ',
					valeur de position_arrivee2 : ' || to_char(coalesce(position_arrivee2, 0), '999999999');
      end if;
      /* Fin : Lancement du perso dans les airs    */
      /* DEBUT : calcul des dégâts portes          */
      degats_portes := floor((v_attaquant_for * v_con_cible) / 9);
      if presence_mur = 1 then
        degats_portes := floor(degats_portes * 1.5);
      end if;
      code_retour := code_retour || 'Vous lancez votre cible, qui subit alors <b>' || trim(to_char(degats_portes, '9999')) || '</b> points de dégâts.';
      if presence_mur = 1 then
        code_retour := code_retour || '<br>Elle vient s’écraser contre un mur';
      end if;
      etat_armure := f_use_armure(v_cible_nouvelle, degats_portes);
      if etat_armure = 2 then
        code_retour := code_retour || 'Vous avez <b>brisé</b> l’armure de votre adversaire !<br>';
      end if;
      insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
      values (1, v_attaquant, v_cible_nouvelle, degats_portes);
      nouveau_pv_cible := v_pv_cible - degats_portes;

      /* DEBUT : coup porté : cible morte      */
      if nouveau_pv_cible <= 0 then -- la cible a été tuée......
        code_retour := code_retour || 'Vous avez <b>tué</b> votre adversaire.<br>';
        texte_evt := '[attaquant] a attrappé [cible] et l’a lancé, infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts, le tuant sur le coup !';
        /* evts pour coup porté */
        perform insere_evenement(v_attaquant, v_cible_nouvelle, 80, texte_evt, 'O', null);
        texte_mort := tue_perso_final(v_attaquant, v_cible_nouvelle);
        texte_mort_px := split_part(texte_mort, ';', 2);
        if (select perso_use_repart_auto from perso where perso_cod = v_attaquant) != 0 then
          if trim(texte_mort_px) is not null then
            code_retour := code_retour || texte_mort_px;
          end if;
        end if;
      /* FIN   : coup porté : cible morte      */
      else -- cible pas tuée
        /* DEBUT : coup porté : cible pas morte  */
        texte_evt := '[attaquant] a attrappé [cible] et l’a lancé, infligeant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts !';
        /* evts pour coups portes */
        perform insere_evenement(v_attaquant, v_cible_nouvelle, 80, texte_evt, 'O', null);
        /*Action pour les PXs */
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (4, v_cible_nouvelle, v_attaquant, degats_portes);

        /*MAJ des PV désactivé à retravailler pour cette attaque*/
        update perso set perso_pv = nouveau_pv_cible
        where perso_cod = v_cible_nouvelle;
        code_retour := code_retour || 'Votre adversaire a survécu à cette attaque. Il est maintenant <b>' || etat_perso(v_cible_nouvelle) || '</b>.<br>';

        if code_retour is null then
          code_retour := 'erreur sur code_retour';
        end if;
      /* FIN   : coup porté : cible pas morte  */
      end if;

      /* DEBUT : Calcul des dégâts collatéraux */
      /* On va boucler sur les persos présents dans l’alignement, en limitant à 4 max*/
      for ligne in select personnage, lancer_des(1, 1000), type_perso as num
                   from trajectoire_perso_hors_lieu(pos_attaquant, position_arrivee2) as (personnage int, v_pos int, type_perso int)
                   where type_perso in (1, 3)
                   order by num limit 4
      loop
        degats_portes := 0;
        degats_portes := floor((v_attaquant_for * v_con_cible) / 20);
        select into v_pv_cible perso_pv from perso where perso_cod = ligne.personnage;
        nouveau_pv_cible := v_pv_cible - degats_portes;

        if nouveau_pv_cible <= 0 then -- la cible a été tuée......
          texte_evt := '[cible] a été touché par un projectile humain, subissant ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts, le tuant sur le coup !';
          /* evts pour coup porté */
          perform insere_evenement(v_attaquant, ligne.personnage, 85, texte_evt, 'O', null);
          texte_mort := tue_perso_final(v_attaquant, ligne.personnage);
          texte_mort_px := split_part(texte_mort, ';', 2);
          if (select perso_use_repart_auto from perso where perso_cod = v_attaquant) != 0 then
            if trim(texte_mort_px) is not null then
              code_retour := code_retour || texte_mort_px;
            end if;
          end if;
        else -- cible pas tuée
          texte_evt := '[cible] a été touché par un projectile humain, subissant  ' || trim(to_char(degats_portes, '9999')) || ' points de dégâts !';
          /* evts pour coups portes */
          perform insere_evenement(v_attaquant, ligne.personnage, 85, texte_evt, 'O', null);
          /*Action pour les PXs */
          insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
          values (4, v_cible_nouvelle, v_attaquant, degats_portes);

          update perso set perso_pv = nouveau_pv_cible
          where perso_cod = ligne.personnage;
          if code_retour is null then
            code_retour := 'erreur sur code_retour';
          end if;
        end if;
      end loop;
    /* FIN : attaque réussie                                        */
    else
      /* DEBUT : attaque esquivée par la cible                  */
      code_retour := code_retour || 'Très adroit, il a réussi à l’éviter<br>';
      texte_evt := '[cible] a adroitement réussi à éviter de se faire attraper par [attaquant]';
      perform insere_evenement(v_attaquant, v_cible_nouvelle, 80, texte_evt, 'O', null);
    /* FIN : attaque esquivée par la cible                    */
    end if; --fin d’attaque réussie
  -- FIN - Attaque Jeu de Troll --
  end if;

  --Gestion des PA --
  update perso
  set perso_pa = perso_pa - 6
  where perso_cod = v_attaquant;
  return code_retour;

  Exception
  when    check_violation then
    code_retour := 'Arrêt sur erreur : valeur de v_pv_cible : ' || to_char(coalesce(v_pv_cible, 0), '9999999999') || ',
			valeur de perso_pv : ' || to_char(coalesce(v_pv_attaquant, 0), '9999999999') || ',
			valeur de degats_portes : ' || to_char(coalesce(degats_portes, 0), '9999999999') || ',
			valeur de nouveau_pv_cible : ' || to_char(coalesce(nouveau_pv_cible, 0), '999999999') || ',
			valeur de position_arrivee : ' || to_char(coalesce(position_arrivee, 0), '999999999') || ',
			valeur de position_arrivee2 : ' || to_char(coalesce(position_arrivee2, 0), '999999999');
    return code_retour;
end;$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;
