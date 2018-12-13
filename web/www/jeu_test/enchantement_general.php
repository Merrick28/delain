<?php 
define("APPEL",1);
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

//
//Contenu de la div de droite
//
$contenu_page = '';


// scripts JS
$contenu_page .= '
<script language="javascript">
function blocking(nr)
{
	if (document.layers)
	{
		current = (document.layers[nr].display == \'none\') ? \'block\' : \'none\';
		document.layers[nr].display = current;
	}
	else if (document.all)
	{
		current = (document.all[nr].style.display == \'none\') ? \'block\' : \'none\';
		document.all[nr].style.display = current;
	}
	else if (document.getElementById)
	{ vista = (document.getElementById(nr).style.display == \'none\') ? \'block\' : \'none\';
		document.getElementById(nr).style.display = vista;
	}
}
function retour()
{
parent.gauche.location.href="menu.php";
}
</script>';
$contenu_page .= '
<style type="text/css">
.interieur {
text-align: center;
}
.tableau {
text-align: center;
border-width: 1px;
width:100%;
border-style: solid;
border-color: #000000
}
.tableau2 {
text-align: center;
border-width: 1px;
width:70%;
border-style: solid;
border-color: #000000
}
.onglet {
text-align: center;
border-top: 1px solid #000000;
border-bottom: 0px solid #000000;
border-left: 1px solid #000000;
border-right: 1px solid #000000;
padding:5px;
}
.pasonglet {
background-color: #CCC;
text-align: center;
border-top: 1px solid #000000;
border-bottom: 1px solid #000000;
border-left: 1px solid #000000;
border-right: 1px solid #000000;
padding:5px;
}
.reste_onglet {
text-align: center;
border-top: 0px solid #000000;
border-bottom: 1px solid #000000;
border-left: 1px solid #000000;
border-right: 1px solid #000000;
padding:5px;
height:200px;
}
</style>
';

//
$is_enchanteur = $db->is_enchanteur($perso_cod);
	if($is_enchanteur)
	{
		$controle = 1;
	}
$contenu_page .= '<span align="center"><strong>Enchantement</strong></span>
(<a href="javascript:blocking(\'aide\');">Aide</a>)<br><br>
	<div id="aide" class="tableau2"  style="display:none;">
	<p><strong>Préambule :</strong>
		<br>L\'art du forgeamage est un art difficile à maitriser. 
	</p>
</div>
<br>';

$ong[0] = 'Analyse des flux magiques';
$ong[1] = 'Créer un composant pour Enchanter';
$ong[2] = 'Créer un objet qui pourra être enchanté';
$ong[3] = 'Enchanter un objet';

if(isset($_POST['t_ench']) && !isset($t_ench))
	$t_ench = $_POST['t_ench'];
if(!isset($t_ench))
	$t_ench = 0;
$nb = count($ong);

//
$contenu_page .= '<div class="bordiv">';
//
$contenu_page .= '<table cellspacing="0" cellpadding="0" width="100%">
									<tr>';

	for($cpt=0;$cpt<$nb;$cpt++)
	{
		if($cpt == $t_ench)
		{
			$style = 'onglet';
			$lien = '';
			$f_lien = '';
			
		}
		else
		{
			$style = 'pas_onglet';
			$lien = '<a href="' . $PHP_SELF . '?t_ench=' . $cpt . '">';
			$f_lien = '</a>';
		}
		$contenu_page .= '<td class="' . $style .'"><div style="text-align:center">' . $lien . $ong[$cpt] . $f_lien . '</div></td>';
	}
	$contenu_page .= '</tr>
										<tr>
										<td colspan="' . $nb. '" class="reste_onglet">';
	switch($t_ench)
	{
		case "0": //Analyse des flux magiques
			if($controle == 1)
			{
				$contenu_page .= '<span align="center"><strong>Les flux magiques</strong></span>
				(<a href="javascript:blocking(\'aide2\');">Aide sur les vents magiques</a>)<br><br>
					<div id="aide2" class="tableau2"  style="display:none;">
					<p><strong>A quoi cela sert-il de comprendre les vents magiques ?</strong>
					<br>Les vents magiques ou flux magiques sont la base même du forgeamage. En effet, vous en aurez besoin pour créer des composants, créer un objet enchantable ou enchanter un objet.
					<br><em>Mais que sont-ils ?</em>
					<br>Chaque sort lancé produit de la magie visible, mais aussi de la magie invisible. Celle-ci reste en suspension, et ne se dissipe que lentement.
					<br>Vous pouvez capter cette magie, et l\'enchasser dans des objets. Grâce à elle, vous pourrez aussi transformer une pierre précieuse ou métal précieux en un composant magique, enchasser la magie dans une arme, armure, artéfact ou un casque. Mais aussi mêler les composants entre eux et réaliser cette opération de forgeamage qui rendra votre objet "différent" ...
					<br>
						</p>
				</div>
				<br>';
				include "enchantement_flux.php";
				$contenu_page .= '<br><br><a href="' . $PHP_SELF . '?t_ench=0">Retour à l\'onglet sélectionné</a>';
			}
			else
			{
				$contenu_page .= '<br />Vous n\'avez pas les compétences pour réaliser ces opérations';
			}
			break;	
		case "1": //Créer un composant pour Enchanter
			if($controle == 1)
			{
				$contenu_page .= '<span align="center"><strong>Les composants</strong></span>
				(<a href="javascript:blocking(\'aide2\');">Aide</a>)<br><br>
					<div id="aide2" class="tableau2"  style="display:none;">
					<p><strong>Création de composants :</strong>
					<br>Les composants sont créés à partir des pierres précieuses et minerais.
					<br>Vous devez vous trouver à un endroit où les vents magiques sont suffisants pour pouvoir les emmagasiner dans un composant.
					<br>Malheureusement, cet art est à la fois dangereux et particulièrement aléatoire.
					<br>Votre niveau de forgeamage va vous permettre de créer des composants rentrant dans des enchantements de plus en plus puissants. Mais l\'inconvénient de cet art, c\'est que vous ne pouvez pas choisir le composant que vous allez réaliser, seulement la pierre que vous allez travailler. Chaque pierre peut donc donner des composants différents.
					<br>Vous aurez aussi besoin d\'un niveau d\'énergie minimum. Mais c\'est le composant que vous allez créer qui va déterminer l\'énergie nécessaire. Donc pour créer un composant de niveau 3, il vous faudra la puissance nécessaire, et si vous ne la possédez pas, la création du composant échouera.
						</p>
				</div>
				<br>';
				include "enchantement_composant.php";
				$contenu_page .= '<br><br><a href="' . $PHP_SELF . '?t_ench=1">Retour à l\'onglet sélectionné</a>';
			}
			else
			{
				$contenu_page .= '<br />Vous n\'avez pas les compétences pour réaliser ces opérations';
			}
			break;		
		case "2": // Créer un objet qui pourra être enchanté
			if($controle == 1)
			{
				$contenu_page .= '<span align="center"><strong>Les objets enchantables</strong></span>
				(<a href="javascript:blocking(\'aide2\');">Aide</a>)<br><br>    
					<div id="aide2" class="tableau2"  style="display:none;">
					<p><strong>Rendre un objet enchantable :</strong>
					<br>A la base, aucun objet n\'est enchantable. Chaque objet est une matière inerte, sans aucune propriété.
					<br>L\'art du forgeamage, c\'est justement de pouvoir canaliser les vents magiques, les capturer, les enchasser dans les objets. C\'est cette opération qui permet de transformer les pierres précieuses en d\'autres composants.
					<br>C\'est aussi cette opération qui est réalisée pour permettre à un objet de devenir enchantable par la suite.
					<br>Le forgeamage va prendre le pouls des vents magiques, et tenter de les enchasser dans un objet.
					<br>Mais ceci n\'est pas sans risque, et ce pour deux raisons :
					<ul><li>Les vents magiques sont dangereux à manipuler, et en cas d\'une mauvaise manoeuvre, peuvent engendrer des inconvénients notables (perte de PV, d\'orientation, de vue ...) mais non permanents (sauf en cas d\'une action engeandrant une perte de PV trop importante ...)
					<br>Bien entendu, meilleur sera l\'enchanteur, moins le risque est important, mais il subsistera toujours.</li>
					<li>L\'objet qui va capturer les vents magiques doit être de la meilleure qualité possible. Tout est enchantable, mais une épée de morbelin sera plus difficilement enchantable qu\'une épée de qualité.
					<br>L\'autre point est que l\'objet peut ne pas supporter l\'enchantement, et donc se briser. Personne ne pourra prédire le résultat. Un enchanteur peut seulement estimer le risque pris.
					<br>Ne pas arriver à l\'enchanter la première fois ne veut pas dire que cela n\'est pas possible, mais qu\'il faut sans doute réitérer la démarche, et donc la prise de risque.</li>
						</p>
				</div>
				<br>';
				include "enchantement_objet.php";
			$contenu_page .= '<br><br><a href="' . $PHP_SELF . '?t_ench='. $t_ench .'">Retour à l\'onglet sélectionné</a>';
			}
			else
			{
				$contenu_page .= '<br />Vous n\'avez pas les compétences pour réaliser ces opérations';
			}
		break;
		case "3": // Enchanter un objet
			if($controle == 1)
			{
				$contenu_page .= '<span align="center"><strong>Aide pour enchanter un objet</strong></span>
				(<a href="javascript:blocking(\'aide3\');">Comment enchanter un objet</a>)<br><br>
				<div id="aide3" class="tableau2"  style="display:none;">
				<p>Rien de plus simple ... Vous avez besoin des composants qui composent une formule de forgeamage, et hop, l\'enchantement pourra être tenté. 
				<br>Ca c\'est la partie facile ... Reste donc maintenant à avoir les bons composants ... et à les associer.
				<br>En effet, votre énergie sera suffisante pour savoir, à partir des composants que vous aurez en votre possession, s\'ils peuvent être associés ou non sur l\'objet enchantable choisi.
				<br>En revanche, avec un seul composant, vous ne saurez pas a priori avec quels autres l\'associer. Nul doute que la communication avec d\'autres aventuriers vous aidera dans cette entreprise pour connaitre les combinaisons possibles ...
					</p>
				</div>
				<br>';
				include "enchantement_objet_enchante.php";
				$contenu_page .= '<br><br><a href="' . $PHP_SELF . '?t_ench='. $t_ench .'">Retour à l\'onglet sélectionné</a>';	
			}
			else
			{
				$contenu_page .= '<br />Vous n\'avez pas les compétences pour réaliser ces opérations';
			}
		break;
		
	}
	$contenu_page .= '</td></tr></table>';
//
//
//
$contenu_page .= '</div>';
//

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');