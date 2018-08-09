CREATE OR REPLACE FUNCTION public.equipe_objet(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function equipe_objet : equipe un objet identifie             */
/*          de l inventaire                                      */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = obj_cod                                               */
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
	v_perso_niveau integer; -- Le niveau du perso
	v_objet_niveau integer; -- Le niveau min de l’objet
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
	if v_type_personnage = 3 then
		code_retour := '-1;Un familier ne peut pas équiper d’objet !!';
		return code_retour;
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
	select into code_perobj,tobjet,tobjet_libelle,max_obj perobj_cod,gobj_tobj_cod,tobj_libelle,tobj_max_equip
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
	if pa < 2 then
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
/* Etape 7 : on vérifie le nombre d objets de ce type equipés */
/**************************************************************/
	select into compt count(obj_cod)
		from perso_objets,objets,objet_generique
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = tobjet
		and perobj_equipe = 'O';

	if compt >= max_obj then
		code_retour := '1;Vous avez déjà '||trim(to_char(compt,'999999'))||' objet(s) de ce type équipé(s) ! (max '||trim(to_char(max_obj,'999999'))||').';
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
	update perso
		set perso_pa = pa - 2
		where perso_cod = personnage;
-- 9.2 on met le marqueur equipe
	update perso_objets
		set perobj_equipe = 'O'
		where perobj_cod = code_perobj;
-- 9.3 on met une ligne d evenement
	select into nom_perso perso_nom from perso
		where perso_cod = personnage;
	texte_evt := '[perso_cod1] a équipé l’objet n°'||trim(to_char(num_objet,'9999999999999'));
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values(nextval('seq_levt_cod'),6,now(),1,personnage,texte_evt,'O','N');
	return code_retour;
end;
$function$

