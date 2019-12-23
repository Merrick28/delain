--
-- Name: f_carac_base(integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_carac_base(integer, text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/*************************************************/
/* fonction f_carac_base                   */
/*-----------------------------------------------*/
/* paramètres :                                  */
/* $1 = le perso                                 */
/* $2 = type carac                               */
/*   possibles : FOR, DEX, INT et CON            */
/*   attention : majuscules !                    */
/*-----------------------------------------------*/
/* code retour : integer                         */
/* retourne la valeur de la carac de base du     */
/* perso (ie sans bonus)                         */
/*-----------------------------------------------*/
/* créé le 11/12/2019 par Marlyza                */
/*************************************************/
declare
	code_retour text;
	v_perso alias for $1;
	v_type_carac alias for $2;

	v_carac_base integer;

begin

  select into v_carac_base corig_carac_valeur_orig from carac_orig where corig_type_carac = v_type_carac and corig_perso_cod = v_perso limit 1;
  if not found then
    		select into v_carac_base
            case v_type_carac
              when 'FOR' then perso_for
              when 'DEX' then perso_dex
              when 'INT' then perso_int
              when 'CON' then perso_con
          else NULL end
		    from perso where perso_cod = v_perso ;
  end if;

	return v_carac_base ;
end;$_$;


ALTER FUNCTION public.f_carac_base(integer, text) OWNER TO delain;

--
-- Name: FUNCTION f_carac_base(integer, text); Type: COMMENT; Schema: public; Owner: delain
--

