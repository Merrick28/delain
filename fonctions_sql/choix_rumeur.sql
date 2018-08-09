CREATE OR REPLACE FUNCTION public.choix_rumeur()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* fonction choix_rumeur : retourne une rumeur           */
/*********************************************************/
declare
  code_retour text;
  poids_total integer;
  v_rum integer;
  ligne record;
  poids_actu integer;
begin
  poids_actu := 0;
  select into poids_total sum(rum_poids) from rumeurs;
  v_rum := lancer_des(1,poids_total);
  for ligne in select rum_cod,rum_poids,rum_texte from rumeurs loop
    poids_actu := poids_actu + ligne.rum_poids;
    if poids_actu >= v_rum then
      update rumeurs set rum_vu = rum_vu + 1 where rum_cod = ligne.rum_cod;
      code_retour := ligne.rum_texte;
      exit;
    end if;
  end loop;
  return code_retour;
end;$function$

