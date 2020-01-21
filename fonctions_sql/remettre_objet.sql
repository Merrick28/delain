--
-- Name: remettre_objet(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION remettre_objet(integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* function equipe_objet : remet un objet identifie              */
/*          dand l inventaire                                    */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = perobj_cod                                            */
/* Le code sortie est une chaine séparée par ;                   */
/*    Caractère 1 =>                                             */
/*       0 = tout est OK, on peut équiper                        */
/*      -1 = anomalie + description                              */
/*****************************************************************/
/* Créé le 27/03/2003                                            */
/* Liste des modifications :                                     */
/*   13/01/2016 : Rajout d un test sur objets non-déséquipables  */
/*                Supression conditions particulières 860 et 640 */
/*   11/02/2011 : Rajout d un test sur les objets génériques 860 */
/*                pour les rendre non déséquipables              */
/*   10/12/2007 : Rajout d un test sur les objets génériques 640 */
/*                pour les rendre non déséquipables              */
/*   22/04/2006 : suppression des valeurs négatives dans les     */
/*                codes erreurs                                  */
/*****************************************************************/
declare
  code_retour text;				-- code retour
  personnage alias for $1;	-- perso passé en param
  perobj alias for $2;			-- perobj passé en param
  compt integer;					-- compteur multi usage
  test_t boolean;				-- test non-déséquipable
  pa perso.perso_pa%type;		-- pa du perso
  nom_perso perso.perso_nom%type;	-- nom du perso pour evt
  texte_evt text;				-- texte de l evenement
  num_objet objets.obj_cod%type;	-- numero objet pour evt
  v_type_perso integer;

begin
  code_retour := '0'; -- par défaut, tout est OK
  /********************************************/
  /* Etape 1 : on vérifie que le perso existe */
  /********************************************/
  select into pa,v_type_perso perso_pa,perso_type_perso from perso where perso_cod = personnage;
  if not found then
    code_retour := 'Perso non trouvé !!';
    return code_retour;
  end if;
  -- si le familier a reussi à l'équiper, on l'autrise à le déséquiper
  -- if v_type_perso = 3 then
  --  code_retour := 'Un familier ne peut pas équiper d''objet !!';
  --  return code_retour;
  -- end if;
  /**********************************************/
  /* Etape 2 : on vérifie que le perobj existe  */
  /**********************************************/
  select into compt perobj_cod from perso_objets
  where perobj_cod = perobj;
  if not found then
    code_retour := 'Anomalie sur l''inventaire (perobj_cod inexistant !)';
    return code_retour;
  end if;

  select into test_t (obj_desequipable='N' or (obj_deposable='N' and v_type_perso = 3)) from objets,perso_objets
  where perobj_cod = perobj and perobj_obj_cod = obj_cod;
  if test_t then
    code_retour := 'Cet objet n''est pas déséquipable.';
    return code_retour;
  end if;

  /****************************************/
  /* Etape 3 : on vérifie les PA du perso */
  /****************************************/
  if pa <2 then
    code_retour:= 'Pas assez de PA pour cette action';
    return code_retour;
  end if;
  /****************************************/
  /* Etape 4 : tout est vérifié, on passe */
  /*   à la suite                         */
  /****************************************/
  -- 8.1 on retire les pa au joueur
  update perso
  set perso_pa = pa - 2
  where perso_cod = personnage;
  -- 8.2 on met le marqueur equipe
  update perso_objets
  set perobj_equipe = 'N'
  where perobj_cod = perobj;
  -- 8.3 on met une ligne d evenement
  select into nom_perso perso_nom from perso
  where perso_cod = personnage;
  select into num_objet perobj_obj_cod from perso_objets
  where perobj_cod = perobj;
  texte_evt := '[perso_cod1] a remis l''objet n° '||trim(to_char(num_objet,'99999999'))||' dans son inventaire';
  insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu)
  values(nextval('seq_levt_cod'),6,now(),1,personnage,texte_evt,'O');
  -- 8.4 MAJ automap (pour le cas des casques
  perform update_automap(personnage);
  return code_retour;
end;
$_$;


ALTER FUNCTION public.remettre_objet(integer, integer) OWNER TO delain;