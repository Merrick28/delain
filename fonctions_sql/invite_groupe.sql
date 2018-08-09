CREATE OR REPLACE FUNCTION public.invite_groupe(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* invite_groupe                               */
/*  $1 = perso_cod invitant                    */
/*  $2 = groupe_cod                            */
/*  $3 = perso_cod invité                      */
/***********************************************/
declare 
	code_retour text;
	v_invitant alias for $1;
	v_groupe_cod alias for $2;
	v_invite alias for $3;
	v_num_mes integer;
	v_texte_mes text;
	v_nom_invitant text;
	v_nom_groupe text;
	is_visible integer;
	pos_perso_lead integer;
	pos_perso_invit integer;
	chef integer;                 --Détermine si le perso invitant est chef du groupe
begin
/*********************************************/
/******     CONTROLES                   ******/
/*********************************************/

	select into v_num_mes
		pgroupe_perso_cod
		from groupe_perso
		where pgroupe_perso_cod = v_invite
		and pgroupe_groupe_cod = v_groupe_cod
		and pgroupe_statut < 2;
	if found then
		return 'Erreur ! Ce perso fait déjà partie de cette coterie, ou bien il a toujours une invitation non répondue pour cette coterie.';
	end if;

	-- Controle de visibilité du perso
	select into pos_perso_lead ppos_pos_cod from perso_position 
		where ppos_perso_cod = v_invitant;
	select into pos_perso_invit ppos_pos_cod from perso_position 
		where ppos_perso_cod = v_invite;

	select into is_visible trajectoire_vue(pos_perso_lead, pos_perso_invit);
	if is_visible != 1 then
		return 'Erreur ! Ce perso n’est pas visible !';
	end if;

	-- Controle de l’invitant
	select into chef pgroupe_chef
		from groupe_perso
		where pgroupe_perso_cod = v_invitant
		and pgroupe_groupe_cod = v_groupe_cod
		and pgroupe_statut = 1;
	if not found or chef != 1 then
		return 'Erreur ! Vous n’êtes pas le chef du groupe, vous ne pouvez pas inviter un autre membre !';
	end if;

/********  Fin des controles  *******/

	-- On supprime les appartenances de ce perso à ce groupe avant de les recréer
	delete from groupe_perso where pgroupe_perso_cod = v_invite
		and pgroupe_groupe_cod = v_groupe_cod;

	-- on commence par inclure le personnage dans le groupe de combat
	insert into groupe_perso
		(pgroupe_groupe_cod,pgroupe_perso_cod,pgroupe_statut)
		values
		(v_groupe_cod, v_invite, 0);
	-- on prépare un message
	select into v_nom_invitant perso_nom
		from perso
		where perso_cod = v_invitant;
	select into v_nom_groupe groupe_nom
		from groupe
		where groupe_cod = v_groupe_cod;
	v_num_mes := nextval('seq_msg_cod');
	--
	insert into messages
		(msg_cod,msg_titre,msg_corps)
		values
		(v_num_mes,'Invitation à un groupe de combat',v_nom_invitant||' vous a invité dans la coterie <b>'||v_nom_groupe||'</b>.<br>
		Vous pouvez <a href="groupe.php?methode=vint">consulter vos invitations en cours</a>, <a href="action.php?methode=accinv&g='|| cast(v_groupe_cod as varchar(10)) ||'">accepter</a> ou <a href="action.php?methode=refinv&g='|| cast(v_groupe_cod as varchar(10)) ||'">refuser</a> cette invitation.');
	insert into messages_exp (emsg_msg_cod,emsg_perso_cod)
		values (v_num_mes,v_invitant);
	insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values (v_num_mes,v_invite,'N','N');
	return 'L’invitation a bien été envoyée. Le destinataire peut maintenant l’accepter ou la refuser.';
end;$function$

