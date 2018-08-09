CREATE OR REPLACE FUNCTION public.terry_vd_chaine(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$
declare
lanceur alias for $1;		-- perso_cod du lanceur
nombre alias for $2;		-- nombre de lancement de vd
lanceur_position integer;
cible integer;
code_retour text;
i integer;

begin
code_retour := 'a';

/* recupérer la position du lanceur*/
SELECT INTO lanceur_position ppos_pos_cod
FROM perso_position
WHERE ppos_perso_cod=lanceur;
	
/*Boucle sur le nombre de VD à lancer*/
for i in 1..nombre loop
	/* selectionner une cible monstre encore vivant sur la case*/
	SELECT INTO cible perso_cod
	FROM perso, perso_position
	WHERE perso_cod=ppos_perso_cod
	AND perso_actif='O'
	AND perso_type_perso=2
	AND ppos_pos_cod=lanceur_position
	ORDER BY perso_cod
	LIMIT 1;
	
	/* lancer vd sur la cible*/
	SELECT into code_retour nv_magie_vague_destructrice(lanceur, cible, 1);
	UPDATE perso SET perso_pa = 12 WHERE perso_cod=lanceur;
	DELETE FROM perso_nb_sorts WHERE pnbs_perso_cod=lanceur;

end loop;
return code_retour;
end;
$function$

