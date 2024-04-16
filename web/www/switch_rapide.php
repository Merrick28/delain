<?php
$target = '  target="_top"';

// changement de perso
if (isset($_REQUEST['perso']))
{
    $change_perso = $_REQUEST['perso'];
}

$verif_connexion = new verif_connexion();
$verif_connexion->ident($change_perso);
$verif_auth = $verif_connexion->verif_auth;
$compte     = $verif_connexion->compte;
$perso_cod  = $verif_connexion->perso_cod;
$perso      = $verif_connexion->perso;
include_once "includes/classes.php";
if (!$verif_auth)
{
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}

$frameless        = ($compte->compt_frameless == 'O');
$autorise_monstre = ($compte->autorise_4e_monstre() == 't');


$num_resultat = 0;
$pdo          = new bddpdo();
$requete
              = "SELECT perso_cod, perso_nom, coalesce(perso_mortel, 'N') AS perso_mortel,
			perso_dlt, dlt_passee(perso_cod) dlt_passee
		FROM perso WHERE perso_cod = :perso";
$stmt         = $pdo->prepare($requete);
$stmt         = $pdo->execute(array(":perso" => $perso_cod), $stmt);

if($perso->charge($perso_cod))
{
    $perso_dlt_passee = $perso->dlt_passee();
} else
{
    $perso_dlt_passee = 0;
}
require "_block_valide_autorise_joue_perso.php";


if ($autorise != 1)
{
    //
    // perso non authorisé et n'est pas sitté.... pas de switch rapide => sortie d'érreur comme validation_login3.php
    //
    include_once 'includes/template.inc';

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

    $template     = $twig->load('template_jeu.twig');
    $options_twig = array(

        'PERSO'        => $perso,
        'PHP_SELF'     => $_SERVER['PHP_SELF'],
        'CONTENU_PAGE' => $contenu_page

    );
    echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));
    die();                  // Ne pas continuer en cas d'erreur
}

// Récupération de la racine du site
$root = 'https://' . $_SERVER['HTTP_HOST'];
$url = substr(str_replace("http://", "https://", $_REQUEST["url"]), strlen($root));
$len = strpos($url, "?") ;
if ($len >0) $url = substr($url, 0, $len );
if ($perso_dlt_passee != 0)
{
    // Le quick-switch ne marche pas, il faut d'abord activer la DLT!
    header("Location: /validation_login3.php");
} else if (!in_array($url, array(
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
    "/jeu_test/groupe.php",
    "/jeu_test/quete_auto.php"
)))
{
    // par sécurité, on switch sur la vue.
    // // echo "<pre>"; print_r($root); echo "</pre>";
    // die($_SERVER["HTTP_ORIGIN"]." => ".substr($_REQUEST["url"], strlen($_SERVER["HTTP_ORIGIN"])));
    header("Location: /jeu_test/frame_vue.php");
} else
{
    // retourner sur l'url en cours de consultation avant le quick-switch (cas particulier des QA, on ne repost pas les même paramètre)
    if ($url == "/jeu_test/quete_auto.php") $_REQUEST["url"] = $_REQUEST["url"] ="/jeu_test/quete_auto.php?onglet=apercu";
    header("Location: " . $_REQUEST["url"]);
}

?>
