CREATE OR REPLACE FUNCTION public.purge_mes(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************/
/* fonction purge_mes                */
/*   purge les messages non archivés */
/*************************************/
declare
	code_retour text;
	temp interval;
	test integer;
	test2 integer;
begin
	temp := trim(to_char(getparm_n(10), '99999999999')) || ' days';

	delete from messages_dest
	where dmsg_archive = 'N'
		and exists
		(
			select 1 from messages
			where dmsg_msg_cod = msg_cod
			and msg_date2 <= now() - temp
		)
		and not exists
		(
			select 1 from perso_compte
			inner join compte on compt_cod = pcompt_compt_cod
			where compt_monstre = 'O'
				and pcompt_perso_cod = dmsg_perso_cod
		);
	get diagnostics test = row_count;

	temp :=  trim(to_char(getparm_n(23), '99999999999')) || ' days';

-- On ne supprime plus du tout les messages archivés.
/*
	delete from messages_dest
	where dmsg_archive = 'O'
		and exists
		(
			select 1 from messages
			where dmsg_msg_cod = msg_cod
			and msg_date2 <= now() - temp
		);
	get diagnostics test2 = row_count;
	test := test + test2;
*/
	code_retour := 'Fonction purge_mes : ' || trim(to_char(test, '99999')) || ' lignes effacées dans dest, ';

	-- On supprime les messages émis, s’ils ne sont pas archivés, et si aucun des destinataires ne l’a gardé.
	delete from messages_exp
	where emsg_archive = 'N'
		and not exists
		(
			select 1 from messages_dest
			where emsg_msg_cod = dmsg_msg_cod
		)
		and not exists
		(
			select 1 from perso_compte
			inner join compte on compt_cod = pcompt_compt_cod
			where compt_monstre = 'O'
				and pcompt_perso_cod = emsg_perso_cod
		);
	get diagnostics test = row_count;
	code_retour := code_retour ||trim(to_char(test, '99999')) || ' lignes effacées dans exp, ';

	-- On supprime les messages qui n’ont plus ni expéditeur ni destinataire
	delete from messages
	where not exists
		(
			select 1 from messages_exp
			where emsg_msg_cod = msg_cod
		)
		and not exists
		(
			select 1 from messages_dest
			where dmsg_msg_cod = msg_cod
		);
	get diagnostics test = row_count;
	code_retour := code_retour ||trim(to_char(test, '99999')) || ' messages effacés, ';
	temp := trim(to_char(getparm_n(44), '99999999999')) || ' days';

	-- On supprime les rumeurs
	delete from rumeurs
	where rum_poids <= 0;
	get diagnostics test = row_count;
	code_retour := code_retour ||trim(to_char(test, '99999')) || ' rumeurs effacées.';

	-- Et on diminue le poids des rumeurs existantes
	update rumeurs set rum_poids = (rum_poids / 1.2)::integer - 1;

	return code_retour;
end;$function$

