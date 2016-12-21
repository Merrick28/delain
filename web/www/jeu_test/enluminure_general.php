<?php 
define("APPEL",1);
include_once "verif_connexion.php";
include_once '../includes/template.inc';
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
include_once 'sjoueur.php';
$is_enlumineur = $db->is_enlumineur($perso_cod);
if($is_enlumineur)
{
	$controle = 1;
}
$contenu_page .= '<span align="center"><b>Enluminure</b></span>
(<a href="javascript:blocking(\'aide\');">Aide</a>)<br><br>
	<div id="aide" class="tableau2"  style="display:none;">
	<p><b>Préambule :</b>
		<br>L’art de l’enluminure n’est en rien complexe.
		<br>Cela nécessite simplement de trouver une peau qui pourra ensuite servir de support comme parchemin afin de lancer son sort dessus.
		<br>La difficulté de cet art est que chaque peau possède intrinsecquement une énergie magique, et que c’est cette énergie qui permet d’en faire un support de qualité.
		<br>L’enlumineur doit alors estimer si il sera capable ou non d’en tirer toute la quintescence.
		<br>Il doit alors choisir quel sera son objectif, ainsi en découle la difficulté du tannage de la peau, qui correspond à l’action de transformer cette peau en parchemin propre à une enluminure.
	</p>
</div>
<br>';

$ong[0] = 'Tannage des peaux';
$ong[1] = 'Atelier de séchage des peaux';


if(isset($_POST['tenl']) && !isset($tenl))
	$tenl = $_POST['tenl'];
if(!isset($tenl))
	$tenl = 0;
$nb = count($ong);

//
//
$contenu_page .= '<table cellspacing="0" cellpadding="0" width="100%">
									<tr>';

for($cpt=0;$cpt<$nb;$cpt++)
{
	if($cpt == $tenl)
	{
		$style = 'onglet';
		$lien = '';
		$f_lien = '';
		
	}
	else
	{
		$style = 'pas_onglet';
		$lien = '<a href="' . $PHP_SELF . '?tenl=' . $cpt . '">';
		$f_lien = '</a>';
	}
	$contenu_page .= '<td class="' . $style .'"><p style="text-align:center">' . $lien . $ong[$cpt] . $f_lien . '</p></td>';
}
$contenu_page .= '</tr>
									<tr>
									<td colspan="' . $nb. '" class="reste_onglet">';
switch($tenl)
{
	case "0": //Tannage des peaux
		if($controle == 1)
		{
			$contenu_page .= '<span align="center"><b>Tannage des peaux</b></span>
			(<a href="javascript:blocking(\'aide2\');">Aide sur le tannage</a>)<br><br>
				<div id="aide2" class="tableau2"  style="display:none;">
				<p><b>Le tannage des peaux, pourquoi ?</b>
				<br>Un parchemin n’est pas un simple bout de papier, comme certains pourraient le croire. Il s’agit d’une peau d’un monstre, qui contient déjà une énergie magique. L’opération de tanange consiste à en retirer cette quintessence magique, afin que le parchemin soit ensuite le réceptacle d’un sort.
				<br><br><b>Comment ?</b>
				<br>Il est nécessaire de sélectionner la peau qui sera tannée, et ensuite, de décider quel niveau de sortilège vous voudrez ensuite pouvoir lancer dessus. Plus vous souhaitez lancer des sortilèges complexes dessus, plus vous avez intérêt à avoir une peau de qualité.
				<br>Néanmoins, toute peau est capable théroriquement de produire un parchemin de haut niveau. Certaines avec une chance très faible.
				<br>Dans les faits, il s’avère que certaines peaux ne peuvent pas produire certains résultats, sans que ceci ne soit expliquable par les enlumineurs.
				<br>De plus, cette opération n’est pas immédiate ! Le résultat ne sera disponible qu’après plusieurs jours, là aussi fonction de la peau et du niveau de sortilège que vous souhaitez réaliser.
				<br>Votre compétence en enluminure va elle aussi rentrer en ligne de compte, à la fois pour votre capacité à produire des parchemins de haut niveau, mais aussi dans votre capacité de réalisation.
					</p>
			</div>
			<br>';
			include "enluminure_tannage.php";
			$contenu_page .= '<br><br><a href="' . $PHP_SELF . '?tenl=0">Retour à l’onglet sélectionné</a>';
		}
		else
		{
			$contenu_page .= '<br />Vous n’avez pas les compétences pour réaliser ces opérations';
		}
		break;	
	case "1": //Peau en séchage
		if($controle == 1)
		{
			include "enluminure_peau_sechage.php";
			$contenu_page .= '<br><br><a href="' . $PHP_SELF . '?tenl=1">Retour à l’onglet sélectionné</a>';
		}
		else
		{
			$contenu_page .= '<br />Vous n’avez pas les compétences pour réaliser ces opérations';
		}
		break;
}
$contenu_page .= '</td></tr></table>';


// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
