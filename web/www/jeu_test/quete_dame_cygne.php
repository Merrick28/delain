<?php 
//
//Contenu de la div de droite
//
$contenu_page = '';
$contenu_page4 = '';
$erreur = 0	;

//On vérifie qu’il s’agit bien d’un perso permettant cette quête sur cette case
$req_comp = "select count(perso_cod) as nombre, max(perso_cod) as numero from perso,perso_position 
	where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod)
		and perso_quete = 'quete_dame_cygne.php'
		and perso_cod = ppos_perso_cod";
$stmt = $pdo->query($req_comp);
$result = $stmt->fetch();
if($result['nombre'] == 0)
{
	$erreur = 1;
	$contenu_page4 .= 'Vous n’avez pas accès à cette page !';
}
$perso_dame_cygne = $result['numero'];
$req_quete = "select pquete_quete_cod,pquete_perso_cod,pquete_nombre,pquete_date_debut,pquete_termine from quete_perso where pquete_perso_cod = $perso_cod and pquete_quete_cod = 18";
$stmt = $pdo->query($req_quete);
if($result = $stmt->fetch())
	$methode3 = $result['pquete_termine']; /*E pas commencé, on lance - N en cours - O quête déjà réalisée*/
else
{
	$methode3 = "E";
}
if ($erreur == 0)
{

	$contenu_page4 .= "<div class='titre'>Quête de la Dame Cygne</div><br /><br />Au détour d’un sentier devenu presque invisible, recouvert par les herbes folles, un petit lac se dévoile. À votre approche, la surface de l’eau semble s’animer pour faire apparaître une créature irréelle : malgré son apparence humaine, son aura ne laisse aucun doute. Il s’agit d’une Dame Cygne, douce et sage, mais au pouvoir magique sans pareil si elle est menacée.
		<br>La légende raconte que ces êtres magiques pondent des pierres de lune, mais nul n’a encore pu vérifier, tant elles se cachent durant cette période.
		<br>Au sortir de l’eau, la créature enchanteresse se dirige vers sa blanche parure pour s’en revêtir : les plumes qui ornent ses vêtements sont sans pareil, fragiles et vaporeuses.
		<br><br>
		La Dame Cygne se tourna alors vers les yeux qui l’observaient depuis un moment :
		<br>- Bienvenue en ces Terres magiques... Qui que tu sois, en fuite ou en exploration, de grandes aventures t’attendent en ces lieux. Si tu souhaites me plaire, peut-être parviendras-tu à m’offrir un cadeau selon mes goûts ? Un papillon blanc ou une fleur rare ...
		<br>Dans le cas contraire, passe ton chemin et suis la route, pour quitter mon domaine. J’apprécie la tranquillité, et seuls les méritants peuvent se vanter de nos discussions.
		<br><br><hr><br>";

	switch($methode3)
	{
		case "E":
			$req = "insert into quete_perso (pquete_quete_cod,pquete_perso_cod) values (18,".$perso_cod.")";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
		break;

		case "N":
			/* On vérifie la possession d’un objet pour cette quête*/
			$req = "select obj_gobj_cod,perobj_obj_cod,obj_nom 
					from objets,perso_objets 
					where perobj_obj_cod = obj_cod 
					and perobj_perso_cod = $perso_cod 
					and obj_gobj_cod = 830 order by obj_gobj_cod";
				$stmt = $pdo->query($req);
				if($stmt->rowCount() != 0)
				{
					$contenu_page4 .= "<br>Voyant son souhait réalisé, la Dame Cygne fit résonner son rire cristallin. Elle semblait satisfaite des efforts fournis et tout en ajustant son collier d’or fin, elle inclina la tête sur le côté, pour sourire :
						<br>- Voilà une preuve de mon affection pour ton dévouement... Prends ce présent: qu’il puisse te porter chance dans tes aventures souterraines. Et voici également ma bénédiction :
						<br>
						<br>Que tes pas même au sein de l’Orage,
						<br>Te guident vers ce que tu cherches.
						<br>Que les épreuves qui fauchent,
						<br>T’apportent Force et Courage...";
					$req = "update perso set perso_px = perso_px + 5 where perso_cod = ".$perso_cod;
					$stmt = $pdo->query($req);
					$req2 = "select gobj_cod,lancer_des(1,1000) as num from objet_generique 
						where gobj_tobj_cod = 21 and gobj_cod not in (561,412) 
						order by num limit 1";
					$stmt = $pdo->query($req2);
					$result = $stmt->fetch();
					$potion = $result['gobj_cod'];
					$req2 = "select cree_objet_perso_nombre('$potion','$perso_cod','1')";
					$stmt = $pdo->query($req2);
					$req = "select lancer_des(1,6) as des";
					$stmt = $pdo->query($req);
					$result = $stmt->fetch();
					$parchemin = 362 + $result['des'];
					$req2 = "select cree_objet_perso_nombre('$parchemin','$perso_cod','1')";
					$stmt = $pdo->query($req2);
					$req2 = "update quete_perso set pquete_termine = 'O' where pquete_quete_cod = 18 and pquete_perso_cod = ".$perso_cod;
					$stmt = $pdo->query($req2);
					$req = "select f_del_objet(obj_cod) from objets
						inner join perso_objets on perobj_obj_cod = obj_cod
						where perobj_perso_cod = $perso_cod 
							and obj_gobj_cod = 830 limit 1";
					$stmt = $pdo->query($req);
					$req = "select insere_evenement($perso_cod, $perso_dame_cygne, 100, '[perso_cod1] a ramené une fleur à la Dame Cygne.', 'O', 'N', null);";
					$stmt = $pdo->query($req);
				}
				else
				{
					$contenu_page4 .= "<br>Vous voici déjà  de retour ? Je ne vois pourtant aucune fleur rare qui pourrait me satisfaire. Soyez donc plus entreprenant dans vos recherches !<br><br>";
				}
		break;
		case "O":
			/*Quête déjà réalisée, mais on va quand même débarrasser le perso de ses fleurs.*/
			$methode = (isset($_GET['methode'])) ? $_GET['methode'] : '';

			$req_nombre = "select count(*) as nombre from objets
				inner join perso_objets on perobj_obj_cod = obj_cod
				where obj_gobj_cod = 830 
					and perobj_perso_cod = $perso_cod";
			$stmt = $pdo->query($req_nombre);
			$result = $stmt->fetch();
			$nombre = $result['nombre'];
			if ($methode == 'vendre' && $nombre > 9)
			{
				$req = "select f_del_objet(obj_cod) from objets
						inner join perso_objets on perobj_obj_cod = obj_cod
						where perobj_perso_cod = $perso_cod 
							and obj_gobj_cod = 830 limit 10";
				$stmt = $pdo->query($req);
				$req = "update perso set perso_po = perso_po + 200 where perso_cod = $perso_cod";
				$stmt = $pdo->query($req);
				$req = "select insere_evenement($perso_cod, $perso_dame_cygne, 100, '[perso_cod1] a vendu dix fleurs à la Dame Cygne.', 'O', 'N', null);";
				$stmt = $pdo->query($req);
			}

			$stmt = $pdo->query($req_nombre);
			$result = $stmt->fetch();
			$nombre = $result['nombre'];
			$contenu_page4 .= "La Dame Cygne vous aperçoit et vous interpelle :
				<br>- Encore merci pour m’avoir ramené cette fleur si rare !";
			
			if ($nombre > 0)
			{
				$contenu_page4 .= "<br><br>Oh, mais je vois qu’il vous en reste $nombre ! Elles ne sont pas aussi belles que la première...
					<br>Allez, je peux vous en reprendre 10 pour 200 brouzoufs, ça servira toujours en infusion.";
				
				if ($nombre > 9)
				{
					$contenu_page4 .= "<br>Acceptez-vous ?
						<br><a href='?methode=vendre'>Bien sûr ! Voici, gente Dame...</a>";
				}
				else
				{
					$contenu_page4 .= "<br>Il faudrait donc que vous m’en rameniez " . (10 - $nombre) . " pour que je vous les rachète.";
				}
			}
		break;
		}
}
echo $contenu_page4;

