<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
$contenu_page4 = '';
$erreur = 0;

//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
$req_comp = "select count(perso_cod) as nombre from perso,perso_position 
										where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = ".$perso_cod.")
										and perso_quete = 'quete_forgeron.php'
										and perso_cod = ppos_perso_cod";
$db->query($req_comp);
$db->next_record();
if($db->f("nombre") == 0 or $db->nf() == 0)
{
	$erreur = 1;
	$contenu_page4 .= 'Vous n\'avez pas accès à cette page !';
}
$req_quete = "select pquete_quete_cod,pquete_perso_cod,pquete_date_debut,pquete_termine,pquete_param from quete_perso 
							where pquete_perso_cod = ".$perso_cod." and pquete_quete_cod = 19";
$db->query($req_quete);
if ($db->nf() == 0 and !isset($methode3))
{
	$methode3 = 'E';
}
else if ($methode3 != 'suite')
{
	$db->next_record();
	$methode3 = $db->f("pquete_termine"); /*E pas commencé, on lance - N en cours - O quête déjà réalisée*/
	$arme_reparation = $db->f("pquete_param");
}

if ($erreur == 0)
{
		switch($methode3)
		{
			case "E":
				$contenu_page4 .= "Dis, l'Ami ! Peux-tu m'aider? Mon apprenti est parti depuis le passage d'un groupe de Ménestrels ! 
				<br>Je mettrais ma main à couper qu'il a voulu suivre la belle aventure avec une des danseuses, le bougre ! 
				<br>En attendant, me voilà avec tout ce fatras non rangé sur les bras ! Si ça te tente, répare-moi un peu ces objets.
				<br><br>
				<a href=\"".$PHP_SELF."?methode3=suite\"><b>J'accepte avec plaisir</b></a>";
			break;
			
			case "suite":
			 $contenu_page4 .= "tu trouveras un marteau, posé sur l'enclume.
			 <br>Et reviens me voir quand tu auras réussi une réparation. Pour cela il te faudra aussi exercer tes talents à identifier ce que tu as sous les yeux.
			 <br><br><i>Le forgeron vous récompensera lorsque vous aurez réussi au moins un réparation sur l'objet qu'il vous remet.</i><br>";
				$req = "select cree_objet_perso(832,".$perso_cod.") as arme_cassee";
				$db->query($req);
				$db->next_record();
				$arme = $db->f("arme_cassee");
				$req = "update objets set obj_etat = 20 where obj_cod = ".$arme;
				$db->query($req);
				$db->next_record();	
				$req = "insert into quete_perso (pquete_quete_cod,pquete_perso_cod,pquete_param) values (19,".$perso_cod.",".$arme.")";
				$db->query($req);
				$db->next_record();
			break;
			
			case "N":
				/* On vérifie la possession d'un objet pour cette quête et son état*/
				$req = "select obj_gobj_cod,perobj_obj_cod,obj_nom,obj_etat 
						from objets,perso_objets 
						where perobj_obj_cod = obj_cod 
						and perobj_perso_cod = $perso_cod 
						and obj_cod = ".$arme_reparation;
					$db->query($req);
					$db->next_record();
					if($db->nf() == 0)
					{
						$contenu_page4 .= "Vous ne possédez plus l'arme que je vous avez donné. Voilà qui est bien dommage. Si d'aventure vous souhaitiez recommencer cette expérience, revenez me voir, je vous fournirais une autre arme à réparer";
						$req = "delete from quete_perso where pquete_quete_cod = 19 and pquete_perso_cod = ".$perso_cod;
						$db->query($req);				
					}
					else
					if($db->f("obj_etat") > 20)
					{
						$contenu_page4 .= "Ah, bravo ! Je savais que je pouvais compter sur toi ! Merci l'Ami ! 
															<br>Tu peux rester et continuer si tu le souhaites, cela ne te sera pas forcément inutile dans tes aventures et te permettra d'améliorer ce nouveau savoir. En attendant, voici une petite bourse de brouzoufs en récompense. 
															<br><br>Ah, j'oubliais. Méfie-toi quand tu répareras ton propre matériel : il n'aura qu'une capacité limité à retrouver son état d'origine. Plus tu le feras sur un objet, moins il retrouvera ses capacités complètes.
															<br><br>";
						$req = "update perso set perso_px = perso_px + 5,perso_po = perso_po + 50 where perso_cod = ".$perso_cod;
						$db->query($req);
						$req = "update quete_perso set pquete_termine = 'O' where pquete_quete_cod = 19 and pquete_perso_cod = ".$perso_cod;
						$db->query($req);
						$db->next_record();		
					}
					else
					{
						$contenu_page4 .= "<br>Je pense que tu peux faire mieux que cela, soit tu as été très fainéant, soit tu as détérioré cet objet !
																<br>Dans l'un ou l'autre cas, tu dois te remettre à l'ouvrage.";
					}
			break;
			
			case "O":
				/*Quête déjà réalisée, donc on ferme les portes*/
				$contenu_page4 .= "Nous nous sommes déjà rencontré je pense. Et tu as bien travaillé ! Je te félicite encore.";
			break;
		}
}
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page4);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?> 
