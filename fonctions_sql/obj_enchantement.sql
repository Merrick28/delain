CREATE OR REPLACE FUNCTION public.obj_enchantement(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/******************************************************/
/* fonction obj_enchantement                          */
/* Params :                                           */
/*  $1 = perso_cod                                    */
/*  $2 = enc_cod                                      */
/*  $3 = obj_cod à enchanter                          */
/* Détermine si un perso donné possède bien tous les  */
/* objets nécessaires pour enchanter un objet         */
/* Retours :                                          */
/*   0 = ne possède pas les objets                    */
/*   1 = possède les objets                           */
/******************************************************/
/* Créé le 04/05/2006 par Merrick                     */
/******************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_enc_cod alias for $2;
	v_obj_cod alias for $3;
	v_nb_obj_perso integer;
	ligne record;
begin
	-- On boucle sur les objets nécessaires.
	-- si on n'a pas le bon nombre, alors on retourne un 0
	for ligne in select * from enc_objets where oenc_enc_cod = v_enc_cod loop
		select into v_nb_obj_perso count(obj_cod)
			from objets,perso_objets
			where perobj_perso_cod = personnage
			and perobj_identifie = 'O'
			and perobj_obj_cod = obj_cod
			and obj_cod != v_obj_cod
			and obj_gobj_cod = ligne.oenc_gobj_cod;
		if v_nb_obj_perso < ligne.oenc_nombre then
			return 0;
		end if;
	end loop;
	-- fin de la boucle. A priori, tout le monde est ok, on peut dire qu'il a tout
	return 1;
end;$function$

