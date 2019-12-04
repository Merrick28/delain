--
-- Name: f_modif_carac_base(integer, text, text, integer, integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_modif_carac_base(integer, text, text, integer, integer, text) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************/
/* fonction f_modif_carac_base                   */
/*-----------------------------------------------*/
/* paramètres :                                  */
/* $1 = perso_cod                                */
/* $2 = type carac                               */
/*   possibles : FOR, DEX, INT et CON            */
/*   attention : majuscules !                    */
/* $3 = H (pour Heure) ou 'T' (pour tour)        */
/* $4 = nb d’heure/de tour/ ou code obj si Equip.*/
/* $5 = modificateur à mettre                    */
/* $6 = S/C/E si bonus Standard/cumulatif/Equip. */
/*-----------------------------------------------*/
/* code retour : texte                           */
/*  si tout bon, on sort 'OK'                    */
/*  sinon, message d’erreur complet              */
/*-----------------------------------------------*/
/* créé le 19/10/2006 par Merrick                */
/*************************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_type_carac alias for $2;
	v_type_delai alias for $3;
	v_temps alias for $4;
	v_modificateur alias for $5;
	v_cumulatif alias for $6;

	temp integer;	-- variable fourre tout

	v_corig_cod integer;
	v_corig_valeur integer;
	v_limit_max integer;
	v_carac_actuelle integer;
	v_carac_base integer;
	v_diff integer;
	v_nouvelle_valeur integer;
	v_temps_inter interval;
	v_pv integer;
	temp_tue text;
	v_obj_cod integer;    -- code de l'objet en cas de bonus d'équipement

begin
	code_retour := 'OK';
	v_obj_cod := null ;   -- par defaut (pas d'objet lié pour les bonus Standard ou cumulatif)
  v_temps_inter := null; -- par defaut

	--
	-- on fait d’abord les contrôles possibles
	--
	select into temp perso_cod from perso where perso_cod = personnage;
	if not found then
		return 'Personnage non trouvé !';
	end if;
	if v_temps = 0 and v_cumulatif != 'E' then      -- sauf equipement
		return 'Paramètre de durée non valide !';
  elsif v_cumulatif != 'E' then
	  v_temps_inter := trim(to_char(v_temps,'999999999'))||' hours';
  else
    -- cas d'équiepemtn, il n'y a pas de nombre de tour, la fin du bonus est conditionné par l'equipement
    v_obj_cod := v_temps ;
    v_temps := null ;
	end if;

	select into v_carac_actuelle
		case v_type_carac when 'FOR' then perso_for
		                  when 'DEX' then perso_dex
		                  when 'INT' then perso_int
		                  when 'CON' then perso_con
		else NULL end
	from perso where perso_cod = personnage;
	if v_carac_actuelle is null then
		return 'Type de caractéristique non valide !';
	end if;

  -- récupérer le valeur de base (tous bonus/malus confondus) on en prend 1, tous le bonus du mmême type contienne la même corig_carac_valeur_orig
  select into v_carac_base corig_carac_valeur_orig from carac_orig where corig_perso_cod = personnage and corig_type_carac = v_type_carac LIMIT 1;
  if not found then
    v_carac_base := v_carac_actuelle ; -- aucun bonus/malus la carac de base c'est la carac actuel du perso
  end if;

  -- dans tous les cas, on doit avoir une limite de carac, impossible à dépasser
  select into v_limit_max tbonus_degressivite from bonus_type where tbonus_libc = v_type_carac ;
	if not found then
		v_limit_max = 50 ;   -- limit max non trouvée, on applique le bonus de la formule d'origine qui était de 50% (en positif comme en negatif)
	end if;
	if v_limit_max<=0 or v_limit_max>=100 then
		v_limit_max = 50 ;   -- si limit bizarre on est jamais trop prudent :-)
	end if;


  --
  -- on regarde s’il y a déjà quelque chose (seulement pour le cas Standard), Equipement et Cumulatif c'est toujours unnouveua bonus/malus!
  --
  select into v_corig_cod, v_corig_valeur corig_cod, corig_valeur from carac_orig where corig_perso_cod = personnage and corig_type_carac = v_type_carac and corig_mode ='S' and v_cumulatif = 'S';
  if found then
    -- update du bonus
    v_diff := v_modificateur - v_corig_valeur ;  -- la différence c'est la valeur que l'on voulait ajouter moins ce qu'il y avait déjà

    if v_type_delai = 'H' then
      update carac_orig set corig_dfin = now() + v_temps_inter, corig_nb_tours = null, corig_valeur = v_modificateur  where corig_cod = v_corig_cod;
    else
      update carac_orig set corig_dfin = null, corig_nb_tours=v_temps, corig_valeur = v_modificateur  where corig_cod = v_corig_cod;
    end if;

  else
    -- insertion du nouveau bonus
    v_diff := v_modificateur ;

    if v_type_delai = 'H' then
      insert into carac_orig(corig_perso_cod, corig_type_carac, corig_carac_valeur_orig, corig_dfin, corig_valeur, corig_mode, corig_obj_cod)
      values (personnage, v_type_carac, v_carac_base, now() + v_temps_inter, v_modificateur, v_cumulatif, v_obj_cod);
    else
      insert into carac_orig(corig_perso_cod, corig_type_carac, corig_carac_valeur_orig, corig_nb_tours, corig_valeur, corig_mode, corig_obj_cod)
      values (personnage, v_type_carac, v_carac_base, v_temps, v_modificateur, v_cumulatif, v_obj_cod);
    end if;

  end if;

  -- le bonus a été ajouté (ou mis à jour), il faut maintenant mettre la carac du perso en conformitée
  -- ATTENTION: la somme de bonus ne doit pas dépasser un % de la carac de base (on vérifie avant de changer la carac)
  v_nouvelle_valeur := v_carac_actuelle + v_modificateur;

  if v_nouvelle_valeur > (v_carac_base * (1 + (v_limit_max/100::numeric))) then
    v_nouvelle_valeur := floor(v_carac_base * (1 + (v_limit_max/100::numeric)));
  elsif v_nouvelle_valeur < (v_carac_base * (1 - (v_limit_max/100::numeric))) then
    v_nouvelle_valeur := ceil(v_carac_base * (1 - (v_limit_max/100::numeric))) ;
  end if;

  if v_nouvelle_valeur <> v_carac_actuelle  then
    v_diff := v_nouvelle_valeur - v_carac_actuelle ;

    if v_type_carac = 'FOR' then
      update perso set perso_for = perso_for + v_diff, perso_enc_max = perso_enc_max + (v_diff * 3) where perso_cod = personnage;

    elsif v_type_carac = 'DEX' then
      update perso set perso_dex = perso_dex + v_diff where perso_cod = personnage;

    elsif v_type_carac = 'INT' then
      update perso set perso_int = perso_int + v_diff where perso_cod = personnage;

    elsif v_type_carac = 'CON' then
      update perso set perso_con = perso_con + v_diff, perso_pv_max = perso_pv_max + (v_diff * 3), perso_pv = perso_pv + (v_diff * 3) where perso_cod = personnage;

    end if;

    select into v_pv perso_pv  from perso  where perso_cod = personnage;
    if v_pv <= 0 then
      temp_tue := 'Un malus de constitution a occasionné une perte de PV temporaires qui vous a été fatale.';
      perform insere_evenement(ligne.corig_perso_cod, ligne.corig_perso_cod, 10, temp_tue, 'N', NULL);
      temp_tue := tue_perso_final(ligne.corig_perso_cod, ligne.corig_perso_cod);
    end if;

  end if;


	return code_retour;
end;$_$;


ALTER FUNCTION public.f_modif_carac_base(integer, text, text, integer, integer, text) OWNER TO delain;

--
-- Name: FUNCTION f_modif_carac_base(integer, text, text, integer, integer, text); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_modif_carac_base(integer, text, text, integer, integer, text) IS 'Modifie de façon temporaire une caractéristique primaire (CON, FOR, INT, DEX)
$1 = perso_cod ; $2 IN (''CON'', ''FOR'', ''INT'', ''DEX'') ; $3 = H ou T ; $4 = durée en heures ; $5 = valeur du bonus / malus. ; $6 = S/C/E (Standard, Cumulatif ou Equipement)';

