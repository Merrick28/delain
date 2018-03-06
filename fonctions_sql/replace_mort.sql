--
-- Name: replace_mort(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION replace_mort(integer, integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*************************************************/
/* replace_mort                                  */
/*************************************************/
declare
  code_retour text;
  v_cible alias for $1;
  v_attaquant alias for $2;
  v_mode alias for $3; --(0 = normal, 1= en prison)
  pos_temple_cible integer;
  nb_temple_cible integer;
  chance_temple integer;
  des_temple integer;
  nouvelle_position integer;
  nv_etage integer;
  etage_ref integer;
  v_familier integer;
  nb_temple integer;
  texte_evt text;
  nouveau_x integer;
  nouveau_y integer;
  nouveau_etage integer;
  v_reputation numeric;
  v_position integer;
  v_arene text;
  v_type_arene integer;

begin
  nouvelle_position := -1;
  select into etage_ref, v_arene, v_type_arene
    etage_mort, etage_arene, etage_type_arene
  from perso_position, positions, etage
  where ppos_perso_cod = v_cible
        and ppos_pos_cod = pos_cod
        and pos_etage = etage_numero;

  -- si arène, on revient au point d’entrée
  -- en effaçant les traces de présence dans perso_arene
  if v_arene = 'O' then
    select into nouvelle_position parene_pos_cod from perso_arene
    where parene_perso_cod = v_cible;

    select into v_familier pfam_familier_cod
    from perso_familier
      inner join perso on perso_cod = pfam_familier_cod
    where perso_actif = 'O';
    if found then
      update perso_position
      set ppos_pos_cod = nouvelle_position
      where ppos_perso_cod = v_familier;
    end if;

    update perso_position
    set ppos_pos_cod = nouvelle_position
    where ppos_perso_cod = v_cible;

    -- si on est dans un donjon on ne supprime pas la trace dans perso_arene
    if v_type_arene <> 2 then
      delete from perso_arene
      where parene_perso_cod = v_cible;
    end if;

    code_retour := 'OK';
    code_retour := to_char(nouvelle_position, '99999999');

    return code_retour;
  end if;


  if v_mode = 0 then
    /***************/
    /* Mode normal */
    /***************/
    -- ajout azaghal 02/07/2009 : traitement du sort de résurection

    select into v_position
      rpos_pos_cod
    from perso_resuc, positions
    where rpos_perso_cod = v_cible
          and rpos_pos_cod = pos_cod;
    if found then
      nouvelle_position  := v_position;
      -- on supprime les enreg de la table perso_resuc
      delete from perso_resuc
      where rpos_perso_cod = v_cible
            and rpos_pos_cod = v_position;
    else
      -- on traite les cas hors sortilège de résurection
      select into v_reputation perso_kharma
      from perso
      where perso_cod = v_cible;
      -- Modif Bleda 30/12/11 : On rejoint le glyphe de résurrection s’il existe
      nouvelle_position := rejoint_glyphe_resurrection(v_cible);
      perform f_del_objet(pglyphe_obj_cod) from perso_glyphes
      where pglyphe_perso_cod = v_cible
            and pglyphe_type = 'R';
      delete from perso_glyphes
      where pglyphe_perso_cod = v_cible
            and pglyphe_type = 'R';
      --update perso_glyphes set pglyphe_resurrection = NULL
      --	where pglyphe_perso_cod = v_cible;
      select into pos_temple_cible, nb_temple_cible ptemple_pos_cod, ptemple_nombre
      from perso_temple
      where ptemple_perso_cod = v_cible;

      if nouvelle_position = -1 and found then
        chance_temple := 100 - (getparm_n(32) * nb_temple_cible);
        if chance_temple <= 0 then
          delete from perso_temple where ptemple_perso_cod = v_cible;
        end if;
        des_temple := lancer_des(1, 99);
        if des_temple <= chance_temple then
          nouvelle_position := pos_temple_cible;
          update perso_temple set ptemple_nombre = ptemple_nombre + 1
          where ptemple_perso_cod = v_cible;
        else
          nouvelle_position := -1;
        end if;
      end if;
      if nouvelle_position = -1 then
        -- on regarde sur quel etage on replace
        des_temple := lancer_des(1, 100);
        if des_temple <= 80 then
          nv_etage = etage_ref + 1;
        else
          nv_etage = etage_ref + 2;
        end if;
        if nv_etage > 0 then
          nv_etage := 0;
        end if;
        -- Cas particulier des arènes
        if etage_ref = -100
          THEN
          nv_etage = -100;
        END IF;

        /*Modif Blade pour distinguer dispensaire bon et mauvais au 0 afin de pouvoir délimiter et gérer les zones de droits*/
        if nv_etage in (0, -1) then
          if v_reputation < 0 then
            select into nouvelle_position lpos_pos_cod
            from lieu, lieu_position, positions
            where pos_etage = nv_etage
                  and lpos_pos_cod = pos_cod
                  and lpos_lieu_cod = lieu_cod
                  and lieu_tlieu_cod = 2
                  and lieu_alignement < 0 and lieu_cod != 76252
            order by random()
            limit 1;
          else
            select into nouvelle_position lpos_pos_cod
            from lieu, lieu_position, positions
            where pos_etage = nv_etage
                  and lpos_pos_cod = pos_cod
                  and lpos_lieu_cod = lieu_cod
                  and lieu_tlieu_cod = 2
                  and lieu_alignement >= 0 and lieu_cod != 76252
            order by random()
            limit 1;
          end if;
        else
          select into nb_temple count(lieu_cod)
          from lieu, lieu_position, positions
          where pos_etage = nv_etage
                and lpos_pos_cod = pos_cod
                and lpos_lieu_cod = lieu_cod
                and lieu_tlieu_cod = 2;

          des_temple := lancer_des(1, nb_temple);
          des_temple := des_temple - 1;

          select into nouvelle_position lpos_pos_cod
          from lieu, lieu_position, positions
          where pos_etage = nv_etage
                and lpos_pos_cod = pos_cod
                and lpos_lieu_cod = lieu_cod
                and lieu_tlieu_cod = 2
          offset des_temple
          limit 1;
        end if;
      end if;	-- pos_cible not null
    end if;
  end if;
  if v_mode = 1 then
    /***************/
    /* Mode prison */
    /***************/
    -- 1 - on sauvegarde le temple actuel et on l’efface pour mettre un de ceux de la prison
    select into nb_temple count(lieu_cod)
    from lieu, lieu_position, positions
    where pos_etage = 5
          and lpos_pos_cod = pos_cod
          and lpos_lieu_cod = lieu_cod
          and lieu_tlieu_cod = 20;

    des_temple := lancer_des(1, nb_temple);
    des_temple := des_temple - 1;

    select into nouvelle_position lpos_pos_cod
    from lieu, lieu_position, positions
    where pos_etage = 5
          and lpos_pos_cod = pos_cod
          and lpos_lieu_cod = lieu_cod
          and lieu_tlieu_cod = 20
    offset des_temple
    limit 1;

    select into pos_temple_cible, nb_temple_cible ptemple_pos_cod, ptemple_nombre
    from perso_temple
    where ptemple_perso_cod = v_cible;
    if found then
      update perso_temple
      set ptemple_anc_pos_cod = ptemple_pos_cod, ptemple_anc_nombre = ptemple_nombre
      where ptemple_perso_cod = v_cible;

      update perso_temple set ptemple_pos_cod = nouvelle_position, ptemple_nombre = - 50
      where ptemple_perso_cod = v_cible;
    else
      insert into perso_temple (ptemple_pos_cod, ptemple_nombre, ptemple_anc_pos_cod, ptemple_anc_nombre, ptemple_perso_cod)
      values (nouvelle_position, -50, 0, 0, v_cible);
    end if;
    texte_evt := '[attaquant] a jeté [cible] en prison';
    insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
    values(42, now(), 1, v_cible, texte_evt, 'N', 'N', v_attaquant, v_cible);

    insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
    values(42, now(), 1, v_attaquant, texte_evt, 'O', 'N', v_attaquant, v_cible);
  end if;
  update perso_position
  set ppos_pos_cod = nouvelle_position
  where ppos_perso_cod = v_cible;

  select into nouveau_x, nouveau_y, nouveau_etage pos_x, pos_y, pos_etage
  from positions
  where pos_cod = nouvelle_position;

  texte_evt := '[cible] réapparait au niveau ' || trim(to_char(nouveau_etage, '999999')) || ', en position ' || trim(to_char(nouveau_x, '9999')) || ', ' || trim(to_char(nouveau_y, '9999')) || '.';
  insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
  values(nextval('seq_levt_cod'), 10, now(), 1, v_cible, texte_evt, 'N', 'N', v_attaquant, v_cible);

  code_retour := 'OK';
  code_retour := to_char(nouvelle_position, '99999999');
  return code_retour;
end;$_$;


ALTER FUNCTION public.replace_mort(integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION replace_mort(integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION replace_mort(integer, integer, integer) IS 'Replace un personnage suite à sa mort.';
