CREATE OR REPLACE FUNCTION public.ajoute_familier(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ajoute_familier :                                    */
/*    Procédure utilisée pour créer un familier et le rattacher  */
/*  à son maître                                                 */
/*  Attention : cette fonction ne gère pas les familiers         */
/*  pré-existants comme Kirga. Uniquement les nouvellement créés.*/
/* On passe en paramètres                                        */
/*    $1 = gmon_cod du familier                                  */
/*    $2 = perso_cod du maître                                   */
/*****************************************************************/
/* Créé le 03/12/2012                                            */
/*****************************************************************/
declare
  type_familier alias for $1;   -- le gmon_cod du familier
  maitre alias for $2;          -- le perso_cod du maître du familier
  familier integer;             -- le perso_cod du familier
  coterie integer;              -- le groupe_cod de la coterie du maître
  nom_maitre text;              -- Le nom du maître
  pos_maitre integer;           -- pos_cod du maître
  maitre_util_PA integer;       -- pos_cod du maître
  nom_familier text;            -- Le nom du familier
  v_dieu_maitre integer;        -- Le code du dieu du maître
  v_dieu_nom text;              -- Le nom du dieu du maître
  v_dieu_niveau integer;        -- Le niveau du maître dans son clergé
  v_sort_niveau integer;        -- Le niveau maximal des sorts auxquels le familier a droit
  v_sort_nombre integer;        -- Le nombre de sorts auxquels le familier a droit
  code_retour text;             -- le code retour : '{0|1};texte'. Avec 0 si OK, 1 si KO. texte = perso_cod du familier si OK, message d’erreur si KO
begin
  code_retour := '';

  -- Vérification d’existence
  select into nom_maitre, maitre_util_PA perso_nom, perso_utl_pa_rest from perso where perso_cod = maitre;
  if not found then
    return '1;Erreur ! Maître non trouvé.';
  end if;

  select into familier pfam_familier_cod from perso_familier
    inner join perso on perso_cod = pfam_familier_cod
  where pfam_perso_cod = maitre and perso_actif = 'O';
  if found then
    return '1;Erreur ! Ce maître a déjà un familier.';
  end if;

  /* -- Suppression de la vérification du type de familier pour autoriser un monstre quelconque.
    if type_familier not in (191, 192, 193, 440, 441) then
      -- Types de familiers connus (hors Kirga / image de Kirga) :
      -- 191 : magie
      -- 192 : combat
      -- 193 : distance
      -- 440 : mûle
      -- 441 : divin
      return '1;Erreur ! Type de familier non reconnu.';
    end if;
  */

  -- Création du monstre
  select into pos_maitre ppos_pos_cod from perso_position where ppos_perso_cod = maitre;
  familier := cree_monstre_pos(type_familier, pos_maitre);

  -- Mise à jour de l’affiliation divine
  if type_familier in (441) then
    select into v_dieu_maitre, v_dieu_niveau, v_dieu_nom
      dper_dieu_cod, dper_niveau, dieu_nom
    from dieu_perso
      inner join dieu on dieu_cod = dper_dieu_cod
    where dper_perso_cod = maitre;

    insert into dieu_perso (dper_dieu_cod, dper_perso_cod, dper_niveau, dper_points)
    values (v_dieu_maitre, familier, 2, v_dieu_niveau * 30 - 50);
  end if;

  -- Mise à jour du nom du familier
  if type_familier in (191, 192, 193, 440) then
    nom_familier := 'Familier de ' || nom_maitre;
  elsif type_familier in (441) then
    nom_familier := 'Familier de ' || nom_maitre || ' (esprit de ' || v_dieu_nom || ')';
  end if;
  update perso set
    perso_nom = coalesce(nom_familier, perso_nom),
    perso_lower_perso_nom = coalesce(lower(nom_familier), perso_lower_perso_nom),
    perso_type_perso = 3,
    perso_kharma = 0,
    perso_utl_pa_rest = maitre_util_PA
  where perso_cod = familier;

  -- Ajout de sortilèges
  if type_familier in (191) then
    v_sort_niveau := 2;
    v_sort_nombre := 2;
  else
    v_sort_niveau := 0;
    v_sort_nombre := 0;
  end if;
  insert into perso_sorts (psort_perso_cod, psort_sort_cod)
    select familier, sort_cod from sorts
    where sort_niveau <= v_sort_niveau and sort_combinaison not like '%9%'
    order by random()
    limit v_sort_nombre;

  -- Gestion des coteries
  select into coterie pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod = maitre and pgroupe_statut = 1;
  if found then
    insert into groupe_perso (pgroupe_groupe_cod, pgroupe_perso_cod, pgroupe_statut, pgroupe_messages, pgroupe_message_mort)
    values (coterie, familier, 1, 0, 0);
  end if;

  -- Ajout du familier
  insert into perso_familier (pfam_perso_cod, pfam_familier_cod)
  values (maitre, familier);

  return '0;' || familier::text;
end;$function$

