--
-- Name: depose_or(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.depose_or(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function depose_or : depose de l or au sol                    */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/*    $2 = qte                                                   */
/* Le code sortie est une chaine séparée par ;                   */
/*    Caractère 1 =>                                             */
/*       0 = tout est OK, on peut déposer                        */
/*      -1 = anomalie + description                              */
/*****************************************************************/
/* Créé le 27/03/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
code_retour text;					-- code retour
	personnage alias for $1;		-- perso_cod
	qte_or alias for $2;			-- obj_cod
	compt integer;						-- compteur mutli utilisations
	pa perso.perso_pa%type;			-- pa du perso
	pos_perso perso_position.ppos_pos_cod%type; -- position actuelle du perso
    v_pos_etage integer;
	qte_or_actuelle integer;
	nom_perso perso.perso_nom%type;	-- nom du perso pour evt
	texte_evt text;
	total_paquet integer;
	n_total_paquet integer;
begin
	code_retour := '0'; -- par défaut, tout est OK
/********************************************/
/* Etape 1 : on vérifie que le perso existe */
/********************************************/
select into compt count(*) from perso
where perso_cod = personnage;
if compt != 1 then
		code_retour := '-1;Personnage non trouvé !';
return code_retour;
else -- sinon, on récupère maintenant les infos dont on aura besoin
select into pa,pos_perso,nom_perso,v_pos_etage perso_pa,ppos_pos_cod,perso_nom,pos_etage
from perso,perso_position,positions
where perso_cod = personnage
  and ppos_perso_cod = perso_cod
  and  pos_cod = ppos_pos_cod;
end if;
/**********************************************************/
/* Etape 2 : on vérifie que l or existe en qte suffisante */
/**********************************************************/
select into compt perso_po from perso
where perso_cod = personnage;
if compt < qte_or then
		code_retour := '-1;Pas assez riche.... ';
return code_retour;
-- penser à mettre un else si on a des infos à récupérer !
end if;
        if qte_or < 0 then
                code_retour := '-1;Bien essayé, mais on ne peut pas poser de qté négative.';
return code_retour;
end if;
        if qte_or = 0 then
                code_retour := '-1;Tu as déjà essayé de poser rien par terre ?.';
return code_retour;
end if;
/*****************************************************/
/* Etape 4 : on vérifie que le perso ait assez de pa */
/*****************************************************/
	if pa < 1 then
		code_retour:= '-1;Pas assez de PA pour cette action';
return code_retour;
end if;
/**************************************/
/* Etape 5 : tout est OK, on continue */
/**************************************/
-- 5.1 : on enlève les pa du perso
update perso
set perso_pa = pa - 1 where perso_cod = personnage;
insert into or_position (por_cod,por_pos_cod,por_qte)
values (nextval('seq_por_cod'),pos_perso,qte_or);
-- 5.5 : on crée la ligne d evenement
texte_evt :='[perso_cod1] a posé au sol '||trim(to_char(qte_or,'99999999'))||' brouzoufs';
insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
values(nextval('seq_levt_cod'),13,now(),1,personnage,texte_evt,'O','O');
-- 5.7 : et on n oublie pas d enlever l or du perso
update perso
set perso_po = perso_po - qte_or
where perso_cod = personnage;
-- 5.6 : et pour finir on retourne le bon code

-- verification d'un declenchement d'EA sur case comme pour un deplacement (necessite l'étage et la position du perso au ramassage, injecter oussi l'objet/type objet ramassé)
code_retour := execute_fonctions(personnage, null, 'DEP', json_build_object('type_dep','depose_or', 'qte_or', qte_or, 'ancien_pos_cod',pos_perso,'ancien_etage',v_pos_etage,'nouveau_pos_cod',pos_perso,'nouveau_etage',v_pos_etage)) ;
if code_retour != '' then
    code_retour := '0;' || code_retour;     -- retourner 0, mais avec le message de l'EA
end if;

return code_retour;
end;
$_$;


ALTER FUNCTION public.depose_or(integer, integer) OWNER TO delain;