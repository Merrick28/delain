
--
-- Name: monture_competence(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION monture_competence(integer, integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function monture_competence : fait un jet de compétence sous la compétence équitation, réalise le gain de PX, et le jet d'augmentation
   Cette compétence est utilisé pour: chevaucher, mettre pied à terre, désarçonner, donner un ordre à la monture.
   On passe en paramètres
     $1 = lanceur
     $2 = type action
          1 : chevaucher
          2 : pied_a terre
          3 : ordre de monture
          4 : désarçonner la cible
     $3 = cible (si action 4) ou null
     $4 = difficulté
   Le code sortie est une chaine séparée par ;
    1 = compétence réussi ?
        0 = non
        1 = oui
    2 = niveau de reussite
        0 échec critique
        1 raté
        2 reussi
        3 special
        4 reussite critique
    3 = chaine html de sortie
*/
/*****************************************************************/
/* Créé le 16/04/2021                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	v_perso_cod alias for $1;		-- perso_cod du lanceur
	v_action alias for $2;		-- type action du lanceur
	v_perso_cible alias for $3;		-- perso_cod de la cible si désarçonnage
	v_difficulte alias for $4;		-- difficulté de la compétence
	v_retour integer;		-- code retour 0 raté / 1 réussi
	v_criticite integer;		-- niveau de reussite echec de 0 à 4
	code_retour text;		-- chaine html de sortie

  v_pos_pvp varchar(1);   -- zone PvP
  v_pos_protegee varchar(1);  -- Lieu protégé
  v_comp integer; -- valeur de base du perso sur la compétence
  v_comp_monture integer; -- valeur de base de la monture sur la compétence
  v_comp_modifie integer; -- valeur de la compétence avec les bonus/malus
  v_special integer; -- valeur de la compétence avec les bonus/malus
	bonmal integer;			-- bonus malus au lancé de des
	temp_ameliore_competence text;	-- chaine temporaire pour amélioration
  px_gagne numeric;		-- px gagnes pour ce sort
	v_perso_pa integer;		-- nombre de PA du perso
	cout_pa integer;		-- Cout en PA de la compétence
	des integer;			-- lancer de dés
	v_perso_cible_nom text;		-- perso cible ou monture
  v_comp_cible integer; -- valeur du perso cible sur la compétence (inclus BM)
  des_cible integer;			-- lancer de dés jet d'opposition
  compt integer;			-- fourre tout
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';

  -- les px (gain de base pour la compétence)
	px_gagne := 0.33::numeric ;

  -- cout des actions
  if v_action = 1 then
      -- 'chevaucher';
      cout_pa := 4 ;
  elsif v_action = 2 then
      -- 'mettre pied à terre';
      cout_pa := 4 ;
  elsif v_action = 3 then
      -- 'donner un ordre à la monture';
      select COALESCE(f_to_numeric(etage_monture->>'pa_action'::text),4) into cout_pa from perso
        join perso_position on ppos_perso_cod=perso_cod
        join positions on pos_cod=ppos_pos_cod
        join etage on etage_numero=pos_etage
        where perso_cod=v_perso_cod ;

  elsif v_action = 4 then
      -- 'désarçonner';
      cout_pa := 6 ;
  else
  	  return '0;0;<p>Erreur ! action inconnue.</p>';
  end if;


-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------

  select perso_pa into v_perso_pa from perso where perso_cod=v_perso_cod;
  if not found then
      return '0;0;<p>Erreur ! Perso introuvable </p>';
  end if;

  if v_perso_pa<cout_pa then
    return '0;0;<p>Erreur ! Vous n’avez pas assez de PA pour effectuer cette action. (' || cout_pa::text || ' requis)</p>';
  end if;

 if v_action = 1 and  v_perso_cible is null then

    return '0;0;<p>Erreur ! Vous devez cibler une monture à chevaucher</p>';

 elsif v_action = 4 then

    -- verification PvP autorisé pour les actions belliqeuses (désarçonner)
    -- sur la position du lanceur
    select into v_pos_protegee coalesce(lieu_refuge, 'N') from perso_position
        left outer join lieu_position ON lpos_pos_cod = ppos_pos_cod
        left outer join lieu ON lieu_cod = lpos_lieu_cod
        where ppos_perso_cod = v_perso_cod;
    if v_pos_protegee = 'O' then
      return '0;0;<p>Erreur ! Vous êtes sur un lieu refuge et ne pouvez donc pas désarconner une cible</p>';
    end if;

    if v_perso_cible is null then
      return '0;0;<p>Erreur ! Vous devez donner une cible pour désarconner</p>';
    end if;

    if f_perso_monture(v_perso_cible) is null then
      return '0;0;<p>Erreur ! Cette cible n’a pas de monture, il vous est impossible de la désarconner</p>';
    end if;

    -- sur la cible + zone de droit
    select into v_pos_pvp, v_pos_protegee pos_pvp, coalesce(lieu_refuge, 'N')
        from perso_position
        inner join positions on pos_cod = ppos_pos_cod
        inner join perso on perso_cod=ppos_perso_cod
        left outer join lieu_position ON lpos_pos_cod = pos_cod
        left outer join lieu ON lieu_cod = lpos_lieu_cod
        where ppos_perso_cod = v_perso_cible and perso_actif = 'O';
    if not found then
      return '0;0;<p>Erreur : cible non trouvée !</p>';
    end if;

    if  v_pos_pvp = 'N' or v_pos_protegee = 'O'then
      return '0;0;<p>Erreur ! Cette cible est en zone de droit ou sur un lieu protégé, il vous est impossible de la désarconner</p>';
    end if;

  end if;

  -- perso cible = monture si null
  if v_perso_cible is null then
    v_perso_cible := f_perso_monture(v_perso_cod) ;
  end if;

  select perso_nom into v_perso_cible_nom from perso where perso_cod = v_perso_cible ;
  if not found then
      return '0;0;<p>Erreur ! ce n’est pas possible de faire ça!!!!!</p>';
  end if;

------------------------------------------------------------
-- les controles semblent bons, on peut passer à la suite
------------------------------------------------------------

  if v_action = 1 then
   -- 'chevaucher';
    code_retour := code_retour || 'Vous tentez de chevaucher '|| v_perso_cible_nom || ' en utilisant votre compétence équitation.<br>';
  elsif v_action = 2 then
    -- 'mettre pied à terre';
    code_retour := code_retour || 'Vous tentez de descendre de '|| v_perso_cible_nom || ' en utilisant votre compétence équitation.<br>';
  elsif v_action = 3 then
    code_retour := code_retour || 'Vous tentez de donner un ordre à '|| v_perso_cible_nom || ' en utilisant votre compétence équitation.<br>';
  elsif v_action = 4 then
    code_retour := code_retour || 'Vous tentez de désarçonner '|| v_perso_cible_nom || ' de sa monture en utilisant votre compétence équitation.<br>';
  end if;


	-- calcul dla compétence de base
	select pcomp_modificateur into v_comp from perso_competences where pcomp_perso_cod = v_perso_cod and pcomp_pcomp_cod = 104;
	if not found then
      v_comp := 30 ;    -- 30% de base mini pour chaque perso
      INSERT INTO perso_competences( pcomp_perso_cod, pcomp_pcomp_cod, pcomp_modificateur) VALUES (v_perso_cod, 104, v_comp);
	end if;

  -- ajout des bonus / malus et de la difficulté de l'action
  v_comp_modifie := v_comp + bonus_equitation(v_perso_cod) - v_difficulte ;

  -- pour l'action chevaucher, on ajoute aussi le bonus/malus de la monture que l'on souhaite monter!
  if v_action = 1 then

      select pcomp_modificateur into v_comp_monture from perso_competences where pcomp_perso_cod = v_perso_cible and pcomp_pcomp_cod = 104;
      if found then
          v_comp_modifie := v_comp_modifie + v_comp_monture ;
      end if;
      v_comp_modifie := v_comp_modifie + bonus_equitation(v_perso_cible);

  end if;

  -- on regarde s il y a concentration
  select into compt concentration_perso_cod from concentrations  where concentration_perso_cod = v_perso_cod;
  if found then
    v_comp_modifie := v_comp_modifie + 20;
    delete from concentrations where concentration_perso_cod = v_perso_cod;
  end if;

  -- Mini à 1%
  if v_comp_modifie < 1 then
      v_comp_modifie := 1;
  end if;

  code_retour := code_retour||'Votre chance de réussir (en tenant compte des modificateurs) est de <b>'||trim(to_char(v_comp_modifie,'9999'))||'</b> ';

  -- calcul d'une reussite spécial (20% de la comp avec bonus/malus)
  v_special := floor(v_comp_modifie/5);

  -- etape  on regarde si le persi est bénie ou maudit
  bonmal := valeur_bonus(v_perso_cod, 'BEN') + valeur_bonus(v_perso_cod, 'MAU');
  if bonmal <> 0 then
    des := lancer_des3(1,100,bonmal);
  else
    des := lancer_des(1,100);
  end if;
  code_retour := code_retour||'et votre lancer de dés est de <b>'||trim(to_char(des,'9999'))||'</b>.<br>';


  -- cas d'un echec critique ------------------------------------------------------
  if des > 96 then

      v_criticite := 0 ;
      px_gagne := 0 ;
      v_retour = 0;
      code_retour := code_retour||'Il s’agit donc d’un <b>échec critique</b>.<br><br>';

  -- cas d'un echec normal ------------------------------------------------------
  elsif des > v_comp_modifie then

      v_criticite := 1 ;
      px_gagne := 0 ;
      v_retour = 0;
      code_retour := code_retour||'Vous avez donc <b>échoué</b>.<br><br>';

  -- cas d'une réussite critique------------------------------------------------------
  elsif des <= 5 then

      v_criticite := 4 ;
      code_retour := code_retour||'il s’agit donc d’une <b>réussite critique</b>.<br><br>';
      px_gagne := px_gagne + 1;
      v_retour = 1;
      cout_pa := floor(cout_pa/2);

  -- cas d'une réussite speciale ------------------------------------------------------
  elsif des <= v_special then

      v_criticite := 3 ;
      code_retour := code_retour||'il s’agit donc d’une <b>réussite spéciale</b>.<br><br>';
      v_retour = 1;
      cout_pa := cout_pa - 1;

  -- cas d'une réussite standard ------------------------------------------------------
  else
      v_criticite := 2 ;
      code_retour := code_retour||'Vous avez donc <b>réussi</b>.<br><br>';
      v_retour = 1;

  end if;

  -- ------------------------------------------------------
  -- on regarde si on améliore la comp
  if (v_comp <= getparm_n(1) and des <= 96) or (v_retour = 1) then

      if (v_comp <= getparm_n(1) ) then
          code_retour := code_retour||'Votre compétence est inférieure à '||trim(to_char(getparm_n(1),'9999'))||' %. Vous tentez une amélioration.<br>';
      else
          code_retour := code_retour||'Vous tentez une amélioration.<br>';
      end if;
      temp_ameliore_competence := ameliore_competence_px(v_perso_cod,104,v_comp);
      code_retour := code_retour||'Votre lancer de dés est de <b>'||split_part(temp_ameliore_competence,';',1)||'</b>, ';
      if split_part(temp_ameliore_competence,';',2) = '1' then
        code_retour := code_retour||'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>'||split_part(temp_ameliore_competence,';',3)||'</b><br><br>';
      else
        code_retour := code_retour||'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
      end if;

  end if;

  -- ------------------------------------------------------
  -- Jet d'opposition pour le desarçonnage

  if v_action = 4 and v_retour = 1 then

      -- calcul de la compétence de base
      select pcomp_modificateur into v_comp_cible from perso_competences where pcomp_perso_cod = v_perso_cible and pcomp_pcomp_cod = 104;
      if not found then
          v_comp_cible := 30 ;    -- 30% de base mini pour chaque perso
      end if;

      -- ajout des bonus / malus
      v_comp_cible := GREATEST(1, v_comp_cible + bonus_equitation(v_perso_cible));

      -- on regarde si la cible est bénie ou maudite, on donne à la cible une bénédiction additionnelle de 30% pour ne pas rendre le désaçonnage trop facile
      bonmal := valeur_bonus(v_perso_cible, 'BEN') + valeur_bonus(v_perso_cible, 'MAU') - 20 ;
      if bonmal <> 0 then
        des_cible := lancer_des3(1,100,bonmal);
      else
        des_cible := lancer_des(1,100);
      end if;

      -- le jet de l'oposant à réussi: si sa compétence est reussie et s'il y a une plus grande difference de dé par rapport au seuil de reussite
      if (des_cible <= v_comp_cible) and (v_comp_cible - des_cible) > (v_comp_modifie - des) then
          code_retour := code_retour||v_perso_cible_nom || ' <b>a réussi son jet d’opposition</b>, vous n’arrivez pas à le désarçonner!</b><br>';
          v_retour := 0 ;
      else
          code_retour := code_retour||v_perso_cible_nom || ' <b>a raté son jet d’opposition</b>, vous arrivez à le désarçonner!</b><br>';
      end if;

  end if;

  -- ------------------------------------------------------
  -- Ajout des px, et retrait des PA
  update perso set perso_px = perso_px + px_gagne, perso_pa = perso_pa  - cout_pa where perso_cod = v_perso_cod;
  if px_gagne > 0 then
      code_retour := code_retour||'Vous gagnez '||trim(to_char(px_gagne,'99.99'))||' PX pour cette action.<br>';
  end if;

  -- ---------------------------
	return v_retour::text || ';' || v_criticite::text || ';' || code_retour;
end;$_$;


ALTER FUNCTION public.monture_competence(integer, integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION monture_competence(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION monture_competence(integer, integer, integer, integer) IS 'Faire une jé de réussite sous la compétence Equitation';
