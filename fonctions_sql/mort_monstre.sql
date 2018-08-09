CREATE OR REPLACE FUNCTION public.mort_monstre(integer, integer, integer, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************/
/* fonction mort_monstre                         */
/*  $1 = perso_cod du monstre                    */
/*  $2 = perso_cod du tueur                      */
/*  $3 = position du monstre                     */
/*  $4 = nom de la fonction à lancer             */
/*************************************************/
declare
	code_retour text;
	v_monstre alias for $1;
	v_tueur alias for $2;
	v_position alias for $3;
	v_fonction alias for $4;
	v_fonction_modif text;
	temp_texte text;
	temp_monstre integer;
begin
	select into temp_monstre act_perso_cod from action_monstre
		where act_perso_cod = v_monstre;
	if not found then
		insert into action_monstre (act_perso_cod) values(v_monstre);
		select into temp_texte perso_nom from perso where perso_cod = v_monstre for update;	
		-- on remet à jour la dlt pour être efficace au niveau des sorts
		--update perso set perso_dlt = now() where perso_cod = v_monstre;
		--temp_texte := calcul_dlt2(v_monstre);
		update perso set perso_pa = 12 where perso_cod = v_monstre;
		-- on prépare la fonction à lancer
		v_fonction_modif := 'select '||replace(v_fonction,'[monstre]',trim(to_char(v_monstre,'99999999999999')));
		v_fonction_modif := replace(v_fonction_modif,'[tueur]',trim(to_char(v_tueur,'9999999999999')));
		v_fonction_modif := replace(v_fonction_modif,'[position]',trim(to_char(v_position,'9999999999')));
		-- normalement, on a ici la chaine prête à être éxécutée
		--insert into trace (trc_texte) values (v_fonction_modif);
		execute v_fonction_modif;
		update perso set perso_actif = 'N' where perso_cod = v_monstre;
		update perso_position set ppos_pos_cod = pos_aleatoire(4) where ppos_perso_cod = v_monstre;

	end if;
	code_retour := '';
	return code_retour;
end;$function$

