<html>
	<head>
		<title></title>
		<link rel="stylesheet" type="text/css" href="style.php">
	</head>
	<body>
	<?php include 'jeu/tableau.php';
	Titre('L\'équipe !')?>
	<div class="barrLbord"><div class="barrRbord">
		<?php 
		$fonction=array("Concept initial","Programmation et mise en oeuvre","Hébergement et conseil","Administration monstres","Gestion de la FAQ","Gestion des règles","Responsable quêtes","Responsable équipe contrôle","Graphisme","Modération du forum","Rédacteur historique","Anciens participants vogant vers d'autres horizons");
		$perso=array("Merrick","Merrick<br>Mirreck<br>Blade<br>Bleda<br>Azaghal","Olivier PRENANT","Azaghal","Bricogne","Erastide","Azaghal","Dwight","Forum Dessin","Azaghal<br>Blade","Perf","Balin<br>Bricogne<br>Naikikoul<br>Dodger<br>Erastide<br>Althéos<br>...");
		for($i=0;$i<12;$i++){?>
		<div style="width:365px;padding:5px 0 0 5px" onMousemove="this.style.background='#BA9C6C'" onMouseout="this.style.background=''">
			<p style="width:230px;float:left;line-height:12pt"><?php echo $fonction[$i]?> :</p>
			<p style="width:120px;float:left;line-height:12pt;padding-left:15px"><?php echo $perso[$i]?></p>
		</div>
		<?php }?>
		<p class="texteNorm" style="clear:left"><br />
			Et un remerciement particulier pour les personnes suivantes :<br /><br />
			<b>M. Olivier PRENANT</b>, gérant de la société <a href="http://www.pyrenet.fr" target="_blank">PYRENET</a>,
			sans qui ce projet n'aurait pas pu aboutir. Je tiens à le remercier tout particulièrement pour sa disponiblité à toute épreuve, et son
			soutien dans le "débuggage" du jeu.<br /><br />
			<b>Les programmeurs et webmasters des jeux <a href="http://www.mountyhall.com" target="_blank">Mountyhall</a> et <a href="http://www.nainwak.com" target="_blank">Nainwak</a></b>
			sans qui je n'aurais jamais connu ce style de jeux, et donc, je n'aurais jamais lancé la programmation de Delain.
			Et pour finir, mon adorable et tendre épouse, qui m'a poussé à faire ceci, et a gentiment accepté de me laisser du temps libre pour
			avancer à ma guise sur ce jeu.<hr />
			Ce jeu est fait pour apporter une touche d'évasion à tous ceux qui le souhaitent. J'espère de tout coeur que vous y trouverez votre bonheur.
			<br /><br />Merrick.
		</p>
	</div></div>
	<?php Bordure_Tab()?>
	</div>
	</body>
</html>