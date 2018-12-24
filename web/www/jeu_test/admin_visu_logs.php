<?php
include "blocks/_header_page_jeu.php";


//
//Contenu de la div de droite
//

$droit_modif = 'dcompt_acces_log';
include "blocks/_test_droit_modif_generique.php";

if ($erreur != 0)
{
    echo "<p>Erreur ! Vous n’avez pas accès à cette page !</p>";
} else
{
    $liste_logs = array(
        "perso" => array("perso_edit.log", "Modification sur les personnages."),
        "monstre" => array("monstre_edit.log", "Modification sur les monstres."),
        "droits" => array("droit_edit.log", "Modification des droits d’accès."),
        "objet" => array("objet_edit.log", "Modification sur les objets."),
        "refuge" => array("change_refuge.log", "Modification sur le statut refuge des magasins."),
        "temple" => array("temple.log", "Modification sur le statut refuge des temples."),
        "factions" => array("factions.log", "Modification sur les factions."),
        "animations" => array("animations.log", "Lancement des animations."),
        "lieux" => array("lieux_etages.log", "Modification sur les étages et lieux."),
        "poste" => array("relais_poste.log", "Transactions via les relais de la poste."),
        "comptes" => array("compte_creation.log", "Creation de compte de joueur."),
        "quêtes" => array("quete_auto.log", "Modification des Quête auto."),
        "hacking" => array("hacking.log", "Tentative de hack du site."),
    );
    $visu = (isset($visu)) ? $visu : "début";
    $mode = (isset($mode)) ? $mode : "web";

    if (isset($liste_logs[$visu]) && $mode == "web")
    {
        echo "<p><strong>Visualisation du fichier de log " . $liste_logs[$visu][1] . "</strong> - <a href='?visu=liste'>Retour au début</a></p>";
        echo "<div class='bordiv' style='max-height: 800px; overflow: auto;'><pre>";
        if (file_exists('../logs/' . $liste_logs[$visu][0]))
        {
            include('../logs/' . $liste_logs[$visu][0]);
        } else
        {
            echo "Il n'y a acun evènements.";
        }
        echo "</pre></div>";
        echo '<a href="?visu=liste" class="centrer">Retour au début</a>';
    }
    if (isset($liste_logs[$visu]) && $mode == "texte")
    {
        header('Content-Type: text/plain; charset=utf-8');
        if (file_exists('../logs/' . $liste_logs[$visu][0]))
        {
            include('../logs/' . $liste_logs[$visu][0]);
        } else
        {
            echo "Il n'y a acun evènements.";
        }
        die();
    }
    if (!isset($liste_logs[$visu]))
    {
        echo "<p><strong>Liste des fichiers de log</strong></p>";
        foreach ($liste_logs as $id => $valeurs)
        {
            $nom = $valeurs[1];
            echo " - $nom <a href='?mode=web&visu=$id'>mode web</a> / <a href='?mode=texte&visu=$id'>mode texte</a><br/>";
        }
    }
}

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
//include"../logs/monstre_edit.log";
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
