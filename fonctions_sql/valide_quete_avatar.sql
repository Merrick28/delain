CREATE OR REPLACE FUNCTION public.valide_quete_avatar(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction valide_quete_avatar                              */
/*   Exécute les fonctions spécifiques liées à la mort       */
/*    d’un avatar                                            */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod de l’avatar tué           */
/*   $2 = cible_cod : le code du perso qui a tué l’avatar    */
/* on a en sortie un message à afficher                      */
/*************************************************************/
/* Créé le 20/05/2014                                        */
/*************************************************************/
declare
	v_perso_cod alias for $1;  -- Le code de l’avatar tué
	v_tueur_cod alias for $2;  -- Le code du perso qui a tué l’avatar

	code_retour text;          -- Le retour de la fonction
	v_queteur_cod integer;     -- Le code de celui qui aurait dû tuer l’avatar
	v_quete_cod integer;       -- Le code de la quête correspondante
	v_mes integer;             -- L’id du message envoyé
	v_corps text;              -- Le corps du message envoyé

begin
	code_retour := '';
	select into v_quete_cod pquete_cod from quete_perso where pquete_perso_cod = v_tueur_cod and pquete_quete_cod = 16 and pquete_param = v_perso_cod;
	if found then
		update quete_perso set pquete_nombre = 2 where pquete_cod = v_quete_cod;
		code_retour := code_retour || '<br><br><b>Une sensation de plénitude vous envahit, vous avez vaincu vos propres peurs et angoisses. Retournez donc dans un centre de maîtrise magique ou magasin runique afin de faire valider cette nouvelle étape.</b><br><br>';
	end if;
	select into v_quete_cod, v_queteur_cod pquete_cod, pquete_perso_cod from quete_perso where pquete_perso_cod != v_tueur_cod and pquete_quete_cod = 16 and pquete_param = v_perso_cod;
	if found then
		code_retour := code_retour || '<br><br><b>Malheur à vous, vous avez tué un avatar qui était destiné à un autre...</b><br><br>';
		v_corps := 'Un autre que vous a achevé l’avatar qui vous était destiné. Vous devez donc recommencer cette quête depuis le départ';
		v_mes := nextval('seq_msg_cod');
		insert into messages (msg_cod, msg_date2, msg_date, msg_titre, msg_corps) values
			(v_mes, now(), now(), 'Un avatar est allé rejoindre les esprits', v_corps);
		insert into messages_exp (emsg_msg_cod, emsg_perso_cod, emsg_archive)
			values (v_mes, v_perso_cod, 'N');
		insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu, dmsg_archive)
			values (v_mes, v_queteur_cod, 'N', 'N');
		delete from quete_perso where pquete_cod = v_quete_cod;
	end if;

	return code_retour;
end;$function$

