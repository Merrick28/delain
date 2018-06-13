<?php
$target = '  target="_top"';

// changement de perso
if (isset($_REQUEST['perso']))
{
    $change_perso = $_REQUEST['perso'];         // -> pour ident.php
}

include_once "ident.php";
include_once "includes/classes.php";
if (!$verif_auth)
{
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}
// normalement, les objets $compte et $perso sont déjà chargés par la page ident.php

$frameless = ($compte->compt_frameless == 'O');
$autorise_monstre = ($compte->autorise_4e_monstre() == 't');


$num_resultat = 0;
$db = new base_delain;
$requete
    = "SELECT perso_cod, perso_nom, coalesce(perso_mortel, 'N') AS perso_mortel, 
			perso_dlt, dlt_passee(perso_cod) dlt_passee
		FROM perso WHERE perso_cod = " . $perso_cod;
$db->query($requete);
$num_resultat = $db->nf();
if ($num_resultat>0)
{
    $db->next_record();
    $perso_dlt_passee = $db->f('dlt_passee');
} else
{
    $perso_dlt_passee = 0;
}
$nom = $perso->perso_nom;
$perso_nom = str_replace(chr(39), " ", $nom);
$maintenant = date("d/m/Y H:i:s");
$perso_mortel = $perso->perso_mortel;
$num_perso = $perso->perso_cod;
$perso_cod = $num_perso;
$autorise = 0;
$type_perso = $perso->perso_type_perso;
if ($type_perso == 1 || ($type_perso == 2 && $autorise_monstre))
{
    // on va quand même charger le perso_compte
    $pcompt = new perso_compte();
    $tab = $pcompt->getBy_pcompt_perso_cod($perso->perso_cod);
    if ($tab !== false)
    {
        // On a trouvé un perso_compte pour ce perso
        if ($tab[0]->pcompt_compt_cod == $compte->compt_cod)
        {
            // le compte compt_cod correspond au compt_cod courant, on autorise
            $autorise = 1;
        }
    }
} elseif ($type_perso == 3)
{
    $pfam = new perso_familier();
    $pcompt = new perso_compte();
    $tab_fam = $pfam->getBy_pfam_familier_cod($perso->perso_cod);
    if ($tab_fam !== false)
    {
        // on est bien dans la table familiers
        $tab_pcompt = $pcompt->getBy_pcompt_perso_cod($tab_fam[0]->pfam_perso_cod);
        {
            if ($tab_pcompt !== false)
            {
                // on est bien dabs la table pcompte
                if ($tab_pcompt[0]->pcompt_compt_cod == $compte->compt_cod)
                {
                    // le compte compt_cod correspond au compt_cod courant, on autorise
                    $autorise = 1;
                }
            }
        }
    }
}
if ($autorise != 1)
    //
    // on va quand même vérifier que le compte n'est pas sitté
    //
{
    if ($type_perso == 1)
    {
        $cs = new compte_sitting();
        if ($cs->isSittingValide($compte->compt_cod, $perso->perso_cod))
        {
            $autorise = 1;
        }
    } elseif ($type_perso == 3)
    {
        $cs = new compte_sitting();
        if ($cs->isSittingFamilierValide($compte->compt_cod, $perso->perso_cod))
        {
            $autorise = 1;
        }
    }
}

if ($autorise != 1)
{
    //
    // perso non authorisé et n'est pas sitté.... pas de switch rapide => sortie d'érreur comme validation_login3.php
    //
    include_once 'includes/template.inc';
    $t = new template('jeu_test');
    $t->set_file('FileRef', '../template/delain/general_jeu.tpl');
    // chemins
    $t->set_var('URL', $type_flux . G_URL);
    $t->set_var('URL_IMAGES', G_IMAGES);
    ob_start();

    if ($autorise != 1)
        echo "Accès refusé !";
    else
        echo '<br>Vous devez activer la DLT du personnage avant d\'utiliser le switch rapide !<br>
                <a href="/jeu_test/switch.php">Gestion compte</a>';

    echo '</div>';



    $contenu_page = ob_get_contents();
    ob_end_clean();

    if ($perso_mortel != 'M')
    {
        // on va maintenant charger toutes les variables liées au menu
        // sauf si le perso est définitivement mort (sinon ça plante...)
        include_once('jeu_test/variables_menu.php');
    }

    $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
    $t->parse("Sortie", "FileRef");
    $t->p("Sortie");
    die();                  // Ne pas continuer en cas d'erreur
}

// Récupération de la racine du site
$root = 'https://' . $_SERVER['HTTP_HOST'] ;
if ($perso_dlt_passee != 0)
{
    // Le quick-switch ne marche pas, il faut d'abord activer la DLT!
    header("Location: /validation_login3.php");
}
else if (!in_array( substr(str_replace("http://", "https://", $_REQUEST["url"]), strlen($root)) , array(
        "/jeu_test/perso2.php",
        "/jeu_test/frame_vue.php",
        "/jeu_test/evenements.php",
        "/jeu_test/inventaire.php",
        "/jeu_test/ramasser.php",
        "/jeu_test/transactions2.php",
        "/jeu_test/deplacement.php",
        "/jeu_test/combat.php",
        "/jeu_test/magie.php",
        "/jeu_test/choix_voie_magique.php",
        "/jeu_test/enchantement_general.php",
        "/jeu_test/objets/pioche.php",
        "/jeu_test/enluminure_general.php",
        "/jeu_test/concentration.php",
        "/jeu_test/messagerie2.php",
        "/jeu_test/guilde.php",
        "/jeu_test/groupe.php"
    )))
{
     // par sécurité, on switch sur la vue.
   // // echo "<pre>"; print_r($root); echo "</pre>";
   // die($_SERVER["HTTP_ORIGIN"]." => ".substr($_REQUEST["url"], strlen($_SERVER["HTTP_ORIGIN"])));
    header("Location: /jeu_test/frame_vue.php");
}
else
{
     // retourner sur l'url en cours de consultation avant le quick-switch
    header("Location: ".$_REQUEST["url"]);
}

?>