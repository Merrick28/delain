<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();

$compte = (isset($_GET['compte'])) ? $_GET['compte'] : 0;	

// Première partie de la page : liste des comptes incriminés
$req = 'select distinct compt_cod, compt_nom
	from
	(
		select * from ligne_evt where levt_perso_cod1 != levt_cible
			and levt_perso_cod1 IN (select perso_cod from perso where perso_pnj=2)
			and levt_cible in
				(select pcompt_perso_cod from perso_compte a where pcompt_compt_cod =
					(select pcompt_compt_cod from perso_compte b where b.pcompt_perso_cod = levt_perso_cod1)) 
		UNION ALL
		select * from ligne_evt where levt_perso_cod1 != levt_attaquant
			and levt_perso_cod1 IN (select perso_cod from perso where perso_pnj=2)
			and levt_attaquant in
				(select pcompt_perso_cod from perso_compte a where pcompt_compt_cod =
					(select pcompt_compt_cod from perso_compte b where b.pcompt_perso_cod = levt_perso_cod1))
	)t
	inner join perso on perso_cod = levt_attaquant
	inner join perso_compte on pcompt_perso_cod = perso_cod
	inner join compte on pcompt_compt_cod = compt_cod';
$db->query($req);

echo '<p><strong>Liste des comptes ayant commis des intéractions entre la triplette de base et son 4e personnage.</strong> Cliquez sur un compte pour voir le détail.</p>';
echo '<p>';
while ($db->next_record())
{
	if ($compte == $db->f('compt_cod')) echo '<strong>';
	echo '- <a href="?compte=' . $db->f('compt_cod') . '">' . $db->f('compt_nom') . '</a> -';
	if ($compte == $db->f('compt_cod')) echo '</strong>';
}
echo '</p>';
echo '<p><strong>Voir aussi la <a href="controle_proximite_4e.php">liste des comptes dont un personnage est à proximité du 4e perso</a></strong> (peut être un peu long à afficher).</p><hr />';

if ($compte > 0)
{
	$req = 'select levt_date, levt_texte,
			p1.perso_cod as pc1,
			p1.perso_nom || case when p1.perso_pnj = 2 then \' *\' else \'\' end as pn1, 
			p2.perso_cod as pc2,
			p2.perso_nom || case when p2.perso_pnj = 2 then \' *\' else \'\' end as pn2
		from
		(
			select * from ligne_evt where levt_perso_cod1 != levt_cible
				and levt_perso_cod1 IN (select perso_cod from perso where perso_pnj=2)
				and levt_cible in
					(select pcompt_perso_cod from perso_compte a where pcompt_compt_cod =
						(select pcompt_compt_cod from perso_compte b where b.pcompt_perso_cod = levt_perso_cod1)) 
			UNION ALL
			select * from ligne_evt where levt_perso_cod1 != levt_attaquant
				and levt_perso_cod1 IN (select perso_cod from perso where perso_pnj=2)
				and levt_attaquant in
					(select pcompt_perso_cod from perso_compte a where pcompt_compt_cod =
						(select pcompt_compt_cod from perso_compte b where b.pcompt_perso_cod = levt_perso_cod1))
		)t
		inner join perso p1 on p1.perso_cod = levt_attaquant
		inner join perso p2 on p2.perso_cod = levt_cible
		inner join perso_compte on pcompt_perso_cod = p1.perso_cod
		where pcompt_compt_cod = ' . $compte . '
		order by levt_date';
	$db->query($req);
?>
<p><a href="detail_compte.php?compte=<?php echo $compte?>">-- Voir les détails du compte --</a></p>
<p></p>
<p>Dans le tableau ci-dessous, une astérisque indique le 4e personnage</p>
<table cellspacing="2">
<tr><td colspan="4" class="titre"><p class="titre">Intéractions</p></td></tr>
<tr>
	<td class="soustitre3"><p><strong>Date</strong></p></td>
	<td class="soustitre3"><p><strong>[attaquant]</strong></p></td>
	<td class="soustitre3"><p><strong>[cible]</strong></p></td>
	<td class="soustitre3"><p><strong>Détail</strong></p></td>
</tr>
<?php 
	while ($db->next_record())
	{
		$pn1 = $db->f("pn1");
		$pc1 = $db->f("pc1");
		$pn2 = $db->f("pn2");
		$pc2 = $db->f("pc2");
		$levt_date = $db->f("levt_date");
		$levt_texte = $db->f("levt_texte");
		echo "<tr>";
		echo "<td class=\"soustitre3\"><p>$levt_date</p></td>";
		echo "<td class=\"soustitre3\"><p>$pn1</p></td>";
		echo "<td class=\"soustitre3\"><p>$pn2</p></td>";
		echo "<td class=\"soustitre3\"><p>$levt_texte</p></td>";
		echo("</tr>");
	}
	echo '</table>';
}

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
