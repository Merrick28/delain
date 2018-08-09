CREATE OR REPLACE FUNCTION public.trg_new_evt_quete()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/* À la création d'un nouvel événement,
   On regarde si cet événement peut correspondre 
   à la fin d'une étape de quête automatique, en lisant les 
   événements potentiels dans une table, et si c'est le cas, on
   regarde de plus près, pour valider l'étape le cas échéant. */

declare
    v_validation integer;
begin
    select into v_validation count(1) from quetes.perso_quete_automatique_etape_evt where NEW.levt_tevt_cod = eeqaperso_tevt_cod and NEW.levt_perso_cod1 = eeqaperso_perso_cod;
    if (v_validation = 0) then
        return NEW;
    end if;
    -- On a une situation de fin d'étape potentielle. On peut prendre le temps de regarder d'un peu plus près.
    -- On détaille selon le type de fin de quête.

    -- Si étape non terminée, on return simplement.
    -- Dans le cas contraire, on supprime la ligne du v_validation (limit 1. Le perso peut être dans plusieurs quêtes nécessitant le même type d'étape), puis on avance d'une étape, en rajoutant une ligne pour la nouvelle validation (On peut probablement faire le tout par trigger sur la table de perso_etape), et enfin on return

    return NEW;
end;$function$

