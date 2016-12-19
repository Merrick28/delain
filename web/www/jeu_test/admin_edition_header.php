<table align="center" cellpadding="10">
<tr>
<?php 
if ($droit['modif_perso'] == 'O')
{
	?>
	<td><a href="admin_perso_edit.php">PERSOS</a></td>
	<td><a href="admin_objet_edit.php">ÉQUIPEMENT</a></td>
	<td><a href="deplace_monstre.php">DÉPLACEMENT MASSE</a></td>
	<?php 
}
if ($droit['creer_monstre'] == 'O')
{
	?>
	<td><a href="admin_creation_monstres.php">AJOUTER MONSTRE</a></td>
	<?php 
}
if ($droit['modif_gmon'] == 'O')
{
	?>
	<td><a href="admin_type_monstre_edit.php">GÉRER MONSTRES</a></td>
	<td><a href="admin_repartition_monstres.php">RÉPART MONSTRES</a></td>
	<td><a href="admin_serie_equipements.php">ÉQUIPEMENTS MONSTRES</a></td>
	<td><a href="admin_recap_repartition.php">SYNTHÈSE RÉPARTITION ÉQUIPEMENTS</a></td>
	<td><a href="admin_test_reglages.php">AUTRES REGLAGES</a></td>
	<?php 
}
?>
</tr>
</table>
