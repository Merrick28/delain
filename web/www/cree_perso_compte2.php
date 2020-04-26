<?php
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
// on appelle l'api de création de perso
$api = new callapi();

$callapi     = new callapi();
$array_perso = array(
    "nom"   => $_REQUEST['nom'],
    "sexe" => $_REQUEST['sexe'],
    "force" => $_REQUEST['force'],
    "con"   => $_REQUEST['con'],
    "dex"   => $_REQUEST['dex'],
    "intel" => $_REQUEST['intel'],
    "voie"  => $_REQUEST['voie'],
    "poste" => $_REQUEST['poste'],
    "race"  => $_REQUEST['race'],
);
if ($callapi->call(API_URL . '/perso', 'POST', $_SESSION['api_token'], $array_perso))
{
    $error_message     = '';
    $perso_json        = $callapi->content;
    $perso_json        = json_decode($perso_json, true);
    $nouveau_perso_cod = $perso_json['perso'];

    $nouveau_perso = new perso();
    $nouveau_perso->charge($nouveau_perso_cod);

    /* affichage des compétences par type */
    $tc    = new type_competences();
    $alltc = $tc->getAll();

    foreach ($alltc as $key => $currenttc)
    {
        // on initialise le tableau des comp
        // pour éviter un plantage de twig
        $alltc[$key]->comp = array();
        $competences       = new competences();
        $tabcomp           = $competences->getByTypeCompetence($currenttc->typc_cod);

        foreach ($tabcomp as $valcomp => $currentcomp)
        {
            $logger->debug(print_r($currentcomp, true));
            $mycomp = new perso_competences();
            if ($mycomp->getByPersoComp($nouveau_perso->perso_cod, $currentcomp->comp_cod))
            {
                $logger->debug('FOUND');
                $alltc[$key]->comp[$valcomp]['comp_libelle']       = $currentcomp->comp_libelle;
                $alltc[$key]->comp[$valcomp]['pcomp_modificateur'] = $mycomp->pcomp_modificateur;
            } else
            {
                $logger->debug('not found');
            }
        }
    }
} else
{
    $error_message = $callapi->content;
}

$template     = $twig->load('cree_perso_compte2.twig');
$options_twig = array(
    'ERROR_MESSAGE'    => $error_message,
    'REQUEST'          => $_REQUEST,
    'PERSO'            => $nouveau_perso,
    'ALLTC'            => $alltc

);

echo $template->render(array_merge($options_twig_defaut, $options_twig));