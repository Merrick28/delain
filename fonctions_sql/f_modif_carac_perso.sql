--
-- Name: f_modif_carac_perso(integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_modif_carac_perso(integer, text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/*************************************************/
/* fonction f_modif_carac_perso                   */
/*-----------------------------------------------*/
/* paramètres :                                  */
/* $1 = perso_cod                                */
/* $2 = type carac                               */
/*   possibles : FOR, DEX, INT et CON            */
/*   attention : majuscules !                    */
/*-----------------------------------------------*/
/* code retour : valeur ajouté à la carac        */
/*-----------------------------------------------*/
/* créé le 12/12/2019 par Marlyza                */
/*************************************************/
declare
	code_retour text;
	v_perso alias for $1;
	v_type_carac alias for $2;

  v_modificateur integer;
  v_carac_actuelle integer;
  v_nouvelle_valeur integer;
  v_carac_base integer;
  v_diff integer;

  v_pv integer;
	temp_tue text;
begin

  -- récupération de la carac actuelle du perso
	select into v_carac_actuelle
		case v_type_carac when 'FOR' then perso_for
		                  when 'DEX' then perso_dex
		                  when 'INT' then perso_int
		                  when 'CON' then perso_con
		else NULL end
	from perso where perso_cod = v_perso;
	if v_carac_actuelle is null then
		return 0 ;      -- pas de changement (erreur sur le type de carac, ce cas ne devrait pas se produire, la carac est vérifiée en amont)
	end if;

  -- on calcul ce qu'il y a comme bonus/malus ==> nouveau modificateur souhaité tous bonus/malus confondus
  -- NOTA: corig_nb_tours et corig_dfin  sont nuls tous les 2 pour les bonus d'équipements
  select into v_modificateur coalesce(sum(corig_valeur),0)
    from carac_orig
    where corig_perso_cod = v_perso and corig_type_carac = v_type_carac  and (corig_dfin >= now() or corig_nb_tours > 0 or (corig_nb_tours is null and corig_dfin is null)) ;

  -- carac de base du perso sans bonus/malus
	v_carac_base := f_carac_base(v_perso, v_type_carac) ;

  -- ATTENTION: la somme de bonus/malus ne doit pas dépasser un % de la carac de base (on vérifie avant de changer la valeur de la carac)
  v_nouvelle_valeur := f_modif_carac_limit(v_type_carac, v_carac_base, v_carac_base + v_modificateur);

  -- on regarde s'il y a du changement
  if v_nouvelle_valeur = v_carac_actuelle  then

    return 0;   -- Pas de changement !

  else

    -- modification réelle de la carac du perso !!!

    v_diff := v_nouvelle_valeur - v_carac_actuelle ;
    if v_type_carac = 'FOR' then
      update perso set perso_for = perso_for + v_diff, perso_enc_max = perso_enc_max + (v_diff * 3) where perso_cod = v_perso;

    elsif v_type_carac = 'DEX' then
      update perso set perso_dex = perso_dex + v_diff, perso_capa_repar = perso_capa_repar + (v_diff * 3) where perso_cod = v_perso;

    elsif v_type_carac = 'INT' then
      update perso set perso_int = perso_int + v_diff, perso_capa_repar = perso_capa_repar + (v_diff * 3) where perso_cod = v_perso;

    elsif v_type_carac = 'CON' then
      update perso set perso_con = perso_con + v_diff, perso_pv_max = perso_pv_max + (v_diff * 3), perso_pv = perso_pv + (v_diff * 3) where perso_cod = v_perso;

    end if;

    -- une baisse de constit peut tuer le perso !!!
    select into v_pv perso_pv  from perso  where perso_cod = v_perso;
    if v_pv <= 0 then
      temp_tue := 'Un malus de constitution a occasionné une perte de PV temporaires qui vous a été fatale.';
      perform insere_evenement(ligne.corig_perso_cod, ligne.corig_perso_cod, 10, temp_tue, 'N', NULL);
      temp_tue := tue_perso_final(ligne.corig_perso_cod, ligne.corig_perso_cod);
    end if;

  end if;

  -- retourner la différence réelle réalisée sur la carac
	return v_diff;

end;$_$;


ALTER FUNCTION public.f_modif_carac_perso(integer, text) OWNER TO delain;

--
-- Name: FUNCTION f_modif_carac_perso(integer, text); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_modif_carac_perso(integer, text) IS 'Modifie de façon temporaire une caractéristique primaire (CON, FOR, INT, DEX) du perso. $1 = perso_cod ; $2 IN (''CON'', ''FOR'', ''INT'', ''DEX'') ';

