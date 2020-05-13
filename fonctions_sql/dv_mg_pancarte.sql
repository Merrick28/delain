CREATE or replace FUNCTION public.dv_mg_pancarte(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function dv_mg_pancarte: lance le sort pancarte               */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        doit être fixé à 3                                     */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 23/02/2016                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
    code_retour              text; -- chaine html de sortie
    texte_evt                text; -- texte pour évènements
    nom_sort                 text; -- nom du sort
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
    v_niveau_lanceur         integer;
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    perdesc                  text;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 integer; -- PX gagnes
    v_bonus                  integer;
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne_rune               record; -- record des rune à dropper
    temp_ameliore_competence text;
    -- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_pv_cible               integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 171;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible,v_pv_cible perso_nom, perso_pv_max
    from perso
    where perso_cod = cible;
    select into nom_sort sort_nom
    from sorts
    where sort_cod = num_sort;
    magie_commun_txt := magie_commun_dieu(lanceur, cible, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);

    select into perdesc perso_description from perso where perso_cod = cible;

    CASE
        WHEN char_length(perdesc) < 62 THEN
            perdesc := perdesc ||
                       ' - Tonto est mon dieu, ma raison de vivre, mon seul et unique ami en fait …  La dive bouteille, instrument de sa gloire, m''accompagne en tous lieux !  La tempérance mourra par ma chope: santé !';
        WHEN char_length(perdesc) BETWEEN 62 AND 96 THEN
            perdesc := perdesc ||
                       ' - Tonto est mon dieu, ma raison de vivre, mon seul et unique ami en fait …  La dive bouteille, instrument de sa gloire, m''accompagne en tous lieux !  Santé !';
        WHEN char_length(perdesc) BETWEEN 97 AND 144 THEN
            perdesc := perdesc ||
                       ' - Tonto est mon dieu, ma raison de vivre, mon seul et unique ami en fait …  Vive la dive bouteille !  Santé !';
        WHEN char_length(perdesc) BETWEEN 145 AND 169 THEN
            perdesc := perdesc ||
                       ' - Tonto est mon dieu, ma raison de vivre, mon seul et unique ami en fait …   Santé !';
        WHEN char_length(perdesc) BETWEEN 170 AND 201 THEN
            perdesc := perdesc || ' - Tonto est mon dieu, ma raison de vivre …   Santé !';
        WHEN char_length(perdesc) BETWEEN 202 AND 221 THEN
            perdesc := perdesc || ' - Tonto est mon dieu …   Santé !' ;
        WHEN char_length(perdesc) BETWEEN 222 AND 245 THEN
            perdesc := perdesc || ' - Santé !' ;
        ELSE
            perdesc := perdesc;

        END CASE;


    update perso set perso_description = perdesc where perso_cod = cible;

    return code_retour;
end;

$_$;


ALTER FUNCTION public.dv_mg_pancarte(integer, integer, integer) OWNER TO delain;
