CREATE OR REPLACE FUNCTION public.vue_pre(integer)
 RETURNS text
 LANGUAGE plpgsql
 STRICT
AS $function$-- fonction vue_pre : Affiche la vue en mode texte
declare
    ligne record;
    v_perso alias for $1;
    v_pos integer; 
    v_x integer;
    v_y integer;
    v_etage integer;
    v_vue integer;
    texte_retour text;
    v_dernier_y integer;
    v_pos_text text; -- Caractère représentant la case

    -- Caractères affichés
    v_aventurier boolean;
    v_murs boolean;
    v_lieux boolean;
    v_brouillard boolean;
    v_monstres boolean;
    v_persos boolean;
    v_melee boolean;
    v_objets boolean;
    v_brouzoufs boolean;

                                                                                
begin                                                                           
    texte_retour := '<pre style="font-family:monospace;border:1px solid;float:left;">';                                                    
    v_dernier_y := 9999;                                                        
    -- Caractères affichés
    v_aventurier := false;
    v_murs := false;
    v_lieux := false;
    v_brouillard := false;
    v_monstres := false;
    v_persos := false;
    v_melee := false;
    v_objets := false;
    v_brouzoufs := false;
                                                                                
    select into v_pos, v_etage, v_x, v_y, v_vue                                 
            ppos_pos_cod, pos_etage, pos_x, pos_y, distance_vue(ppos_perso_cod) 
        from perso_position, positions                                          
        where ppos_perso_cod = v_perso                                            
        and ppos_pos_cod = pos_cod;                                             
    if not found then                                                           
        return 'Position inconnue.';                                                  
    end if;                                                                     
                                                                                
    for ligne in select pos_cod, pos_y, trajectoire_vue3(v_pos, pos_cod) as visible,   
            dauto_vue, dauto_type_bat                                           
            from positions, donnees_automap                                     
            where dauto_pos_cod = pos_cod                                       
            and pos_etage = v_etage                                             
            and pos_x between v_x - v_vue and v_x + v_vue                       
            and pos_y between v_y - v_vue and v_y + v_vue                       
            order by pos_y desc, pos_x asc loop                                 
                                                                                
        if v_dernier_y != ligne.pos_y then                                
            -- Nouvelle ligne ?                                                 
            texte_retour := texte_retour || e'\\
';
            v_dernier_y := ligne.pos_y;                             
        end if;                                                                 
                                                                                
        if v_pos = ligne.pos_cod then                                           
            -- Perso : @                                                        
            texte_retour := texte_retour || '@'; 
            v_aventurier := true;                                
                                                                                
        elsif ligne.dauto_vue > 900 then
            -- Mur : #
            texte_retour := texte_retour || '#';
            v_murs := true;                                

        elsif ligne.dauto_type_bat != 0 then
            -- Bâtiment : ^
            texte_retour := texte_retour || '^';
            v_lieux := true;                                

        elsif ligne.visible != 1 then
            -- Brouillard de guerre : .
            texte_retour := texte_retour || '.';
            v_brouillard := true;                                

        else
            -- Position visible: On calcule son affichage
            select into v_pos_text substr(race_nom, 1, 1) 
                    from race, perso, perso_position 
                    where race_cod = perso_race_cod
                    and perso_cod = ppos_perso_cod
                    and ppos_pos_cod = ligne.pos_cod
                    and perso_type_perso = 2
                    and perso_actif = 'O'
                    order by random() limit 1;
            if not found then
                -- Monstres : Une des races au hasard
                -- Sinon : Rien
                v_pos_text := ' ';
            else
                v_monstres := true;                                
            end if;
            if exists ( select 1 from perso_position, perso 
                   where ppos_perso_cod = perso_cod 
                   and ppos_pos_cod = ligne.pos_cod
                   and perso_type_perso != 2
                   and perso_actif = 'O' ) then
                if v_pos_text != ' ' then
                    -- Aventuriers et monstres : %
                    v_pos_text := '%';
                    v_melee := true;                                
                else
                    -- Aventuriers : %
                    v_pos_text := '°';
                    v_persos := true;                                
                end if;
            end if;

            if v_pos_text = ' ' then
                -- Pas d''êtres vivants: On regarde les objets
                select into v_pos_text tobj_affichage_char 
                        from type_objet, objet_generique, objets, objet_position
                        where tobj_cod = gobj_tobj_cod
                        and gobj_cod = obj_gobj_cod
                        and obj_cod = pobj_obj_cod
                        and pobj_pos_cod = ligne.pos_cod
                        order by random() limit 1;
                if not found then
                    -- Objets : Un des codes au hasard
                    -- Sinon : Rien
                    v_pos_text := ' ';
                else
                    v_objets := true;                                
                end if;
            end if;

            if v_pos_text = ' ' then
                -- Pas d''objets non plus. Brouzoufs : $
                if exists (select 1 from or_position 
                        where por_pos_cod = ligne.pos_cod) then
                    v_brouzoufs := true;                                
                    v_pos_text := '$';
                end if;
            end if;
            texte_retour := texte_retour || v_pos_text;
        end if;

        -- On rajoute un espace pour rendre l''affichage plus "carré"
        texte_retour := texte_retour || ' ';
    end loop;
    texte_retour := texte_retour || '</pre>';

    -- Légende
    texte_retour := texte_retour || '<pre style="float:right;">';
    if v_aventurier then
        texte_retour := texte_retour || e'@ Vous-même\\
';
    end if;
    if v_murs then
        texte_retour := texte_retour || e'# Un mur\\
';
    end if;
    if v_lieux then
        texte_retour := texte_retour || e'^ Un lieu\\
';
    end if;
    if v_brouillard then
        texte_retour := texte_retour || e'. Brouillard de guerre\\
';
    end if;
    if v_monstres then
        texte_retour := texte_retour || e'A-Z Monstres\\
';
    end if;
    if v_persos then
        texte_retour := texte_retour || e'° Aventuriers\\
';
    end if;
    if v_melee then
        texte_retour := texte_retour || e'% Mêlée\\
';
    end if;
    if v_brouzoufs then
        texte_retour := texte_retour || e'$ Brouzoufs\\
';
    end if;
    if v_objets then
        texte_retour := texte_retour || e'Autres: Divers objets\\
';
    end if;
     

return texte_retour;
end;
$function$

