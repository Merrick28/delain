--
-- Name: f_perso_affinite(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function f_perso_affinite(integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*******************************************/
/* f_perso_affinite                         */
/*  params : $1 = perso_cod source         */
/*  params : $2 = perso_cod cible          */
/* return:                                 */
/*     - si perso innactif ou innexistant  */
/*     A dans le cas de 2 monstres (Amis)  */
/*     E si aucune afinité (Ennemis)       */
/*     T si perso de même Triplette        */
/*     C si perso de même Coterie          */
/*******************************************/
declare

  personnage alias for $1;
  cible alias for $2;

  v_type_perso_source	integer;	-- type de perso source
  v_coterie_source integer;		  -- coterie du perso source
  v_compt_cod integer;		      -- compte de la source
  v_type_perso_cible integer;		-- type de peerso cible
  v_coterie_cible integer;		  -- coterie du perso cible
  v_triplette integer;		      -- etat triplette entre les 2 perso

begin

  -- recup des info sdu perso source
  select into  v_type_perso_source, v_coterie_source
     perso_type_perso, COALESCE(pgroupe_groupe_cod,0)
  from perso
  left join groupe_perso on pgroupe_perso_cod = perso_cod and pgroupe_statut = 1
  where perso_cod = personnage and perso_actif='O';
  if not found then
      return '-';
  end if;

    -- récupération du compte joueur (pour identification de la triplette)
  v_compt_cod := 0 ;    -- pas de triplette pour les monstres
  if v_type_perso_source = 3 then
      select into v_compt_cod pcompt_compt_cod from perso_familier join perso_compte on pcompt_perso_cod = pfam_perso_cod where pfam_familier_cod=personnage ;
  elsif v_type_perso_source = 1 then
     select into v_compt_cod  pcompt_compt_cod from perso_compte where pcompt_perso_cod=personnage ;
  end if;

  -- récupération des infos sur la cible
  select into v_type_perso_cible, v_coterie_cible, v_triplette
    perso_type_perso, COALESCE(pgroupe_groupe_cod,0), case when triplette.triplette_perso_cod IS NOT NULL THEN 1 ELSE 0 END
  from perso
  left join groupe_perso on pgroupe_perso_cod = perso_cod and pgroupe_statut = 1
  left join (
        select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso on perso_cod=pcompt_perso_cod where compt_cod=v_compt_cod and perso_actif='O'
        union
        select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso_familier on pfam_perso_cod=pcompt_perso_cod  join perso on perso_cod=pfam_familier_cod where compt_cod=v_compt_cod and perso_actif='O'
    ) as triplette on triplette_perso_cod = perso_cod
  where perso_cod = cible and perso_actif='O';
  if not found then
    return '-';
  end if;


  if (v_type_perso_source = 2 and v_type_perso_cible = 2) then
    return 'A';
  elsif v_type_perso_source != v_type_perso_cible and (v_type_perso_source = 2 or v_type_perso_cible = 2) then
      return 'E';   -- pas d'affinité avec les monstres, les perso sont des ennemies
  elsif v_triplette = 1 then
      return 'T';   -- les perso sont dans la même triplette
  elsif v_coterie_source = v_coterie_cible then
      return 'C';   -- les perso sont dans la même coterie
  end if;

  return 'E';   -- pas d'affinité entre 2 joueurs qui ne partage pas ni triplette, ni coterie

end;$_$;


ALTER FUNCTION public.f_perso_affinite(integer, integer) OWNER TO delain;
