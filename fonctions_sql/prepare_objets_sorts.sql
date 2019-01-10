--
-- Name: prepare_objets_sorts(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or REPLACE FUNCTION prepare_objets_sorts(integer, integer, integer) RETURNS boolean
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* function prepare_objets_sorts : prépare la                    */
/*          table objets_sorts_magie                             */
/*          pour le lancement d'un sort avec un objet ensorcelé  */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = objsort_cod                                           */
/*    $3 = sort_cod                                              */
/* retourne vrai ou faux en cas d'erreur;                        */
/*****************************************************************/
/* Créé le 08/01/2019 PAr marlyza                                */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
  code_retour boolean;				-- code retour
  v_perso_cod alias for $1;	-- perso passé en param
  v_objsort_cod alias for $2;			-- objsort_cod passé en param
  v_sort_cod alias for $3;			-- sort_cod passé en param
  v_count integer;			-- compteur

begin
  code_retour := true; -- par défaut, tout est OK

  /********************************************************************/
  /* Etape 1 : nettoyage d'un eventuel sort qui se serait mal terminé */
  /********************************************************************/
  delete from objets_sorts_magie where objsortm_perso_cod=v_perso_cod;

  /********************************************************************/
  /* Etape 2 : on vérifie que le sort lancé est bien celui de l'objet */
  /********************************************************************/
  select into v_count count(*) from objets_sorts where objsort_cod = v_objsort_cod and objsort_sort_cod=v_sort_cod;
  if not found then
    code_retour := false;
    return code_retour;
  end if;

  /********************************************************************/
  /* Etape 3 : on prépare la table*/
  /********************************************************************/
  insert into objets_sorts_magie(objsortm_perso_cod, objsortm_objsort_cod) VALUES (v_perso_cod, v_objsort_cod);

  return code_retour;
end;
$_$;

ALTER FUNCTION public.prepare_objets_sorts(integer, integer, integer) OWNER TO delain;