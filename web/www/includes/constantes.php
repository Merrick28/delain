<?php
// fichier de déclaration des constantes


$__VERSION = "20210711";        // A changer aussi dans variable_menu.php

//$racine_url = "http://www.jdr-delain.net/";
$racine_url = ((isset($_SERVER['HTTPS'])||isset($_SERVER['SSL_PROTOCOL'])) ? "https://" : "http://").$_SERVER['HTTP_HOST']."/";
$racine_unix = "/home/delain/public_html/";

$classes = $racine_unix . "classes/";
$path_images = $racine_url . "images/";


$tab_blessures[0] = 'touché';
$tab_blessures[1] = 'blessé';
$tab_blessures[2] = 'gravement touché';
$tab_blessures[3] = 'presque mort';

$perso_type_perso[1] = 'aventurier';
$perso_type_perso[2] = 'monstre';
$perso_type_perso[3] = 'monstre';

$nom_sexe['M'] = 'Messire';
$nom_sexe['F'] = 'Damoiselle';

$etat[0] = 'Comme neuf';
$etat[1] = 'Excellent';
$etat[2] = 'Bon';
$etat[3] = 'Mauvais';
$etat[4] = 'Médiocre';
$etat[5] = 'Déplorable';

$palbable['O'] = '';
$palbable['N'] = '<em> - (impalpable)</em>';