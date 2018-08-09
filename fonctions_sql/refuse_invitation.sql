CREATE OR REPLACE FUNCTION public.refuse_invitation(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* refus_invitation                            */
/*  $1 = perso_cod                             */
/*  $2 = groupe_cod                            */
/***********************************************/
declare 
	code_retour text;
	v_perso alias for $1;
	v_groupe_cod alias for $2;
	v_chef integer;
	v_num_mes integer;
	v_texte_mes text;
	v_nom_invite text;
	v_nom_groupe text;
begin
	-- on commence par virer l'invitation
	delete from groupe_perso
		where pgroupe_perso_cod = v_perso
		and pgroupe_groupe_cod = v_groupe_cod;
	-- on prépare un message
	select into v_chef groupe_chef
		from groupe
		where groupe_cod = v_groupe_cod;
	select into 	v_nom_invite
		perso_nom from perso
		where perso_cod = v_perso;
		
		
	
	
	v_num_mes := nextval('seq_msg_cod');
	--
	insert into messages
		(msg_cod,msg_titre,msg_corps)
		values
		(v_num_mes,'Invitation refusée à un groupe de combat',v_nom_invite||' a refusé l''invitation à votre coterie.');
	insert into messages_exp (emsg_msg_cod,emsg_perso_cod)
		values (v_num_mes,v_perso);
	insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values (v_num_mes,v_chef,'N','N');
	return 'L''inscription a bien été refusée.';
end;$function$

