CREATE OR REPLACE FUNCTION public.cree_revolution(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************************/
/* fonction cree_revolution : crée une révolution de guilde        */
/*  on passe en paramètre :                                        */
/*  $1 = perso_cod du lanceur                                      */
/*  $2 = perso_cod de l'anim ciblé                                 */
/* on a en retour une chaine html                                  */
/*******************************************************************/
declare
-------------------------------------------------
-- variables du lanceur
-------------------------------------------------
	lanceur alias for $1;			-- perso_cod du lanceur
	guilde_lanceur integer;			-- guilde_cod du lanceur
	nom_lanceur text;					-- nom du lanceur
	rang_lanceur text;				-- rang du lanceur dans la guilde
-------------------------------------------------
-- variables de la cible
-------------------------------------------------	
	cible alias for $2;				-- perso_cod de la cible
	guilde_cible integer;			-- guilde_cod de la cible
	rang_cible text;					-- rang de la cible dans la guilde
	nom_cible text;					-- nom de la cible
	deja_revolution integer;		-- cible déjà en révolution ?
-------------------------------------------------
-- variables de retour
-------------------------------------------------		
	code_retour text;					-- texte du code retour
-------------------------------------------------
-- variables de calcul
-------------------------------------------------	
	duree interval;					-- durée de la révolution
	code_msg integer;					-- msg_cod du message qui sera envoyé
	texte_msg text;					-- texte des messages
	titre_msg text;					-- titre des messages
	liste_membres record;			-- liste des membres
	code_revolution integer;		-- code de la révolution
	
begin
-------------------------------------------------
-- On cherche les infos du lanceur
-------------------------------------------------	
	select into nom_lanceur
		perso_nom
		from perso
		where perso_cod = lanceur
		and perso_actif = 'O';
	if not found then
		code_retour := '<p>Erreur ! Lanceur non trouvé !';
		return code_retour;
	end if;
	select into guilde_lanceur,rang_lanceur
		guilde_cod,rguilde_admin
		from guilde,guilde_perso,guilde_rang
		where pguilde_perso_cod = lanceur
		and pguilde_guilde_cod = guilde_cod
		and rguilde_guilde_cod = guilde_cod
		and rguilde_rang_cod = pguilde_rang_cod
		and pguilde_valide = 'O';
	if not found then
		code_retour := '<p>Erreur ! Guilde lanceur non trouvé !';
		return code_retour;
	end if;
	if rang_lanceur = 'O' then
		code_retour := '<p>Erreur ! Le lanceur est un administrateur de guilde !';
		return code_retour;
	end if;
-------------------------------------------------
-- On cherche les infos de la cible
-------------------------------------------------	
	select into nom_cible
		perso_nom
		from perso
		where perso_cod = cible
		and perso_actif = 'O';
	if not found then
		code_retour := '<p>Erreur ! Cible non trouvée !';
		return code_retour;
	end if;	
	select into guilde_cible,rang_cible
		guilde_cod,rguilde_admin
		from guilde,guilde_perso,guilde_rang
		where pguilde_perso_cod = cible
		and pguilde_guilde_cod = guilde_cod
		and rguilde_guilde_cod = guilde_cod
		and rguilde_rang_cod = pguilde_rang_cod
		and pguilde_valide = 'O';
	if not found then
		code_retour := '<p>Erreur ! Guilde cible non trouvé !';
		return code_retour;
	end if;
	if rang_cible != 'O' then
		code_retour := '<p>Erreur ! Le cible n''est pas administrateur de guilde !';
		return code_retour;
	end if;
-------------------------------------------------
-- On fait les vérifs
-------------------------------------------------
	if guilde_cible != guilde_lanceur then
		code_retour := '<p>Erreur ! Le lanceur et la cible ne sont pas dans la même guilde !';
		return code_retour;
	end if;
--
	select into deja_revolution
		revguilde_cod
		from guilde_revolution
		where revguilde_cible = cible;
	if found then
		code_retour := '<p>Erreur ! La cible est déjà visée par une révolution !';
		return code_retour;
	end if;
--
	select into deja_revolution
		revguilde_cod
		from guilde_revolution
		where revguilde_lanceur = lanceur;
	if found then
		code_retour := '<p>Erreur ! Le lanceur est déjà dans une révolution !';
		return code_retour;
	end if;
-------------------------------------------------
-- Tout semble OK, on continue
-------------------------------------------------
-- en premier, on enregistre les params liés à la révolution
	duree := trim(to_char(getparm_n(34),'9999'))||' days';
	insert into guilde_revolution
		(revguilde_lanceur,revguilde_cible,revguilde_guilde_cod,revguilde_datfin)
		values
		(lanceur,cible,guilde_lanceur,now() + duree);
	select into code_revolution revguilde_cod
		from guilde_revolution
		where revguilde_lanceur = lanceur
		and revguilde_cible = cible
		and revguilde_guilde_cod = guilde_lanceur;
	if not found then
		code_retour := '<p>Erreur Application : code révolution non trouvé !';
		return code_retour;
	end if;
-- ensuite, on envoie les messages
-- d'abord, pour faire plaisir, celui à la cible
	code_msg := nextval('seq_msg_cod');
	texte_msg := 'Le joueur '||nom_lanceur||' a lancé une révolution contre vous. Les membres de votre guilde ont maintenant '||trim(to_char(getparm_n(34),'9999'));
	texte_msg := texte_msg||' jours pour faire leur choix. D''ici là, vous ne pouvez pas intervenir au sein de votre guilde au sujet de ce membre.';
	titre_msg := 'Révolution !';
	insert into messages
		(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
		values
		(code_msg,now(),titre_msg,texte_msg,now());
	insert into messages_exp
		(emsg_msg_cod,emsg_perso_cod,emsg_archive)
		values
		(code_msg,lanceur,'N');
	insert into messages_dest
		(dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values
		(code_msg,cible,'N','N');
-- ensuite, tout le reste de la guilde
	code_msg := nextval('seq_msg_cod');
	texte_msg := 'Le joueur '||nom_lanceur||' a lancé une révolution contre l''administrateur '||nom_cible||'. Vous avez maintenant '||trim(to_char(getparm_n(34),'9999'));
	texte_msg := texte_msg||' jours pour faire votre choix en allant voter dans votre page de guilde.';
	insert into messages
		(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
		values
		(code_msg,now(),titre_msg,texte_msg,now());
	insert into messages_exp
		(emsg_msg_cod,emsg_perso_cod,emsg_archive)
		values
		(code_msg,lanceur,'N');
	for liste_membres in
		select perso_cod
		from guilde_perso,perso
		where pguilde_guilde_cod = guilde_lanceur
		and pguilde_perso_cod = perso_cod
		and pguilde_valide = 'O'
		and perso_actif = 'O'
		and perso_type_perso = 1
		and perso_cod != cible loop
		insert into messages_dest
			(dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
			values
			(code_msg,liste_membres.perso_cod,'N','N');	
	end loop;
-- ok, tout le monde est au courant maintenant, il nous reste à initialiser les deux premiers votes
	insert into guilde_revolution_vote
		(vrevguilde_perso_cod,vrevguilde_revguilde_cod,vrevguilde_vote)
		values
		(lanceur,code_revolution,'O');
	insert into guilde_revolution_vote
		(vrevguilde_perso_cod,vrevguilde_revguilde_cod,vrevguilde_vote)
		values
		(cible,code_revolution,'N');	
	code_retour := '<p>Vous avez lancé une révolution contre <b>'||nom_cible||'</b>.';
	return code_retour;
end;$function$

