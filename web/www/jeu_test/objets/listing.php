<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = $verif_connexion->perso;


$contenu_page = '';

// ON VERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.

$req_matos = "select perobj_obj_cod from perso_objets,objets "
             . "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 248 ";
$stmt      = $pdo->query($req_matos);
if (!($result = $stmt->fetch()))
{
  // PAS D'OBJET.
 	$contenu_page .= "<p>Une liste ? où ça une liste ? Je n'ai pas vu de liste...</p>";
} else {
    $num_obj = $result['perobj_obj_cod'];
  // TRAITEMENT DES ACTIONS.
	if(isset($_POST['methode']))
    {


        if ($perso->perso_pa < 4)
        {
            $contenu_page .= '<p><strong>Vous n’avez pas assez de PA !</strong></p>';
        } else
        {
            $intel = $perso->perso_int;
            // ON ENLEVE LES PAs
            $perso->perso_pa = $perso_pa - 4;
            $perso->stocke();

            if ($intel < 19)
            {
                //INSERTION DU MALUS de vue
                $req_bonus = 'select ajoute_bonus(' . $perso_cod . ',\'VUE\',2,-1)';
                $stmt      = $pdo->query($req_bonus);
                //INSERTION DU MALUS de magie
                $req_bonus    = 'select ajoute_bonus(' . $perso_cod . ',\'PAM\',2,1)';
                $stmt         = $pdo->query($req_malus);
                $contenu_page .= "<p>
					  Votre Intelligence est de <strong>$intel</strong>.<br><br>
					  C’est très insuffisant pour comprendre ce charabia, vous avez maintenant un très gros mal de crâne...
					  </p>";
      		} else {
				$contenu_page .= "<p>
						  Votre Intelligence est de <strong>$intel</strong>.<br><br>
						  Après avoir parcouru quelques lignes, une intuition (ou peut être un instinct de survie ?) vous pousse à arrêter la lecture : ce manuscrit ne contient rien d’intéressant, en prolonger la lecture ne serait que prendre des risques inutiles pour votre santé mentale.<br>
						  Votre maman a dû vous le dire : la curiosité est un vilain défaut. <img src='http://www.jdr-delain.net/images/smilies/icon_mrgreen.gif'><br>
						<br>
						<br>
						Cela dit peut-être qu’en trouvant un moyen de rendre cet objet à son propriétaire...
					</p>";
      		}
		}
 }  else {
		$contenu_page .= '<p align="center">Une grosse pile de papier listing, noircie de dessins, schémas, calculs et textes. <br><br>
		Le titre de la première page est assez énigmatique : "Bugs à corriger et Evolutions à coder".<br><br>
		Il semble que tout cela parle des souterrains et de leurs secrets.<br><br>
		Peut-être qu’en examinant ces papiers de plus près vous pourrez découvrir des secrets incroyables sur les souterrains ? Découvrir des lieux inconnus ? des techniques inédites ? des trésors fantastiques ?<br><br><br>
			<form method="post" action="listing.php">
				<input type="hidden" name="methode" value="lire" />
				<input type="submit" value="Lire (4PA)"  class="test" />
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