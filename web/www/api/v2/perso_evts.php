<?php
ob_start();


/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://jdr-delain.net/api/v2/perso/:id/evts
 *
 * @api {get} /perso/:id/evts Evenements d'un perso
 * @apiName PersoEvts
 * @apiGroup Perso
 *
 * @apiDescription Liste des evts d'un perso
 *
 * @apiHeader {string} [X-delain-auth] Token
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "X-delain-auth": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 *
 *
 * @apiParam {Integer} id Numéro du perso
 * @apiParam {Integer} [marqueLu] si égal à 1, les evts sont marqués comme lus

 * @apiSuccess {json} Tableau des données
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "perso": "2"
 *     }
 */


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // on regarder si le compte a le droit
    // de regarder ce perso
    $api       = new callapi();
    $test_api  = $api->verifyCall();
    $compte    = $test_api['compte'];
    $compt_cod = $compte->compt_cod;

    if (!isset($_REQUEST['visu_perso'])) {
        header('HTTP/1.0 405 MissingArgument');
        die('visu_perso non transmis');
    }
    if (filter_var($_REQUEST['visu_perso'], FILTER_VALIDATE_INT) === false) {
        header('HTTP/1.0 405 MissingArgument');
        die('visu_perso non entier');
    }





    $perso = new perso;
    if (!$perso->charge($_REQUEST['visu_perso'])) {
        header('HTTP/1.0 405 MissingArgument');
        die('perso non trouvé');
    }


    if (!$compte->autoriseJouePerso($perso->perso_cod)) {
        header('HTTP/1.0 405 MissingArgument');
        die('perso non autorisé pour ce compte');
    }

    $levt = new ligne_evt;
    $all_evt = $levt->getByPersoNonLu($perso->perso_cod);

    foreach ($all_evt as $key => $val) {
        // reformatage du texte
        $texte = str_replace('[perso_cod1]', $perso->perso_nom, $val->levt_texte);
        $texte = str_replace('[attaquant]', $val->perso_attaquant->perso_nom, $texte);
        $texte = str_replace('[cible]', $val->perso_cible->perso_nom, $texte);
        $val->levt_texte = $texte;
        // suppression des persos
        $val->perso_cod_attaquant = $val->perso_attaquant->perso_cod;
        $val->perso_cod_cible = $val->perso_cible->perso_cod;
        //unset($val->perso_cible);
        //unset($val->perso_attaquant);
    }

    if (isset($_REQUEST['marqueLu'])) {
        if ($_REQUEST['maruqeLu'] == 1) {
            $levt->marquePersoLu($perso->perso_cod);
        }
    }
    echo json_encode($all_evt);
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');
