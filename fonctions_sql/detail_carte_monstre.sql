CREATE OR REPLACE FUNCTION public.detail_carte_monstre(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function vue_perso :Procédure de détermination de la zone     */
/*                        de vue du personnage                   */
/* On passe en paramètres                                        */
/*    $1 = etage                                                 */
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
/*****************************************************************/
/* Créé le 07/03/2003                                            */
/* Liste des modifications : 19/01/2004 : adaptation pour JS     */
/*****************************************************************/
declare
	ligne record;
	v_etage alias for $1;
	v_ligne alias for $2;
	v_debut alias for $3;
	texte_retour text;
	nb_monstre integer;
	cpt integer;
	position_actuelle integer;
	x_actuel integer;
	y_actuel integer;
	etage_actuel integer;
	d_vue integer;
	nb_objet integer;
	nb_or integer;
	nb_joueur integer;
	v_dauto_valeur integer;
	v_dauto_type_bat integer;
	v_dauto_vue integer;
	v_decor integer;
	detail_monstre text;
	ligne_detail record;
	
begin
	texte_retour := '';
	cpt := v_debut;
	for ligne in SELECT 	pos_cod,
			pos_x,
			pos_y,
			pos_etage, 
			pos_type_aff,
			pos_decor,
			pos_decor_dessus
	FROM 	positions
	where pos_etage = v_etage
	and pos_y = v_ligne
	ORDER BY pos_y desc,pos_x asc loop
		detail_monstre := ' ';
		select into v_dauto_valeur,v_dauto_type_bat,v_dauto_vue
			dauto_valeur,dauto_type_bat,dauto_vue
			from donnees_automap
			where dauto_pos_cod = ligne.pos_cod;
		
		select into nb_objet count(distinct(pobj_cod)) from objet_position
				where pobj_pos_cod = ligne.pos_cod;
		select into nb_or count(distinct(por_cod)) from or_position
				where por_pos_cod = ligne.pos_cod;
		select into nb_joueur COUNT(DISTINCT(perso_cod)) from perso_position,perso
				where ppos_pos_cod = ligne.pos_cod 
				and ppos_perso_cod = perso_cod
				and perso_type_perso = 1
				and perso_actif = 'O';		
		select into nb_monstre COUNT(DISTINCT(perso_cod)) from perso_position,perso
				where ppos_pos_cod = ligne.pos_cod 
				and ppos_perso_cod = perso_cod
				and perso_type_perso = 2
				and perso_actif != 'N';
		if nb_monstre != 0 then
			for ligne_detail in select gmon_nom,count(distinct(perso_cod)) as nb
				from perso_position,perso,monstre_generique
				where ppos_pos_cod = ligne.pos_cod 
				and ppos_perso_cod = perso_cod
				and perso_type_perso = 2
				and perso_actif != 'N'
				and perso_gmon_cod = gmon_cod
				group by gmon_nom loop
			detail_monstre := detail_monstre||trim(to_char(ligne_detail.nb,'9999999'))||'&nbsp;'||ligne_detail.gmon_nom||'<br>';
		end loop;
			
		end if;
		texte_retour := texte_retour||'carte['''||trim(to_char(cpt,'999999'))||''']=';
		texte_retour := texte_retour||'['||trim(to_char(ligne.pos_cod,'999999'))||','; -- position
		texte_retour := texte_retour||trim(to_char(ligne.pos_x,'999999'))||','; -- X
		texte_retour := texte_retour||trim(to_char(ligne.pos_y,'999999'))||','; -- Y
		texte_retour := texte_retour||trim(to_char(nb_joueur,'999999'))||',';  -- nb persos
		texte_retour := texte_retour||trim(to_char(nb_monstre,'9999999'))||',';  -- nb monstre
		texte_retour := texte_retour||trim(to_char(v_dauto_valeur,'999999'))||','; -- type affichage
		texte_retour := texte_retour||trim(to_char(nb_objet,'999999'))||','; -- objets
		texte_retour := texte_retour||trim(to_char(nb_or,'999999'))||','; -- or
		texte_retour := texte_retour||'1'||','; -- distance
		texte_retour := texte_retour||trim(to_char(v_dauto_vue,'999999'))||','; -- type mur
		texte_retour := texte_retour||trim(to_char(ligne.pos_type_aff,'999999'))||','; -- type case
		texte_retour := texte_retour||trim(to_char(v_dauto_type_bat,'999999'))||','; -- type case
		v_decor := 0;
		if ligne.pos_decor is not null then
			v_decor = ligne.pos_decor;
		end if;
		texte_retour := texte_retour||trim(to_char(v_decor,'999999'))||','; -- decor
		texte_retour := texte_retour||trim(to_char(ligne.pos_decor_dessus,'999999'))||','; -- decor dessus
		texte_retour := texte_retour||''''||replace(detail_monstre,chr(39),' ')||'''];';
		texte_retour := texte_retour||E'\\
';
		cpt := cpt + 1;
			
	end loop;
	texte_retour := texte_retour||'#'||trim(to_char(cpt,'9999999999999'));

	
	
return texte_retour;
end;
 
    
$function$

