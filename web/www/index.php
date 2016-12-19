<?php
//echo "ok";
//echo "Migration du serveur OK, import de la base postgres du jeu OK, optimisations base du jeu en cours.";
//die('');
$nouvelle_version = 1;
// par défaut, on n'est pas authentifié
$verif_auth = false;

include G_CHE . "ident.php";

include "classes.php";
$db        = new base_delain;

// monstre générique
$gmon = new monstre_generique;
$gmon->getRandom();

// chargement des news
$news = new news;
$numberNews = $news->getNumber();

if (!isset($_REQUEST['start_news']))
{
    $start_news = 0;
}
else
{
    $start_news = $_REQUEST['start_news'];
}
if($start_news < 0)
{
    $start_news = 0;
}

$tabNews = $news->getNews($start_news);

require_once CHEMIN . 'choix_pub.php';
$pub = choix_pub_index();

require_once CHEMIN . '../includes/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(CHEMIN . '/../templates');

$twig     = new Twig_Environment($loader, array());
$template = $twig->loadTemplate('index.twig');

$options_twig = array(
   'URL'        => G_URL,
   'URL_IMAGES' => G_IMAGES,
   'HTTPS' => $type_flux,
   'ISAUTH' => $verif_auth,
   'AVENTURIERS_MORTS' => $db->getparm_n(64),
   'MONSTRES_MORTS' => $db->getparm_n(65),
   'FAMILIERS_MORTS' => $db->getparm_n(66),
   'GMON' => $gmon,
   'TABNEWS' => $tabNews,
   'START_NEWS' => $start_news,
   'NB_NEWS' => $numberNews,
   'PUB' => $pub
   

);
echo $template->render($options_twig);
   
   
 die('');
include 'includes/template.inc';
$t                = new template;
$t->set_file('FileRef', 'template/delain/index.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
//
// identification
//
ob_start();

$ident            = montre_formulaire_connexion($verif_auth, ob_get_contents());
ob_end_clean();
$t->set_var("IDENT", $ident);

//
//Contenu de la div de droite
//
$contenu_page = '';
include "news_v2.php";
//$contenu_page = "test";
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');
?>
ok2
