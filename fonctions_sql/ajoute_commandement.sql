CREATE OR REPLACE FUNCTION public.ajoute_commandement(integer, integer, boolean)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ajoute_commandement :                                */
/*      Procédure utilisée pour ajouter un monstre à l’armée     */
/*  d’un commandant                                              */
/* On passe en paramètres                                        */
/*    $1 = commandant                                            */
/*    $2 = monstre à ajouter                                     */
/*    $3 = ignorer la valeur de la compétence commandement       */
/*****************************************************************/
/* Créé le 03/12/2012                                            */
/*****************************************************************/
declare
  commandant alias for $1;   -- le perso_cod du commandant
  troufion alias for $2;     -- le perso_cod du monstre à ajouter à l’armée du commandant
  ignorer_comp alias for $3; -- indique si on vérifie que le commandant peut contrôler assez de monde.
  coterie integer;           -- le groupe_cod de la coterie du commandant
  nom_commandant text;       -- Le nom du commandant
  nom_troufion text;         -- Le nom du troufion
  code_commandement integer; -- le code de compétence de commandement
  comp_commandement integer; -- le valeur de compétence de commandement
  nb_troufions integer;      -- le valeur de compétence de commandement
  chef integer;              -- Indique si le commandant est chef de sa coterie
  code_retour text;          -- le code retour
begin
  code_retour := '';
  code_commandement := 80;

  -- Vérification d’existence
  select into nom_commandant perso_nom from perso where perso_cod = commandant;
  if not found then
    return 'Erreur ! Commandant non trouvé.';
  end if;
  select into nom_troufion perso_nom from perso where perso_cod = troufion;
  if not found then
    return 'Erreur ! Troufion non trouvé.';
  end if;
  select into comp_commandement pcomp_modificateur from perso_competences
  where pcomp_perso_cod = commandant and pcomp_modificateur != 0 and pcomp_pcomp_cod = code_commandement;
  if not found and not ignorer_comp then
    return 'Erreur ! Le commandant n’a pas la compétence idoine.';
  elsif not ignorer_comp then
    select into nb_troufions count(*) from perso_commandement where perso_superieur_cod = commandant;
    if nb_troufions >= comp_commandement then
      return 'Erreur ! Le commandant a assez de troupes comme ça.';
    end if;
  end if;

  -- Ajout du commandement
  insert into perso_commandement (perso_subalterne_cod, perso_superieur_cod) values (troufion, commandant);

  -- Récupération de la coterie
  select into chef, coterie pgroupe_chef, pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod = commandant and pgroupe_statut <> 0;
  if not found then
    -- Création d’une nouvelle coterie
    insert into groupe (groupe_nom, groupe_chef)
    values ('Coterie du commandant ' || nom_commandant, commandant);

    chef := 1;

    select into coterie groupe_cod from groupe where groupe_chef = commandant order by groupe_cod desc limit 1;

    insert into groupe_perso (pgroupe_perso_cod, pgroupe_groupe_cod, pgroupe_statut, pgroupe_chef, pgroupe_message_mort)
    values (commandant, coterie, 1, 1, 0);
    code_retour := 'Coterie créée et donnée à ' || nom_commandant || '.';
  end if;

  -- Si le commandant est chef de sa coterie, on y ajoute l’autre monstre.
  if chef = 1 then
    delete from groupe_perso where pgroupe_perso_cod = troufion;
    insert into groupe_perso (pgroupe_perso_cod, pgroupe_groupe_cod, pgroupe_statut, pgroupe_message_mort, pgroupe_messages)
    values (troufion, coterie, 1, 0, 0);
  else
    code_retour := 'Attention, ' || nom_troufion || ' n’a pas été ajouté à la coterie du commandant, car ce dernier n’en est pas le chef.';
  end if;
  code_retour := nom_troufion || ' ajouté sous le commandement de ' || nom_commandant || '. ' || code_retour;

  return code_retour;
end;$function$

