<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
// chargement des news
    $news       = new news;
    $numberNews = $news->getNumber();


    if (!isset($_REQUEST['start_news']))
    {
        $start_news = 0;
    } else
    {
        $start_news = (int)$_REQUEST['start_news'];
    }
    if ($start_news < 0)
    {
        $start_news = 0;
    }
    $tabNews = $news->getNews($start_news);
    echo json_encode($tabNews);
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');