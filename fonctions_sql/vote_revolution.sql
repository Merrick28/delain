CREATE OR REPLACE FUNCTION public.vote_revolution(integer, integer, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* fonction vote_revolution : enregistre un vote dans une */
/*   revolution de guilde                                 */
/* on passe en paramètres :                               */
/*   $1 = le perso_cod du votant                          */
/*   $2 = le revguilde_cod de la révolution               */
/*   $3 = le vote                                         */
/* on a en sortie une chaine html utilisable dans la page */
/*   action.php                                           */
/**********************************************************/
/* créé le vendredi 13/02/2004                            */
/* 18/01/2007 Blade : rajout d'un titre à l'expulsion     */
/**********************************************************/
declare
------------------------------------------------------------
-- variables du votant
------------------------------------------------------------
	votant alias for $1;				-- perso_cod du votant
	vote alias for $3;				-- vote à prendre en compte
	v_guilde integer;					-- guilde du votant
------------------------------------------------------------
-- variables de la guilde
------------------------------------------------------------
	code_revolution alias for $2;	-- code révolution
	v_guilde_revolution integer;	-- guilde de la révolution
	nom_guilde text; 			-- nom de la guilde en révolution
	nom_cible text;					-- nom de la cible
	code_cible integer;				-- perso_cod de la cible
	nom_lanceur text;					-- nom du lanceur
	code_lanceur integer;			-- perso_cod du lanceur
------------------------------------------------------------
-- variables de contrôle et de retour
------------------------------------------------------------
	code_retour text;					-- texte de retour
	temp integer;						-- fourre tout
	nb_oui integer;					-- nombre de oui
	taux_oui numeric;					-- taux de oui
	nb_non integer;					-- nombre de non
	taux_non numeric;					-- taux de non
	nb_membre integer;				-- nombre de membres
	limite_oui integer;				-- nombre de oui à atteindre
	limite_non integer;				-- nombre de non à atteindre
	code_message integer;			-- numéro des messages envoyés
	titre_message text;				-- Titre du message
	corps_message text;				-- corps du message
	liste_membres record;			-- liste des membres de la guilde
	score_oui integer;
	score_non integer;
	v_renommee numeric;				-- variation de renommee
begin
------------------------------------------------------------
-- on contrôle tout ce qu'on nous a donné
------------------------------------------------------------	
	select into temp perso_cod from perso	
		where perso_cod = votant;
	if not found then
		code_retour := '<p>Erreur ! Votant non trouvé !';
		return code_retour;
	end if;
	select into v_guilde,nom_guilde pguilde_guilde_cod,guilde_nom
		from guilde_perso,guilde
		where pguilde_perso_cod = votant 
		and pguilde_valide = 'O'
		and guilde_cod = pguilde_guilde_cod;
	if not found then
		code_retour := '<p>Erreur ! Le votant n''appartient pas à une guilde !';
		return code_retour;
	end if;
	select into v_guilde_revolution revguilde_guilde_cod
		from guilde_revolution
		where revguilde_cod = code_revolution;
	if not found then
		code_retour := '<p>Erreur ! Pas de révolution trouvée !';
		return code_retour;
	end if;
	if v_guilde != v_guilde_revolution then
		code_retour := '<p>Erreur ! Le votant ne fait pas partie de la guilde en révolution !';
		return code_retour;
	end if;
	select into nom_lanceur,code_lanceur perso_nom,perso_cod from perso,guilde_revolution
		where revguilde_cod = code_revolution
		and perso_cod = revguilde_lanceur;
	select into nom_cible,code_cible perso_nom,perso_cod from perso,guilde_revolution
		where revguilde_cod = code_revolution
		and perso_cod = revguilde_cible;
------------------------------------------------------------
-- Tout semble bon, on enregistre le vote
------------------------------------------------------------	
	insert into guilde_revolution_vote
		(vrevguilde_perso_cod,vrevguilde_revguilde_cod,vrevguilde_vote)
		values
		(votant,code_revolution,vote);
------------------------------------------------------------
-- on prend les résultats
------------------------------------------------------------
	select into nb_oui count(vrevguilde_cod)
		from guilde_revolution_vote
		where vrevguilde_revguilde_cod = code_revolution
		and vrevguilde_vote = 'O';
	select into nb_non count(vrevguilde_cod)
		from guilde_revolution_vote
		where vrevguilde_revguilde_cod = code_revolution
		and vrevguilde_vote = 'N';		
	select into nb_membre count(perso_cod) 
       FROM guilde_perso, perso
       WHERE pguilde_guilde_cod = v_guilde
       AND pguilde_perso_cod = perso_cod
       AND pguilde_valide = 'O'
       AND perso_actif = 'O'
       AND perso_type_perso = 1;
    limite_oui := round(nb_membre * getparm_n(35) / 100);
    limite_non := round(nb_membre * (100 - getparm_n(35)) / 100);
       score_oui := getparm_n(35);
       score_non := 100 - getparm_n(35);
   if nb_oui > limite_oui then
-----------------------------------------------------------
-- Le OUI a gagné !
------------------------------------------------------------  	
--	1° on envoie un message de soutien au pauvre admin.....
		code_message := nextval('seq_msg_cod');
		titre_message := 'Révolution : résultat';
		corps_message := 'La révolution menée par '||nom_lanceur||' à votre encontre à malheureusement abouti.<br>';
		corps_message := corps_message||'Vous avez perdu cette partie de bras de fer, et vous êtes banni de votre propre guilde.<br>';
		corps_message := corps_message||'Mais ne désespérez pas, vous retrouverez certainement '||nom_lanceur||' sur votre route, et vous aurez l''occasion de vous venger.';
		insert into messages
			(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
			values
			(code_message,now(),titre_message,corps_message,now());
		insert into messages_exp
			(emsg_msg_cod,emsg_perso_cod)
			values
			(code_message,'209808');
		insert into messages_dest
			(dmsg_msg_cod,dmsg_perso_cod)
			values
			(code_message,code_cible);
-- On rajoute un titre
		nom_guilde := '[Ancien Administrateur de la guilde '||nom_guilde||', exclu suite à une révolution]';
		insert into perso_titre values(default,code_cible,nom_guilde,now(),'2');
-- 2° on le vire méchamment de la guilde
		delete from guilde_perso where pguilde_perso_cod = code_cible;
-- 3° on prévient le lanceur de la révolution
		code_message := nextval('seq_msg_cod');
		titre_message := 'Révolution : résultat';
		corps_message := 'La révolution menée contre '||nom_cible||' a abouti.<br>';
		corps_message := corps_message||'Vous avez gagné cette partie de bras de fer, et '||nom_cible||' a été banni de sa propre guilde.<br>';
		corps_message := corps_message||'De plus, vous avez été promu administrateur de la guilde.';
		insert into messages
			(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
			values
			(code_message,now(),titre_message,corps_message,now());
		insert into messages_exp
			(emsg_msg_cod,emsg_perso_cod)
			values
			(code_message,'209808');
		insert into messages_dest
			(dmsg_msg_cod,dmsg_perso_cod)
			values
			(code_message,code_lanceur);
-- 4° on le met en admin
		update guilde_perso
			set pguilde_rang_cod = 0
			where pguilde_perso_cod = code_lanceur;
-- 5° on prévient quand même par acquis de conscience tous les membres de la guilde
		code_message := nextval('seq_msg_cod');
		titre_message := 'Révolution : résultat';
		corps_message := 'La révolution menée par '||nom_lanceur||' contre '||nom_cible||' a abouti.<br>';
		corps_message := corps_message||nom_lanceur||' a gagné et '||nom_cible||' a été banni de sa propre guilde.';
		insert into messages
			(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
			values
			(code_message,now(),titre_message,corps_message,now());
		insert into messages_exp
			(emsg_msg_cod,emsg_perso_cod)
			values
			(code_message,code_cible);
		for liste_membres in
			select perso_cod
			from guilde_perso,perso
			where pguilde_guilde_cod = v_guilde
			and pguilde_perso_cod = perso_cod
			and pguilde_valide = 'O'
			and perso_actif = 'O'
			and perso_type_perso = 1	
			and perso_cod != code_cible loop
			insert into messages_dest
				(dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
				values
				(code_message,liste_membres.perso_cod,'N','N');	
		end loop;
-- 6° on génère quand même un code retour
		code_retour := '<p>Votre compte a bien été pris en compte, et il a fait pencher la bascule du côté des frondeurs !<br>';
		code_retour := code_retour||nom_cible||' a été renvoyé de la guilde !';
-- 7° on efface la révolution en cours
		delete from guilde_revolution_vote
			where vrevguilde_revguilde_cod = code_revolution;
		delete from guilde_revolution
			where revguilde_cod = code_revolution;
-- 8 ° on met à jour la renommee
		v_renommee := nb_oui/10;
		if v_renommee > 2 then
			v_renommee := 2;
		end if;
		update perso set perso_renommee = perso_renommee - v_renommee where perso_cod = code_cible;
		update perso set perso_renommee = perso_renommee + v_renommee where perso_cod = code_lanceur;
		return code_retour;
	end if;
	if nb_non >= limite_non then
-----------------------------------------------------------
-- Le NON a gagné !
------------------------------------------------------------  	
--	1° on envoie un message de soutien au pauvre frondeur.....
		code_message := nextval('seq_msg_cod');
		titre_message := 'Révolution : résultat';
		corps_message := 'La révolution que vous avez mené a lamentablement échoué.<br>';
		corps_message := corps_message||'Vous avez perdu cette partie de bras de fer, et vous êtes banni de votre propre guilde.<br>';
		corps_message := corps_message||'Mais ne désespérez pas, vous retrouverez certainement '||nom_cible||' sur votre route, et vous aurez l''occasion de vous venger.';
		insert into messages
			(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
			values
			(code_message,now(),titre_message,corps_message,now());
		insert into messages_exp
			(emsg_msg_cod,emsg_perso_cod)
			values
			(code_message,'209808');
		insert into messages_dest
			(dmsg_msg_cod,dmsg_perso_cod)
			values
			(code_message,code_lanceur);
-- On rajoute un titre
		nom_guilde := '[Ancien membre de la guilde '||nom_guilde||', exclu suite à une révolution]';
		insert into perso_titre values(default,code_lanceur,nom_guilde,now(),'2');
-- 2° on le vire méchamment de la guilde
		delete from guilde_perso where pguilde_perso_cod = code_lanceur;
-- 3° on prévient la cible de la révolution
		code_message := nextval('seq_msg_cod');
		titre_message := 'Révolution : résultat';
		corps_message := 'La révolution menée par '||nom_lanceur||' a échoué.<br>';
		corps_message := corps_message||'De ce fait, '||nom_lanceur||' a été banni de sa guilde.<br>';
		insert into messages
			(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
			values
			(code_message,now(),titre_message,corps_message,now());
		insert into messages_exp
			(emsg_msg_cod,emsg_perso_cod)
			values
			(code_message,code_lanceur);
		insert into messages_dest
			(dmsg_msg_cod,dmsg_perso_cod)
			values
			(code_message,code_cible);
-- 5° on prévient quand même par acquis de conscience tous les membres de la guilde
		code_message := nextval('seq_msg_cod');
		titre_message := 'Révolution : résultat';
		corps_message := 'La révolution menée par '||nom_lanceur||' contre '||nom_cible||' a échoué.<br>';
		corps_message := corps_message||nom_lanceur||' a perdu et a été banni de sa propre guilde.';
		insert into messages
			(msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
			values
			(code_message,now(),titre_message,corps_message,now());
		insert into messages_exp
			(emsg_msg_cod,emsg_perso_cod)
			values
			(code_message,'209808');
		for liste_membres in
			select perso_cod
			from guilde_perso,perso
			where pguilde_guilde_cod = v_guilde
			and pguilde_perso_cod = perso_cod
			and pguilde_valide = 'O'
			and perso_actif = 'O'
			and perso_type_perso = 1	
			and perso_cod != code_lanceur loop
			insert into messages_dest
				(dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
				values
				(code_message,liste_membres.perso_cod,'N','N');	
		end loop;
-- 6° on génère quand même un code retour
		code_retour := '<p>Votre compte a bien été pris en compte, et il a fait pencher la bascule du côté de '||nom_cible||' !<br>';
		code_retour := code_retour||nom_lanceur||' a été renvoyé de la guilde !';
-- 7° on efface la révolution en cours
		delete from guilde_revolution_vote
			where vrevguilde_revguilde_cod = code_revolution;
		delete from guilde_revolution
			where revguilde_cod = code_revolution;
-- 8 ° on met à jour la renommee
		v_renommee := nb_oui/10;
		if v_renommee > 2 then
			v_renommee := 2;
		end if;
		update perso set perso_renommee = perso_renommee + v_renommee where perso_cod = code_cible;
		update perso set perso_renommee = perso_renommee - v_renommee where perso_cod = code_lanceur;		
		return code_retour;

	end if;	
-----------------------------------------------------------
-- Pas de bascule
------------------------------------------------------------  	
	code_retour := '<p>Votre vote a bien été pris en compte, mais n''a pas été suffisant pour faire pencher la balance d''un côté ou de l''autre.';
	return code_retour;
end;
$function$

