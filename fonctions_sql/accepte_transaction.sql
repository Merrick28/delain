--
-- Name: accepte_transaction(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION accepte_transaction(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/**********************************************/
/* fonction accepte_transaction               */
/*   accepte la transaction passée en $1      */
/* retourne un texte séparé par ;             */
/*  0 = code retour (0 pour OK, -1 pour BAD)  */
/*  1 = code erreur si bad                    */
/**********************************************/
/* créé le 20/05/2003                         */
/* 27/11/2008 Inverse attaquant et cible      */
/**********************************************/
declare
  code_retour text;
  v_transaction alias for $1;
  -- variables de calcul
  compt integer;
  texte_evt text;
  -- variables de la transaction
  v_vendeur integer;
  v_acheteur integer;
  v_po integer;
  v_prix integer;
  v_objet integer;
  v_poids_max integer;
  v_poids_actu numeric;
  v_poids_objet numeric;
  v_deposable text;
  v_ramassable integer;
  v_type_objet text;
  v_objet_nom text;
  gobj integer;
  v_temp integer;
begin

  /***********************************************/
  /* Etape 1 : on regarde si la transaction      */
  /*   existe toujours                           */
  /***********************************************/
  select into compt tran_cod from transaction where tran_cod = v_transaction;
  if not found then
    code_retour := '-1;La transaction n’est plus active.';
    return code_retour;
  end if;
  /***********************************************/
  /* Etape 2 : on récupère les infos de la       */
  /*   transaction                               */
  /***********************************************/
  select into v_vendeur, v_acheteur, v_prix, v_objet tran_vendeur, tran_acheteur, tran_prix, tran_obj_cod
  from transaction
  where tran_cod = v_transaction;
  select into v_po, v_poids_max, v_poids_actu perso_po, perso_enc_max, get_poids(perso_cod) from perso
  where perso_cod = v_acheteur;
  select into v_poids_objet, v_deposable, v_objet_nom, v_type_objet, gobj, v_ramassable obj_poids, obj_deposable, obj_nom, tobj_libelle, gobj_cod, obj_verif_perso_condition_inv(v_acheteur,v_objet)
  from objets, objet_generique, type_objet
  where obj_cod = v_objet
        and obj_gobj_cod = gobj_cod
        and gobj_tobj_cod = tobj_cod;
  if ((v_poids_actu + v_poids_objet) > (v_poids_max * 3))	then
    v_poids_max := v_poids_max * 3;
    code_retour := '-1;<p>Vous ne pouvez accepter un objet qui vous fait dépasser ' || trim(to_char(v_poids_max, '99999999')) || ' d’encombrement.';
    return code_retour;
  end if;
  if v_deposable = 'N' then
    code_retour := '-1;<p>Cet objet est <b>non déposable</b> et ne peut être transféré au moyen d’une transaction.';
    return code_retour;
  end if;
  if v_ramassable <= 0 then
    code_retour := '-1;<p>L''acheteur <b>ne peut pas transporter</b> cet objet, il ne peut être transféré au moyen d’une transaction.';
    return code_retour;
  end if;


  -- Interdit de prendre une transaction pendant un défi
  if exists(select 1 from defi where defi_statut = 1 and v_acheteur in (defi_lanceur_cod, defi_cible_cod))
     or exists (select 1 from defi
    inner join perso_familier on pfam_perso_cod in (defi_lanceur_cod, defi_cible_cod)
  where defi_statut = 1 and v_acheteur = pfam_familier_cod)
     or exists (select 1 from defi
    inner join perso_familier on pfam_perso_cod in (defi_lanceur_cod, defi_cible_cod)
  where defi_statut = 1 and v_vendeur = pfam_familier_cod)
  then
    code_retour := '-1;<p>Il est interdit d’acheter un objet pendant un défi !</p>';
    return code_retour;
  end if;
  /***********************************************/
  /* Etape 3 : on regarde qu il y ait assez de   */
  /*   brouzoufs pour acheter l objet            */
  /***********************************************/
  if v_prix > v_po then
    code_retour := '-1;Vous n’avez pas assez de brouzoufs pour acheter cet objet !';
    return code_retour;
  end if;
  /********************************/
  /* Etape 3.1                    */
  /* Modif Bleda 30/01/11         */
  /* Glyphe de résurrection ?     */
  /********************************/
  if gobj = 859 then -- TODO Type d’objet
    select into v_temp 1 from perso_glyphes
    where pglyphe_perso_cod = v_acheteur
          --and pglyphe_resurrection is not NULL
          and pglyphe_obj_cod = v_objet;
    if found then
      code_retour := '-1;Vous ne pouvez porter votre propre glyphe de résurrection !';
      return code_retour;
    end if;
  end if;
  /***********************************************/
  /* Etape 4 : tout semble OK, on valide         */
  /***********************************************/
  -- 4.1 : on transfère le perso_objets
  -- ajout SD : on supprimer d'un côté pour recréer de l’autre
  -- pour délencher le trigger (un update de perso ne suffit pas)
  delete from perso_objets
  where 	perobj_perso_cod = v_vendeur
         and perobj_obj_cod = v_objet;
  if not found then
    code_retour := '-1; Le vendeur ne possède pas cet objet !';
    return code_retour;
  end if;
  insert into perso_objets (perobj_perso_cod, perobj_obj_cod)
  values (v_acheteur, v_objet);
  -- accesoirement, on vérifie pour l’identification
  select into compt
    pio_perso_cod
  from perso_identifie_objet
  where pio_perso_cod = v_acheteur
        and pio_obj_cod = v_objet
        or exists
        (select 1 from objets, objet_generique, type_objet
      where obj_gobj_cod = gobj_cod
            and gobj_tobj_cod = tobj_cod
            and tobj_identifie_auto = 1
            and obj_cod = v_objet)
        or exists (select 1
                   from perso_identifie_objet
                   where pio_perso_cod = v_vendeur
                         and pio_obj_cod = v_objet);
  if found then
    update perso_objets
    set perobj_identifie = 'O'
    where perobj_perso_cod = v_acheteur
          and perobj_obj_cod = v_objet;
  else
    v_objet_nom := 'objet non identifié';
  end if;
  -- 4.2 : on enlève les brouzoufs à l’acheteur
  update perso
  set perso_po = perso_po - v_prix
  where perso_cod = v_acheteur;
  -- 4.3 : on les rajoute au vendeur
  update perso
  set perso_po = perso_po + v_prix
  where perso_cod = v_vendeur;
  -- 4.4 : on achève la transaction
  delete from transaction
  where tran_cod = v_transaction;
  -- 4.5 : on ajoute un évènements
  texte_evt := '[attaquant] a acheté un objet à [cible] <i>(' || trim(to_char(v_objet, '99999999')) || ' / ' || v_type_objet || ' / ' || v_objet_nom || ')</i>';
  perform insere_evenement(v_acheteur, v_vendeur, 17, texte_evt, 'N', '[obj_cod]=' || v_objet::text);

  code_retour := '0';
  return code_retour ;
end;$_$;


ALTER FUNCTION public.accepte_transaction(integer) OWNER TO delain;

--
-- Name: FUNCTION accepte_transaction(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION accepte_transaction(integer) IS 'Accepte une transaction (noooon, sans blague...)';

