CREATE OR REPLACE FUNCTION public.efface_perso(integer)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function efface_perso :Procédure d effacement de personnage   */
/*                                                               */
/* On passe en paramètres                                        */
/*    $1 = perso à effacer                                       */
/* Le code sortie est :                                          */
/*    numero de perso = Tout s''est bien passé                   */
/*****************************************************************/
/* Créé le 06/03/2003                                            */
/* Liste des modifications :                                     */
/*      rajout du nettoyage de la table cachette_perso           */
/*****************************************************************/
declare
	code_erreur integer;
	personnage alias for $1; 
temp integer;
	
begin
	code_erreur := personnage; /* par défaut, tout s est bien passé */
	delete from perso_competences where pcomp_perso_cod = personnage;
	delete from perso_position where ppos_perso_cod = personnage;
	delete from perso_objets where perobj_perso_cod = personnage;
	delete from messages_exp where emsg_perso_cod = personnage;
	delete from messages_dest where dmsg_perso_cod = personnage;
	delete from perso_compte where pcompt_perso_cod = personnage;
	delete from perso_banque where pbank_perso_cod = personnage;
	delete from lock_combat where lock_attaquant = personnage;
	delete from lock_combat where lock_cible = personnage;	
delete from magasin_gerant where mger_perso_cod = personnage;
delete from concentrations where concentration_perso_cod = personnage;
delete from dieu_perso where dper_perso_cod = personnage;
delete from guilde_perso where pguilde_perso_cod = personnage;
delete from perso_nb_comp where pnb_perso_cod = personnage;
delete from perso_sorts where psort_perso_cod = personnage;
delete from perso_identifie_objet where pio_perso_cod = personnage;
delete from perso_nb_sorts where pnbs_perso_cod = personnage;
delete from perso_nb_sorts_total where pnbst_perso_cod = personnage;
delete from perso_sorts where psort_perso_cod = personnage;
delete from perso_temple where ptemple_perso_cod = personnage;
delete from journal where journal_perso_cod = personnage;

delete from riposte where riposte_attaquant = personnage;
delete from riposte where riposte_cible = personnage;
delete from etage_visite where vet_perso_cod = personnage;
delete from quete_perso where pquete_perso_cod = personnage;
update vampire_hist set vamp_perso_pere = null where vamp_perso_pere = personnage;
update vampire_hist set vamp_perso_fils = null where vamp_perso_fils = personnage;
delete from transaction where tran_vendeur = personnage;
delete from transaction where tran_acheteur = personnage;
delete from peine where peine_perso_cod = personnage;
delete from peine where peine_magistrat = personnage;
delete from perso_titre where ptitre_perso_cod = personnage;
delete from groupe_perso where pgroupe_perso_cod = personnage;
delete from perso where perso_cod = personnage;
delete from cachettes_perso where persocache_perso_cod = personnage;
delete from tutorat where tuto_filleul = personnage;
delete from tutorat where tuto_tuteur = personnage;
delete from perso_glyphes where pglyphe_perso_cod = personnage;

temp := nettoie_automap(personnage);


	
return code_erreur;
end;

$function$

