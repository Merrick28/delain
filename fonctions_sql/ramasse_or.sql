CREATE OR REPLACE FUNCTION public.ramasse_or(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function ramasse_arme : ramasse de l or posé au sol et        */
/*          l ajoute dans l inventaire du perso                  */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = or_cod                                                */
/* Le code sortie est un entier                                  */
/*    0 = tout s est bien passé                                  */
/*    1 = le perso et l ore ne sont pas sur la même position     */
/*    2 = le perso n a pas assez de pa                           */
/*    3 = perso non trouvé                                       */
/*    4 = or non trouvée                                         */
/*    5 = position perso non trouvée                             */
/*    6 = position or non trouvée                                */
/*****************************************************************/
/* Créé le 11/03/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour text;
	personnage alias for $1;
	num_or alias for $2;
	qte_or or_position.por_qte%type;
	compt integer;
	pos_perso positions.pos_cod%type;
	pos_or positions.pos_cod%type;
	pa perso.perso_pa%type;
	texte_evt text;
	nom_perso perso.perso_nom%type;
	tangibilite text;
	cout_pa integer;
	or_palpable text;

begin
/**************************************/
/* Etape 1                            */
/* On cherche le perso                */
/**************************************/
	select into compt,tangibilite perso_cod,perso_tangible from perso where perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Perso non trouvé !';
		return code_retour;
	end if;
/**************************************/
/* Etape 2                            */
/* On cherche l or                    */
/**************************************/
	select into compt por_cod from or_position where por_cod = num_or;
	if not found then
		code_retour := '<p>Erreur ! Brouzoufs non trouvés !';
		return code_retour;
	end if;
/**************************************/
/* Etape 3                            */
/* On cherche le pos_cod du perso     */
/**************************************/
	select into pos_perso,nom_perso ppos_pos_cod,perso_nom from perso_position,perso
			where ppos_perso_cod = personnage
			and perso_cod = personnage;
	if not found then								-- if compt_pos != 0
		code_retour := '<p>Erreur ! Position du ramasseur non trouvée !';
		return code_retour;
	end if;							-- if compt_pos != 0

/**************************************/
/* Etape 4                            */
/* Interdit de ramasser pendant un défi*/
/**************************************/
	if exists(select 1 from defi where defi_statut = 1 and personnage in (defi_lanceur_cod, defi_cible_cod)
		UNION ALL select 1 from defi
			inner join perso_familier on pfam_perso_cod in (defi_lanceur_cod, defi_cible_cod)
			where defi_statut = 1 and pfam_familier_cod = personnage)
	then
		code_retour := '<p>Anomalie : il est interdit de ramasser des brouzoufs pendant un défi !</p>';
		return code_retour;
	end if;
/**************************************/
/* Etape 5                            */
/* On cherche le pos_cod de l or      */
/**************************************/	
	if tangibilite = 'O' then
		cout_pa := getparm_n(41);
	else
		cout_pa := getparm_n(42);
	end if;
/********************************/
/* Etape 7                      */
/* On verifie les PA du perso   */
/********************************/
	select into pa perso_pa from perso where perso_cod = personnage;
	if pa < cout_pa then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de PA pour ramasser ces brouzoufs !';
		return code_retour;
	end if; 			
	select into pos_or,qte_or,or_palpable por_pos_cod,por_qte,por_palpable from or_position
		where por_cod = num_or;
	if or_palpable = 'N' then
		update perso set perso_pa = perso_pa - cout_pa where perso_cod = personnage;
		delete from or_position where por_cod = num_or;
		code_retour := '<p>A peine vous approchez vous du tas de brouzoufs que celui-ci disparait. Vous comprenez qu''il s''agissait de brouzoufs de farfadets....';
		return code_retour;
	end if;
/********************************************/
/* Etape 6                                  */
/* On verifie la correspondance des pos_cod */
/********************************************/	
	if pos_perso != pos_or then
		code_retour := '<p>Erreur ! Distance trop éloignée !';
		return code_retour;
	end if; 							-- if pos_perso != pos_arme
/********************************/
/* Etape 7                      */
/* On verifie les PA du perso   */
/********************************/
	select into pa perso_pa from perso where perso_cod = personnage;
	if pa < cout_pa then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de PA pour ramasser ces brouzoufs !';
		return code_retour;
	end if; 							-- if pa < 1	
/********************************/
/* Etape 8                      */
/* on valide les changements    */
/********************************/
-- 8.1 : on supprime le or_position
	delete from or_position where por_cod = num_or;
-- 8.2 : on rajoute l or dans l inventaire du perso
	update perso set perso_po = (perso_po + qte_or) where perso_cod = personnage;
-- 8.3 : on enlève les pa au perso
	update perso set perso_pa = (pa - cout_pa) where perso_cod = personnage;
-- 8.4 : on rajoute un évènement
	texte_evt := '[perso_cod1] a ramassé '||to_char(qte_or,'99999999')||' brouzoufs.';
	insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values (nextval('seq_levt_cod'),4,now(),1,personnage,texte_evt,'O','O');
	code_retour := '<p>Vous avez ramassé '||trim(to_char(qte_or,'99999999'))||' brouzoufs.';
	return code_retour;
end;	
$function$

