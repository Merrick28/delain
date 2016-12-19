<?php 
define("SYNODIC", 29.53058867); //constante pour la période synodique

define("MSPARJOUR", 24 * 60 * 60 * 1000); //constante pour le nombre de millisecondes par jour


function CalcPhase() 
{
	$DateRef = mktime(8,53,0,7,29,2003); //date de référence : le 29/07/2003 à 6:53:00 GMT il y a eu une pleine lune
	$Aujourdui = mktime(); //date du jour
	$msDiff = ($Aujourdui - $DateRef)*1000 + MSPARJOUR; //on calcule la différence en millisecondes
	$phase = ($msDiff * 100)/(SYNODIC * MSPARJOUR); //on calcule le pourcentage de la phase
	
	while($phase>100) // tant que c'est supérieur à 100, on retire 100
	{
		$phase -= 100;
	}
	return $phase;
}

function ImgPhase($phase) 
{
	$NumImage = round(24 * $phase / 100, 0); // on convertit le pourcentage en age de la lune (sur 29 jours)
	if($NumImage==0) 
		$NumImage = 1;
		$ImagePhase = 'http://www.jdr-delain.net/images/lune_'.$NumImage.'.png';
		return $ImagePhase;
}

function PartEntier($phase) 
{
	if ( $phase <= 50.0 )
	{
		$plein = $phase * 2;
	}
	else
	{
		$plein = ( 100 - $phase ) * 2;
	}
	return $plein;
}

function NommerPhase($phase)
//convertit le pourcentage de lunaison en mots
{
	if($phase >= 0 && $phase < 2.5)
	{
		$NomPhase = "<b>Nouvelle Lune
		</b><br><br>Cette phase est particulièrement propice à la récupération de composants, et notammant les plus rares.";
	}
	else if($phase >= 2.5 && $phase < 22.5)
	{
		$NomPhase = "<b>Premier Croissant
		</b><br><br>Phase peu fertile, on peut trouver des composants de potion, mais ceux ci sont à trier scrupuleusement, rendant la recherche plus pauvre";
	}
	else if($phase >= 22.5 && $phase < 27.5)
	{
		$NomPhase = "<b>Premier Quartier
		</b><br><br>A cette période, la recherche est plutôt bonne, un bon chercheur saura toujours s'y retrouver. Moins propice que la nouvelle lune ou pleine lune, cela reste quand même l'une des meilleures phase de récupération d'ingrédients pour les potions.";
	}
	else if($phase >= 27.5 && $phase < 47.5)
	{
		$NomPhase = "<b>Lune gibbeuse
		</b><br><br>Cette phase lunaire est assez neutre. Certains y trouveront leur compte, mais d'autres la trouveront bien pauvre comparée aux nouvelles lunes et pleines lunes.";
	}
	else if($phase >= 47.5 && $phase < 52.5)
	{
		$NomPhase = "<b>Pleine Lune
		</b><br><br>Cette phase est particulièrement propice à la récupération de composants, et notammant les plus rares.";
	}
	else if($phase >= 52.5 && $phase < 73.5)
	{
		$NomPhase = "<b>Lune gibbeuse.
		</b><br><br>Cette phase lunaire est assez neutre. Certains y trouveront leur compte, mais d'autres la trouveront bien pauvre comparée aux nouvelles lunes et pleines lunes.";
	}
	else if($phase >= 73.5 && $phase < 77.5)
	{
		$NomPhase = "<b>Dernier quartier
		</b><br><br>A cette période, la recherche est plutôt bonne, un bon chercheur saura toujours s'y retrouver. Moins propice que la nouvelle lune ou pleine lune, cela reste quand même l'une des meilleures phase de récupération d'ingrédients pour les potions.";
	}
	else if($phase >= 77.5 && $phase < 97.5)
	{
		$NomPhase = "<b>Dernier croissant
		</b><br><br>Phase peu fertile, on peut trouver des composants de potion, mais ceux ci sont à trier scrupuleusement, rendant la recherche plus pauvre";
	}
	else
	{
		$NomPhase = "<b>Nouvelle Lune
		</b><br><br>Cette phase est particulièrement propice à la récupération de composants, et notammant les plus rares.";
	}
return $NomPhase;
}

function JoursAvantNL($phase) //calcule le nombre de jours avant la nouvelle Lune
{ 
$val = round((1 - $phase / 100) * SYNODIC,2);
return $val; 
}

function JoursAvantPL($phase) //calcule le nombre de jours avant la pleine Lune
{
	if ($phase < 50)
	{
		$val = round((0.5 - $phase / 100) * SYNODIC,2);
		return $val; 
	}
	else 
	{
				$val = round((1.5 - $phase / 100) * SYNODIC,2);
		return $val; 
	}
}

	$soleil = "inconnu";
		$Tdate = getdate($tstamp);
		$heure=$Tdate["hours"];
		if($heure <6 || $heure > 18){
			$soleil = "NUIT";
		} else {
			$soleil = "JOUR";
		}


/*if(!isset($db))
	include "verif_connexion.php";
$db2 = new base_delain;
include "../includes/img_pack.php";
include "../includes/template.inc";
// definition des variables de template
$temp = new template;
$temp->set_file("FileRef","../template/classic/" . $nom_template . ".tpl");
// chemins
$temp->set_var("g_url",$type_flux.G_URL);
$temp->set_var("g_che",G_CHE);
$temp->set_var("action_onload",$action_onload);
// chemin des images
$temp->set_var("img_path",G_IMAGES);
// contenu de la page*/

$phase = CalcPhase();
$contenu_page .= '<table width="70%">
											<td><b>- Phase lunaire : </td>
											<td><b>- Image lune : </b></td>
											<td><b>- Aide Lunaire : </b></td>
										<tr>
											<td><i>'.NommerPhase($phase).'</i></b></td>
											<td><img src="'.ImgPhase($phase).'"></td>
											<td><i>Il reste '. JoursAvantNL($phase) .' jours avant la nouvelle lune
											<br> et '. JoursAvantPL($phase) .' jours avant la pleine lune <i></td>
										</tr>
										</table>';

//
// Tests sur les possibilités de cueillettes
//
if (!isset($methode))
		$methode = "debut";
switch($methode)
	{
		case "debut":
			$contenu_page .= '<br><br><br><i>Vous ne pouvez pas récupérer de composants à n\'importe quel moment. En effet, il ne faut pas que vous soyez en combat pour cela ...
			<br>En dehors de cette contrainte, vous pouvez lancer une cueillette quand vous le souhaitez. Vous récupèrerez plusieurs composants. Mais si aucun composant ne se trouve dans la zone vos PA seront perdus.
			<br>Pour améliorer vos chances, vous pouvez lancer des détections, qui vous indiqueront les coins propices à la présence de composants. Attention, ceci ne signifie pas forcément que des composants seront présents, mais qu\'il y a au moins une chance qu\'il y en ait.
			<br><br></i>';			
			$lock = $db->is_locked($perso_cod);
			if ($lock == true)
			{
				$contenu_page .= '<br><br><b>Vous ne pouvez pas réaliser de cueillette, étant donné que vous êtes locké en combat</b>';
	
			}
			else
			{
				
				$contenu_page .= '<form name="recup_composant" method="post" action="'. $PHP_SELF .'">
														<input type="hidden" name="methode" value="recup">
														<input type="hidden" name="t" value="'. $t .'">
														<table width="70%">
														<td><input type="submit" name="recuperation" value="Cueillette de composants (4 PA)"  class="test"></td>
													</form></table>';
			}
		break;

		case "recup":
		/*vérif si pas de lock de combat*/
		$lock = $db->is_locked($perso_cod);
		$phase = CalcPhase();
		if ($lock == true)
		{
			$contenu_page .= '<br>Vous ne pouvez pas faire de cueillette, étant donné que vous êtes locké en combat';
			$erreur = 1;
		}
		/*Controle pour la compétence alchimie*/
		$alchimiste = $db->existe_competence($perso_cod,97);
		if ($alchimiste != true)
		{
			$contenu_page .= '<br>Vous ne pouvez pas réaliser de cueillette, car vous n\'êtes pas alchimiste ! Ici, il n\'y a que de vulgaires plantes pour vous.';
			$erreur = 1;
		}		
		/*Controle sur les Pas*/
		$req ='select perso_pa from perso where perso_cod = '. $perso_cod;
		$db->query($req);
		if($db->nf() == 0)
			{
				$contenu_page .= '<td><br />Erreur sur le perso concerné<br /><br /></td>';
				$erreur = 1;
			}
		else
			{	
				$db->next_record();
				if ($db->f("perso_pa") < 4)
				{
					$contenu_page .= '<br>Vous n\'avez pas suffisamment de PA pour réaliser cette action.';
					$erreur = 1;
				}
			}
		if ($erreur != 1)
		{
				$position = $db->get_pos($perso_cod);
				$pos_cod = $position['pos_cod'];
				$req = 'select potions.recup_composant('. $perso_cod .','. $pos_cod .','. $phase .') as resultat';
				$db->query($req);
				$db->next_record();
				$contenu_page .= $db->f('resultat');
		}
		break;

	}
	
/*$temp->set_var("contenu_page",$contenu_page);
$temp->parse("Sortie","FileRef");
$temp->p("Sortie");*/
?>
