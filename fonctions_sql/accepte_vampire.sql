CREATE OR REPLACE FUNCTION public.accepte_vampire(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction accepte_vampire                            */
/*  params :                                           */
/*  $1 = fils                                          */
/*  retour texte                                       */
/*******************************************************/
declare
  code_retour text;
  v_fils alias for $1;
  v_pere integer;
  v_pa integer;
  v_des_regen integer;
  v_karma numeric;
  texte_evt text;
  nom_pere text;
  nom_fils text;
  v_race integer;
  v_af integer;

begin
  select into v_pere,nom_pere
    tvamp_perso_pere,perso_nom
  from vampire_tran,perso
  where tvamp_perso_fils = v_fils
        and tvamp_perso_pere = perso_cod;
  if not found then
    code_retour := 'Erreur ! Ascendant non trouvé !';
    return code_retour;
  end if;
  select into v_pa,v_des_regen,v_karma,nom_fils,v_race perso_pa,perso_des_regen,perso_kharma,perso_nom,perso_race_cod from perso
  where perso_cod = v_fils;
  if not found then
    code_retour := 'Erreur ! problème de base';
    return code_retour;
  end if;
  if v_pa < 12 then
    code_retour := 'Erreur ! Pas assez de PA pour accomplir le rite !';
    return code_retour;
  end if;
  -- controles OK, on peut passer à la suite
  --Traitement spécifique des nains
  if (v_race = 2) then
    delete from perso_competences where pcomp_perso_cod = v_fils and pcomp_pcomp_cod = 27;
    insert into perso_sort (psort_sort_cod,psort_perso_cod) values (6,v_fils);
    select into v_af pcomp_pcomp_cod from perso_competences where pcomp_perso_cod = v_fils and pcomp_pcomp_cod in (25,61,62);
    if (v_af = 25) then
      delete from perso_competences where pcomp_perso_cod = v_fils and pcomp_pcomp_cod = 25;
    elsif (v_af = 61) then
      update perso_competences set pcomp_pcomp_cod = 25 where pcomp_perso_cod = v_fils and pcomp_pcomp_cod = 61;
    elsif (v_af = 62) then
      update perso_competences set pcomp_pcomp_cod = 61 where pcomp_perso_cod = v_fils and pcomp_pcomp_cod = 62;
    end if;
  end if;

  -- regen
  if v_des_regen > 10 then
    v_des_regen := 10;
  end if;
  update perso set perso_des_regen = 0, perso_vampirisme = (v_des_regen*0.05)::numeric
  where perso_cod = v_fils;
  -- race
  update perso set perso_race_cod = (select perso_race_cod from perso where perso_cod = v_pere)
  where perso_cod = v_fils;
  -- sorts
  insert into perso_sorts
  (psort_perso_cod,psort_sort_cod)
  values
    (v_fils,44);
  -- karma
  if v_karma > 0 then
    update perso set perso_kharma = 0 where perso_cod = v_fils;
  end if;
  -- degats
  update perso
  set perso_val_des_degats = 4,perso_pa = 0 where perso_cod = v_fils;
  -- niveau vampire
  update perso
  set perso_niveau_vampire = 1 where perso_cod = v_fils;
  --transactions
  delete from vampire_tran
  where tvamp_perso_fils = v_fils;
  -- historisation
  insert into vampire_hist (vamp_perso_pere,vamp_nom_ppere,vamp_perso_fils,vamp_nom_pfils)
  values
    (v_pere,nom_pere,v_fils,nom_fils);
  -- evenements
  texte_evt := '[attaquant] a transformé [cible] en vampire';
  /* evts pour coup porté */
  insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
  values(nextval('seq_levt_cod'),27,now(),1,v_pere,texte_evt,'N','O',v_pere,v_fils);
  insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
  values(nextval('seq_levt_cod'),27,now(),1,v_fils,texte_evt,'O','O',v_pere,v_fils);

  code_retour := '<p>Le rituel est accompli. Vous faites maintenant partie de la famille des vampires.<br>';
  return code_retour;
end;
$function$

