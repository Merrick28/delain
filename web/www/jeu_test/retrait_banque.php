<?php
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();

// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!is_lieu($perso_cod))
{
    echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
    $erreur = 1;
}
if ($erreur == 0)
{
    $tab_lieu = get_lieu($perso_cod);
    if ($tab_lieu[3] != 'Banque')
    {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
    }
}

if ($erreur == 0)
{
    echo("<img src=\"../images/banque3.png\"><br />");
    // on recherche l'or en banque
    $req_or = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
    $res_or = pg_exec($dbconnect, $req_or);
    if (!$res_or)
    {
        echo("<p>Une erreur est sruvenue pendant la recherche de votre compte.");
    }
    $nb_or = pg_numrows($res_or);
    if ($nb_or == 0)
    {
        $qte_or = 0;
    } else
    {
        $tab_or = pg_fetch_array($res_or, 0);
        $qte_or = $tab_or[0];
    }
    echo("<p>Vous avez $qte_or brouzoufs sur votre compte.");
    ?>
    <hr/>
    <form name="depot" method="post" action="valide_retrait_banque.php">
        <p>Retirer <input type="text" name="quantite"> brouzoufs de mon compte.
        <p><input type="submit" value="Valider !" class="test centrer">
    </form>
    <?php

}
$close = pg_close($dbconnect);

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');
