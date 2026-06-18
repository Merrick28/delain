CREATE OR REPLACE FUNCTION public.f_compteur_modif( integer,integer, text, integer)
    RETURNS numeric
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE
AS $_$/*****************************************************************/
/* function f_compteur_modif : modifi un compteur global         */
/*          ou individuel                                        */
/* On passe en paramètres                                        */
/*    $1 = compteur_cod                                          */
/*    $2 = perso_cod                                             */
/* $3 = valeur au format dé rollist                              */
/* $4 = sens 0=assigner, 1=incrémenter, -1=décrémerter           */
/* 	retourne la valeur du compteur sinon null                    */
/*****************************************************************/
/* Créé le 17/06/2026                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare

    v_compteur_cod alias for $1;
    v_perso_cod alias for $2;
    v_valeur_roliste alias for $3;
    v_sens alias for $4;

    v_compteur_type integer;
    v_compteur_init numeric;
    v_compteur_min numeric;
    v_compteur_max numeric;
    v_valeur numeric;
    v_comptval_cod integer;

begin

    select compteur_type, compteur_init , compteur_min , compteur_max into v_compteur_type, v_compteur_init, v_compteur_min, v_compteur_max from compteur where compteur_cod = v_compteur_cod;
    if not found then
        return null;
    end if;

    -- lecture de la valeur
    if v_valeur_roliste ~ '^-?[0-9]+([.][0-9]+)?$' then
        v_valeur := v_valeur_roliste::numeric;
    else
        v_valeur := f_lit_des_roliste(v_valeur_roliste);
    end if;

    if v_compteur_type = 0 then

        -- compteur global (on ignore le perso_cod)
        select comptval_cod into v_comptval_cod from compteur_valeur where comptval_compteur_cod = v_compteur_cod and comptval_perso_cod is null;
        if not found then
            -- pas de valeur existante, on crée la ligne
            insert into compteur_valeur (comptval_compteur_cod, comptval_perso_cod, comptval_valeur) values (v_compteur_cod, null, v_compteur_init)
                returning comptval_cod into v_comptval_cod;
        end if;

    else

        -- vérifier que le perso existe
        if not exists (select 1 from perso where perso_cod = v_perso_cod) then
            return null;
        end if;

        -- compteur individuel (on prend en compte le perso_cod)
        select comptval_cod into v_comptval_cod from compteur_valeur where comptval_compteur_cod = v_compteur_cod and comptval_perso_cod = v_perso_cod;
        if not found then
            -- pas de valeur existante, on crée la ligne
            insert into compteur_valeur (comptval_compteur_cod, comptval_perso_cod, comptval_valeur) values (v_compteur_cod, v_perso_cod, v_compteur_init)
                returning comptval_cod into v_comptval_cod;
        end if;

    end if;

    if (v_sens = 0) then
        -- on assigne la valeur au  compteur
        update compteur_valeur set comptval_valeur = v_valeur where comptval_cod = v_comptval_cod;

    elsif (v_sens = 1) then
        -- on ajoute la valeur au compteur
        update compteur_valeur set comptval_valeur = comptval_valeur  + v_valeur where comptval_cod = v_comptval_cod;

    elsif (v_sens = -1) then
        -- on retire la valeur au compteur
        update compteur_valeur set comptval_valeur = comptval_valeur  - v_valeur where comptval_cod = v_comptval_cod;

    end if;

    -- récupérer la valeur du compteur après modification
    select comptval_valeur into v_valeur from compteur_valeur where comptval_cod = v_comptval_cod;

    -- ajouter un controle des limites
    if v_compteur_min is not null and v_valeur < v_compteur_min then
        v_valeur := v_compteur_min;
    end if;

    if v_compteur_max is not null and v_valeur > v_compteur_max then
        v_valeur := v_compteur_max;
    end if;

    -- mettre à jour la valeur du compteur si elle a été modifiée par les limites
    update compteur_valeur set comptval_valeur = v_valeur where comptval_cod = v_comptval_cod;


    return v_valeur;

end;$_$;

ALTER FUNCTION public.f_compteur_modif(integer,integer, text, integer) OWNER TO delain;
