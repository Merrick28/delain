<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head><title>Les news du site</title>
<meta name="keywords" content="jeu,rôle,delain,ligne,gratuit,multi-joueur">
<meta name="description" content="Les souterrains de Delain, jeu de rôle gratuit en ligne">
<html>   
<?php 
if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		if ($_SERVER['HTTP_X_FORWARDED_HOST'] != 'www.jdr-delain.net')
		{
			die("L'accès du jeu se fait uniquement par l'adresse <a href=\"http://www.jdr-delain.net\">www.jdr-delain.net</a>");
		}
	}
include "img_pack.php";
include "classes.php";
//
// DEBUT PUBLICITES
//
// google
$n_pub = array
('google' => '<script type="text/javascript"><!--
google_ad_client = "pub-6632318064183878";
google_alternate_color = "666666";
google_ad_width = 468;
google_ad_height = 60;
google_ad_format = "468x60_as";
google_ad_channel ="";
google_page_url = document.location;
google_color_border = "800000";
google_color_bg = "CEB68D";
google_color_link = "800000";
google_color_url = "800000";
google_color_text = "000000";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
'
);
//
// on rajoute click in text si besoin
//
/*, 
'promobenef' => '<!-- Tag PromoBenef site membre N°24301-->
<script type="text/javascript">
<!--
var promobenef_site = "24301";
var promobenef_minipub = "1";
var promobenef_format = "1";
//-->
</script>
<script type="text/javascript" src="http://www.promobenef.com/pub/"></script>
<noscript><p><a href="http://www.promobenef.com/">PromoBenef : r&eacute;gie publicitaire<img src="http://www.promobenef.com/no_js/?sid=24301&fid=1" alt="PromoBenef" width="0" height="0" style="border:none;" /></a></p></noscript>',
*/

if(!isset($_COOKIE['cit']))
{
	$pub_temp = array(
		"CIT" => ' <!--
		// ClickInText(TM) - Slide In Technology :
		// (fr) Pensez à vérifier que le site sur lequel vous installez ce script a bien été ajouté à votre compte ClickInText
		-->
		<script type="text/javascript" src="http://fr.slidein.clickintext.net/?a=267"></script>
		<!--
		// ClickInText(TM) - Slide In Technology : End
		-->');
	$n_pub = array_merge($n_pub,$pub_temp);
}
srand ((double) microtime() * 10000000);
$nb_pub = array_rand($n_pub,1);
//
// on force la pub à google sur l'origine est un click adwords
//
if(isset($origine))
	if($origine='google')
		$nb_pub = 'google';
$aff_pub = $n_pub[$nb_pub];
$nb_pub2 = rand(1,110);
/*if ($nb_pub2 > 90)
{
	$aff_pub = '<table width=468 border=0 cellpadding=0 cellspacing=1 bgcolor=#000000 style="border:0px;background:#000000;border-spacing:1px">
<tr bgcolor=#FFFFC0><td height=44 style="padding:0px"><iframe src="http://www.eurobarre.com/gagner-de-l-argent-468x60-658205096351.php" width=466 height=44 frameborder=0 marginwidth=0 marginheight=0 scrolling=no></iframe></td></tr><tr><td><table width=466 border=0 cellpadding=0 cellspacing=0><tr height=13 bgcolor=#FFFFC0><td width=24><a href="http://www.eurobarre.com"><img src="http://www.eurobarre.com/gagner-de-l-argent-FFFFC0-000000.png" alt="Gagner de l\'argent" width=24 height=13 border=0></a></td><td bgcolor=#FFFFC0 style="font-family:Arial;font-size:10px;text-decoration:none;color:#000000;padding:0px"><center>Un petit <a href="http://www.eurobarre.com" style="font-family:Arial;font-size:10px;text-decoration:none;color:#000000">besoin d\'argent</a>, <a href="http://www.eurobarre.com" style="font-family:Arial;font-size:10px;text-decoration:none;color:#000000">gagner de l\'argent</a> avec <a href="http://www.eurobarre.com" style="font-family:Arial;font-size:10px;text-decoration:none;color:#000000">eurobarre</a>, c\'est <a href="http://www.eurobarre.com" style="font-family:Arial;font-size:10px;text-decoration:none;color:#000000">facile</a> et <a href="http://www.eurobarre.com" style="font-family:Arial;font-size:10px;text-decoration:none;color:#000000">gratuit</a> !</center></td><td width=24><a href="http://www.eurobarre.com/echange-de-liens.php"><img src="http://www.eurobarre.com/echange-de-liens-FFFFC0-000000.png" alt="Echange de liens dur avec Eurobarre: \'Jeux de Rôle - PBEM\'." width=24 height=13 border=0></a></td></tr></table></td></tr></table>';
	$nb_pub = 0;
}*/
//
// si la pub affichée est click in text, on pose un cookie pour ne pas reprendre cette valeur pendant une douzaine d'heures
// ceci nous permet de se concentrer sur les pubs qui peuvent rapporter plus
//
if($nb_pub == 'CIT')
{
	// on met un cookie pour eviter d'afficher plusieurs fois la pub	
	setcookie("cit",0,time()+50000);
}
if($nb_pub == '128a')
{
	// on met un cookie pour eviter d'afficher plusieurs fois la pub	
	setcookie("128a",0,time()+50000);
}
if($nb_pub == '128b')
{
	// on met un cookie pour eviter d'afficher plusieurs fois la pub	
	setcookie("128b",0,time()+50000);
}
if($nb_pub == '128c')
{
	// on met un cookie pour eviter d'afficher plusieurs fois la pub	
	setcookie("128c",0,time()+50000);
}
/// FIN PUBLICITES

$db = new base_delain;
$recherche = "SELECT gmon_avatar FROM monstre_generique where gmon_avatar is not null and gmon_avatar != 'defaut.png' order by random() limit 1";
$db->query($recherche);
$db->next_record();
$image = $db->f("gmon_avatar");
?>

<link rel="stylesheet" type="text/css" href="style.php">
<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
</head>
<body>

<?php  include 'jeu/tableau.php';
?>
<div class="barrLbord"><div class="barrRbord">
<table>
<td>
	<div>
		<p class="texteMenu">
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
			<a href="doc/aide.php" target="droite">Comment débuter ?</a><br>
			<a href="formu_cree_compte.php" target="droite">Créer un compte</a><br>
		</p>
		<p class="texteMenu">
			<a href="classement.php" target="droite">Classement Joueurs</a><br>
			<a href="statistiques.php" target="droite">Statistiques</a><br>
			<a href="geo.php" target="droite">Où êtes vous ?</a><br>
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
</div>
	<div><img src="http://www.jdr-delain.net/forum/templates/Chronicles/images/logo_delain.gif"></div>
</td>	
<td style="width:800px;">
	
<img src="http://www.jdr-delain.net/images/image_accueil.png" style="display:block;margin:auto;vertical-align:middle;" USEMAP="#Map">
	<MAP NAME="Map">
	<AREA SHAPE="rect"
		   HREF="forum/index.php"
		   COORDS="0,0,50,50"
		   title="Le forum pourra vous aider">

	<AREA SHAPE="rect"
		   HREF="login2.php"
		   COORDS="50,50,100,100"
		   title="Entrez par ici !">

	<AREA SHAPE="rect"
		   HREF="http://www.jdr-delain.net/wiki/index.php/Histoire"
		   COORDS="100,100,150,150"
		   title="L'histoire des souterrains">
</MAP>
	
	<i><center><hr><b>Aventurier, baladin, réfugié, bandit de grand chemin, te voici arrivé sur les terres du royaume de Delain Ou plutôt devrait-on dire sous les terres.</b><br>
Là où séveille depuis peu un mal très ancien ; dans les ténèbres de ces cavernes au plus profond desquelles Malkiar le Rouge reprend lentement ses forces et envoie ses hordes démoniaques à lassaut des extérieurs
Sauras-tu surmonter les mille épreuves qui se dresseront devant toi, affronter les dangers de cette vie souterraine ? Pourras-tu protéger les contrées extérieures de ce mal grandissant ?
<br><b>Entre, et trouve par toi-même les réponses à ces questions. </b></center>
</i><hr>

<br><b>Les souterrains de Delain</b> est un jeu de rôles en ligne, où vous pouvez incarner jusqu'à 3 aventuriers. Il se déroule en tour par tour, et les seuls outils nécessaires pour y jouer sont une connexion internet, un navigateur, et une adresse mail.
Pour plus d'infos : <a href="doc/aide.php">comment débuter</a>, <a href="regles/regles.php">les règles</a>, et <a href="faq.php">la faq</a>.<br><br>
</td>
<td>
		<div>
		<script language="JavaScript1.2">
		ejs_scroll_largeur = 400;
		ejs_scroll_hauteur = 100;
		ejs_scroll_bgcolor = '';
		ejs_scroll_background = "";
		ejs_scroll_pause_seconde = 4;
		ejs_scroll_message = new Array;
		<?php 
		if (!isset($start_news))
		{
			$start_news = 0;
		}
		if ($start_news < 0)
		{
			$start_news = 0;
		}
		
		if (!preg_match('/^[0-9]*$/i', $start_news))
		{
			echo "<p>Anomalie sur Offset !";
			exit();
		}
		$recherche = "SELECT news_cod,news_titre,news_texte,to_char(news_date,'DD/MM/YYYY') as date_news,news_auteur,news_mail_auteur 
									FROM news order by news_cod desc limit 5 offset $start_news";
		$db->query($recherche);
		$cpt = 0;
		$affiche = '';
		while($db->next_record())
		{
			$titre_news = str_replace("'","\'",$db->f("news_titre"));
			$titre_news = preg_replace("/(\r\n|\n|\r)/", "",$titre_news);
			$texte_news = str_replace("'","\'",$db->f("news_texte"));
			$texte_news = preg_replace("/(\r\n|\n|\r)/", "",$texte_news);
			$date_news = $db->f("date_news");
			$auteur_news = $db->f("news_auteur");
			$mail_auteur_news = $db->f("news_mail_auteur");
			if ($mail_auteur_news != "")
			{
				$auteur_news = "<a href=\"mailto:$mail_auteur_news\">$auteur_news</a>";
			}
			$auteur_news = preg_replace("/(\r\n|\n|\r)/", "",$auteur_news);
				$auteur_news = str_replace("'","\' ",$auteur_news);
			echo('ejs_scroll_message['.$cpt.'] = \'<br>'.$titre_news.'<br>'.$texte_news.'<br><br>'.$auteur_news.'<br>'.$date_news.'<br><br>\';');
			$cpt = $cpt + 1;
		}
		?>
		function d(texte)
		{
		document.write(texte);
		}

		d('<div id="ejs_scroll_relativ" style="position:relative;width:'+ejs_scroll_largeur+';height:'+ejs_scroll_hauteur+';background-color:'+ejs_scroll_bgcolor+';background-image:url('+ejs_scroll_background+')"></div></div>');
		d('<div id="ejs_scroll_cadre"  class="texteNorm"   style="position:relative;width:'+(ejs_scroll_largeur-8)+';height:'+(ejs_scroll_hauteur-8)+';top:-100;left:4;clip:rect(0 '+(ejs_scroll_largeur-8)+' '+(ejs_scroll_hauteur-8)+' 0)">');
		d('<div class="barrL"><div class="barrR"><p class="barrC"></p></div></div><div class="barrLbord"><div class="barrRbord"><div id="ejs_scroller_1" class="texteNorm" style="position:relative;width:'+(ejs_scroll_largeur-8)+';left:0;top:0;" CLASS=ejs_scroll>'+ejs_scroll_message[0]+'</div>');
		d('<div id="ejs_scroller_2" class="texteNorm" style="position:relative;width:'+(ejs_scroll_largeur-8)+';left:0;top:'+ejs_scroll_hauteur+';" class=ejs_scroll>'+ejs_scroll_message[1]+'</div>');
		d('</div></div><div class="barrL"><div class="barrR"><p class="barrC"></p></div></div>');

		/*d('<div class="barrLbord"><div class="barrRbord"><div id="ejs_scroll_relativ" class="blockPierre" style="position:relative;width:'+ejs_scroll_largeur+';height:'+ejs_scroll_hauteur+';background-color:'+ejs_scroll_bgcolor+';background-image:url('+ejs_scroll_background+')"></div></div>');
		d('<div id="ejs_scroll_cadre"  class="texteNorm"   style="position:absolute;width:'+(ejs_scroll_largeur-8)+';height:'+(ejs_scroll_hauteur-8)+';top:4;left:4;clip:rect(0 '+(ejs_scroll_largeur-8)+' '+(ejs_scroll_hauteur-8)+' 0)">');
		d('<div id="ejs_scroller_1" class="texteNorm" style="position:absolute;width:'+(ejs_scroll_largeur-8)+';left:0;top:0;" CLASS=ejs_scroll>'+ejs_scroll_message[0]+'</div>');
		d('<div id="ejs_scroller_2" class="texteNorm" style="position:absolute;width:'+(ejs_scroll_largeur-8)+';left:0;top:'+ejs_scroll_hauteur+';" class=ejs_scroll>'+ejs_scroll_message[1]+'</div>');
		d('</div></div>');*/
		
		ejs_scroll_mode = 1;
		ejs_scroll_actuel = 0;
		function ejs_scroll_start()
		{
		if(ejs_scroll_mode == 1)
		{
		ejs_scroller_haut = "ejs_scroller_1";
		ejs_scroller_bas = "ejs_scroller_2";
		ejs_scroll_mode = 0;
		}
		else
		{
		ejs_scroller_bas = "ejs_scroller_1";
		ejs_scroller_haut = "ejs_scroller_2";
		ejs_scroll_mode = 1;
		}
		ejs_scroll_nb_message = ejs_scroll_message.length-1;
		if(ejs_scroll_actuel == ejs_scroll_nb_message)
		ejs_scroll_suivant = 0;
		else
		ejs_scroll_suivant = ejs_scroll_actuel+1;
		if(document.getElementById)
		document.getElementById(ejs_scroller_bas).innerHTML = ejs_scroll_message[ejs_scroll_suivant];
		ejs_scroll_top = 0;
		if(document.getElementById)
		setTimeout("ejs_scroll_action()",ejs_scroll_pause_seconde*1000)
		}
		function ejs_scroll_action()
		{
		ejs_scroll_top -= 1;
		document.getElementById(ejs_scroller_haut).style.top = ejs_scroll_top;
		document.getElementById(ejs_scroller_bas).style.top = ejs_scroll_top+ejs_scroll_hauteur;
		if((ejs_scroll_top+ejs_scroll_hauteur) > 0)
		setTimeout("ejs_scroll_action()",10)
		else
		ejs_scroll_stop()
		}
		function ejs_scroll_stop()
		{
		ejs_scroll_actuel = ejs_scroll_suivant;
		ejs_scroll_start()
		}
		window.onload = ejs_scroll_start;
		</script>
	</div>
	<div class="barrLbord"><div class="barrRbord">
	<p class="texteNorm">
	<?php 
	$suite = $start_news+5;
	$prec = $start_news-5;
	if ($start_news != 0)
	{
		echo "<a href=\"news2.php?start_news=$prec\"><== plus récent</a><a href=\"news2.php?start_news=$suite\">archive des news ==></a>";
	}
	else
	{
		echo "<a href=\"news2.php?start_news=$suite\">archive des news ==></a>";
	}
	?>
	</p>
	</div></div>
	<div>
		<img src="http://www.jdr-delain.net/avatars/<?php echo $image?>" 	style="display:block;margin:auto;vertical-align:middle;" HEIGHT=150>
	</div>
</td>

</table>

</div></div>

<table>
		<td colspan="4">		<div id="hooseek_recherche"></div>
		<script type="text/javascript" src="http://www.hooseek.com/hooseek.js"></script>
		<script language="javascript">
		_hsk_logo= "";
		_hsk_url= "http://www.jdr-delain.net";
		_hsk_site = "www.jdr-delain.net";
		_hsk_ong = 148802;
		_hsk_taille = 250;
		_hsk_recherche();
		</script>
	</td>
	<td>
	<p style="text-align:center;"><?php  echo $aff_pub; ?>
	<td>
</table>
</p>
<?php 
Bordure_Tab();
?>
</body>
</html>
