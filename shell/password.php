<?php
require_once "../web/www/includes/delain_header.php";


$compte = new compte();
if (!$compte->charge($argv[1]))
{
    die('Erreur sur chargement de compte' . PHP_EOL);
}
$compte->compt_passwd_hash = crypt($argv[2], sha1(microtime(true)));
$compte->stocke();
echo "Le mot de passe du compte " . $compte->compt_nom . " a été changé" . PHP_EOL;