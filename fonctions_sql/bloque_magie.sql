--
-- Name: bloque_magie(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function bloque_magie(integer, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* function bloque_magie : tente une resistance                  */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = niveau du sort lancé                                  */
/* Le code sortie est un entier                                  */
/*     0 = resistance ratée                                      */
/*     1 = resistance réussie                                    */
/*     2 = resistance critique                                   */
/*     autre = anomalie :                                        */
/*        -1 = perso non trouvé                                  */
/*        -2 = pas la compétence                                 */
/*****************************************************************/
/* Créé le 01/04/2003                                            */
/* Liste des modifications :                                     */
/*     17/04/2003 : ajout de la procédure amelioration_com       */
/*****************************************************************/
declare
  code_retour integer;
  personnage alias for $1;
  niveau_sort alias for $2;
  compt integer;
  resistance integer;
  valeur_esquive integer;
  des integer;
  -- variable pour les evts
  nom_perso perso.perso_nom%type;
  texte_evt text;
  nv_resitance integer;
  temp_amelioration text;
begin
  code_retour := 0;
  select into resistance,nom_perso
    pcomp_modificateur,perso_nom
  from perso,perso_competences
  where perso_cod = personnage
        and pcomp_perso_cod = perso_cod
        and pcomp_pcomp_cod = 27;
  if not found then
    code_retour := -1;
    return code_retour;
  else

    if resistance is null then
      code_retour := -2;
      return code_retour;
    end if;
  end if; -- compt = 1
  /******************************/
  /* Etape 1 : on lance les des */
  /******************************/
  nv_resitance := resistance - (3 * niveau_sort);
  des := lancer_des(1,100);
  if des >= 96 then -- echec critique
    code_retour := 0;
    texte_evt := nom_perso||' a joliment raté une résistance magique... ';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
    values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
    return code_retour;
  end if; -- des
  if des <= 4 then -- réussite critique
    code_retour := 2;
    texte_evt := nom_perso||' a réussi une résistance magique parfaite. ';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
    values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
    temp_amelioration := ameliore_competence(personnage,27,resistance);
    return code_retour;
  end if; -- des
  if des > nv_resitance then
    if resistance <= getparm_n(1) then
      temp_amelioration := ameliore_competence(personnage,27,resistance);
    end if;
    code_retour := 0;
    texte_evt := nom_perso||' a raté un blocage magique... ';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
    values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
    return code_retour;
  else
    if des <= (valeur_esquive / 3) then -- réussite critique
      code_retour := 2;
      texte_evt := nom_perso||' a réussi un blocage magique parfait. ';
      insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
      values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
      temp_amelioration := ameliore_competence(personnage,27,resistance);
      return code_retour;
    end if; -- reusiite critique
    code_retour := 1;
    texte_evt := nom_perso||' a réussi un blocage magique. ';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
    values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
    temp_amelioration := ameliore_competence(personnage,27,resistance);
    return code_retour;
  end if; -- des > esquive
end;
$_$;


ALTER FUNCTION public.bloque_magie(integer, integer) OWNER TO delain;

--
-- Name: bloque_magie(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function bloque_magie(integer, integer, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* function bloque_magie : tente une resistance                  */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = niveau du sort lancé                                  */
/* Le code sortie est un entier                                  */
/*     0 = resistance ratée                                      */
/*     1 = resistance réussie                                    */
/*     2 = resistance critique                                   */
/*     autre = anomalie :                                        */
/*        -1 = perso non trouvé                                  */
/*        -2 = pas la compétence                                 */
/*****************************************************************/
/* Créé le 01/04/2003                                            */
/* Liste des modifications :                                     */
/*     17/04/2003 : ajout de la procédure amelioration_com       */
/*****************************************************************/
declare
  code_retour integer;
  personnage alias for $1;
  niveau_sort alias for $2;
  v_reussite alias for $3;
  compt integer;
  resistance integer;
  valeur_esquive integer;
  des integer;
  blm integer;
  -- variable pour les evts
  nom_perso perso.perso_nom%type;
  texte_evt text;
  nv_resistance integer;
  temp_amelioration text;
  trace_txt text;
begin
  code_retour := 0;
  select into resistance,nom_perso
    pcomp_modificateur,perso_nom
  from perso,perso_competences
  where perso_cod = personnage
        and pcomp_perso_cod = perso_cod
        and pcomp_pcomp_cod = 27;
  if not found then
    code_retour := -1;
    return code_retour;
  else

    if resistance is null then
      code_retour := -2;
      return code_retour;
    end if;
  end if;
  -- On rajoute le modificateur de Bloque Magie
  resistance := resistance + valeur_bonus(personnage, 'BLM');
  /******************************/
  /* Etape 1 : on lance les des */
  /******************************/
  nv_resistance := round((40 * resistance)/((((niveau_sort-1)*0.5) * v_reussite) + 15));
  if nv_resistance < 10 then
    nv_resistance := 10;
  end if;
  des := lancer_des(1,100);
  if des >= 96 then -- echec critique
    code_retour := 0;
    texte_evt := '[perso_cod1] a joliment raté une résistance magique... ';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
    values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
    return code_retour;
  elsif des <= 4 then -- réussite critique
    code_retour := 2;
    texte_evt := '[perso_cod1] a réussi une résistance magique parfaite. ';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
    values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
    temp_amelioration := ameliore_competence(personnage,27,resistance);
    return code_retour;
  elsif des > nv_resistance then
    if resistance <= getparm_n(1) then
      temp_amelioration := ameliore_competence(personnage,27,resistance);
    end if;
    code_retour := 0;
    texte_evt := '[perso_cod1] a raté un blocage magique... ';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
    values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
    return code_retour;
  else
    if des <= (nv_resistance / 3) then -- réussite critique
      code_retour := 2;
      texte_evt := '[perso_cod1] a réussi un blocage magique parfait. ';
      insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
      values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
      temp_amelioration := ameliore_competence(personnage,27,resistance);
      return code_retour;
    end if; -- reusiite critique
    code_retour := 1;
    texte_evt := '[perso_cod1] a réussi un blocage magique. ';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
    values(nextval('seq_levt_cod'),15,now(),1,personnage,texte_evt,'N');
    temp_amelioration := ameliore_competence(personnage,27,resistance);
    return code_retour;
  end if; -- des > esquive

end;
$_$;


ALTER FUNCTION public.bloque_magie(integer, integer, integer) OWNER TO delain;
