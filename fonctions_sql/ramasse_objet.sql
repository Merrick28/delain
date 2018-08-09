CREATE OR REPLACE FUNCTION public.ramasse_objet(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ramasse_arme : ramasse une arme posée au sol et la   */
/*          dans l’inventaire du perso                           */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = arm_cod                                               */
/* Le code sortie est un entier                                  */
/*    0 = tout s est bien passé                                  */
/*    1 = le perso et l’arme ne sont pas sur la même position    */
/*    2 = le perso n a pas assez de pa                           */
/*    3 = perso non trouvé                                       */
/*    4 = arme non trouvée                                       */
/*    5 = position perso non trouvée                             */
/*    6 = position arme non trouvée                              */
/*****************************************************************/
/* Créé le 11/03/2003                                            */
/* Liste des modifications :                                     */
/*  18/12/2003 : Retour en texte pour intégrer les mimiques      */
/*  26/06/2012 : Ajout du nom de l’objet dans les événements     */
/*  26/06/2012 : Rationalisation des requêtes                    */
/*****************************************************************/
declare
	code_retour text;
	personnage alias for $1;
	num_objet alias for $2;
	pos_perso positions.pos_cod%type;
	pos_objet positions.pos_cod%type;
	pa integer;
	objet_position_cod objet_position.pobj_cod%type;
	nom_objet objets.obj_nom_generique%type;
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
	v_temp integer;
begin
	/**************************************/
	/* Etape 1                            */
	/* On cherche le perso                */
	/**************************************/
	select into tangibilite, v_poids_max, pos_perso, v_poids_actu, pa
		perso_tangible, perso_enc_max, ppos_pos_cod, get_poids(perso_cod), perso_pa
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod
	where perso_cod = personnage;
	if not found then
		code_retour := '<p>Anomalie : personnage ou position non trouvés !</p>';
		return code_retour;
	end if;

	/**************************************/
	/* Etape 2                            */
	/* On cherche l’objet                 */
	/**************************************/
	select into nom_objet, tobjet, gobj, v_poids_objet, pos_objet, objet_position_cod
		obj_nom_generique, tobj_libelle, gobj_cod, obj_poids, pobj_pos_cod, pobj_cod
	from objets
	inner join objet_generique on gobj_cod = obj_gobj_cod
	inner join type_objet on tobj_cod = gobj_tobj_cod
	inner join objet_position on pobj_obj_cod = obj_cod
	where obj_cod = num_objet;
	if not found then
		code_retour := '<p>Anomalie : objet ou position non trouvés !</p>';
		return code_retour;
	end if;

	-- Interdit de ramasser pendant un défi
	if exists(select 1 from defi where defi_statut = 1 and personnage in (defi_lanceur_cod, defi_cible_cod)
		UNION ALL select 1 from defi
			inner join perso_familier on pfam_perso_cod in (defi_lanceur_cod, defi_cible_cod)
			where defi_statut = 1 and pfam_familier_cod = personnage)
	then
		code_retour := '<p>Anomalie : il est interdit de ramasser un objet pendant un défi !</p>';
		return code_retour;
	end if;

	-- on regarde le poids
	if ((v_poids_actu + v_poids_objet) > (v_poids_max * 3))	then
		v_poids_max := v_poids_max * 3;
		code_retour := '<p>Vous ne pouvez ramasser un objet qui vous fait dépasser '||trim(to_char(v_poids_max,'99999999'))||' d’encombrement.</p>';
		return code_retour;
	end if;

	/********************************************/
	/* Etape 3                                  */
	/* On verifie la correspondance des pos_cod */
	/********************************************/	
	if pos_perso != pos_objet then
		code_retour := '<p>Anomalie : le perso et l’objet ne sont pas sur la même position !</p>';
		return code_retour;
	end if;

	/********************************/
	/* Etape 4                      */
	/* On verifie les PA du perso   */
	/********************************/
	if tangibilite = 'O' then
		cout_pa := getparm_n(41);
	else
		cout_pa := getparm_n(42);
	end if;
	if pa < cout_pa then
		code_retour := '<p>Anomalie : pas assez de PA pour ramasser cet objet !</p>';
		return code_retour;
	end if;

	/********************************/
	/* Etape 5                    */
	/* Modif Bleda 30/01/11         */
	/* Glyphe de résurrection ?     */
	/********************************/
	if gobj = 859 then
		select into v_temp 1 from perso_glyphes 
		where pglyphe_perso_cod = personnage
		--and pglyphe_resurrection is not NULL
		and pglyphe_obj_cod = num_objet;

		if found then
			code_retour := '<p>Erreur : vous ne pouvez ramasser votre propre glyphe de résurrection !</p>';
			return code_retour;
		end if;
	end if;

	/********************************/
	/* Etape 6                      */
	/* on valide les changements    */
	/********************************/
	-- 6.1 : on supprime le objet_position
	delete from objet_position where pobj_cod = objet_position_cod;

	-- 6.2 : on regarde à tout hasard si ce n’est pas une mimique
	if gobj = 84 then
		code_monstre := cree_monstre_pos(38, pos_objet);

		update perso set perso_cible = personnage
		where perso_cod = code_monstre;

		code_ia := ia_monstre(code_monstre);
		code_retour := '<p>L’objet que vous essayez de ramasser est en fait une mimique, qui vous attaque en se réveillant !</p>';
		return code_retour;
	end if;
	if gobj = 85 then
		code_monstre := cree_monstre_pos(39, pos_objet);

		update perso set perso_cible = personnage
		where perso_cod = code_monstre;

		code_ia := ia_monstre(code_monstre);
		code_retour := '<p>L’objet que vous essayez de ramasser est en fait une mimique, qui vous attaque en se réveillant !</p>';
		return code_retour;
	end if;

	-- 6.3 : on rajoute l’objet dans l’inventaire du perso
	-- 6.3.1 : on regarde si l’objet est identifié
	if exists (select 1 from perso_identifie_objet
		where pio_perso_cod = personnage
		and pio_obj_cod = num_objet)
	then
		insert into perso_objets (perobj_cod, perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe)
		values (nextval('seq_perobj_cod'), personnage, num_objet, 'O', 'N');	
	else
		insert into perso_objets (perobj_cod, perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe)
		values (nextval('seq_perobj_cod'), personnage, num_objet, 'N', 'N');
	end if;

	-- 6.4 : on enlève les pa au perso
	update perso set perso_pa = (pa - cout_pa) where perso_cod = personnage;

	-- 6.5 : on rajoute un événement
	texte_evt := '[perso_cod1] a ramassé un objet « ' || nom_objet || ' » (' || tobjet || ' ' || to_char(num_objet, '99999999999') || ')';
	insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
	values (nextval('seq_levt_cod'), 3, now(), 1, personnage, texte_evt, 'O', 'O');

	code_retour := '<p>L’objet « ' || nom_objet || ' » a été ramassé. Il est maintenant dans votre inventaire.</p>';
	return code_retour;
end;$function$

