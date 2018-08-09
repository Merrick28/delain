CREATE OR REPLACE FUNCTION public.test_vv(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function vue_perso_limite :Procédure de                       */
/*                        de vue du personnage                   */
/* On passe en paramètres                                        */
/*    $1 = perso ciblé                                           */
/*    $2 = limitation_vue                                        */
/* En sortie, on a la chaine exploitable par JS                  */
/*****************************************************************/
/* Créé le 07/03/2003                                            */
/* Liste des modifications : 19/01/2004 : adaptation pour JS     */
/*****************************************************************/
declare
	ligne record;
	personnage alias for $1;
	texte_retour text;
	cpt integer;
	position_actuelle integer;
	x_actuel integer;
	y_actuel integer;
	etage_actuel integer;
	d_vue alias for $2;
	nb_perso integer;
	nb_monstre integer;
	nb_or integer;
	nb_obj integer;
	
begin
	texte_retour := '';
	cpt := 0;
	select into x_actuel,y_actuel,etage_actuel,position_actuelle
		pos_x,pos_y,pos_etage,pos_cod
		from perso_position,positions
		where ppos_perso_cod = personnage
		and ppos_pos_cod = pos_cod;
	for ligne in SELECT 	pos_cod,
			pos_x,
			pos_y,
			pos_etage, 
			pos_type_aff,
			pos_decor,
			pos_decor_dessus,
			dauto_valeur,
			dauto_type_bat,
			dauto_vue,
			distance(position_actuelle,pos_cod) AS distance, 
			trajectoire_vue(position_actuelle,pos_cod) AS trajectoire
	FROM 	positions,donnees_automap
	where dauto_pos_cod = pos_cod
	AND pos_etage = etage_actuel
	and pos_x between (x_actuel - d_vue) and (x_actuel + d_vue) 
	AND pos_y between (y_actuel - d_vue) and (y_actuel + d_vue) 
	ORDER BY pos_y desc,pos_x asc loop
		nb_obj := 0;
		nb_or := 0;
		nb_perso := 0;
		nb_monstre := 0;
		if ligne.trajectoire != 0 then
			select into nb_obj count(distinct(pobj_cod)) from objet_position
				where pobj_pos_cod = ligne.pos_cod;
			select into nb_or count(distinct(por_cod)) from or_position
				where por_pos_cod = ligne.pos_cod;
			select into nb_perso COUNT(DISTINCT(perso_cod)) from perso_position,perso
				where ppos_pos_cod = ligne.pos_cod 
				and ppos_perso_cod = perso_cod
				and perso_type_perso = 1
				and perso_actif = 'O';
			select into nb_monstre COUNT(DISTINCT(perso_cod)) from perso_position,perso
				where ppos_pos_cod = ligne.pos_cod 
				and ppos_perso_cod = perso_cod
				and perso_type_perso in (2,3)
				and perso_actif = 'O';
		end if;
		
		
		
		texte_retour := texte_retour||'carte['''||trim(to_char(cpt,'999999'))||''']=';
		texte_retour := texte_retour||'['''||trim(to_char(ligne.pos_cod,'999999'))||''','''; -- position
		texte_retour := texte_retour||trim(to_char(ligne.pos_x,'999999'))||''','''; -- X
		texte_retour := texte_retour||trim(to_char(ligne.pos_y,'999999'))||''','''; -- Y
		texte_retour := texte_retour||trim(to_char(nb_perso,'999999'))||''',''';  -- nb persos
		texte_retour := texte_retour||trim(to_char(nb_monstre,'9999999'))||''',''';  -- nb monstre
		texte_retour := texte_retour||trim(to_char(ligne.dauto_valeur,'999999'))||''','''; -- type affichage
		texte_retour := texte_retour||trim(to_char(nb_obj,'999999'))||''','''; -- objets
		texte_retour := texte_retour||trim(to_char(nb_or,'999999'))||''','''; -- or
		texte_retour := texte_retour||trim(to_char(ligne.distance,'999999'))||''','''; -- distance
		texte_retour := texte_retour||trim(to_char(ligne.dauto_vue,'999999'))||''','''; -- type mur
		texte_retour := texte_retour||trim(to_char(ligne.pos_type_aff,'999999'))||''','''; -- type case
		texte_retour := texte_retour||trim(to_char(ligne.dauto_type_bat,'999999'))||'''];'; -- type case
		texte_retour := texte_retour||e'\\
';
		cpt := cpt + 1;
			
	end loop;


	
	
return texte_retour;
end;
 
 
    
$function$

