<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head><title>Les news du site</title>
<meta name="keywords" content="jeu,rôle,delain,ligne,gratuit,multi-joueur">
<meta name="description" content="Les souterrains de Delain, jeu de rôle gratuit en ligne">
<link href="https://plus.google.com/107447277968158531530" rel="publisher" /><script type="text/javascript">
window.___gcfg = {lang: 'fr'};
(function() 
{var po = document.createElement("script");
po.type = "text/javascript"; po.async = true;po.src = "https://apis.google.com/js/plusone.js";
var s = document.getElementsByTagName("script")[0];
s.parentNode.insertBefore(po, s);
})();</script>
</head>
<html>   
<?php 
include "classes.php";
//
// DEBUT PUBLICITES
//
//require G_CHE . "choix_pub.php";
require G_CHE . "choix_pub.php";
$publicite = choix_pub_index();
/// FIN PUBLICITES

$db = new base_delain;
$recherche = "SELECT gmon_avatar FROM monstre_generique where gmon_avatar is not null and gmon_avatar != 'defaut.png' order by random() limit 1";
$db->query($recherche);
$db->next_record();
$image = $db->f("gmon_avatar");
?>
<head>
<link rel="stylesheet" type="text/css" href="style.php">
<link rel="shortcut icon" href="<?php echo $type_flux .G_URL;?>drake_head_red.ico" type="image/gif">
</head>
<body>

<?php include 'jeu/tableau.php';
Titre('Bienvenue dans les souterrains')?>
<div class="barrLbord"><div class="barrRbord">
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
	<p style="text-align:center;"><?php  echo $publicite; ?>
	<td>
</table>
</p>

<table>
<td><img src="<?php echo $type_flux .G_URL;?>logo_delain.gif"></td>	
<td style="width:800px;"><i><center><hr><b>Aventurier, baladin, réfugié, bandit de grand chemin, te voici arrivé sur les terres du royaume de Delain... Ou plutôt devrait-on dire... sous les terres.</b><br>
Là où s'éveille depuis peu un mal très ancien ; dans les ténèbres de ces cavernes au plus profond desquelles Malkiar le Rouge reprend lentement ses forces et envoie ses hordes démoniaques à l'assaut des extérieurs
Sauras-tu surmonter les mille épreuves qui se dresseront devant toi, affronter les dangers de cette vie souterraine ? Pourras-tu protéger les contrées extérieures de ce mal grandissant ?
<br><b>Entre, et trouve par toi-même les réponses à ces questions. </b></center></i><hr></td>
<td>
<?php 
$nb_pub = rand(1,6);
if($nb_pub == 1)
{
?>
<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FLes-souterrains-de-Delain%2F185893811435175&amp;width=292&amp;colorscheme=dark&amp;show_faces=false&amp;stream=false&amp;header=false&amp;height=62" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:62px;" allowTransparency="true"></iframe>
<?php 
}
elseif($nb_pub == 2)
{
?>
<g:plus href="https://plus.google.com/107447277968158531530" size="badge"></g:plus>
<?php 
}
else
{
?>
<img src="http://images.jdr-delain.net/avatars/<?php echo $image?>" style="max-width: 200px; max-height: 200px;">
<?php 
}
?>
</td>

</table>
<br><b>Les souterrains de Delain</b> est un jeu de rôles en ligne, où vous pouvez incarner jusqu'à 3 aventuriers. Il se déroule en tour par tour, et les seuls outils nécessaires pour y jouer sont une connexion internet, un navigateur, et une adresse mail.
Pour plus d'infos : <a href="doc/aide.php">comment débuter</a>, <a href="regles/regles.php">les règles</a>, et <a href="faq.php">la faq</a>.<br><br>
</div></div>
<?php 
Bordure_Tab();
?>
<div class="blockPierre"><br><br></div>
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
$recherche = "SELECT news_cod,news_titre,news_texte,to_char(news_date,'DD/MM/YYYY') as date_news,news_auteur,news_mail_auteur FROM news order by news_cod desc limit 5 offset $start_news";
$db->query($recherche);
$cpt = 0;
while($db->next_record())
{
	$cpt = $cpt + 1;
	
	$titre_news = $db->f("news_titre");
	$texte_news = $db->f("news_texte");
	$date_news = $db->f("date_news");
	$auteur_news = $db->f("news_auteur");
	$mail_auteur_news = $db->f("news_mail_auteur");
	/*if ($cpt == 2)
	{
		$nb_pub = rand(1,5);
		if ($nb_pub == 1)
		{
			Bordure_Tab();
			?>
			<center><script language='JavaScript' src='http://www.clicjeux.net/banniere.php?id=390'></script></center>
			<?
		}
		
	}*/
	Titre($titre_news);
	?>
	<div class="barrLbord"><div class="barrRbord">
	<p class="texteNorm" style="text-align:right;">
	<?php echo $date_news?>
	</p>
	<p class="texteNorm">
	<?php echo $texte_news?>
	</p>
	<p class="texteNorm" style="text-align:right;">
	<?php 
	if ($mail_auteur_news != "")
	{
		echo "<a href=\"mailto:$mail_auteur_news\">$auteur_news</a>";
	}
	else
	{
		echo $auteur_news;
	}
	?>
	</p>
	</div></div>
	<?php 
}
Bordure_Tab();
?>
<div class="barrLbord"><div class="barrRbord">
<p class="texteNorm">
<?php 
$suite = $start_news+5;
$prec = $start_news-5;
if ($start_news != 0)
{
	echo "<a href=\"news2.php?start_news=$prec\"><== plus récent</a><img src=\"" . G_IMAGES . "del.gif\" width=\"50\" height=\"1\"><a href=\"news2.php?start_news=$suite\">archive des news ==></a>";
}
else
{
	echo "<a href=\"news2.php?start_news=$suite\">archive des news ==></a>";
}
?>
</p>
</div></div>
<?php 
Bordure_Tab();
?>
<!--
 <div class="barrLbord"><div class="barrRbord">
        <p class="texteNorm" style="text-align:right;">
Conception et hébergement par <a href="http://www.sdewitte.net" target="_blank">sdewitte.net</a>
</p></div></div>
-->
</body>
</html>
