CREATE OR REPLACE FUNCTION public.nv_magie_resurrection(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function transfert_pouvoir : résurrection                     */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
	texte_evt text;				-- texte pour évènements
	nom_sort text;					-- nom du sort
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
        v_int_perso integer;
        v_x integer;
        v_y integer;
        v_etage integer;
        v_position integer;
        v_pos_position integer;
        pos_actuelle integer;

-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	nom_cible text;				-- nom de la cible
	v_pv_cible text;                     -- pvie 
        v_pv_max_cible text;                 -- pvie max
        v_pos_resuc integer;         


 

-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne text;				-- PX gagnes
	
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
										-- chaine temporaire pour amélioration
	v_bloque_magie integer;		-- vérif si résistance magique
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 161;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	

	select into nom_sort sort_nom from sorts
		where sort_cod = num_sort;
	magie_commun_txt := magie_commun(lanceur,cible,type_lancer,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	px_gagne := split_part(magie_commun_txt,';',4);
	
---- minimum syndical on alimente les infos de positions actuelle

select into 
						pos_actuelle,
                                                v_pos_position,
						v_etage,
						v_x,
						v_y,
						v_int_perso,
						v_pv_cible,
						v_pv_max_cible
						
						
					ppos_pos_cod,
                                        ppos_cod,
					pos_etage,
					pos_x,
					pos_y,
					perso_int,
					perso_pv,
					perso_pv_max
					
		from perso,perso_position,positions
		where perso_cod = cible
		and ppos_perso_cod = cible
		and ppos_pos_cod = pos_cod;

        
--- il suffit maintenant de stocker dans la table de résurection la position actuelle.
--- en prenant garde à ne laisser qu'une seule itération possible pour un code perso.

       select into v_pos_resuc rpos_pos_cod from perso_resuc
            where rpos_perso_cod = cible;
       if found  then
       code_retour := code_retour||'<br>vous possédiez déja un lieu de résurrection, il est désormais détruit. <br>';
        delete from perso_resuc where rpos_perso_cod = cible;
       end if;
        -- mise à jour de la table perso_resuc
       insert into perso_resuc (rpos_cod, rpos_pos_cod, rpos_perso_cod) 
               values (v_pos_position ,pos_actuelle, cible);


       code_retour := code_retour||'<br>vous lancez le puissant sortilège de résurrection, dorénavant votre prochaine mort vous ramènera ici. Notez bien l''endroit, car nulle information de ce lieu secret connu de vous seul ne pourra vous être donnée dans le futur. Pire une inscription dans un dispensaire ne saurait empêcher les dieux de vous ramenez ici.<br>';

	code_retour := code_retour||'<br>Vous gagnez '||px_gagne||' PX pour cette action.<br>';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] ';
      insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   if (lanceur != cible) then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
   end if;

	return code_retour;



	end;
$function$

