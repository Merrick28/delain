--
-- Name: f_modif_carac_limit(text, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_modif_carac_limit(text, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/*************************************************/
/* fonction f_modif_carac_limit                   */
/*-----------------------------------------------*/
/* paramètres :                                  */
/* $1 = type carac                               */
/*   possibles : FOR, DEX, INT et CON            */
/*   attention : majuscules !                    */
/* $2 : la base du perso                         */
/* $3 = la valeur souhaitée                      */
/*-----------------------------------------------*/
/* code retour : ineger                          */
/*  retourne la valeur en respectant les         */
/* contraintes                                   */
/*-----------------------------------------------*/
/* créé le 10/12/2019 par Marlyza                */
/*************************************************/
declare
	code_retour text;
	v_type_carac alias for $1;
	v_carac_base alias for $2;
  v_valeur alias for $3;

	v_limit_max integer;
  v_nouvelle_valeur integer ;

begin

  -- dans tous les cas, on doit avoir une limite de carac, impossible à dépasser (en min ou max)
  select into v_limit_max tbonus_degressivite from bonus_type where tbonus_libc = v_type_carac ;
	if not found then
		v_limit_max = 50 ;   -- limit max non trouvée, on applique le bonus de la formule d'origine qui était de 50% (en positif comme en negatif)
	end if;
	if v_limit_max<=0 or v_limit_max>100 then
		v_limit_max = 50 ;   -- si limit bizarre on est jamais trop prudent :-)
	end if;

  v_nouvelle_valeur := v_valeur;

  if v_nouvelle_valeur > (v_carac_base * (1 + (v_limit_max/100::numeric))) then
    v_nouvelle_valeur := floor(v_carac_base * (1 + (v_limit_max/100::numeric)));
  elsif v_nouvelle_valeur < (v_carac_base * (1 - (v_limit_max/100::numeric))) then
    v_nouvelle_valeur := ceil(v_carac_base * (1 - (v_limit_max/100::numeric))) ;
  end if;


	return v_nouvelle_valeur ;
end;$_$;


ALTER FUNCTION public.f_modif_carac_limit(text, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION f_modif_carac_limit(text, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

