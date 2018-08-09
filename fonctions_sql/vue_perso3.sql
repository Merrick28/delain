CREATE OR REPLACE FUNCTION public.vue_perso3(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function vue_perso :Procédure de détermination de la zone     */
/*                        de vue du personnage                   */
/* On passe en paramètres                                        */
/*    $1 = perso ciblé                                           */
/* En sortie, on a la chaine exploitable par JS                  */
/*		ordre de sortie :                                          */
/*		1 - position                                               */
/*    2 - X                                                      */
/*    3 - Y                                                      */
/*    4 - Nb persos                                              */
/*    5 - Nb monstres                                            */
/*    6 - Type affichage                                         */
/*    7 - Objets                                                 */
/*    8 - Or                                                     */
/*    9 - Distance                                               */
/*   10 - Type mur                                               */
/*   11 - Type case                                              */
/*	  12 - type batiment                                          */
/*****************************************************************/
/* Créé le 07/03/2003                                            */
/* Liste des modifications : 19/01/2004 : adaptation pour JS     */
/*****************************************************************/
declare
	ligne record;
	personnage alias for $1;
	texte_retour text;
	nb_monstre integer;
	cpt integer;
	position_actuelle integer;
	x_actuel integer;
	y_actuel integer;
	etage_actuel integer;
	d_vue integer;
	
begin
	texte_retour := '';
	cpt := 0;
	select into x_actuel,y_actuel,etage_actuel,position_actuelle,d_vue
		pos_x,pos_y,pos_etage,pos_cod,distance_vue(personnage)
		from perso_position,positions
		where ppos_perso_cod = personnage
		and ppos_pos_cod = pos_cod;
	for ligne in SELECT 	posfin.pos_cod as pos_cod,
			posfin.pos_x,
			posfin.pos_y,
			posfin.pos_etage, 
			posfin.pos_type_aff,
			posfin.pos_decor,
			posfin.pos_decor_dessus,
			automap.dauto_valeur,
			automap.dauto_type_bat,
			automap.dauto_vue,
			(select count(distinct(pobj_cod)) from objet_position
				where pobj_pos_cod = pos_cod) as objet,
			(select count(distinct(por_cod)) from or_position
				where por_pos_cod = pos_cod) as tas_or,
			distance(position_actuelle,posfin.pos_cod) AS distance, 
			(select COUNT(DISTINCT(perso_cod)) from perso_position,perso
				where ppos_pos_cod = posfin.pos_cod 
				and ppos_perso_cod = perso_cod
				and perso_type_perso = 1
				and perso_actif = 'O') as joueur,
			(select COUNT(DISTINCT(perso_cod)) from perso_position,perso
				where ppos_pos_cod = posfin.pos_cod 
				and ppos_perso_cod = perso_cod
				and perso_type_perso in (2,3)
				and perso_actif = 'O') as monstre
	FROM 	positions posfin,
	donnees_automap automap
	where automap.dauto_pos_cod = posfin.pos_cod
	AND posfin.pos_etage = etage_actuel
	and posfin.pos_x between (x_actuel - d_vue) and (x_actuel + d_vue) 
	AND posfin.pos_y between (y_actuel - d_vue) and (y_actuel + d_vue) 
	ORDER BY pos_y desc,pos_x asc loop
		texte_retour := texte_retour||'tc['''||trim(to_char(cpt,'999999'))||''']=';
		texte_retour := texte_retour||'['||trim(to_char(ligne.pos_cod,'999999'))||','; -- position
		texte_retour := texte_retour||trim(to_char(ligne.pos_x,'999999'))||','; -- X
		texte_retour := texte_retour||trim(to_char(ligne.pos_y,'999999'))||','; -- Y
		texte_retour := texte_retour||trim(to_char(ligne.joueur,'999999'))||',';  -- nb persos
		texte_retour := texte_retour||trim(to_char(ligne.monstre,'9999999'))||',';  -- nb monstre
		texte_retour := texte_retour||trim(to_char(ligne.dauto_valeur,'999999'))||','; -- type affichage
		texte_retour := texte_retour||trim(to_char(ligne.objet,'999999'))||','; -- objets
		texte_retour := texte_retour||trim(to_char(ligne.tas_or,'999999'))||','; -- or
		texte_retour := texte_retour||trim(to_char(ligne.distance,'999999'))||','; -- distance
		texte_retour := texte_retour||trim(to_char(ligne.dauto_vue,'999999'))||','; -- type mur
		texte_retour := texte_retour||trim(to_char(ligne.pos_type_aff,'999999'))||','; -- type case
		texte_retour := texte_retour||trim(to_char(ligne.dauto_type_bat,'999999'))||',';
		texte_retour := texte_retour||trim(to_char(ligne.pos_decor,'999999'))||','; -- decor normal
		texte_retour := texte_retour||trim(to_char(ligne.pos_decor_dessus,'999999'))||'];'; -- decor dessus
		texte_retour := texte_retour||E'\\
';
		cpt := cpt + 1;
			
	end loop;


	
	
return texte_retour;
end;
 
    
$function$

