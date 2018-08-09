CREATE OR REPLACE FUNCTION public.ouvrir_prison(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************/
/* ouvrir prison                                 */
/*   $1 = perso à qui on ouvre                   */
/*   $2 = perso_ouvrant                          */
/*************************************************/
declare
	code_retour text;
	personnage alias for $1;
	geolier alias for $2;
	nom_personnage text;
	num_message integer;
	anc_temple integer;
begin
	select into nom_personnage
		perso_nom
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := 'Erreur ! Personnage non trouvé !';
		return code_retour;
	end if;
	insert into perso_grand_escalier
		(pge_perso_cod,pge_lieu_cod)
		values (personnage,2139);
	num_message := nextval('seq_msg_cod');
	insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps) 
		values
		(num_message,now(),now(),'Ouverture de porte','Vous entendez au cliquetis de la serrure que le geolier vous a ouvert la porte de la prison. Vous êtes libre de sortir.');
	insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
		values (nextval('seq_emsg_cod'),num_message,geolier,'N');
	insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) 
		values (num_message, personnage, 'N', 'N');
	select into anc_temple ptemple_anc_pos_cod
		from perso_temple
		where ptemple_perso_cod = personnage;
	if anc_temple = 0 then
		delete from perso_temple where ptemple_perso_cod = personnage;
	else
		update perso_temple
			set ptemple_pos_cod = ptemple_anc_pos_cod,
			ptemple_nombre = ptemple_anc_nombre
			where ptemple_perso_cod = personnage;		
	end if;
	code_retour := '<p>Vous avez ouvert la porte pour ce prisonnier, il est maintenant libre de sortir.';
	return code_retour;
end;
	$function$

