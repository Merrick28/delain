<?php
require __DIR__ . '/conf.php';

header('Content-type: text/html; charset=utf-8');

//
// Fonction de hashage salé
//
function calculeHash($compte, $clef)
{
    return hash('sha256', md5($compte) . $clef);
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
// on triche pour les fonctions apc
if (!function_exists('apc_exists'))
{

    function apc_exists()
    {
        return true;
    }

}
if (!function_exists('apc_fetch'))
{
    function apc_fetch($arg)
    {
        switch ($arg)
        {
            case 'g_url':
                return G_URL;
            case 'g_che':
                return G_CHE;
            case 'g_images':
                return G_IMAGES;
            case 'nom_cook':
                return NOM_COOK;
            case 'img_path':
                return IMG_PATH;
        }
    }
}

define('CHEMIN',G_CHE);
if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] = 'on'))
{
    $type_flux = 'https://';
}
else
{
    $type_flux = 'http://';
}
// modif par SD : on tente les variables par apc ?
if (!apc_exists('g_url'))
{
    apc_store('g_url', G_URL);
}
if (!apc_exists('g_che'))
{
    apc_store('g_che', G_CHE);
}
if (!apc_exists('g_images'))
{
    apc_store('g_images', G_IMAGES);
}
if (!apc_exists('nom_cook'))
{
    apc_store('nom_cook', NOM_COOK);
}
if (!apc_exists('img_path'))
{
    apc_store('img_path', IMG_PATH);
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

// on commence la temporisation de sortie
ob_start();

