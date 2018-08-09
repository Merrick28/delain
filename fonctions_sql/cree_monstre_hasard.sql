CREATE OR REPLACE FUNCTION public.cree_monstre_hasard(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_monstre_hasard:Procédure de création de monstre */
/* en position aleatoire                                         */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gmon_cod (monstre générique)                        */
/*   $2 = le niveau ou il doit apparaitre                        */
/* Le code sortie est :                                          */
/*    le numéro de monstre                                       */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer;
	v_gmon alias for $1;
	v_level alias for $2;
-- récupération des données génériques
	v_num_monstre integer;
	pos_portail integer;
begin
/**********************************************/
/* Etape 1 : on insère dans perso les valeurs */
/**********************************************/
	code_retour := 0;
	v_num_monstre := cree_monstre(v_gmon,v_level);
	pos_portail := pos_aleatoire(v_level);
	update perso_position set ppos_pos_cod = pos_portail where ppos_perso_cod = v_num_monstre;
	code_retour := v_num_monstre;
	return code_retour;
end;
 
    

$function$

