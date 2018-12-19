<?php
include "blocks/_header_page_jeu.php";
// on regarde si le joueur est bien sur un centre d’entrainement
$type_lieu = 6;
$nom_lieu = 'un centre d\'entraînement';

include "blocks/_test_lieu.php";

if ($erreur == 0)
{
/* on cherche la valeur actuelle */
    $req_comp = "select pcomp_modificateur, comp_libelle, comp_typc_cod
        from perso_competences
        inner join competences on comp_cod = pcomp_pcomp_cod
        where pcomp_perso_cod = $perso_cod
            and pcomp_pcomp_cod = $comp_cod";
	$db->query($req_comp);
	$db->next_record();
	$niveau_comp = $db->f("pcomp_modificateur");
    $nom_competence = $db->f("comp_libelle");
    $type_comp = $db->f("comp_typc_cod");

    /* données du perso */
    $req_perso = "select perso_pa, perso_po 
        from perso
        where perso_cod = $perso_cod ";
	$db->query($req_perso);
	$db->next_record();

	if ($niveau_comp <= 25)
	{
		$val_des = 4;
		$pa = 1;
	}
	if (($niveau_comp > 25) && ($niveau_comp <= 50 ))
	{
		$val_des = 3;
		$pa = 1;
	}
	if (($niveau_comp > 50) && ($niveau_comp <= 75 ))
	{
		$val_des = 2;
		$pa = 2;
	}
	if (($niveau_comp > 75) && ($niveau_comp < 85 ))
	{
		$val_des = 1;
		$pa = 3;
	}
	if ($niveau_comp >= 85)
	{
		$val_des = 0;
		$contenu_page .= "<p>Erreur ! Compétence > 85 !";
		$erreur = 1;
	}
/* on regarde les pa */
	$pa_actuel = $db->f("perso_pa");
	if ($pa_actuel < $pa)
	{
		$contenu_page .= "<p>Vous n’avez pas assez de PA pour vous entrainer !!!";
		$erreur = 1;
	}
/* on regarde les brouzoufs */
	$brouzouf_actuel = $db->f("perso_po");
	 $prix = $niveau_comp * 4;
	if ($brouzouf_actuel < $prix)
	{
		$contenu_page .= "<p>Vous n’avez pas assez de brouzoufs pour vous payer cet entrainement !";
		$erreur = 1;
	}

	if ($type_comp != 2 && $type_comp != 6 && $type_comp != 7 && $type_comp != 8 && $type_comp != 19)
	{
		$contenu_page .= "<p>Cette compétence ne peut pas être améliorée dans ce centre.";
		$erreur = 1;
	}

	if ($erreur == 0)
	{	
/* on lance les dés */
		$req_des = "select lancer_des(1, $val_des) as des";
		$db->query($req_des);
		$db->next_record();
		$des = $db->f("des");
		$req_up1 = 'update perso_competences set pcomp_modificateur = pcomp_modificateur + ' . $des  . '
			where pcomp_perso_cod = ' . $perso_cod . ' and pcomp_pcomp_cod = ' .$comp_cod;
		$db->query($req_up1);
		$req_up2 = 'update perso set perso_pa = perso_pa - ' . $pa . ',perso_po = perso_po - ' . $prix . ' where perso_cod = ' . $perso_cod;
		$db->query($req_up2);
/*On intègre un évènement d'amélioration*/
		$nouvelle_valeur = $niveau_comp + $des;
		$texte_evt = '[perso_cod1] a amélioré sa compétence '. $nom_competence .', la passant à '. $nouvelle_valeur .'%.';
		$req_evt = "select insere_evenement($perso_cod, $perso_cod, 12, '$texte_evt', 'N', NULL)";
		$db->query($req_evt);	
		$contenu_page .= "<p>Vous avez amélioré la compétence « $nom_competence » de <strong>$des</strong> points !";

		$prix = $nouvelle_valeur * 4;
		if ($nouvelle_valeur <= 25)
		{
			$val_des = 4;
			$pa = 1;
		    $contenu_page .= '<p>Vous pouvez poursuivre votre entrainement de cette compétence
				<br><a href="'. $PHP_SELF .'?comp_cod='.$comp_cod.'">Vous entrainer à nouveau ?</a> <em>(Prix : '.$prix .' brouzoufs, '. $pa .' PA))</em>';
		}
		else if ($nouvelle_valeur <= 50 )
		{
			$val_des = 3;
			$pa = 1;
	    	$contenu_page .= '<p>Vous pouvez poursuivre votre entrainement de cette compétence
				<br><a href="'. $PHP_SELF .'?comp_cod='.$comp_cod.'">Vous entrainer à nouveau ?</a> <em>(Prix : '.$prix .' brouzoufs, '. $pa .' PA)</em>';
		}
		else if ($nouvelle_valeur <= 75 )
		{
			$val_des = 2;
			$pa = 2;
	    	$contenu_page .= '<p>Vous pouvez poursuivre votre entrainement de cette compétence
		    	<br><a href="'. $PHP_SELF .'?comp_cod='.$comp_cod.'">Vous entrainer à nouveau ?</a> <em>(Prix : '.$prix .' brouzoufs, '. $pa .' PA)</em>';
		}
		else if ($nouvelle_valeur < 85 )
		{
			$val_des = 1;
			$pa = 3;
		    $contenu_page .= '<p>Vous pouvez poursuivre votre entrainement de cette compétence
				<br><a href="'. $PHP_SELF .'?comp_cod='.$comp_cod.'">Vous entrainer à nouveau ?</a> <em>(Prix : '. $prix .' brouzoufs, '. $pa .' PA)</em>';
		}
		else
		{
			$val_des = 0;
			$contenu_page .= "<br>Vous ne pouvez pas entrainer à nouveau cette compétence.";
			$erreur = 1;
		}
	}
	$contenu_page .= '<br><br><p><a href="lieu.php">Retour au centre d’entrainement</a>';
}

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

include "blocks/_footer_page_jeu.php";