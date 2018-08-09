CREATE OR REPLACE FUNCTION public.depose_objet(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function depose_objet : repose à terre un objet               */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = obj_cod                                               */
/* Le code sortie est une chaine séparée par ;                   */
/*    Caractère 1 =>                                             */
/*       0 = tout est OK, on peut équiper                        */
/*      -1 = anomalie + description                              */
/*****************************************************************/
/* Créée le 27/03/2003                                           */
/* Liste des modifications :                                     */
/*  19/02/2004 : gestion des objets non droppables               */
/*  19/02/2004 : modif du code sortie pour du html               */
/*  26/06/2012 : modif de l’événement enregistré                 */
/*****************************************************************/
declare
	code_retour text;                           -- code retour
	personnage alias for $1;                    -- perso_cod
	num_objet alias for $2;                     -- obj_cod
	pa perso.perso_pa%type;                     -- pa du perso
	v_obj_identifie perso_objets.perobj_identifie%type;
	pos_perso perso_position.ppos_pos_cod%type; -- position actuelle du perso
	perobj perso_objets.perobj_cod%type;        -- le perobj à supprimer
	tobjet objet_generique.gobj_tobj_cod%type;  -- le type d’objet à déposer
	nom_objet objets.obj_nom_generique%type;    -- le nom de l’objet déposé
	texte_evt text;
	nb_trans integer;
	v_deposable text;
begin
	code_retour := '0'; -- par défaut, tout est OK
	texte_evt := '';

	/********************************************/
	/* Etape 1 : on vérifie que le perso existe */
	/********************************************/
	select into pa, pos_perso
		perso_pa, ppos_pos_cod
	from perso, perso_position
	where perso_cod = personnage
		and ppos_perso_cod = perso_cod;
	if not found then
		code_retour := 'Erreur ! Personnage non trouvé !';
		return code_retour;
	end if;

	/*******************************************/
	/* Etape 2 : on vérifie que l’objet existe */
	/*******************************************/
	select into nom_objet, tobjet, v_deposable
		obj_nom_generique, gobj_tobj_cod, obj_deposable
	from objets
	inner join objet_generique on gobj_cod = obj_gobj_cod
	where obj_cod = num_objet;
	if not found then
		code_retour := 'Erreur ! Objet non trouvé !';
		return code_retour;
	end if;

	/********************************************************************/
	/* Etape 3 : on vérifie que l’objet soit dans l’inventaire du perso */
	/********************************************************************/
	select into perobj, v_obj_identifie
		perobj_cod, perobj_identifie
	from perso_objets
	where perobj_perso_cod = personnage
		and perobj_obj_cod = num_objet;
	if not found then
		code_retour := 'Erreur ! L’objet n’est pas dans l’inventaire !';
		return code_retour;
	end if;

	-- petit changement temporaire : on vérfie que ce ne soit pas un poisson
	if tobjet = 16 then
		code_retour := 'Vous ne pouvez pas déposer cet objet !';
		return code_retour;
	end if;

	/*****************************************************/
	/* Etape 4 : on vérifie que le perso ait assez de pa */
	/*****************************************************/
	if pa < 1 then
		code_retour:= 'Erreur ! Pas assez de PA pour cette action !';
		return code_retour;
	end if;

	/**************************************/
	/* Etape 5 : tout est OK, on continue */
	/**************************************/
	-- 5.0 : on enlève les transactions sur cet objet
	delete from transaction
	where tran_obj_cod = num_objet;
	get diagnostics nb_trans = row_count;
	if nb_trans != 0 then
		texte_evt := 'La transaction en cours sur l’objet « ' || nom_objet || ' » (' || trim(to_char(num_objet,'99999999')) || ') a été annulée !';
		insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
		values (nextval('seq_levt_cod'), 17, 'now()', 1, personnage, texte_evt, 'O', 'N');
		texte_evt := '<p>' || texte_evt || '</p>';
	end if;

	-- 5.1 : on enlève les pa du perso
	update perso
	set perso_pa = pa - 1
	where perso_cod = personnage;

	-- 5.2 : on détruit le perso_objet
	code_retour := code_retour || ';' || to_char(perobj,'9999');
	delete from perso_objets
	where perobj_cod = perobj;

	-- 5.3 : si objet déposable on crée un objet_position
	if v_deposable != 'N' then
		insert into objet_position (pobj_cod, pobj_obj_cod, pobj_pos_cod)
		values (nextval('seq_pobj_cod'), num_objet, pos_perso);
		code_retour := texte_evt || '<p>L’objet « ' || nom_objet || ' » (' || trim(to_char(num_objet,'99999999')) || ') a été déposé au sol.</p>'; 
		texte_evt := '[perso_cod1] a posé au sol l’objet « ' || nom_objet || ' » (' || trim(to_char(num_objet,'99999999')) || ')';
	else
		delete from perso_identifie_objet where pio_obj_cod = num_objet;
		delete from objets where obj_cod = num_objet;
		code_retour := texte_evt || '<p>L’objet « ' || nom_objet || ' » (' || trim(to_char(num_objet,'99999999')) || ') a été déposé au sol, mais s’est désintégré immédiatement.</p>'; 
		texte_evt := '[perso_cod1] a posé au sol l’objet « ' || nom_objet || ' » (' || trim(to_char(num_objet,'99999999')) || ') qui s’est désintégré';
	end if; 

	-- 5.4 : on crée la ligne d’événement
	insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
	values(nextval('seq_levt_cod'), 7, now(), 1, personnage, texte_evt, 'O', 'O');

	-- 5.5 : et pour finir on retourne le bon code
	return code_retour;
end;
$function$

