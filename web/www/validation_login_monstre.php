<?php
include_once 'classes.php';
include_once G_CHE . 'ident.php';

$db = new base_delain;
$req = "SELECT compt_frameless FROM compte WHERE compt_cod = $compt_cod";
$db->query($req);
$db->next_record();
$frameless = ($db->f('compt_frameless') == 'O');

if ($frameless) {
    include_once 'includes/template.inc';
    $t = new template('jeu_test');
    $t->set_file('FileRef', '../template/delain/general_jeu.tpl');
    // chemins
    $t->set_var('URL', $type_flux . G_URL);
    $t->set_var('URL_IMAGES', G_IMAGES);
    ob_start();
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" title="essai">
        <title>Login monstre</title>
    </head>
    <body background="images/fond5.gif">
    <div class="bordiv">
    <?php
}

// On trouve le monstre
$requete = "select perso_cod, perso_dlt, to_char(now(), 'DD/MM/YYYY hh24:mi:ss') as maintenant, perso_nom
    from perso where perso_cod = $numero and (perso_type_perso = 2 or perso_pnj = 1)";
$db->query($requete);

$num_resultat = $db->nf();
$num_droits = 0;

// Étage autorisé ?
if ($num_resultat > 0) {
    $db->next_record();
    $maintenant = $db->f("maintenant");
    $nom = $db->f("perso_nom");
    $num_perso = $db->f("perso_cod");
    $perso_cod = $db->f("perso_cod");

    $requete = "select pos_etage from perso_position
		inner join positions on pos_cod = ppos_pos_cod
		where ppos_perso_cod = $numero";
    $db->query($requete);
    $db->next_record();
    $etage_monstre = $db->f('pos_etage');

    $requete = "select dcompt_etage from compt_droit
		where dcompt_compt_cod = $compt_cod
			and (replace(dcompt_etage, ' ', '') like '%,$etage_monstre,%'
				or replace(dcompt_etage, ' ', '') like '$etage_monstre,%'
				or replace(dcompt_etage, ' ', '') like '%,$etage_monstre'
				or replace(dcompt_etage, ' ', '') = '$etage_monstre'
				or replace(dcompt_etage, ' ', '') = 'A')";
    $db->query($requete);
    $num_droits = $db->nf();
}

if ($num_resultat == 0 || $num_droits == 0) {
    // Identification échouée
    echo("<p>Identification échouée !!!</p><p>Vérifiez que le numéro corresponde à un monstre (ou PNJ) existant, et qu’il se trouve à un étage qui
		vous est autorisé.\n");
} else {
    echo("<p>Identification réussie !!!</p></td>\n");

    // avant toute autre chose, on renseigne la date de dernier login !
    $req_maj_date = "update perso set perso_der_connex = now() where perso_cod = $num_perso";
    $db->query($req_maj_date);
    // on met la bonne info dans le compte
    $req = "update compte set compt_der_perso_cod = " . $num_perso . " where compt_cod = " . $compt_cod;
    $db->query($req);
    $req_maj_dlt = "select calcul_dlt2($num_perso) as dlt,perso_cod from perso where perso_cod = $num_perso ";
    $db->query($req_maj_dlt);
    $db->next_record();
    $retour_dlt = $db->f("dlt");

    $req_dlt = "select to_char(perso_dlt,'dd/mm/yyyy hh24:mi:ss') as dlt,perso_pa from perso where perso_cod = $num_perso";
    $db->query($req_dlt);
    $db->next_record();


    echo("$retour_dlt<br>");

    printf("<p>Votre date limite de tour est : %s</p>", $db->f("dlt"));
    printf("<p>Il vous reste %s points d'action</p>\n", $db->f("perso_pa"));

    // recherche des evts non lus

    echo("<p><em>Date et heure serveur : <strong>$maintenant</strong> </em></p>");

    // formulaire pour passer au jeu
    if ($frameless) {
        echo "<form name=\"ok\" method=\"post\" action=\"jeu_test/index.php\" target=\"_top\">";
    } else {
        echo "<script type='text/javascript'>if (parent.gauche) parent.gauche.location.href='jeu/menu.php';</script>";
        echo "<form name=\"ok\" method=\"post\" action=\"jouer.php\" target=\"_top\">";
    }

    echo("<input type=\"hidden\" name=\"nom_perso\" value=\"$nom\">\n");
    echo("<input type=\"hidden\" name=\"nom_perso\" value=\"$nom\">\n");
    echo("<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">");
    echo("<input type=\"hidden\" name=\"num_perso\" value=\"$num_perso\">\n");
    echo("<center><input type=\"submit\" value=\"Jouer !!\" class=\"test\"></form>");
}
if ($frameless) {
    $contenu_page = ob_get_contents();
    ob_end_clean();

    // on va maintenant charger toutes les variables liées au menu
    include_once('jeu_test/variables_menu.php');

    $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
    $t->parse("Sortie", "FileRef");
    $t->p("Sortie");
} else {
    ?>
    </div>
    </body>
    </html>
<?php }

