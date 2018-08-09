CREATE OR REPLACE FUNCTION public.temp_retire_px(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour text;
	receveur alias for $2;
	donneur alias for $1;
	nb_px alias for $3;
	nom_donneur text;
	message text;
	num_message integer;
begin
	-- retrait
	update perso set perso_px = perso_px - nb_px
		where perso_cod = receveur;
	select into nom_donneur perso_nom from perso where perso_cod = donneur;
	message := 'Vous avez pu apercevoir que vous avez reçu '||trim(to_char(nb_px,'999999999999'))||' PX de manière injustifiée de la part de '||nom_donneur;
	message := message||'. Ces PX vous ont été retirés. Merci de votre compréhension.';
	num_message := nextval('seq_msg_cod');
	insert into messages 
		(msg_cod,msg_titre,msg_corps)
		values
		(num_message,'Don de PX',message);
	insert into messages_exp
		(emsg_msg_cod,emsg_perso_cod)
		values
		(num_message,81890);
	insert into messages_dest
		(dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values
		(num_message,receveur,'N','N');
	insert into messages_dest
		(dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values
		(num_message,donneur,'N','N');
	return 0;
end;
	$function$

