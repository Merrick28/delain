CREATE OR REPLACE FUNCTION public.cree_groupe(integer, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* cree_groupe                                 */
/*  $1 = perso_cod                             */
/*  $2 = nom du groupe                         */
/***********************************************/
declare 
	code_retour text;
	v_nom_groupe alias for $2;
	v_perso alias for $1;
	temp integer;
	v_num_groupe integer;
begin
	select into temp
		groupe_cod
		from groupe
		where lower(groupe_nom) = lower(v_nom_groupe);
	if found then
		return 'Désolé, une coterie porte déjà ce nom.<br>
			<a href="groupe.php?methode=cree">Retour à la création</a>';
	end if;
	-- on prend la séquence
	v_num_groupe := nextval('seq_groupe_cod');
	-- on crée le groupe
	insert into groupe
		(groupe_cod,groupe_nom,groupe_chef)
		values
		(v_num_groupe,v_nom_groupe,v_perso);
	-- on créé le groupe_perso
	insert into groupe_perso
		(pgroupe_perso_cod,pgroupe_groupe_cod,pgroupe_statut,pgroupe_chef)
		values
		(v_perso,v_num_groupe,1,1);
	return 'La coterie a bien été créée.';
end;$function$

