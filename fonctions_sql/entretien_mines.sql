
--
-- Name: entretien_mines(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.entretien_mines() RETURNS text
    LANGUAGE plpgsql STRICT
    AS $$/*****************************************************************/
/* function entretien_mines: Rajoute des murs creusables dans les*/
/*   mines. Pour chaque étage, on regarde combien il y a de murs */
/*   creusables, combien il en manque en fonction du taux prévu  */
/*   et on rajoute pour chaque etage le nombre de nouveaux murs  */
/*   calculés. Pour éviter les avalanches et les blocages, on    */
/*   ne rajoute à chaque fois qu''une partie des murs prévus     */
/*                                                               */
/* Marlyza 27/06/2026 : ajout du mode terrain (si etage_mine < 0)*/
/* pour ne rajouter des murs que sur les cases de  type de       */
/* terrain mur (pos_ter_cod=17)                                  */
/*****************************************************************/
declare
cases_total integer; -- Nombre de cases sans mur de l''étage
    mines_total integer; -- Nombre de murs creusables de l''étage
    mines integer;       -- Nombre actuel de murs creusables de l''étage
    eboulements integer; -- Nombre de nouveaux murs creusables de l''etage

    max_murs integer; -- Taux maximal de murs à rajouter (pas d''avalanche)
    code_retour text;
    ligne record;

    mode_terrain boolean; -- true si etage_mine négatif (mode pos_ter_cod=17 code du terrain type mur)

begin
    code_retour := '';
    max_murs := 5; -- En pourcentage, arrondi au supérieur

    for ligne in (select etage_numero, etage_mine from etage where etage_mine != 0)
    loop

        mode_terrain := (ligne.etage_mine < 0);

        if mode_terrain then
            -- comptage de toutes les cases qui ne sont pas des murs non-creusables et qui sont sur un terrain de type mur (pos_ter_cod=17)
            select into cases_total count(1) from positions
                where pos_etage = ligne.etage_numero
                  and pos_ter_cod=17
                  and not exists (select 1 from murs where mur_pos_cod = pos_cod and mur_creusable = 'N');

            -- si etage_mine=-1, alors 100% des cases terrains (murs) seront entretenu par des mines,
            -- si etage_mine=-500, alors 50% des cases sont des mines, etc.
            -- si etage_mine=-1000, alors 0% des cases sont des mines, mais on garde au moins 1 mine
            mines_total := GREATEST(1, cases_total - cases_total * abs(ligne.etage_mine) / 1000);

            -- Nombre maximal de murs creusables à rajouter (max 5% par jours)
            eboulements := 1 + mines_total*max_murs/100;

            -- Mines actuelles creusables sur les cases pos_ter_cod=17
            select into mines count(1) from murs m
                inner join positions p on p.pos_cod = m.mur_pos_cod
                where p.pos_etage = ligne.etage_numero
                  and p.pos_ter_cod=17
                  and m.mur_creusable = 'O';

        else
            -- comptage de toutes les cases qui ne sont pas des murs non-creusables
            select into cases_total count (1) from positions
                where pos_etage = ligne.etage_numero
                  and not exists (select 1 from murs where mur_pos_cod = pos_cod and mur_creusable = 'N');

            mines_total := 1 + cases_total * ligne.etage_mine / 1000;

            -- Nombre maximal de murs creusables à rajouter: 1 + mines*max_murs/100
            eboulements := 1 + mines_total*max_murs/100;

            select into mines count(1) from murs m
                left join positions p on p.pos_cod = m.mur_pos_cod and p.pos_etage = ligne.etage_numero
                where m.mur_creusable = 'O' and p.pos_etage = ligne.etage_numero;

        end if;

        code_retour := code_retour || 'Pour l''étage ' || trim(to_char(ligne.etage_numero,'999'))
                || ' il y a ' || trim(to_char(mines,'9999')) || ' murs creusables sur les '
                || trim(to_char(mines_total,'9999')) || ' prévus.';

        if  (( mines < mines_total - eboulements ) and not mode_terrain) or (mode_terrain and (mines < mines_total))  then
            code_retour := code_retour || ' On en rajoute ' || trim(to_char(eboulements,'9999')) || E'\r\n';
            -- eboulement
            code_retour := code_retour || eboulement(ligne.etage_numero, eboulements);
        else
            code_retour := code_retour || E'\r\n';
        end if ;

    end loop;

    perform init_automap();
    return code_retour;
end;$$;


ALTER FUNCTION public.entretien_mines() OWNER TO delain;
