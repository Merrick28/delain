--
-- Name: action_derniere_taverne(); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function action_derniere_taverne() RETURNS text
LANGUAGE plpgsql
AS $$declare
  ligne record;
  v_pos_cod integer;
  v_familier integer;
  v_count integer;
begin



  -----------------------------------------------------------------
  -- trouver les persos "inactif"
  -----------------------------------------------------------------
  -- rechercher des persos:
  --    * aventuriers qui ne sont pas des PNJ
  --    * ils ne sont pas déjà à la taverne (109)
  --    * ils ne sont pas sur un etage de proving ground (-100)
  --    * ils ne sont pas en hibernation
  --    * ils ont une DLT qui date de plus de 6 mois

  v_count := 0 ;
  for ligne in SELECT  perso_cod, 'X='||pos_x::text||' Y='|| pos_y::text||' '||etage_libelle as pos_desc
                FROM perso
                JOIN perso_position on ppos_perso_cod=perso_cod
                JOIN positions on pos_cod =ppos_pos_cod
                JOIN etage on etage_numero=pos_etage
                JOIN perso_compte on pcompt_perso_cod=perso_cod
                JOIN compte on compt_cod=pcompt_compt_cod
                WHERE etage_numero<>109 and perso_type_perso=1 and perso_pnj=0
                  AND etage_reference<>-100
                  AND compt_hibernation is null
                  AND perso_dlt<NOW() - '6 MONTH'::interval
                order by perso.perso_dlt
  loop
      -- trouver une place à la taverne
      SELECT pos_cod INTO v_pos_cod FROM positions WHERE pos_etage = 109 and pos_x in (4,-4) and pos_y in (4,-4) ORDER BY random() LIMIT 1 ;

      -- mermoriser quelques infos en donnant un titre au perso !
      INSERT INTO public.perso_titre( ptitre_perso_cod, ptitre_titre)
        VALUES (ligne.perso_cod, 'Envoyé à la dernière taverne le ' ||TO_CHAR(NOW()::date, 'dd/mm/yyyy')||' depuis '||ligne.pos_desc);

      -- deplacer le perso!
      DELETE FROM perso_arene WHERE parene_perso_cod = ligne.perso_cod ;
      DELETE FROM lock_combat WHERE lock_cible = ligne.perso_cod OR lock_attaquant = ligne.perso_cod  ;
	    DELETE FROM transaction	WHERE tran_vendeur = ligne.perso_cod OR tran_acheteur = ligne.perso_cod;
      UPDATE perso_position SET ppos_pos_cod = v_pos_cod WHERE ppos_perso_cod=ligne.perso_cod ;

       -- et son familier
      SELECT max(pfam_familier_cod) INTO v_familier FROM perso_familier INNER JOIN perso ON perso_cod=pfam_familier_cod WHERE perso_actif='O' and pfam_perso_cod =  ligne.perso_cod ;
      DELETE FROM lock_combat WHERE lock_cible = v_familier OR lock_attaquant = v_familier  ;
	    DELETE FROM transaction	WHERE tran_vendeur = v_familier OR tran_acheteur = v_familier ;
      UPDATE perso_position SET ppos_pos_cod = v_pos_cod WHERE ppos_perso_cod=v_familier ;

      v_count = v_count + 1 ;
  end loop;

  return  'Nombre d’aventuriers envoyés à la dernière taverne: '||v_count::text ;

end$$;


ALTER FUNCTION public.action_derniere_taverne() OWNER TO delain;

--
-- Name: FUNCTION action_generique(); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION action_derniere_taverne() IS 'Nettoyage des persos inactifs vers la dernière taverne (cron)';
