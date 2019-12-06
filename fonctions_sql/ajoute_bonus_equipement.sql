--
-- Name: ajoute_bonus_equipement(integer, text, integer, numeric); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ajoute_bonus_equipement(integer, text, integer, numeric) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Rajoute un bonus d'équipement à un perso
-- $1 = Le code du perso en question
-- $2 = Le type de bonus
-- $3 = le code de l'objet qui donne ce bonus
-- $4 = La valeur du bonus

declare
  v_perso alias for $1;
  v_type alias for $2;
  v_obj_cod alias for $3;
  v_valeur alias for $4;

	temp integer;	-- variable fourre tout
  code_retour text;
begin

  -- 08/11/2019 - Marlyza - Ajout de bonus de carac (FOR/INT/DEX/CON) à l'aide de f_modif_carac
  if v_type in ('DEX', 'INT', 'FOR', 'CON')  then

    -- la fontion ajoute_bonus_equipement() retourne un texte, mais pas de code d'erreur, on va ignorer le resultat de f_modif_carac_base()
	  perform f_modif_carac_base(v_perso, v_type, 'T', v_obj_cod, v_valeur::integer, 'E')	;

  else

    -- cas des bonus standards en mode équipement !--
    insert into bonus (bonus_perso_cod, bonus_tbonus_libc, bonus_valeur, bonus_mode, bonus_obj_cod)  values (v_perso, v_type, v_valeur, 'E', v_obj_cod);

  end if;

  return 1;
end;$_$;


ALTER FUNCTION public.ajoute_bonus_equipement(integer, text, integer, numeric) OWNER TO delain;
