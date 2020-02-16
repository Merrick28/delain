<?php
include_once 'classes.php';
include_once G_CHE . 'ident.php';
include_once 'fonctions.php';

$pdo = new bddpdo;

ob_start();


// On trouve le monstre

$monstre = new perso;
if (!$monstre->charge($_REQUEST['numero']))
{
    die('Erreur sur le chargement de perso');
}
if (($monstre->perso_type_perso != 2) && ($monstre->perso_pnj != 1))
{
    die('Erreur sur le type de perso');
}

$num_droits = 0;

// Étage autorisé ?

$maintenant = date('Y-m-d H:i:s');
$nom        = $monstre->perso_nom;
$num_perso  = $monstre->perso_cod;
$perso_cod  = $monstre->perso_cod;

$requete       = "select pos_etage from perso_position
		inner join positions on pos_cod = ppos_pos_cod
		where ppos_perso_cod = :perso_cod";
$stmt          = $pdo->prepare($requete);
$stmt          = $pdo->execute(array(":perso_cod" => $monstre->perso_cod), $stmt);
$result        = $stmt->fetch();
$etage_monstre = $result['pos_etage'];

$requete = "select dcompt_etage from compt_droit
		where dcompt_compt_cod = :compte
			and (replace(dcompt_etage, ' ', '') like '%,$etage_monstre,%'
				or replace(dcompt_etage, ' ', '') like '$etage_monstre,%'
				or replace(dcompt_etage, ' ', '') like '%,$etage_monstre'
				or replace(dcompt_etage, ' ', '') = '$etage_monstre'
				or replace(dcompt_etage, ' ', '') = 'A')";
$stmt    = $pdo->prepare($requete);
$stmt    = $pdo->execute(array(
                             ":compte" => $compt_cod), $stmt);


if (!$result = $stmt->fetch())
{
    // Identification échouée
    echo(" <p>Identification échouée !!!</p><p> Vérifiez que le numéro corresponde à un monstre(ou PNJ) existant, et qu’il se trouve à un étage qui
		vous est autorisé . \n");
} else
{
    echo(" <p>Identification réussie !!!</p></td> \n");

    // avant toute autre chose, on renseigne la date de dernier login !
    $monstre->perso_der_connex = date('Y-m-d H:i:s');
    $monstre->stocke();

    // on met la bonne info dans le compte
    $compte                      = new compte;
    $compte                      = $verif_connexion->compte;
    $compte->compt_der_perso_cod = $monstre->perso_cod;
    $compte->stocke();

    $retour_dlt = $monstre->calcul_dlt();


    echo("$retour_dlt <br>");

    printf(" <p>Votre date limite de tour est : %s </p> ", format_date($monstre->perso_dlt));
    printf(" <p>Il vous reste % s points d'action</p>\n", $monstre->perso_pa);

    // recherche des evts non lus

    echo("<p><em>Date et heure serveur : <strong>" . date('Y-m-d H:i:s') . "</strong> </em></p>");

    // formulaire pour passer au jeu
    if ($frameless)
    {
        echo "<form name=\"ok\" method=\"post\" action=\"jeu_test/index.php\" target=\"_top\">";
    } else
    {
        echo "<script type='text / javascript'>if (parent.gauche) parent.gauche.location.href='jeu / menu . php';</script>";
        echo "<form name=\"ok\" method=\"post\" action=\"jouer.php\" target=\"_top\">";
    }

    echo("<input type=\"hidden\" name=\"nom_perso\" value=\"$nom\">\n");
    echo("<input type=\"hidden\" name=\"nom_perso\" value=\"$nom\">\n");
    echo("<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">");
    echo("<input type=\"hidden\" name=\"num_perso\" value=\"$num_perso\">\n");
    echo("<center><input type=\"submit\" value=\"Jouer !!\" class=\"test\"></form>");
}

$contenu_page = ob_get_contents();
ob_end_clean();

// on va maintenant charger toutes les variables liées au menu
include_once('jeu_test/variables_menu.php');


$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'PERSO'        => $perso,
    'PHP_SELF'     => $_SERVER['PHP_SELF'],
    'CONTENU_PAGE' => $contenu_page

);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));


