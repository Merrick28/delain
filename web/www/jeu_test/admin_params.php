<?php
include "blocks/_header_page_jeu.php";
ob_start();


$erreur = false;

//
// verif droits
//

$droit_modif = 'dcompt_gere_droits';
include "blocks/_test_droit_modif_generique.php";


if ($erreur == 0)
{
    $log = '';
    // initialisation de la méthode
    $methode = get_request_var('methode', 'debut');
    if ($methode != 'debut')
    {
        $req       = "select compt_nom from compte where compt_cod = $compt_cod";
        $stmt      = $pdo->query($req);
        $result    = $stmt->fetch();
        $compt_nom = $result['compt_nom'];
        $log       = date("d/m/y - H:i") . " Compte $compt_nom (n°$compt_cod) modifie les paramètres.\n";
    }
    define("APPEL", 1);

    // Choix de l’onglet
    $lesMethodes = array(
        'messagerie' => array(
            'mess_add',
            'mess_del'
        ),
        'globaux'    => array(
            'glob_modif',
            'glob_creation'
        ),
        'renommee'   => array(
            'ren_g_creation',
            'ren_g_modif',
            'ren_g_supp',
            'ren_a_creation',
            'ren_a_modif',
            'ren_a_supp',
            'ren_m_creation',
            'ren_m_modif',
            'ren_m_supp',
            'ren_k_creation',
            'ren_k_modif',
            'ren_k_supp'
        )
    );

    $onglet = 'aucun';
    $onglet = (in_array($methode, $lesMethodes['globaux'])) ? 'globaux' : $onglet;
    $onglet = (in_array($methode, $lesMethodes['renommee'])) ? 'renommee' : $onglet;
    $onglet = (in_array($methode, $lesMethodes['messagerie'])) ? 'messagerie' : $onglet;

    if ($onglet == 'aucun' && isset($_GET['onglet']))
        $onglet = $_GET['onglet'];

    switch ($onglet)
    {
        case 'globaux':    // Modifs des paramètres globaux
            $page_include = 'admin_params.globaux.php';
            $style_glob   = 'style="font-weight:bold;"';
            break;

        case 'renommee':   // Modifs de renommées
            $page_include = 'admin_params.renommee.php';
            $style_renom  = 'style="font-weight:bold;"';
            break;

        case 'messagerie':   // Modifs de renommées
            $page_include     = 'admin_params.messagerie.php';
            $style_messagerie = 'style="font-weight:bold;"';
            break;
    }

    echo "<h1><strong><big>Gestion des paramètres</big></strong></h1><div>
			<a href='?onglet=globaux' $style_glob>Paramètres globaux</a>
			 - <a href='?onglet=renommee' $style_renom>Renommées / karma</a>
			 - <a href='?onglet=messagerie' $style_messagerie>Messagerie</a></div></div><br />";

    if ($page_include != '')
        include_once $page_include;
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
