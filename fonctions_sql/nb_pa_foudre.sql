CREATE OR REPLACE FUNCTION public.nb_pa_foudre(integer)
 RETURNS integer
 LANGUAGE plpgsql
 STRICT SECURITY DEFINER
AS $function$/*********************************************/
/* fonction nb_pa_foudre                     */
/*   retourne le nombre de pa nécessaires    */
/*   pour porter une attaque foudroyante     */
/*   en fonction de l'arme équipée par le    */
/*   perso à ce moment                       */

/* on passe en paramètre :                   */
/*   $1 = perso_cod                          */
/*********************************************/
/* Créé le 21/05/2003                        */
/*********************************************/
declare
	code_retour integer;
	v_perso alias for $1;
	pa_temp integer;
	v_bonus integer;
	att_temp integer;
	
begin
	select into pa_temp gobj_pa_eclair 
		from objet_generique,perso_objets,objets
		where perobj_perso_cod = v_perso
		and perobj_equipe = 'O'
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = 1;
	if pa_temp is null then
		att_temp := getparm_n(5);
	else
		att_temp := pa_temp;
	end if;
	code_retour := att_temp + valeur_bonus(v_perso, 'PAA') + valeur_bonus(v_perso, 'PPA');
if code_retour < 1 then
code_retour := 1;
end if;
	return code_retour;
end;
		
$function$

