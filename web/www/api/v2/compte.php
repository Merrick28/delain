<?php
ob_start();

/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://www.jdr-delain.net/api/v2/compte/
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
 * @apiError (403) Token non UUID Le token n'est pas un UUID
 *
 * @apiSuccess {json} Tableau des données
 *
 *  * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "compte": "2",
 *       "token": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 */

// on commence par rechercher le compte
$headers = getallheaders();
if (!isset($headers['X-delain-auth']))
{
    header('HTTP/1.0 403 NoToken');
    die('Token non transmis');
}

$UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
if(!preg_match($UUIDv4, $headers['X-delain-auth']))
{
    {
        header('HTTP/1.0 403 NoToken');
        die('Token non UUID');
    }
}

$auth_token = new auth_token();

if (!$auth_token->charge($headers['X-delain-auth']))
{
    header('HTTP/1.0 403 TokenNotFound');
    die('Token non trouvé');
}

$compte = new compte;
if (!$compte->charge($auth_token->at_compt_cod))
{
    header('HTTP/1.0 403 AccountNotFound');
    die('Compte non trouvé');
}

$compt_cod = $compte->compt_cod;

/*
$admin = 'N';
if ($db->is_admin_monstre($compt_cod))
{
    die('compte monstre');
}
if ($db->is_admin($compt_cod))
{
    die('compte admin');
}
*/

$return = '';


