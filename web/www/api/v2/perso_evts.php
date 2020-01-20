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
 * @apiSuccess {json} Evenements Tableau des données
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
    }

    if ($isauth) {
        if (isset($_REQUEST['marqueLu'])) {
            if ($_REQUEST['maruqeLu'] == 1) {
                $levt->marquePersoLu($perso->perso_cod);
            }
        }
    }

    echo json_encode(array("isauth" => $isauth, "evts" => $all_evt));
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');
