<?php 

include "../verif_connexion.php";


$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.
$db        = new base_delain;
$req_matos = "select perobj_obj_cod from perso_objets,objets "
. "where perobj_obj_cod = obj_cod and perobj_obj_cod = $objet and perobj_perso_cod = $perso_cod and obj_gobj_cod in (640) order by perobj_obj_cod";
$db->query($req_matos);
if (!($db->next_record()))
{
  // PAS D'OBJET.
    $contenu_page .= "<p>Vous avez beau chercher, il n'y a aucune nourriture dans votre sac</p>";
} else {
    $num_obj = $db->f("perobj_obj_cod");
  //echo "OBJ=".$num_obj;
    // TRAITEMENT DES ACTIONS.
    //echo $objet;
    if ($objet == null)
        $objet = isset($_POST['objet'])?$_POST['objet']: "-1";
    if(isset($_POST['methode'])){
        $req_pa = "select perso_pa,perso_nom from perso where perso_cod = $perso_cod";
        $db->query($req_pa);
        $db->next_record();
        if ($db->f("perso_pa") < 4)
        {
            $contenu_page .= '<p><strong>Vous n\'avez pas assez de PA !</strong></p>';
        }
        else
        {
            // ON ENLEVE LES PAs
            $req_enl_pa = "update perso set
                perso_pa = perso_pa - 4,
                perso_pv = min(perso_pv + 4, perso_pv_max)
                where perso_cod = $perso_cod";
            $db->query($req_enl_pa);
            $contenu_page .= '<p><strong>Vous faites un festin et gagnez quelques points de vie... Attention à votre régime.</strong></p>';
            $db->query('select lancer_des(1,100) as reussite');
            $db->next_record();
            $reussite = $db->f('reussite');
            if ($reussite >= 96)
            {
                $contenu_page .= '<p><strong>Votre charisme vient d\'en prendre un coup... Il est temps de vous remettre au sport !</strong></p>';
                // On rajoute une bouée disgrâcieuse.
                $req_cree_bouee = "select cree_objet_perso_equipe(640, $perso_cod), f_del_objet($objet)";
                $db->query($req_cree_bouee);
            }
        }
    }
	$contenu_page .= '<p align="center"><br>
		<p>Un peu de nourriture n’a jamais fait de mal, je suppose... </p>
		<form method="post" action="digestion_difficile.php">
		<input typeURL="hidden" name="methode" value="manger">
		<input type="submit" value="Manger (4PA)"  class="test">
		<input type="hidden" name="objet" value="' . $objet . '" />
		</form>
		</p>';
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));