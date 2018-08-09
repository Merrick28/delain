CREATE OR REPLACE FUNCTION public.ouvre_cadeau(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************/
/* ouvre cadeau                                   */
/**************************************************/
declare
  code_retour text;
  personnage alias for $1;
  v_objet integer;
  v_pa integer;
  temp integer;
  temp_txt text;
  v_pv integer;
  texte_evt text;
begin
  ----------------------------------------------------
  -- DEBUT CONTROLES AVANT ACTION
  ----------------------------------------------------
  --
  ----------------------------------------------------
  -- DEBUT vérification de la possession du cadeau
  ----------------------------------------------------
  select into v_objet
    obj_cod
  from objets,perso_objets
  where perobj_perso_cod = personnage
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = 327;
  if not found then
    code_retour := 'Erreur ! Vous n’avez pas de cadeau dans votre inventaire !';
    return code_retour;
  end if;
  ----------------------------------------------------
  -- FIN   vérification de la possession du cadeau
  ----------------------------------------------------
  --
  ----------------------------------------------------
  -- DEBUT vérification des PA
  ----------------------------------------------------
  select into v_pa
    perso_pa
  from perso
  where perso_cod = personnage;
  if v_pa < getparm_n(98) then
    code_retour := 'Erreur ! Vous n’avez pas assez de PA pour cette action !';
    return code_retour;
  end if;
  ----------------------------------------------------
  -- FIN   vérification des PA
  ----------------------------------------------------
  --
  ----------------------------------------------------
  -- FIN   CONTROLES AVANT ACTION
  ----------------------------------------------------
  --
  ----------------------------------------------------
  -- DEBUT ouverture cadeau, fonction principale
  ----------------------------------------------------
  --
  ----------------------------------------------------
  -- DEBUT destruction cadeau
  ----------------------------------------------------
  v_pa := f_del_objet(v_objet);
  ----------------------------------------------------
  -- FIN   destruction cadeau
  ----------------------------------------------------
  --
  ----------------------------------------------------
  -- DEBUT choix action
  ----------------------------------------------------
  update perso
  set perso_pa = perso_pa - getparm_n(98)
  where perso_cod = personnage;
  v_pa := lancer_des(1,100);

  if v_pa <= 60 then
    -- création rune
    temp := lancer_des(1,20) + 26;
    temp_txt := cree_objet_perso_nombre(temp, personnage, 1);
    temp := lancer_des(1,20) + 26;
    temp_txt := cree_objet_perso_nombre(temp, personnage, 1);
    code_retour := 'Vous trouvez deux runes dans le cadeau !';
  elsif v_pa <= 90 then
    -- explosion de cadeau moyenne
    select into v_pv
      perso_pv
    from perso
    where perso_cod = personnage;
    temp := lancer_des(1,10);
    v_pv := v_pv - temp;
    if v_pv < 1 then
      v_pv := 1;
    end if;
    code_retour := 'Le cadeau vous explose au visage !';
    update perso set perso_pv = v_pv where perso_cod = personnage;
  else
    -- explosion de cadeau forte
    select into v_pv
      perso_pv
    from perso
    where perso_cod = personnage;
    temp := lancer_des(2,10);
    v_pv := v_pv - temp;
    if v_pv < 1 then
      v_pv := 1;
    end if;
    code_retour := 'Le cadeau vous explose fortement au visage !';
    update perso set perso_pv = v_pv where perso_cod = personnage;
  end if;

  /* Partie modifiee pour les recompenses de la quete de noel afin de mettre un cadeau et 2 runes dans le cadeau
    if v_pa <= 10 then
    -- création rune male
      temp := cree_objet_perso_nombre(27,personnage,2);
      code_retour := 'Vous trouvez deux runes dans le cadeau !';
    elsif v_pa <= 20 then
    -- création rune femelle
      temp := cree_objet_perso_nombre(28,personnage,2);
      code_retour := 'Vous trouvez deux runes dans le cadeau !';
    elsif v_pa <= 50 then
    -- création rune famille 2
      temp := lancer_des(1,3)+28;
      temp := cree_objet_perso_nombre(temp,personnage,2);
      code_retour := 'Vous trouvez deux runes dans le cadeau !';
    else
    -- création rune famille 3 ou 4
    temp := lancer_des(1,9)+31;
    temp := cree_objet_perso_nombre(temp,personnage,2);
    code_retour := 'Vous trouvez deux runes dans le cadeau !';
    end if;
  */
  texte_evt := '[perso_cod1] a ouvert un cadeau.';
  insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
  values(62,now(),personnage,texte_evt,'O','O');
  return code_retour;
  ----------------------------------------------------
  -- FIN   choix action
  ----------------------------------------------------
  --
  ----------------------------------------------------
  -- FIN   ouverture cadeau, fonction principale
  ----------------------------------------------------
end;$function$

