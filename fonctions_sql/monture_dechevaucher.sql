--
-- Name: monture_dechevaucher(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function monture_dechevaucher(integer) RETURNS text
LANGUAGE plpgsql
AS $_$declare
  v_perso alias for $1;
  code_retour text;
  v_perso_pa integer;
  v_monture integer;
  v_monture_nom text;
  temp_competence text;   -- text du jet de compétence
  des integer ;     -- jet de dé (incident facheux)
  v_pv integer ;     -- nombre de PV perdu par la chute
  v_perso_pv integer ;     -- nombre de PV
  v_perso_con integer ;     -- constit du perso
  v_monture_taille integer ;     -- taille de la monture
  temp_tue text ;     -- text en cas de chute mortelle
begin
	code_retour := '';

  select perso_monture, perso_pa into v_monture, v_perso_pa from perso where perso_cod=v_perso ;
  if not found then
    return '<p>Erreur ! Le perso n''a pas été trouvé !';
  end if;

  if v_perso_pa < 4 then
    return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
  end if;

  select perso_nom into v_monture_nom from perso where perso_cod=v_monture ;
  if not found then
    return '<p>Erreur ! la monture n''a pas été trouvée !';
  end if;


  -- Test de compétence équitation (difficulté 0) => gère le la consommation de PA
  /* update perso set perso_pa = perso_pa  - 4 where perso_cod = v_perso; fait par le test de compétence */
   temp_competence := monture_competence(v_perso, 1, v_monture, 0);
  code_retour := code_retour||split_part(temp_competence,';',3);

  -- Réaliser les actions du dé-chevauchement (ou la chute, le résultat est le même :-) !!!
  update perso set perso_monture=null where perso_cod=v_perso ;

  /* -- Test sur le jet de compétence */
  if split_part(temp_competence,';',1) = '1' then
      code_retour := code_retour || '<p>Désormais, vous ne chevauchez plus: ' || v_monture_nom || ' !<br>';

      -- evenement déchevaucher (106)
      perform insere_evenement(v_perso, v_monture, 106, '[attaquant] est descendu de sa monture [cible].', 'O', NULL);

   else
      -- si echec du jet de compétence
      code_retour := code_retour||'<br><p>Vous n’avez pas réussi à descendre de ' || v_monture_nom || ', mais vous êtes tombé comme une m... heu comme un sac !<br>';


      -- evenement déchevaucher (106)
      perform insere_evenement(v_perso, v_monture, 106, '[attaquant] est tombé de sa monture [cible].', 'O', NULL);

      select into v_perso_con, v_perso_pv perso_con, perso_pv from perso where perso_cod = v_perso;
      select into v_monture_taille perso_taille from perso where perso_cod = v_monture;

      -- la perte de PV depend de la constit du perso (plus il est gros, plus il morfle) et de la taille de sa monture est grande plus il morfle)
      v_pv = (v_perso_con * v_monture_taille) / 2 ;

      -- incident facheux sur mauvaise chute!
      des := lancer_des(1,100);

      -- partie comunne des dégats de la chute (sauf succes critique)
      if des>5 then
          update perso set perso_pv = perso_pv - v_pv  where perso_cod = v_perso;
          code_retour := code_retour || 'Vous perdez <b>' || trim(to_char(v_pv,'99999')) || '</b> points de vie.<br> ';

          if v_pv >= v_perso_pv then
                temp_tue := tue_perso_final(v_perso,v_perso);
                code_retour := code_retour || 'Vous êtes <b>mort en tombant de votre monture !</b><br><br>';
          end if;
      end if;

      if des>65 then     -- malus de mouvement
          perform ajoute_bonus(v_perso, 'DEP', 4, 1);
          code_retour := code_retour || E'En tombant, vous vous êtes foulé la cheville, vous allez boiter pendant un bon moment...';

      elsif des>45 then  -- Baisse de l'esquive
          perform ajoute_bonus(v_perso, 'PAA', 2, 2);
          code_retour :=  code_retour || E'En tombant, vous vous êtes blessé la main, vous avez maintenant des dificultés à tenir votre arme....';

      elsif des>35 then   -- désorientation
          perform ajoute_bonus(v_perso, 'DES', 2, 50);
          code_retour :=  code_retour || E'La chute vous a completement désorienté...';

      elsif des>25 then   -- perte d'armure
          perform ajoute_bonus(v_perso, 'FRA', 2, 2);
          code_retour :=  code_retour || E'La chute a fragilisé votre armure...';

      elsif des>15 then   -- Baisse de l'esquive
          perform ajoute_bonus(v_perso, 'ESQ', 2, -20);
          code_retour :=  code_retour || E'Vous êtes étourdis par la chute...';

      elsif des>5 then    -- BERNARDO
          perform ajoute_bonus(v_perso, 'BER', 3, 1);
          code_retour :=  code_retour || E'Vous avez souffle coupé par la honte, vous ne pouvez plus articuler une seule parole !';

      else  -- succes critique sur un accident critique :-) !
          code_retour :=  code_retour || E'Vous êtes tombé comme un gros sac, mais par chance le sol était mou à cet endroit !';

      end if;

  end if;


  return code_retour ;
end;
$_$;


ALTER FUNCTION public.monture_dechevaucher(integer) OWNER TO delain;