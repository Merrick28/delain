<?php include_once "verif_connexion.php";
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
ob_start();

// Vérification des paramètres d’appel : existance.
$erreur = !isset($_GET['perso']) || !isset($_GET['methode']) || !isset($perso_cod);
if (!$erreur)
{
	$perso = $_GET['perso'];
	$methode = $_GET['methode'];
}
switch ($methode)
{
	case 'stop':
	case 'stop_oui':
	case 'stop_non':
	case 'oui':
	case 'non':
	break;

	default: $erreur = true; break;
}

$erreur = $erreur || !is_numeric($perso);
$nom_ia = 'Une voix d’outre-tombe';
$err_msg = 'Je suis désolé, mais je n’ai rien compris à ce que vous me racontez ! Et d’abord, vous êtes qui, vous ?';

// On vérifie que le perso soit bien en train de suivre l’appelant, et qu’il en attend une réponse.
$attente = false;
if (!$erreur)
{
	$req_verif = "select pia_parametre, pia_msg_statut, perso_nom from perso_ia
		inner join perso on perso_cod = pia_perso_cod
		where pia_perso_cod = $perso AND pia_ia_type = 15";
	$db->query($req_verif);
	$erreur = !$db->next_record();
	
	if (!$erreur)
	{
		$nom_ia = $db->f('perso_nom');
		$suivre = $db->f('pia_parametre');
		$attente = ($db->f('pia_msg_statut') == '1');
		$erreur = $perso_cod != $suivre;
	}
}

echo "<p><strong>$nom_ia vous répond :</strong></p>";
if (!$erreur)
{
	$req_nom = "select perso_nom from perso where perso_cod = $perso_cod";
	$db->query($req_nom);
	$db->next_record();
	$nom = $db->f('perso_nom');

	switch ($methode)
	{
		case 'oui':
			if (!$attente)
				$reponse = 'Ah, désolé, mais vous m’aviez déjà répondu ! Si je n’ai pas fait le bon choix, il faudrait que vous reveniez me chercher...';
			else
			{
				$req_passage = "select passage($perso) as resultat";
				$db->query($req_passage);
				$db->next_record();
				$resultat = $db->f('resultat');
				$tab_resultat = explode('#', $resultat);

				$reponse = "Ah, excellent, $nom ! J’y vais de ce pas, j’espère vous retrouver de l’autre côté.<br />";
				$reponse .= $tab_resultat[0];

				$req_maj = "update perso_ia set pia_msg_statut = 0 where pia_perso_cod = $perso";
				$db->query($req_maj);
			}
		break;
		case 'non':
			if (!$attente)
				$reponse = 'Ah, désolé, mais vous m’aviez déjà répondu ! Si je n’ai pas fait le bon choix, il faudrait que vous reveniez me chercher...';
			else
			{
				$reponse = "Très bien $nom, je ne prends pas cet escalier. Mais ne me laissez pas tout seul !";
				$req_maj = "update perso_ia set pia_msg_statut = 0 where pia_perso_cod = $perso";
				$db->query($req_maj);
			}
		break;
		case 'stop':
			$reponse = "Que... Que je vous laisse tranquille ? Mais, vous deviez me mener à ma destination ! Qu’est-ce que je vais devenir ?? <br />";
			$reponse .= "<br /> -> De la chair à pâté pour Malkiar, sans aucun doute ! <a href='?methode=stop_oui&perso=$perso'>Confirmer l’abandon du PNJ</a>.<br />";
			$reponse .= "<br /> -> Mais non, ah ah ! Je vous ai bien eu, hein ?! Suivez-moi... <a href='?methode=stop_non&perso=$perso'>Ne pas abandonner le PNJ</a>.<br />";
		break;
		case 'stop_oui':
			$reponse = "Je... C’est honteux ! Soyez sûr que je m’en souviendrai !";
			$req_maj = "update perso_ia set pia_parametre = 0 where pia_perso_cod = $perso";
			$db->query($req_maj);
		break;
		case 'stop_non':
			$reponse = "Ah... Ah ah... Vous êtes blagueur, vous ! Ne me refaites jamais une peur pareille !";
		break;
	}
	echo "<p>$reponse</p>";
}
else
{
	echo "<p>$err_msg</p>";
}

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
