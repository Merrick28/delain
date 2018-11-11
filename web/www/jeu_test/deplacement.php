<?php 
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

$resultat_deplacement = '';
if (isset($_POST['methode']) && $_POST['methode'] == 'deplacement')
{
	$erreur = false;
	/* On se déplace */
	$req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
	$db->query($req);
	$db->next_record();
	if ($db->f('perso_type_perso') == 3)
	{
		$resultat_deplacement .= '<p>Erreur ! Un familier ne peut pas se déplacer seul !</p>';
		$erreur = true;
	}
	$position = '';
	if(isset($_POST['position']))
		$position = $_POST['position'];
	if(isset($_GET['position']))
		$position = $_GET['position'];
	if (!isset($position) || $position === '')
	{
		$resultat_deplacement .= '<p>Erreur ! Position non définie !</p>';
		$erreur = true;
	}
	if (!$erreur)
	{
		$req_deplace = 'select deplace_code(' . $perso_cod . ',' . $position . ') as deplace';
		$db->query($req_deplace);
		$db->next_record();
		$result = explode('#',$db->f('deplace'));
		$page_retour = 'frame_vue.php';

		$resultat_deplacement .= $result[1];

		if (strpos($result[1], 'Erreur') !== 0)
		{
			$is_phrase = rand(1,100);
			if ($is_phrase < 34)
			{
				$req = 'select choix_rumeur() as rumeur ';
				$db->query($req);
				$db->next_record();
				$resultat_deplacement .= '<hr /><p><i>Rumeur :</i> '. $db->f('rumeur'). '</p>';
			}
			else if ($is_phrase < 67)
			{
				include 'phrase.php';
				$idx_phrase = rand(1, sizeof($phrase));
				$resultat_deplacement .= '<hr /><p><i>' . $phrase[$idx_phrase] . '</i></p>';
			}
			else
			{
				include 'phrase_indice.php';
				$idx_phrase2 = rand(1, sizeof($phrase_indice));
				$resultat_deplacement .= '<hr /><p>Sur le sol est gravé un indice qui pourrait être fort utile : <br /><i>' . $phrase_indice[$idx_phrase2] . '</i></p>';
			}
		}
	}
}

ob_start();
if (!isset($position))
{
	$position = -1;
}
$req_position_actuelle = "select pos_cod,pos_x,pos_y,perso_pa,pos_etage,perso_nom, vue_pre(perso_cod) as vue_pre from perso_position,positions,perso ";
$req_position_actuelle = $req_position_actuelle . "where ppos_perso_cod = $perso_cod ";
$req_position_actuelle = $req_position_actuelle . "and ppos_pos_cod = pos_cod ";
$req_position_actuelle = $req_position_actuelle . "and perso_cod = $perso_cod";
$db->query($req_position_actuelle);
$db->next_record();

$x = $db->f("pos_x");
$y = $db->f("pos_y");

// construction sous-requête
$temp_table = '';
for ($ix = $x - 1; $ix <= $x + 1; $ix++)
	for ($iy = $y - 1; $iy <= $y + 1; $iy++)
	{
		$temp_table .= "select $ix as temp_x, $iy as temp_y ";
		if ($ix != $x + 1 || $iy != $y + 1)
			$temp_table .= " UNION ALL ";
	}

$etage = $db->f("pos_etage");
$position_actuelle = $db->f("pos_cod");
$vue = $db->f('vue_pre');
$nom = $db->f('perso_nom');
?>
	
	<script language="Javascript" src="../scripts/ajax.js"></script>
	<script src="<?php $type_flux.G_URL; ?>/js/jquery.js"></script>
	<script src="../scripts/ajax2.js?v20181111" type="text/javascript"></script>
	<div style="float:left;" class="bordiv"><?php  echo $vue ?></div>
<?php if ($db->f("perso_pa") >= $db->get_pa_dep($perso_cod))
{ 
?>
	<form name="deplacement" method="post" action="deplacement.php">
	<input type="hidden" name="methode" value="deplacement">
    <input type="hidden" name="menu_deplacement" value="1">
	<table>
	<tr>
	<td></td>
	<?php 
	echo("<td class=\"soustitre2\"><p>X = " . ($x - 1) . "</p></td>\n");
	echo("<td class=\"soustitre2\"><p>X = $x</p></td>\n");
	echo("<td class=\"soustitre2\"><p>X = " . ($x + 1) . "</p></td>\n");
	?>
	</tr>
	<?php 
	
	$req_alentours = "select coalesce(pos_cod, -1) as pos_cod, temp_x, temp_y, coalesce(mur_pos_cod, -1) as mur from 
		($temp_table) t
		left outer join positions on pos_x = temp_x AND pos_y = temp_y AND pos_etage = $etage
		left outer join murs on mur_pos_cod = pos_cod
		order by pos_y desc, pos_x asc";

	$db->query($req_alentours);
	$num_ligne = 0;
	
	while ($db->next_record())
	{
		if ($db->f('temp_x') == $x - 1) // première case
		{
			echo '<tr>';
			echo '<td class="soustitre2"><p>Y = ' . $db->f('temp_y') . '</p></td>';
		}
		echo '<td>';
		if ($db->f("mur") == -1 && $db->f('pos_cod') > 0)
		{
			if ($db->f("pos_cod") != $position_actuelle)
				echo "<input type='radio' name='position' value='" . $db->f("pos_cod") . "'>";
			else
				echo $perso_nom;
		}
		echo '</td>';
		if ($db->f('temp_x') == $x + 1) // dernière case
			echo '</tr>';
	}
	?>
	<tr>
	<td colspan="4"><center><input type="submit" class="test" value="Bouger !!"></center></td></tr>
	</table>
	</form>
<?php }
else
{
	echo "<p>Vous n’avez pas assez de PA pour vous déplacer.</p>";
}

// fin tableau principal
$is_attaque = 0;
$portee = 0;

if ($resultat_deplacement != '')
{
	echo '<div class="bordiv">';
	echo $resultat_deplacement;
	echo '</div>';
}

echo '<div id="vue_bas" class="bordiv">';
include("include_tableau2.php");
echo '</div>';

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>


