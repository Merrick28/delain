<?php
include "blocks/_header_page_jeu.php";
$db2 = new base_delain;

ob_start();
// on cherche la guilde dans laquelle est le joueur
$req_guilde = "select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod,rguilde_admin,pguilde_message from guilde,guilde_perso,guilde_rang ";
$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_rang_cod = pguilde_rang_cod ";
$req_guilde = $req_guilde . "and pguilde_valide = 'O' ";
$db->query($req_guilde);
if ($db->nf() == 0) {
    echo "<p>Erreur ! Vous n'êtes affilié à aucune guilde !";
    $erreur = 1;
}
$db->next_record();
$num_guilde = $db->f("guilde_cod");
// on regarde les détails de la révolution
if (!$db->is_revolution($num_guilde)) {
    echo "<p>Aucune révolution en cours pour votre guilde.";
    $erreur = 1;
}
$revguilde_cod = $_POST['revguilde_cod'];
$req_lanceur = "select * from v_revguilde where code_rev = $revguilde_cod ";
$db->query($req_lanceur);
$db->next_record();
// on regarde si la personne peut voter
$req2 = "select vrevguilde_cod from guilde_revolution_vote ";
$req2 = $req2 . "where vrevguilde_revguilde_cod = " . $db->f("code_rev") . " ";
$req2 = $req2 . "and vrevguilde_perso_cod = $perso_cod ";
$db2->query($req2);
if ($db2->nf() != 0) {
    echo "<p>Vous avez déjà voté !";
    $erreur = 1;
}
if ($erreur == 0) {
    ?>
    <form name="revolution" method="post" action="action.php">
    <input type="hidden" name="methode" value="vote_guilde">
    <input type="hidden" name="revguilde_cod" value="<?php echo $revguilde_cod; ?>">
    <p>A qui voulez vous accorder votre vote ?
        <select name="vote">
            <option value="O"><?php echo $db->f("nom_lanceur"); ?> (lanceur)</option>
            <option value="N"><?php echo $db->f("nom_cible"); ?> (cible)</option>
        </select>
    <p style="text-align:center;"><input type="submit" value="Valider le vote !" class="test">
    <?php
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";