
-- supprimer l'ancienne fonction à 2 parametres pour remplacer par celle si à 4 paramètress dont 2 facultatifs !
DROP FUNCTION IF EXISTS public.equipe_objet(integer, integer);

--
-- Name: equipe_objet(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.equipe_objet(integer, integer, integer default 2, integer default 0) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function equipe_objet : equipe un objet identifie             */
/*          de l inventaire                                      */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = obj_cod                                               */
/*    $3 = nb pa necessaire                                      */
/*    $4 = remplacer equipement                                  */
/* Le code sortie est une chaine séparée par ;                   */
/*    Caractère 1 =>                                             */
/*       0 = tout est OK, on peut équiper                        */
/*      -1 = anomalie + description                              */
/*       1 = trop d obejts de ce type déjà équipés               */
/* Ensuite, dans l ordre                                         */
/*   -------------------------------------------                 */
/*   | Si 1 (trop d objets de ce type équipés) |                 */
/*   -------------------------------------------                 */
/*     1 : libelle du type objet                                 */
/*****************************************************************/
/* Créé le 19/03/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour text;
	personnage alias for $1;
	num_objet alias for $2;
	nb_pa_requis alias for $3;
	remplacement alias for $4;  -- si = 1, on va essayer de déséquiper un objet pour équiper celui-la
-- variables de vérification
	compt integer;
	code_perobj perso_objets.perobj_cod%type;
	obj_identifie perso_objets.perobj_identifie%type;
	obj_equipe perso_objets.perobj_equipe%type;
	pa perso.perso_pa%type;
	tobjet type_objet.tobj_cod%type;
	tobjet_libelle type_objet.tobj_libelle%type;
-- variables liées aux évènements
	texte_evt text;
	nom_perso perso.perso_nom%type;
	nb_trans integer;
	v_type_personnage integer;
	max_obj integer;
	v_perobj_cod integer; -- perobj_cod d'un objet déjà équipé du même type
	v_perso_niveau integer; -- Le niveau du perso
	v_objet_niveau integer; -- Le niveau min de l’objet
	v_est_equipable integer; -- 0 pas équipable, -1 pas equipable pour les familiers et 1 équipable
	v_perso_nb_mains integer; -- nombre de mains occupé
	v_objet_nb_mains integer; -- nombre de mains nécéssaire pour l'objet
begin
	code_retour := '0'; -- par défaut, tout est OK
/********************************************/
/* Etape 1 : on vérifie que le perso existe */
/********************************************/
	select into pa,v_type_personnage, v_perso_niveau perso_pa,perso_type_perso, perso_niveau from perso where perso_cod = personnage;
	if not found then
		code_retour := '-1;Perso non trouvé !!';
		return code_retour;
	end if;
/*****************************************************************/
/* Etape 1.1 : on vérifie si les pré-requis d'équipement sont là */
/*****************************************************************/
  select obj_verif_perso_condition_equip(personnage,num_objet) into v_est_equipable ;
  if v_est_equipable!=1 then
    if v_type_personnage = 3 then
      code_retour := '-1;Un familier ne peut pas équiper d’objet !!';
      return code_retour;
    else
      code_retour := '-1;Vous ne disposez pas des pré-requis nécéssaires pour équiper cet objet !!';
      return code_retour;
    end if;
  end if;
/********************************************/
/* Etape 2 : on vérifie que l objet existe  */
/********************************************/
	select into compt, v_objet_niveau obj_cod, obj_niveau_min from objets
		where obj_cod = num_objet;
	if not found then
		code_retour := '-1;Objet inexistant';
		return code_retour;
	end if;
/***********************************************************/
/* Etape 3 : on vérifie que l objet est dans l inventaire  */
/***********************************************************/
	select into code_perobj,tobjet,tobjet_libelle,max_obj,v_objet_nb_mains perobj_cod,gobj_tobj_cod,tobj_libelle,tobj_max_equip, gobj_nb_mains
			from perso_objets,objets,objet_generique,type_objet
			where perobj_perso_cod = personnage
			and perobj_obj_cod = num_objet
			and obj_cod = num_objet
			and obj_gobj_cod = gobj_cod
			and gobj_tobj_cod = tobj_cod;
	if not found then
		code_retour := '-1;L’objet n’est pas dans l’inventaire';
		return code_retour;
	end if;
/******************************************************/
/* Etape 4 : on vérifie que l objet est identifie     */
/******************************************************/
	select into obj_identifie perobj_identifie
		from perso_objets
		where perobj_cod = code_perobj;
	if obj_identifie = 'N' then
		code_retour := '-1;L’objet n’est pas identifié';
		return code_retour;
	end if;
/****************************************/
/* Etape 5 : on vérifie les PA du perso */
/****************************************/
	if pa < nb_pa_requis then
		code_retour:= '-1;Pas assez de PA pour cette action';
		return code_retour;
	end if;
/**********************************************************/
/* Etape 6 : on vérifie que l objet n est pas deja equipe */
/**********************************************************/
	select into obj_equipe perobj_equipe
		from perso_objets
		where perobj_cod = code_perobj;
	if obj_equipe = 'O' then
		code_retour := '-1;L’objet est déjà équipé';
		return code_retour;
	end if;
/**************************************************************/
/* Etape 7a : on vérifie le nombre d objets de ce type equipés */
/**************************************************************/
	select into compt count(obj_cod)
		from perso_objets,objets,objet_generique
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = tobjet
		and perobj_equipe = 'O';

  v_perobj_cod := null ;
	if compt >= max_obj then
	  if remplacement = 0 then
        code_retour := '1;Vous avez déjà '||trim(to_char(compt,'999999'))||' objet(s) de ce type équipé(s) ! (max '||trim(to_char(max_obj,'999999'))||').';
        return code_retour;
    else
        /* on va essayer de trouver un objet déjà équipé pour le replacer par celui-la */
        select  perobj_cod into v_perobj_cod
            from perso_objets,objets,objet_generique
            where perobj_perso_cod = personnage
            and perobj_obj_cod = obj_cod
            and obj_gobj_cod = gobj_cod
            and gobj_tobj_cod = tobjet
            and perobj_equipe = 'O'
            and gobj_desequipable='O'
            order by perobj_cod desc limit 1;
        if not found then
          code_retour := '1;Vous avez déjà '||trim(to_char(compt,'999999'))||' objet(s) de ce type équipé(s) ! (max '||trim(to_char(max_obj,'999999'))||').';
          return code_retour;
        end if;

    end if;
	end if;

/****************************************************************************/
/* Etape 7b : on vérifie q'on dispose d'assez de mains libres                 */
/****************************************************************************/
select into v_perso_nb_mains coalesce(sum(gobj_nb_mains),0)
			from perso_objets,objets,objet_generique
			where perobj_perso_cod = personnage
			and obj_cod = perobj_obj_cod
			and gobj_cod= obj_gobj_cod
			and perobj_equipe='O' ;

if (v_perso_nb_mains + v_objet_nb_mains) > 2 and v_perobj_cod is null then
  code_retour := '-1;Vous n''avez pas de main libre pour équiper cet objet !' ;
  return code_retour;
end if;

/****************************************************************************/
/* Etape 8 : on vérifie que l’objet et le perso ont des niveaux compatibles */
/****************************************************************************/
	if v_perso_niveau < v_objet_niveau then
		code_retour := '-1;Vous ne pouvez pas équiper cet objet avant d’avoir atteint le niveau ' || v_objet_niveau::text;
		return code_retour;
	end if;


/****************************************/
/* Etape 9 : tout est vérifié, on passe */
/*   à la suite                         */
/****************************************/
-- 9.0 on enlève les transactions sur cet objet
	delete from transaction
		where tran_obj_cod = num_objet;
	get diagnostics nb_trans = row_count;
	if nb_trans != 0 then
		texte_evt := 'La transaction en cours sur l’objet n°'||trim(to_char(num_objet,'999999999'))||' a été annulée !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values (nextval('seq_levt_cod'),17,'now()',1,personnage,texte_evt,'O','N');
	end if;

-- 9.1 on retire les pa au joueur
	update perso	set perso_pa = pa - nb_pa_requis	where perso_cod = personnage;

-- 9.2.1 on commence par déséquiper un autre objet en cas de remplacement
   if v_perobj_cod is not null then
	    update perso_objets	set perobj_equipe = 'N'	where perobj_cod = v_perobj_cod;
	 end if;

-- 9.2.2 on met le marqueur equipe
	update perso_objets	set perobj_equipe = 'O'	where perobj_cod = code_perobj;

-- 9.3 on met une ligne d evenement
	select into nom_perso perso_nom from perso	where perso_cod = personnage;
	texte_evt := '[perso_cod1] a équipé l’objet n°'||trim(to_char(num_objet,'9999999999999'));
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values(nextval('seq_levt_cod'),6,now(),1,personnage,texte_evt,'O','N');

  -- 2021-06-02 - marlyza - automap (pour le cas des casques et autre bonus de vu)
  perform update_automap(personnage);

	return code_retour;
end;
$_$;


ALTER FUNCTION public.equipe_objet(integer, integer, integer, integer) OWNER TO delain;
