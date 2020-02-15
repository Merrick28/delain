<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 20/12/18
 * Time: 13:24
 */
/* on lance les dés */
$req_des = "select lancer_des(1, $val_des) as des";
$stmt = $pdo->query($req_des);
$result = $stmt->fetch();
$des = $result['des'];
$req_up1 = 'update perso_competences set pcomp_modificateur = pcomp_modificateur + ' . $des . '
			where pcomp_perso_cod = ' . $perso_cod . ' and pcomp_pcomp_cod = ' . $comp_cod;
$stmt = $pdo->query($req_up1);
$req_up2 = 'update perso set perso_pa = perso_pa - ' . $pa . ',perso_po = perso_po - ' . $prix . ' where perso_cod = ' . $perso_cod;
$stmt = $pdo->query($req_up2);
/*On intègre un évènement d'amélioration*/
$nouvelle_valeur = $niveau_comp + $des;
$texte_evt = '[perso_cod1] a amélioré sa compétence ' . $nom_competence . ', la passant à ' . $nouvelle_valeur . '%.';
$req_evt = "select insere_evenement($perso_cod, $perso_cod, 12, '$texte_evt', 'N', NULL)";
$stmt = $pdo->query($req_evt);
$contenu_page .= "<p>Vous avez amélioré la compétence « $nom_competence » de <strong>$des</strong> points !";

$prix = $nouvelle_valeur * $multiplicateur_prix;
if ($nouvelle_valeur <= 25)
{
    $val_des      = 4;
    $pa           = 1;
    $contenu_page .= '<p>Vous pouvez poursuivre votre entrainement de cette compétence
				<br><a href="' . $_SERVER['PHP_SELF'] . '?comp_cod=' . $comp_cod . '">Vous entrainer à nouveau ?</a> <em>(Prix : ' . $prix . ' brouzoufs, ' . $pa . ' PA))</em>';
} else if ($nouvelle_valeur <= 50)
{
    $val_des      = 3;
    $pa           = 1;
    $contenu_page .= '<p>Vous pouvez poursuivre votre entrainement de cette compétence
				<br><a href="' . $_SERVER['PHP_SELF'] . '?comp_cod=' . $comp_cod . '">Vous entrainer à nouveau ?</a> <em>(Prix : ' . $prix . ' brouzoufs, ' . $pa . ' PA)</em>';
} else if ($nouvelle_valeur <= 75)
{
    $val_des      = 2;
    $pa           = 2;
    $contenu_page .= '<p>Vous pouvez poursuivre votre entrainement de cette compétence
		    	<br><a href="' . $_SERVER['PHP_SELF'] . '?comp_cod=' . $comp_cod . '">Vous entrainer à nouveau ?</a> <em>(Prix : ' . $prix . ' brouzoufs, ' . $pa . ' PA)</em>';
} else if ($nouvelle_valeur < 85)
{
    $val_des      = 1;
    $pa           = 3;
    $contenu_page .= '<p>Vous pouvez poursuivre votre entrainement de cette compétence
				<br><a href="' . $_SERVER['PHP_SELF'] . '?comp_cod=' . $comp_cod . '">Vous entrainer à nouveau ?</a> <em>(Prix : ' . $prix . ' brouzoufs, ' . $pa . ' PA)</em>';
} else
{
    $val_des = 0;
    $contenu_page .= "<br>Vous ne pouvez pas entrainer à nouveau cette compétence.";
    $erreur = 1;
}