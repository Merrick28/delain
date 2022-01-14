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
 * @apiParam {Integer} [marqueLu] si égal à 1, tous les evts sont marqués comme lus (nécessite l'authentification)
 * 
 * @apiSuccess {boolean} isauth Vue complète des événements, en étant authentifié
 * @apiSuccess {json[]} evts Liste des événements
 * @apiSuccess {integer} evts.levt_cod Code de l'événement
 * @apiSuccess {integer} evts.levt_tevt_cod Code du type d'événement
 * @apiSuccess {date} evts.levt_date Date de l'événement
 * @apiSuccess {integer} evts.levt_perso_cod1 Numéro du perso concerné
 * @apiSuccess {texte} evts.levt_texte Texte de l'événement
 * @apiSuccess {char} [evts.levt_lu] Evénement lu ?
 * @apiSuccess {char} [evts.levt_visible] Evénement visible par tous ?
 * @apiSuccess {char} evts.levt_attaquant Code perso de l'attaquant
 * @apiSuccess {char} evts.levt_cible Code perso de la cible
 * 
 * 
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 * {
 * "isauth": true,
 * "evts": [
 *   {
 *     "levt_cod": 959930627,
 *     "levt_tevt_cod": 9,
 *     "levt_date": "2020-01-21 05:00:26.418928+01",
 *     "levt_perso_cod1": 50,
 *     "levt_texte": "Nuée de chauves souris (n° 8840755) a frappé Merrick avec Griffes, infligeant 0 points de dégâts.",
 *     "levt_lu": "N",
 *     "levt_visible": "O",
 *     "levt_attaquant": 8840755,
 *     "levt_cible": 50
 *   },
 *   {
 *     "levt_cod": 959930625,
 *     "levt_tevt_cod": 8,
 *     "levt_date": "2020-01-21 05:00:26.418928+01",
 *     "levt_perso_cod1": 50,
 *     "levt_texte": "Merrick a raté une esquive... ",
 *     "levt_lu": "N",
 *     "levt_visible": "N",
 *     "levt_attaquant": null,
 *     "levt_cible": null
 *    }
 *  ]
 * }
 */


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // on regarder si le compte a le droit
    // de regarder ce perso
    $api      = new callapi();
    $isauth   = true;

    include "fonctions_api.php";
    $perso = test_perso();

    $test_api = $api->verifyCallIsAuth();

    if ($test_api === false) {
        $isauth = false;
    } else {
        $compte = $test_api['compte'];
        if (!$compte->autoriseJouePerso($perso->perso_cod)) {
            $isauth = false;
        }
    }

    $test_offset_limit = test_offset_limit();
    $offset            = $test_offset_limit['offset'];
    $limit             = $test_offset_limit['limit'];

    $levt    = new ligne_evt;
    $all_evt = $levt->getByPerso($perso->perso_cod, $offset, $limit);

    foreach ($all_evt as $key => $val) {
        if ($isauth) {
            // on prend la ligne de l'événement
            $texte = str_replace('[perso_cod1]', $perso->perso_nom, $val->levt_texte);
        } else {
           
            // non auth, on prend la ligne du type d'événement
            $texte = str_replace('[perso_cod1]', $perso->perso_nom, $val->tevt->tevt_texte);
        }

        $texte           = str_replace('[attaquant]', $val->perso_attaquant->perso_nom, $texte);
        $texte           = str_replace('[cible]', $val->perso_cible->perso_nom, $texte);
        $val->levt_texte = $texte;
        // suppression des persos
        $val->perso_cod_attaquant = $val->perso_attaquant->perso_cod;
        $val->perso_cod_cible     = $val->perso_cible->perso_cod;
        unset($val->perso_cible);
        unset($val->perso_attaquant);
        unset($val->tevt);
        unset($val->levt_type_per1);
        unset($val->levt_type_per1);
        unset($val->levt_type_per2);
        unset($val->levt_perso_cod2);
        unset($val->levt_nombre);
        unset($val->levt_parametres);
        unset($val->perso_cod_attaquant);
        unset($val->perso_cod_cible);
        if (!$isauth)
        {
             if($val->levt_visible == 'N')
            {
                unset($val);
            }
        }
        
    }

    if ($isauth) {
        if (isset($_REQUEST['marqueLu'])) {
            if ($_REQUEST['marqueLu'] == 1) {
                $levt->marquePersoLu($perso->perso_cod);
            }
        }
    }

    echo json_encode(array("isauth" => $isauth, "evts" => $all_evt));
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');
