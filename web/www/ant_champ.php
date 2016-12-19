<?php 
include "includes/classes.php";
$db = new base_delain;
$db2 = new base_delain;
$db3 = new base_delain;
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php include "jeu_test/tab_haut.php";
?>
<p class="titre">les champions de Delain !</p><hr>
<?php 
$req = "select typc_cod,typc_libelle from type_competences order by typc_libelle ";
$db->query($req);
while($db->next_record())
{
	?>
	<p class="titre"><?php echo $db->f("typc_libelle");?></p>
	<?php 
	$req_c = "select comp_cod,comp_libelle from competences where comp_typc_cod = " . $db->f("typc_cod") . "
		and comp_cod not in (78,69,70) order by comp_libelle ";
	$db2->query($req_c);
	while($db2->next_record())
	{
		$req_p = "select perso_cod,perso_nom,pcomp_modificateur from perso,perso_competences 
			where pcomp_pcomp_cod = " . $db2->f("comp_cod") . " 
			and pcomp_perso_cod = perso_cod
			and perso_type_perso = 1
			and perso_actif = 'O' 
			and perso_cod not in (170696,369198,195193,170692,92891,195087,1911,128293,195147,1908,195192,195084,1909)
			order by pcomp_modificateur asc 
			limit 1";
		$db3->query($req_p);
		if ($db3->nf() != 0)
		{
			$db3->next_record();
			?>
			<table>
			<tr><td class="soustitre2"><p style="text-align:center;"><b><?php echo $db2->f("comp_libelle");?></b></p></td>
			<td>L'anti-champion est <b><?php echo $db3->f("perso_nom");?></b> <i>(<?php echo $db3->f("pcomp_modificateur");?> %)</i></td></tr></table>
			<?php 
		}
		
	}
}	
include "jeu_test/tab_bas.php";
?>
</body>
</html>
