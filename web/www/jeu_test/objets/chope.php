<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = $verif_connexion->perso;

$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.

$req_matos = "select perobj_obj_cod from perso_objets,objets "
             . "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 409 ";
$stmt      = $pdo->query($req_matos);
if (!($result = $stmt->fetch()))
{
    // PAS D'OBJET.
    $contenu_page .= "<p>Hélas... aucune choppe pleine ne se trouve dans votre inventaire !</p>";
} else
{
    $num_obj = $result['perobj_obj_cod'];
    // TRAITEMENT DES ACTIONS.
    if (isset($_POST['methode']))
    {

        if ($perso->perso_pa < 1)
        {
            $contenu_page .= '<p><strong>Vous n’avez pas assez de PA !</strong></p>';
        } else
        {
            // ON ENLEVE LES PAs
            $perso->perso_pa = $perso->perso_pa - 1;
            $perso->stocke();

            // ON SUPPRIME L'OBJET.
            $req_supr_obj = "select  f_del_objet($num_obj)";
            $stmt         = $pdo->query($req_supr_obj);
            // ON CREE LA CHOPPE VIDE
            $req_supr_obj = "select  cree_objet_perso(410,$perso_cod)";
            $stmt         = $pdo->query($req_supr_obj);

            //INSERTION DU BONUS
            $req_bonus =
                'select ajoute_bonus(' . $perso_cod . ',\'ALC\',2, 0.2 + valeur_bonus(' . $perso_cod . ', \'ALC\'))';
            $stmt      = $pdo->query($req_bonus);

            // INSERTION DE L'EVENT
            $levt                  = new ligne_evt();
            $levt->levt_tevt_cod   = 69;
            $levt->levt_perso_cod1 = $perso_cod;
            $levt->levt_texte      = '[perso_cod1] a bu une chope de bière';
            $levt->levt_lu         = 'O';
            $levt->levt_visible    = 'O';
            $levt->stocke(true);

            $contenu_page .= '<p><strong>Vous descendez le verre d’un trait, quel délice !</strong></p>';
        }
    } else
    { //Not isset ('methode')
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

    'CONTENU_PAGE' => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));