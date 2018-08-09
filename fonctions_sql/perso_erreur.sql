CREATE OR REPLACE FUNCTION public.perso_erreur()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* persos anomalies                       */
/******************************************/
declare
	code_retour text;
	ligne record;
	v_niveau integer;
	v_calcul integer;
begin
	code_retour := e'Debut<br>\\
';
	for ligne in
		select perso_cod, perso_nom,perso_temps_tour,perso_niveau,compt_cod,compt_nom,coalesce(perso_redispatch,'NR') as perso_redispatch
			from perso,perso_compte,compte
			where perso_actif != 'N'
			and perso_type_perso = 1
			and perso_pnj != 1
			and perso_cod = pcompt_perso_cod
			and pcompt_compt_cod = compt_cod
			order by compt_cod loop
		v_calcul := compter_perso_amelioration(ligne.perso_cod);
		if v_calcul = -1 then
			code_retour := code_retour||'Compte '||trim(to_char(ligne.compt_cod,'99999999'))||' ('||ligne.compt_nom||') - perso '||ligne.perso_nom||'  ('||trim(to_char(ligne.perso_cod,'9999999'))||') - ANOMALIE : temps incorrect ('||trim(to_char(ligne.perso_temps_tour,'99999999'))||e')<br>\\
';
		else
			select into v_niveau perso_niveau from perso where perso_cod = ligne.perso_cod;
			if v_niveau != v_calcul then
				code_retour := code_retour||'Compte '||trim(to_char(ligne.compt_cod,'99999999'))||' ('||ligne.compt_nom||') perso '||ligne.perso_nom||'  ('||trim(to_char(ligne.perso_cod,'9999999'))||') - ANOMALIE : niveau actuel : '||trim(to_char(v_niveau,'99999999'))||' - niveau calculé :' ||trim(to_char(v_calcul,'99999999'))||' - redispatch : '||ligne.perso_redispatch||e'<br>\\
';
			end if;
		end if;	
		if trim(code_retour) is null then
			return trim(to_char(ligne.perso_cod,'999999999'));
		end if;
	end loop;
	return code_retour;
end;
	$function$

