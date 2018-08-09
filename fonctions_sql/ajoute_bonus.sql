CREATE OR REPLACE FUNCTION public.ajoute_bonus(integer, text, integer, numeric)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$-- Rajoute un bonus à un perso
-- $1 = Le code du perso en question
-- $2 = Le type de bonus
-- $3 = La durée (DLT) du bonus
-- $3 = La valeur du bonus
-- Retourne 1 si le bonus est nouveau, 0 s'il en remplace un autre du même signe.
-- 13/02/2009 Bleda: Supprime les bonus existants du même type, avant de créer le nouveau
-- ??/??/???? ?????: Remplace les bonus de même signe, sans toucher à ceux de signe contraire
-- 07/12/2009 problème : remplace les bonus de meme signe, meme inférieur.

declare
  v_perso alias for $1;
  v_type alias for $2;
  v_duree alias for $3;
  v_valeur alias for $4;
  v_retour integer;
begin
  v_retour := 1;
  delete from bonus where
    bonus_perso_cod = v_perso and
    bonus_tbonus_libc = v_type and sign(bonus_valeur) = sign(v_valeur);
  if found then
    -- On a effacé un bonus existant pour le remplacer.
    v_retour := 0;
  end if;
  -- En attendant la version définitive, on supprime aussi les bonus de sens opposé
  -- AJout de Az le 07/12/2009.. on tente une mise en production définitive.. mise en commentaire des 3 lignes suivantes
  -- AJout de Bleda le 07/02/2011.. on tente une mise en production définitive.. encore. mise en commentaire des 3 lignes suivantes
  -- Bleda, 27/03/2011 : Désactivation du cumul de bonus pour PAA et DEP: Décommenter les 4 lignes suivantes
  -- delete from bonus where
  --   bonus_perso_cod = v_perso and
  --   bonus_tbonus_libc = v_type and sign(bonus_valeur) != sign(v_valeur);
  --   and tbonus_libc in ('PAA', 'DEP')
  -- Fin de la partie à décommenter pour les bonus sélectionnés
  insert into bonus (bonus_perso_cod, bonus_tbonus_libc, bonus_nb_tours, bonus_valeur)
  values (v_perso,         v_type,            v_duree,        v_valeur);

  return v_retour;
end;$function$

CREATE OR REPLACE FUNCTION public.ajoute_bonus(integer, text, integer, numeric, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$-- Rajoute un bonus à un perso
-- $1 = Le code du perso en question
-- $2 = Le type de bonus
-- $3 = La durée (DLT) du bonus
-- $4 = La valeur du bonus
-- $5 = La valeur de laquelle le bonus évolue à chaque DLT.

declare
  v_perso alias for $1;
  v_type alias for $2;
  v_duree alias for $3;
  v_valeur alias for $4;
  v_croissance alias for $5;
  v_retour integer;
begin
  v_retour := ajoute_bonus(v_perso, v_type, v_duree, v_valeur);

  update bonus set bonus_croissance = v_croissance
  where bonus_perso_cod = v_perso
        and bonus_tbonus_libc = v_type
        and bonus_valeur = v_valeur;

  return v_retour;
end;$function$

