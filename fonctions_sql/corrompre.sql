--
-- Name: corrompre(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION corrompre(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*********************************************/
/* corrompre                                 */
/*  $1 = perso_cod                           */
/*  $2 = qte brouzouf                        */
/*********************************************/
declare
	code_retour text;
	nom_perso text;
	personnage alias for $1;
	quantite alias for $2;
	po integer;
	receveur integer;
	num_mess integer;
	texte_evt text;
begin
	select into po,nom_perso perso_po,perso_nom from perso
		where perso_cod = personnage;
	if po < quantite then
		code_retour := 'Anomalie ! Vous n''avez pas assez de brouzoufs !';
		return code_retour;
	end if;
	update perso set perso_po = perso_po - quantite where perso_cod = personnage;
	select into receveur perso_cod
		from perso,guilde_perso
		where perso_actif = 'O'
		and pguilde_perso_cod = perso_cod
		and pguilde_guilde_cod = 49
		and pguilde_valide = 'O'
		and pguilde_rang_cod = 16;
	update perso set perso_po = perso_po + quantite where perso_cod = receveur;
	-- message
	num_mess := nextval('seq_msg_cod');
	insert into messages
		(msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
		values
		(num_mess,now(),now(),'[tentative de corruption]','Le joueur '||nom_perso||' a tenté de vous corrompre avec la somme de '||trim(to_char(quantite,'999999999'))||' brouzoufs.');
	insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)
		values
		(num_mess,personnage,'N');
	insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values
		(num_mess,receveur,'N','N');
	-- evènements
	texte_evt := '[attaquant] a tenté de corrompre [cible] avec '||trim(to_char(quantite,'9999999'))||' brouzoufs.';
	insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(41,now(),1,personnage,texte_evt,'O','N',personnage,receveur);
	insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(41,now(),1,receveur,texte_evt,'N','N',personnage,receveur);
	code_retour := 'Vous avez tenté de corrompre le geolier avec '||trim(to_char(quantite,'9999999'))||' brouzoufs.<br> Celui ci a reçu votre demande et va l''analyser.';
	return code_retour;
end;$_$;


ALTER FUNCTION public.corrompre(integer, integer) OWNER TO delain;
