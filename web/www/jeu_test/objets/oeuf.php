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
$req_matos = "select perobj_obj_cod, obj_etat, obj_gobj_cod from perso_objets,objets "
. "where perobj_obj_cod = obj_cod and perobj_obj_cod = $objet and perobj_perso_cod = $perso_cod and obj_gobj_cod in (269, 1137) order by perobj_obj_cod";
$bd->query($req_matos);
if(!($bd->next_record())){
  // PAS D'OBJET.
 	$contenu_page .= "<p>Vous avez beau chercher, il n’y a aucun œuf dans votre sac</p>";
} else {
	$eclosion = false;
	$num_obj =   $bd->f("perobj_obj_cod");
	$etat_objet = $bd->f("obj_etat");
	$type_oeuf = $bd->f("obj_gobj_cod");
	$is_familier = false;
	$req_familier = "select 1 from perso where perso_cod = $perso_cod and perso_type_perso = 3";
	$bd->query($req_familier);
	if($bd->next_record()){
		$is_familier = true;
	}
	$has_familier = false;
	$req_familier = "select pfam_familier_cod from perso_familier,perso where pfam_perso_cod = $perso_cod and pfam_familier_cod = perso_cod and perso_actif = 'O'";
	$bd->query($req_familier);
	if($bd->next_record()){
		$has_familier = true;
	}
	
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
			$contenu_page .= "<p><b>Vous n’avez pas assez de PA !</b></p>";
		}
		else
		{
			$perso_nom = $bd->f("perso_nom");
			if($has_familier)
				$contenu_page .= "<p>Votre familier vous fait clairement comprendre qu’il n’est pas prêt à vous laisser vous occuper de cet œuf.<br />
					Peut-être faudrait-il laisser cet objet à quelqu’un qui n’a pas d’animal jaloux ?</p>";
			else if($is_familier)
				$contenu_page .= "<p>Un familier ne peut pas s’occuper d’un œuf !</p>";
			else
			{
			     // ON ENLEVE LES PAs
				$req_enl_pa = "update perso set perso_pa = perso_pa - 4 where perso_cod = $perso_cod";
				$bd->query($req_enl_pa);
				$contenu_page .= "<p><b>Vous réchauffez l’œuf quelques minutes...</b></p>";
				
      			// ON DIMINUE 'ETAT
      			$diff_etat = mt_rand(0,25) + 1;
				$etat_objet = $etat_objet - $diff_etat;
      			$req_etat = "update objets set obj_etat = $etat_objet where obj_cod = $num_obj";
				$bd->query($req_etat);
				
				if($etat_objet <= 0){
					// L'OEUF ECLOT !
					$eclosion = true;
					// ON SUPPRIME L'OBJET.
					$req_supr_obj = "select  f_del_objet($num_obj)";
					$bd->query($req_supr_obj);
					
					// POSITION DU PROPRIETAIRE
					$req_pos = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod";
					$bd->query($req_pos);
					$bd->next_record();
					$perso_position = $bd->f("ppos_pos_cod");
					
					$choix = mt_rand(0,100);
					if($choix < 25){
						if ($type_oeuf == 1137)
						{
							$contenu_page .= "<p><b>Un cobra apparaît ! Il n’a pas l’air amical. </b><br></p>";
							$req_monstre = "select cree_monstre_pos(533,$perso_position) as num";
							$bd->query($req_monstre);
						} else {						
							$contenu_page .= "<p><b>Un lièvre apparaît ! Il n’a pas l’air apprivoisé. </b><br></p>";
							$req_monstre = "select cree_monstre_pos(16,$perso_position) as num";
							$bd->query($req_monstre);
						}
					} else if($choix < 50){
						if ($type_oeuf == 1137)
						{
							$contenu_page .= "<p><b>Une poule en chocolat apparaît ! Elle vous donne faim. </b><br></p>";
							$req_monstre = "select cree_monstre_pos(570,$perso_position) as num";
							$bd->query($req_monstre);
						} else {
							$contenu_page .= "<p><b>Un basilic apparaît ! Il n’a pas l’air amical. </b><br></p>";
							$req_monstre = "select cree_monstre_pos(13,$perso_position) as num";
							$bd->query($req_monstre);
						}
					} else {
						$typefam = mt_rand(0,1) + 192;	// 192 combat, 193 distance
						$contenu_page .= "<p><b>Un familier sort de l’œuf, il a l’air de vous apprécier et de vous suivre. </b><br></p>";
						$req_monstre = "select cree_monstre_pos($typefam, $perso_position) as num";
						$bd->query($req_monstre);
						$bd->next_record();
						$num_fam = $bd->f("num");
						$nom2 = 'Familier de ' . pg_escape_string($perso_nom);
						$req_monstre = "update perso set perso_nom = e'$nom2', perso_lower_perso_nom = lower(e'$nom2'), perso_type_perso = 3 "
								."where perso_cod = $num_fam";
						$bd->query($req_monstre);
						$req_etat = "insert into perso_familier (pfam_perso_cod,pfam_familier_cod) values ($perso_cod,$num_fam)";
						$bd->query($req_etat);
						// Ajout à la coterie du maître
						$req_coterie = "select pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod=$perso_cod and pgroupe_statut = 1";
						$bd->query($req_coterie);
						if ($bd->next_record())
						{
							$num_coterie = $bd->f("pgroupe_groupe_cod");
							$req_ajout = "insert into groupe_perso (pgroupe_groupe_cod, pgroupe_perso_cod, pgroupe_statut, pgroupe_messages, pgroupe_message_mort)
								values ($num_coterie, $num_fam, 1, 0, 0)";
						}
					}
				}
			}
		}
	}

	if (!$eclosion)
	{
		if($etat_objet < 20)
			$contenu_page .= "<p>L’œuf est craquellé de toutes parts, quelques morceaux de la coquille se détachent... Il est sur le point d’éclore !</p>";
		else if($etat_objet < 40)
			$contenu_page .= "<p>L’œuf bouge de plus en plus les craquelures sont plus nettes et plus nombreuses.</p>";
		else if($etat_objet < 60)
			$contenu_page .= "<p>Les mouvements à l’intérieur de l’œuf sont plus nombreux, quelques craquelures apparaissent par endroits.</p>";
		else if($etat_objet < 80)
			$contenu_page .= "<p>On sent quelques légers mouvements à l’intérieur de l’œuf, la coquille se fendille légèrement.</p>";
		else
			$contenu_page .= "<p>L’œuf est intact et inerte, rien à signaler.</p>";

		$contenu_page .= '<form method="post" action="oeuf.php">
				<input type="hidden" name="methode" value="rechauffer">
				<input type="submit" value="Réchauffer (4PA)"  class="test">
				<input type="hidden" name="objet" value="'.$objet.'" />
			</form>';
	}
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");