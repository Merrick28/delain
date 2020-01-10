<?php


require __DIR__ . '/conf.php';
require G_CHE . '../vendor/autoload.php';

header('Content-type: text/html; charset=utf-8');

$profiler = new \Fabfuel\Prophiler\Profiler();
$profiler->addAggregator(new \Fabfuel\Prophiler\Aggregator\Database\QueryAggregator());

$toolbar = new \Fabfuel\Prophiler\Toolbar($profiler);
$toolbar->addDataCollector(new \Fabfuel\Prophiler\DataCollector\Request());



$logger = new \Fabfuel\Prophiler\Adapter\Psr\Log\Logger($profiler);


// mode debug
$debug_mode = false;
if (isset($_REQUEST['debug_delain'])) {
    if ($_REQUEST['debug_delain'] == DEBUG_TOKEN) {
        $debug_mode = true;
        $logger->debug('Debug manuel demandé');
    }
}
if (defined('DEBUG_MODE')) {
    if (DEBUG_MODE) {
        $debug_mode = true;
    }
}

//
// Fonction de hashage salé
//
function calculeHash($compte, $clef)
{
    return hash('sha256', md5($compte) . $clef);
}

function writelog($textline, $filename = 'undefined', $verbose = true)
{
    $file = __DIR__ . "/../logs/" . $filename . ".log";
    if (file_exists($file)) {
        if (is_writable($file)) {
            @file_put_contents($file, date("Y-m-d H:i:s") . " : " . $textline . "\n", FILE_APPEND);
        } else {
            if ($verbose) {
                echo "Cannot write to file ($file)";
            }
        }
    } else {
        @file_put_contents($file, date("Y-m-d H:i:s") . " : " . $textline . "\n", FILE_APPEND);
    }
}

/**
 * Récupérer la véritable adresse IP d'un visiteur
 */
function get_ip()
{
    // IP si internet partagé
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } // IP derrière un proxy
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } // Sinon : IP normale
    else {
        return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
    }
}

// On recherche des chaines qui pourraient faire penser à de l'injection SQL comme "SELECT * FROM", "DELETE FROM" et "UPDATE FROM"
// mais il faut penser qu'un paramètre du type "&action=delete" est valide
// =>  Pour commencer on  va interdire tout ce qui contient "FROM" et une autre chaine du type "SELECT", "DELETE" ou "UPDATE"
function is_hacking($value)
{
    if (is_array($value)) {
        foreach ($value as $v) {
            $hacking_value = is_hacking($v);
            if ($hacking_value != '') {
                return $hacking_value;
            }
        }
    } else {
        if (stripos($value, "from") !== false) {
            if ((stripos($value, "select") !== false) || (stripos($value, "delete") !== false) || (stripos($value, "update") !== false)) {
                return $value;
            }
        }
    }
    return "";
}

function register_globals($order = 'egpcs')
{
    // define a subroutine
    if (!function_exists('register_global_array')) {
        function register_global_array(array $superglobal)
        {
            $hacking_value = "";           // Chaine qui est détéectée comme du hacking
            foreach ($superglobal as $varname => $value) {
                $hacking_value = is_hacking($value);

                // C'est louche, on ne permet pas d'aller plus loin!!
                if ($hacking_value != '') {
                    $log = "Tentative de " . get_ip() . " sur la page " . $_SERVER["REQUEST_URI"] . "\nInjection sur le paramètre '{$varname}' : {$hacking_value}\n ";
                    writelog($log, 'hacking', false);
                    die('<br>Une erreur est survenue, si le problème se répète, merci de contacter les administrateurs sur le <a target="_blank" href="https://forum.jdr-delain.net/viewforum.php?f=2&sid=9a837e88f0b38247280c5869a6a6a99c">Forum bug</a><br><br>Pour faciliter le débuggage veuillez préciser la date et l\'heure de l\'incident: <b>' . date("Y-m-d H:i:s") . '</b>');
                }
                global $$varname;
                $$varname = $value;
            }
        }
    }

    $order = explode("\r\n", trim(chunk_split($order, 1)));
    foreach ($order as $k) {
        switch (strtolower($k)) {
            case 'e':
                register_global_array($_ENV);
                break;
            case 'g':
                register_global_array($_GET);
                break;
            case 'p':
                register_global_array($_POST);
                break;
            case 'c':
                register_global_array($_COOKIE);
                break;
            case 's':
                register_global_array($_SERVER);
                break;
        }
    }
}

/**
 * Undo register_globals
 * @author Ruquay K Calloway
 * @link hxxp://www.php.net/manual/en/security.globals.php#82213
 */
function unregister_globals()
{
    if (ini_get('register_globals')) {
        $array = array('_REQUEST', '_SESSION', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value) {
            foreach ($GLOBALS[$value] as $key => $var) {
                if ($var === $GLOBALS[$key]) {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}

register_globals();
$filename = G_CHE . 'stop_jeu';
if (file_exists($filename) && $_SERVER["REMOTE_ADDR"] != '195.37.61.152') {
    //echo "Le jeu est actuellement arrêté pour quelques minutes. <hr>";
    include G_CHE . 'stop_jeu';
    die();
}
require 'prepend.php';
// chemins du jeu

define('CHEMIN', G_CHE);

if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] = 'on')) {
    $type_flux = 'https://';
} else {
    $type_flux = 'http://';
}
if (SERVER_PROD) {
    $type_flux = 'https://';
}
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ('https' == $_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $type_flux = 'https://';
}

// on force tout en https
//$type_flux = 'https://';

/**
 * Autochargement des classes manquantes
 */
function my_autoloader($class)
{
    if (file_exists(CHEMIN . 'includes/class.' . $class . '.php')) {
        require_once CHEMIN . 'includes/class.' . $class . '.php';
    } elseif (file_exists(CHEMIN . '../includes/class.' . $class . '.php')) {
        require_once CHEMIN . '../includes/class.' . $class . '.php';
    }
}

spl_autoload_register('my_autoloader');

// on prépare ce qu'il faut pour twig
$loader = new Twig_Loader_Filesystem(CHEMIN . '/../templates');
if (defined('TWIG_CACHE')) {
    if (TWIG_CACHE) {
        $twig = new Twig_Environment($loader, array(
            'cache' => CHEMIN . '/../../cache',
        ));
    } else {
        $twig = new Twig_Environment($loader, array('debug' => true));
        $twig->addExtension(new \Twig\Extension\DebugExtension());
    }
} else {
    $twig = new Twig_Environment($loader, array('debug' => true));
    $twig->addExtension(new \Twig\Extension\DebugExtension());
}


$options_twig_defaut = array(
    'URL'        => G_URL,
    'URL_IMAGES' => G_IMAGES,
    'HTTPS'      => $type_flux,
    'DEBUG' => $debug_mode
);

// on commence la temporisation de sortie
ob_start();
