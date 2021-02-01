--
-- Name: aq_verif_perso_condition_etape(integer,integer,integer,integer); Type: FUNCTION; Schema: potions; Owner: postgres
--

CREATE or REPLACE FUNCTION quetes.aq_verif_perso_condition_etape(integer,integer,integer,integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function aq_verif_perso_condition_etape						   */
/* parametres :                                          */
/*  $1 = personnage                                      */
/*  $2 = l'étape contenant les conditions à vérifier     */
/*  $3 = le N° du paramètre de l'étape                   */
/*  $4 = la référence aqperso (ou 0 si non démarrée)     */
/* Sortie :                                              */
/*  code_retour = 1 si conditions vérifiées 0 sinon      */
/*********************************************************/
declare
  v_perso_cod alias for $1;	    -- perso_cod
  v_aqetape_cod alias for $2;	  --  etape
  v_param_id alias for $3;	    --  n° de param
  v_aqperso_cod alias for $4;	  --  quete du perso
  --
  v_nb_condition integer;				  -- nb de condition
  v_nb_verif integer;				      -- nb de condition vérifié
	ligne record;
  --

begin

  -- on vérifie d'abord les conditions du type ET ( aqelem_param_num_1=0)!  aqelem_misc_cod=> pas de conditions!
  v_nb_condition := 0;
  v_nb_verif := 0;
	for ligne in
    select aqelem_misc_cod, aqelem_param_num_2, aqelem_param_num_3, aqelem_param_txt_1, aqelem_param_txt_2, aqelem_param_txt_3, aqelem_quete_step
    from quetes.aquete_element
    where aqelem_type='perso_condition' and aqelem_misc_cod>0 and aqelem_param_num_1=0 and aqelem_aqetape_cod = v_aqetape_cod and aqelem_param_id=v_param_id and ((aqelem_aqperso_cod IS NULL and v_aqperso_cod=0) or aqelem_aqperso_cod=v_aqperso_cod)
    order by aqelem_param_ordre

  loop

    v_nb_condition := v_nb_condition + 1 ;
    v_nb_verif := v_nb_verif + quetes.aq_verif_perso_condition(v_perso_cod, ligne.aqelem_misc_cod, ligne.aqelem_param_txt_1, ligne.aqelem_param_txt_2, ligne.aqelem_param_txt_3) ;

  end loop;

  -- on doit vérifier toutes les condistions du type ET
  if v_nb_condition != v_nb_verif then
    return 0;
  end if;


  -- on vérifie ensuite les conditions du type OU ( aqelem_param_num_1=1)!
  v_nb_condition := 0;
  v_nb_verif := 0;
	for ligne in
    select aqelem_misc_cod, aqelem_param_num_2, aqelem_param_num_3, aqelem_param_txt_1, aqelem_param_txt_2, aqelem_param_txt_3, aqelem_quete_step
    from quetes.aquete_element
    where aqelem_type='perso_condition' and aqelem_misc_cod>0 and aqelem_param_num_1=1 and aqelem_aqetape_cod = v_aqetape_cod and aqelem_param_id=v_param_id and ((aqelem_aqperso_cod IS NULL and v_aqperso_cod=0) or aqelem_aqperso_cod=v_aqperso_cod)
    order by aqelem_param_ordre

  loop

    v_nb_condition := v_nb_condition + 1 ;
    v_nb_verif := v_nb_verif + quetes.aq_verif_perso_condition(v_perso_cod, ligne.aqelem_misc_cod, ligne.aqelem_param_txt_1, ligne.aqelem_param_txt_2, ligne.aqelem_param_txt_3) ;

  end loop;

  -- on doit vérifier au moins une conditions du type OU (s'il y en a)
  if v_nb_verif = 0 and v_nb_condition > 0 then
    return 0;
  end if;

  return 1;
end;
$_$;


ALTER FUNCTION quetes.aq_verif_perso_condition_etape(integer,integer,integer,integer) OWNER TO delain;