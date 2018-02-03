CREATE OR REPLACE FUNCTION public.attribue_monstre_4e_perso(integer)
  RETURNS integer AS
$BODY$/**************************************************************/
/* function attribue_monstre_4e_perso                         */
/* Sélectionne un monstre, et l’attribue à un compte en guise */
/* de 4e perso.                                               */
/* parametres :                                               */
/*  $1 = compt_cod à qui donner le monstre                    */
/* Sortie :                                                   */
/*  code_retour = perso_cod du monstre choisi                 */
/**************************************************************/
/**************************************************************/
/* Création - 24/09/2012 - Reivax                             */
/**************************************************************/
declare
  num_compte alias for $1;       -- compt_cod
  v_autorise boolean;            -- autorisation d’avoir 4 persos
  v_possede boolean;             -- possession d’un 4e
  v_type_quatrieme smallint;     -- Type choisi pour le 4e perso
  niveau_max integer;            -- niveau maximal autorisé
  code_ancien_monstre integer;   -- perso_cod du 4e perso actuel
  v_etage_correct boolean;       -- indique si l’étage du 4e perso-monstre actuel est correct
  v_trop_vieux boolean;          -- indique si le monstre est depuis trop longtemps sous contrôle
  code_monstre integer;          -- code retour contenant -1 ou le perso_cod du monstre donné
  code_etage integer;            -- code de l’étage où le monstre donné a été trouvé
  v_temp varchar(200);           -- variable temporaire
begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  code_monstre := -1; --par défaut

  /*********************************************************/
  /*                  C O N T R O L E S                    */
  /*********************************************************/
  -- controle sur la possibilité d’ajouter un 4e perso
  select into v_autorise, v_possede, v_type_quatrieme, niveau_max
    autorise_4e_monstre(compt_quatre_perso, compt_dcreat),
    possede_4e_perso(compt_cod),
    coalesce(compt_type_quatrieme, 1),
    (now()::date - compt_dcreat::date) / 80
  from compte
  where compt_cod = num_compte;
  if not found then
    RAISE NOTICE 'LIGNE 43';
    return code_monstre;
  end if;

  -- Type de 4e personnage <> monstre
  if v_type_quatrieme <> 2 then
    RAISE NOTICE 'LIGNE 49';
    return code_monstre;
  end if;

  -- Pas d’autorisation du 4e perso
  if not v_autorise then
    RAISE NOTICE 'LIGNE 55';
    return code_monstre;
  end if;

  -- Possède déjà un 4e perso
  if v_possede then
    -- On possède déjà un 4e perso.
    -- Avant de sortir, on va vérifier s’il n’y a pas lieu
    -- de le perdre (changement d’étage, durée de possession...)
    select into code_ancien_monstre, v_trop_vieux perso_cod,
      case when (now() - pcompt_date_attachement) > '90 days'::interval then 1
      else 0 end
    from perso_compte
      inner join perso on perso_cod = pcompt_perso_cod
    where pcompt_compt_cod = num_compte
          and perso_actif = 'O'
          and perso_type_perso = 2;
    if not found then -- On trouve pas, donc il s’agissait d’un aventurier...
      RAISE NOTICE 'LIGNE 73';
      return code_monstre;
    else -- on a un monstre...
      select into v_etage_correct
        case when pos_etage = perso_etage_origine then 1
        else 0 end
      from perso
        inner join perso_position on ppos_perso_cod = perso_cod
        inner join positions on pos_cod = ppos_pos_cod
      where perso_cod = code_ancien_monstre;
      if v_etage_correct and not v_trop_vieux then -- ... qui est au bon étage, pas trop vieux. On ne fait rien.
        RAISE NOTICE 'LIGNE 84';
        return code_monstre;
      else -- ... qui n’est pas au bon étage, ou trop vieux. On le libère.
        if v_trop_vieux then
          select into v_temp relache_monstre_4e_perso(code_ancien_monstre, 0::smallint);
        else
          select into v_temp relache_monstre_4e_perso(code_ancien_monstre, 2::smallint);
        end if;
      end if;
    end if;
  end if;

  /*********************************************************/
  /*             E X É C U T I O N                         */
  /*********************************************************/

  -- Choix des étages autorisés -> connu du joueur ; pas d’escalier entre cet étage et un étage du joueur
  CREATE TEMP TABLE IF NOT EXISTS etages_autorises  (
    etage_num integer
  ) ;
  INSERT INTO etages_autorises (etage_num)
    SELECT DISTINCT vet_etage FROM etage_visite
      inner join perso on perso_cod = vet_perso_cod
      inner join perso_compte on pcompt_perso_cod = vet_perso_cod
    where pcompt_compt_cod = num_compte
          and perso_actif = 'O';

  -- Liste des étages occupés par le joueur
  CREATE TEMP TABLE IF NOT EXISTS etages_interdits  (
    etage_num integer
  ) ;
  INSERT INTO etages_interdits (etage_num)
    SELECT DISTINCT pos_etage FROM perso
      inner join perso_compte on pcompt_perso_cod = perso_cod
      inner join perso_position on ppos_perso_cod = perso_cod
      inner join positions on pos_cod = ppos_pos_cod
    where pcompt_compt_cod = num_compte
          and perso_actif = 'O';

  -- liste des étages liés aux étages occupés par le joueur
  INSERT INTO etages_interdits (etage_num)
    SELECT DISTINCT etage FROM
      (
        -- au départ de l'étage des personnages du compte ;
        SELECT p2.pos_etage as etage FROM etages_interdits
          inner join positions p1 on p1.pos_etage = etage_num
          inner join lieu_position on lpos_pos_cod = p1.pos_cod
          inner join lieu on lieu_cod = lpos_lieu_cod
          inner join positions p2 on p2.pos_cod = lieu_dest

        UNION
        -- et à l'arrivée.
        SELECT p2.pos_etage as etage FROM etages_interdits
          inner join positions p1 on p1.pos_etage = etage_num
          inner join lieu on lieu_dest = p1.pos_cod
          inner join lieu_position on lpos_lieu_cod = lieu_cod
          inner join positions p2 on p2.pos_cod = lpos_pos_cod

      ) t;

  DELETE FROM etages_autorises WHERE etage_num IN (SELECT etage_num FROM etages_interdits);

  if not exists(SELECT etage_num FROM etages_autorises WHERE etage_num = 0) then
    INSERT INTO etages_autorises (etage_num) VALUES (0);  -- l’étage 0 est donné
  end if;
  if not exists(SELECT etage_num FROM etages_autorises WHERE etage_num = -1) then
    INSERT INTO etages_autorises (etage_num) VALUES (-1);  -- l’étage -1 est donné
  end if;

  -- Choix d’un monstre aléatoire, contrôlé par l’IA, actif, n’appartenant à aucun compte, respectant les critères précédents (étage et niveau max)
  SELECT INTO code_monstre, code_etage perso_cod, pos_etage
  FROM perso
    INNER JOIN perso_position ON ppos_perso_cod = perso_cod
    INNER JOIN positions ON pos_cod = ppos_pos_cod
    INNER JOIN etages_autorises ON etage_num = pos_etage
    LEFT OUTER JOIN perso_compte ON pcompt_perso_cod = perso_cod
    LEFT OUTER JOIN perso_commandement ON perso_subalterne_cod = perso_cod
  WHERE perso_dcreat >= now() - '48 hours'::interval
        AND perso_actif = 'O'
        AND perso_type_perso = 2
        AND perso_niveau <= niveau_max
        AND perso_quete IS NULL
        AND pcompt_cod IS NULL
        AND perso_subalterne_cod IS NULL
        AND perso_dirige_admin <> 'O'
  ORDER BY RANDOM()
  LIMIT 1;

  IF code_monstre IS NOT NULL AND code_monstre > 0 then
    -- Ajout du monstre au compte du joueur
    INSERT INTO perso_compte (pcompt_compt_cod, pcompt_perso_cod, pcompt_date_attachement)
    VALUES (num_compte, code_monstre, now());

    -- Sauvegarde des données initiales du monstre (pour tableau d’honneur)
    INSERT INTO perso_compte_monstre(pcm_pcompt_cod, pcm_renommee, pcm_renommee_magie, pcm_karma, pcm_px, pcm_description)
      SELECT pcompt_cod, perso_renommee, perso_renommee_magie, perso_kharma, perso_px, perso_description
      from perso_compte
        inner join perso on perso_cod = pcompt_perso_cod
      where perso_cod = code_monstre;

    -- Sortie du monstre de l’IA, indication de l’étage d’origine
    UPDATE perso SET perso_dirige_admin = 'O', perso_etage_origine = code_etage WHERE perso_cod = code_monstre;
  END IF;
  RAISE NOTICE 'LIGNE 189';
  delete from etages_autorises;
  delete from etages_interdits;
  return code_monstre;
end;$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;
ALTER FUNCTION public.attribue_monstre_4e_perso(integer)
OWNER TO webdelain;
COMMENT ON FUNCTION public.attribue_monstre_4e_perso(integer) IS 'Donne un monstre à jouer à un compte, en tant que 4e personnage, suivant certaines règles restrictives.';
