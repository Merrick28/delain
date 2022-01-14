--
-- Name: cible_aleatoire(integer, integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION cible_aleatoire(integer, integer, integer, integer, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/****************************************************/
/* fonction cible_aleatoire : permet à un monstre   */
/*   de choisir une cible dans sa vue               */
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
/****************************************************/
declare
  code_retour integer;
  v_monstre alias for $1;
  pos_actuelle alias for $2;
  v_etage alias for $3;
  nb_joueur_en_vue alias for $4;
  v_vue_init alias for $5;
  v_vue integer;

  nb_joueur integer;
  v_cible integer;
  v_x integer;
  v_y integer;
  v_seuil_cible_monstre integer;
begin
  v_seuil_cible_monstre := getparm_n(124);

  if (v_vue_init > 6) then
    v_vue := 6;
  else
    v_vue := v_vue_init;
  end if;

  select into v_x,v_y pos_x,pos_y from positions
  where pos_cod = pos_actuelle;

  nb_joueur := lancer_des(1, nb_joueur_en_vue) - 1;

  -- si désorientation, on ajoute les monstres en cible potentielle, et on n’enregistre pas la cible
  if valeur_bonus(v_monstre, 'DES') > 0 then
    select into v_cible perso_cod
    from perso, perso_position, positions
    where pos_x >= (v_x - v_vue) and pos_x <= (v_x + v_vue)
          and pos_y >= (v_y - v_vue) and pos_y <= (v_y + v_vue)
          and ppos_perso_cod = perso_cod
          and pos_etage = v_etage
          and ppos_pos_cod = pos_cod
          and perso_actif = 'O'
          and perso_tangible = 'O'
          and not exists
    (select 1 from lieu,lieu_position
        where lpos_pos_cod = ppos_pos_cod
              and lpos_lieu_cod = lieu_cod
              and lieu_refuge = 'O')
          and is_surcharge(perso_cod, v_monstre) <= 1
          and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1
    order by random()
    limit 1;
  else
    select into v_cible perso_cod
    from perso,perso_position,positions
    where pos_x >= (v_x - v_vue) and pos_x <= (v_x + v_vue)
          and pos_y >= (v_y - v_vue) and pos_y <= (v_y + v_vue)
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
          and is_surcharge(perso_cod, v_monstre) <= 1
          and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1
    order by random()
    limit 1;

    -- Le monstre enregistre sa nouvelle cible
    update perso set perso_cible = v_cible where perso_cod = v_monstre;
  end if;

  code_retour := v_cible;
  return code_retour;
end;
$_$;


ALTER FUNCTION public.cible_aleatoire(integer, integer, integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION cible_aleatoire(integer, integer, integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION cible_aleatoire(integer, integer, integer, integer, integer) IS 'Permet à un monstre de choisir une cible dans sa vue.';
