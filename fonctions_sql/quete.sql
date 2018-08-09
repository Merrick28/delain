CREATE OR REPLACE FUNCTION public.quete(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function quete : permet de lancer des requêtes relatives aux  */
/*          quêtes automatisees                                  */
/* On passe en paramètres                                        */
/*    $1 = quete_cod numéro de quête concernée                    */
/*    $2 = perso_cod numéro du perso concerné                    */
/* Le code sortie est texte utilisable                           */
/*****************************************************************/
/* Créé le 07/06/2006                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
/*******************************/
		 code_retour text;
		 code_quete alias for $1;
		 quete_nombre integer;
		 personnage alias for $2;
		 des integer;
		 image text;
		 dieu integer;
/*******************************/

begin

code_retour := '';

--------------------------------------------
-- Quête des Dieux, Falis, Elian, Balgur
--------------------------------------------
/*Etape 1 Falis et Balgur, même code quête, dieux différents*/
if (code_quete = 7) then
select into dieu dper_dieu_cod 
	from dieu_perso 
	where dper_perso_cod = personnage;
		 		 /*Traitement du cas de Falis*/
		 		 if dieu = 7 then
		 		 
			 		 		select into quete_nombre pquete_nombre 
							 		 		from quete_perso 
							 		 		where pquete_quete_cod = code_quete 
							 		 		and pquete_perso_cod = personnage;
							/*On regarde à quelle étape de la quête on se trouve */
	
						 if not found then
								des := lancer_des(1,2);
		 		 		 		 if des = 1 then
		 		 		 		 		 image := 'quete.png';
		 		 		 		 else
		 		 		 		 		 image := 'quete1.png';
		 		 		 		 end if;
		 		 		 		 
		 		 		 		 /*validation de l'étape, possibilité de passer à la suivante*/
		 		 		 		 insert into quete_perso (pquete_nombre,pquete_perso_cod,pquete_quete_cod,pquete_termine) values (1 ,personnage,7,'N');
		 		 		 		 update perso set perso_px = perso_px + 10, perso_prestige = perso_prestige + 2 where perso_cod = personnage;
		 		 		 		 insert into perso_titre values (default, personnage, '[Illuminé par Falis]', default, '1');
		 		 
		 		 		 		 code_retour := code_retour||'<br> En vous approchant de la paroi, vous avez vu cet interstice. La curiosité aidant, vous vous êtes pris au jeu, et vous avez voulu en savoir plus.
		 		 		 		 		 		 		 		 <br>Mais la curiosité vous a fait un drôle de cadeau. Non pas celui dont on vous avait parlé, pas celui dun trésor enfoui. Dailleurs qui croit encore aux trésors ?
		 		 		 		 		 		 		 		 <br>Non, le résultat en est encore plus étrange.
		 		 		 		 		 		 		 		 <br>Vous êtes arrivé dans une sorte de petite grotte, assez petite pour quun groupe ne puisse pas sy introduire en même temps. Vous avez à 		 		 		 		 		 		 		 		 peine la place de tourner sur vous même.
		 		 		 		 		 		 		 		 <br>Il vous faut alors un peu de temps pour comprendre que les sigles qui se trouvent autour de vous sont en fait des lettres.
		 		 		 		 		 		 		 		 <br>En utilisant votre source de lumière, et en tournant sur vous même, vous pouvez alors apercevoir une sorte de poème, n''ayant que peu de sens à 		 		 		 		 		 		 vos yeux.
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br><img src="http://www.jdr-delain.net/avatars/'||image||'">
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>A sa lecture, une douleur se fait ressentir dans votre tête, et une voix semble vous parler intérieurement :
		 		 		 		 		 		 		 		 <br><i>Tu es initié maintenant. A toi de trouver le chemin
		 		 		 		 		 		 		 		 <br>Peu pourront t''aider pour cela, choisi bien tes amis</i>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>Un courant d''air éteint alors votre flamme, et l''obscurité vous oblige à battre en retraite ...';
		 		 
		 		 		 		 return code_retour;
		 		 		elsif (quete_nombre = 1) then
						 		 code_retour := code_retour||'<br>Un sentiment de déjà vu remonte en vous. Mais vous ne savez plus si il s''agit de cette même petite salle déjà visitée ... Une salle portant des marques sur le mur, une salle avec une sorte de poème. Rien n''est pareil. Pourtant, il vous semble vous rappeler les évènements, la voix, l''obscurité ... 
						 		 <br>Vos souvenirs vous joueraient-ils des tours ? Vous seriez vous trompé ?';
						 		 return code_retour;
						else return code_retour;
		 			 	end if;		 
		 		 
		 		 /*Traitement du cas de Balgur, dans les mêmes cachettes*/
		 		 elsif dieu = 2 then
		 					
		 					 select into quete_nombre pquete_nombre 
					 					 from quete_perso 
					 					 where pquete_quete_cod = code_quete 
					 					 and pquete_perso_cod = personnage;
							/*On regarde à quelle étape de la quête on se trouve */
						 if quete_nombre = 1 then
	 		 		 		 
		 		 		 		 /*validation de l'étape, possibilité de passer à la suivante*/
		 		 		 		 update quete_perso set pquete_nombre = 2 where pquete_perso_cod = personnage and pquete_quete_cod = 7;
		 		 		 		 update perso set perso_px = perso_px + 10, perso_prestige = perso_prestige + 2 where perso_cod = personnage;
		 		 		 		 insert into perso_titre values (default, personnage, '[Illuminé par Balgur]', default, '1');
		 		 		 		 		 		 		 		 		 		 
		 		 		 		 code_retour := code_retour||'<br> En vous approchant de la paroi, vous avez vu cet interstice. La curiosité aidant, vous vous êtes pris au jeu, et vous avez voulu en savoir plus.
		 		 		 		 		 		 		 		 <br>Mais la curiosité vous a fait un drôle de cadeau. Non pas celui dont on vous avait parlé, pas celui dun trésor enfoui. Dailleurs qui croit encore aux trésors ?
		 		 		 		 		 		 		 		 <br>Non, le résultat en est encore plus étrange.
		 		 		 		 		 		 		 		 <br>Vous êtes arrivé dans une sorte de petite grotte, assez petite pour quun groupe ne puisse pas sy introduire en même temps. Vous avez à 		 		 		 		 		 		 		 		 peine la place de tourner sur vous même.
		 		 		 		 		 		 		 		 <br>Il vous faut alors un peu de temps pour comprendre que les sigles qui se trouvent autour de vous sont en fait des lettres.
		 		 		 		 		 		 		 		 <br>En utilisant votre source de lumière, et en tournant sur vous même, vous pouvez alors apercevoir une sorte de poème, n''ayant que peu de sens à 		 		 		 		 		 		 vos yeux.
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br><img src="http://www.jdr-delain.net/avatars/quete_2165balg1ur.png">
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>A sa lecture, une douleur se fait ressentir dans votre tête, et une voix semble vous parler intérieurement :
		 		 		 		 		 		 		 		 <br><i>Tu es initié maintenant. A toi de trouver le chemin
		 		 		 		 		 		 		 		 <br>Peu pourront t''aider pour cela, choisi bien tes amis</i>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>Un courant d''air éteint alors votre flamme, et l''obscurité vous oblige à battre en retraite ...';
		 		 
		 		 		 		 return code_retour;
		 		 		 elsif (quete_nombre > 1) then
						 		 code_retour := code_retour||'<br>Un sentiment de déjà vu remonte en vous. Mais vous ne savez plus si il s''agit de cette même petite salle déjà visitée ... Une salle portant des marques sur le mur, une salle avec une sorte de poème. Rien n''est pareil. Pourtant, il vous semble vous rappeler les évènements, la voix, l''obscurité ... 
						 		 <br>Vos souvenirs vous joueraient-ils des tours ? Vous seriez vous trompé ?';
						 		 return code_retour;
		 			 	else return code_retour;
		 			 	end if;
		 			else return code_retour;
				 end if;

-- Fin du cas de la quête 7, concernant Balgur et Falis

-- On gère le cas des adeptes d'Elian qui ont validé la première étape dans un temple, donc code quête 8, étape 1
/*Etape 1 Elian*/
elsif (code_quete = 8) then
		 select into quete_nombre pquete_nombre 
						 from quete_perso 
						 where pquete_quete_cod = code_quete 
						 and pquete_perso_cod = personnage;
/*On regarde à quelle étape de la quête on se trouve */
		 if quete_nombre = 1 then
		 
		 		 		 		 /*validation de l'étape, possibilité de passer à la suivante*/
		 		 		 		 update quete_perso set pquete_nombre = 2 where pquete_perso_cod = personnage and pquete_quete_cod = 8;
		 		 		 		 update perso set perso_px = perso_px + 10, perso_prestige = perso_prestige + 2 where perso_cod = personnage;
		 		 		 		 insert into perso_titre values (default, personnage, '[Illuminé par Elian]', default, '1');
		 		 		 		 		 		 		 		 		 		 
		 		 		 		 code_retour := code_retour||'<br> Votre vision ne vous avait pas trahi.
		 		 		 		 		 		 		 		 <br>Il y a bien ici un message qui vous était destiné
		 		 		 		 		 		 		 		 <br>Mais le plus étrange, c''est que ce message n''est pas gravé, il semble flotter sur les murs, comme si sa présence était irréelle.
		 		 		 		 		 		 		 		 <br>sa lecture ne peut que vous laisser perplexe ...
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br><img src="http://www.jdr-delain.net/avatars/quete1_00el1an.png">
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>A sa lecture, une douleur se fait ressentir dans votre tête, et une voix semble vous parler intérieurement :
		 		 		 		 		 		 		 		 <br><b><i>Tu es initié maintenant. A toi de trouver le chemin
		 		 		 		 		 		 		 		 <br>Peu pourront t''aider pour cela, choisi bien tes amis
		 		 		 		 		 		 		 		 <br>Que la foi en Moi te guide vers la suite de ta destinée</i></b>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>Un courant d''air éteint alors votre flamme, votre tête bourdonne, et toute réalité semble s''évanouir ...';
		 		 
		 		 		 		 return code_retour;
		 end if;
		 if (quete_nombre = 2) then

		 		 code_retour := code_retour||'<br>Un sentiment de déjà vu remonte en vous. Mais vous ne savez plus si il s''agit de cette même petite salle déjà visitée ... Une salle portant des marques sur le mur, une salle avec une sorte de poème. Rien n''est pareil. Pourtant, il vous semble vous rappeler les évènements, la voix, l''obscurité ... 
		 		 <br>Vos souvenirs vous joueraient-ils des tours ? Vous seriez vous trompé ?';
		 		 return code_retour;
		 end if;

/* On gère le cas des adeptes d'Elian qui ont validé la deuxième étape dans la cachette 1 ainsi que retour dans un temple, donc code de quête 9, étape 1 de la quête*/
elsif (code_quete = 9) then
		 select into quete_nombre pquete_nombre 
						 from quete_perso 
						 where pquete_quete_cod = code_quete 
						 and pquete_perso_cod = personnage;
/*On regarde à quelle étape de la quête on se trouve */
		 if quete_nombre = 1 then
		 
		 		 		 		 /*validation de l'étape, possibilité de passer à la suivante*/
		 		 		 		 update quete_perso set pquete_nombre = 2 where pquete_perso_cod = personnage and pquete_quete_cod = 9;
		 		 
		 		 		 		 code_retour := code_retour||'<br> A nouveau, une vision en entrant dans cet endroit vous submerge
		 		 		 		 		 		 		 		 <br>Cette sensation, vous la reconnaissez, elle ne peut vous laisser de marbre.
		 		 		 		 		 		 		 		 <br>A nouveau, un message danse devant vos yeux
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br><img src="http://www.jdr-delain.net/avatars/quete_15el1an2.png">
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>Comme la première fois, une douleur se fait ressentir dans votre tête, et une voix semble vous parler intérieurement :
		 		 		 		 		 		 		 		 <br><i><b>Tu as eu le courage de poursuivre.
		 		 		 		 		 		 		 		 <br>Tout est maintenant dit
		 		 		 		 		 		 		 		 <br>Prends garde à l''épée qui pourrait se trouver en travers de ta route</i></b>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>Un courant d''air éteint alors votre flamme, et l''obscurité vous oblige à battre en retraite ...';
		 		 
		 		 		 		 return code_retour;
		 end if;
		 if (quete_nombre = 2) then
		 		 code_retour := code_retour||'<br>Un sentiment de déjà vu remonte en vous. Mais vous ne savez plus si il s''agit de cette même petite salle déjà visitée ... Une salle portant des marques sur le mur, une salle avec une sorte de poème. Rien n''est pareil. Pourtant, il vous semble vous rappeler les évènements, la voix, l''obscurité ... 
		 		 <br>Vos souvenirs vous joueraient-ils des tours ? Vous seriez vous trompé ?';
		 		 return code_retour;
		 end if;


/*Etape 1 Io*/
elsif (code_quete = 10) then
		 select into quete_nombre pquete_nombre 
						 from quete_perso 
						 where pquete_quete_cod = code_quete 
						 and pquete_perso_cod = personnage;
/*On regarde à quelle étape de la quête on se trouve */
		 if quete_nombre = 1 then
		 		 /*Traitement du cas de Io*/
		 		 		 		 /*validation de l'étape, possibilité de passer à la suivante*/
		 		 		 		 update quete_perso set pquete_nombre = 2 where pquete_perso_cod = personnage and pquete_quete_cod = 10;
		 		 		 		 update perso set perso_px = perso_px + 10, perso_prestige = perso_prestige + 2 where perso_cod = personnage;
		 		 		 		 insert into perso_titre values (default, personnage, '[Illuminé par Io]', default, '1');		 		 		 		 
		 		 		 		 		 		 
		 		 		 		 code_retour := code_retour||'<br> Votre vision ne vous avait pas trahi.
		 		 		 		 		 		 		 		 <br>Il y a bien ici un message qui vous était destiné
		 		 		 		 		 		 		 		 <br>Mais le plus étrange, c''est que ce message n''est pas gravé, il semble flotter sur les murs, comme si sa présence était irréelle.
		 		 		 		 		 		 		 		 <br>Sa lecture ne peut que vous laisser perplexe ...
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br><img src="http://www.jdr-delain.net/avatars/quete_I1io6o.png">
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>A sa lecture, une douleur se fait ressentir dans votre tête, et une voix semble vous parler intérieurement :
		 		 		 		 		 		 		 		 <br><b><i>L''Aveugle te guide
		 		 		 		 		 		 		 		 <br>Il est la Balance qui doit guider le monde
		 		 		 		 		 		 		 		 <br>En Moi tu dois voir, pour le futur établir.</i>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>
		 		 		 		 		 		 		 		 <br>Un courant d''air éteint alors votre flamme, votre tête bourdonne, et toute réalité semble s''évanouir ...';
		 		 
		 		 		 		 return code_retour;
		 end if;
		 if (quete_nombre = 2) then

		 		 code_retour := code_retour||'<br>Un sentiment de déjà vu remonte en vous. Mais vous ne savez plus si il s''agit de cette même petite salle déjà visitée ... Une salle portant des marques sur le mur, une salle avec une sorte de poème. Rien n''est pareil. Pourtant, il vous semble vous rappeler les évènements, la voix, l''obscurité ... 
		 		 <br>Vos souvenirs vous joueraient-ils des tours ? Vous seriez vous trompé ?';
		 		 return code_retour;
		 end if;
end if;
return code_retour;
-------------------------------------------------
-- Fin Quête des Dieux, Falis, Elian, Balgur
-------------------------------------------------
end;$function$

