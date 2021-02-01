<?php
ob_start();

/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://jdr-delain.net/api/v2/compte
 *
 * @api {get} /compte/ retourne les détails du compte
 * @apiName CompteDetail
 * @apiGroup Compte
 *
 * * @apiDescription Permet de demander le détail du compte
 *
 * @apiHeader {string} X-delain-auth Token
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "X-delain-auth": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 *
 * @apiError (403) NoToken Token non transmis
 * @apiError (403) TokenNotFound Token non trouvé dans la base
 * @apiError (403) AccountNotFound Compte non trouvé dans la base
 * @apiError (403) TokenNonUUID Le token n'est pas un UUID
 *
 * @apiSuccess {json} compte Détail du compte
 * @apiSuccess {integer} compte.compt_cod Numéro du compte
 * @apiSuccess {text} compte.compt_nom Nom du compte
 * @apiSuccess {text} compte.compt_mail Adresse e-mail 
 * @apiSuccess {char} compte.compt_actif Compte actif ? (O = Oui)
 * @apiSuccess {date} compte.compt_dcreat Date de création
 * @apiSuccess {date} compte.compt_der_connex Date de dernière connexion
 * @apiSuccess {char} compte.compt_hibernation hibernation ('O ' = Oui, 'T' = fin, null ou 'N' = non)
 * @apiSuccess {date} compte.compt_dfin_hiber Date fin hibernation 
 * @apiSuccess {date} compte.compt_ddeb_hiber Date début hibernation
 * @apiSuccess {integer} compte.compt_der_news Numéro de la dernière news lue
 * @apiSuccess {char} compte.compt_quatre_perso Compte éligible au 4e perso ?
 * @apiSuccess {integer} compte.compt_type_quatrieme Type de 4e (1 = perso, 2 = monstre)
 * 
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 * {
 *   "compte": {
 *     "compt_cod": 4,
 *     "compt_nom": "Moncompte",
 *     "compt_mail": "me@myself.com",
 *     "compt_actif": "O",
 *     "compt_dcreat": "2003-08-11 14:15:59.644098+02",
 *     "compt_der_connex": "2020-01-21 09:28:23+01",
 *     "compt_hibernation": null,
 *     "compt_dfin_hiber": null,
 *     "compt_ddeb_hiber": null,
 *     "compt_der_news": 710,
 *     "compt_quatre_perso": "O",
 *     "compt_type_quatrieme": 2
 *    }
 * }
 */

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // on commence par rechercher le compte

    $api      = new callapi();
    $test_api = $api->verifyCall();

    $compte = $test_api['compte'];

    $compt_cod = $compte->compt_cod;

    // on efface tout ce qu'on ne veut pas afficher
    unset($compte->compt_password);
    unset($compte->compt_validation);
    unset($compte->compt_habilitation);
    unset($compte->compt_commentaire);
    unset($compte->compt_renvoye);
    unset($compte->compt_monstre);
    unset($compte->compt_testeur);
    unset($compte->compt_admin);
    unset($compte->compt_acc_charte);
    unset($compte->compt_confiance);
    unset($compte->compt_quete);
    unset($compte->compt_envoi_mail);
    unset($compte->compt_envoi_mail_message);
    unset($compte->compt_ligne_perso);
    unset($compte->compt_wikidev);
    unset($compte->compt_compte_lie);
    unset($compte->compt_der_perso_cod);
    unset($compte->compt_fb);
    unset($compte->compt_twitter);
    unset($compte->compt_google);
    unset($compte->compt_frameless);
    unset($compte->compt_clef_forum);
    unset($compte->compt_validite_clef_forum);
    unset($compte->compt_nombre_clef_forum);
    unset($compte->compt_phashword);
    unset($compte->compt_clef_reinit_mdp);
    unset($compte->compt_passwd_hash);
    unset($compte->compt_ip);
    unset($compte->compt_vue_desc);
    unset($compte->compt_envoi_mail_frequence);

    $return = array(
        "compte" => $compte
    );
    echo json_encode($return);
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');


