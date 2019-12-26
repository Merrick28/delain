<?php
/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://www.jdr-delain.net/api/v2/auth/
 * @api {post} /auth/ Request a new token
 * @apiName requestToken
 * @apiGroup Auth
 * @apiDescription Permet de demander un token d'identification
 * qu'il faudra faire suivre pour les prochaines demandes
 * @apiParam {String} login Login du compte
 * @apiParam {String} password Password du compte
 * @apiParamExample {json} Request-Example:
 *     {
 *       "login": "monlogin",
 *       "password": "monpassword"
 *     }
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "Content-type": "application/json"
 *     }
 *  @apiError (403) FailedAuth Authentification échouée
 * @apiSuccess {String} compte Numéro du compte
 * @apiSuccess {String} token  Token à garder
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "compte": "2",
 *       "token": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 */


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // on récupère les variables json
    $inputJSON = file_get_contents('php://input');
    $input     = json_decode($inputJSON, TRUE);

    if(!isset($input))
    {
        header('HTTP/1.0 403 Forbidden');
        die('Pas de token.');
    }

    if(!isset($input['login']))
    {
        header('HTTP/1.0 403 Forbidden');
        die('Pas de login.');
    }

    if(!isset($input['password']))
    {
        header('HTTP/1.0 403 Forbidden');
        die('Pas de password.');
    }

    $login     = $input['login'];
    $password  = $input['password'];



    $compte = new compte;
    if ($compte->getByLoginPassword($login, $password))
    {
        // on est authentifié
        $auth_token = new auth_token;

        $token  = $auth_token->create_token($compte);
        $result = array(
            "compte" => $compte->compt_cod,
            "token"  => $token
        );


        die(json_encode($result));
    } else
    {
        header('HTTP/1.0 403 Forbidden');
        die('Authentification échouée.');
    }
}
/**
 * @apiVersion 2.0.0
 * @apiSampleRequest https://www.jdr-delain.net/api/v2/auth/
 * @api {delete} /auth/ Deletes an existing token
 * @apiName deleteToken
 * @apiGroup Auth
 * @apiDescription Supprime le token
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "X-delain-auth": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 * @apiError (403) NoToken Token non transmis
 * @apiError (403) TokenNotFound Token non trouvé dans la base
 * @apiError (403) AccountNotFound Compte non trouvé dans la base
 * @apiError (403) Token non UUID Le token n'est pas un UUID
 *
 */
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
    $api = new callapi();
    $test_api = $api->verifyCall();

    $auth_token = $test_api['token'];
    $auth_token->delete();
    die('Token supprimé');


}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');


