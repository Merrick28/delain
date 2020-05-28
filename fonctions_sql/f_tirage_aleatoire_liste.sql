
CREATE or replace FUNCTION public.f_tirage_aleatoire_liste(text, text, json) RETURNS integer
    LANGUAGE plpgsql
AS
$_$
declare
  key_cod alias for $1;
  key_tx alias for $2;
  list alias for $3;

  ligne record;                -- Une ligne d’enregistrements
  taux numeric;
  tirage numeric;

begin

    -- calcul du taux max
    taux:= 0 ;
    for ligne in (select value from json_array_elements(list)  )
    loop
        taux := taux + f_to_numeric(ligne.value->>key_tx::text) ;
    end loop;

    if taux = 0 then
        return 0 ;		-- pas de tirage possible
    end if;

    if taux < 100 then
        taux := 100 ;		-- tirage sur 100% au dessus du taux réel, c'est un echec
    end if;

    tirage := random() * taux  ;	-- tirage aléatoire

    -- maintenant rechercher la clé correspondante
    taux:= 0 ;
    for ligne in (select value from json_array_elements(list)  )
    loop
        taux := taux + f_to_numeric(ligne.value->>key_tx::text) ;
        if tirage <= taux then
            return f_to_numeric(ligne.value->>key_cod::text) ;
        end if;
    end loop;

    -- tirage hors liste !
    return 0;

end;
$_$;
ALTER FUNCTION public.f_tirage_aleatoire_liste(text, text, json) OWNER TO delain;
