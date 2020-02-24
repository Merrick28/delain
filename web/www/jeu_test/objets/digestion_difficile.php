<?php

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = $verif_connexion->perso;

$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.

$req_matos = "select perobj_obj_cod from perso_objets,objets "
             . "where perobj_obj_cod = obj_cod and perobj_obj_cod = $objet and perobj_perso_cod = $perso_cod and obj_gobj_cod in (640) order by perobj_obj_cod";
$stmt      = $pdo->query($req_matos);
if (!($result = $stmt->fetch()))
{
    // PAS D'OBJET.
    $contenu_page .= "<p>Vous avez beau chercher, il n'y a aucune nourriture dans votre sac</p>";
} else
{
    $num_obj = $result['perobj_obj_cod'];
    //echo "OBJ=".$num_obj;
    // TRAITEMENT DES ACTIONS.
    //echo $objet;
    if ($objet == null)
    {
        $objet = get_request_var('objet', -1);
    }

    if (isset($_POST['methode']))
    {

        if ($perso->perso_pa < 4)
        {
            $contenu_page .= '<p><strong>Vous n\'avez pas assez de PA !</strong></p>';
        } else
        {
            // ON ENLEVE LES PAs
            $perso->perso_pa = $perso_pa - 4;
            $perso->perso_pv = min($perso->perso_pv + 4, $perso->perso_pv_max);
            $perso->stocke();


            $contenu_page .= '<p><strong>Vous faites un festin et gagnez quelques points de vie... Attention à votre régime.</strong></p>';
            $stmt         = $pdo->query('select lancer_des(1,100) as reussite');
            $result       = $stmt->fetch();
            $reussite     = $result['reussite'];
            if ($reussite >= 96)
            {
                $contenu_page .= '<p><strong>Votre charisme vient d\'en prendre un coup... Il est temps de vous remettre au sport !</strong></p>';
                // On rajoute une bouée disgrâcieuse.
                $req_cree_bouee = "select cree_objet_perso_equipe(640, $perso_cod), f_del_objet($objet)";
                $stmt           = $pdo->query($req_cree_bouee);
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

    'CONTENU_PAGE' => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));