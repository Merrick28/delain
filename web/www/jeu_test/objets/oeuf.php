<?php 
include "../verif_connexion.php";


$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.

$req_matos = "select perobj_obj_cod, obj_etat, obj_gobj_cod from perso_objets,objets "
. "where perobj_obj_cod = obj_cod and perobj_obj_cod = $objet and perobj_perso_cod = $perso_cod and obj_gobj_cod in (269, 1137) order by perobj_obj_cod";
$stmt = $pdo->query($req_matos);
if (!($result = $stmt->fetch()))
{
  // PAS D'OBJET.
 	$contenu_page .= "<p>Vous avez beau chercher, il n’y a aucun œuf dans votre sac</p>";
} else {
	$eclosion     = false;
    $num_obj      = $result['perobj_obj_cod'];
    $etat_objet   = $result['obj_etat'];
    $type_oeuf    = $result['obj_gobj_cod'];
	$is_familier  = false;
	$req_familier = "select 1 from perso where perso_cod = $perso_cod and perso_type_perso = 3";
    $stmt = $pdo->query($req_familier);
    if($result = $stmt->fetch())
    {
		$is_familier = true;
	}
	$has_familier = false;
    $req_familier =
        "select pfam_familier_cod from perso_familier,perso where pfam_perso_cod = $perso_cod and pfam_familier_cod = perso_cod and perso_actif = 'O'";
    $stmt         = $pdo->query($req_familier);
    if ($result = $stmt->fetch())
    {
        $has_familier = true;
    }

    // TRAITEMENT DES ACTIONS.
    //echo $objet;
    if ($objet == null)
    {
        $objet = get_request_var('objet', -1);
    }

    if (isset($_POST['methode']))
    {
        $req_pa = "select perso_pa,perso_nom from perso where perso_cod = $perso_cod";
        $stmt   = $pdo->query($req_pa);
        $result = $stmt->fetch();
        if ($result['perso_pa'] < 4)
        {
            $contenu_page .= "<p><strong>Vous n’avez pas assez de PA !</strong></p>";
        } else
		{
            $perso_nom = $result['perso_nom'];
			if($has_familier)
				$contenu_page .= "<p>Votre familier vous fait clairement comprendre qu’il n’est pas prêt à vous laisser vous occuper de cet œuf.<br />
					Peut-être faudrait-il laisser cet objet à quelqu’un qui n’a pas d’animal jaloux ?</p>";
			else if($is_familier)
				$contenu_page .= "<p>Un familier ne peut pas s’occuper d’un œuf !</p>";
			else
			{
			     // ON ENLEVE LES PAs
				$req_enl_pa = "update perso set perso_pa = perso_pa - 4 where perso_cod = $perso_cod";
                $stmt = $pdo->query($req_enl_pa);
				$contenu_page .= "<p><strong>Vous réchauffez l’œuf quelques minutes...</strong></p>";
				
      			// ON DIMINUE 'ETAT
      			$diff_etat = mt_rand(0,25) + 1;
				$etat_objet = $etat_objet - $diff_etat;
      			$req_etat = "update objets set obj_etat = $etat_objet where obj_cod = $num_obj";
                $stmt = $pdo->query($req_etat);
				
				if($etat_objet <= 0){
					// L'OEUF ECLOT !
					$eclosion = true;
					// ON SUPPRIME L'OBJET.
					$req_supr_obj = "select  f_del_objet($num_obj)";
                    $stmt = $pdo->query($req_supr_obj);
					
					// POSITION DU PROPRIETAIRE
					$req_pos = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod";
                    $stmt = $pdo->query($req_pos);
                    $result = $stmt->fetch();
                    $perso_position = $result['ppos_pos_cod'];
					
					$choix = mt_rand(0,100);
					if($choix < 25){
						if ($type_oeuf == 1137)
						{
							$contenu_page .= "<p><strong>Un cobra apparaît ! Il n’a pas l’air amical. </strong><br></p>";
							$req_monstre = "select cree_monstre_pos(533,$perso_position) as num";
                            $stmt = $pdo->query($req_monstre);
						} else {						
							$contenu_page .= "<p><strong>Un lièvre apparaît ! Il n’a pas l’air apprivoisé. </strong><br></p>";
							$req_monstre = "select cree_monstre_pos(16,$perso_position) as num";
                            $stmt = $pdo->query($req_monstre);
						}
					} else if($choix < 50){
						if ($type_oeuf == 1137)
						{
							$contenu_page .= "<p><strong>Une poule en chocolat apparaît ! Elle vous donne faim. </strong><br></p>";
							$req_monstre = "select cree_monstre_pos(570,$perso_position) as num";
                            $stmt = $pdo->query($req_monstre);
						} else {
							$contenu_page .= "<p><strong>Un basilic apparaît ! Il n’a pas l’air amical. </strong><br></p>";
							$req_monstre = "select cree_monstre_pos(13,$perso_position) as num";
                            $stmt = $pdo->query($req_monstre);
						}
					} else {
						$typefam = mt_rand(0,1) + 192;	// 192 combat, 193 distance
						$contenu_page .= "<p><strong>Un familier sort de l’œuf, il a l’air de vous apprécier et de vous suivre. </strong><br></p>";
						$req_monstre = "select cree_monstre_pos($typefam, $perso_position) as num";
                        $stmt = $pdo->query($req_monstre);
                        $result = $stmt->fetch();
                        $num_fam     = $result['num'];
						$nom2 = 'Familier de ' . pg_escape_string($perso_nom);
						$req_monstre = "update perso set perso_nom = e'$nom2', perso_lower_perso_nom = lower(e'$nom2'), perso_type_perso = 3 "
								."where perso_cod = $num_fam";
                        $stmt = $pdo->query($req_monstre);
						$req_etat = "insert into perso_familier (pfam_perso_cod,pfam_familier_cod) values ($perso_cod,$num_fam)";
                        $stmt = $pdo->query($req_etat);
						// Ajout à la coterie du maître
						$req_coterie = "select pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod=$perso_cod and pgroupe_statut = 1";
                        $stmt = $pdo->query($req_coterie);
                        if($result = $stmt->fetch())
						{
                            $num_coterie = $result['pgroupe_groupe_cod'];
							$req_ajout   = "insert into groupe_perso (pgroupe_groupe_cod, pgroupe_perso_cod, pgroupe_statut, pgroupe_messages, pgroupe_message_mort)
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

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));