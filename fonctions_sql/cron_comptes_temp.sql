CREATE OR REPLACE FUNCTION public.cron_comptes_temp()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* fonction test2 : scan la table comptes_temp pour remettre     */
/*                  les persos sur les comptes initiaux          */
/*****************************************************************/
/* Créé le 14/09/2006 / RMT                                      */
/* Liste des modifications :                                     */
/*****************************************************************/

declare
--------------------------------------------------------------------------------
-- variables fourre tout
--------------------------------------------------------------------------------
				code_retour text;
				des integer;
				ligne record;					-- enregistrements
				date timestamp;
				texte_evt text;

--------------------------------------------------------------------------------
-- renseignements de l attaquant
--------------------------------------------------------------------------------
	

--------------------------------------------------------------------------------
-- renseignements de la cible
--------------------------------------------------------------------------------
				compte_cible integer;              -- compte de la cible
				familier integer;              -- détermine le type du perso traité
				v_pos integer;              -- repositionnement de la cible en cas de famlier
				lien_perso_fam integer;              -- retrouver le maitre du familier

begin

date := now();
/* On sélectionne toutes les lignes concernées */
for ligne in select compt_temp_init,compt_temp_transit,compt_temp_date_deb,compt_temp_date_fin,compt_temp_perso_cod
		from comptes_temp 
		where compt_temp_date_fin < date loop

		update perso_compte set pcompt_compt_cod = ligne.compt_temp_init where pcompt_perso_cod = ligne.compt_temp_perso_cod;

--on rend le perso à nouveau visible aux autres joueurs
		update perso set perso_actif = 'O' where perso_cod = ligne.compt_temp_perso_cod;
-- Si c'est un familier on le replace avec son maitre
select into familier perso_type_perso from perso 
            where perso_cod = ligne.compt_temp_perso_cod
            and perso_actif = 'O';
if familier = 3 then
		select into lien_perso_fam
		pfam_perso_cod
			from perso_familier
			where pfam_familier_cod = ligne.compt_temp_perso_cod;
		select into v_pos ppos_pos_cod from perso_position 
			where ppos_perso_cod = lien_perso_fam;
		if found then
			update perso_position
			set ppos_pos_cod = v_pos
			where ppos_perso_cod = ligne.compt_temp_perso_cod;
		end if;
end if;
				texte_evt := '[perso_cod1] a réussi à sortir du sac dans lequel il était enfermé';
				insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
					values(nextval('seq_levt_cod'),79,now(),1,ligne.compt_temp_perso_cod,texte_evt,'N','O',default,ligne.compt_temp_perso_cod);
		
--on supprime la ligne de compte temporaire
		delete from comptes_temp 
			where compt_temp_perso_cod = ligne.compt_temp_perso_cod 
			and compt_temp_date_fin < date;

		end loop;
	return code_retour;
end;$function$

