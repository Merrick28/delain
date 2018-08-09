CREATE OR REPLACE FUNCTION public.deb_tour_generique_case(integer, text, text, text, text, numeric, text, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* deb_tour_generique_case                      */
/* Applique les bonus et effectue les actions   */
/* spécifiées lors de l'activation d'une DLT.   */
/* On passe en paramètres:                      */
/*   $1 = source (perso_cod du monstre)         */
/*   $2 = bonus (de la table bonus_type)        */
/*   $3 = valeur (Entier ou +/-nDy)             */
/*   $4 = distance (-1..n , nC pour une case)   */
/*   $5 = cibles (SAERTP, et limite)            */
/*   $6 = Probabilité d atteindre chaque cible  */
/*   $7 = Message d événement associé           */
/*   $8 = Message affiché à l'init de la dlt    */
/************************************************/
/* Créé le 15 Janvier 2010	                    */
/************************************************/
declare
    -- Parameters
    v_source alias for $1;
    v_bonus alias for $2;
    v_valeur alias for $3;
    v_distance alias for $4;
    v_cibles alias for $5;
    v_proba alias for $6;
    v_texte_evt alias for $7;
    v_texte_retour alias for $8;

    -- initial data
    v_x_source integer; -- source X position
    v_y_source integer; -- source Y position
    v_et_source integer; -- etage de la source
    v_type_source integer; -- Type perso de la source
    v_distance_int integer; -- Range
    v_cibles_type character; -- Type de cible (SAERT)
    v_cibles_nombre integer; -- Nombre de cibles maxi
    v_race_source integer; -- La race de la source
    v_position_source integer; -- Le pos_cod de la source
    
    -- Output and data holders 
    ligne record;  -- Une ligne d'enregistrements
    i integer; -- Compteur de boucle
    ch character; -- Un caractère tout ce qu'il y a de plus banal
    valeur integer; -- Valeur numérique de l'impact du bonus ou de l'action
    n integer; -- Nombre de dés.
    d integer; -- Nombre de faces du dé
    signe integer; -- Signe de la valeur (1 ou -1)
    cible_case integer; -- Cible une case
    -- Resistance magique
    v_bloque_magie integer;
    v_RM1 integer;
    compt integer;
    niveau_cible integer;
    v_int_cible integer;
    v_con_cible integer;
    niveau_attaquant integer;
    v_seuil integer;
    des integer;
    code_retour text;
    action_immediate_result text;

begin
    -- Initialisation des conteneurs
		code_retour = '';
    cible_case := 0;
    for i in 1..length(v_distance) loop
        ch := substr(v_distance , i , 1);
        if ch IN ('c' , 'C') then
            cible_case := 1;
        end if;
    end loop;
    if (cible_case = 0) then
        v_distance_int := v_distance;
    else
        -- La cible est une case. Choisir une case puis définir case = source, et
        -- distance = 0
        -- TODO
        v_distance_int := 0;
    end if;

    -- Position et type perso
    select into v_x_source , v_y_source , v_et_source , v_type_source ,    v_race_source, v_position_source
                pos_x ,      pos_y ,      pos_etage ,   perso_type_perso , perso_race_cod, pos_cod
				    from perso_position , positions , perso
				    where ppos_perso_cod = v_source
				    and pos_cod = ppos_pos_cod
				    and perso_cod = v_source;

    -- Cibles : P = Perso individuel, même sur un lieu protégé, S = Soi même, R = Race spécifique, A = autre perso du même type, E = persos d'un type différent, T = Tout le monde
    v_cibles_type := 'T'; -- Tout le monde, par défaut
    v_cibles_nombre := 0; -- Pas de limite, par défaut
    for i in 1..length(v_cibles) loop
        ch := substr(v_cibles , i , 1);
        if ch IN ('S' , 'A' , 'E' , 'R' , 'T', 'P') then
            v_cibles_type := ch;
        else
            v_cibles_nombre := 10 * v_cibles_nombre + cast(ch AS integer);
        end if;
    end loop;
    if (v_cibles_nombre = 0) then
        --v_cibles_nombre := 999999; -- modif par merrick : boucles trop longues
	v_cibles_nombre := 100;
    end if;

    -- Détermination de la liste des cibles
    if (v_distance_int < 0) then
        v_distance_int = 0;
        v_cibles_type = 'S';
    end if;
    if (v_cibles_nombre >= 20) then
    	v_cibles_nombre := 20;
    end if;
    for ligne in (select perso_cod , perso_type_perso , perso_race_cod
			        from perso , perso_position , positions
			        where perso_actif = 'O'
			        and ppos_perso_cod = perso_cod
			        and ppos_pos_cod = pos_cod
			        -- À portée
			        and pos_x between (v_x_source - v_distance_int) and (v_x_source + v_distance_int)
			        and pos_y between (v_y_source - v_distance_int) and (v_y_source + v_distance_int)
			        and pos_etage = v_et_source
			        and trajectoire_vue(pos_cod,v_position_source) = '1'
			        -- Parmi les cibles spécifiées
			        and
			            ((v_cibles_type = 'S' and perso_cod = v_source) or
			            (v_cibles_type = 'A' and perso_type_perso = v_type_source) or
			            (v_cibles_type = 'E' and perso_type_perso != v_type_source) or
			            (v_cibles_type = 'R' and perso_race_cod = v_race_source) or
			            (v_cibles_type = 'P' and perso_cod = v_source) or
			            (v_cibles_type = 'T'))
			        -- Répondant à une proba d'être touchées.
			        and random() <= v_proba
			        -- Dans les limites autorisées
			        order by random()
			        limit v_cibles_nombre)
    loop
        -- On peut maintenant appliquer le bonus ou l'action sur une cible unique.
        -- Valeur
        n := 0;
        d := -1;
        signe := 1;
        for i in 1..length(v_valeur) loop
            ch := substr(v_valeur , i , 1);
            if ch IN ('D' , 'd') then
                d := 0;
            else
                if ch = '-' then
                    signe := -1;
                elseif d = -1 then
                    n := n * 10 + cast(ch AS integer);
                else
                    d := d * 10 + cast(ch AS integer);
                end if;
            end if;
        end loop;
        --if (v_valeur ilike 'd') then
        --	d := 0;
        --end if;
        --if (v_valeur < 0) then
        --	signe = -1;
        --else
        --	if d = -1 then
        --		n := v_valeur;
        --	else
        --		d:= v_valeur;
        --	end if;
        --end if;
        if (d = -1) then
            d := 1;
        end if;
        -- ajout Merrick, si on est sur du QENL, on ne fait pas le lancer de dés (avec des num de perso à plus d'un million, ça tourne trop longtemps)
        if (v_bonus = 'QENL') then
        	valeur := v_valeur;
        else
        	valeur := signe * lancer_des(n , d);
        end if;
        -- Ajout azaghal on teste un simili resiste magie pour chaque personne cible sauf si la     cible est le lanceur
        If v_cibles_type != 'S' then
					v_bloque_magie := 0;
				-- on récupère les données de l'attaquant
					select into niveau_attaquant 
								perso_niveau
								from perso
								where perso_cod = v_source;
				-- on récupère les données de la cible
					select into niveau_cible,v_int_cible,v_con_cible
								perso_niveau,perso_int,perso_con
								from perso
								where perso_cod = ligne.perso_cod;
				-- on calcule le seuil de résistance (en fonction de l'int, la con le niv du sort et la marge de réussite
				  v_con_cible := floor(v_con_cible/10);
				  niveau_cible := floor(niveau_cible/2);
					v_RM1 := (v_int_cible * 5) + v_con_cible + niveau_cible;
				  compt := 30;
				  compt := compt + (2*niveau_attaquant);
				-- calcul du seuil effectif
				  v_seuil = v_RM1 - compt;
				-- on limite une premiere fois le seuil à 15
				  if v_seuil < 15 then
				     v_seuil := 15;
				   end if;
				
				 -- le seuil (v_seuil) est maintenant calculé on peut tester
					if lancer_des(1,100) > v_seuil then 	-- resistance ratée
						v_bloque_magie := 0;	
					else
						v_bloque_magie := 1;
				    valeur:= floor(valeur/2);
				    if valeur = 0 then 
				        valeur:= 1; 
				    end if; /*rajout Blade du cas ou valeur devient 0 à cause du floor*/
					end if;
				end if;

        -- Effet: Bonus ou action
        if not exists (select tbonus_libc from bonus_type
                       where tbonus_libc = v_bonus) then
            -- Ceci est une action à effet immédiat, qui n'est pas gérée à travers les bonus (gains/perte de PV, ...)
            --Dans ce cas, v_bonus symbolise l'action qui va être lancée (de préférence utiliser 4 caractères), et valeur les indications de valeurs qui vont être mise en place.
					action_immediate_result := '';
					        
          select into action_immediate_result action_immediate(ligne.perso_cod, v_bonus, valeur);
          if substr(action_immediate_result , 1, 2) != 'KO' then
			        -- On rajoute la ligne d'événements
				        if strpos(v_texte_evt , '[attaquant]') != 0 then
				            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
												values(nextval('seq_levt_cod'),54,now(),1,v_source,v_texte_evt,'O','O',v_source,ligne.perso_cod);
				        end if;
				        if strpos(v_texte_evt , '[cible]') != 0 then
				            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
												values(nextval('seq_levt_cod'),54,now(),1,ligne.perso_cod,v_texte_evt,'N','O',v_source,ligne.perso_cod);
				        end if;
				        code_retour := v_texte_retour||action_immediate_result;
	    					return code_retour;
          end if;
        else
            -- Un bonus. On met à jour la table bonus
            perform ajoute_bonus(ligne.perso_cod, v_bonus, 2, valeur);
		        -- On rajoute la ligne d'événements
		        if strpos(v_texte_evt , '[attaquant]') != 0 then
		            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
										values(nextval('seq_levt_cod'),54,now(),1,v_source,v_texte_evt,'O','O',v_source,ligne.perso_cod);
		        end if;
		        if strpos(v_texte_evt , '[cible]') != 0 then
		            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
										values(nextval('seq_levt_cod'),54,now(),1,ligne.perso_cod,v_texte_evt,'N','O',v_source,ligne.perso_cod);
			        code_retour := code_retour||v_texte_retour;
		        end if;
    			return code_retour;
        end if;

    end loop;
    return code_retour;
end;$function$

