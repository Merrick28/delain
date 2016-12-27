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
. "where perobj_obj_cod = obj_cod and perobj_obj_cod = $objet and perobj_perso_cod = $perso_cod and obj_gobj_cod in (861) order by perobj_obj_cod";
$bd->query($req_matos);
if(!($bd->next_record())){
  // PAS D'OBJET.
    $contenu_page .= "<p>Vous avez beau chercher, il n'y a aucune nourriture dans votre sac</p>";
} else {
  $num_obj =   $bd->f("perobj_obj_cod");
  //echo "OBJ=".$num_obj;
    // TRAITEMENT DES ACTIONS.
    //echo $objet;
    if ($objet == null)
        $objet = isset($_POST['objet'])?$_POST['objet']: "-1";
    if(isset($_POST['methode'])){
        $req_pa = "select perso_pa,perso_nom from perso where perso_cod = $perso_cod";
        $bd->query($req_pa);
        $bd->next_record();
        if ($bd->f("perso_pa") < 4)
        {
            $contenu_page .= '<p><b>Vous n\'avez pas assez de PA !</b></p>';
        }
        else
        {
            // ON ENLEVE LES PAs
            $req_enl_pa = "update perso set
                perso_pa = perso_pa - 4,
                perso_pv = min(perso_pv + 4, perso_pv_max)
                where perso_cod = $perso_cod";
            $bd->query($req_enl_pa);
            $contenu_page .= '<p><b>Vous faites un festin et gagnez quelques points de vie... Attention à votre régime.</b></p>';
            $bd->query('select lancer_des(1,100) as reussite');
            $bd->next_record();
            $reussite = $bd->f('reussite');
            if ($reussite >= 96)
            {
                $contenu_page .= '<p><b>Votre charisme vient d\'en prendre un coup... Il est temps de vous remettre au sport !</b></p>';
                // On rajoute une bouée disgrâcieuse.
                $req_cree_bouee = "select cree_objet_perso_equipe(640, $perso_cod), f_del_objet($objet)";
                $bd->query($req_cree_bouee);
            }
        }
    }
	$contenu_page .= '<p align="center"><br>
		<p>Un peu de nourriture n’a jamais fait de mal, je suppose... </p>
		<form method="post" action="nomnom.php">
		<input type="hidden" name="methode" value="manger">
		<input type="submit" value="Manger (4PA)"  class="test">
		<input type="hidden" name="objet" value="' . $objet . '" />
		</form>
		</p>';
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");