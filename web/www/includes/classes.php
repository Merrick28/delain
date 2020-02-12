<?php 
$maintenance = false;
//ini_set('zlib.output_compression','1');
//die ('Maintenance, merci de patienter');
if (isset($ip_bad))
{
    if ( array_key_exists("REMOTE_ADDR", $_SERVER) && !isset($from_forum))
    { // Existe toujours sauf quand le script est appelé en local
        $adr_ip = $_SERVER["REMOTE_ADDR"];
        $filtre_bad = array_search($adr_ip,$ip_bad);
        if($filtre_bad)
        {
            die('Votre adresse ip a été bannie du jeu.');
        }
        if($maintenance)
        {
            $filtre_ok = array_search($adr_ip,$ip_ok);
            if($filtre_ok)
            {
                echo '<!-- acces autorise pendant maintenance -->';
            }
            else
            {
                die('Redemerrage prévu ce soir entre 20h et 21h pour les joueurs.<br />Merrick.<br /><br />');
            }
        }
    }
}


// Gère l’accès à la base de données.


// Gère l’affichage standardisé d’éléments HTML.
require_once dirname(__FILE__) . '/html.php';
$html = new html;

// Gère l’envoi de messages dans le jeu.
require_once dirname(__FILE__) . '/message.php';

