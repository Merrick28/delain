<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title></title>

		<link rel="stylesheet" type="text/css" href="style.php">
		<meta name="robot" content="noindex, follow">
		<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
		<link rel="canonical" href="http://www.jdr-delain.net" />
<script type="text/javascript" src="http://apis.google.com/js/plusone.js">
  {lang: 'fr'}
</script>
	</head>
	<body>

	<div id="blockMenu">
	<?php include 'jeu/tableau.php'?>
	<?php Bordure_Tab()?>
	<div class="barrLbord"><div class="barrRbord"><p class="barrTitle">Delain</p></div></div>
	<?php Bordure_Tab()?>
	<div class="barrLbord"><div class="barrRbord">
		<p class="texteMenu">
			<a href="news.php" target="droite">Accueil</a><br>
			<a href="http://forum.jdr-delain.net" target="_blank">Forum</a><br>
		</p>
		<p class="texteMenu">
			<a href="http://www.jdr-delain.net/wiki/index.php/Histoire" target="droite">L'histoire</a><br>
			<a href="http://www.chez.com/delain/" target="droite">le commencement</a><br>
			<a href="http://www.jdr-delain.net/voix/" target="droite">La Gazette</a><br>
		</p>
		<p class="texteMenu">
			<a href="http://wiki.jdr-delain.net/index.php/R%C3%A8gles" target="_blank">Les règles</a><br>
			<a href="faq.php" target="droite">FAQ</a><br>
			<a href="http://wiki.jdr-delain.net/" target="_blank">Wiki</a><br>
			<a href="doc/aide.php" target="droite">Comment débuter ?</a><br>
			<a href="formu_cree_compte.php" target="droite">Créer un compte</a><br>
		</p>
		<p class="texteMenu">
			<a href="classement.php" target="droite">Classement Joueurs</a><br>
			<a href="champions.php" target="droite">Les champions</a><br>
			<a href="statistiques.php" target="droite">Statistiques</a><br>
			<a href="geo.php" target="droite">Où êtes vous ?</a><br>
		</p>
		<p class="texteMenu">
			<a href="http://wiki.jdr-delain.net/index.php/L%27association" target="_blank"><b>L'Assoc' !!</b></a><br>
			<a href="http://wiki.jdr-delain.net/index.php/L%27equipe" target="_blank">L'équipe !!</a><br>
			<a href="soutenir.php" target="droite">Soutenir le jeu</a><br>
			<a href="liste_dif.php" target="droite">Liste de diffusion</a><br>
			<a href="irc.php" target="droite">Le chat de Delain</a><br>


		</p>
		<p class="texteMenu">
			<a href="news_rss.php"><img src="images/rss.gif" border="0" alt="Flux des news"></a><br>
			<a href="presse.php" target="droite">Ils en ont parlé...</a><br>

		</p>
		<hr>
		<?php 
        //
        // identification
        //
        ob_start();
        include G_CHE . "ident.php";
        $ident = montre_formulaire_connexion($verif_auth, ob_get_contents());
        ob_end_clean();
        echo $ident;
		?>
		</p>
	</div></div>
	<?php Bordure_Tab()?>
	</div>
	</body>
</html>
