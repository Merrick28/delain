<?php 
//include "../connexion.php";
include "../verif_connexion.php";
include '../../includes/template.inc';

$t = new template('..');
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

$contenu_page = '';

// ON VERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.
$bd=new base_delain;
$req_matos = "select perobj_obj_cod from perso_objets,objets "
. "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 248 ";
$bd->query($req_matos);
if(!($bd->next_record())){
  // PAS D'OBJET.
 	$contenu_page .= "<p>Une liste ? où ça une liste ? Je n'ai pas vu de liste...</p>";
} else {
  $num_obj =   $bd->f("perobj_obj_cod");
  // TRAITEMENT DES ACTIONS.
	if(isset($_POST['methode'])){
		$req_pa = "select perso_pa,perso_int from perso where perso_cod = $perso_cod";
		$bd->query($req_pa);
		$bd->next_record();

		if ($bd->f("perso_pa") < 4)
		{
			$contenu_page .= '<p><b>Vous n’avez pas assez de PA !</b></p>';
		}
		else
		{
			$intel = $bd->f("perso_int");
      // ON ENLEVE LES PAs
			$req_enl_pa = "update perso set perso_pa = perso_pa - 4 where perso_cod = $perso_cod";
			$bd->query($req_enl_pa);

			if($intel < 19){
				//INSERTION DU MALUS de vue
                $req_bonus = 'select ajoute_bonus(' . $perso_cod . ',\'VUE\',2,-1)';
				$bd->query($req_bonus);
      			//INSERTION DU MALUS de magie
                $req_bonus = 'select ajoute_bonus(' . $perso_cod . ',\'PAM\',2,1)';
				$bd->query($req_malus);
				$contenu_page .= "<p>
					  Votre Intelligence est de <b>$intel</b>.<br><br>
					  C’est très insuffisant pour comprendre ce charabia, vous avez maintenant un très gros mal de crâne...
					  </p>";
      		} else {
				$contenu_page .= "<p>
						  Votre Intelligence est de <b>$intel</b>.<br><br>
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

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
