CREATE OR REPLACE FUNCTION public.ramasse_objet_cachette(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ramasse_objet_cachette : récupère un objet qui se    */
/*          trouve dans une cachette. Copie de la fonction       */
/*          ramasse_objet                                        */
/*          dans l inventaire du perso                           */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = obj_cod                                               */
/* Le code sortie est un entier                                  */
/*    0 = tout s est bien passé                                  */
/*    1 = le perso et l arme ne sont pas sur la même position    */
/*    2 = le perso n a pas assez de pa                           */
/*    3 = perso non trouvé                                       */
/*    4 = arme non trouvée                                       */
/*    5 = position perso non trouvée                             */
/*    6 = position arme non trouvée                              */
/*****************************************************************/
/* Créé le 15/05/2006 : copie de ramasse_objet                   */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour text;
	personnage alias for $1;
	num_objet alias for $2;
	pos_perso positions.pos_cod%type;
	pos_objet positions.pos_cod%type;
	compt integer;
	pa integer;
	objet_position_cod objet_position.pobj_cod%type;
	nom_objet objet_generique.gobj_nom_generique%type;
	nom_perso perso.perso_nom%type;
	texte_evt text;
	tobjet type_objet.tobj_libelle%type;
	gobj integer;
	code_monstre integer;
	code_ia text;
	tangibilite text;
	cout_pa integer;
	v_poids_max integer;
	v_poids_actu numeric;
	v_poids_objet numeric;
	
begin
/**************************************/
/* Etape 1                            */
/* On cherche le perso                */
/**************************************/
	select into compt,tangibilite,v_poids_max
		perso_cod,perso_tangible,perso_enc_max
		from perso where perso_cod = personnage;
	v_poids_actu := get_poids(personnage);
	if not found then
		code_retour := '<p>Anomalie : Personnage non trouvé !';
		return code_retour;
	end if;							-- if compt = 0
/**************************************/
/* Etape 2                            */
/* On cherche l objet                 */
/**************************************/
	select into 	nom_objet,
						tobjet,
						v_poids_objet
					obj_nom_generique,
					tobj_libelle,
					obj_poids
		from objets,objet_generique,type_objet
			where obj_cod = num_objet
			and obj_gobj_cod = gobj_cod
			and gobj_tobj_cod = tobj_cod;
	if not found then
		code_retour := '<p>Anomalie : objet non trouvé !';
		return code_retour;
	end if;	
-- on regarde le poids
	if ((v_poids_actu + v_poids_objet) > (v_poids_max * 3))	then
		v_poids_max := v_poids_max * 3;
		code_retour := '<p>Vous ne pouvez pas récupérer un objet qui vous fait dépasser '||trim(to_char(v_poids_max,'99999999'))||' d''encombrement.';
		return code_retour;
	end if;
/**************************************/
/* Etape 3                            */
/* On cherche le pos_cod du perso     */
/**************************************/
	select into pos_perso,nom_perso ppos_pos_cod,perso_nom from perso_position,perso
			where ppos_perso_cod = personnage
			and perso_cod = personnage;
	if not found then
		code_retour := '<p>Anomalie : position perso incohérente !';
		return code_retour;
	end if;							-- if compt_pos != 0
/**************************************/
/* Etape 5 inutile pour les cachettes */
/*          													*/
/**************************************/	
/********************************************/
/* Etape 6   inutile pour les cachettes	    */
/* 																					*/
/********************************************/	
/********************************/
/* Etape 7                      */
/* On verifie les PA du perso   */
/********************************/
	if tangibilite = 'O' then
		cout_pa := getparm_n(41);
	else
		cout_pa := getparm_n(42);
	end if;
	select into pa perso_pa from perso where perso_cod = personnage;
	if pa < cout_pa then
		code_retour := '<p>Anomalie : Pas assez de PA pour ramasser cet objet !';
		return code_retour;
	end if; 							-- if pa < 1
/********************************/
/* Etape 8                      */
/* on valide les changements    */
/********************************/
-- 8.1 : on supprime le arme_position
	delete from objet_position where pobj_cod = objet_position_cod;
--8.1 bis : on supprime l'arme de la cachette
	delete from cachettes_objets where objcache_obj_cod = num_objet;
	select into tobjet,gobj gobj_tobj_cod,gobj_cod from objets,objet_generique
		where obj_cod = num_objet
		and obj_gobj_cod = gobj_cod;
-- 8.1.1 : on regarde à tout hasard si ce n est pas une mimique
	if gobj = 84 then
		code_monstre := cree_monstre_pos(38,pos_objet);
		update perso set perso_cible = personnage
			where perso_cod = code_monstre;
		code_ia := ia_monstre(code_monstre);
		code_retour := '<p>L''objet que vous essayez de récupérer est en fait une mimique, qui vous attaque en se réveillant !';
		return code_retour;
	end if;
	if gobj = 85 then
		code_monstre := cree_monstre_pos(39,pos_objet);
		update perso set perso_cible = personnage
			where perso_cod = code_monstre;
		code_ia := ia_monstre(code_monstre);
		code_retour := '<p>L''objet que vous essayez de récupérer est en fait une mimique, qui vous attaque en se réveillant !';
		return code_retour;
	end if;

-- 8.2 : on rajoute l arme dans l inventaire du perso
-- 8.2.1 : on regarde si l objet est identifie
	if exists (select 1 from perso_identifie_objet
		where pio_perso_cod = personnage
		and pio_obj_cod = num_objet) then
			insert into perso_objets (perobj_cod,perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
				values (nextval('seq_perobj_cod'),personnage,num_objet,'O','N');	
	else
		insert into perso_objets (perobj_cod,perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
			values (nextval('seq_perobj_cod'),personnage,num_objet,'N','N');
	end if;
-- 8.2.2 : si c est une rune, on l identifie

	if tobjet = 5 then
		update perso_objets
			set perobj_identifie = 'O'
			where perobj_perso_cod = personnage
			and perobj_obj_cod = num_objet;	
	end if;	
	-- 8.3 : on enlève les pa au perso
	update perso set perso_pa = (pa - cout_pa) where perso_cod = personnage;
	-- 8.4 : on rajoute un évènement
	texte_evt := '[perso_cod1] a récupéré un objet ('||tobjet||' '||to_char(num_objet,'99999999999')||')';
	insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values (nextval('seq_levt_cod'),3,now(),1,personnage,texte_evt,'O','O');
	code_retour := '<p>L''objet a été récupérer de la cachette. Il est maintenant dans votre inventaire.';
	return code_retour;
end;$function$

