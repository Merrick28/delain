CREATE OR REPLACE FUNCTION public.resiste_magie(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function resiste_magie : tente une resistance magique         */
/* On passe en paramètres                                        */
/*    $1 = perso_cod cible                                       */
/*    $2 = perso_cod attaquant                                   */
/*    $3 = niveau du sort lancé                                  */
/* Le code sortie est un entier                                  */
/*     0 = resistance ratée                                      */
/*     1 = resistance réussie                                    */
/*     2 = resistance critique                                   */
/*     autre = anomalie :                                        */
/*        -1 = perso non trouvé                                  */
/*        -2 = pas la compétence                                 */
/*****************************************************************/
/* Créé le 30/09/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer;
	v_cible alias for $1;
	v_attaquant alias for $2;
	niveau_sort alias for $3;
	niveau_attaquant integer;
	niveau_cible integer;
	nombre_esquive integer;
	seuil integer;
	des integer;

begin
	code_retour := 0;
-- on récupère les données de l'attaquant
	select into niveau_attaquant
		perso_niveau
		from perso
		where perso_cod = v_attaquant;
	if niveau_attaquant is null then
		code_retour := -1;
		return code_retour;
	end if;
-- on récupère les données de la cible
	select into niveau_cible,nombre_esquive
		perso_niveau,perso_nb_esquive
		from perso
		where perso_cod = v_cible;
	if niveau_cible is null then
		code_retour := -1;
		return code_retour;
	end if;
-- on rajoute une esquive
	update perso set perso_nb_esquive = perso_nb_esquive + 1 where perso_cod = v_cible;
-- on calcule le seuil de résistance
	seuil := (niveau_cible * 4) + (niveau_cible - niveau_attaquant) - niveau_sort;
	if nombre_esquive != 0 then
		seuil := round(seuil / (1.5 * nombre_esquive));
	end if;
	if lancer_des(1,100) > seuil then
	-- resistance ratée
		code_retour := 0;	
		return code_retour;
	else
		code_retour := 1;
		return code_retour;		
	end if;
end;
$function$

CREATE OR REPLACE FUNCTION public.resiste_magie(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function resiste_magie : tente une resistance magique (012009 */
/* On passe en paramètres                                        */
/*    $1 = perso_cod cible                                       */
/*    $2 = perso_cod attaquant                                   */
/*    $3 = niveau du sort lancé                                  */
/*    $4 = réussite du lancer                                    */
/* Le code sortie est un entier                                  */
/*     0 = resistance ratée                                      */
/*     1 = resistance réussie                                    */
/*     2 = resistance critique                                   */
/*     autre = anomalie :                                        */
/*        -1 = perso non trouvé                                  */
/*        -2 = pas la compétence                                 */
/*****************************************************************/
/* Créé le 30/09/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer;
	v_cible alias for $1;
	v_attaquant alias for $2;
	niveau_sort alias for $3;
        v_reussite alias for $4;
	niveau_attaquant integer;
	niveau_cible integer;
	des integer;
        v_renomee numeric;      -- renommée magique du lanceur
        v_int_cible integer;    -- intelligence de la cible
        v_con_cible integer;    -- constitution de la cible
        v_seuil integer;        -- seuil dans les calculs
        v_bonmal integer;       -- bonus malus dans les calculs
        compt integer;          -- fourre tout
        v_RM1 integer;          -- Base de la RM1 dans les calculs
        v_niv_seuil integer;    -- Seuil dans les calculs en fonction du niv

begin
	code_retour := 0;
-- on récupère les données de l'attaquant
	select into niveau_attaquant, v_renomee
		perso_niveau,perso_renommee_magie
		from perso
		where perso_cod = v_attaquant;
	if niveau_attaquant is null then
		code_retour := -1;
		return code_retour;
	end if;
-- on récupère les données de la cible
	select into niveau_cible,v_int_cible,v_con_cible
		perso_niveau,perso_int,perso_con
		from perso
		where perso_cod = v_cible;
	if niveau_cible is null then
		code_retour := -1;
		return code_retour;
	end if;
-- on calcule le seuil de résistance (en fonction de l'int, la con le niv du sort et la marge de réussite
        v_con_cible := floor(v_con_cible/10);
	v_RM1 := (v_int_cible * 5) + v_con_cible;
        compt := 0;
        compt := (7 * niveau_sort);
        compt := compt + v_reussite;
-- calcul du seuil effectif
        v_seuil = v_RM1 - compt;
-- on limite une premiere fois le seuil à 25, afin d'apliquer le bonmalus sur 25 au mini
        if v_seuil < 25 then
        v_seuil := 25;
        end if;
-- application du bonus-malus au seuil mini on défini un seuil mini de 25%
-- qui évolue en fonction du niveau de la cible et de la renomee du lanceur
        if v_renomee < 0 then
        v_renomee := 0;
        end if;
        -- passage de la renomee magique en entier
        v_renomee := floor(v_renomee/1);
        -- calcul du bonus malus
        v_niv_seuil := niveau_cible * 10;
        v_bonmal := floor ((v_renomee - v_niv_seuil)/100);
       -- on fait varier le seuil en fonction de l'ajustement bonus malus
       v_seuil := v_seuil - v_bonmal;
       -- limitation du seuil mini
      if v_seuil < 5 then
      v_seuil := 5;
      end if;
 -- le seuil (v_seuil) est maintenant calculé on peut tester
	if lancer_des(1,100) > v_seuil then
	-- resistance ratée
		code_retour := 0;	
		return code_retour;
	else
		code_retour := 1;
		return code_retour;		
	end if;
end;
$function$

