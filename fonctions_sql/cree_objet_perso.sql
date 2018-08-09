CREATE OR REPLACE FUNCTION public.cree_objet_perso(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_objet_perso : Procédure de création d’un objet  */
/*   dans l’inventaire d’un perso                                */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gobj_cod (objet générique)                          */
/*   $2 = le numéro du perso à qui on donne l’objet              */
/* Le code sortie est le obj_cod de l’objet                      */
/*                    ou -1 en cas d’erreur                      */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	-- Paramètres
	v_gobj alias for $1;
	v_perso alias for $2;
begin
	return cree_objet_perso(v_gobj, v_perso, 'O');
end;$function$

CREATE OR REPLACE FUNCTION public.cree_objet_perso(integer, integer, character)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_objet_perso : Procédure de création d’un objet  */
/*   dans l’inventaire d’un perso                                */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gobj_cod (objet générique)                          */
/*   $2 = le numéro du perso à qui on donne l’objet              */
/*   $3 = le statut, identifié ou non, de l’objet                */
/* Le code sortie est le obj_cod de l’objet                      */
/*                    ou -1 en cas d’erreur                      */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	-- Paramètres
	code_retour integer;
	v_gobj alias for $1;
	v_perso alias for $2;
	v_identifie alias for $3;

	-- Variables
	v_code_objet integer;
	texte text;
begin
	/************************************************/
	/* Étape 1 : on insère dans l’étage les valeurs */
	/************************************************/
	code_retour := 0;

	if not exists (select gobj_cod from objet_generique where gobj_cod = v_gobj) then
		code_retour := -1;
		return code_retour;
	end if;

	insert into objets (obj_gobj_cod) values (v_gobj) RETURNING obj_cod INTO v_code_objet;
	if v_gobj in ('723', '829') then
		select into texte obj_text_texte from potions.objet_texte where obj_text_gobj_cod = v_gobj order by random() limit 1;
		update objets set obj_description = texte where obj_cod = v_code_objet;
	end if;

	/*************************/
	/* Étape 2 : on le place */
	/*************************/
	insert into perso_objets (perobj_obj_cod, perobj_perso_cod, perobj_equipe, perobj_identifie)
		values (v_code_objet, v_perso, 'N', v_identifie);
	code_retour := v_code_objet;

	return code_retour;
end;$function$

