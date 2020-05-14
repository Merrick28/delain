--
-- Name: execute_effet_auto_mag(integer, integer, integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION execute_effet_auto_mag(integer, integer, integer, text) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction execute_effet_auto_mag                                */
/*   Exécute les fonctions spécifiques liées à un monstre    */
/*    et/ou un personnage                                    */
/*   on passe en paramètres :                                */
/*   $1 = v_perso_cod : le perso_cod de la source            */
/*   $2 = protagoniste : le perso_cod de la cible/source     */
/*   $3 = sort_cod : le sort                                 */
/*   $4 = type_effet : L=lancé / C=ciblé                     */
/* on a en sortie les sorties concaténées des fonctions.     */
/*************************************************************/
/* Créé le 19/05/2014                                        */
/*************************************************************/
declare
  v_perso_cod    alias for $1;  -- Le code du perso en question
  v_protagoniste alias for $2;  -- le perso_cod de la cible si type "L" et du lanceur "C"
	v_sort_cod     alias for $3;  -- Le code du sort
	v_type         alias for $4;  -- type_effet : L=lancé / C=ciblé

	code_retour text;          -- Le retour de la fonction
	retour_fonction text;      -- Le résultat de l’exécution d’une fonction
	ligne_fonction record;     -- Les données de la fonction
	code_fonction text;        -- Le code SQL lançant la fonction
	v_gmon_cod integer;        -- Le code du monstre générique
	v_sort_aggressif text;     -- sort de agressif
	v_sort_soutien text;       -- sort de agressif

begin

	code_retour := '';    -- code de retour

  -- Rechercher les infos sur le sorts
  select sort_aggressif, sort_soutien into v_sort_aggressif, v_sort_soutien from sorts where sort_cod=v_sort_cod ;

  -- Eventuellement les fonctions du monstre générique
	select into v_gmon_cod perso_gmon_cod from perso inner join monstre_generique on gmon_cod=perso_gmon_cod where perso_cod = v_perso_cod;
	if not found then
      v_gmon_cod:= null ;
	end if;

  -- boucle sur toutes les fonctions specifiques de l'évenement pour le perso et le bonus qui vérifie le passage du seuil
	for ligne_fonction in (
      select fonc_cod, fonc_nom
      from fonction_specifique
      where ((fonc_gmon_cod = coalesce(v_gmon_cod, -1)) OR (fonc_perso_cod = v_perso_cod))
            and ((fonc_type='MAL' and v_type='L') or (fonc_type='MAC' and v_type='C'))
            and (
                (fonc_trigger_param->>'fonc_trig_type_benefique'::text = 'O' and v_sort_soutien = 'O')
              or
                (fonc_trigger_param->>'fonc_trig_type_agressif'::text = 'O' and v_sort_aggressif = 'O')
              or
                (fonc_trigger_param->>'fonc_trig_type_neutre'::text = 'O' and v_sort_soutien = 'N' and v_sort_aggressif = 'N')
                )
            and (
                (v_type!='L')
              or
                (v_type='L' and fonc_trigger_param->>'fonc_trig_effet'::text = '1' and v_protagoniste is null)
              or
                (v_type='L' and fonc_trigger_param->>'fonc_trig_effet'::text = 'N' and v_protagoniste is not null)
                )
            and (fonc_date_limite >= now() OR fonc_date_limite IS NULL)
      order by fonc_cod desc
		)
	loop

      code_fonction := ligne_fonction.fonc_nom;
      retour_fonction := execute_fonction_specifique(v_perso_cod, COALESCE(v_protagoniste,v_perso_cod), ligne_fonction.fonc_cod) ;

      if coalesce(retour_fonction, '') != '' then
        -- code_retour := code_retour || code_fonction || ' : ' || coalesce(retour_fonction, '') || '<br />';
        code_retour := code_retour || coalesce(retour_fonction, '') || '<br />';
      end if;
	end loop;

	if code_retour != '' then
		  code_retour := replace('<br /><b>Effets automatiques :</b><br />' || code_retour, '<br /><br />', '<br />') || '<br />';
	end if;

	return code_retour;
end;$_$;


ALTER FUNCTION public.execute_effet_auto_mag(integer, integer, integer, text) OWNER TO delain;

--
-- Name: FUNCTION execute_effet_auto_mag(integer, integer, integer, text); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION execute_effet_auto_mag(integer, integer, integer, text) IS 'Exécute les fonctions liées au changement de BM du perso_cod donné.';
