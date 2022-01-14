<?php
/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://jdr-delain.net/api/v2/news
 *
 * @api {get} /news/ Retourne les news
 * @apiName news
 * @apiGroup News
 *
 * @apiDescription Permet de demander les news (par 5)
 * @apiParam {Number} [start_news=0] Numéro de la première news demandée pour pagination
 *
 *
 * @apiSuccess {json} Tableau des données
 *

 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
// chargement des news
    $news       = new news;
    $numberNews = $news->getNumber();


    $start_news = $news->clean_start_news();
    $tabNews    = $news->getNews($start_news);
    $numberNews = $news->getNumber();
    echo json_encode(array("numberNews" => $numberNews, "news" => $tabNews));
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');