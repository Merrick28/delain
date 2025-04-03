<?php
$nouvelle_version = 1;


// déjà, on rejette les false news id
/*if(isset($_REQUEST['start_news']))
{
    if(!is_int($_REQUEST['start_news']))
    {
        die('test');
    }
}*/

// par défaut, on n'est pas authentifié
$verif_auth      = false;
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;


// parametres
$param = new parametres;

// monstre générique
$gmon = new monstre_generique;
$gmon->getRandom();


$news       = new news();
$start_news = $news->clean_start_news();

$callapi = new callapi();
if ($callapi->call(API_URL . '/news?start_news=' . $start_news,
                   'GET'))
{
    $tabNews = json_decode($callapi->content);
} else
{
    die('Erreur sur appel API ' . $callapi->content);
}

require_once CHEMIN . 'choix_pub.php';
$pub = choix_pub_index();

/* Finances */

$finances = new finances;
$workDate  = explode('-', $_REQUEST['change_date']);
$workYear  = date('Y');
$workMonth = date('m');
$total     = $finances->getTotalByDate($workMonth, $workYear);

/** @var Twig_Loader_Filesystem $twig */
$template     = $twig->load('index.twig');
$options_twig = array(

    'AVENTURIERS_MORTS' => $param->getparm(64),
    'MONSTRES_MORTS'    => $param->getparm(65),
    'FAMILIERS_MORTS'   => $param->getparm(66),
    'GMON'              => $gmon,
    'TABNEWS'           => $tabNews->news,
    'START_NEWS'        => $start_news,
    'NB_NEWS'           => $tabNews->numberNews,
    'PUB'               => $pub,
    'TOTAL_FINANCES'    => $total


);
echo $template->render(array_merge($options_twig_defaut, $options_twig));


