<?php 
//
//Contenu de la div de droite
//
$contenu_page = '';
$db2 = new base_delain;
$db3 = new base_delain;
$contenu_page4 = '';
$erreur = 0	;

//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
$req_comp = "select count(perso_cod) as nombre from perso,perso_position
										where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod)
										and perso_quete = 'quete_chasseur.php'
										and perso_cod = ppos_perso_cod";
$db->query($req_comp);
$db->next_record();
if($db->f("nombre") == 0 or $db->nf() == 0)
{
	$erreur = 1;
	$contenu_page4 .= 'Vous n\'avez pas accès à cette page !';
}
$req_quete = "select pquete_quete_cod,pquete_perso_cod,pquete_nombre,pquete_date_debut from quete_perso where pquete_perso_cod = $perso_cod and pquete_quete_cod = 13";
$db->query($req_quete);
$quete_avancement = 0;
if ($db->nf() != 0)
{
	$db->next_record();
	$quete_avancement = $db->f("pquete_nombre");
}

if (!isset($methode3))
{
	$methode3 = 'debut';
}
if ($erreur == 0)
{

		switch($methode3)
		{
		case "debut":
			$contenu_page4 .= "Alors que vous vaquiez à vos traditionnelles occupations d'aventurier aguerri, vous sentez subitement une présence toute proche. Une respiration paisible se fait entendre juste derrière vous. Sursautant, vous vous retournez en un bond. Vous faites face à un individu dont la dégaine ne laisse aucun doute : vous ne faites certainement pas face à un marchand grassouillet ou un enchanteur de foire.
												<br /><br />L'homme est solidement bâti, il arbore une armure solide, tient fermement une lourde arbalète, une épée effilée pend à son côté. Se débarrassant de son capuchon d'un geste vif, il découvre un visage buriné et marqué de nombreuses cicatrices. Une détermination sans faille se lit dans le regard appuyé qu'il vous adresse. Sur l'épaule, il arbore un sigle auquel vous n'aviez jamais été confronté. On peut y voir la silhouette d'un homme dont l'épée longue est fichée dans la poitrine béante d'un basilic terrassé et qui pointe son arbalète comme s'il s'apprêtait à en découdre avec un autre monstre.
												<br /><br /><i>- « Je fais partie de la Guilde des Traqueurs »</i>, vous dit il simplement.";
			if ($quete_avancement != 0)
			{
				if($quete_avancement == 1)
				{
					$titre = "Apprenti traqueur";
				}
				else if ($quete_avancement == 2)
				{
						$titre = "Traqueur";
				}
				else if ($quete_avancement == 3)
				{
						$titre = "Traqueur aguerri";
				}
				$contenu_page4 .= "<br /><i>«  Je vois que vous avez déjà rencontré un de mes collègues, et qu'il vous a jugé apte à être un \"". $titre ."\" !
													<br />C'est un honneur immense.  » </i><br>";
			}
			$contenu_page4 .= "<a href=\"$PHP_SELF?methode3=suite\">suite ...</a><br /><br />";

		break;
		case "suite":
		$req = "select 	pquete_cod,pquete_quete_cod,pquete_perso_cod,pquete_nombre,pquete_date_debut,pquete_date_fin,pquete_termine,pquete_param
										from quete_perso
										where pquete_quete_cod = 12
										and pquete_perso_cod = $perso_cod
										and pquete_termine not in ('O','R')";/*On a la quête en elle même pour chasser les monstres*/
		$db->query($req);
		if ($db->nf() != 0) // le perso a une mission en cours, on teste si il l'a terminée.
		{
			$db->next_record();
			$statut_quete = $db->f("pquete_termine");
			$nombre_monstre = $db->f("pquete_nombre");
			$monstre_mission = $db->f("pquete_param");
			$debut_mission = $db->f("pquete_date_debut");
			$fin_mission = $db->f("pquete_date_fin");
			$quete_cod = $db->f("pquete_cod");
				$total_encours = 0;
				$req = "select sum(ptab_total) as total_encours from perso_tableau_chasse
								where ptab_perso_cod = $perso_cod
								and ptab_gmon_cod = $monstre_mission
								and ptab_date > '$debut_mission'
								and ptab_date < '$fin_mission'";
				$db3->query($req);
				$db3->next_record();
				$total_encours = $db3->f("total_encours");
				if ($db3->nf() == 0 or is_null($total_encours))
				{
					$total_encours = 0;
				}
				if ($db3->nf() == 0 or $total_encours < $nombre_monstre)
				{
                                            $req = "update quete_perso set pquete_termine = 'R' where pquete_cod = $quete_cod and pquete_date_fin < now()";
                                            $db->query($req);
                                            if ($db->nf() == 0) // Pas d'expiration pour cette mission.
                                            {
                                                $req2 = "select gmon_nom from monstre_generique
                                                                where gmon_cod = $monstre_mission";
                                                $db2->query($req2);
                                                $db2->next_record();
                                                $monstre_nom = $db2->f("gmon_nom");
                                                $contenu_page4 .= "Le traqueur vous regarde intensément quelques secondes. Il ajuste son fourreau et hausse les épaules :
																																	<br /><br /><i>- « Votre tête ne m'est pas inconnue » </i> finit il par lâcher platement <i>« Vous semblez déjà avoir une tâche de ce type il me semble ! Ne l'avez-vous donc toujours pas effectuée ? Peut-être me suis-je trompé sur votre personne, moi qui croyais que vous aviez la trempe d'un vrai chasseur de monstres. Ne revenez me voir qu'une fois ces créatures de l'Innommable tuées, vous savez bien, ceux qu'on appelle ". $monstre_nom ." ! </i>»";
                                            }
                                            else
                                            {
                                            	$contenu_page4 .= "Le traqueur vous regarde intensément ... baisse la tête et fait un signe de négation ...
                                            	<br /><br /><i>- « Vous ne semblez pas digne de la guilde des traqueurs. Vous avez échoué dans la mission qui vous avait été confiée.</i> »";
                                            }
				}
				else
				{
				$contenu_page4 .= "<i>- «  Ah ! Vous, vous avez bien travaillé à ce que je vois ! La guilde des Traqueurs a bien fait de vous accorder sa confiance. N'était-ce pas trop dur ? Non, je suis sur que vous avez mené cette tâche à bien de main de maître  »</i> clame le traqueur à la mine réjouie <i>« Chose promise chose due, voici donc votre prime de chasse ! »</i>";
					if ($quete_avancement == 1) /*On regarde si il est déjà engagé dans la quête des traqueurs, et donc, on change les paramètres*/
					{
						$contenu_page4 .= "<br /><br /> Vous recevez 7500 brouzoufs, 15 points d'expérience et 2 points de renommée.
															<br /><br /> Votre réputation auprès de la guilde des Traqueurs a augmenté, le titre de <i>Traqueur</i> vous est officiellement décerné. ";
						$req = "update perso set perso_po = perso_po + 7500,perso_px = perso_px + 15,perso_prestige = perso_prestige + 2
										where perso_cod = $perso_cod";
						$db->query($req);
						$req = "update quete_perso set pquete_termine = 'O' where pquete_cod = $quete_cod";
						$db->query($req);
						$req = "delete from perso_titre where ptitre_perso_cod = $perso_cod and ptitre_type = 5";
						$db->query($req);
						$req = "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date,ptitre_type) values ($perso_cod,'Traqueur',now(),5)";
						$db->query($req);
						$req = "update quete_perso set pquete_nombre = 2 where pquete_quete_cod = 13 and pquete_perso_cod = $perso_cod";
						$db->query($req);
					}
					else if ($quete_avancement == 2)
					{
						$contenu_page4 .= "<br /><br /> Vous recevez 10000 brouzoufs, 20 points d'expérience et 3 points de renommée.
															<br /><br /> Votre réputation auprès de la guilde des Traqueurs a augmenté, le titre de <i> Traqueur aguerri</i> vous est officiellement décerné. ";
						$req = "update perso set perso_po = perso_po + 10000,perso_px = perso_px + 20,perso_prestige = perso_prestige + 3
										where perso_cod = $perso_cod";
						$db->query($req);
						$req = "update quete_perso set pquete_termine = 'O' where pquete_cod = $quete_cod";
						$db->query($req);
						$req = "delete from perso_titre where ptitre_perso_cod = $perso_cod and ptitre_type = 5";
						$db->query($req);
						$req = "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date,ptitre_type) values ($perso_cod,'Traqueur aguerri',now(),5)";
						$db->query($req);
						$req = "update quete_perso set pquete_nombre = 3 where pquete_quete_cod = 13 and pquete_perso_cod = $perso_cod";
						$db->query($req);
					}
					else
					{
						$contenu_page4 .= "<br /><br /> Vous recevez 5000 brouzoufs, 10 points d'expérience et 1 point de renommée.
															<br /><br /> Votre réputation auprès de la guilde des Traqueurs a augmenté, le titre d'<i>Apprenti traqueur</i> vous est officiellement décerné. ";
						$req = "update perso set perso_po = perso_po + 5000,perso_px = perso_px + 10,perso_prestige = perso_prestige + 1
										where perso_cod = $perso_cod";
						$db->query($req);
						$req = "update quete_perso set pquete_termine = 'O' where pquete_cod = $quete_cod";
						$db->query($req);
						$req = "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date,ptitre_type) values ($perso_cod,'Apprenti traqueur',now(),5)";
						$db->query($req);
						$req = "insert into quete_perso (pquete_quete_cod,pquete_perso_cod,pquete_nombre,pquete_date_debut)
																		values ('13','$perso_cod',1,now())";
						$db->query($req);
					}
				}
		}
		else /*le perso n'a pas de quête de ce type engagée, on teste si on lui en donne une
		Pour l'instant, test sur son num de perso et la date. On pourrait aussi imaginer de ne faire ça que tous les 10 premiers jours
		de chaque mois*/
		{

				/*$test_quete = $perso_cod[strlen($perso_cod)-1];
				$date = date("d");
				$date_quete = $date[strlen($date)-1];*/
				$req = "select donne_contrat($perso_cod) as resultat";
				$db->query($req);
				$db->next_record();
				$donne_contrat = $db->f("resultat");

				$req = "select perso_niveau, etage_reference
                    from perso, perso_position, positions, etage
                    where perso_cod = ppos_perso_cod
                    and ppos_pos_cod = pos_cod
                    and pos_etage = etage_numero
                    and perso_cod = $perso_cod";
				$db->query($req);
				$db->next_record();
				$perso_level = $db->f("perso_niveau");
				$etage_reference = $db->f("etage_reference");
				if ($donne_contrat == 'O'  or $perso_cod == '61')		/*Les dates coincident, on va lui donner une mission*/
				{
					$input = array ("<br />S'approchant de vous, le traqueur vous glisse quelques mots à l'oreille :
															<br /><br /><i>- « Avez-vous déjà eu l'occasion de combattre des séides de Malkiar ? Vous m'en avez tout l'air. Mes informateurs m'ont certifié que certaines créatures pullulaient un peu trop. La guilde des Traqueurs est prête à récompenser toute personne acceptant d'en exécuter un certain nombre ! A vous voir, j'ai la certitude qu'il s'agit là d'une tâche que vous serez à même de remplir. Ne tardez pas, fourbissez vos armes et revenez me voir une fois le travail accompli. Vous pourrez aussi voir un de mes confrères traqueur. La guilde des traqueurs saura se montrer généreuse. <br /> Rien ne servira d'aller dans un bâtiment administratif, vous connaissez ces gens ! Tous des fonctionnaires d'Hormandre qui ne connaissent rien à la traque de bêtes féroces ! Ils pensent pouvoir mener leurs propres traques, mais ce sont des amateurs !» ",
													"<br />Posant solennellement sa main sur votre épaule, le traqueur plonge son regard dans le vôtre. Vous sentez sa poigne d'acier s'exercer sur votre bras à mesure qu'il le serre, comme pour en évaluer la force :
															<br /><br /><i>- « Certaines créatures nous posent actuellement problème. Nos éclaireurs nous affirment qu'ils font un véritable massacre chez les aventuriers. La guilde des Traqueurs ne peut rester inactive face à une telle menace. Vous m'avez tout l'air d'avoir la trempe d'un sacré chasseur vous ! Que diriez vous de nous apporter votre concours, j'ai ici un petit contrat que vous rempliriez sans mal, j'en suis sur ! Il vous suffit d'occire quelques uns de ces monstres et de revenir me voir une fois le travail accompli. Vous pourrez aussi voir un de mes confrères traqueur. Les Traqueurs vous récompenseront, soyez sans crainte ! <br /> Rien ne servira d'aller dans un bâtiment administratif, vous connaissez ces gens ! Tous des fonctionnaires d'Hormandre qui ne connaissent rien à la traque de bêtes féroces ! Ils pensent pouvoir mener leurs propres traques, mais ce sont des amateurs !»",
													"<br />D'un ton jovial, le traqueur commence par vous congratuler sur l'apparente forme physique que vous semblez tenir. Il s'approche de vous, vous adresse une tape virile sur l'épaule et un sourire triomphant :
															<br /><br /><i>- « On voit que vous n'êtes pas n'importe qui vous ! La guilde des Traqueurs est à la recherche de solides aventuriers afin d'aller trépaner quelques subordonnés du Démon Rouge. Ces saloperies pullulent et mettent à mal bon nombre de vos collègues. Je suis certain que vous n'y êtes pas insensible. Revenez me voir une fois que vous aurez fait comprendre qui commande à ces créatures. Vous pourrez aussi voir un de mes confrères traqueur. La guilde des Traqueurs ne manquera pas de vous signifier sa gratitude ! <br /> Rien ne servira d'aller dans un bâtiment administratif, vous connaissez ces gens ! Tous des fonctionnaires d'Hormandre qui ne connaissent rien à la traque de bêtes féroces ! Ils pensent pouvoir mener leurs propres traques, mais ce sont des amateurs !»
													");
					$wanted = array_rand ($input, 1);
					$phrase_wanted = $input[$wanted];
					/* On détermine l'intervalle de niveau du monstre qui va êter chassé*/
					$niv_mini = 8;
					$niv_max = 10;
					if ($quete_avancement == 1) /*On regarde si il est déjà engagé dans la quête des traqueurs, et donc, on change les paramètres*/
					{
						$phrase_wanted .= "<br /><i>«  La guilde des Traqueurs a encore besoin de vous. Le combat contre les hordes de Malkiar ne fait que commencer, accepteriez vous un autre contrat ?  » </i>";
						$niv_mini = 5;
						$niv_max = 15;
					}
					else if ($quete_avancement == 2)
					{
						$phrase_wanted .= " «  La guilde des Traqueurs aurait encore un petit service à vous demander. Accepteriez-vous un autre contrat ?  »";
						$niv_mini = 0;
						$niv_max = 25;
					}
					if(isset($_COOKIE['quete_gmon_cod']))
					{
						$req = "select gmon_cod,gmon_nom,gmon_avatar
												from monstre_generique
												where gmon_cod = " . $_COOKIE['quete_gmon_cod'];
					}
					else
					{
                        if ( $etage_reference == -9 ) // Étage le plus bas du jeu...
                        {
                            $niv_mini = $perso_level;
                        }
						$req = "select gmon_cod,gmon_nom,gmon_avatar
												from monstre_generique
												where gmon_niveau between $perso_level - $niv_mini and $perso_level + $niv_max
												and gmon_quete = 'O'
												and gmon_cod in
																	(select rmon_gmon_cod from repart_monstre where rmon_etage_cod in
																		(select pos_etage from perso_position,positions
																		where ppos_perso_cod = $perso_cod
																		and ppos_pos_cod = pos_cod))
												order by random()
												limit 1";
					}
					$db->query($req);
					if ($db->nf() == 0) /*Le perso n'a pas le niveau requis pour cet étage pour avoir une mission*/
					{
						$contenu_page4 .= '<br><i>Je ne vous vois pas combattre un quelconque monstre à cet étage. Sachez que les Traqueurs sont très exigeants quant aux cibles à chasser !</i><br><br>';
					}
					else
					{
						$contenu_page4 .= $phrase_wanted;

						$db->next_record();
						$quete_gmon_cod = $db->f("gmon_cod");
						setcookie("quete_gmon_cod",$quete_gmon_cod,time()+86400);
						$monstre = $db->f("gmon_cod");
						$monstre_nom = $db->f("gmon_nom");
						$avatar = $db->f("gmon_avatar");
						srand ((double) microtime() * 10000000); // pour intialiser le random
						$contenu_page4 .= "<br /><br />- « Ces temps-ci, ce sont les ".$monstre_nom." qui créent bien des soucis à la guilde des Traqueurs ! C'est dans leurs rangs qu'il faudra frapper fort ! ";
						if ($avatar != null) //Utilisation des avatars si il existe pour ce monstre.
						{
						$contenu_page4 .= "<br>Ce type de monstre est facilement reconnaissable. Il ressemble à cela :<br><p\"><img src=\"../avatars/" . $avatar . "\"></p>";
						}
						$contenu_page4 .= "<br><i>Alors, tope là ? »</i><br /><br /> <b>(Attention, ce choix vous engage pour une certaine durée)</b>";

						$contenu_page4 .= "<form name=\"mission\" method=\"post\" action=\"$PHP_SELF\">
										<input type=\"hidden\" name=\"methode3\" value=\"mission\">
											<table>
											<tr>
						<td class=\"soustitre2\"><p>Oui, cela peut être intéressant</td>
						<td><input type=\"radio\" class=\"vide\" name=\"controle1\" value=\"oui\"></td>
											</tr>
											<tr>
						<td class=\"soustitre2\"><p>Non, je préfère réfléchir encore un peu</td>
						<td><input type=\"radio\" class=\"vide\" name=\"controle1\" value=\"non\"></td>
											</tr>
											<tr>
											<td><input type=\"hidden\" class=\"vide\" name=\"monstre\" value=\"<?=$monstre;?>\"></td>
											</tr>
											<tr>
											<td><input type=\"hidden\" class=\"vide\" name=\"monstre_nom\" value=\"<?=$monstre_nom;?>\"></td>
											</tr>
											</table>
											<input type=\"submit\" class=\"test\" value=\"Valider !\">
											</form>";
					}
				}
				else //Le perso n'obtiendra pas de quête aujourd'hui
				{
						$contenu_page4 .= "<br />Le traqueur semble à peine prêter attention à vous. Finalement, il daigne vous adresser la parole :
															<br /><br /><i>- « Désolé mais je n'ai rien à vous proposer pour l'instant ! Je suis bien trop occupé à m'occuper de tous les contrats d'autres prétendus chasseurs s'évertuent à ne pas remplir. Qui sait, si nos chemins se recroisent, il en sera peut-être autrement. Pour l'heure, je vous salue ! » </i><br /><br />";
				}
		}
		break;

		case "mission":
			if(isset($_COOKIE['quete_gmon_cod']))
			{
				$req = "select gmon_cod,gmon_nom,gmon_avatar
										from monstre_generique
										where gmon_cod = " . $_COOKIE['quete_gmon_cod'];
				$db->query($req);
				$db->next_record();
				$monstre = $db->f("gmon_cod");
				$monstre_nom = $db->f("gmon_nom");
			}
			else
			{
				$monstre = $_POST['monstre'];
				$monstre_nom = $_POST['monstre_nom'];
			}
			$mission = $_POST['controle1'];
			if ($mission == 'oui')
			{
			$random = rand (1,4);
			$temps = $random + 4; //Nombre de semaines autorisées pour la mission
			$contenu_page4 .= "<i>- «  J'étais sur que vous étiez de la trempe de ceux qui ne reculent pas devant le danger »</i> vous fait le traqueur d'un ton amical <i>« Sachez cependant qu'aucun autre contrat ne pourra vous être délivré tant que vous n'aurez pas mené celui-ci à bien ! Montrez-vous inflexible, ces créatures doivent mourir ! partez donc en chasse au $monstre_nom ! Ramenez nous en <b>$random</b> pour nous montrer votre courage. <br>Nous vous laissons $temps semaines pour réaliser cette mission, autrement, cela signifiera que vous n'êtes pas digne des vrais traqueurs.
						<br>La mission pour tuer doit se réaliser dans ce laps de temps, mais vous pourrez ensuite prendre tout le temps que vous souhaitez pour venir chercher votre récompense.»</i> ";
			$req = "insert into quete_perso (pquete_quete_cod,pquete_perso_cod,pquete_nombre,pquete_date_debut,pquete_date_fin,pquete_param)
																values ('12','$perso_cod','$random',now(),now() + '$temps weeks'::interval,'$monstre')";
			$db->query($req);
			$db->next_record();

			}
			else
			{
			/***********/
			/* COOKIES */
			/***********/
			setcookie("quete_gmon_cod",$quete_gmon_cod,time()+86400);
			$contenu_page4 .= "<i>- «  Je me suis sans doute trompé sur vous  »</i> lâche platement le traqueur <i>« Ce n'est pas avec des fainéants comme vous qu'on fera reculer les forces de l'Innommable. Je tiens également à vous préciser que si nous venions à nous recroiser, le même contrat pourrait vous être à nouveau proposé !  » </i>";
			}
		break;
	}
}
echo $contenu_page4;
?>
