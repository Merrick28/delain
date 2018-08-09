CREATE OR REPLACE FUNCTION public.verse_solde()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* verse_solde                                           */
/*********************************************************/
declare
	code_retour text;
	ligne record;
	num_mes integer;
begin
	for ligne in select perso_cod,rguilde_solde
		from guilde_perso,guilde_rang,perso
		where pguilde_valide = 'O'
		and pguilde_guilde_cod = 49
		and rguilde_guilde_cod = 49
		and pguilde_rang_cod = rguilde_rang_cod 
		and perso_actif != 'N'
		and pguilde_perso_cod = perso_cod loop
		update guilde_perso set pguilde_solde = pguilde_solde + ligne.rguilde_solde
			where pguilde_perso_cod = ligne.perso_cod;
		update parametres set parm_valeur = parm_valeur - ligne.rguilde_solde where parm_cod = 39;
		num_mes := nextval('seq_msg_cod');
		insert into messages (msg_cod,msg_date,msg_date2,msg_titre,msg_corps) values
			(num_mes,now(),now(),'Jour de paie !','Votre solde vient de vous être versée. Vous pouvez la retire à la préfecture.');
		insert into messages_exp (emsg_msg_cod,emsg_perso_cod) values (num_mes,4);
		insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values (num_mes,ligne.perso_cod,'N','N');
	end loop;
	return 'Paye versée !';
end;$function$

