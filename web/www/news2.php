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
$n_pub[1] = '<script type="text/javascript"><!--
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
';
$n_pub[2] = '<!-- Tag PromoBenef site membre N°24301-->
<script type="text/javascript">
<!--
var promobenef_site = "24301";
var promobenef_minipub = "1";
var promobenef_format = "1";
//-->
</script>
<script type="text/javascript" src="http://www.promobenef.com/pub/"></script>
<noscript><a href="http://www.promobenef.com/">PromoBenef : r&eacute;gie publicitaire<img src="http://www.promobenef.com/no_js/?sid=24301&amp;fid=1" width="0" height="0" border="0" /></a></noscript>';
$n_pub[3] = '<!-- Tag PromoBenef site membre N°24301-->
<script type="text/javascript">
<!--
var promobenef_site = "24301";
var promobenef_minipub = "1";
var promobenef_format = "1";
//-->
</script>
<script type="text/javascript" src="http://www.promobenef.com/pub/"></script>
<noscript><a href="http://www.promobenef.com/">PromoBenef : r&eacute;gie publicitaire<img src="http://www.promobenef.com/no_js/?sid=24301&amp;fid=1" width="0" height="0" border="0" /></a></noscript>';
$n_pub[4] = '<script type="text/javascript"><!--
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
</script>';
//$n_pub[3] = '<!--Code à insérer CibleClick : Loterie 9 sur 49 (Siteunder) --><script language="JavaScript" src="http://ad.cibleclick.com/cibles/banniere/script.cfm/script.js?site_id=51908922&friend_id=443102863&banniere_id=26623"></script><!-- fin du Code à insérer CibleClick : Loterie 9 sur 49 (Siteunder) -->';
$nb_pub = rand(1,4);
if(isset($origine))
	if($origine='google')
		$nb_pub = 1;
$aff_pub = $n_pub[$nb_pub];
$nb_pub = rand(1,100);
if ($nb_pub > 95)
	$aff_pub = '<iframe src="http://www.eurobarre.com/webmaster/pub.php?p=658205096351" width="468" height="60" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>';
/// FIN PUBLICITES
	

$aff_pub = '<script type="text/javascript" src="http://fr.slidein.clickintext.net/?a=267"></script>';
?>
<head>
<link rel="stylesheet" type="text/css" href="style.php">
<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
</head>
<body>

<?php include 'jeu_test/tableau.php';
Titre('Bienvenue dans les souterrains')?>
<div class="barrLbord"><div class="barrRbord">
<p style="text-align:center;"><?php  echo $aff_pub; ?>
</p>
<i><hr>Aventurier, baladin, réfugié, bandit de grand chemin, te voici arrivé sur les terres du royaume de Delain Ou plutôt devrait-on dire sous les terres.<br>
Là où séveille depuis peu un mal très ancien ; dans les ténèbres de ces cavernes au plus profond desquelles Malkiar le Rouge reprend lentement ses forces et envoie ses hordes démoniaques à lassaut des extérieurs
Sauras-tu surmonter les mille épreuves qui se dresseront devant toi, affronter les dangers de cette vie souterraine ? Pourras-tu protéger les contrées extérieures de ce mal grandissant ?
Entre, et trouve par toi-même les réponses à ces questions. </i><hr>
<b>Les souterrains de Delain</b> est un jeu de rôles en ligne, où vous pouvez incarner jusqu'à 3 aventuriers. Il se déroule en tour par tour, et les seuls outils nécessaires pour y jouer sont une connexion internet, un navigateur, et une adresse mail.
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
$db = new base_delain;
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
	if ($cpt == 2)
	{
		Bordure_Tab();
		$nb_pub = rand(1,5);
		if ($nb_pub <= 3)
		{
			?>
			<div class="barrLbord"><div class="barrRbord">
			<p class="texteNorm" style="text-align:center;">
			<a href="http://www.pyrenet.fr" target="_blank"><img src="images/pyrenet.gif" align="absmiddle" border="0"></a><img src="images/del.gif" align="absmiddle" border="0" width="10"><b>Les souterrains de Delain</b> sont hébergés par la société <a href="http://www.pyrenet.fr" target="_blank">Pyrenet</a>
			</p>
			</div></div>
			<?php 
		}
		else
		{
			?>
			<!--Code à insérer CibleClick : CD WOW! --><script language="JavaScript" src="http://ad.cibleclick.com/cibles/banniere/script.cfm/script.js?site_id=33698117&friend_id=443102863&banniere_id=26943"></script><!-- fin du Code à insérer CibleClick : CD WOW! -->
			<?php 
		}
		
	}
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
 <div class="barrLbord"><div class="barrRbord">
        <p class="texteNorm" style="text-align:right;">
Conception et hébergement par <a href="http://www.sdewitte.net" target="_blank">sdewitte.net</a>
</p></div></div>

<?php 
Bordure_Tab();
?>

</body>
</html>
