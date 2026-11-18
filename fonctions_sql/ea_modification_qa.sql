create or replace function ea_modification_qa(integer, integer, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_modification_qa                             */
/* lancé par les effet auto                       */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du perso)             */
/*   $2 = Perso ciblé                             */
/*   $3 = Probabilité de declencehement           */
/*   $4 = Message d’événement associé             */
/*   $5 = Paramètre additionnels                  */
/**************************************************/
declare
    -- Parameters
    v_perso_cod alias for $1;
    v_cible_cod alias for $2;
    v_proba alias for $3;
    v_texte_evt alias for $4;
    v_params alias for $5;

    code_retour text;
    v_perso_nom text;
    v_cible_nom text;
    v_qa_cod integer;
    v_nb_qa integer;
    sens integer;
    taux numeric;
    qa_perso text;
    v_count_elem integer;
    v_max_ordre_elem integer;
    v_aqelem_cod integer;        -- élément correspondant au perso
    v_aqelem_vide integer;       -- élément « vide » (aqelem_misc_cod null) réutilisable
    v_aqetape_cod integer;
    v_ajout boolean;
    v_retrait boolean;
    ligne record;                -- Une ligne d’enregistrements

begin

    -- Chances de déclencher l’effet
    if random() > v_proba then
        return '';
    end if;
    -- Initialisation des conteneurs
    code_retour := '' ;

    -- nom source et cible
    select perso_nom into v_perso_nom from perso where perso_cod = v_perso_cod;
    select perso_nom into v_cible_nom from perso where perso_cod = COALESCE( v_cible_cod,v_perso_cod) ;

    -- boucle sur la liste des mécanismes
    v_nb_qa := 0 ;  -- comptage des qa modifiées!
    for ligne in (select value from json_array_elements((v_params->>'fonc_trig_qa')::json)  )
    loop

        taux := f_to_numeric(ligne.value->>'taux'::text) / 100 ;
        sens := f_to_numeric(ligne.value->>'sens'::text)::integer;
        qa_perso := coalesce(ligne.value->>'qa_perso'::text, 'N')::text;

        if (taux=0) or (random() <= taux) then

            -- activation/desactivation de la QA !!! sens: 0: activer, -1: desactiver, 2: inverser

            v_qa_cod := f_to_numeric(ligne.value->>'qa_cod'::text)::integer;

            if qa_perso != 'O' then

                -- ============================= TRAITEMENT DES QA NORMALES =============================
                v_nb_qa := v_nb_qa + 1;     -- +1 sur le compteur de QA modifiées

                if sens = 2 then
                    -- inverser
                    update quetes.aquete set aquete_actif = CASE WHEN aquete_actif='O' THEN 'N' ELSE 'O' END where aquete_cod = v_qa_cod ;
                elsif sens = -1 then
                    -- desactiver
                    update quetes.aquete set aquete_actif='N' where aquete_cod = v_qa_cod ;
                else
                    -- activer
                    update quetes.aquete set aquete_actif='O' where aquete_cod = v_qa_cod ;
                end if;

            else

                -- ============================= TRAITEMENT DES QA PERSO =============================
                v_aqelem_cod := null;
                v_aqelem_vide := null;

                select aquete_etape_cod into v_aqetape_cod from quetes.aquete where aquete_cod = v_qa_cod ;

                select count(*), max(aqelem_param_ordre) into v_count_elem, v_max_ordre_elem
                from quetes.aquete_element
                where aqelem_aqetape_cod = v_aqetape_cod and aqelem_type = 'perso' and aqelem_param_id = 1 and aqelem_aqperso_cod is null ;

                -- seulement si la QA attend bien un paramètre de type perso, on modifie le paramètre au lieu de la QA elle-même
                if v_count_elem > 0 then

                    -- le perso est-il déjà dans la QA ?
                    select aqelem_cod into v_aqelem_cod
                        from quetes.aquete_element
                        where aqelem_aqetape_cod = v_aqetape_cod and aqelem_type = 'perso' and aqelem_param_id = 1 and aqelem_aqperso_cod is null and aqelem_misc_cod = v_perso_cod
                        limit 1;

                    -- décision de l’action
                    v_ajout := false;
                    v_retrait := false;
                    if sens = 2 then
                        -- inverser
                        if v_aqelem_cod is not null then
                            v_retrait := true;
                        else
                            v_ajout := true;
                        end if;
                    elsif sens = -1 then
                        -- retirer, seulement s’il y est
                        v_retrait := (v_aqelem_cod is not null);
                    else
                        -- ajouter, seulement s’il n’y est pas déjà
                        v_ajout := (v_aqelem_cod is null);
                    end if;

                    if v_retrait then
                        v_nb_qa := v_nb_qa + 1;     -- +1 sur le compteur de QA modifiées
                        if v_count_elem = 1 then
                            -- seul élément : on garde le paramètre, mais on le vide
                            update quetes.aquete_element set aqelem_misc_cod = null where aqelem_cod = v_aqelem_cod ;
                        else
                            delete from quetes.aquete_element where aqelem_cod = v_aqelem_cod ;
                        end if;

                    elsif v_ajout then
                        v_nb_qa := v_nb_qa + 1;     -- +1 sur le compteur de QA modifiées

                        -- un élément vide existe-t-il ? On le réutilise plutôt que d’en insérer un nouveau
                        select aqelem_cod into v_aqelem_vide
                            from quetes.aquete_element
                            where aqelem_aqetape_cod = v_aqetape_cod and aqelem_type = 'perso' and aqelem_param_id = 1 and aqelem_aqperso_cod is null and aqelem_misc_cod is null
                            limit 1;

                        if v_aqelem_vide is not null then
                            update quetes.aquete_element set aqelem_misc_cod = v_perso_cod where aqelem_cod = v_aqelem_vide ;
                        else
                            insert into quetes.aquete_element(aqelem_aquete_cod, aqelem_aqetape_cod, aqelem_param_id, aqelem_type, aqelem_misc_cod, aqelem_param_ordre)
                                values (v_qa_cod, v_aqetape_cod, 1, 'perso', v_perso_cod, coalesce(v_max_ordre_elem, 0) + 1);
                        end if;

                    end if; -- fin de test sur l’action à effectuer
                end if; -- fin de test sur l’existence de la QA avec un paramètre perso

            end if; --fin de test sur qa_perso
        end if; -- fin de test sur la probabilité de déclenchement (taux)

    end loop;

    -- On rajoute la ligne d’événements
    if v_texte_evt != '' and v_nb_qa > 0 then
        code_retour := code_retour || replace( replace( v_texte_evt, '[attaquant]', v_perso_nom),  '[cible]', v_cible_nom) ;
        if strpos(v_texte_evt , '[cible]') != 0 then
            perform insere_evenement(v_perso_cod, v_cible_cod, 54, v_texte_evt, 'O', 'N', null);
        else
            perform insere_evenement(v_perso_cod, v_cible_cod, 54, v_texte_evt, 'O', 'O', null);
        end if;
    end if;

    return code_retour;
end;$_$;