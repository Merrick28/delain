<?php include '../connexion.php'?>
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="../style.php">
<link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
<?php if(!isset($_GET['regle_cod'])
    || sscanf($_GET['regle_cod'] , "%u" , $regle_cod) == 0)
    $regle_cod=1;?>
<?php include '../jeu_test/tableau.php';
Titre('Règles')?>
<div class="barrLbord"><div class="barrRbord">
<p class="texteNorm">
<center><i>Règles en cours de mise à jour (en gras) pour tenir compte des modifications récentes. En attendait la fin de leur réécriture, la version ancienne reste disponible</i><table width="80%">
<?php 
	$res_regles=pg_exec($dbconnect,"select regle_cod,regle_titre,regle_texte from regles order by regle_cod");
	$nb_regles=pg_numrows($res_regles);
	for($cpt=0;$cpt<$nb_regles;$cpt++)
	{
		if (fmod($cpt,2) == 0)
		{
			echo("<tr>");
		}
		echo "<td class=\"soustitre2\" valign=\"top\" width=\"50%\">";
		$tab_regles=pg_fetch_array($res_regles,$cpt);
		echo '<a href="regles.php?regle_cod='.$tab_regles[0].'">'.$tab_regles[1].'</a>';
		echo "</td>";
		if (fmod(($cpt+1),2) == 0)
		{
			echo("</tr>");
		}
	}
?>
</table></center>
</p>
</div></div>
<?php Bordure_Tab()?>
</div><br>
<?php Titre('A savoir')?>
<div class="barrLbord"><div class="barrRbord">
<p class="texteNorm">
<?php 
	$res=pg_exec($dbconnect,"select regle_titre,regle_texte from regles where regle_cod='".$regle_cod."'");
	$tab=pg_fetch_array($res,0);
	echo "<b>".$tab[0]."</b><br>".$tab[1];
?>
</p>
</div></div>
<?php Bordure_Tab()?>
</div>
</body>
</html>