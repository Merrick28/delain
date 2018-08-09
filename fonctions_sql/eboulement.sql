CREATE OR REPLACE FUNCTION public.eboulement(etage_cod integer, nouveaux_murs integer)
 RETURNS text
 LANGUAGE plpgsql
 STRICT
AS $function$/*****************************************************************/
/* function eboulement : Rajoute N murs creusables dans l''étage */
/*   passé en paramètre, à une position libre, définie comme suit*/
/*   Pas de mur sur la position                                  */
/*   Pas de lieu à une distance de 1 ou moins                    */
/*   Les objets présents ont récupérables après creusage         */
/*   Les murs éboulés sont de grande qualité (1000)              */
/*   Leur résistance est faible (300)                            */
/* On passe en paramètres  :                                     */
/*    1 le code de l''étage                                      */
/*    2 le nombre de murs à rajouter                             */
/*****************************************************************/
declare
    v_etage_cod alias for $1;
    nouveaux_murs alias for $2;
    ligne record; -- Une ligne d'enregistrements
    code_retour text;
    compteur integer;
    v_mur_type integer;
    v_mur_richesse integer;
begin
    compteur := 0;
    code_retour := '';
    select into v_mur_type, v_mur_richesse etage_mine_type, etage_mine_richesse from etage 
        where etage_numero = v_etage_cod;
    for ligne in (select pos_cod from positions a where pos_etage = v_etage_cod and 
        not exists (select mur_pos_cod from murs where mur_pos_cod = a.pos_cod) and
        not exists (select ppos_pos_cod from perso_position where ppos_pos_cod = a.pos_cod) and
        not exists (select lpos_pos_cod from lieu_position,positions b
            where lpos_pos_cod = b.pos_cod
            and abs(a.pos_x - b.pos_x) <= 1
            and abs(a.pos_y - b.pos_y) <= 1
            and a.pos_etage = b.pos_etage)
        order by random() limit nouveaux_murs) loop
        insert into murs (mur_pos_cod, mur_type, mur_creusable, mur_usure, mur_richesse)
            values (ligne.pos_cod, v_mur_type, 'O', 300, v_mur_richesse);
        compteur := compteur + 1;
        code_retour := code_retour || 'Ajouté un mur à l''étage ' || trim(to_char(v_etage_cod,'999')) 
            || ' en position ' || trim(to_char(ligne.pos_cod,'9999999')) || E'\\
';
    end loop;
    
    if compteur != 0 then
        code_retour := code_retour || 'Ajouté ' || trim(to_char(compteur,'999'))
            || ' murs à l''etage ' || trim(to_char(v_etage_cod,'999')) || E'\\
';
    end if;
    
    return code_retour;

end;$function$

