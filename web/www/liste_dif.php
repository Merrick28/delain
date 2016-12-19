<html>
	<head>
		<title></title>
		<link rel="stylesheet" type="text/css" href="style.php">
	</head>
	<body>
	<?php include 'jeu/tableau.php';
	Titre('Liste de diffusion')?>
	<div class="barrLbord"><div class="barrRbord">
		<p class="texteNorm">Si vous souhaitez recevoir par mail toutes les nouveautés du jeu (en gros, si vous souhaitez
		être prévenus dès que je mets une annonce sur les news), il vous suffit de remplir le formulaire suivant :<br><br></p>
		<form method="get" action="http://fr.groups.yahoo.com/subscribe/jdr_delain">
			Entrez ici votre adresse e-mail : <input type="text" name="user" size="45">
			<input type="submit" class="bt_delain" value="Cliquez pour vous inscrire !"><br><br>
		</form>
	</div></div>
	<?php Bordure_Tab()?>
	</div>
	</body>
</html>