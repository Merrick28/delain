<?php
include "blocks/_header_page_jeu.php";
ob_start();

?>

<p><strong>Liste des comptes dont les personnages principaux sont proches du quatrième personnage.</strong>
	Ce n’est pas nécessairement répréhensible, mais un avertissement spécifique leur est envoyé à la connexion.</p>


<table cellspacing="2">
<tr>
	<td class="soustitre3" rowspan='2'><p><strong>Compte</strong></p></td>
	<td class="soustitre3" colspan='2'><p><strong>Personnage principal</strong></p></td>
	<td class="soustitre3" colspan='2'><p><strong>Quatrième personnage</strong></p></td>
</tr>
<tr>
	<td class="soustitre3"><p><strong>Nom</strong></p></td>
	<td class="soustitre3"><p><strong>Position</strong></p></td>
	<td class="soustitre3"><p><strong>Nom</strong></p></td>
	<td class="soustitre3"><p><strong>Position</strong></p></td>
</tr>
<?php 
$req = 'select compt_cod, compt_nom,
		triplette.perso_cod as perso_cod1, triplette.perso_nom as perso_nom1,
		quatrieme.perso_cod as perso_cod4, quatrieme.perso_nom as perso_nom4,
		pos_triplette.pos_x::text || \', \' || pos_triplette.pos_y::text || \', \' || etage_triplette.etage_libelle as pos1,
		pos_quatrieme.pos_x::text || \', \' || pos_quatrieme.pos_y::text || \', \' || etage_quatrieme.etage_libelle as pos4
	from perso triplette
	inner join perso_compte pc1 on pc1.pcompt_perso_cod = triplette.perso_cod
	inner join perso_compte pc2 on pc2.pcompt_compt_cod = pc1.pcompt_compt_cod
	inner join perso quatrieme on quatrieme.perso_cod = pc2.pcompt_perso_cod
	inner join compte on compt_cod = pc1.pcompt_compt_cod
	inner join perso_position pp1 on pp1.ppos_perso_cod = pc1.pcompt_perso_cod
	inner join perso_position pp2 on pp2.ppos_perso_cod = pc2.pcompt_perso_cod
	inner join positions pos_triplette on pos_triplette.pos_cod = pp1.ppos_pos_cod
	inner join positions pos_quatrieme on pos_quatrieme.pos_cod = pp2.ppos_pos_cod
	inner join etage etage_triplette on etage_triplette.etage_numero = pos_triplette.pos_etage
	inner join etage etage_quatrieme on etage_quatrieme.etage_numero = pos_quatrieme.pos_etage
	where triplette.perso_pnj <> 2 and quatrieme.perso_pnj = 2
		and triplette.perso_actif = \'O\' and quatrieme.perso_actif = \'O\'
		and controle_persos_proches(triplette.perso_cod, quatrieme.perso_cod, 10)
	order by compt_cod';
$db->query($req);

$lignes = '';
$compte_precedent = -1;
$nombre = 0;

while ($db->next_record())
{
	$c_cod = $db->f("compt_cod");
	$c_nom = $db->f("compt_nom");

	if ($c_cod != $compte_precedent && $nombre > 0)
	{
		echo "<tr><td class=\"soustitre3\" rowspan='$nombre'>$lignes";
		$nombre = 0;
		$compte_precedent = $c_cod;
		$lignes = '';
	}
	$pn1 = $db->f("perso_nom1");
	$pc1 = $db->f("perso_cod1");
	$pn4 = $db->f("perso_nom4");
	$pc4 = $db->f("perso_cod4");
	$pos1 = $db->f("pos1");
	$pos4 = $db->f("pos4");
	if ($nombre == 0)
	{
		$lignes .= "<a href='detail_compte.php?compte=$c_cod'>$c_nom</a></td>";
	}
	else
	{
		$lignes .= "<tr>";
	}
	$lignes .= "<td class=\"soustitre3\"><p>$pn1 ($pc1)</p></td>";
	$lignes .= "<td class=\"soustitre3\"><p>$pos1</p></td>";
	$lignes .= "<td class=\"soustitre3\"><p>$pn4 ($pc4)</p></td>";
	$lignes .= "<td class=\"soustitre3\"><p>$pos4</p></td>";
	$lignes .= "</tr>";
	$nombre++;
}
if ($c_cod != $compte_precedent && $nombre > 0)
{
	echo "<tr><td class=\"soustitre3\" rowspan='$nombre'>$lignes";
}
echo '</table>';

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
