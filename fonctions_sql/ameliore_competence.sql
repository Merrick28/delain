--
-- Name: ameliore_competence(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ameliore_competence(integer, integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* function ameliore_competence : essaie d ameliorer une comp    */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = comp_cod                                              */
/*    $3 = valeur actuelle de compétence                         */
/* Le code sortie est une chaine séparée par ;                   */
/*    Caractère 1 =>                                             */
/*       0 = tout est OK                                         */
/*      -1 = anomalie                                            */
/* Ensuite, dans l ordre                                         */
/*      Valeur des dés lancés                                    */
/*      1 pour amélioration, 0 si loupé                          */
/*      Nouvelle valeur de compétence                            */
/*****************************************************************/
/* Créé le 19/03/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
  code_retour text;
  personnage alias for $1;
  code_competence alias for $2;
  toto alias for $3;
  valeur_comp integer;
  des integer;
  temp_amelioration integer;
  nouvelle_valeur integer;
  -- pour evts
  texte_evt text;
  nom_personnage perso.perso_nom%type;
  nom_competence competences.comp_libelle%type;
begin
  select into valeur_comp
    pcomp_modificateur
  from perso_competences
  where pcomp_perso_cod = personnage
        and pcomp_pcomp_cod = code_competence;
  code_retour := '0;'; -- par défaut, tout est OK
  if valeur_comp < 100 then
    des := lancer_des(1,100);
  else
    des := lancer_des(1,valeur_comp);
  end if;
  if des < valeur_comp then -- on échoue
    code_retour := trim(to_char(des,'999'))||';0;'||trim(to_char(valeur_comp,'999'));
    return code_retour;
  else
    /********************************/
    /* 1 - calcul de l amélioration */
    /********************************/
    if (valeur_comp <= 25) then
      temp_amelioration := lancer_des(1,4);
    end if;
    if (valeur_comp > 25) and (valeur_comp <= 50) then
      temp_amelioration := lancer_des(1,3);
    end if;
    if (valeur_comp > 50) and (valeur_comp <= 75) then
      temp_amelioration := lancer_des(1,2);
    end if;
    if valeur_comp > 75 then
      temp_amelioration := 1;
    end if;
    nouvelle_valeur := valeur_comp + temp_amelioration;
    /********************************/
    /* 2 - Modification des données */
    /********************************/
    update perso_competences
    set pcomp_modificateur = nouvelle_valeur
    where pcomp_pcomp_cod = code_competence
          and pcomp_perso_cod = personnage;
    /********************************/
    /* 3 - Insertion d un evenement */
    /********************************/
    -- 3.1 : on recherche les infos nécessaires au texte
    select into nom_personnage,nom_competence perso_nom,comp_libelle
    from perso,competences
    where perso_cod = personnage
          and comp_cod = code_competence;
    texte_evt := '[perso_cod1] a amélioré sa compétence '||nom_competence||', la passant à '||trim(to_char(nouvelle_valeur,'9999'))||'%.';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
    values(nextval('seq_levt_cod'),12,now(),1,personnage,texte_evt,'O','N');
    /*******************/
    /* 4 - Code retour */
    /*******************/
    code_retour := trim(to_char(des,'999'))||';1;'||trim(to_char(nouvelle_valeur,'999'));
    return code_retour;
  end if;
end;
$_$;


ALTER FUNCTION public.ameliore_competence(integer, integer, integer) OWNER TO delain;