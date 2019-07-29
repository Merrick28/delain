<?php
$nouvelle_version = 1;


//$toolbar->addDataCollector(new \Fabfuel\Prophiler\DataCollector\PDO());

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
// si tentative de hack, on affiche la page par défaut
// ça évite les logs, et ça permet d'afficher quand
// même de la pub :-)
if ( (int) $start_news !== $start_news )
{
    $start_news = 0;
}

$tabNews = $news->getNews($start_news);




require_once CHEMIN . 'choix_pub.php';
$pub = choix_pub_index();



$template = $twig->load('index.twig');
$options_twig = array(
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
echo $template->render(array_merge($options_twig_defaut,$options_twig));


