<?php
$compte = new compte;
$compte->charge(2);


$auth_token = new auth_token();
$auth_token->create_token($compte);

print_r($auth_token);

$array_good1 = array(
    "nom"   => "monperso",
    "force" => 12,
    "con"   => 12,
    "dex"   => 12,
    "intel" => 9,
    "voie"  => "guerrier",
    "poste" => "H",
    "race"  => 1
);

$array_good2 = array(
    "nom"   => "monperso2",
    "force" => 12,
    "con"   => 12,
    "dex"   => 12,
    "intel" => 9,
    "voie"  => "guerrier",
    "poste" => "H",
    "race"  => 1
);
$callapi     = new callapi();


// creation persos
/*$perso1 = json_decode($callapi->call(API_URL . '/perso', 'POST', $auth_token->at_token, $array_good1));
$perso1 = $perso1["perso"];
$perso2 = json_decode($callapi->call(API_URL . '/perso', 'POST', $auth_token->at_token, $array_good2));
$perso2 = $perso2["perso"];*/


echo "<br /><br /><br /><br /><br /><br /><br />";
/*

$levt                  = new ligne_evt;
$levt->levt_perso_cod1 = 1;
$levt->levt_attaquant  = 3;
$levt->levt_tevt_cod   = 9;
$levt->levt_lu         = 'N';
$levt->levt_texte      = '[attaquant] a méchamment attaqué [perso_cod1]';
$levt->stocke(true);
unset($levt);

$levt                  = new ligne_evt;
$levt->levt_perso_cod1 = 1;
$levt->levt_cible      = 3;
$levt->levt_tevt_cod   = 9;
$levt->levt_lu         = 'N';
$levt->levt_texte      = '[perso_cod1] a méchamment attaqué [cible]';
$levt->stocke(true);
unset($levt);

$levt                  = new ligne_evt;
$levt->levt_perso_cod1 = 1;
$levt->levt_attaquant  = 4;
$levt->levt_tevt_cod   = 9;
$levt->levt_lu         = 'N';
$levt->levt_texte      = '[attaquant] a méchamment attaqué [perso_cod1]';
$levt->stocke(true);
unset($levt);

$levt                  = new ligne_evt;
$levt->levt_perso_cod1 = 1;
$levt->levt_cible      = 4;
$levt->levt_tevt_cod   = 1;
$levt->levt_lu         = 'N';
$levt->levt_texte      = '[perso_cod1] a méchamment attaqué [cible]';
$levt->stocke(true);
unset($levt);
*/

$perso_temp = new perso;
if(!$perso_temp->charge(3))
{
    die ("erreur");
}

if ($callapi->call(API_URL . '/perso/1/evts', 'GET'))
{
    echo "<pre>";
    print_r($callapi->content);
    print_r(json_decode($callapi->content, true));
    //print_r($callapi);
    echo "</pre>";
} else
{
    echo "<h1>ERREUR</h1>";
    print_r($callapi);
}

