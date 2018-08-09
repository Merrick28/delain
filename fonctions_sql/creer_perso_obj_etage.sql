CREATE OR REPLACE FUNCTION public.creer_perso_obj_etage(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_objet  :Procédure de création d objet     en    */
/*   dans l'inventaire d'un perso                                */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gobj_cod (objet   générique)                        */
/*   $2 = le type de perso                                       */
/*   $3 = l'etage concerné                                       */
/* Le code sortie est :                                          */
/*    0 = Tout s est bien passé                                  */
/*   -1 = le gmon_cod n existe pas                               */
/*    2 = cree perso anomalie                                    */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer;
	v_gobj alias for $1;
	v_type alias for $2;
	v_etage alias for $3;
-- variables d'enregistrement
        ligne record;

-- variables globales
	compt integer;
	v_code_objet integer;
        v_perso integer;
	retour_cree_perso integer;
begin
-- etape 1 on enregistre les valeurs dans la zone record
for ligne in select pos_etage,pos_cod,perso_cod
 from perso,perso_position,positions
 where perso_actif = 'O'
 and ppos_pos_cod = pos_cod
 and perso_cod = ppos_perso_cod
 and perso_type_perso = v_type
 and pos_etage = v_etage
 order by perso_cod loop 

/************************************************/
/* Etape 2 : on insère dans l'étage les valeurs */
/************************************************/
	code_retour := 0;
	v_code_objet := nextval('seq_obj_cod');
	select into compt gobj_cod from objet_generique
		where gobj_cod = v_gobj;
	if not found then
		code_retour := -1;
		return code_retour;
	end if;
	insert into objets (obj_cod,obj_gobj_cod) values (v_code_objet,v_gobj);

	
/*************************/
/* Etape 4 : on le place */
/*************************/
         v_perso := ligne.perso_cod;
	insert into perso_objets (perobj_obj_cod,perobj_perso_cod,perobj_equipe,perobj_identifie) 
	values (v_code_objet,v_perso,'N','O');
         code_retour := v_code_objet;
end loop;
return code_retour;
end;
$function$

