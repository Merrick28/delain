CREATE OR REPLACE FUNCTION public.invoque_rejetons(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction invoque_rejetons                                 */
/*   Invoque des monstres d’un type donné sur la position    */
/*    actuelle de l’invocateur (typiquement, gelées)         */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod de l’invocateur           */
/*   $2 = nombre : le nombre de rejetons à créer             */
/*   $3 = gmon_cod : le type de rejetons à créer             */
/* on a en sortie un message texte vide                      */
/*************************************************************/
/* Créé le 20/05/2014                                        */
/*************************************************************/
declare
	v_perso_cod alias for $1;  -- Le code du monstre générant ses rejetons
	v_nombre alias for $2;     -- Le nombre de rejetons
	v_gmon_cod alias for $3;   -- Le type de rejetons à créer
v_perso_type  integer;


	code_retour text;          -- Le retour de la fonction
	v_pos_cod integer;         -- La position du monstre parent
	v_compteur integer;        -- Compteur de boucle
	v_code_monstre integer;    -- Le code du monstre créé

begin
	code_retour := '';

	select into v_pos_cod ppos_pos_cod from perso_position where ppos_perso_cod = v_perso_cod;
	for v_compteur in 1..v_nombre loop
		v_code_monstre := cree_monstre_pos(v_gmon_cod, v_pos_cod);
	end loop;

	return code_retour;
end;$function$

