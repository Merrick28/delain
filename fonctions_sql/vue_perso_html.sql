CREATE OR REPLACE FUNCTION public.vue_perso_html(integer, text)
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
	fin_texte text;
	v_comment text;
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
	img_path alias for $2;
	y_encours integer;
	
begin
	y_encours = -2000;
	texte_retour := '';
	cpt := 0;
	select into x_actuel,y_actuel,etage_actuel,position_actuelle,d_vue
		pos_x,pos_y,pos_etage,pos_cod,distance_vue(personnage)
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
			trajectoire_vue3(position_actuelle,pos_cod) AS trajectoire
	FROM 	positions,donnees_automap
	where dauto_pos_cod = pos_cod
	AND pos_etage = etage_actuel
	and pos_x between (x_actuel - d_vue) and (x_actuel + d_vue) 
	AND pos_y between (y_actuel - d_vue) and (y_actuel + d_vue) 
	ORDER BY pos_y desc,pos_x asc loop
		
	fin_texte := '';
	v_comment := '';
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
		if (y_encours != ligne.pos_y) then
			if y_encours != -2000 then
				texte_retour := texte_retour||'</tr>';
			end if;
			texte_retour := texte_retour||'<tr class="vueoff" height="10"><td height="10" class="coord2"><p class="coord">'||trim(to_char(ligne.pos_y,'999999'))||'</p></td>';
			y_encours = ligne.pos_y;
		end if;
		texte_retour := texte_retour||'<td class="v'||trim(to_char(ligne.pos_type_aff,'999999'))||'">';
		if ligne.pos_decor != 0 then
			texte_retour := texte_retour||'<div id="1" class="decor'||trim(to_char(ligne.pos_decor,'999999'))||'">';
			fin_texte := fin_texte||'</div>';
		end if;
		if nb_perso != 0 then
			v_comment := v_comment||trim(to_char(nb_perso,'999999'))||' aventuriers, ';
			texte_retour := texte_retour||'<div id="1" class="joueur">';
			fin_texte := fin_texte||'</div>';
		end if;
		if nb_monstre != 0 then
			v_comment := v_comment||trim(to_char(nb_monstre,'999999'))||' monstres, ';
			texte_retour := texte_retour||'<div id="1" class="monstre">';
			fin_texte := fin_texte||'</div>';
		end if;
		if nb_obj != 0 then
			v_comment := v_comment||trim(to_char(nb_obj,'999999'))||' objets, ';
			texte_retour := texte_retour||'<div id="1" class="objet">';
			fin_texte := fin_texte||'</div>';
		end if;
		if nb_or != 0 then
			v_comment := v_comment||trim(to_char(nb_or,'999999'))||' tas d''or, ';
			if nb_obj = 0 then
				texte_retour := texte_retour||'<div id="1" class="objet">';
				fin_texte := fin_texte||'</div>';
			end if;
		end if;	
		if ligne.dauto_valeur = 1 then
			v_comment := v_comment||'1 mur';
			texte_retour := texte_retour||'<div id="1" class="mur_'||trim(to_char(ligne.dauto_vue,'999999'))||'">';
			fin_texte := fin_texte||'</div>';
		end if;
		if ligne.dauto_type_bat != 0 then
			v_comment := v_comment||'1 lieu, ';
			texte_retour := texte_retour||'<div id="1" <div class="lieu'||trim(to_char(ligne.dauto_type_bat,'999999'))||'">';
			fin_texte := fin_texte||'</div>';
		end if;
		if ligne.distance = 0 then
			texte_retour := texte_retour||'<div id="1" class="oncase">';
			fin_texte := fin_texte||'</div>';
		end if;
		texte_retour := texte_retour||'<div id="dep" class="main" onClick="javascript:window.parent.automap.document.det_cadre.position.value='''||trim(to_char(ligne.pos_cod,'999999'))||''';window.parent.automap.document.det_cadre.dist.value='''||trim(to_char(ligne.distance,'999999'))||''';window.parent.automap.document.det_cadre.submit();" title="'||v_comment||'">';
		if ligne.trajectoire = 0 then
			if ligne.dauto_valeur != 1 then
				texte_retour := texte_retour||'<div id="1" class="br">';
				fin_texte := fin_texte||'</div>';
			end if;
		end if;
		if ligne.trajectoire = 1 then
			texte_retour := texte_retour||'<div id="cell2'||trim(to_char(ligne.pos_cod,'999999'))||'">';  
			fin_texte := fin_texte||'</div>';
		end if;
		if ligne.pos_decor_dessus != 0 then
			texte_retour := texte_retour||'<div id="1" class="decor'||trim(to_char(ligne.trajectoire,'999999'))||'">';
			fin_texte := fin_texte||'</div>';
		end if;
		texte_retour := texte_retour||'<div id="cell'||trim(to_char(ligne.pos_cod,'999999'))||'" class="pasvu" style="background:url('''||img_path||'c1.gif'')" onClick="javascript:document.deplacement.position.value='''||trim(to_char(ligne.pos_cod,'999999'))||''';document.deplacement.submit();" title="'||v_comment||'">';
		texte_retour := texte_retour||'<img src="'||img_path||'del.gif" width="28" height="28" alt="'||v_comment||'">';
		texte_retour := texte_retour||fin_texte||'</td>';
		
		
		/*texte_retour := texte_retour||'tc['''||trim(to_char(cpt,'999999'))||''']=';
		texte_retour := texte_retour||'['||trim(to_char(ligne.pos_cod,'999999'))||','; -- position 0
		texte_retour := texte_retour||trim(to_char(ligne.pos_x,'999999'))||','; -- X 1 
		texte_retour := texte_retour||trim(to_char(ligne.pos_y,'999999'))||','; -- Y 2
		texte_retour := texte_retour||trim(to_char(nb_perso,'999999'))||',';  -- nb persos 3
		texte_retour := texte_retour||trim(to_char(nb_monstre,'9999999'))||',';  -- nb monstre 4
		texte_retour := texte_retour||trim(to_char(ligne.dauto_valeur,'999999'))||','; -- type affichage 5
		texte_retour := texte_retour||trim(to_char(nb_obj,'999999'))||','; -- objets 6
		texte_retour := texte_retour||trim(to_char(nb_or,'999999'))||','; -- or 7
		texte_retour := texte_retour||trim(to_char(ligne.distance,'999999'))||','; -- distance 8
		texte_retour := texte_retour||trim(to_char(ligne.dauto_vue,'999999'))||','; -- type mur 9
		texte_retour := texte_retour||trim(to_char(ligne.pos_type_aff,'999999'))||','; -- type case 10
		texte_retour := texte_retour||trim(to_char(ligne.dauto_type_bat,'999999'))||','; 11
		texte_retour := texte_retour||trim(to_char(ligne.pos_decor,'999999'))||','; -- decor normal 12
		texte_retour := texte_retour||trim(to_char(ligne.trajectoire,'999999'))||','; -- trajectoire 13
		texte_retour := texte_retour||trim(to_char(ligne.pos_decor_dessus,'999999'))||'];'; -- decor dessus 14
		texte_retour := texte_retour||E'\\
';
		cpt := cpt + 1;*/
			
	end loop;


	
	
return texte_retour;
end;
 
    
$function$

