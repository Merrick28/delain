CREATE OR REPLACE FUNCTION public.annule_sort(integer, integer, integer, numeric)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/****************************************************/
/* annule_sort                                      */
/*  $1 = perso_cod du lanceur                       */
/*  $2 = sort_cod                                   */
/*  $3 = type_lancer                                */
/****************************************************/
declare
  code_retour text;
  lanceur alias for $1;
  num_sort alias for $2;
  type_lancer alias for $3;
  px_gagne alias for $4;
  cout_pa integer;
  niveau_sort integer;
  temp_renommee numeric;
  bonus_pa integer;
  ligne_rune record;
  temp_obj integer;
begin
  select into cout_pa,niveau_sort sort_cout,sort_niveau from sorts where sort_cod = num_sort;
  if not found then
    code_retour := code_retour||'0;<p>Erreur : sort non trouvé !';
    return code_retour;
  end if;
  temp_renommee := ((niveau_sort-1)*0.1)::numeric;
  bonus_pa := valeur_bonus(lanceur, 'PAM');
  -- on modifie le nombre de sorts total
  update perso_nb_sorts_total
  set pnbst_nombre = pnbst_nombre - 1
  where pnbst_sort_cod = num_sort
        and pnbst_perso_cod = lanceur;
  -- on modifie le nombre de sorts du tour
  update perso_nb_sorts
  set pnbs_nombre = pnbs_nombre - 1
  where pnbs_sort_cod = num_sort
        and pnbs_perso_cod = lanceur;
  -- on enlève la renommée magique
  update perso set perso_renommee_magie = perso_renommee_magie - temp_renommee where perso_cod = lanceur;
  -- on enlève les px
  update perso set perso_px = perso_px - px_gagne where perso_cod = lanceur;
  -- on remet les PA
  if type_lancer != 2 then
    cout_pa := cout_pa + bonus_pa;
  else
    cout_pa := 2;
  end if;
  update perso
  set perso_pa = perso_pa + cout_pa
  where perso_cod = lanceur;
  -- on remet les runes
  if type_lancer = 0 then
    for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
      temp_obj := cree_objet_perso_nombre(ligne_rune.srune_gobj_cod,lanceur,1);
    end loop;
  elsif type_lancer = 2 then
    insert into recsort (recsort_perso_cod,recsort_sort_cod)
    values (lanceur,num_sort);
  end if;
  return 'OK';
end;$function$

CREATE OR REPLACE FUNCTION public.annule_sort(integer, integer, integer, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/****************************************************/
/* annule_sort                                      */
/*  $1 = perso_cod du lanceur                       */
/*  $2 = sort_cod                                   */
/*  $3 = type_lancer                                */
/****************************************************/
declare
  code_retour text;
  lanceur alias for $1;
  num_sort alias for $2;
  type_lancer alias for $3;
  px_gagne alias for $4;
  cout_pa integer;
  niveau_sort integer;
  temp_renommee numeric;
  bonus_pa integer;
  ligne_rune record;
  temp_obj integer;
begin
  select into cout_pa,niveau_sort sort_cout,sort_niveau from sorts where sort_cod = num_sort;
  if not found then
    code_retour := code_retour||'0;<p>Erreur : sort non trouvé !';
    return code_retour;
  end if;
  temp_renommee := ((niveau_sort-1)*0.1)::numeric;
  bonus_pa := valeur_bonus(lanceur, 'PAM');
  -- on modifie le nombre de sorts total
  update perso_nb_sorts_total
  set pnbst_nombre = pnbst_nombre - 1
  where pnbst_sort_cod = num_sort
        and pnbst_perso_cod = lanceur;
  -- on modifie le nombre de sorts du tour
  update perso_nb_sorts
  set pnbs_nombre = pnbs_nombre - 1
  where pnbs_sort_cod = num_sort
        and pnbs_perso_cod = lanceur;
  -- on enlève la renommée magique
  update perso set perso_renommee_magie = perso_renommee_magie - temp_renommee where perso_cod = lanceur;
  -- on enlève les px
  update perso set perso_px = perso_px - to_number(px_gagne,'99999999D99') where perso_cod = lanceur;
  -- on remet les PA
  if type_lancer != 2 then
    cout_pa := cout_pa + bonus_pa;
  else
    cout_pa := 2;
  end if;
  update perso
  set perso_pa = perso_pa + cout_pa
  where perso_cod = lanceur;
  -- on remet les runes
  if type_lancer = 0 then
    for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
      temp_obj := cree_objet_perso_nombre(ligne_rune.srune_gobj_cod,lanceur,1);
    end loop;
  elsif type_lancer = 2 then
    insert into recsort (recsort_perso_cod,recsort_sort_cod)
    values (lanceur,num_sort);
  end if;
  return 'OK';
end;$function$

