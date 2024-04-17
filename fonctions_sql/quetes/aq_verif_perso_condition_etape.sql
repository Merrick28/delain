--
-- Name: aq_verif_perso_condition_etape(integer,integer,integer,integer); Type: FUNCTION; Schema: potions; Owner: postgres
--

CREATE or REPLACE FUNCTION quetes.aq_verif_perso_condition_etape(integer,integer,integer,integer, integer default null) RETURNS integer
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function aq_verif_perso_condition_etape						   */
/* parametres :                                          */
/*  $1 = personnage                                      */
/*  $2 = l'étape contenant les conditions à vérifier     */
/*  $3 = le N° du paramètre de l'étape                   */
/*  $4 = la référence aqperso (ou 0 si non démarrée)     */
/*  $5 = N° ordre du premier element (si cond. multiple) */
/* Sortie :                                              */
/*  code_retour = 1 si conditions vérifiées 0 sinon      */
/*********************************************************/
declare
  v_perso_cod alias for $1;	    -- perso_cod
  v_aqetape_cod alias for $2;	  --  etape
  v_param_id alias for $3;	    --  n° de param
  v_aqperso_cod alias for $4;	  --  quete du perso
  v_param_ordre_min alias for $5;	  --  N° d'ordre de début, on va vérifier jusqu'a un changement de groupe de condition (donné par num2)
  --
  v_type_perso integer;				  -- type de perso
  v_nb_element integer;				  -- nb d'élément à vérifier
  v_nb_condition integer;				-- nb de condition
  v_nb_verif integer;				    -- nb de condition vérifié
  v_param_ordre_max integer;    -- n° d'ordre de la dernire condition à vérifier
	ligne record;
  --

begin

  -- recupérer le type de perso au passage
  select perso_type_perso into v_type_perso from perso where perso_cod=v_perso_cod ;
  if not found then
    return 0;
  end if;

  -- les familliers n'ont pas le droit de démarrer une QA (cas de l'étape 0 et où v_aqperso_cod=0) sauf, s'il y a une condiftion specifique sur le type de perso
  -- il doit y avoir une condition en "ET/OU" ( aqelem_param_num_1=0 ou 1) sur le code 17 = Type de perso (et il doivent la vérifier)
  if v_type_perso = 3 and v_aqperso_cod = 0 and v_param_id = 0 then
      select count(*) into v_nb_element
      from quetes.aquete_element
      where aqelem_type='perso_condition' and aqelem_misc_cod=17 and aqelem_aqetape_cod = v_aqetape_cod and aqelem_param_id=0 and aqelem_aqperso_cod IS NULL ;
      if v_nb_element=0 then
         return 0;
      end if;
  end if;

  -- capture de ordre max en cas de condtion sur liste avec une condition d'ordre min de défini
  v_param_ordre_max := null ;
  if v_param_ordre_min is not null then
      select min(aqelem_param_ordre) into v_param_ordre_max
          from quetes.aquete_element
          where (aqelem_type='perso_condition' or aqelem_type='perso_condition_liste') and aqelem_misc_cod>0 and aqelem_aqetape_cod = v_aqetape_cod and aqelem_param_id=v_param_id and ((aqelem_aqperso_cod IS NULL and v_aqperso_cod=0) or aqelem_aqperso_cod=v_aqperso_cod)
                      and aqelem_param_num_2=1 and aqelem_param_ordre>v_param_ordre_min ;
  end if;


  -- on vérifie d'abord les conditions du type ET ( aqelem_param_num_1=0)!  aqelem_misc_cod=> pas de conditions!
  v_nb_condition := 0;
  v_nb_verif := 0;
	for ligne in
      select aqelem_misc_cod, aqelem_param_num_2, aqelem_param_num_3, aqelem_param_txt_1, aqelem_param_txt_2, aqelem_param_txt_3, aqelem_quete_step
      from quetes.aquete_element
      where (aqelem_type='perso_condition' or aqelem_type='perso_condition_liste') and aqelem_misc_cod>0
                and aqelem_param_num_1=0 and aqelem_aqetape_cod = v_aqetape_cod and aqelem_param_id=v_param_id
                and ((aqelem_aqperso_cod IS NULL and v_aqperso_cod=0) or aqelem_aqperso_cod=v_aqperso_cod)
                and (v_param_ordre_min is null or aqelem_param_ordre>=v_param_ordre_min)
                and (v_param_ordre_max is null or aqelem_param_ordre<v_param_ordre_max)
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
      where (aqelem_type='perso_condition' or aqelem_type='perso_condition_list') and aqelem_misc_cod>0
                and aqelem_param_num_1=1 and aqelem_aqetape_cod = v_aqetape_cod and aqelem_param_id=v_param_id
                and ((aqelem_aqperso_cod IS NULL and v_aqperso_cod=0) or aqelem_aqperso_cod=v_aqperso_cod)
                and (v_param_ordre_min is null or aqelem_param_ordre>=v_param_ordre_min)
                and (v_param_ordre_max is null or aqelem_param_ordre<v_param_ordre_max)
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


ALTER FUNCTION quetes.aq_verif_perso_condition_etape(integer,integer,integer,integer,integer) OWNER TO delain;
