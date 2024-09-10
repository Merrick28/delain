--
-- Name: attaque(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function attaque(integer, integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* function attaque : provoque une attaque                       */
/* On passe en paramètres                                        */
/*    $1 = perso_cod attaquant                                   */
/*    $2 = perso_cod cible                                       */
/*    $3 = type d’attaque                                        */
/*       0 = normale                                             */
/*       1 = AF lvl 1                                            */
/*       2 = AF lvl 2                                            */
/*       3 = AF lvl 3                                            */
/*       4 = Feinte lvl 1                                        */
/*       5 = Feinte lvl 2                                        */
/*       6 = Feinte lvl 3                                        */
/*       7 = Coup de grâce lvl 1                                 */
/*       8 = Coup de grâce lvl 2                                 */
/*       9 = Coup de grâce lvl 3                                 */
/*      10 = bout portant lvl 1                                  */
/*      11 = bout portant lvl 2                                  */
/*      12 = bout potant lvl 3                                   */
/*      13 = tir précis lvl 1                                    */
/*      14 = tir précis lvl 2                                    */
/*      15 = tir précis lvl 3                                    */
/*      16 = balayage                                            */
/*      17 = garde manger                                        */
/*      18 = Hydre à neuf têtes                                  */
/*      19 = Jeu de Trolls                                       */
/*      20 = charge divine       			         */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 01/04/2003                                            */
/* Liste des modifications :                                     */
/*   12/06/2003 : ajout des coups critiques                      */
/*   21/01/2004 : modif du code sortie en html                   */
/*                compilation att locale et distance             */
/*   26/03/2004 : correction bug pour attaque dans temple        */
/*                ajout du type d’esquive                        */
/*   10/08/2004 : ajout des comp de combat                       */
/*   17/08/2004 : ajout du trace dans actions                    */
/*   22/10/2004 : prise en compte des combats de groupe, avec    */
/*     mise en place d’un paramètre (56)                         */
/*   23/05/2005 : Optimisation des elsif                         */
/*                ajout du change_cible_combat pour les IA       */
/*   20/07/2006 : ajout de la folie d’Ecatis                     */
/*                ajout de l’harmonie offensive                  */
/*   11/09/2006 : ajout de la gestion du nombre de comp spé.     */
/*   18/04/2007 : ajout du gain de px dégressif sur spe          */
/*   31/05/2007 : modif des dégâts pvp initiative                */
/*   09/09/2007 : Retiré la condition comp_init = 15 sur HO      */
/*   13/12/2007 : modif sur le code retour pour les armes d’hast */
/*   14/02/2009 : modif sur l’appel de lancer_des                */
/*   26/03/2009 : modif sur la défense de danse de saint guy     */
/*   07/04/2009 : ajout charge divine                            */
/*   31/01/2011 : Bleda: Perce armure ajout az : coeff perc      */
/*   21/03/2011 : Bleda: Zone de droit                           */
/*   29/01/2015 : Kahlann: Mise en place des EA type A (tous)    */
/*****************************************************************/
declare
  --------------------------------------------------------------------------------
  -- variables fourre tout
  --------------------------------------------------------------------------------
  debug_txt text;                               -- Texte éventuel de débuggage
  trace_texte text;                             -- Texte de mise en table trace, le cas échéant
  code_retour text;                             -- chaine qui contiendra le html de retour
  compt integer;                                -- compteur multi usage
  texte_evt text;                               -- texte pour évènements
  v_riposte integer;                            -- variable pour créer riposte
  des integer;                                  -- lancer de des
  chance_touche_autre integer;                  -- chance de toucher un autre adversaire
  des_autre integer;
  nb_assaillant integer;                        -- nopmbre d’assaillants
  v_protection integer;                         -- protection du juste
  recalcul_spe numeric;                               -- Variable pour le debug
  --------------------------------------------------------------------------------
  -- renseignements de l attaquant
  --------------------------------------------------------------------------------
  v_attaquant alias for $1;                     -- perso_cod attaquant
  v_type_attaque alias for $3;
  nom_type_attaque text;
  pa integer;                                   -- pa de l’attaquant
  v_cout_pa integer;                            -- cout en PA de l’attaque
  pos_per1 integer;                             -- position de l attaquant
  etage_attaquant integer;                      -- etage de l attaquant
  v_nb_des_degats integer;                      -- des de dégâts par défaut
  v_val_des_degats integer;                     -- valeur des dés par défaut
  num_comp integer;                             -- comp_cod utilisée (arme)
  num_comp_spe integer;                         -- comp_cod utilisée (attaquae non normale)
  nom_competence competences.comp_libelle%type; -- nom de la comp utilisée
  comp_attaque integer;                         -- valeur de la comp d’attaque
  val_comp_attaque_orig integer;
  comp_attaque_init integer;                    -- valeur initiale de la comp
  comp_attaque_amel integer;                    -- valeur initiale de la comp
  comp_attaque_modifie integer;                 -- valeur de la comp d’attaque modifiée
  nb_des_attaque integer;                       -- nb de dés de dégâts utilisés pour l’attaque
  valeur_des_attaque integer;                   -- valeur des des de dégâts
  bonus_attaque integer;                        -- bonus à la comp d’attaque
  v_amelioration_degats integer;                -- bonus aux dégâts de l’attaquant
  px_gagne numeric;                             -- px gagnés par l’attaquant
  limite_comp_maitre integer;                   -- limite pour le coup spécial
  v_bonus_toucher integer;                      -- bonus au toucher de l’attaquant (magie)
  v_bonus_degats integer;                       -- bonus au toucher de l’attaquant (caracs)
  nom_arme text;                                -- nom de l’arme utilisée
  v_type_arme integer;                          -- type d’arme : 0 pas d arme, 1 contact, 2 distance
  distance_perso integer;                       -- distance maxi d’attaque
  degats_portes integer;                        -- dégâts théoriques de l’attaque
  degats_effectues integer;                     -- dégâts rééllement effectués (armure déduite)
  temp_ameliore_competence text;
  temp_comp_attaque numeric;                    -- texte temporaire pour l’amel de la compétence
  v_chute numeric;                              -- chute de l’arme
  v_amel_degats_dist integer;                   -- bonus aux dégâts à distance
  v_trajectoire text;                           -- trajectoire en cas d’attaque à distance
  res_traj integer;                             -- resultat trajectoire
  nv_traj integer;                              -- nouvelle trajectoire éventuelle
  pos_x_mur integer;                            -- position en X du mur
  pos_y_mur integer;                            -- position en Y du mur
  distance_cible integer;                       -- distance entre cible et attaquant
  malus_degats integer;                         -- malus lié à la chute
  qualite_attaque integer;                      -- qualité de l’attaque
  nom_qualite_attaque text;
  num_arme integer;                             -- numéro de l’arme équipée
  usure_arme numeric;                           -- usure de l’arme
  etat_arme numeric;                            -- etat de l’arme après l’attaque
  etat_avant text;                              -- texte de l’état avant attaque
  etat_apres text;                              -- texte de l’état après attaque
  v_vampire numeric;                            -- vampirisme de l’arme
  v_absorbe numeric;                            -- absorption de l’arme
  regen_vampire integer;                        -- pv regagnés par vampire
  pv_attaquant integer;                         -- pv de l’attaquant
  pv_max_attaquant integer;                     -- pv max de l’attaquant
  diff_pv integer;
  v_perso_vampirisme numeric;
  v_perce_armure boolean;
  v_coeff_perce numeric;
  v_nb_comp integer;
  deg_max integer;
  type_attaquant integer;
  v_num_objet integer;
  nb_lock_d integer;                            -- nombre de locks défensifs de l’attaquant
  v_type_lock integer;
  v_comp_nb_util_tour integer;                  -- nombre de fois où la compétence est utilisable
  v_perso_nb_spe integer;
  v_degats_effectues integer;
  var_gain integer;                             --Détermine si gain de px et kharma ou pas en cas de touchage d’une cible annexe
  --------------------------------------------------------------------------------
  -- renseignements de la cible
  --------------------------------------------------------------------------------
  v_cible alias for $2;                         -- perso_cod cible
  nv_cible integer;                             -- cible déinfitive
  type_cible integer;                           -- type de la cible (1 pour joueur, 2 pour monstre)
  pos_per2 integer;                             -- position de la cible
  etage_cible integer;                          -- etage de la cible
  nom_per2 perso.perso_nom%type;                -- nom de la cible
  armure integer;                               -- armure de la cible
  pv_cible integer;                             -- points de vie de la cible
  nouveau_pv_cible integer;                     -- points de vie de la cible après attaque
  reussite_esquive integer;                     -- marqueur pour réussite esquive
  v_armure_physique integer;                    -- armure physique du perso
  etat_armure integer;                          -- etat de l’armure de la cible
  v_cible_tangible text;                        -- tangibilité de la cible
  v_compte_fam integer;
  v_compte integer;
  nb_lock_d_cible integer;                      -- nombre de locks défensifs de la cible
  v_taille_cible integer;                       -- taille de la cible
  v_cible_surcharge integer;                    -- cible surchargée ? (0 pour non, 1 pour oui)
  v_malus_surcharge integer;                    -- malus pour compétence de combat mélée si surcharge
  v_nb_surcharge integer;                       -- nombre de surcharge de la cible
  is_attaquable integer;
  mode_milice integer;
  v_dsg_bonus integer;                          --  bonus de danse de saint guy
  -- pour aura de feu
  v_aura_feu numeric;
  v_degats_aura_feu integer;
  v_pv_attaquant integer;
  aura_texte text;
  v_bonus_critique integer;
  v_des_critique integer;
  temp_use_casque integer;
  v_casque integer;
  texte_mort text;
  texte_mort_px text;
  temp_change_cible integer;
  c_def integer;                                -- défense de la cible
  m_def numeric;                                -- modificateur à la défense de la cible
  v_type_arme_cible integer;                    -- type d’arme de la cible
  v_main_attaquant integer;
  v_main_cible integer;
  v_modif_att numeric;
  v_modif_def numeric;
  v_facteur1 integer;
  v_facteur2 integer;
  v_seuil_force integer;
  v_seuil_dex integer;
  v_force integer;
  v_dex integer;
  v_limite_maitre numeric;
  nb_hon integer;
  bonmal integer;                               -- pour gérer les bénis maudits.
  malus integer;
  v_pos_pvp character;                          -- Si la cible est en zone de droit
  v_atq_gmon_cod integer;                       -- Type de monstre attaquant. Pour interdire les attaques aux golems
  v_desorientation integer;                     -- Malus de désorientation
  texte_desorientation text;                    -- message de désorientation
  v_attaque_critique integer;                   -- bonus/malus d'attaque critique

  --------------------------------------------------------------------------------
  -- variables évènements
  --------------------------------------------------------------------------------
begin

  -------------------------------------
  -- Rajout Blade temporaire pour attaques spéciales
  -------------------------------------

  if v_type_attaque in (16, 17, 18, 19) then
    code_retour := attaque_spe(v_attaquant, v_cible, v_type_attaque);
    return code_retour;
  end if;

  -------------------------------------
  -- init des facteurs
  -------------------------------------
  debug_txt := '';
  v_facteur1 := 350;
  v_facteur2 := 410;
  var_gain := 1;
  -- initialisation de la limite de coup spécial
  -- ajout azaghal pour traiter le malus impotence du mage de guerre
  if valeur_bonus(v_attaquant, 'IMP') > 0 then
    v_limite_maitre := 0.05 + bonus_spe_dex(v_attaquant);
  else
    v_limite_maitre := 0.25 + bonus_spe_dex(v_attaquant);
  end if;

  debug_txt := debug_txt || 'Modificateur de limite pour coup spécial comp dex : ' || trim(to_char(v_limite_maitre, '99D99')) || '.<br>';
  -- initialisation du texte debug
  v_malus_surcharge := 0;
  v_cible_surcharge := 0;
  val_comp_attaque_orig := 100;
  nom_type_attaque := '';
  nom_qualite_attaque := '';
  v_armure_physique := f_armure_perso_physique(v_cible);
  qualite_attaque := 0;
  nv_cible = v_cible;
  px_gagne := 0;
  code_retour := '<p>'; -- par défaut, tout est OK
  armure := f_armure_perso(nv_cible);
  v_perce_armure := false;
  --
  -- Milice : mise en place d’un texte d’avertissement
  --
  if is_milice(v_attaquant) = 1 then
    select into mode_milice
      pguilde_mode_milice
    from guilde_perso
    where pguilde_perso_cod = v_attaquant;
    if mode_milice = 3 then
      code_retour := code_retour || 'Milice : Vous êtes en mode <b>CRS</b><br>';
    elsif mode_milice = 2 then
      code_retour := code_retour || 'Milice : Vous êtes en mode <b>Application des peines</b><br>';
    elsif mode_milice = 1 then
      code_retour := code_retour || 'Milice : Vous êtes en mode <b>Normal</b><br>';
    end if;
  end if;

  /***********************************************/
  /* DEBUT : Vérfications liées à l’attaquant    */
  /***********************************************/
  select into pa,
    pos_per1,
    etage_attaquant,
    v_amelioration_degats,
    v_nb_des_degats,
    v_val_des_degats,
    v_type_arme,
    distance_perso,
    v_amel_degats_dist,
    v_cible_tangible,
    pv_attaquant,
    pv_max_attaquant,
    v_perso_vampirisme,
    type_attaquant,
    v_pv_attaquant,
    v_atq_gmon_cod
    perso_pa,
    ppos_pos_cod,
    pos_etage,
    perso_amelioration_degats,
    perso_nb_des_degats,
    perso_val_des_degats,
    type_arme(perso_cod),
    portee_attaque(perso_cod),
    perso_amel_deg_dex,
    perso_tangible,
    perso_pv,
    perso_pv_max,
    perso_vampirisme, perso_type_perso,
    perso_pv,
    perso_gmon_cod
  from perso, perso_position, positions
  where perso_cod = v_attaquant
        and ppos_perso_cod = perso_cod
        and ppos_pos_cod = pos_cod;
  if not found then
    code_retour := '<p>Erreur ! Attaquant non trouvé !';
    return code_retour;
  end if;

  -- Les golems n’attaquent pas
  if v_atq_gmon_cod IN (531, 535) then
    code_retour := '<p>Le golem se regarde, cherche dans cet amoncèlement quelque chose qui pourrait servir d’arme... et ne trouve rien. Dépité, il laisse sa cible tranquille.</p>';
    return code_retour;
  end if;

  if v_cible_tangible = 'N' then
    code_retour := '<p>Erreur ! Vous êtes impalpable, il vous est impossible d’attaquer !';
    return code_retour;
  end if;
  /***********************************************/
  /* FIN : Vérfications liées à l’attaquant      */
  /***********************************************/

  /*********************************************/
  /* DEBUT : spécificités sur Désorientation   */
  /*         Conséquence de Morsure du Soleil  */
  /*********************************************/
  /* 2018-09-06 - Marlyza - on réalise ici un eventuel changement de cible */
  texte_desorientation := '' ;    -- pour un affichage ultérieur quand on aura des infos sur la cible
  v_desorientation := LEAST(100, valeur_bonus(v_attaquant, 'DES')) ; -- récupération du malus de désorientation

  if v_desorientation > 0 then

    /* Convertion de la valeur en %
     -- Marlyza - 2020-05-10 : avec l'arrivée des bonus d'équipement, la désorientation passe en procentage plus besoin de conversion!
    if v_desorientation=1 then
      v_desorientation := 33 ;
    elsif v_desorientation=2 then
       v_desorientation := 66 ;
    else
       v_desorientation := 100 ;
    end if;
    */

    des := lancer_des(1, 100);

    /* % de changement de cible en fonction de la valeur de désorientation est +1: 33%, +2:66% et autre valeur :100% */
    if des<=v_desorientation then

      v_cible := choix_cible_aleatoire(v_attaquant, 0) ;

      if (v_cible>0 and v_cible<>nv_cible) then

        texte_desorientation := 'Vous êtes désorienté (<b>' || v_desorientation::text || '</b>% de chance de rater:' || des::text || ') et votre bras dévie au dernier moment ' ;
        nv_cible := v_cible ;       -- nouvelle cible !!!

      else

        texte_desorientation := 'Vous êtes désorienté mais vous êtes chanceux, votre bras n''a pas dévié ' ;
        v_desorientation := 0 ;    -- Finalement l'attaquant à eu de la chance, il n'a pas été désorienté (pas d'évent)
        v_cible := nv_cible ;      -- au cas ou  choix_cible_aleatoire renvoi 0 !!!

      end if;

    else

      /* pas de changement de cible, car pas de désorientation */
      texte_desorientation := 'Vous êtes désorienté (<b>' || v_desorientation::text || '</b>% de chance de rater:' || des::text || '), heureusement votre bras n''a pas dévié ' ;
      v_desorientation := 0 ;    -- Finalement l'attaquant à eu de la chance, il n'a pas été désorienté (pas d'évent)

    end if;
  end if;
  /*********************************************/
  /* FIN   : spécificités sur Désorientation   */
  /*********************************************/

  --
  /*********************************************/
  /* DEBUT : vérification liées à la cible     */
  /*********************************************/
  select into 	type_cible,
    nom_per2,
    pos_per2,
    etage_cible,
    pv_cible,
    v_cible_tangible,
    v_taille_cible,
    v_type_arme_cible,
    v_pos_pvp
    perso_type_perso,
    perso_nom,
    ppos_pos_cod,
    pos_etage,
    perso_pv,
    perso_tangible,
    perso_taille,
    type_arme(perso_cod),
    pos_pvp
  from perso, perso_position, positions
  where perso_cod = nv_cible
        and ppos_perso_cod = perso_cod
        and ppos_pos_cod = pos_cod
        and perso_actif = 'O' for update;
  if not found then
    code_retour := '<p>Erreur ! Cible non trouvée !';
    return code_retour;
  end if;
  if v_cible_tangible = 'N' then
    code_retour := '<p>Erreur ! Cette cible est impalpable, il vous est impossible de l’attaquer !';
    return code_retour;
  end if;
  if type_attaquant != 2 and type_cible != 2 and v_pos_pvp = 'N' then
    code_retour := '<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de l’attaquer car elle n’est pas une engeance de Malkiar !<br /> La zone de droit couvre tout l’Ouest de l’étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n’importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)';
    return code_retour;
  end if;
  /*********************************************/
  /* FIN   : vérification liées à la cible     */
  /*********************************************/
  --
  /**********************************************/
  /* DEBUT : vérification familiers même compte */
  /**********************************************/
  if type_cible = 3 then
    if type_attaquant = 1 then
      select into v_compte_fam
        pcompt_compt_cod
      from perso_familier, perso_compte
      where pfam_familier_cod = nv_cible
            and pfam_perso_cod = pcompt_perso_cod;
      select into v_compte pcompt_compt_cod from perso_compte
      where pcompt_perso_cod = v_attaquant;
      if v_compte = v_compte_fam then
        code_retour := '<p>Erreur ! Vous ne pouvez pas attaquer les familiers d’un des persos de votre compte !';
        return code_retour;
      end if;
    end if;
  end if;
  /**********************************************/
  /* FIN   : vérification familiers même compte */
  /**********************************************/
  --
  /************************************************/
  /* DEBUT : vérification sur position de refuge  */
  /************************************************/
  -- 1 . Cible
  select into compt lieu_cod
  from lieu, lieu_position
  where lpos_pos_cod = pos_per2
        and lpos_lieu_cod = lieu_cod
        and lieu_refuge = 'O';
  if found then
    code_retour := '<p>Erreur ! La cible est sur un refuge !';
    return code_retour;
  end if;
  -- 2 . Attaquant
  select into compt lieu_cod
  from lieu, lieu_position
  where lpos_pos_cod = pos_per1
        and lpos_lieu_cod = lieu_cod
        and lieu_refuge = 'O';
  if found then
    code_retour := '<p>Erreur ! Vous êtes sur un refuge, vous ne pouvez pas attaquer !';
    return code_retour;
  end if;
  /************************************************/
  /* FIN   : vérification sur position de refuge  */
  /************************************************/
  --
  /*********************************************/
  /* DEBUT : controles sur combat de groupe    */
  /*********************************************/
  --
  -- 1 . Choix de la cible possible ?
  --
  select into nb_lock_d count(lock_cod)
  from lock_combat
  where lock_cible = v_attaquant;
  if nb_lock_d != 0 then
    is_attaquable := 0;
    -- a partir d’ici, on sait qu’on n’a plus le choix de la cible
    select into nb_lock_d lock_cod
    from lock_combat
    where lock_cible = v_attaquant
          and lock_attaquant = v_cible;
    if found then
      is_attaquable = 1;
    end if;
    select into nb_lock_d lock_cod
    from lock_combat
    where lock_cible = v_cible
          and lock_attaquant = v_attaquant;
    if found then
      is_attaquable = 1;
    end if;
    if is_attaquable = 0 then
      code_retour := '<p>Erreur ! Vous ne pouvez pas choisir cette cible (vous êtes déjà engagé en combat par d’autres cibles).';
      return code_retour;
    end if;
  end if;
  --
  -- 2 . Calcul de la surcharge éventuelle
  --
  select into nb_lock_d_cible
    count(lock_cod)
  from lock_combat
  where lock_cible = v_cible
        and lock_attaquant != v_attaquant;
  if nb_lock_d_cible >= (2*v_taille_cible) then
    if v_type_arme != 2 then
      code_retour := '<p>Erreur ! Vous ne pouvez pas choisir cette cible (elle est déjà surchargée par d’autres assaillants).';
      return code_retour;
    else
      v_nb_surcharge := nb_lock_d_cible  - v_taille_cible;
    end if;
  elsif nb_lock_d_cible >= v_taille_cible then
    -- surcharge !
    v_limite_maitre := v_limite_maitre * (1 + ((nb_lock_d_cible-v_taille_cible)/nb_lock_d_cible::numeric));
    debug_txt := debug_txt || 'Taille : ' || trim(to_char(v_taille_cible, '99999')) || '.<br>';
    debug_txt := debug_txt || 'Nombre d’assaillants : ' || trim(to_char(nb_lock_d_cible, '99999')) || '.<br>';
    debug_txt := debug_txt || 'Modificateur de limite pour coup spécial (et 2): ' || trim(to_char(v_limite_maitre, '99D99')) || '.<br>';
    if v_type_arme != 2 then
      v_malus_surcharge := getparm_n(57) * (nb_lock_d_cible - v_taille_cible);
      if v_malus_surcharge < 0 then
        v_malus_surcharge := 0;
      end if;
    else
      v_nb_surcharge := nb_lock_d_cible  - v_taille_cible;
    end if;
  end if;
  /*********************************************/
  /* FIN   : controles sur combat de groupe    */
  /*********************************************/
  --
  /*******************************************************/
  /* DEBUT : controles sur PA en fct° du type d’attaque  */
  /*******************************************************/
  if v_type_attaque = 0 then
    v_cout_pa := nb_pa_attaque(v_attaquant);
  elsif v_type_attaque = 1 then
    num_comp_spe := 25;
    v_cout_pa := nb_pa_foudre(v_attaquant);
    nom_type_attaque := ' (attaque foudroyante) ';
    if v_type_arme = 2 then
      code_retour := 'Erreur ! Cette compétence ne peut pas s’utiliser avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 2 then
    num_comp_spe := 61;
    v_cout_pa := nb_pa_foudre(v_attaquant);
    nom_type_attaque := ' (attaque foudroyante lvl 2) ';
    if v_type_arme = 2 then
      code_retour := 'Erreur ! Cette compétence ne peut pas s’utiliser avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 3 then
    num_comp_spe := 62;
    v_cout_pa := nb_pa_foudre(v_attaquant);
    nom_type_attaque := ' (attaque foudroyante lvl 3) ';
    if v_type_arme = 2 then
      code_retour := 'Erreur ! Cette compétence ne peut pas s’utiliser avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 4 then
    num_comp_spe := 63;
    v_cout_pa := nb_pa_attaque(v_attaquant) + 3;
    nom_type_attaque := ' (feinte) ';
  elsif v_type_attaque = 5 then
    num_comp_spe := 64;
    v_cout_pa := nb_pa_attaque(v_attaquant) + 1;
    nom_type_attaque := ' (feinte lvl 2) ';
  elsif v_type_attaque = 6 then
    num_comp_spe := 65;
    v_cout_pa := nb_pa_attaque(v_attaquant);
    nom_type_attaque := ' (feinte lvl 3) ';
  elsif v_type_attaque = 7 then
    num_comp_spe := 66;
    v_cout_pa := nb_pa_attaque(v_attaquant) + 3;
    nom_type_attaque := ' (Coup de grâce) ';
  elsif v_type_attaque = 8 then
    num_comp_spe := 67;
    v_cout_pa := nb_pa_attaque(v_attaquant) + 1;
    nom_type_attaque := ' (Coup de grâce lvl 2) ';
  elsif v_type_attaque = 9 then
    num_comp_spe := 68;
    v_cout_pa := nb_pa_attaque(v_attaquant);
    nom_type_attaque := ' (Coup de grâce lvl 3) ';
  elsif v_type_attaque = 10 then
    num_comp_spe := 72;
    v_cout_pa := nb_pa_attaque(v_attaquant);
    nom_type_attaque := ' (Bout portant) ';
    if v_type_arme != 2 then
      code_retour := 'Erreur ! Cette compétence ne peut s’utiliser qu’avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 11 then
    num_comp_spe := 73;
    v_cout_pa := nb_pa_attaque(v_attaquant);
    nom_type_attaque := ' (Bout portant lvl 2) ';
    if v_type_arme != 2 then
      code_retour := 'Erreur ! Cette compétence ne peut s’utiliser qu’avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 12 then
    num_comp_spe := 74;
    v_cout_pa := nb_pa_attaque(v_attaquant);
    nom_type_attaque := ' (Bout portant lvl 3) ';
    if v_type_arme != 2 then
      code_retour := 'Erreur ! Cette compétence ne peut s’utiliser qu’avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 13 then
    num_comp_spe := 75;
    v_cout_pa := nb_pa_attaque(v_attaquant) + 3;
    nom_type_attaque := ' (Tir précis) ';
    if v_type_arme != 2 then
      code_retour := 'Erreur ! Cette compétence ne peut s’utiliser qu’avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 14 then
    num_comp_spe := 76;
    v_cout_pa := nb_pa_attaque(v_attaquant) + 1;
    nom_type_attaque := ' (Tir précis lvl 2) ';
    if v_type_arme != 2 then
      code_retour := 'Erreur ! Cette compétence ne peut s’utiliser qu’avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 15 then
    num_comp_spe := 77;
    v_cout_pa := nb_pa_attaque(v_attaquant);
    nom_type_attaque := ' (Tir précis lvl 3) ';
    if v_type_arme != 2 then
      code_retour := 'Erreur ! Cette compétence ne peut s’utiliser qu’avec une arme à distance !';
      return code_retour;
    end if;
  elsif v_type_attaque = 20 then
    v_cout_pa := 0;
  end if;
  if pa < v_cout_pa then
    code_retour := '<p>Erreur ! Pas assez de PA pour effectuer cette action';
    return code_retour;
  end if;
  /*******************************************************/
  /* FIN   : controles sur PA en fct° du type d’attaque  */
  /*******************************************************/
  --
  /*************************************************/
  /* DEBUT : controle sur la distance d’attaque    */
  /*************************************************/
  if distance(pos_per1, pos_per2) > distance_perso then
    code_retour := '<p>Erreur ! La cible est trop éloignée de l’attaquant';
    return code_retour;
  end if;
  if etage_cible != etage_attaquant then
    code_retour := '<p>Erreur ! La cible est trop éloignée de l’attaquant';
    return code_retour;
  end if;
  /*************************************************/
  /* FIN   : controle sur la distance d’attaque    */
  /*************************************************/
  --
  /*******************************************************/
  /* DEBUT : contrôles liées aux attaques non normales   */
  /*******************************************************/
  if v_type_attaque != 0 then
    if v_type_attaque != 20 then
      --
      -- on regarde combien de fois on peut lancer l’attaque
      --
      select into v_comp_nb_util_tour
        comp_nb_util_tour
      from competences
      where comp_cod = num_comp_spe;
      --
      -- on rajoute ici le compteur pour les attaques spéciales
      --
      if not exists (select 1 from perso_nb_comp
      where pnb_perso_cod = v_attaquant
            and pnb_comp_cod = num_comp_spe) then
        insert into perso_nb_comp (pnb_perso_cod, pnb_comp_cod, pnb_nombre)
        values (v_attaquant, num_comp_spe, 0);
      end if;
      select into v_nb_comp pnb_nombre
      from perso_nb_comp
      where pnb_perso_cod = v_attaquant
            and pnb_comp_cod = num_comp_spe;
      --
      -- on regarde si le nombre d’attaques spéciales est atteint ou pas
      --
      if v_nb_comp >= v_comp_nb_util_tour then
        code_retour := '<p>Erreur ! Vous ne pouvez pas utiliser cette compétence plus de ' || trim(to_char(v_comp_nb_util_tour, '99999')) || ' fois dans le même tour.';
        return code_retour;
      end if;
      update perso_nb_comp
      set pnb_nombre = pnb_nombre + 1
      where pnb_perso_cod = v_attaquant
            and pnb_comp_cod = num_comp_spe;
    end if;
  end if;
  /*******************************************************/
  /* FIN   : contrôles liées aux attaques non normales   */
  /*******************************************************/
  --
  /****************************************************************************/
  /* FIN DE TOUS CONTROLES DE COHERENCE                                       */
  /* L’attaque est à présent possible et va être portée                       */
  /****************************************************************************/
  --
  /*************************************/
  /* Etape 6 : les controles sont bons */
  /*     On passe à l attaque          */
  /*************************************/
  -- etape de préparation, on va faire les contrôles liés à la taille
  -- note : il est IMPORTANT de les faire avant toute chose, car sinon, on va être bloqués sur les calculs de locks.
  /*********************************/
  /* DEBUT : gestion de l’honneur  */
  /*********************************/
  nb_hon := valeur_bonus(v_attaquant, 'HON');
  if nb_hon != 0 then
    update bonus set bonus_valeur = 0
    where bonus_tbonus_libc = 'HON'
          and bonus_perso_cod = v_attaquant;
  end if;
  /*********************************/
  /* FIN   : gestion de l’honneur  */
  /*********************************/
  --
  /**************************************************/
  /* DEBUT : gestion des locks                      */
  /**************************************************/
  if v_type_arme != 2 AND type_attaquant != 3 AND type_cible != 3 AND v_attaquant != nv_cible then
    delete from lock_combat where lock_attaquant = v_attaquant and lock_cible = nv_cible;
    insert into lock_combat (lock_attaquant, lock_cible, lock_nb_tours)
    values (v_attaquant, nv_cible, getparm_n(17));
  end if;
  /**************************************************/
  /* FIN   : gestion des locks                      */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : gestion des ripostes                   */
  /**************************************************/
  v_riposte := cree_riposte(v_attaquant, nv_cible);
  /**************************************************/
  /* FIN   : gestion des ripostes                   */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : gestion des PA attaquant               */
  /**************************************************/
  update perso set perso_pa = pa - v_cout_pa where perso_cod = v_attaquant;
  /**************************************************/
  /* FIN   : gestion des PA attaquant               */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : On cherche les comp à utiliser         */
  /**************************************************/
  if v_type_arme = 0 then 		-- pas d’arme équipée
    select into comp_attaque pcomp_modificateur
    from perso_competences
    where pcomp_perso_cod = v_attaquant
          and pcomp_pcomp_cod = 30;
    num_comp := 30;
    nb_des_attaque := v_nb_des_degats;
    valeur_des_attaque := v_val_des_degats;
    bonus_attaque := 0;
    nom_arme = 'sans arme';
    v_chute := 0;
    v_vampire := v_perso_vampirisme;
    v_absorbe := 0;
  else		-- arme équipée
    select into 	comp_attaque,
      nb_des_attaque,
      valeur_des_attaque,
      bonus_attaque,
      num_comp,
      v_perce_armure,
      v_coeff_perce,
      nom_arme,
      v_chute,
      num_arme,
      usure_arme,
      v_vampire,
      v_seuil_force,
      v_seuil_dex,
      etat_avant
      pcomp_modificateur,
      obj_des_degats,
      obj_val_des_degats,
      obj_bonus_degats,
      gobj_comp_cod,
      gobj_perce_armure,
      gobj_coeff_percearmure,
      obj_nom_generique,
      coalesce(obj_chute, 0),
      obj_cod,
      obj_usure,
      obj_vampire,
      obj_seuil_force,
      obj_seuil_dex,
      get_etat_objet(obj_etat)
    from perso_competences, perso_objets, objets, objet_generique, objets_caracs
    where perobj_perso_cod = v_attaquant
          and perobj_equipe = 'O'
          and perobj_obj_cod = obj_cod
          and obj_gobj_cod = gobj_cod
          and gobj_tobj_cod = 1
          and gobj_obcar_cod = obcar_cod
          and gobj_comp_cod = pcomp_pcomp_cod
          and pcomp_perso_cod = v_attaquant;
    nom_arme := 'avec ' || nom_arme;
  end if;
  /**************************************************/
  /* FIN   : On cherche les comp à utiliser         */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : Modificateur si attaque non normale    */
  /**************************************************/
  comp_attaque_init := comp_attaque;
  comp_attaque_amel := comp_attaque;
  if v_type_attaque != 0 AND v_type_attaque != 20 then
    num_comp := num_comp_spe;
    val_comp_attaque_orig := comp_attaque;
    select into comp_attaque
      pcomp_modificateur
    from perso_competences
    where pcomp_perso_cod = v_attaquant
          and pcomp_pcomp_cod = num_comp_spe;
    if not found then
      code_retour := '<p>Erreur ! Vous n’avez la compétence requise pour ce type d’attaque !<br>';
      return code_retour;
    end if;
    comp_attaque_amel := comp_attaque;
    comp_attaque := round(comp_attaque * (val_comp_attaque_orig*0.01));
  end if;
  /**************************************************/
  /* FIN   : Modificateur si attaque non normale    */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : Modificateur liés aux seuils des armes */
  /**************************************************/
  if v_type_arme != 0 then
    select into v_force, v_dex
      perso_for, perso_dex
    from perso
    where perso_cod = v_attaquant;
    if v_seuil_force != 0 then
      if v_force >= v_seuil_force then
        comp_attaque := comp_attaque + ((v_force - v_seuil_force) * 1);
      else
        comp_attaque := comp_attaque + ((v_force - v_seuil_force) * 3);
      end if;
    end if;
    if v_seuil_dex != 0 then
      if v_dex >= v_seuil_dex then
        comp_attaque := comp_attaque + ((v_dex - v_seuil_dex) * 2);
      else
        comp_attaque := comp_attaque + ((v_dex - v_seuil_dex) * 4);
      end if;
    end if;
  end if;
  /**************************************************/
  /* FIN   : Modificateur liés aux seuils des armes */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : Modificateur concentration             */
  /**************************************************/
  select into compt concentration_perso_cod from concentrations
  where concentration_perso_cod = v_attaquant;
  if found then
    comp_attaque := comp_attaque + 20;
    delete from concentrations
    where concentration_perso_cod = v_attaquant;
  end if;
  /**************************************************/
  /* FIN   : Modificateur concentration             */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : Modificateur harmonie offensive        */
  /**************************************************/
  if v_type_arme = 2 then
    comp_attaque := comp_attaque + valeur_bonus(v_attaquant, 'HOF');
  end if;
  /**************************************************/
  /* FIN   : Modificateur harmonie offensive        */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : Modificateurs liés aux sorts           */
  /**************************************************/
  comp_attaque := comp_attaque + valeur_bonus(v_attaquant, 'TOU');
  if v_type_arme = 2 then
    comp_attaque := comp_attaque + valeur_bonus(v_attaquant, 'PTD');
  else
    comp_attaque := comp_attaque + valeur_bonus(v_attaquant, 'PCC');
  end if;
  /**************************************************/
  /* FIN   : Modificateurs liés aux sorts           */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : Calculs chance toucher (attaquant)     */
  /**************************************************/
  ---------------------------------------------------------------------------
  -- introduction de la surcharge
  comp_attaque := comp_attaque - v_malus_surcharge;
  ---------------------------------------------------------------------------
  -- Modificateur en fonction du type de combat (offensif, défensif, normal)
  select into v_modif_att mcom_modif_att
  from mode_combat, perso
  where perso_cod = v_attaquant
        and perso_mcom_cod = mcom_cod;
  comp_attaque := round(v_facteur1 * comp_attaque * v_modif_att);
  ---------------------------------------------------------------------------
  -- on verifie en cas d’attaque à distance s’ils ne sont pas sur la même case
  if v_type_arme = 2 then
    if pos_per1 = pos_per2 then
      comp_attaque := comp_attaque - getparm_n(25);
      if v_type_attaque = 10 then
        comp_attaque := comp_attaque + 5;
      elsif v_type_attaque = 11 then
        comp_attaque := comp_attaque + 15;
      elsif v_type_attaque = 12 then
        comp_attaque := comp_attaque + 25;
      end if;
      if comp_attaque < 10 then
        comp_attaque := 10;
      end if;
    end if;
  end if;
  /**************************************************/
  /* FIN   : Calculs chance toucher (attaquant)     */
  /**************************************************/
  --
  /**************************************************/
  /* DEBUT : Calculs défense cible                  */
  /**************************************************/
  c_def := f_chance_attaque(v_cible);

  --
  -- différences d’armes
  --
  m_def := modif_type_arme(v_attaquant, v_cible);
  c_def := round(m_def*c_def*ln(c_def)/4);
  --	c_def := round(c_def * m_def);
  ------------------------------------------
  -- Fin différences d’armes
  ------------------------------------------
  --
  -- modificateurs en fonction du mode de combat
  --
  select into v_modif_def mcom_modif_def
  from mode_combat, perso
  where perso_cod = v_cible
        and perso_mcom_cod = mcom_cod;
  -- ajout azaghal le 26/03/09, si la cible est sous danse de saint guy, le modificateur sera toujours de 1.2
  if valeur_bonus(v_cible, 'DSG') != 0 then
    v_modif_def := v_modif_def + 0.2;
  end if;
  c_def := round(c_def * v_modif_def);
  c_def := c_def + v_facteur2;
  --
  -- fin modificateurs en fonction du mode de combat
  --
  /**************************************************/
  /* FIN   : Calculs défense cible                  */
  /**************************************************/
  --
  comp_attaque := round(comp_attaque / c_def);
  -- honneur
  comp_attaque := comp_attaque + nb_hon;
  if comp_attaque < 10 then
    comp_attaque := 10;
  end if;
  /********************************************************************************/
  /* NOTE IMPORTANTE : la valeur comp_attaque représente maintenant la chance     */
  /*  réélle de toucher un adversaire                                             */
  /* Elle ne doit plus être modifiée après ce point                               */
  /********************************************************************************/
  ---------------------------------------------------------------------------
  -- on commence à générer un code retour
  code_retour := code_retour || 'Vous avez attaqué le ';
  if type_cible = 1 then
    code_retour := code_retour || 'joueur ';
  else
    code_retour := code_retour || 'monstre ';
  end if;
  code_retour := code_retour || ' <b>' || nom_per2 || '</b><br><br>'; -- pos 2
  if v_type_attaque != 0 AND v_type_attaque != 20 then
    select into nom_competence comp_libelle from competences
    where comp_cod = num_comp_spe;
  else
    select into nom_competence comp_libelle from competences
    where comp_cod = num_comp;
  end if;
  code_retour := code_retour || 'Vous avez utilisé la compétence <b>' || nom_competence;
  code_retour := code_retour || '</b> (chance de toucher :<b>' || trim(to_char(comp_attaque, '999')) || '</b> %)<br>';
  debug_txt := debug_txt || 'chance de toucher : ' || trim(to_char(comp_attaque, '999')) || ' / ';
  ---------------------------------------------------------------------------
  -- mémorisation de la limite pour une attaque spéciale
  limite_comp_maitre := round(comp_attaque * v_limite_maitre);
  debug_txt := debug_txt || 'limite pour coup spécial : ' || trim(to_char(limite_comp_maitre, '9999999999')) || '.<br>';
  -- etape 5.3 : on regarde si l attaque a réussi
  -- etape 5.3.1 on regarde si la l’attaquant est bénie ou maudite
  -- etape  on regarde si la cible est bénie ou maudite
  bonmal := valeur_bonus(v_attaquant, 'BEN') + valeur_bonus(v_attaquant, 'MAU');
  if bonmal <> 0 then
    des := lancer_des3(1, 100, bonmal);
  else
    des := lancer_des(1, 100);
  end if;
  code_retour := code_retour || 'Votre lancer de dés est de <b>' || trim(to_char(des, '999')) || '</b>, '; -- pos 5 lancer des
  /**********************************************************/
  /* DEBUT : attaque ratée sur échec critique               */
  /**********************************************************/
  if des > 96 then -- echec critique
    code_retour := code_retour || 'il s’agit donc d’un échec automatique.<br><br>';
    px_gagne := 0;
    if v_type_arme != 0 then
      update objets set obj_etat = obj_etat - (usure_arme * 2) where obj_cod = num_arme
      returning obj_etat into etat_arme;

      if etat_arme <= 0 then
        texte_evt := 'L’arme de [perso_cod1] s’est brisée !';
        perform insere_evenement(v_attaquant, v_attaquant, 37, texte_evt, 'O', NULL);
        compt := f_del_objet(num_arme);
        code_retour := code_retour || '<b>Votre arme s’est brisée pendant l’attaque !</b><br>';
      end if;
    end if;
    texte_evt := '[attaquant] a raté une attaque ' || nom_arme || nom_type_attaque || ' contre [cible]';
    perform insere_evenement(v_attaquant, nv_cible, 9, texte_evt, 'O', NULL);
    code_retour := code_retour || 'Vous gagnez ' || trim(to_char(px_gagne, '99999')) || ' PX pour cette action.';
    var_gain := 0;
    return code_retour;
  /**********************************************************/
  /* FIN   : attaque ratée sur échec critique               */
  /**********************************************************/
  --
  /***************************/
  /* DEBUT : attaque ratée   */
  /***************************/
  elsif des > comp_attaque then -- attaque loupée !!
    code_retour := code_retour || 'l’attaque a donc échoué.<br><br>'; -- pos 6
    texte_evt := '[attaquant] a raté une attaque ' || nom_arme || nom_type_attaque || ' contre [cible]';
    if comp_attaque_amel <= getparm_n(1) then -- amélioration auto
      temp_ameliore_competence := ameliore_competence_px(v_attaquant, num_comp, comp_attaque_amel);
      code_retour := code_retour || 'Votre jet d’amélioration est de ' || split_part(temp_ameliore_competence, ';', 1) || ', '; -- pos 7 8 9 10
      if split_part(temp_ameliore_competence, ';', 2) = '1' then
        code_retour := code_retour || 'vous avez donc <b>amélioré</b> cette compétence. <br>';
        code_retour := code_retour || 'Sa nouvelle valeur est ' || split_part(temp_ameliore_competence, ';', 3) || '<br><br>';
        px_gagne := px_gagne + 1;
      else
        code_retour := code_retour || 'vous n’avez pas amélioré cette compétence.<br><br> ';
      end if;
    end if; --  fin amélioration auto
    perform insere_evenement(v_attaquant, nv_cible, 9, texte_evt, 'O', NULL);
    code_retour := code_retour || 'Vous gagnez ' || trim(to_char(px_gagne, '99999')) || ' PX pour cette action.';
    return code_retour;
  end if; -- fin attaque loupée
  /***************************/
  /* FIN   : attaque ratée   */
  /***************************/
  --
  /*********************************************/
  /* DEBUT : spécificités sur attaque distante */
  /*********************************************/
  --
  /*********************************************/
  /* DEBUT : calcul trajectoire si surcharge   */
  /*********************************************/
  -- en cas d’attaque distante, on vérifie la trajectoire
  if v_type_arme = 2 then
    -- etape de préparation, on va faire les contrôles liés à la taille
    --
    chance_touche_autre := v_nb_surcharge * getparm_n(58);
    if chance_touche_autre > 80 then
      chance_touche_autre := 80;
    end if;
    des_autre := lancer_des(1, 100);
    if ((v_type_attaque < 13) or (v_type_attaque > 15)) then
      if des_autre < chance_touche_autre then
        -- a partir d’ici, mauvaise nouvelle on touche un assaillant
        -- la mauvaise nouvelle est pour l’attaquant, mais aussi pour moi, faut que j’en trouve un
        select into nv_cible, nom_per2, pv_cible, v_armure_physique, armure
          perso_cod, perso_nom, perso_pv, f_armure_perso_physique(perso_cod), f_armure_perso(perso_cod)
        from perso, lock_combat
        where lock_cible = v_cible
              and lock_attaquant = perso_cod
              and perso_actif = 'O'
              and perso_tangible = 'O'
        order by random()
        limit 1;
        if num_comp = 55 then
          code_retour := code_retour || 'votre cible est surchargée, vous touchez un de ses assaillants : vous frappez malencontreusement ' || nom_per2;
          texte_evt := '[attaquant] n’a  pas touché la cible prévue.';
        else
          code_retour := code_retour || 'votre cible est surchargée, vous touchez un de ses assaillants : ' || nom_per2 || ' reçoit votre projectile. ';
          texte_evt := 'Le projectile de [attaquant] n’a  pas touché la cible prévue.';
        end if;
        /* evts pour coups portes */
        perform insere_evenement(v_attaquant, nv_cible, 35, texte_evt, 'O', NULL);
      end if;
    end if;
    /*********************************************/
    /* FIN   : calcul trajectoire si surcharge   */
    /*********************************************/
    --
    /*********************************************/
    /* DEBUT : calcul trajectoire normale        */
    /*********************************************/
    v_trajectoire := trajectoire(pos_per1, pos_per2, nv_cible, v_attaquant);
    res_traj := split_part(v_trajectoire, ';', 1);
    nv_traj := split_part(v_trajectoire, ';', 2);
    if res_traj = 1 then
      -- on se mange un mur
      select into pos_x_mur, pos_y_mur pos_x, pos_y
      from positions
      where pos_cod = nv_traj;
      code_retour := code_retour || 'Votre projectile arrive dans un mur en position ' || trim(to_char(pos_x_mur, '99999')) || ', ' || trim(to_char(pos_y_mur, '99999')) || '<br>.';
      code_retour := code_retour || 'Vous gagnez ' || trim(to_char(px_gagne, '99999')) || ' PX pour cette action.';
      return code_retour;
    end if;
    if res_traj = 2 and v_type_attaque not in (13, 14, 15) then
      nv_cible := nv_traj;
      select into type_cible,
        nom_per2,
        pos_per2,
        pv_cible,
        v_armure_physique,
        armure
        perso_type_perso,
        perso_nom,
        ppos_pos_cod,
        perso_pv,
        f_armure_perso_physique(nv_cible),
        f_armure_perso(nv_cible)
      from perso, perso_position
      where perso_cod = nv_cible and ppos_perso_cod = perso_cod;

      code_retour := code_retour || 'Votre projectile frappe <b>' || nom_per2 || '</b> sur sa trajectoire.<br>';
      /* evts pour coups portes */
      texte_evt := 'Le projectile de [attaquant] n’a  pas touché la cible prévue.';
      perform insere_evenement(v_attaquant, nv_cible, 35, texte_evt, 'O', NULL);
      var_gain := 0;
    end if;
  end if;
  /*********************************************/
  /* FIN   : calcul trajectoire normale        */
  /*********************************************/
  --
  /*********************************************/
  /* FIN   : spécificités sur attaque distante */
  /*********************************************/
  --
  /*********************************************/
  /* DEBUT : spécificités sur Folie Ecatis     */
  /*********************************************/
  if valeur_bonus(v_attaquant, 'FEC') != 0 then
    des_autre := lancer_des(1, 100);
    if des_autre <= v_bonus_degats then
      -- on doit trouver une autre cible sur la même case
      select into nv_cible, nom_per2, pv_cible
        perso_cod, perso_nom, perso_pv
      from perso, perso_position
      where ppos_pos_cod = pos_per2
            and ppos_perso_cod = perso_cod
            and perso_actif = 'O'
            and not exists
              (select 1 from lieu,lieu_position
              where lpos_pos_cod = ppos_pos_cod
              and lpos_lieu_cod = lieu_cod
              and lieu_refuge = 'O')
      order by random()
      limit 1;
      code_retour := code_retour || 'Vous êtes sous le coup de la folie d’Ecatis et votre bras dévie au dernier moment. ' || nom_per2 || ' reçoit votre attaque.<br> ';
      /* evts pour coups portes */
      texte_evt := 'L’attaque de [attaquant] n’a pas touché la cible prévue.';
      perform insere_evenement(v_attaquant, nv_cible, 35, texte_evt, 'O', NULL);
      var_gain := 0;
    end if;
  end if;
  /*********************************************/
  /* FIN   : spécificités sur Folie Ecatis     */
  /*********************************************/

  /*********************************************/
  /* DEBUT : spécificités sur Désorientation   */
  /*         Conséquence de Morsure du Soleil  */
  /*********************************************/
  /* 2018-09-06 - Marlyza - on insere maintenant l'évenement de désorrientation et que s'il y a eu lieu */
  if texte_desorientation != '' then
    code_retour := code_retour || texte_desorientation || nom_per2 || ' reçoit votre attaque.<br> ';

    /* evts pour coups portes et annulation du gain de px: seulement s'il y a eu désorientation */
    if v_desorientation != 0 then
      texte_evt := 'L’attaque de [attaquant] n’a pas touché la cible prévue.';
      perform insere_evenement(v_attaquant, nv_cible, 35, texte_evt, 'O', NULL);
      var_gain := 0;
    end if;

  end if;
  /*********************************************/
  /* FIN   : spécificités sur Désorientation   */
  /*********************************************/

  /* Modif Kahlann prise en compte EA sur evenement Attaque */
  /**************************************************/
  /* on regarde si une fonction doit être exécutée  */
  /**************************************************/
  -- on exécute les fonctions déclenchées par l'attaque portée ou recue
  code_retour := code_retour || execute_fonctions(v_attaquant, v_cible, 'A', null);
  code_retour := code_retour || execute_fonctions(v_cible, v_attaquant, 'AC', null);
  /* Fin modif Kahlann */

  --
  /*********************************************/
  /* DEBUT : attaque portée, coup critique     */
  /*********************************************/
  -- ajout azaghal la cible est sous défense du juste
  if valeur_bonus(nv_cible, 'JUS') != 0 then
    if des <= 5 then
      des := limite_comp_maitre + 1;
      code_retour := code_retour || 'Alors que votre coup semblait parfait, un éclair de lumière vient s’interposer déviant légèrement votre coup pour le rendre moins parfait.';
      delete from bonus where bonus_perso_cod = nv_cible and bonus_mode != 'E' and bonus_tbonus_libc = 'JUS';
      texte_evt := '[cible] a été protégé d’un coup critique par son dieu.';
      perform insere_evenement(v_attaquant, nv_cible, 47, texte_evt, 'O', NULL);
    end if;
  end if;

  -- calcul de la limite du critique (prise en comtpe des bonus malus AttCRitique): => garde de fou entre 0 et 100
  v_attaque_critique := LEAST(100, GREATEST( 0, 5 + valeur_bonus(v_attaquant, 'ATC') ) );
  if des <= v_attaque_critique then
    if v_armure_physique != 0 then
      if v_type_arme != 0 then
        update objets set obj_etat = obj_etat - (usure_arme * 2) where obj_cod = num_arme
        returning obj_etat into etat_arme;

        if etat_arme <= 0 then
          texte_evt := 'L’arme de [perso_cod1] s’est brisée !';
          perform insere_evenement(v_attaquant, v_attaquant, 37, texte_evt, 'O', NULL);
          compt := f_del_objet(num_arme);
          code_retour := code_retour || '<b>Votre arme s’est brisée pendant l’attaque !</b><br>';
        end if;
      end if;
    else
      if v_type_arme = 2 then
        update objets set obj_etat = obj_etat - usure_arme where obj_cod = num_arme
        returning obj_etat into etat_arme;

        if etat_arme <= 0 then
          texte_evt := 'L’arme de [perso_cod1] s’est brisée !';
          perform insere_evenement(v_attaquant, v_attaquant, 37, texte_evt, 'O', NULL);
          compt := f_del_objet(num_arme);
          code_retour := code_retour || '<b>Votre arme s’est brisée pendant l’attaque !</b><br>';
        end if;
      end if;
    end if;
    qualite_attaque := 2;
    nom_qualite_attaque := ' (coup critique) ';
    px_gagne := px_gagne + 2;
    code_retour := code_retour || 'il s’agit d’un coup critique. L’armure de la cible est ignorée.'; -- pos 6
    armure := 0;
    -- etape 5.4 : l attaque a réussi, on attribue un PX
    -- etape 5.5 : on calcule les dégâts portés
    /*v_bonus_critique integer;
    v_des_critique integer;*/
    v_bonus_critique := bonus_art_critique(nv_cible);
    if v_bonus_critique != 0 then
      v_des_critique := lancer_des(1, 100);
      if v_des_critique < v_bonus_critique then
        code_retour := code_retour || '<br>L''équipement de la cible la protège. Le coup est transformé en <b>spécial</b> (armure de la cible divisée par deux).<br>';
        /* evt pour esquive */
        texte_evt := '[cible] a été protégé d’un coup critique par son équipement.';
        perform insere_evenement(v_attaquant, nv_cible, 47, texte_evt, 'O', NULL);

        select into v_casque perobj_obj_cod
        from perso_objets, objets, objet_generique
        where perobj_perso_cod = nv_cible
              and perobj_equipe ='O'
              and perobj_obj_cod = obj_cod
              and obj_gobj_cod = gobj_cod
              and coalesce(obj_critique,0) != 0
              -- and gobj_tobj_cod = 4;  /* avant seuls les casques avait ce privilège */
        order by obj_critique*random() desc limit 1;
        if found then
            temp_use_casque := use_artefact(v_casque);
        end if;
        qualite_attaque := 2;
        nom_qualite_attaque := ' (coup spécial) ';
        armure := floor(f_armure_perso(nv_cible)/2);
      end if;
    end if;
    code_retour := code_retour || '<br><br>';
    degats_portes := lancer_des(nb_des_attaque, valeur_des_attaque) + bonus_attaque;
    deg_max := (nb_des_attaque * valeur_des_attaque) + bonus_attaque;
    degats_portes := degats_portes + valeur_bonus(v_attaquant, 'DEG');
    deg_max := deg_max + valeur_bonus(v_attaquant, 'DEG');
  /*********************************************/
  /* FIN   : attaque portée, coup critique     */
  /*********************************************/
  --
  /*********************************************/
  /* DEBUT : attaque portée, coup spécial      */
  /*********************************************/
  elsif (des <= limite_comp_maitre) then -- coup spécial - armure / 2
    if v_armure_physique != 0 then
      if v_type_arme != 0 then
        update objets set obj_etat = obj_etat - (usure_arme * 1.5) where obj_cod = num_arme
        returning obj_etat into etat_arme;

        if etat_arme <= 0 then
          texte_evt := 'L’arme de [perso_cod1] s’est brisée !';
          perform insere_evenement(v_attaquant, v_attaquant, 37, texte_evt, 'O', NULL);
          compt := f_del_objet(num_arme);
          code_retour := code_retour || '<b>Votre arme s’est brisée pendant l’attaque !</b><br>';
        end if;
      end if;
    else
      if v_type_arme = 2 then
        update objets set obj_etat = obj_etat - usure_arme where obj_cod = num_arme
        returning obj_etat into etat_arme;

        if etat_arme <= 0 then
          texte_evt := 'L’arme de [perso_cod1] s’est brisée !';
          perform insere_evenement(v_attaquant, v_attaquant, 37, texte_evt, 'O', NULL);
          compt := f_del_objet(num_arme);
          code_retour := code_retour || '<b>Votre arme s’est brisée pendant l’attaque !</b><br>';
        end if;
      end if;
    end if;
    qualite_attaque := 1;
    nom_qualite_attaque := ' (coup spécial) ';
    --
    -- Gain de px dégressif
    --
    if getparm_n(105) = 0 then
      px_gagne := px_gagne + 1;
    else
      update perso set perso_nb_spe = perso_nb_spe + 1 where perso_cod = v_attaquant
      returning perso_nb_spe into v_perso_nb_spe;
      px_gagne := px_gagne + (1 / (v_perso_nb_spe - 1)::numeric);
    end if;
    code_retour := code_retour || 'Il s’agit d’un coup spécial. L’armure de l’adversaire est divisée par 2.';
    recalcul_spe = v_limite_maitre * comp_attaque;
    armure := floor(f_armure_perso(nv_cible)/2);
    v_bonus_critique := bonus_art_critique(nv_cible);
    if v_bonus_critique != 0 then
      v_des_critique := lancer_des(1, 100);
      if v_des_critique < v_bonus_critique then
        code_retour := code_retour || '<br>L''équipement de la cible la protège. Le coup est transformé en <b>normal</b>.<br>';

        /* evt pour casque */
        texte_evt := '[cible] a été protégé d’un coup spécial par son équipement.';
        perform insere_evenement(v_attaquant, nv_cible, 47, texte_evt, 'O', NULL);

        select into v_casque perobj_obj_cod
        from perso_objets, objets, objet_generique
        where perobj_perso_cod = nv_cible
              and perobj_equipe ='O'
              and perobj_obj_cod = obj_cod
              and obj_gobj_cod = gobj_cod
              and coalesce(obj_critique,0) != 0
              -- and gobj_tobj_cod = 4;  /* avant seuls les casques avait ce privilège */
        order by obj_critique*random() desc limit 1;
        if found then
            temp_use_casque := use_artefact(v_casque);
        end if;
        armure := f_armure_perso(nv_cible);
        qualite_attaque := 0;
        nom_qualite_attaque := '';

      end if;
    end if;
    code_retour := code_retour || '<br><br>';
    -- etape 5.5 : on calcule les dégâts portés
    degats_portes := lancer_des(nb_des_attaque, valeur_des_attaque) + bonus_attaque;
    deg_max := (nb_des_attaque * valeur_des_attaque) + bonus_attaque;
    degats_portes := degats_portes + valeur_bonus(v_attaquant, 'DEG');
    deg_max := deg_max + valeur_bonus(v_attaquant, 'DEG');
  --armure := f_armure_perso(nv_cible);
  /*********************************************/
  /* FIN   : attaque portée, coup spécial      */
  /*********************************************/
  --
  /*********************************************/
  /* DEBUT : attaque portée, coup normal       */
  /*********************************************/
  else
    if v_armure_physique != 0 AND v_type_arme != 0
       OR v_armure_physique = 0 AND v_type_arme = 2
    then
      update objets set obj_etat = obj_etat - usure_arme where obj_cod = num_arme
      returning obj_etat into etat_arme;

      if etat_arme <= 0 then
        texte_evt := 'L’arme de [perso_cod1] s’est brisée !';
        perform insere_evenement(v_attaquant, v_attaquant, 37, texte_evt, 'O', NULL);
        compt := f_del_objet(num_arme);
        code_retour := code_retour || '<b>Votre arme s’est brisée pendant l’attaque !</b><br>';
      end if;
    end if;

    code_retour := code_retour || 'L’attaque a donc réussi.'; -- pos 6
    if nb_hon != 0 then
      code_retour := code_retour || 'Grâce au sort honneur, le coup devient un coup spécial.';
      qualite_attaque := 1;
    end if;
    code_retour := code_retour || '<br><br>';
    -- cas particulier pour les feintes, on force un esquive spéciale
    if v_type_attaque in (4, 5, 6) then
      qualite_attaque := 1;
    end if;
  end if;
  /*********************************************/
  /* FIN   : attaque portée, coup normal       */
  /*********************************************/
  --
  /*Information sur la dégradation de l’état de l’arme*/
  select into etat_apres get_etat_objet(obj_etat) from objets where obj_cod = num_arme;
  if found and etat_apres != etat_avant then
    code_retour := code_retour || 'Lors de cette attaque, votre arme a passé un seuil d’usure. Prenez garde à vérifier son état prochainement.
			<br>Elle est maintenant dans <b>un état ' || etat_apres || '</b><br><br>';
  end if;
  /*********************************************/
  /* DEBUT : calcul des dégâts portes          */
  /*********************************************/
  if v_type_arme = 2 then
    bonus_attaque := bonus_attaque + v_amel_degats_dist;
    bonus_attaque := bonus_attaque + valeur_bonus(v_attaquant, 'PDD');
  else
    bonus_attaque := bonus_attaque + v_amelioration_degats + bonus_degats_melee(v_attaquant) + valeur_bonus(v_attaquant, 'PDC');
  end if;
  degats_portes := lancer_des(nb_des_attaque, valeur_des_attaque) + bonus_attaque;
  deg_max := (nb_des_attaque * valeur_des_attaque) + bonus_attaque;
  degats_portes := degats_portes + valeur_bonus(v_attaquant, 'DEG');
  deg_max := deg_max + valeur_bonus(v_attaquant, 'DEG');
  -- armure := f_armure_perso(nv_cible);
  -- rajout de la chute
  distance_cible := distance(pos_per1, pos_per2);
  if distance_cible = 0 then
    distance_cible := 1;
  end if;
  malus_degats := floor((distance_cible - 1) * v_chute);
  degats_portes := degats_portes - malus_degats;
  deg_max := deg_max - malus_degats;
  -- cas particuliers : AF et CdG
  if v_type_attaque = 1 then
    degats_portes := round(degats_portes / 2);
  elsif v_type_attaque = 2 then
    degats_portes := round(degats_portes * .75);
  elsif v_type_attaque in (7, 8, 9) then
    degats_portes := deg_max;
  end if;
  /*********************************************/
  /* FIN   : calcul des dégâts portes          */
  /*********************************************/
  --
  /*********************************************/
  /* DEBUT : gains de px et améliorations      */
  /*********************************************/
  update perso set perso_px = perso_px + px_gagne where perso_cod = v_attaquant;

  -- etape 5.4 bis : on augmente la compétence utilisée
  temp_ameliore_competence := ameliore_competence_px(v_attaquant, num_comp, comp_attaque_amel);
  code_retour := code_retour || 'Votre jet d’amélioration est de ' || split_part(temp_ameliore_competence, ';', 1) || ', '; -- pos 7 8 9 10
  if split_part(temp_ameliore_competence, ';', 2) = '1' then
    code_retour := code_retour || 'vous avez donc <b>amélioré</b> cette compétence.<br> ';
    code_retour := code_retour || 'Sa nouvelle valeur est ' || split_part(temp_ameliore_competence, ';', 3) || '<br><br>';
    px_gagne := px_gagne + 1;
  else
    code_retour := code_retour || 'vous n’avez pas amélioré cette compétence.<br><br> ';

  end if;
  /*********************************************/
  /* FIN   : gains de px et améliorations      */
  /*********************************************/
  --
  -- si monstre, on change la cible
  if type_cible = 2 then
    temp_change_cible := change_cible_attaque(nv_cible, v_attaquant);
  end if;
  --
  code_retour := code_retour || 'Vous portez une attaque de <b>' || trim(to_char(GREATEST(0,degats_portes), '9999')) || '</b> '; -- pos 11
  /************************************/
  /* DEBUT : esquive de la cible      */
  /************************************/
  if v_type_arme = 2 then
    reussite_esquive := f_esquive_distance(1, nv_cible, qualite_attaque);
  else
    reussite_esquive := f_esquive(1, nv_cible, qualite_attaque);
  end if;
  if reussite_esquive != 0 then -- esquive cible réussie
    code_retour := code_retour || 'que votre adversaire arrive à esquiver'; -- pos 12
    if qualite_attaque != 0 then
      code_retour := code_retour || ' malgré tout.';
    end if;
    code_retour := code_retour || '.<br>';

    /* evt pour esquive */
    texte_evt := '[attaquant] a attaqué ' || nom_arme || nom_type_attaque || ' [cible] qui a esquivé l’attaque' || nom_qualite_attaque;
    perform insere_evenement(v_attaquant, nv_cible, 9, texte_evt, 'O', NULL);

    code_retour := code_retour || 'Vous gagnez ' || trim(to_char(px_gagne, '99999')) || ' PX pour cette action.';
    if var_gain = 1 then
      insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
      values (1, v_attaquant, nv_cible, max(1, (degats_portes - armure) / 2));
    end if;
    /* Modif Kahlann prise en compte EA sur evenement Attaque Esquivée*/
    /**************************************************/
    /* on regarde si un EA doit être exécuté          */
    /**************************************************/
    -- on exécute les fonctions déclenchées par une attaque effectuee esquivee ou une attaque recue esquivee
    code_retour := code_retour || execute_fonctions(v_attaquant, v_cible, 'AE', null);
    code_retour := code_retour || execute_fonctions(v_cible, v_attaquant, 'ACE', null);
    /* Fin modif Kahlann */
    --code_retour := code_retour || '<hr><b>Informations de débuggage : </b><br><i>' || debug_txt || '</i><br><hr>';
    /*insert into trace2 (trace2_texte) values (debug_txt);*/
    return code_retour;
  else
    code_retour := code_retour || 'que votre adversaire n’a pas réussi à esquiver.<br>'; -- pos 12
    /* Modif Kahlann prise en compte EA sur evenement Attaque */
    /**************************************************/
    /* on regarde si une fonction doit être exécutée  */
    /**************************************************/
    -- on exécute les fonctions déclenchées par une attaque effectuee qui touche ou une attaque recue qui touche
    code_retour := code_retour || execute_fonctions(v_attaquant, v_cible, 'AT', null);
    code_retour := code_retour || execute_fonctions(v_cible, v_attaquant, 'ACT', null);
  /* Fin modif Kahlann */
  end if; -- fin esquive
  /************************************/
  /* FIN   : esquive de la cible      */
  /************************************/
  --
  /****************************/
  /* DEBUT : aura de feu      */
  /****************************/
  if v_type_arme != 2 then
    v_aura_feu := bonus_art_aura_feu(nv_cible);
    if v_aura_feu != 0 then
      v_degats_aura_feu := round(degats_portes * v_aura_feu);
      v_degats_aura_feu := effectue_degats(v_attaquant, v_degats_aura_feu, type_cible);
      if v_degats_aura_feu < 1 then
        v_degats_aura_feu := 1;
      end if;
      code_retour := code_retour || '<br>L’aura de feu de votre adversaire vous provoque <b>' || trim(to_char(v_degats_aura_feu, '99999999')) || '</b> dégâts.<br>';
      if v_degats_aura_feu > v_pv_attaquant then
        code_retour := code_retour || 'Vous avez été <b>tué</b> par l’aura de feu de votre adversaire !<br>';
        aura_texte := tue_perso_final(nv_cible, v_attaquant);
      else
        update perso set perso_pv = perso_pv - v_degats_aura_feu where perso_cod = v_attaquant;
      end if;
      code_retour := code_retour || '<br>';
      texte_evt := 'L’aura de feu de [attaquant] a causé ' || trim(to_char(v_degats_aura_feu, '9999')) || ' dégâts à [cible].';
      perform insere_evenement(nv_cible, v_attaquant, 46, texte_evt, 'N', NULL);
    end if;
  end if;
  /****************************/
  /* FIN   : aura de feu      */
  /****************************/
  --
  -- effet de la charge
  if v_type_attaque = 20 then
    malus := 0;
    malus := floor(v_force / 6);
    armure := armure - malus;
    if armure < 0 then
      armure = 0;
    end if;
  end if;
  /*****************************************/
  /* DEBUT : coup porté : ajout des dégâts */
  /*****************************************/
  if not v_perce_armure then
    code_retour := code_retour || 'Il a une armure de ' || trim(to_char(armure, '9999')) || ', '; -- pos 13
    -- prise en compte des spéciaux et critiques
    degats_effectues := degats_portes - armure;
    if degats_effectues <= 0 then
      degats_effectues := 0;
    end if; -- degats
    code_retour := code_retour || 'et vous portez donc ' || trim(to_char(degats_effectues, '9999')) || ' points de dégâts.<br>'; -- pos 14
    etat_armure := f_use_armure(nv_cible, degats_portes);
  else
    armure := floor( armure * (1 - v_coeff_perce ));
    code_retour := code_retour || 'Dû à votre arme, il a une armure de ' || trim(to_char(armure, '9999')) || ', '; -- pos 13
    -- prise en compte des spéciaux et critiques
    degats_effectues := degats_portes - armure;
    if degats_effectues <= 0 then
      degats_effectues := 0;
    end if; -- degats
    code_retour := code_retour || 'et vous portez donc ' || trim(to_char(degats_effectues, '9999')) || ' points de dégâts.<br>'; -- pos 14
    etat_armure := f_use_armure(nv_cible, degats_portes);
  end if;
  --
  -- ajout de la fonction effectue_degats
  --
  v_degats_effectues := effectue_degats(nv_cible, degats_effectues, type_attaquant);
  if v_degats_effectues != degats_effectues then
    code_retour := code_retour || '<br>Les dégâts réels liés à l’initiative sont de ' || trim(to_char(v_degats_effectues, '999999999')) || '.<br />';
    insert into trace (trc_texte) values ('att ' || trim(to_char(v_attaquant, '99999999')) || ' cib ' || trim(to_char(nv_cible, '99999999')) || ' init ' || trim(to_char(degats_effectues, '99999999')) || ' fin ' || trim(to_char(v_degats_effectues, '99999999')));
  end if;
  degats_effectues := v_degats_effectues;
  if var_gain = 1 then
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (1, v_attaquant, nv_cible, degats_effectues);
  end if;

  -- 2020-02-20 - Marlyza - Ajout d'un bonus de vapirisme on maximise aussi à 100% (=1) on ne peut pas gagner plus que ce que l'on prend.
  -- 2024-09-04 - Marlyza - ajout d'une limite basse à 0 en cas de malus de VMP
  v_vampire :=  GREATEST( 0, LEAST( 1, coalesce(v_vampire, 0) + ( valeur_bonus(v_attaquant, 'VMP')::numeric / 100) ));
  if v_vampire > 0 then
    regen_vampire := floor(degats_effectues*v_vampire);
    diff_pv := pv_max_attaquant - pv_attaquant;
    if regen_vampire > diff_pv then
      regen_vampire := diff_pv;
    end if;
    update perso set perso_pv = perso_pv + regen_vampire where perso_cod = v_attaquant;
    code_retour := code_retour || 'Vous avez régénéré ' || trim(to_char(regen_vampire, '9999999')) || ' points de vie grâce au vampirisme.<br>';
  end if;
  if etat_armure = 2 then
    code_retour := code_retour || 'Vous avez <b>brisé</b> l’armure de votre adversaire !<br>';
  end if;
  nouveau_pv_cible := pv_cible - degats_effectues;
  /*****************************************/
  /* FIN   : coup porté : ajout des dégâts */
  /*****************************************/
  --
  /*****************************************/
  /* DEBUT : coup porté : cible morte      */
  /*****************************************/
  if nouveau_pv_cible <= 0 then -- la cible a été tuée......
    if type_cible != 3 then
      code_retour := code_retour || 'Vous avez <b>tué</b> votre adversaire.<br>'; -- pos 15
    else
      if (select perso_gmon_cod from perso where perso_cod = nv_cible) in (381) then
        code_retour := code_retour || 'Votre adversaire subit un <b>CARTON ROUGE</b> et se retrouve éliminé du terrain ! Tant mieux !<br>';
      else
        code_retour := code_retour || 'Vous avez <b>tué</b> votre adversaire.<br>'; -- pos 15
      end if;
    end if;
    texte_evt := '[attaquant] a frappé [cible] ' || nom_arme || nom_type_attaque || ', infligeant ' || trim(to_char(degats_effectues, '9999')) || ' points de dégâts.' || nom_qualite_attaque;

    /* evts pour coup porté */
    perform insere_evenement(v_attaquant, nv_cible, 9, texte_evt, 'O', NULL);

    texte_mort := tue_perso_final(v_attaquant, nv_cible);

    px_gagne := px_gagne + to_number(split_part(texte_mort, ';', 1), '9999999D99');
    code_retour := code_retour || 'Vous gagnez ' || trim(to_char(px_gagne, '99999')) || ' PX pour cette action.';

    texte_mort_px := split_part(texte_mort, ';', 2);
    if trim(texte_mort_px) is not null then
      code_retour := code_retour || texte_mort_px;
    end if;

    return code_retour;
  /*****************************************/
  /* FIN   : coup porté : cible morte      */
  /*****************************************/
  --
  /*****************************************/
  /* DEBUT : coup porté : cible pas morte  */
  /*****************************************/
  else -- cible pas tuée
    texte_evt := '[attaquant] a frappé [cible] ' || nom_arme || nom_type_attaque || ', infligeant ' || trim(to_char(degats_effectues, '9999')) || ' points de dégâts.' || nom_qualite_attaque;

    /* evts pour coups portes */
    perform insere_evenement(v_attaquant, nv_cible, 9, texte_evt, 'O', NULL);

    update perso
    set perso_pv = nouveau_pv_cible
    where perso_cod = nv_cible;

    code_retour := code_retour || 'Votre adversaire a survécu à cette attaque. Il est maintenant <b>' || etat_perso(nv_cible) || '</b>.<br>'; -- pos 15, 16

    code_retour := code_retour || 'Vous gagnez ' || trim(to_char(px_gagne, '99999')) || ' PX pour cette action.';
    if code_retour is null then
      code_retour := 'erreur sur code_retour';
    end if;

    code_retour := code_retour || execute_fonctions(v_cible, v_attaquant, 'CES', null);    -- Controle Etat de Santé de la cible!

    return code_retour;
  /*****************************************/
  /* FIN   : coup porté : cible pas morte  */
  /*****************************************/
  end if;
end;$_$;


ALTER FUNCTION public.attaque(integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION attaque(integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION attaque(integer, integer, integer) IS 'Fonction gérant les attaques, hors attaques très spéciales';
