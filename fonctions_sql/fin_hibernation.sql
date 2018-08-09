CREATE OR REPLACE FUNCTION public.fin_hibernation(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************/
/* fonction fin_hibernation                        */
/*************************************************/
declare
	code_retour text;
	v_compte alias for $1;
	v_compte_nom text;
	v_commentaire text;
	v_numero integer;
begin
	update compte set compt_hibernation =null, compt_dfin_hiber = null 
		where compt_cod = v_compte;
	update perso set perso_actif = 'O',perso_der_connex = now() where perso_actif = 'H' 
		and perso_type_perso = 3 
		and perso_cod in 
		(select pfam_familier_cod from perso_compte, perso_familier
		where pcompt_compt_cod = v_compte
		and pcompt_perso_cod = pfam_perso_cod) ;
	update perso set perso_actif = 'O',perso_der_connex = now() where perso_actif = 'H' 
		and perso_type_perso = 1 
		and perso_cod in 
		(select pcompt_perso_cod from perso_compte 
		where pcompt_compt_cod = v_compte) ;
	select into v_compte_nom , v_commentaire compt_nom , compt_confiance from compte where compt_cod = v_compte and compt_confiance = 'S';
	if found then
		select into v_numero nextval('seq_msg_cod');
		insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps,msg_init) values (v_numero,now(),now(),'[Hibernation] ' || v_compte_nom,'Le compte ' || v_compte_nom || ' a fini son hibernation',v_numero);
		insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive) values (nextval('seq_emsg_cod'),v_numero,840026,'N');
		insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values (nextval('seq_dmsg_cod'),v_numero, 840026,'N','N');
	end if;
	code_retour = 'OK';
	return code_retour;
end;$function$

