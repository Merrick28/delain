--
-- Name: verif_perso_condition(integer,json); Type: FUNCTION; Schema: potions; Owner: postgres
--

CREATE or REPLACE FUNCTION verif_perso_condition(integer,json) RETURNS integer
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function verif_perso_condition						   */
/* parametres :                                          */
/*  $1 = personnage                                      */
/*  $2 = les conditions à vérifier                       */
/* Sortie :                                              */
/*  code_retour = 1 si conditions vérifiées 0 sinon      */
/*********************************************************/
declare
  v_perso_cod alias for $1;	    -- perso_cod
  v_conditions alias for $2;	      --  list des conditions

  --
  v_nb_condition integer;				-- nb de condition
  v_nb_verif integer;				    -- nb de condition vérifié
  v_nb_element integer;				  -- nb d'élément à vérifier
  value json;				            -- nb d'élément à vérifier
  --
	ligne record;                  -- Les données d'une condition

begin
  -- S'il n'y a pas de condition, on considère que c'est vérifié!
  if v_conditions is null or coalesce(json_array_length(v_conditions), 0) = 0  then
    return 1;
  end if;


  -- on vérifie d'abord les conditions du type ET ( objelem_param_num_1=0)!
  v_nb_condition := 0;
  v_nb_verif := 0;
	for ligne in (  select v from (select  json_array_elements( v_conditions )  as v ) as s where  v->>'conj' = 'ET' and v->>'cond' > 0 )
	loop

    v_nb_condition := v_nb_condition + 1 ;
    v_nb_verif := v_nb_verif + quetes.aq_verif_perso_condition(v_perso_cod, f_to_numeric(ligne.v->>'cond')::integer, ligne.v->>'signe', ligne.v->>'val1', ligne.v->>'val2') ;

	end loop;

  -- on doit vérifier toutes les condistions du type ET
  if v_nb_condition != v_nb_verif then
    return 0;
  end if;


  -- on vérifie ensuite les conditions du type OU ( objelem_param_num_1=1)!
  v_nb_condition := 0;
  v_nb_verif := 0;
	for ligne in (  select v from (select  json_array_elements( v_conditions )  as v ) as s where  v->>'conj' = 'OU' and v->>'cond' > 0 )
	loop

    v_nb_condition := v_nb_condition + 1 ;
    v_nb_verif := v_nb_verif + quetes.aq_verif_perso_condition(v_perso_cod, f_to_numeric(ligne.v->>'cond')::integer, ligne.v->>'signe', ligne.v->>'val1', ligne.v->>'val2') ;

	end loop;

  -- on doit vérifier au moins une conditions du type OU (s'il y en a)
  if v_nb_verif = 0 and v_nb_condition > 0 then
    return 0;
  end if;

  return 1;
end;
$_$;


ALTER FUNCTION verif_perso_condition(integer,json) OWNER TO delain;