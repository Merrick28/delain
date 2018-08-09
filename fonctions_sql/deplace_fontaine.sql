CREATE OR REPLACE FUNCTION public.deplace_fontaine(integer, integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Fontaine de jouvence                         */ 
/* les monstres ne sont pas affectés            */
/* 12/01/2010                                   */
/* perso : $1                                   */
/* bonus PV immediat dés : $2                   */
/* bonus PV immediat nbre dés : $3              */
/* bonus Régèn : $4                             */
/* nombre de tour Régèn : $5                    */
/************************************************/

declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;			-- texte pour action
	texte_evt text;				-- texte pour évènements
-------------------------------------------------------------
-- variables concernant le perso
-------------------------------------------------------------
	personnage alias for $1;        -- perso_cod du perso
	type_perso integer;             -- perso_type_perso du perso ou monstre
	pv_perso integer;               -- PV du perso
	pv_max integer;									--PV max du perso
	pos_perso integer;		-- position du perso
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	gain_pv_des alias for $2;
	gain_pv_nbre_des alias for $3;	
	gain_pv integer;		--gain pv accordé
	gain_regen alias for $4;
	gain_regen_nbre_dlt alias for $5;
	texte2 text;
	des integer; 				-- correspond à l'aléatoire

begin
      code_retour := '';		
      select into type_perso,pv_perso,pv_max
				perso_type_perso,perso_pv,perso_pv_max
				from perso
				where perso_cod = personnage;
	if not found then
		return 'soucis sur la sélection de type de perso !';
	end if;
	if type_perso = 2             --On teste si il s'agit d'un monstre
			then return 'Sans conséquence';
	else  
                       --un perso est entré sur la case
		code_retour := 'Vous venez d''arriver sur une fontaine de jouvence. Vous en profitez pour vous refaire une santé, et profiter de la vue magnifique.
		<br>Oh tiens, mais quel est donc ce monstre si bucolique qui court après cet aventurier ?';


		texte_evt := '[perso_cod1] vient de visiter une fontaine de jouvence.';
		insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
     		values(54,now(),1,personnage,texte_evt,'O','O');
		gain_pv := lancer_des(gain_pv_nbre_des,gain_pv_des);
		if  gain_pv > (pv_max-pv_perso) then
			gain_pv := (pv_max-pv_perso);
		end if;
		-- personnage : mise à jour des pvs
		update perso set perso_pv = pv_perso + gain_pv where perso_cod = personnage;
		code_retour := code_retour||'<br>Vous gagnez '||trim(to_char(gain_pv,'99999999'))||' PV, ainsi qu''un bonus de régénération';
-------------------------------------------------------------
-- MAJ bonus Régén
-------------------------------------------------------------
		if gain_regen != 0 then
			perform ajoute_bonus(personnage,'REG',gain_regen,gain_regen_nbre_dlt);
		end if;
-------------------------------------------------------------
-- Code retour des évènements et du texte
-------------------------------------------------------------
		return code_retour;
	end if;
end;$function$

