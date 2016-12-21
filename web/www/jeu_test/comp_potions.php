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
</script>';
// contenu de la page
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

$contenu_page .= '<span align="center"><b>Alchimie</b></span>
(<a href="javascript:blocking(\'aide\');">Aide</a>)<br><br>
	<div id="aide" class="tableau2"  style="display:none;">
	<p><b>Préambule :</b>
		<br>L’art des potions est un art difficile à maitriser. Certains pensent pouvoir mélanger tout et n’importe quoi, mais une vraie science se cache derrière.<bt>C’est pourquoi la majorité pense pouvoir utiliser sans risque les potions, voir en concocter, mais seuls les vrais <b>alchimistes</b> peuvent réaliser des trésors avec leurs mains.</p>
	<p><b>Utilisation d’une potion :</b>
	<br>Une potion s’utilise très simplement, il suffit de l’ingurgiter.
	<br>Quelques risques subsitent : les potions sont vivantes, elles peuvent se transformer au cours du temps. On parle alors d’instabilité. Dans ce cas, la potion devient néfaste, et l’effet produit n’est pas celui attendu.
	<br>D’autre part, il peut s’avérer dangereux de recourir à trop de potions en même temps. Certains phénomènes de toxicité peuvent apparaître. Un avertissement vous le rappelera.</p>
	<p><b>Composition de potions	</b>
	<br>Pour composer une potion vous devez disposer d’<b>ingrédients</b> et d’un <b>flacon</b> et maîtriser la compétence <b>Alchimie</b>.
	<br>La composition d’une potion se déroule en deux étapes :
	<br> <b>- Mélange des composants :</b> ceci n’est pas couteux en temps, peu risqué, mais parfois, on peut avoir des surprises.
		<br>Chaque composant est alors supprimé, pour être introduit soit dans un flacon vide, soit dans un mélange déjà existant.
		<br>Autant dire qu’une fois mélangé, un composant est considéré comme perdu en tant que tel.
	<br><b> - Finalisation :</b> Opération finale pour faire prendre le mélange, cela peut s’assimiler à une opération de cuisson, de mélange ...
		<br> Dans le cadre de potion déjà connue, on peut se passer des étapes de sélection des composants pour obtenir directement le mélange souhaité.</p>
	<p><b>Recherche de composants :</b>
		<br>Plusieurs options de recherche sont possibles, moyennant des coûts en PA différents.</p>
	<p><b>Mélange de deux potions :</b>
		<br>Pour l’instant, les plus grands savants n’ont pas encore réussi à comprendre le mécanisme sous-jacent.</p>
</div>
<br>';

// Suppression de la liste des potions ; cf inventaire.
//$ong[0] = 'Utilisation d’une potion';
$ong[1] = 'Recherche d’ingrédients';
$ong[2] = 'Cueillette des ingrédients';
$ong[3] = 'Fabrication de potions';
$ong[4] = 'Potions connues';
$ong[5] = 'Mélange de potions';

if(isset($_POST['tpot']) && !isset($tpot))
	$tpot = $_POST['tpot'];
if(!isset($tpot))
	$tpot = 1;
$nb = count($ong);


$contenu_page .= '
	<table cellspacing="0" cellpadding="0" width="100%">
	<tr>';

for($cpt = 1; $cpt < $nb; $cpt++)
{
	if($cpt == $tpot)
	{
		$style = 'onglet';
		$lien = '';
		$f_lien = '';
		
	}
	else
	{
		$style = 'pas_onglet';
		$lien = '<a href="' . $PHP_SELF . '?tpot=' . $cpt . '">';
		$f_lien = '</a>';
	}
	$contenu_page .= '<td class="' . $style .'"><div style="text-align:center">' . $lien . $ong[$cpt] . $f_lien . '</div></td>';
}
$contenu_page .= '
	</tr>
	<tr>
	<td colspan="' . $nb. '" class="reste_onglet">';
$req_comp = "select pcomp_modificateur from perso_competences 
			where pcomp_perso_cod = $perso_cod 
				and pcomp_pcomp_cod in (97,100,101);";
$db->query($req_comp);
if($db->nf() != 0)
{
	$controle = 1;
}
switch($tpot)
{
	case "1": // Recherche d’ingrédients
		if($controle == 1)
		{
			$contenu_page .= '<span align="center"><b>Les composants</b></span>
				(<a href="javascript:blocking(\'aide2\');">Aide</a>)<br><br>
				<div id="aide2" class="tableau2"  style="display:none;">
				<p><b>Recherche de composants :</b>
					<br>La recherche de composants est un préalable indispensable à la cueillette des composants. 
					<br>En effet, vous seriez comme un sourcier sans sa baguette, aveugle à ce qui pourrait être dans les parages.
					<br>Ceci vous permettra de cartographier les environs. Vous aurez ainsi la vision des composants potentiellement présents.
					<br>En revanche, vous n’aurez pas une idée précise des composants réellement présents.
					<br><br>Plusieurs méthodes vous seront proposés. 
					<br><ul><li>La première ne coutant aucun PA, ne permet que de regarder sur la case sur laquelle vous êtes.</li>
					<br><li>La seconde donne une vision panoramique des alentours. Son coût en PA est diminué à partir du niveau 2 d’alchimie</li>
					<br><li>Une troisième est disponible lorsque vous atteignez le niveau 3 de l’alchimie, qui vous permet alors d’analyser encore une surface plus importante.	</li>
					</ul> </p>
				</div>
				<br>';
			include "potions_detect_composants.php";
			$contenu_page .= '<br><a href="' . $PHP_SELF . '?tpot='. $tpot .'"><br>Retour à l’onglet sélectionné</a>';
		}
		else
		{
			$contenu_page .= '<br />Vous n’avez pas les compétences pour réaliser ces opérations';
		}
	break;
	case "2": // Cueillette des composants
		if($controle == 1)
		{
			$contenu_page .= '<span align="center"><b>Cueillir des composants ?</b></span>
			(<a href="javascript:blocking(\'aide3\');">Aide</a>)<br><br>
				<div id="aide3" class="tableau2"  style="display:none;">
				<p><b>Après les detecter, il faut les cueillir :</b>
					<br>Une fois que vous savez que le coin est propice à la cueillette, il faut alors se lancer dans l’opération proprement dite.
					<br>Pour cela, vous devez faire attention à un point très important : les Cycles lunaires.
					<br>En fonction du cycle, les composants sont plus ou moins faciles à récupérer.
					<br>Les deux périodes les plus propices sont les nouvelles lunes et pleines lunes. Vous aurez un maximum de chance de récupérer beaucoup de composants. Néanmoins, ne vous acharnez pas ! Si il n’y a plus de composants à un endroit, la lune ne les inventera pas !
					<br>Deux autres éléments viennent interférer sur votre capacité à cueillir rapidement des composants : l’intelligence et la dextérité. Mais vous n’y pouvez pas grand chose à court terme
					<br><br>Enfin, impossible de cueillir des composants en plein combats ! Vous êtes bien trop occupés à éviter les coups !
					</p>
				</div>
				<br>';
			include "potions_recup_composant.php";
			$contenu_page .= '<br><a href="' . $PHP_SELF . '?tpot='. $tpot .'">Retour à l’onglet sélectionné</a>';
		}
		else
		{
			$contenu_page .= '<br />Vous n’avez pas les compétences pour réaliser ces opérations';
		}
	break;
	case "3": // Fabrication de potions
		if($controle == 1)
		{
			$contenu_page .= '<span align="center"><b>Fabriquer une potion</b></span>
			(<a href="javascript:blocking(\'aide4\');">Aide</a>)<br><br>
				<div id="aide4" class="tableau2"  style="display:none;">
				<p><b>Et que faire de tous ces composants ??</b>
					<br>Les mélanger bien sûr !
					<br>Première nécessité, posséder un flacon vide. Vous aller dans un premier temps mettre les ingrédients qui constitueront votre potion dedans. Ceci ne consomme rien du tout, si ce n’est le composant.
					<br><b>Une fois un ingrédient dans un flacon, il ne peut plus être récupéré !</b>
					<br>Une fois que vous pensez que votre potion est en voie de finalisation, vous allez devoir réaliser la préparation pour obtenir une potion finie.
					<br>Ceci est caractérisé par un temps en PA uniquement.
					<br><br>Mais dans ce cas, comment connaître les formules des potions ??
					<br>En voilà une bonne question ! Ceci est un art complexe qui ne se diffuse que par le bouche à oreille, ou en récupérant des indices. N’hésitez pas à les chercher ou à communiquer pour les trouver !
					<br>Ce que vous savez en tant qu’alchimiste :
					<br><ul><li>Une potion nécessite toujours une et une seule once de composant de base. Cela peut être la léonide sucrée, le pissenlit de vin ou herbe de lune.</li>
					<br><li>Il existe trois niveaux de potions, pour lequel vous devez être en adéquation en terme de niveau d’alchimie</li>
					<br><li>Une potion de niveau 1 comporte forcément a minima quatre onces de composants en plus de son composant de base. Exemples : Base + ABCD</li>
					<br><li>Une potion de niveau 2 comporte forcément a minima cinq onces de composants en plus de son composant de base. De plus, un composant est forcément représenté par deux onces minimum. Exemples : Base + AABCD,Base + AABCDE, Base + AAABC</li>
					<br><li>Une potion de niveau 3 comporte forcément a minima sept onces de composants en plus de son composant de base. De plus, deux composants sont forcément représentés par deux onces minimum. Exemples : Base + AABBCDE,Base + AABBCDEF, Base + AAABBCD</li>
					</ul>
					</p>
				</div>
				<br>';

			include "potions_composition.php";
			$contenu_page .= '<br><a href="' . $PHP_SELF . '?tpot='. $tpot .'">Retour à l’onglet sélectionné</a>';
		}
		else
		{
			$contenu_page .= '<br />Vous n’avez pas les compétences pour réaliser ces opérations';
		}
	break;
	case "4": // Potions connues
		if($controle == 1)
		{
			$contenu_page .= '<span align="center"><b>Je connais la formule !</b></span>
				(<a href="javascript:blocking(\'aide5\');">Aide</a>)<br><br>
				<div id="aide5" class="tableau2"  style="display:none;">
				<p><b>Que faire si je connais la formule ?</b>
				<br>Dès que vous aurez découvert une formule, plus besoin de vous poser de question ! Vous pourrez directement la composer, <b>si tant est que vous avez les composants nécessaires !</b>
				</p>
				</div>
				<br>';
			include "potions_connues.php";
			$contenu_page .= '<br><a href="' . $PHP_SELF . '?tpot='. $tpot .'">Retour à l’onglet sélectionné</a>';
		}
		else
		{
			$contenu_page .= '<br />Vous n’avez pas les compétences pour réaliser ces opérations';
		}
	break;
	case "5": // Mélange de potions
		$contenu_page .= '<br />À venir';
	break;
}
$contenu_page .= '</td></tr></table>';
//
//
//
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//
//
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
