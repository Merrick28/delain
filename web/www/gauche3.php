<html>
	<head>
		<title></title>
		
		<link rel="stylesheet" type="text/css" href="style.php">
		<meta name="robot" content="noindex, follow">
		<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
<script type="text/javascript">

window.onload=montre;
function montre(id) {
var d = document.getElementById(id);
	for (var i = 1; i<=10; i++) {
		if (document.getElementById('smenu'+i)) {document.getElementById('smenu'+i).style.display='none';}
	}
if (d) {d.style.display='block';}
}

</script>

<style type="text/css">
/* CSS issu des tutoriels http://css.alsacreations.com */

dl, dt, dd, ul, li {
margin: 0;
padding: 0;
list-style-type: none;
}
#menu {
position: absolute;
top: 0em;
left: 1em;
width: 10em;
}
#menu dt {
cursor: pointer;
}
#menu dd {
position: absolute;
z-index: 100;
left: 12em;
margin-top: -2em;
width: 20em;
border: 1px solid gray;

}
#menu ul {
	background : url(images/fondparchemin.gif);
	padding-right:2px;
	padding-left:2px;

}
#menu li {
text-align: center;
font-size: 85%;
height: 20px;
line-height: 20px;
}
#menu li a, #menu dt a {
color: #000;
text-decoration: none;
display: block;
}
#menu li a:hover {
text-decoration: underline;
}
#mentions {
font-family: verdana, arial, sans-serif;
position: absolute;
bottom : 200px;
left : 50px;
color: #000;
background-color: #ddd;
}
#mentions a {text-decoration: none;
color: #222;
}
#mentions a:hover{text-decoration: underline;
}

</style>

</head>
<body>

<dl id="menu">
	<div id="blockMenu">
	<?php include 'jeu/tableau.php'?>
	<?php Bordure_Tab()?>
	<div class="barrLbord"><div class="barrRbord"><p class="barrTitle">Delain</p></div></div>
	<?php Bordure_Tab()?>
	<div class="barrLbord"><div class="barrRbord">
<!-- Menu  -->

	<br />
<p class="texteMenu">
		<dt onmouseover="javascript:montre('smenu1');"><a href="#">Aides de jeu</a></dt>
			<dd id="smenu1" onmouseover="javascript:montre('smenu1');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="forum/index.php" target="_blank"><b>Forum</b></a></li>
					<li><a href="http://www.jdr-delain.net/wiki/index.php/Règles" target="_blank">Les règles</a></li>
					<li><a href="faq.php" target="droite">FAQ</a></li>
					<li><a href="http://www.jdr-delain.net/wiki" target="_blank">Le Wiki des joueurs</a></li>
				</ul>
			</dd>	
<br /></p>	
<p class="texteMenu">
		<dt onmouseover="javascript:montre('smenu2');" onmouseout="javascript:montre();">Nouveaux joueurs, c'est ici pour vous !</dt>
			<dd id="smenu2" onmouseover="javascript:montre('smenu2');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="doc/aide.php" target="droite">Comment débuter ?</a></li>
					<li><a href="formu_cree_compte.php" target="droite"><b>Créer un compte</b></a></li>
				</ul>
			</dd>	
<br /></p>	
<p class="texteMenu">
		<dt onmouseover="javascript:montre('smenu3');" onmouseout="javascript:montre();">Statistiques et recherches</dt>
			<dd id="smenu3" onmouseover="javascript:montre('smenu3');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="classement.php" target="droite"><b>Classement Joueurs</b></a></li>
					<li><a href="statistiques.php" target="droite">Statistiques</a></li>
					<li><a href="geo.php" target="droite">Où êtes vous ?</a></li>
				</ul>
			</dd>
<br /></p>	
<p class="texteMenu">
		<dt onmouseover="javascript:montre('smenu4');" onmouseout="javascript:montre();">L'histoire de Delain, les éléments de Role Play</dt>
			<dd id="smenu4" onmouseover="javascript:montre('smenu4');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="http://www.jdr-delain.net/wiki/index.php/Histoire" target="droite"><b>L'histoire</b></a></li>
					<li><a href="http://www.chez.com/delain/" target="droite">Le commencement</a></li>
					<li><a href="http://www.jdr-delain.net/voix/" target="droite">La Gazette</a></li>
				</ul>
			</dd>
<br /></p>	
<p class="texteMenu">
		<dt onmouseover="javascript:montre('smenu5');" onmouseout="javascript:montre();">Comment vit Delain ?</dt>
			<dd id="smenu5" onmouseover="javascript:montre('smenu5');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="http://www.jdr-delain.net/wiki/index.php/L%27association" target="_blank"><b>L'Assoc' !!</b></a></li>
					<li><a href="http://www.jdr-delain.net/wiki/index.php/L%27%C3%A9quipe" target="_blank">L'équipe !!</a></li>
					<li><a href="soutenir.php" target="droite">Soutenir le jeu</a></li>
					<li><a href="liste_dif.php" target="droite">Liste de diffusion</a></li>
					<li><a href="irc.php" target="droite">Le chat de Delain</a></li>
				</ul>
			</dd>	
<br /></p>					
	<br /><br />
	<?php  Bordure_Tab()?>
</dl>

	</div></div>

	</div>
	</body>
</html>


<!--

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title></title>
		
		<link rel="stylesheet" type="text/css" href="style.php">
		<meta name="robot" content="noindex, follow">
		<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">

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
			<a href="forum/index.php" target="_blank">Forum</a><br>
			<a href="login2.php" target="droite">Identification</a><br>
		</p>
		<p class="texteMenu">
			<a href="http://www.jdr-delain.net/wiki/index.php/Histoire" target="droite">L'histoire</a><br>
			<a href="http://www.chez.com/delain/" target="droite">le commencement</a><br>		
			<a href="http://www.jdr-delain.net/voix/" target="droite">La Gazette</a><br>
		</p>
		<p class="texteMenu">
			<a href="http://www.jdr-delain.net/wiki/index.php/R%C3%A8gles" target="_blank">Les règles</a><br>
			<a href="faq.php" target="droite">FAQ</a><br>
			<a href="http://www.jdr-delain.net/wiki" target="_blank">Wiki</a><br>

		</p>
		<p class="texteMenu">

		</p>
		<p class="texteMenu">
			<a href="http://www.jdr-delain.net/wiki/index.php/L%27association" target="_blank"><b>L'Assoc' !!</b></a><br>
			<a href="http://www.jdr-delain.net/wiki/index.php/L%27%C3%A9quipe" target="_blank">L'équipe !!</a><br>
			<a href="soutenir.php" target="droite">Soutenir le jeu</a><br>
			<a href="liste_dif.php" target="droite">Liste de diffusion</a><br>
			<a href="irc.php" target="droite">Le chat de Delain</a><br>
			
			
		</p>
		<p class="texteMenu">
			<a href="pack.php" target="droite">Le pack graphique</a><br>
			<a href="news_rss.php"><img src="images/rss.gif" border="0"></a><br>
			<a href="presse.php" target="droite">Ils en ont parlé...</a><br>
			<a href="fow.php" target="droite"><span style="font-size:8pt">Le projet Fog of War</span></a><br>
			<a href="http://www.tourdejeu.net/annu/votejeu.php?id=537&amp;retourfiche=1" target="_blanck"><img src="images/boutonanim.gif" alt="Les autres mondes"></a><br>
			<a href="http://www.gamersroom.com/vote.php?num=56" target="_blank"><img src="http://www.gamersroom.com/pub/bouton3.gif" alt="Gamers'room"></a><br>Votez pour ce site !<br>
			<a href="http://www.jeu-gratuit.net" target="_blank"><img src="http://www.jeu-gratuit.net/images/logoAnime.gif" border="0" alt="Jeux en ligne gratuits"></a><br>
			<a href="http://www.spreadfirefox.com/?q=affiliates&amp;id=83639&amp;t=65"><img border="0" alt="Get Firefox!" title="Get Firefox!" src="http://sfx-images.mozilla.org/affiliates/Buttons/110x32/safer.gif"/></a>
		</p>
	</div></div>
	<?php Bordure_Tab()?>
	</div>
	</body>
</html>
-->