CREATE OR REPLACE FUNCTION public.trg_new_msg_dest()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/*****************************************/
/* trigger new_msg_dest                  */
/* balaye les messages pour les envois   */
/*****************************************/
declare
	v_mail integer;
	v_perso_cod integer;
	v_envoi_mail integer;
	v_nom_exp text;
	v_nom_dest text;
	v_num_msg integer;
	v_texte_mail text;
	v_compt_cod integer;
	


begin
	v_mail := 0;
	v_perso_cod := NEW.dmsg_perso_cod;
	v_num_msg := NEW.dmsg_msg_cod;
	select into v_compt_cod,v_envoi_mail
		compt_cod,compt_envoi_mail_message
		from perso_compte,compte
		where pcompt_perso_cod = v_perso_cod
		and pcompt_compt_cod = compt_cod;
	if found then
		if v_envoi_mail = 1 then
			select into v_nom_dest
				perso_nom from perso
				where perso_cod = v_perso_cod;
			select into v_nom_exp
				perso_nom from perso,messages_exp
				where emsg_msg_cod = v_num_msg
				and emsg_perso_cod = perso_cod;
			v_texte_mail := 'Le '||to_char(now(),'DD/MM/YYYY')||' à '||to_char(now(),'hh24:mi:ss')||' : ';
			v_texte_mail := v_texte_mail||v_nom_exp||' a envoyé un message à '||v_nom_dest||'.';
			insert into envois_mail
				(menv_perso_cod,menv_compt_cod,menv_texte)
				values
				(v_perso_cod,v_compt_cod,v_texte_mail);
	
	
		end if;	
	end if;
	return NEW;
end;$function$

