CREATE OR REPLACE FUNCTION public.demonstrifie_perso(integer, integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$-- Démonstrification de perso, en cas d'urgence.
-- Paramètres:
-- $1 = numéro de perso
-- $2 = numéro de compte auquel le rattacher
-- Pas de retour.
declare
  v_perso alias for $1;
  v_compte alias for $2;
begin
  update perso set perso_type_perso = 1 , perso_actif = 'O' where perso_cod = v_perso;
  delete from perso_compte where pcompt_perso_cod = v_perso;
  insert into perso_compte values (default , v_compte , v_perso);
end;$function$

