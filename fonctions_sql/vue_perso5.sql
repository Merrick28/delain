CREATE OR REPLACE FUNCTION public.vue_perso5(integer)
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
	nb_obj integer;
	nb_or integer;
	nb_perso integer;
	
begin
	texte_retour := '';
	cpt := 0;
	for ligne in select * from vue_perso6(personnage) loop
		texte_retour := texte_retour||'tc['''||trim(to_char(ligne.tvue_num,'999999'))||''']=';
		texte_retour := texte_retour||'['||trim(to_char(ligne.t_pos_cod,'999999'))||','; -- position
		texte_retour := texte_retour||trim(to_char(ligne.t_x,'999999'))||','; -- X
		texte_retour := texte_retour||trim(to_char(ligne.t_y,'999999'))||','; -- Y
		texte_retour := texte_retour||trim(to_char(ligne.t_nb_perso,'999999'))||',';  -- nb persos
		texte_retour := texte_retour||trim(to_char(ligne.t_nb_monstre,'9999999'))||',';  -- nb monstre
		texte_retour := texte_retour||trim(to_char(ligne.t_type_aff,'999999'))||','; -- type affichage
		texte_retour := texte_retour||trim(to_char(ligne.t_nb_obj,'999999'))||','; -- objets
		texte_retour := texte_retour||trim(to_char(ligne.t_or,'999999'))||','; -- or
		texte_retour := texte_retour||trim(to_char(ligne.t_dist,'999999'))||','; -- distance
		texte_retour := texte_retour||trim(to_char(ligne.t_type_mur,'999999'))||','; -- type mur
		texte_retour := texte_retour||trim(to_char(ligne.t_type_case,'999999'))||','; -- type case
		texte_retour := texte_retour||trim(to_char(ligne.t_type_bat,'999999'))||',';
		texte_retour := texte_retour||trim(to_char(ligne.t_decor,'999999'))||','; -- decor normal
		texte_retour := texte_retour||trim(to_char(ligne.t_traj,'999999'))||','; -- trajectoire
		texte_retour := texte_retour||trim(to_char(ligne.t_decor_dessus,'999999'))||'];'; -- decor dessus
		texte_retour := texte_retour||e'\\
';
	end loop;
	
return texte_retour;
end;
	
	
	
	
	
	
	
$function$

