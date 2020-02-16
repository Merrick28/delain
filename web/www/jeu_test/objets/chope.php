<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;


$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.

$req_matos = "select perobj_obj_cod from perso_objets,objets "
             . "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 409 ";
$stmt      = $pdo->query($req_matos);
if (!($result = $stmt->fetch()))
{
  // PAS D'OBJET.
 	$contenu_page .= "<p>Hélas... aucune choppe pleine ne se trouve dans votre inventaire !</p>";
} else {
    $num_obj = $result['perobj_obj_cod'];
  // TRAITEMENT DES ACTIONS.
	if(isset($_POST['methode'])){
		$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
        $stmt = $pdo->query($req_pa);
        $result = $stmt->fetch();
        if ($result['perso_pa'] < 1)
		{
			$contenu_page .= '<p><strong>Vous n’avez pas assez de PA !</strong></p>';
		}
		else
		{
      // ON ENLEVE LES PAs
			$req_enl_pa = "update perso set perso_pa = perso_pa - 1 where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_enl_pa);

			// ON SUPPRIME L'OBJET.
			$req_supr_obj = "select  f_del_objet($num_obj)";
            $stmt = $pdo->query($req_supr_obj);
			// ON CREE LA CHOPPE VIDE
			$req_supr_obj = "select  cree_objet_perso(410,$perso_cod)";
            $stmt = $pdo->query($req_supr_obj);

			//INSERTION DU BONUS
			$req_bonus = 'select ajoute_bonus(' . $perso_cod . ',\'ALC\',2, 0.2 + valeur_bonus(' . $perso_cod . ', \'ALC\'))';
            $stmt = $pdo->query($req_bonus);

            // INSERTION DE L'EVENT
            $req_bonus = "insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)".
                        "values(69,now(),$perso_cod,'	[perso_cod1] a bu une chope de bière.','O','O')";
            $stmt = $pdo->query($req_bonus);

			$contenu_page .= '<p><strong>Vous descendez le verre d’un trait, quel délice !</strong></p>';
		}
	}  else { //Not isset ('methode')
		$contenu_page .= '<p align="center">
			Une mousse légère couvre cette boisson aux reflets de miel... non vous ne rêvez pas c’est bien le breuvage des dieux !<br>

			<form method="post" action="chope.php">
			<input type="hidden" name="methode" value="boire">
			<input type="submit" value="Boire (1PA)"  class="test">
			</form>
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