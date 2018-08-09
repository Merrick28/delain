CREATE OR REPLACE FUNCTION public.identifier_objet(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function identifier_objet : identifie un objet de l’inventaire*/
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = obj_cod                                               */
/* Le code sortie est une chaine séparée par ;                   */
/*    Caractère 1 =>                                             */
/*       0 = tout est OK                                         */
/*      -1 = anomalie + description                              */
/* Ensuite, dans l ordre                                         */
/*      comp_cod                                                 */
/*      libelle de la competence                                 */
/*      pourcentage dans la competence                           */
/*      lancer des                                               */
/*      0 si comp ratée, 1 si réussie , -1 si echec auto         */
/* (si 0 ou -1, on arrête ici )                                  */
/*      num objet pour rappel                                    */
/*      nombre de px gagnés                                      */
/*      lancer des pour amelioration competence                  */
/*      1 si amélioration, 0 sinon                               */
/* (si 0, on arrête ici)                                         */
/*      nouvelle valeur compétence                               */
/*****************************************************************/
/* Créé le 19/03/2003                                            */
/* Liste des modifications :                                     */
/* 19/04/2006 : changement de l’amélioration pour rajouter       */
/*              les Pxs en cas de réussite                       */
/* 28/05/2012 : Rajout renommée artisanale                       */
/*****************************************************************/
declare
	code_retour text;
	limite_exp integer;
	personnage alias for $1;
	num_objet alias for $2;
-- variables de vérification
	compt integer;
	code_perobj perso_objets.perobj_cod%type;
	obj_identifie perso_objets.perobj_identifie%type;
	pa perso.perso_pa%type;
-- variables liées à la compétence
	comp_ident competences.comp_cod%type;
	nom_comp competences.comp_libelle%type;
	valeur_comp perso_competences.pcomp_modificateur%type;
	valeur_comp_modifie integer;
	des integer;
	amelioration integer;
	nouvelle_valeur_comp integer;
	temp_ameliore_competence text;
	gain_renommee numeric;		-- gain (ou perte) de renommée artisanale
-- variables liées aux évènements
	texte_evt text;
	nom_perso perso.perso_nom%type;
begin
	limite_exp := getparm_n(1);
	code_retour := '0'; -- par défaut, tout est OK
	gain_renommee := 0.1;
/********************************************/
/* Etape 1 : on vérifie que le perso existe */
/********************************************/
	select into pa perso_pa from perso where perso_cod = personnage;
	if not found then
		code_retour := '-1;Perso non trouvé !!';
		return code_retour;
	end if;
/********************************************/
/* Etape 2 : on vérifie que l’objet existe  */
/********************************************/
	select into compt obj_cod from objets
		where obj_cod = num_objet;
	if not found then
		code_retour := '-1;Objet inexistant';
		return code_retour;
	end if;	
/***********************************************************/
/* Etape 3 : on vérifie que l’objet est dans l’inventaire  */
/***********************************************************/
	select into code_perobj perobj_cod
			from perso_objets
			where perobj_perso_cod = personnage
			and perobj_obj_cod = num_objet;
	if not found then
		code_retour := '-1;L’objet n’est pas dans l’inventaire';
		return code_retour;
	end if;
/********************************************************/
/* Etape 4 : on vérifie que l objet n est pas identifie */
/********************************************************/
	select into obj_identifie perobj_identifie
		from perso_objets
		where perobj_cod = code_perobj;
	if obj_identifie = 'O' then
		code_retour := '-1;L’objet est déjà identifié';
		return code_retour;
	end if;
/****************************************/
/* Etape 5 : on vérifie les PA du perso */
/****************************************/
	if pa < 2 then
		code_retour:= '-1;Pas assez de PA pour cette action';
		return code_retour;
	end if;
/****************************************/
/* Etape 6 : tout est vérifié, on passe */
/*   à la suite                         */
/****************************************/
-- 6.1 on retire les pa au joueur
	update perso
		set perso_pa = pa - 2
		where perso_cod = personnage;
-- 6.2 on recherche le type d objet pour déterminer la compétence à utiliser
	select into comp_ident tobj_ident_comp_cod
		from type_objet,objets,objet_generique
		where obj_cod = num_objet
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = tobj_cod;
	code_retour := code_retour || ';' || trim(to_char(comp_ident, '99999'));	-- retour pos 1

-- 6.3 on recherche le libellé de la compétence et son pourcentage
	select into nom_comp,valeur_comp comp_libelle,pcomp_modificateur
		from competences,perso_competences
		where comp_cod = comp_ident
		and pcomp_pcomp_cod = comp_cod
		and pcomp_perso_cod = personnage;
-- 6.3 bis : concentration
	select into compt concentration_perso_cod from concentrations
		where concentration_perso_cod = personnage;
	if found then
		valeur_comp_modifie := valeur_comp + 20;
		delete from concentrations where concentration_perso_cod = personnage;
	else
		valeur_comp_modifie := valeur_comp;
	end if;
	code_retour := code_retour || ';' || nom_comp || ';' || trim(to_char(valeur_comp_modifie, '999')); -- retour pos 2 et 3

-- 6.4 on fait un jet de dés et on regarde si on réussit la compétence
	des := lancer_des(1,100);
	code_retour := code_retour||';'||trim(to_char(des,'999')); -- retour pos 4
	if des > 96 then -- echec automatique
		gain_renommee := gain_renommee * (-2);
		code_retour := code_retour||';-1'; --retour pos 5
		return code_retour;
	end if;
	if des > valeur_comp_modifie then 
		gain_renommee := gain_renommee * (-1);
		if valeur_comp <= limite_exp then
			temp_ameliore_competence := ameliore_competence_px(personnage, comp_ident, valeur_comp);
			code_retour := code_retour||';0;'||temp_ameliore_competence||';'; -- retour pos 5,6,7,8
		end if;
		code_retour := code_retour||';0;'; -- retour pos 5,6,7,8
		return code_retour;
	end if;
/****************************************/
/* Etape 7 : la compétence est réussie  */
/****************************************/
	code_retour := code_retour||';1;'||trim(to_char(num_objet,'99999999')); -- retour pos 5,6
-- 7.1 on met le marqueur identifié
	update perso_objets
		set perobj_identifie = 'O'
		where perobj_cod = code_perobj;
-- 7.2 on attribue des PX au joueur
	update perso
		set perso_px = perso_px + 1,
		perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee
		where perso_cod = personnage;
-- 7.3 on met une ligne d evenement
	select into nom_perso perso_nom from perso
		where perso_cod = personnage;
	texte_evt := '[perso_cod1] a identifié l’objet n°' || trim(to_char(num_objet, '999999999'));
	insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
		values(nextval('seq_levt_cod'), 5, now(), 1, personnage, texte_evt, 'O', 'N');
	code_retour := code_retour||';1;'; -- retour pos 7
/**************************************************/
/* Etape 8 : on essaie d ameliorer la competence  */
/**************************************************/
	temp_ameliore_competence := ameliore_competence_px(personnage, comp_ident, valeur_comp);
	code_retour := code_retour||temp_ameliore_competence;
	return code_retour;
end;
$function$

