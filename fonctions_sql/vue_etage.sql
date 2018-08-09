CREATE OR REPLACE FUNCTION public.vue_etage(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function vue_etage :Procédure de détermination de la zone     */
/*                        de vue d un etage                      */
/* On passe en paramètres                                        */
/*    $1 = etage ciblé                                           */
/* En sortie, on a une chaine formatée des pos_cod séparés par   */
/* des virgules (utilisable dans php)                            */
/* La forme est :                                                */
/* pos1,x1,y0,distance,vue;pos2x1,y1,vue                         */
/* Les infos dans chaque positions sont séparées par ,           */
/* Les positions dans la même ligne sont séparées par des @      */
/* Les "lignes" de x sont séparées par #                         */
/* la vue est :                                                  */
/*    -1 : hors limite                                           */
/*   999 : un mur....                                            */
/*     0 : rien                                                  */
/*     1 : au moins un joueur                                    */
/*     2 : au moins un monstre                                   */
/*     4 : au moins un objet                                     */
/*     8 : au moins un site                                      */
/*****************************************************************/
/* Créé le 07/03/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	texte_retour text; -- sert de code retour
	temp_texte text;
	temp_x integer;
	temp_y integer;
	temp_calcul integer;
	etage alias for $1; 
	ligne_pos record;
	temp_distance integer;
	trouve_objet integer;
	v_type_mur integer;
	nb_perso integer;
	nb_monstre integer;
	
begin
	temp_y := -2000;
	texte_retour := '';
	for ligne_pos in select pos_cod,pos_x,pos_y from positions
		where pos_etage = etage
		order by pos_y desc,pos_x asc	loop
		if temp_y = ligne_pos.pos_y then -- on est toujours sur le même y
			temp_texte := '@';
		else    -- if temp_y = ligne_pos.pos_y
			if temp_y != -2000 then
				temp_texte := '#';
				temp_y := ligne_pos.pos_y;
			else
				temp_texte := '';
				temp_y := ligne_pos.pos_y;
			end if;
		end if; --	if temp_x = ligne_pos.pos_x	
		temp_calcul := 0;
		temp_texte := temp_texte||to_char(ligne_pos.pos_cod,'9999999')||','||to_char(ligne_pos.pos_x,'9999')||',';
		temp_texte := temp_texte||to_char(ligne_pos.pos_y,'9999')||',';
		/* calcul pour la distance */
		temp_texte := temp_texte||to_char(0,'999')||',';
/**************************************/
/* On calcule pour les perso visibles */
/**************************************/		
		select into nb_perso count(perso_cod)
			from perso_position,perso
			where ppos_pos_cod = ligne_pos.pos_cod
			and ppos_perso_cod = perso_cod
			and perso_actif = 'O' and perso_type_perso = 1;
		if nb_perso != 0 then
			temp_calcul := temp_calcul + 1;
		end if; -- if exists pour perso
/*****************************************/
/* On calcule pour les monstres visibles */
/*****************************************/		
		select into nb_monstre count(perso_cod)
			from perso_position,perso
			where ppos_pos_cod = ligne_pos.pos_cod
			and ppos_perso_cod = perso_cod
			and perso_actif = 'O' and perso_type_perso = 2;
		if nb_monstre != 0 then 
			temp_calcul := temp_calcul + 2;
		end if; -- if exists pour monstre
/*****************************/
/* On calcule pour les sites */
/*****************************/		
		if exists (select 1 from lieu_position
			where lpos_pos_cod = ligne_pos.pos_cod) then
			temp_calcul := temp_calcul + 8;
		end if; -- if exists pour monstre
/***************************************/
/* On calcule pour les objets visibles */		
/***************************************/
		trouve_objet := 0;
		if exists (select 1 from objet_position
			where pobj_pos_cod = ligne_pos.pos_cod) then
			temp_calcul := temp_calcul + 4;
			trouve_objet := 1;
		end if; -- if exists pour objet
		if exists (select 1 from or_position where por_pos_cod = ligne_pos.pos_cod) then
			if (trouve_objet = 0) then
				temp_calcul := temp_calcul + 4;
				trouve_objet := 1;
			end if;  -- if exists pour or
		end if ;		-- if trouve_objet = 0 
/********************************/
/* On calcule ici pour les murs */
/********************************/
		if exists (select 1 from murs where mur_pos_cod = ligne_pos.pos_cod) then
			select into v_type_mur mur_type from murs
				where mur_pos_cod = ligne_pos.pos_cod;
			temp_calcul := v_type_mur;	
		end if;		-- if exists pour mur
		/***************************************************/
		/* On rajoutera plus tard ici le calcul pour les   */
		/* montres, lieux à rajouter dans                  */
		/* temp_calcul                                     */
		/***************************************************/
		temp_texte := temp_texte||to_char(temp_calcul,'999');
		temp_texte := temp_texte||','||trim(to_char(nb_perso,'999999'))||','||trim(to_char(nb_monstre,'999999'))||',';
		texte_retour := texte_retour||temp_texte;
	end loop;	
return texte_retour;
end;
 
    
$function$

