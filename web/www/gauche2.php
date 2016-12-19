<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php  
$nom_template = 'general';
include "includes/constantes.php";
if (!@include "includes/template.inc")
			include "includes/template.inc";
// définition template
$t = new template;
$t->set_file("FileRef","template/classic/" . $nom_template . ".tpl");
// chemins
$t->set_var("g_url",$type_flux . G_URL);
$t->set_var("g_che",G_CHE);
// chemin des images
$t->set_var("img_path",G_IMAGES);
// contenu de la page
?>
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
<?php 
$contenu_page2 = '';
$contenu_page2 .= '<html>
	<head>
		<title></title>
		<base target="droite">
		<link rel="stylesheet" type="text/css" href="style.php">
		<meta name="robot" content="noindex, follow">
		<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
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
	<body>';
	
$contenu_page2 .= '<dl id="menu">
	<div id="blockMenu">
	';
	include 'jeu/tableau.php';

$contenu_page2 .= '
	<div class="barrLbord"><div class="barrRbord">';
	$contenu_page2 .= '<p class="barrTitle">Delain</p>';
	$contenu_page2 .= '</div></div>
	';


	$contenu_page2 .= '	<div class="barrLbord"><div class="barrRbord">

	<br />
<p class="texteMenu">
		<dt onmouseover="javascript:montre(\'smenu1\');"><a href="#">Aides de jeu</a></dt>
			<dd id="smenu1" onmouseover="javascript:montre(\'smenu1\');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="forum/index.php" target="_blank"><b>Forum</b></a></li>
					<li><a href="http://www.jdr-delain.net/wiki/index.php/Règles" target="_blank">Les règles</a></li>
					<li><a href="faq.php" target="droite">FAQ</a></li>
					<li><a href="http://www.jdr-delain.net/wiki" target="_blank">Le Wiki des joueurs</a></li>
				</ul>
			</dd>	
<br /></p>	
<p class="texteMenu">
		<dt onmouseover="javascript:montre(\'smenu2\');" onmouseout="javascript:montre();">Nouveaux joueurs, c\'est ici pour vous !</dt>
			<dd id="smenu2" onmouseover="javascript:montre(\'smenu2\');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="doc/aide.php" target="droite">Comment débuter ?</a></li>
					<li><a href="formu_cree_compte.php" target="droite"><b>Créer un compte</b></a></li>
				</ul>
			</dd>	
<br /></p>	
<p class="texteMenu">
		<dt onmouseover="javascript:montre(\'smenu3\');" onmouseout="javascript:montre();">Statistiques et recherches</dt>
			<dd id="smenu3" onmouseover="javascript:montre(\'smenu3\');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="classement.php" target="droite"><b>Classement Joueurs</b></a></li>
					<li><a href="statistiques.php" target="droite">Statistiques</a></li>
					<li><a href="geo.php" target="droite">Où êtes vous ?</a></li>
				</ul>
			</dd>
<br /></p>	
<p class="texteMenu">
		<dt onmouseover="javascript:montre(\'smenu4\');" onmouseout="javascript:montre();">L\'histoire de Delain, les éléments de Role Play</dt>
			<dd id="smenu4" onmouseover="javascript:montre(\'smenu4\');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="http://www.jdr-delain.net/wiki/index.php/Histoire" target="droite"><b>L\'histoire</b></a></li>
					<li><a href="http://www.chez.com/delain/" target="droite">Le commencement</a></li>
					<li><a href="http://www.jdr-delain.net/voix/" target="droite">La Gazette</a></li>
				</ul>
			</dd>
<br /></p>	
<p class="texteMenu">
		<dt onmouseover="javascript:montre(\'smenu5\');" onmouseout="javascript:montre();">Comment vit Delain ?</dt>
			<dd id="smenu5" onmouseover="javascript:montre(\'smenu5\');" onmouseout="javascript:montre();">
				<ul>
					<li><a href="http://www.jdr-delain.net/wiki/index.php/L%27association" target="_blank"><b>L\'Assoc\' !!</b></a></li>
					<li><a href="http://www.jdr-delain.net/wiki/index.php/L%27%C3%A9quipe" target="_blank">L\'équipe !!</a></li>
					<li><a href="soutenir.php" target="droite">Soutenir le jeu</a></li>
					<li><a href="liste_dif.php" target="droite">Liste de diffusion</a></li>
					<li><a href="irc.php" target="droite">Le chat de Delain</a></li>
				</ul>
			</dd>	
<br /></p>					
	<br /><br />
	'; 
/*Bordure_Tab();*/
	$contenu_page2 .= '
</dl>	
</div></div>
	</div>';
	$contenu_page2 .= '</div>
										</body>
									</html>';
$t->set_var("contenu_page2",$contenu_page2);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
