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
 * @apiSuccess {json} Tableau des données
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "compte": "2",
 *       "token": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 */

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // on commence par rechercher le compte

    $api      = new callapi();
    $test_api = $api->verifyCall();

    $compte = $test_api['compte'];

    $compt_cod = $compte->compt_cod;


    /*if ($compte->is_admin_monstre())
    {
        die('compte monstre');
    }
    if ($compte->is_admin())
    {
        die('compte admin');
    }*/


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

    $return = array(
        "compte" => $compte
    );
    echo json_encode($return);
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');


