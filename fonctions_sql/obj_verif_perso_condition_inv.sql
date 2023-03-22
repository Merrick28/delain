--
-- Name: obj_verif_perso_condition_inv(integer,integer); Type: FUNCTION; Schema: potions; Owner: postgres
--

CREATE or REPLACE FUNCTION obj_verif_perso_condition_inv(integer,integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function obj_verif_perso_condition						   */
/* parametres :                                          */
/*  $1 = personnage                                      */
/*  $2 = l'objet contenant les conditions à vérifier     */
/* Sortie :                                              */
/*  code_retour = 1 si conditions vérifiées 0 sinon      */
/* s'il n'y a pas de condtion spécifique -1 est retourné */
/* si le perso est un familier                           */
/*********************************************************/
declare
  v_perso_cod alias for $1;	    -- perso_cod
  v_obj_cod alias for $2;	      --  objet

  --
  v_type_perso integer;				  -- type de perso
  v_gobj_cod integer;				    -- code generique de l'objet
  v_nb_condition integer;				-- nb de condition
  v_nb_verif integer;				    -- nb de condition vérifié
  v_nb_element integer;				  -- nb d'élément à vérifier
	ligne record;
  --

begin
  -- on recupère le code gérérique de l'objet
  select obj_gobj_cod into v_gobj_cod from objets where obj_cod = v_obj_cod ;
  if not found then
    return 0;
  end if;

  -- regarder si le perso possède de l'objet et recupérer son type de perso au passage
  select perso_type_perso into v_type_perso from perso where perso_cod=v_perso_cod ;
  if not found then
    return 0;
  end if;

  -- s'il n'y a aucune condition pour un perso alors s'est ramassable par defaut !
  select count(*) into v_nb_element from objet_element where objelem_type='perso_condition' and objelem_misc_cod>0 and (objelem_obj_cod = v_obj_cod or objelem_gobj_cod=v_gobj_cod) and objelem_param_id=2 ;
  if v_nb_element=0 then
    return 1;
  end if;

  -- on vérifie d'abord les conditions du type ET ( objelem_param_num_1=0)!
  v_nb_condition := 0;
  v_nb_verif := 0;
	for ligne in
    select objelem_misc_cod, objelem_param_num_2, objelem_param_num_3, objelem_param_txt_1, objelem_param_txt_2, objelem_param_txt_3
    from objet_element
    where objelem_type='perso_condition' and objelem_misc_cod>0 and objelem_param_num_1=0 and (objelem_obj_cod = v_obj_cod or objelem_gobj_cod=v_gobj_cod) and objelem_param_id=2
    order by objelem_param_ordre

  loop

    v_nb_condition := v_nb_condition + 1 ;
    v_nb_verif := v_nb_verif + quetes.aq_verif_perso_condition(v_perso_cod, ligne.objelem_misc_cod, ligne.objelem_param_txt_1, ligne.objelem_param_txt_2, ligne.objelem_param_txt_3) ;

  end loop;

  -- on doit vérifier toutes les condistions du type ET
  if v_nb_condition != v_nb_verif then
    return 0;
  end if;


  -- on vérifie ensuite les conditions du type OU ( objelem_param_num_1=1)!
  v_nb_condition := 0;
  v_nb_verif := 0;
	for ligne in
    select objelem_misc_cod, objelem_param_num_2, objelem_param_num_3, objelem_param_txt_1, objelem_param_txt_2, objelem_param_txt_3
    from objet_element
    where objelem_type='perso_condition' and objelem_misc_cod>0 and objelem_param_num_1=1 and (objelem_obj_cod = v_obj_cod or objelem_gobj_cod=v_gobj_cod) and objelem_param_id=2
    order by objelem_param_ordre

  loop

    v_nb_condition := v_nb_condition + 1 ;
    v_nb_verif := v_nb_verif + quetes.aq_verif_perso_condition(v_perso_cod, ligne.objelem_misc_cod, ligne.objelem_param_txt_1, ligne.objelem_param_txt_2, ligne.objelem_param_txt_3) ;

  end loop;

  -- on doit vérifier au moins une conditions du type OU (s'il y en a)
  if v_nb_verif = 0 and v_nb_condition > 0 then
    return 0;
  end if;

  return 1;
end;
$_$;


ALTER FUNCTION obj_verif_perso_condition_inv(integer,integer) OWNER TO delain;
