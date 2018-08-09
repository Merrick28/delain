CREATE OR REPLACE FUNCTION public.use_artefact(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/******************************************************/
/* use_artefact                                       */
/*  on passe en param : $1 = numéro de l’objet        */
/******************************************************/
declare
	code_retour integer;
	num_objet alias for $1;
	v_etat numeric;
	texte_evt text;
	nom_objet text;
	num_perso integer;
	temp integer;
begin
	update objets
		set obj_etat = obj_etat - COALESCE(obj_usure, 0)
		where obj_cod = num_objet;
	select into v_etat, nom_objet
		obj_etat, obj_nom
		from objets
		where obj_cod = num_objet;

	if v_etat <= 0 then
		select into num_perso perobj_perso_cod
			from perso_objets
			where perobj_obj_cod = num_objet;
		texte_evt := 'L’artefact ' || nom_objet || ' s’est détruit !';
		perform insere_evenement(num_perso, num_perso, 39, texte_evt, 'N', '[obj]=' || nom_objet || '(' || num_objet::text || ')');
		temp := f_del_objet(num_objet);
	end if;
	return 0;
end;
	
	
	$function$

