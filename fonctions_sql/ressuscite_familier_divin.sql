-- Function: public.ressuscite_familier_divin(integer)

-- DROP FUNCTION public.ressuscite_familier_divin(integer);

CREATE OR REPLACE FUNCTION public.ressuscite_familier_divin(integer)
  RETURNS text AS
$BODY$/*****************************************************************/
/* function ressuscite_familier_divin :                          */
/*    Procédure utilisée pour résuciter un familier divin        */
/* On passe en paramètres                                        */
/*    $1 = perso_cod du maître                                   */
/*****************************************************************/
/* Créé le 01/06/2018 - Marlyza                                  */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables entrée/sortie
-------------------------------------------------------------
  maitre alias for $1;          -- le perso_cod du maître du familier
	texte_evt text;				-- texte pour évènements
  code_retour text;             -- le code retour : '{0|1};texte'. Avec 0 si OK, 1 si KO. texte = perso_cod du familier si OK, message d’erreur si KO

-------------------------------------------------------------
-- variables concernant la cible (maitre)
-------------------------------------------------------------
	cible alias for $1;			-- perso_cod de la cible (=maitre)
	nom_cible text;				  -- nom de la cible
  pos_actuelle integer;   -- position de la cible
  cible_etage integer;    -- etage de la cible

-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
  familier_perso_cod integer; -- perso_cod du familer à réssuciter
  familier_perso_nom text;    -- nom du familier résuccité
  taux_perte_xp numeric;      -- taux de perte d'xp à la résurrection (dépendant de la voie magique)
  px_perdus numeric;				    -- PX perdus par la resurrection
  v_imp_F integer;            -- nombre de tour d'impalpabilité lié à l'étage de résurrection

begin
  code_retour := '';


  select into
    pos_actuelle, cible_etage
    ppos_pos_cod, pos_etage
		from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on ppos_pos_cod = pos_cod
		where perso_cod = cible ;

	-- on regarde s’il n’y a pas déjà un familier, on ne peut en posséder qu'un seul
	select into familier_perso_cod
		pfam_familier_cod
	from perso_familier inner join perso on perso_cod = pfam_familier_cod
	where pfam_perso_cod = cible
		and perso_actif = 'O';
	if found then
		code_retour := '<p>Vous êtes déjà en charge d’un autre familier, deux seraient trop à gérer.</p>';
		return '1;' || code_retour;
	end if;

  -- la perte d'xp dépend de la voie magique pour la magie runique, elle est fixe pour les divinité
  taux_perte_xp := 0.25 ;        -- perte de 25% des PX  actuels

  -- récupération du délai d'impalpabilité lié à l'étage
  select into
    v_imp_F
    etage_duree_imp_f
  from etage where etage_numero = cible_etage;

   ---- trouver le dernier fam divin  à recussiter (il doit avoir encore de la même foi que le maitre)
   select into
    familier_perso_cod, familier_perso_nom, px_perdus
    perso_cod, perso_nom, taux_perte_xp * perso_px  FROM perso_familier
    INNER JOIN perso on perso_cod=pfam_familier_cod
    INNER JOIN (
      SELECT MAX(fam.perso_dcreat) perso_dcreat
      FROM perso_familier
      INNER JOIN perso fam on fam.perso_cod=pfam_familier_cod
      INNER JOIN dieu_perso dieu_fam on dieu_fam.dper_perso_cod=pfam_familier_cod
      INNER JOIN perso maitre on maitre.perso_cod=pfam_perso_cod
      INNER JOIN dieu_perso dieu_maitre on dieu_maitre.dper_perso_cod=pfam_perso_cod
      WHERE pfam_perso_cod = cible AND fam.perso_actif='N' AND fam.perso_gmon_cod=441 AND dieu_maitre.dper_dieu_cod=dieu_fam.dper_dieu_cod
    ) dernier_familier ON dernier_familier.perso_dcreat = perso.perso_dcreat
    WHERE pfam_perso_cod = cible;
  if not found then
		code_retour := '<p>la résurection n''est pas possible, l''ame de votre familier n''a pas été retrouvée.</p>';
		return '1;' || code_retour;
  end if;

  ---- Recussiter le familier, avec malus de PX, et impalpabilité
 update perso set
  perso_actif = 'O',
  perso_px = perso_px - px_perdus ,
  perso_tangible='N',
  perso_nb_tour_intangible = v_imp_F
  where perso_cod = familier_perso_cod ;

 -- le positionner à coté de son maitre
 update perso_position set ppos_pos_cod = pos_actuelle where ppos_perso_cod = familier_perso_cod ;

 -- pour un familier divin qui aurait été tué par manque de foi, on remet sa barre à 20%
  update dieu_perso dieu_fam set dper_points=40 where dper_perso_cod=familier_perso_cod and dper_points<40;

  code_retour := code_retour||'<br>Votre dieu vous exauce et ramène <b>' || familier_perso_nom || '</b> du plan des morts.<br>';

	texte_evt := 'La résurrection depuis le plan des morts, fait perdre ' || trim(to_char(px_perdus, '9999999')) || ' px à [cible].';
	insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
		values(nextval('seq_levt_cod'), 10, now(), 1, familier_perso_cod, texte_evt, 'N', 'N', maitre, familier_perso_cod);

  texte_evt := '[cible] a été ramené par son dieu du plan des morts.';
  insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'), 54, now(), 1, familier_perso_cod, texte_evt, 'N', 'O', familier_perso_cod, familier_perso_cod);

  return '0;' || code_retour::text;
end;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.ressuscite_familier_divin(integer)
  OWNER TO delain;
COMMENT ON FUNCTION public.ressuscite_familier_divin(integer) IS 'Réssucscite un familier divin, sauf si celui si est mort à cause de son manque de foi.';
