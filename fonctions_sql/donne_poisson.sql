CREATE OR REPLACE FUNCTION public.donne_poisson(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* fonction donne_poisson                               */
/*   $1 = donneur                                       */
/*   $2 = receveur                                      */
/*   $3 = num_objet                                     */
/********************************************************/
declare
    code_retour text;
    donneur alias for $1;
    receveur alias for $2;
    num_objet alias for $3;
    nom_cible text;
    nb_poiss integer;
    v_type_receveur integer;
    v_race_receveur integer;   
    v_pa integer;
    v_compte_donneur integer;
    v_compte_receveur integer;
    v_code_message integer;   
    v_temp integer;
    texte_evt text;
    ligne record;
    des integer;                    -- lancer de dés
    temp_var text;
    v_niveau integer;
begin
code_retour := '';
/*********************************************************/
/* C O N T R O L E S                                     */
/*********************************************************/
-- type de perso
    select into v_type_receveur,v_race_receveur,nom_cible perso_type_perso,perso_race_cod,perso_nom
        from perso
        where perso_cod = receveur;
    if not found then
        code_retour := 'Erreur ! Receveur non trouvé !';
        return code_retour;
    end if;
    if v_type_receveur != 1 and v_race_receveur != 45 then
        code_retour := 'Erreur ! Vous ne pouvez pas donner les poissons aux monstres ou aux familiers !';
        return code_retour;
    end if;
-- PA
    select into v_pa,v_niveau perso_pa,perso_niveau from perso where perso_cod = donneur;
    if not found then
        code_retour := 'Erreur ! Donneur non trouvé !';
        return code_retour;
    end if;
    if v_pa < 1 then
        code_retour := 'Erreur ! Vous n''avez pas assez de PA pour donner ce poisson !';
        return code_retour;
    end if;
-- comptes
    select into v_compte_donneur pcompt_compt_cod from perso_compte
        where pcompt_perso_cod = donneur;
    if not found then
        code_retour := 'Erreur ! Compte donneur non trouvé !';
        return code_retour;
    end if;   
    if v_race_receveur != 45 then
        select into v_compte_receveur pcompt_compt_cod from perso_compte
            where pcompt_perso_cod = receveur;
        if not found then
            code_retour := 'Erreur ! Compte receveur non trouvé !';
            return code_retour;
        end if;   
        if v_compte_donneur = v_compte_receveur then
            code_retour := 'Erreur ! Vous ne pouvez pas donner les poissons à un autre personnage de votre compte !';
            return code_retour;
        end if;   
    end if;
-- objet dans l'inventaire ?
    select into v_temp perobj_obj_cod from perso_objets
            where perobj_perso_cod = donneur
            and perobj_obj_cod = num_objet;
    if not found then
        code_retour := 'Erreur ! L''objet n''est pas dans l''inventaire !';
        return code_retour;
    end if;
/*********************************************************/
/* C O N T R O L E S  O K --> S U I T E                  */
/*********************************************************/
-- enlevage de PA
    update perso
        set perso_pa = perso_pa - 1
        where perso_cod = donneur;
-- Transfert de propriété
    update perso_objets
        set perobj_perso_cod = receveur
        where perobj_obj_cod = num_objet;       
-- Evènement
    texte_evt := '[attaquant] a généreusement offert un poisson à [cible]';
    insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
        values(49,now(),donneur,texte_evt,'O','O',donneur,receveur);
    insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
        values(49,now(),receveur,texte_evt,'N','O',donneur,receveur);
-- Si le reveceveur est un snorkys
    if v_race_receveur = 45 then
        des := lancer_des(1,100);
        des := des + v_niveau;
        if des < 5 then -- don de rune
        		if lancer_des(1,10) = 1 then
        			des := lancer_des(1,14) + 26;
        			v_temp := cree_objet_perso_nombre(des,donneur,1);
       			code_retour := code_retour||'Le Snorky apprécie énormément votre geste, et vous offre une rune en échange !';
        		else
        			-- Renvoi de 2 poissons           
            	v_temp := f_del_objet(num_objet);
            	v_temp := cree_objet_perso_nombre(226,donneur,2);
            	-- EVENEMENT 
            	texte_evt := '[attaquant] a généreusement offert deux poissons à [cible]';
            	insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                	values(49,now(),donneur,texte_evt,'O','O',receveur,donneur);
            	insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                	values(49,now(),receveur,texte_evt,'N','O',receveur,donneur);         
            	code_retour := code_retour||'Deux tu l''as vaut mieux qu''un tu l''avais !';
        		end if;
        elsif des < 25 then
                -- On s'assure que le Snorky a assez de PAs
                select into v_pa perso_pa from perso where perso_cod = receveur;
                if v_pa < 4 then
                   update perso
                   set perso_pa = 4
                   where perso_cod = receveur;
                end if;
                delete from perso_nb_sorts where pnbs_perso_cod = receveur;
            temp_var := nv_magie_bipbip(receveur,donneur,1);
                code_retour := code_retour||' Celui-ci vous lance Bip-Bip, étrange ce poisson';
        elsif des < 45 then
                -- On s'assure que le Snorky a assez de PAs
                select into v_pa perso_pa from perso where perso_cod = receveur;
                if v_pa < 8 then
                   update perso
                   set perso_pa = 8
                   where perso_cod = receveur;
                end if;
             	insert into perso_sorts(psort_perso_cod,psort_sort_cod) values (receveur,35);
             	delete from perso_nb_sorts where pnbs_perso_cod = receveur;
            	temp_var := nv_magie_morsure_soleil(receveur,donneur,1);
            	delete from perso_sorts where psort_perso_cod = receveur and psort_sort_cod = 35;
                code_retour := code_retour||'Le Snorky semble ne pas apprécier votre cadeau. Il vous lance le sort <b>morsure du soleil</b>.';
        elsif des < 55 then
                -- On s'assure que le Snorky a assez de PAs
                select into v_pa perso_pa from perso where perso_cod = receveur;
                if v_pa < 8 then
                   update perso
                   set perso_pa = 8
                   where perso_cod = receveur;
                end if;
             	insert into perso_sorts(psort_perso_cod,psort_sort_cod) values (receveur,12);
             	delete from perso_nb_sorts where pnbs_perso_cod = receveur;
            	temp_var := nv_magie_melasse(receveur,donneur,1);
            	delete from perso_sorts where psort_perso_cod = receveur and psort_sort_cod = 12;
                code_retour := code_retour||'Le Snorky semble ne pas apprécier votre cadeau. Il vous lance le sort <b>mélasse</b>.';
        elsif des < 65 then
            v_code_message := nextval('seq_msg_cod');
            insert into messages(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
                values(v_code_message,now(),'Oh OoooOooh','Poisson ?<BR /> Huh ?<BR /> Mmmmm...<BR />Gloup...<BR /> Miam, meci!<BR />',now());
            insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod)
                values(v_code_message,donneur);
            insert into messages_exp (emsg_msg_cod,emsg_perso_cod)
                values(v_code_message,receveur);       
        elsif des < 85 then
            -- Renvoi de 2 poissons           
            v_temp := f_del_objet(num_objet);
            v_temp := cree_objet_perso_nombre(226,donneur,2);
            -- EVENEMENT 
            texte_evt := '[attaquant] a généreusement offert deux poissons à [cible]';
            insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                values(49,now(),donneur,texte_evt,'O','O',receveur,donneur);
            insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
                values(49,now(),receveur,texte_evt,'N','O',receveur,donneur);         
            code_retour := code_retour||'Deux tu l''as vaut mieux qu''un tu l''avais !';
        elsif des < 110 then
            -- don de 10 Br
            update perso
            set perso_po = perso_po + 10
            where perso_cod = donneur;
            code_retour := code_retour||' Celui-ci est super content, il vous donne 10 Br: Pas grand chose. Les snorkies sont un peu radins.';
        else
            -- don de 500 Br
            update perso
            set perso_po = perso_po + 500
            where perso_cod = donneur;
            code_retour := code_retour||' Celui-ci est super content, il vous donne 500 Br: Ca doit cacher quelque chose...';
        end if;
    end if;                   
-- on vérifie qu'on ne puisse pas faire un gros poisson chez le receveur
    select into nb_poiss count(obj_cod)
        from objets,perso_objets
        where perobj_perso_cod = receveur
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = 226;
    if nb_poiss >= 5 then
    -- on retire 5 petits poissons
        for ligne in
            select perobj_obj_cod
                from objets,perso_objets   
                where perobj_perso_cod = receveur
                and perobj_obj_cod = obj_cod
                and obj_gobj_cod = 226
                limit 5 loop
            v_temp := f_del_objet(ligne.perobj_obj_cod);
        end loop;
    -- on ajoute un gros poisson
        v_temp := cree_objet_perso_nombre(227,receveur,1);
    end if;
-- code retour
    code_retour  := '<br>'||nom_cible||' a bien reçu votre poisson.<br>'||code_retour;
    return code_retour;
end;$function$

