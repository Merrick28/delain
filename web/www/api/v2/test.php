<?php
$compte = new compte;
$compte->charge(2);


$auth_token = new auth_token();
$auth_token->create_token($compte);

$callapi     = new callapi();

echo "<br /><br /><br /><br /><br /><br /><br />";

$array_good = array(
            "nom"   => "monpers2",
            "force" => 12,
            "con"   => 12,
            "dex"   => 12,
            "intel" => 9,
            "voie"  => "guerrier",
            "poste" => "H",
            "race"  => 1
        );
if ($retour = $callapi->call(API_URL . '/perso', 'POST', $auth_token->at_token, $array_good)) {
    $retour = json_decode($callapi->content, true);
    $perso_temp = $retour['perso'];
    echo "Numéro de perso : " . $perso_temp . "<br />";
} else {
    die("Erreur sur création perso : " . $callapi->content);
}




if ($callapi->call(API_URL . '/perso/' . $perso_temp . '/msg_dest', 'GET', $auth_token->at_token)) {
    echo $callapi->content;
    echo "<hr />";
    $temp = json_decode($callapi->content, true);
    print_r($temp);
    //print_r($callapi);
    echo "**</pre>";
} else {
    echo "<h1>ERREUR</h1>";
    print_r($callapi);
}
