<?php
ob_start();


/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://jdr-delain.net/api/v2/perso
 *
 * @api {get} /compte/persos Liste les persos d'un compte
 * @apiName ComptePersos
 * @apiGroup Compte
 *
 * * @apiDescription Permet de lister les personnages d'un compte
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
 * @apiError (403) PersoExists Il existe déjà un perso avec ce nom
 * @apiError (403) NotInteger Valeur non entière
 *
 * @apiParam {Boolean} [horsPersos=false] Si à true, on n'affiche pas les persos (que les fams)
 * @apiParam {Boolean} [horsFam=false] Si à true, on n'affiche pas les fams (que les persos)
 *
 * @apiSuccess {json[]} persos Persos du compte
 * @apiSuccess {int} persos.perso_cod Numéro de perso
 * @apiSuccess {text} persos.type Type de perso 
 * @apiSuccess {text} persos.nom Nom de perso
 * @apiSuccess {json[]} sittes Persos sittes
 * @apiSuccess {int} sittes.perso_cod Numéro de perso
 * @apiSuccess {text} sittes.type Type de perso 
 * @apiSuccess {text} sittes.nom Nom de perso
 *
 *
 * @apiSuccessExample {json} Success-Response:
 *     {
  "persos": [
    {
      "perso_cod": 50,
      "type": "perso",
      "nom": "Merrick"
    },
    {
      "perso_cod": 8753659,
      "type": "monstre",
      "nom": "Elementa d'infragène (n° 8753659)"
    },
    {
      "perso_cod": 8837692,
      "type": "perso",
      "nom": "Galogan"
    },
    {
      "perso_cod": 8837693,
      "type": "perso",
      "nom": "Belena"
    },
    {
      "perso_cod": 7939836,
      "type": "familier",
      "nom": "Familier de Merrick"
    }
  ],
  "sittes": []
}
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $api      = new callapi();
    $test_api = $api->verifyCall();
    $compte   = $test_api['compte'];

    $compt_cod = $compte->compt_cod;

    $horsPersos = false;
    $horsFam    = false;

    if (isset($_REQUEST['horsPersos']))
    {
        $horsPersos = $_REQUEST['horsPersos'];
    }
    if (isset($_REQUEST['horsFam']))
    {
        $horsFam = $_REQUEST['horsFam'];
    }


    $temp['persos'] = $compte->getPersosActifs($horsPersos, $horsFam);
    $temp['sittes'] = $compte->getPersosSittes($horsPersos, $horsFam);

    // on ne sort que le numéro de perso
    $return['persos'] = array();
    $return['sittes'] = array();
    foreach ($temp['persos'] as $key => $val)
    {
        $return['persos'][$key]['perso_cod'] = $val->perso_cod;
        if($val->perso_type_perso == 1)
        {
            $type = "perso";
        }
        elseif($val->perso_type_perso == 2)
        {
            $type = "monstre";
        }
        else
        {
            $type = "familier";
        }
        $return['persos'][$key]['type'] = $type;
        $return['persos'][$key]['nom'] = $val->perso_nom;
    }
    foreach ($temp['sittes'] as $key => $val)
    {
        $return['sittes'][$key]['perso_cod'] = $val->perso_cod;
        if($val->perso_type_perso == 1)
        {
            $type = "perso";
        }
        elseif($val->perso_type_perso == 2)
        {
            $type = "monstre";
        }
        else
        {
            $type = "familier";
        }
        $return['persos'][$key]['type'] = $type;
        $return['persos'][$key]['nom'] = $val->perso_nom;
    }

    ob_end_clean();

    echo json_encode($return);
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');
