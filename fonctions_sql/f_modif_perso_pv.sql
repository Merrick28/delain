--
-- Name: f_modif_perso_pv(integer, text, text, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function f_modif_perso_pv(integer, text, text default null, integer default null) RETURNS text
LANGUAGE plpgsql
AS $_$declare
  v_perso alias for $1;
  v_pv_roliste alias for $2;             -- au fromat dé rollist (soin si positif)
  txt_evt alias for $3;        -- test de l'event, si null pas d'event
  v_attaquant alias for $4;     -- pour l'event, si null, c'est le perso qui est l'attaquant

  v_pv integer;             -- nombre  de PV perdu (ou gagné si positif)
  v_perso_pv integer ;     -- nombre de PV actuel du perso

  code_retour text;
  temp_tue text ;     -- text en cas de perte mortelle mortelle
begin
	code_retour := '';

    select perso_pv into v_perso_pv from perso where perso_cod=v_perso ;
    if not found then
        return '<p>Erreur ! Le perso n''a pas été trouvé !';
    end if;

    v_pv:= COALESCE(f_lit_des_roliste(v_pv_roliste),0)::integer;
    if v_pv = 0 then
        return code_retour;
    end if;

    -- modifier les PV du perso (en s'assurant qu'ils restent entre 0 et le maximum)
    update perso set perso_pv = GREATEST(0, LEAST(perso_pv_max, perso_pv + v_pv))  where perso_cod = v_perso;

    if v_pv > 0 then
        code_retour := code_retour || 'Vous gagnez <b>' || trim(to_char(v_pv,'99999')) || '</b> points de vie.<br> ';
    else
        code_retour := code_retour || 'Vous perdez <b>' || trim(to_char(v_pv,'99999')) || '</b> points de vie.<br> ';
    end if;

    -- si le perso est mort, on appelle la fonction tue_perso_final pour gérer la mort
    if v_perso_pv + v_pv <= 0 then
        temp_tue := tue_perso_final(v_perso, COALESCE(v_attaquant, v_perso) );
        code_retour := code_retour || 'Vous êtes <b>mort !</b><br><br>';
    end if;

    if txt_evt is not null and txt_evt != '' then
        perform insere_evenement(COALESCE(v_attaquant, v_perso), v_perso, 54, txt_evt, 'O', NULL);
    end if;

    return code_retour ;
end;
$_$;


ALTER FUNCTION public.f_modif_perso_pv(integer, text, text, integer) OWNER TO delain;