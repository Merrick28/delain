<?php
require __DIR__ . '/conf.php';
require G_CHE . '../vendor/autoload.php';

header('Content-type: text/html; charset=utf-8');

//
// Fonction de hashage salé
//
function calculeHash($compte, $clef)
{
    return hash('sha256', md5($compte) . $clef);
}

function writelog($textline,$filename='undefined',$verbose=true)
{
    $file = __DIR__ . "/../logs/" . $filename . ".log";
    if(!file_exists($file))
    {
        if (is_writable($file)) {
            @file_put_contents($file, date("Y-m-d H:i:s")." : ".$textline."\n",  FILE_APPEND);
        }
        else
        {
            if($verbose)
            {
                echo "Cannot write to file ($file)";
            }
        }
    }
    else
    {
        @file_put_contents($file, date("Y-m-d H:i:s")." : ".$textline."\n",  FILE_APPEND);
    }

}

function register_globals($order = 'egpcs')
{
    // define a subroutine
    if (!function_exists('register_global_array'))
    {

        function register_global_array(array $superglobal)
        {
            foreach ($superglobal as $varname => $value)
            {
                global $$varname;
                $$varname = $value;
            }
        }

    }

    $order = explode("\r\n", trim(chunk_split($order, 1)));
    foreach ($order as $k)
    {
        switch (strtolower($k))
        {
            case 'e': register_global_array($_ENV);
                break;
            case 'g': register_global_array($_GET);
                break;
            case 'p': register_global_array($_POST);
                break;
            case 'c': register_global_array($_COOKIE);
                break;
            case 's': register_global_array($_SERVER);
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
    if (ini_get('register_globals'))
    {
        $array = array('_REQUEST', '_SESSION', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value)
        {
            foreach ($GLOBALS[$value] as $key => $var)
            {
                if ($var === $GLOBALS[$key])
                {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}

register_globals();
$filename = G_CHE . 'stop_jeu';
if (file_exists($filename) && $_SERVER["REMOTE_ADDR"] != '195.37.61.152')
{
    //echo "Le jeu est actuellement arrêté pour quelques minutes. <hr>";
    include G_CHE . 'stop_jeu';
    die();
}
require 'prepend.php';
// chemins du jeu

define('CHEMIN',G_CHE);
if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] = 'on'))
{
    $type_flux = 'https://';
}
else
{
    $type_flux = 'http://';
}
/**
 * Autochargement des classes manquantes
 */
function my_autoloader($class) {
    if (file_exists(CHEMIN . 'includes/class.' . $class . '.php'))
    {
        require_once CHEMIN . 'includes/class.' . $class . '.php';
    }
    elseif (file_exists(CHEMIN . '../includes/class.' . $class . '.php'))
    {
        require_once CHEMIN . '../includes/class.' . $class . '.php';
    }
}
spl_autoload_register('my_autoloader');

// on prépare ce qu'il faut pour twig
$loader = new Twig_Loader_Filesystem(CHEMIN . '/../templates');
if(defined('TWIG_CACHE'))
{
    if(TWIG_CACHE)
    {
        $twig     = new Twig_Environment($loader, array(
            'cache' => CHEMIN . '/../../cache',
        ));
    }
    else
    {
        $twig     = new Twig_Environment($loader, array());
    }
}
else
{
    $twig     = new Twig_Environment($loader, array());
}


$options_twig_defaut = array(
    'URL'               => G_URL,
    'URL_IMAGES'        => G_IMAGES,
    'HTTPS'             => $type_flux,
);

// on commence la temporisation de sortie
ob_start();

