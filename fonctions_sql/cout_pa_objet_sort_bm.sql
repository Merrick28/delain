--
-- Name: cout_pa_objet_sort_bm(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION cout_pa_objet_sort_bm(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function cout_pa_objet_sort_bm : calcul le cout en pa pour les sorts  */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = numéro du sort objet                                   */
/* Le code sortie est une valeur html du cout en pa              */
/*****************************************************************/
/* Créé le 01/03/2019  - Marlyza                                 */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
-------------------------------------------------------------
-- variables concernant le lanceur
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur

-------------------------------------------------------------
-- variables concernant le sort d'objet
-------------------------------------------------------------
	v_objsortbm_cod alias for $2;		-- numéro du sort à lancer
	cout_pa integer;		-- Cout en PA du sort
	temp text;			-- passage de la valeur en txt



begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';

-- le coût pour les ojets magiques dépend de l'objet.


  select into cout_pa objsortbm_cout
    from objets_sorts_bm
    join perso_objets on perobj_obj_cod=objsortbm_obj_cod
    where perobj_perso_cod = lanceur and objsortbm_cod=v_objsortbm_cod
      and perobj_identifie = 'O'
      and (perobj_equipe='O' or objsortbm_equip_requis=false)
      and (objsortbm_nb_utilisation_max>objsortbm_nb_utilisation or COALESCE(objsortbm_nb_utilisation_max,0) = 0) ;
  if not found then
    cout_pa := 20 ;     -- il y a une anomalie, on met un nombre de PA impossible, le sort ne sera pas lancé
  end if;

  temp := cout_pa;
  code_retour := code_retour||temp;
	return code_retour;
end;
$_$;


ALTER FUNCTION public.cout_pa_objet_sort_bm(integer, integer) OWNER TO delain;