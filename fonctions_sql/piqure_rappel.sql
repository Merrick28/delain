CREATE OR REPLACE FUNCTION public.piqure_rappel()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/****************************************/
/* piqure_rappel                        */
/****************************************/
declare
	code_retour text;
	v_sexe text;
	ligne record;
	v_corps text;
	v_mes integer;
begin
	for ligne in select perso_cod,perso_sex from perso
		where perso_piq_rap_env = 0
		and perso_type_perso = 1
		and perso_dcreat + '5 days'::interval < now() loop
		if ligne.perso_sex = 'M' then
			v_corps := 'Alors que vous marchez laborieusement à travers les fourrés des Extérieurs, vous vous remémorez la conversation que vous avez surprise entre Hernin et Gildwen, au poste de garde. Votre esprit commence à s''égarer sur les courbes évocatrices de la gracieuse elfette... Puis, vous essayez de vous reconcentrer sur les paroles.<br>
				<br>
				Apparemment, il faut se méfier des <b>Voyous</b> et des <b>monstres</b>. Parmi les monstres, les <b>morbelins</b> sont intéressants car ils permettent de se procurer une épée. Les <b>queues de rat</b> peuvent être échangées dans les <b>Centres Administratifs</b> contre des brouzoufs, la monnaie locale.<br>
				<br>
				Lorsqu''on a un problème avec un autre aventurier, on peut faire appel à la <b>Milice</b>.<br>
				<br>
				Gildwen aime bien les Miliciens, peut-être que si vous en devenez un, elle succombera à votre charme.... Vous reconcentrez à nouveau votre pensée sur les paroles de Gildwen...<br>
				<br>
				Il vaut mieux parler avec les aventuriers pour composer un <b>groupe</b> au plus vite... et peut-être que parmi eux, il y aura un visage aussi ravissant que celui de...<br>
				<br>
				<br>
				Bref, fort de tous ces beaux conseils, vous les mettez en pratique sans plus tarder ! ';
		else
			v_corps := 'Alors que vous marchez laborieusement à travers les fourrés des Extérieurs, vous vous remémorez la conversation que vous avez surprise entre Hernin et Gildwen, au poste de garde. L''elfette avait l''air bien délurée, pensez-vous avec répprobation... Puis, vous essayez de vous reconcentrer sur les paroles.<br>
				<br>
				Apparemment, il faut se méfier des <b>Voyous</b> et des <b>monstres</b>. Parmi les monstres, les <b>morbelins</b> sont intéressants car ils permettent de se procurer une épée. Les <b>queues de rat</b> peuvent être échangées dans les <b>Centres Administratifs</b> contre des brouzoufs, la monnaie locale.<br>
				<br>
				Lorsqu''on a un problème avec un autre aventurier, on peut faire appel à la <b>Milice</b>.<br>
				<br>
				Gildwen aime bien les Miliciens... Tsss, Encore une Marie-Couche-Toi-Là que cela ne vous étonnerait guère, elle avait un genre si vulgaire ! Vous reconcentrez à nouveau votre pensée sur les paroles de Gildwen...<br>
				<br>
				Il vaut mieux parler avec les aventuriers pour composer un <b>groupe</b> au plus vite... En tout cas, une chose est sure, pas question que vous vous associiez à une nénette écervelée comme cette Gildwen, elle pourrait vous faire de l''ombre en plus...<br>
				<br>
				<br>
				Bref, fort de tous ces beaux conseils, vous les mettez en pratique sans plus tarder !';
		end if;
	v_mes := nextval('seq_msg_cod');
	insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps) values
		(v_mes,now(),now(),'Souvenirs...',v_corps);
	insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)		
		values (v_mes,1773905,'N');
	insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values (v_mes,ligne.perso_cod,'N','N');
	update perso set perso_piq_rap_env = 1 where perso_cod = ligne.perso_cod;
	end loop;
/*Message pour la contrebande en parallèle */
/* Désactivé pour cause de dissolution. */
	for ligne in select perso_cod,perso_sex from perso
		where perso_piq_rap_env = 1
		and perso_type_perso = 1
		and perso_dcreat + '8 days'::interval < now() loop
		
		v_corps := 'Hep, l''ami !
<br><br>
Oui toi qui viens d''arriver dans ce monde peuplé de créatures étranges 
et d''aventuriers en soif de gloire et de richesses. N''aie pas peur, je 
n''en veux pas à ta bourse...
<br><br>
Peut-être as tu quelques minutes à me consacrer ?
<br><br>
Ce bon roi Hormandre veut annexer ce royaume pour le rattacher à son 
domaine, vois tu. Mais il se moque éperdument de ceux qui foulent le sol de 
Delain. Regarde ton équipement, tu n''aimerais pas toi aussi pouvoir 
t''équiper correctement comme ces fanfarons de Miliciens ? Tu n''aimerais 
pas pouvoir planter quelques coudes d''acier dans tous ces monstres ou ces 
aventuriers qui en veulent à tes maigres richesses ?
<br><br>
Mais tu n''as pas envie de reconnaitre la souveraineté de ce roi qui se 
moque des sujets de son royaume ou de ceux qui oeuvrent à libérer les 
souterrains des créatures de Malkiar ? Tu n''as pas envie d''enrichir la 
cour qui se roule dans la luxure par l''argent durement ramassé par les 
honnêtes aventuriers que nous sommes, n''est ce pas ?
<br><br>
Tu m''as l''air sympathique, aussi, je vais te confier un petit secret, 
mais il faut que tu le gardes pour toi, l''ami. Si tu n''as pas envie 
d''avoir à faire avec les échoppes du roi Hormandre et que tu veux faire de 
vraies affaires, contacte les Contrebandiers, adresse toi à eux et nul 
doute qu''ils sauront trouver quelque chose pour te satisfaire.
<br><br>
N''oublie pas mon conseil l''ami, sombrer dans la facilité du commerce 
avec la Caravane c''est enrichir un roi qui n''a que faire de ton sort.';
--	v_mes := nextval('seq_msg_cod');
--	insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps) values
--		(v_mes,now(),now(),'Dans un recoin ...',v_corps);
--	insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)		
--		values (v_mes,858361,'N');
--	insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
--		values (v_mes,ligne.perso_cod,'N','N'); 		
	update perso set perso_piq_rap_env = 2 where perso_cod = ligne.perso_cod;
	end loop;
	return 'OK';
end;$function$

