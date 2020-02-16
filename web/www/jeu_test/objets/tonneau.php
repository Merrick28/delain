<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.

$req_matos = "select perobj_obj_cod from perso_objets,objets "
             . "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 196 ";
$stmt      = $pdo->query($req_matos);

$test_objet = false;
if($result = $stmt->fetch())
{
	$test_objet = true;
} else {
	// On vérifie si il y a un tonneau sur la case
	$req_matos = "select obj_cod, obj_nom, obj_gobj_cod from objets obj
join objet_position opos on (opos.pobj_obj_cod = obj.obj_cod)
join perso_position ppos on (opos.pobj_pos_cod = ppos.ppos_pos_cod)
where ppos.ppos_perso_cod = $perso_cod
and obj.obj_gobj_cod = 196 ";
    $stmt = $pdo->query($req_matos);
    if($result = $stmt->fetch())
    {
		$test_objet = true;
	}
}



if(!$test_objet){
  // PAS D'OBJET.
 	$contenu_page .= "<p>Hélas... aucun tonneau dans votre inventaire !</p>";
} else {
    $num_obj = $result['perobj_obj_cod'];
  // TRAITEMENT DES ACTIONS.
	if(isset($_POST['methode'])){
		$erreur = 0;
		$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
        $stmt = $pdo->query($req_pa);
        $result = $stmt->fetch();
        if ($result['perso_pa'] < 1)
		{
			$contenu_page .= '<p><strong>Vous n’avez pas assez de PA !</strong></p>';
			$erreur = 1;
		}
		$req_matos = "select perobj_obj_cod from perso_objets,objets "
      . "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 410 ";
        $stmt = $pdo->query($req_matos);
        if (!($result = $stmt->fetch()))
        {
		  	$contenu_page .= '<p><strong>Vous n’avez pas de chope vide !</strong></p>';
			$erreur = 1;
		}
		if($erreur == 0)
		{
            $num_obj_chope = $result['perobj_obj_cod'];
			// ON ENLEVE LES PAs
			$req_enl_pa = "update perso set perso_pa = perso_pa - 1 where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_enl_pa);

			// ON SUPPRIME L'OBJET.
			$req_supr_obj = "select  f_del_objet($num_obj_chope)";
            $stmt = $pdo->query($req_supr_obj);
			// ON CREE LA CHOPPE VIDE
			$req_supr_obj = "select  cree_objet_perso(409,$perso_cod)";
            $stmt = $pdo->query($req_supr_obj);

			$contenu_page .= '<p><strong>La chope est maintenant pleine de bière !</strong></p>';
		}
	}  else {
		$contenu_page .= '';

		$req_matos = "select perobj_obj_cod from perso_objets,objets "
			. "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 410 ";
        $stmt = $pdo->query($req_matos);
        if (!($result = $stmt->fetch()))
        {
			$contenu_page .= '<p align="center">Vous n’avez pas de choppe vide, tout ce que vous pouvez faire c’est apporter ce tonneau à un aubergiste en manque d’approvisionnement.</p>';
		} else {
			$contenu_page .= '<p align="center">Vous pouvez remplir la choppe vide avec le contenu d’un tonneau !</p>
				<br><br>
				<form method="post" action="tonneau.php">
				<input type="hidden" name="methode" value="rempir">
				<input type="submit" value="Remplir la chope (1PA)"  class="test">
				</form>';
		}
	}
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');


$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
