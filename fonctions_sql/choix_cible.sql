--
-- Name: choix_cible(integer, integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION choix_cible(integer, integer, integer, integer, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/****************************************************/
/* fonction choix_cible : permet à un monstre de    */
/*   choisir une cible dans sa vue                  */
/* on passe en paramètres :                         */
/*   $1 = perso_cod du monstre                      */
/*   $2 = pos_cod du monstre                        */
/*   $3 = l etage du monstre                        */
/*   $4 = le nombre de persos en vue                */
/*   $5 = distance vue du monstre                   */
/****************************************************/
/* on a en sortie le perso_cod de la cible          */
/****************************************************/
/* créé le 08/08/2003                               */
/* Modif Blade le 07/11/2009 : correction ligne vue */
/****************************************************/
declare
  code_retour integer;
  v_monstre alias for $1;
  pos_actuelle alias for $2;
  v_etage alias for $3;
  nb_joueur_en_vue alias for $4;
  v_vue alias for $5;
  --
  nb_joueur integer;
  v_cible integer;
  des_choix integer;
  nb_cible_en_vue integer;
  nb_cible_sur_case integer;
  index_cible integer;
  ligne_cible record;
  etat_cible numeric;
  dernier_etat_cible numeric;
  v_reputation numeric;
  dernier_v_reputation numeric;
  distance_limite integer;
  v_x integer;
  v_y integer;
  nb_lock integer;
  des_autre integer;
  --v_vue integer;
  v_seuil_cible_monstre integer;

begin
  -- principes de base :
  -- le monstre n a plus sa cible sur la case
  -- on doit donc en choisir un autre
  -- on fait un lancer de dés pour savoir ce que le monstre va faire
  -- 1-20 : il essaie de garder la même cible
  -- 20-40 : il prend une cible sur la même case
  -- 40-60 : il prend la plus blessée
  -- 60-80 : il prend la plus basse réputation
  -- 80-100 : il prend celle à la plus haute réputation
  -- dans tous les cas, si un des choix échoue, on prend une cible aléatoire

  v_seuil_cible_monstre := getparm_n(124);

  select into nb_lock count(lock_cod)
  from lock_combat,perso
  where lock_cible = v_monstre
        and lock_attaquant = perso_cod
        and perso_actif = 'O';
  if nb_lock != 0 then
    des_autre := lancer_des(1,nb_lock);
    des_autre := des_autre - 1;
    select into v_cible
      perso_cod
    from perso,lock_combat
    where lock_cible = v_cible
          and lock_attaquant = perso_cod
          and perso_actif = 'O'
    limit 1
    offset des_autre;
    return v_cible;
  end if;
  v_cible := cible_aleatoire(v_monstre,pos_actuelle,v_etage,nb_joueur_en_vue,v_vue);
  select into v_x,v_y pos_x,pos_y
  from positions
  where pos_cod = pos_actuelle;
  des_choix := lancer_des(1,100);
  if des_choix <= 20 then
    -- même cible
    -- on regarde d'abord si elle est en vue
    select into v_cible perso_cible from perso
    where perso_cod = v_monstre;
    select into	nb_cible_en_vue count(*)
    from perso_position,perso,positions
    where ppos_perso_cod = v_cible
          and ppos_pos_cod = pos_actuelle
          and perso_cod = v_cible
          and perso_actif = 'O'
          and ( perso_type_perso != 2
                OR perso_monstre_attaque_monstre >= v_seuil_cible_monstre)
          and perso_tangible = 'O'
          and perso_cod <> v_monstre
          and distance(pos_actuelle, pos_cod) <= v_vue
          and not exists
    (select 1 from lieu,lieu_position
        where lpos_pos_cod = ppos_pos_cod
              and lpos_lieu_cod = lieu_cod
              and lieu_refuge = 'O')
          and ppos_pos_cod = pos_cod
          and pos_etage = v_etage
          and is_surcharge(perso_cod,v_monstre) <= 1
          and trajectoire_vue_murs(pos_actuelle,pos_cod) = 1;
    if nb_cible_en_vue = 0 then
      v_cible := cible_aleatoire(v_monstre,pos_actuelle,v_etage,nb_joueur_en_vue,v_vue);
    end if;
  end if;
  --
  if des_choix > 20 and des_choix <= 40 then
    -- cible sur même case
    select into nb_cible_sur_case count(*)
    from perso_position,perso,positions
    where ppos_perso_cod = v_cible
          and ppos_pos_cod = pos_actuelle
          and perso_actif = 'O'
          and ( perso_type_perso != 2
                OR perso_monstre_attaque_monstre >= v_seuil_cible_monstre)
          and perso_tangible = 'O'
          and perso_cod <> v_monstre
          and distance(pos_actuelle, pos_cod) <= v_vue
          and not exists
    (select 1 from lieu,lieu_position
        where lpos_pos_cod = ppos_pos_cod
              and lpos_lieu_cod = lieu_cod
              and lieu_refuge = 'O')
          and ppos_pos_cod = pos_cod
          and pos_etage = v_etage
          and is_surcharge(perso_cod,v_monstre) <= 1
          and trajectoire_vue_murs(pos_actuelle,pos_cod) = 1;
    if nb_cible_sur_case = 0 then
      v_cible := cible_aleatoire(v_monstre,pos_actuelle,v_etage,nb_joueur_en_vue,v_vue);
    else
      index_cible := lancer_des(1,nb_cible_sur_case);
      select into v_cible perso_cod
      from perso,perso_position,positions
      where ppos_pos_cod = pos_actuelle
            and ppos_perso_cod = perso_cod
            and pos_etage = v_etage
            and ppos_pos_cod = pos_cod
            and ( perso_type_perso != 2
                  OR perso_monstre_attaque_monstre >= v_seuil_cible_monstre)
            and perso_actif = 'O'
            and perso_tangible = 'O'
            and perso_cod <> v_monstre
            and distance(pos_actuelle, pos_cod) <= v_vue
            and not exists
      (select 1 from lieu,lieu_position
          where lpos_pos_cod = ppos_pos_cod
                and lpos_lieu_cod = lieu_cod
                and lieu_refuge = 'O')
            and is_surcharge(perso_cod,v_monstre) <= 1
            and trajectoire_vue_murs(pos_actuelle,pos_cod) = 1
      limit 1
      offset index_cible;
    end if;
  end if;
  --
  if des_choix > 40 and des_choix <= 60 then
    -- plus blessée

    dernier_etat_cible := 2;
    for ligne_cible in select perso_cod,perso_pv,perso_pv_max
                       from perso,perso_position,positions
                       where pos_x >= (v_x - v_vue) and pos_x <= (v_x + v_vue)
                             and pos_y >= (v_y - v_vue) and pos_y <= (v_y - v_vue)
                             and ppos_perso_cod = perso_cod
                             and pos_etage = v_etage
                             and ppos_pos_cod = pos_cod
                             and ( perso_type_perso != 2
                                   OR perso_monstre_attaque_monstre >= v_seuil_cible_monstre)
                             and perso_actif = 'O'
                             and perso_tangible = 'O'
                             and perso_cod <> v_monstre
                             and not exists
                       (select 1 from lieu,lieu_position
                           where lpos_pos_cod = ppos_pos_cod
                                 and lpos_lieu_cod = lieu_cod
                                 and lieu_refuge = 'O')
                             /*Correction Blade pour rajouter les deux lignes suivantes, qui n’apparaissaient pas 07/11/2009*/
                             and is_surcharge(perso_cod,v_monstre) <= 1
                             and trajectoire_vue_murs(pos_actuelle,pos_cod) = 1
    loop
      etat_cible := ligne_cible.perso_pv/ligne_cible.perso_pv_max;
      if etat_cible <= dernier_etat_cible then
        dernier_etat_cible := etat_cible;
        v_cible := ligne_cible.perso_cod;
      end if;
    end loop;
  end if;
  --
  if des_choix > 60 and des_choix <= 80 then
    -- plus basse réputation
    dernier_v_reputation := 100000;
    for ligne_cible in select perso_cod,perso_kharma
                       from perso,perso_position,positions
                       where pos_x >= (v_x - v_vue) and pos_x <= (v_x + v_vue)
                             and pos_y >= (v_y - v_vue) and pos_y <= (v_y - v_vue)
                             and ppos_perso_cod = perso_cod
                             and pos_etage = v_etage
                             and ppos_pos_cod = pos_cod
                             and ( perso_type_perso != 2
                                   OR perso_monstre_attaque_monstre >= v_seuil_cible_monstre)
                             and perso_actif = 'O'
                             and perso_tangible = 'O'
                             and perso_cod <> v_monstre
                             and not exists
                       (select 1 from lieu,lieu_position
                           where lpos_pos_cod = ppos_pos_cod
                                 and lpos_lieu_cod = lieu_cod
                                 and lieu_refuge = 'O')
                             and is_surcharge(perso_cod,v_monstre) <= 1
                             and trajectoire_vue_murs(pos_actuelle,pos_cod) = 1
    loop
      v_reputation := ligne_cible.perso_kharma;
      if v_reputation <= dernier_v_reputation then
        v_cible := ligne_cible.perso_cod;
        dernier_v_reputation := v_reputation;
      end if;
    end loop;
  end if;
  --
  if des_choix > 80 then
    -- plus haute réputation
    dernier_v_reputation := -100000;
    for ligne_cible in select perso_cod,perso_kharma
                       from perso,perso_position,positions
                       where pos_x >= (v_x - v_vue) and pos_x <= (v_x + v_vue)
                             and pos_y >= (v_y - v_vue) and pos_y <= (v_y - v_vue)
                             and ppos_perso_cod = perso_cod
                             and pos_etage = v_etage
                             and ppos_pos_cod = pos_cod
                             and ( perso_type_perso != 2
                                   OR perso_monstre_attaque_monstre >= v_seuil_cible_monstre)
                             and perso_actif = 'O'
                             and perso_tangible = 'O'
                             and perso_cod <> v_monstre
                             and not exists
                       (select 1 from lieu,lieu_position
                           where lpos_pos_cod = ppos_pos_cod
                                 and lpos_lieu_cod = lieu_cod
                                 and lieu_refuge = 'O')
                             and is_surcharge(perso_cod,v_monstre) <= 1
                             and trajectoire_vue_murs(pos_actuelle,pos_cod) = 1
    loop
      v_reputation := ligne_cible.perso_kharma;
      if v_reputation >= dernier_v_reputation then
        v_cible := ligne_cible.perso_cod;
        dernier_v_reputation := v_reputation;
      end if;
    end loop;
  end if;
  if v_cible is null then
    return des_choix;
  end if;

  update perso set perso_cible = v_cible where perso_cod = v_monstre;
  code_retour := v_cible;
  return code_retour;
end;
$_$;


ALTER FUNCTION public.choix_cible(integer, integer, integer, integer, integer) OWNER TO delain;
