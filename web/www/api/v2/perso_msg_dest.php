<?php
ob_start();


/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://jdr-delain.net/api/v2/perso/:id/msg_dest
 *
 * @api {get} /perso/:id/msg_dest Messages dont le perso est destinataire
 * @apiName PersoMsgDest
 * @apiGroup messages
 *
 * @apiDescription Liste les messages dont le perso est destinatire
 *
 * @apiHeader {string} X-delain-auth Token
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "X-delain-auth": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 *
 *
 * @apiParam {Integer} id Numéro du perso
 * @apiSuccess {json} message Détail du message
 * @apiSuccess {json} messages_exp Détail de l'expéditeur
 * @apiSuccess {json} messages_dest Liste des destinataires
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 * [
 *  {
 *    "message": {
 *      "msg_cod": 148,
 *      "msg_date": "2020-01-20",
 *      "msg_titre": "Vous êtes indiscret...",
 *      "msg_corps": "texte du message",
 *      "msg_date2": "2020-01-20 14:08:13+00",
 *      "msg_guilde": "N",
 *      "msg_guilde_cod": null,
 *      "msg_init": 148,
 *      "exp_perso_cod": null,
 *      "tabDest": null
 *    },
 *    "messages_exp": {
 *      "emsg_perso_cod": 1,
 *      "emsg_archive": "N",
 *      "emsg_lu": 0
 *    },
 *    "messages_dest": [
 *      {
 *        "dmsg_perso_cod": 72,
 *        "dmsg_lu": "N",
 *        "dmsg_archive": "N"
 *      }
 *    ]
 *  }
 * ]
 */


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // on regarder si le compte a le droit
    // de regarder ce perso
    include "fonctions_api.php";
    $api      = new callapi();
    $test_api = $api->verifyCall();
    $compte = $test_api['compte'];
    $compt_cod = $compte->compt_cod;

    $perso = test_perso();
    if (!$compte->autoriseJouePerso($perso->perso_cod)) {
        header('HTTP/1.0 403 UnauthorizedPerso');
        die('Perso non autorisé pour ce compte');
    }

    $test_offset_limit = test_offset_limit();
    $offset            = $test_offset_limit['offset'];
    $limit             = $test_offset_limit['limit'];

    $messages_dest = new messages_dest;
    $all_messages = $messages_dest->getByPerso($perso->perso_cod);

    foreach ($all_messages as $key => $val) {
        unset($val->messages_exp->emsg_cod);
        unset($val->messages_exp->emsg_msg_cod);
        if (!$compte->autoriseJouePerso($val->messages_exp->emsg_perso_cod)) {
            unset($val->messages_dest->emsg_archive);
            unset($val->messages_dest->emsg_lu);
        }
        foreach ($val->messages_dest as $key2 => $val2) {
            unset($val2->dmsg_cod);
            unset($val2->dmsg_msg_cod);
            unset($val2->dmsg_efface);
            if (!$compte->autoriseJouePerso($val2->dmsg_perso_cod)) {
                unset($val2->dmsg_lu);
                unset($val2->dmsg_archive);
                unset($val2->dmsg_msg_cod);
            }
        }
    }

    echo json_encode($all_messages);
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');
