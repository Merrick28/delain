<?php 
include "../verif_connexion.php";


$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.
$bd=new base_delain;
$req_matos = "select perobj_obj_cod
	from perso_objets
	inner join objets on obj_cod = perobj_obj_cod
	inner join objet_generique on gobj_cod = obj_gobj_cod
	where perobj_perso_cod = $perso_cod
		and gobj_tobj_cod = 15
		and perobj_equipe = 'O'";
$bd->query($req_matos);
if(!($bd->next_record()))
{
	// PAS D'OBJET.
	$contenu_page .= "<p>Hélas... Vous n’avez équippé aucun instrument !</p>";
} else {
	$num_obj =   $bd->f("perobj_obj_cod");
  // TRAITEMENT DES ACTIONS.
	if(isset($_POST['methode'])){
		$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
		$bd->query($req_pa);
		$bd->next_record();
		if ($bd->f("perso_pa") < 2)
		{
			$contenu_page .= '<p><strong>Vous n’avez pas assez de PA !</strong></p>';
		}
		else
		{
			// ON ENLEVE LES PAs
			$req_enl_pa = "update perso set perso_pa = perso_pa - 2 where perso_cod = $perso_cod";
			$bd->query($req_enl_pa);
			
			$code_evt = 0;
			$texte_evt = '';
			switch ($_POST['style'])
			{
				case 'complainte':
					$code_evt = 70;
					$texte_evt = '[perso_cod1] a chantonné une complainte romantique.';
				break;
				case 'valse':
					$code_evt = 71;
					$texte_evt = '[perso_cod1] a interprété une valse classique.';
				break;
				case 'marche':
					$code_evt = 72;
					$texte_evt = '[perso_cod1] a entonné une marche militaire.';
				break;
				case 'berceuse':
					$code_evt = 73;
					$texte_evt = '[perso_cod1] a fredonné une berceuse mélancolique.';
				break;
				case 'paillarde':
					$code_evt = 74;
					$texte_evt = '[perso_cod1] a braillé une chanson paillarde.';
				break;
				case 'religieux':
					$code_evt = 90;
					$texte_evt = '[perso_cod1] a psalmodié un chant religieux.';
				break;
			}

			if ($code_evt > 0)
			{
				// On regarde où le chant a été réalisé.
				$req_pos = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod";
				$bd->query($req_pos);
				$bd->next_record();
				$position = $bd->f('ppos_pos_cod');
				$req_chant = "select insere_evenement($perso_cod, $perso_cod, $code_evt, '$texte_evt', 'O', '[pos_cod]=$position')";
				$bd->query($req_chant);
			}
			$contenu_page .= '<p><strong>Une interprétation émouvante, mais encore quelques progrès à faire avant de collectioner les fans.</strong></p>';
		}
	} else {
		$contenu_page .= '
			<p align="center"><br><br>
			Un magnifique instrument de musique, voilà qui donne envie de jouer quelques notes !<br><br>


			<form method="post" action="instrument.php">
				<input type="hidden" name="methode" value="jouer">
				<input type="hidden" name="style" value="complainte">
				<p><input type="submit" value="Complainte romantique (2PA)"  class="test"></p>
			</form>

			<form method="post" action="instrument.php">
				<input type="hidden" name="methode" value="jouer">
				<input type="hidden" name="style" value="valse">
				<p><input type="submit" value="Valse classique (2PA)"  class="test"></p>
			</form>

			<form method="post" action="instrument.php">
				<input type="hidden" name="methode" value="jouer">
				<input type="hidden" name="style" value="marche">
				<p><input type="submit" value="Marche militaire (2PA)"  class="test"></p>
			</form>

			<form method="post" action="instrument.php">
				<input type="hidden" name="methode" value="jouer">
				<input type="hidden" name="style" value="paillarde">
				<p><input type="submit" value="Chanson paillarde (2PA)"  class="test"></p>
			</form>

			<form method="post" action="instrument.php">
				<input type="hidden" name="methode" value="jouer">
				<input type="hidden" name="style" value="berceuse">
				<p><input type="submit" value="Berceuse mélancolique (2PA)"  class="test"></p>
			</form>

			<form method="post" action="instrument.php">
				<input type="hidden" name="methode" value="jouer">
				<input type="hidden" name="style" value="religieux">
				<p><input type="submit" value="Chant religieux (2PA)"  class="test"></p>
			</form>

			<font size="small">Remarque : ces actions sont disponibles juste pour le fun, pas la peine de chercher le moindre effet pour le moment...</font>
			</p>';
	}
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));