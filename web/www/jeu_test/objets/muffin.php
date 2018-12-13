<?php 
include "../verif_connexion.php";
include '../../includes/template.inc';

$t = new template('..');
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.
$bd=new base_delain;
$req_matos = "select perobj_obj_cod from perso_objets,objets "
. "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 244 ";
$bd->query($req_matos);
if(!($bd->next_record())){
  // PAS D'OBJET.
 	$contenu_page .= "<p>Vous avez beau chercher, pas le moindre petit muffin à manger</p>";
} else {
  $num_obj =   $bd->f("perobj_obj_cod");
  // TRAITEMENT DES ACTIONS.
	if(isset($_POST['methode'])){
		$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
		$bd->query($req_pa);
		$bd->next_record();
		if ($bd->f("perso_pa") < 4)
		{
			$contenu_page .= '<p><strong>Vous n’avez pas assez de PA !</strong></p>';
		}
		else
		{
      // ON ENLEVE LES PAs
			$req_enl_pa = "update perso set perso_pa = perso_pa - 4 where perso_cod = $perso_cod";
			$bd->query($req_enl_pa);

			// ON SUPPRIME L'OBJET.
			$req_supr_obj = "select  f_del_objet($num_obj)";
			$bd->query($req_supr_obj);
			// EFFETS 1 BONUS + 1 MALUS
			$codes =         array ("ATT","ARM","PAA","TOU","DEG","ESQ","REG","DEP");
			$modificateurs = array (    1,    1,   -1,    5,    1,    5,    1,    -1);

			$bonus_num = rand(0,count($codes)-1);
			$bonus_val = rand(1,3)*$modificateurs[$bonus_num];
			$bonus_tours = rand(1,3);
			$malus_num = rand(0,count($codes)-1);
			if($malus_num == $bonus_num)
				$malus_num ++;
			if($malus_num == count($codes))
				$malus_num = 0;
			$malus_val = rand(1,3)*$modificateurs[$malus_num] * -1;
			$malus_tours = rand(1,3);


			//INSERTION DU BONUS
            $req_bonus = 'select ajoute_bonus(' . $perso_cod . ',' . $codes[$bonus_num] . ',' . $bonus_tours . ',' . $bonus_val . ')';
			$bd->query($req_bonus);
            //INSERTION DU MALUS
            $req_bonus = 'select ajoute_bonus(' . $perso_cod . ',' . $codes[$malus_num] . ',' . $malus_tours . ',' . $malus_val . ')';
			$bd->query($req_malus);

			$contenu_page .= '<p><strong>Vous mangez le muffin. Vous vous sentez très bizarre...</strong></p>';
		}
	}
	else
	{
	$contenu_page .= '
		<p align="center"><br><br><br><br>
		Un muffin, il a l’air appétissant, même si sa couleur est un peu ... étrange.<br><br>
		Il est emballé dans du papier listing gribouillé sur lequel on peut lire:<br><br>
		<strong><em>Muffins recette spéciale Lutin, naturellement chimique.</em></strong> <br><br>
		<strong>Ingrédients</strong> : <br><br>
		-	Un peu de tout : 60%<br><br>
		-	Un peu de n’importe quoi : 30%<br><br>
		-	Ingrédients Inconnus : 3%<br><br>
		-	Ingrédients non identifiés 6 %<br><br>
		<strong>Colorants & Conservateurs</strong> :';
	$contenu_page .= "E".rand(1,999);
	for($i = 0;$i<500;$i++){
		$contenu_page .= ", E".rand(1,999);
	}
	$contenu_page .= '<br><br>';

	$contenu_page .= '<form method="post" action="muffin.php">
	<input type="hidden" name="methode" value="manger">
	<input type="submit" value="Manger (4PA)"  class="test">
	</form>
	</p>';
	}
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");