CREATE OR REPLACE FUNCTION public.entretien_mines()
 RETURNS text
 LANGUAGE plpgsql
 STRICT
AS $function$/*****************************************************************/
/* function entretien_mines: Rajoute des murs creusables dans les*/
/*   mines. Pour chaque étage, on regarde combien il y a de murs */
/*   creusables, combien il en manque en fonction du taux prévu  */
/*   et on rajoute pour chaque etage le nombre de nouveaux murs  */
/*   calculés. Pour éviter les avalanches et les blocages, on    */
/*   ne rajoute à chaque fois qu''une partie des murs prévus     */
/*****************************************************************/
declare
    cases_total integer; -- Nombre de cases sans mur de l''étage
    mines_total integer; -- Nombre de murs creusables de l''étage
    mines integer;       -- Nombre actuel de murs creusables de l''étage
    eboulements integer; -- Nombre de nouveaux murs creusables de l''etage
    
    max_murs integer; -- Taux maximal de murs à rajouter (pas d''avalanche)
    code_retour text;
    ligne record; 
    
begin
    code_retour := '';
    max_murs := 5; -- En pourcentage, arrondi au supérieur
    for ligne in (select etage_numero, etage_mine from etage where etage_mine != 0) loop
        select into cases_total count (1) from positions 
            where pos_etage = ligne.etage_numero
            and not exists (select 1 from murs where mur_pos_cod = pos_cod and mur_creusable = 'N');
        mines_total := 1 + cases_total * ligne.etage_mine / 1000;
        -- Nombre maximal de murs creusables à rajouter: 1 + mines*max_murs/100
        eboulements := 1 + mines_total*max_murs/100;
        select into mines count(1) from murs m
            left join positions p on p.pos_cod = m.mur_pos_cod and p.pos_etage = ligne.etage_numero
            where m.mur_creusable = 'O' and p.pos_etage = ligne.etage_numero;
        code_retour := code_retour || 'Pour l''étage ' || trim(to_char(ligne.etage_numero,'999'))
            || ' il y a ' || trim(to_char(mines,'9999')) || ' murs creusables sur les ' 
            || trim(to_char(mines_total,'9999')) || ' prévus.';
        if ( mines < mines_total - eboulements ) then
            code_retour := code_retour || ' On en rajoute ' || trim(to_char(eboulements,'9999')) || E'\\
';
            -- eboulement
            code_retour := code_retour || eboulement(ligne.etage_numero, eboulements);
        else
            code_retour := code_retour || E'\\
';
        end if;
    end loop;
    perform init_automap();
    return code_retour;
end;$function$

