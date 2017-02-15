<?php
$nouvelle_version = 1;

// par défaut, on n'est pas authentifié
$verif_auth = false;
include G_CHE . "ident.php";

// parametres
$param = new parametres;

// monstre générique
$gmon = new monstre_generique;
$gmon->getRandom();

// chargement des news
$news       = new news;
$numberNews = $news->getNumber();

if (!isset($_REQUEST['start_news']))
{
    $start_news = 0;
}
else
{
    $start_news = $_REQUEST['start_news'];
}
if ($start_news < 0)
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
    'URL'               => G_URL,
    'URL_IMAGES'        => G_IMAGES,
    'HTTPS'             => $type_flux,
    'ISAUTH'            => $verif_auth,
    'PERCENT_FINANCES'  => $percent_finances,
    'AVENTURIERS_MORTS' => $param->getparm(64),
    'MONSTRES_MORTS'    => $param->getparm(65),
    'FAMILIERS_MORTS'   => $param->getparm(66),
    'GMON'              => $gmon,
    'TABNEWS'           => $tabNews,
    'START_NEWS'        => $start_news,
    'NB_NEWS'           => $numberNews,
    'PUB'               => $pub,


);
echo $template->render($options_twig);
