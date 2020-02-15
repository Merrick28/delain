<?php
$niveau[100] = "First";
$niveau[80] = "Grand Ancien";
$niveau[60] = "Maitre Vampire";
include "blocks/_header_page_jeu.php";
ob_start();
$erreur  = 0;
$methode = get_request_var('methode', 'entree');
$req     = "select tvamp_cod,perso_nom,perso_cod from vampire_tran,perso ";
$req     = $req . "where tvamp_perso_fils = $perso_cod ";
$req     = $req . "and tvamp_perso_pere = perso_cod ";
$stmt    = $pdo->query($req);
if ($stmt->rowCount() == 0) {
    $erreur = 1;
    echo "<p>Vous n'avez pas accès à cette page !";
} else {
    $result = $stmt->fetch();
    $pere = $result['perso_cod'];
    echo 'père = ' . $pere;
}
if ($erreur == 0) {
    switch ($methode) {
        case "entree":
            echo '<p class="titre">Vampirisme</p>';
            echo "<p><strong>", $result['perso_nom'], "</strong> vous a invité à rejoindre sa famille de vampires.<br>";
            echo "Cet acte n'est pas anodin. Avant de continuer, voici ce que vous devez savoir sur les vampires :<br>";
            ?>
            - Une transformation exige un rituel long et complexe. Son cout est de 12 PA (un tour entier).<br>
            - Lors d'une transformation, vos points de régénération sont convertis en points de vampirisme (voir plus bas), et vous gagnez le sort "drain vampirique". De plus, la race est transformée pour devenir vampire.
            <br>
            - Par voie de conséquence, tous les avantages de votre race précédente disparaîtront. Si vous êtes un nain transformé en vampire, le bloque magie sera supprimé ainsi qu'un niveau d'Attaque foudroyante. Dans ce cas et uniquement dans ce cas, le sort attaque vous sera en plus rajouté, et la limite d'apprentissage du nombre de sort (INT/2) disparaîtra.
            - Vous ne pouvez pas créer de descendance tant qu'un maître vampire ne vous y autorise pas.<br>
            <br><strong>Avantages et inconvénients du vampire</strong><br>
            - les vampires ont des points de vampirisme (max 10). Pour chaque attaque à mains nues, chaque point de vampirisme fait que le vampire régénère 0.1 x P x D points de vie, D étant les dégats effectués à l'adversaire, et P le nombre de points de vampirisme.
            <br>
            - leur attaque à mains nues est légèrement augmentée (passage de 1D3 à 1D4)<br>
            - Les vampires ne peuvent pas avoir de karma positif.<br>
            - Le sort drain vampirique (6PA - contact) fait 1D10 de dégats à une cible, et les points effectués sont rajoutés au PV du vampire (jusqu'à la limite de ses PV max)
            <br>
            - à chaque changement de niveau, on peut rajouter un point de vampirisme (max 10, limité à niveau actuel/2)
            - Le régénération "normale" au calcul de tour ne marche plus.<br><br>
            Vous avez maintenant toutes les cartes en main pour accepter ou refuser ce marché.
            <br><strong>Attention ! Toute transformation en vampire est irréversible !</strong>
            Souhaitez-vous accepter ou refuser cette offre ?<br>
            <a href="tran_vamp.php?methode=oui">J'ACCEPTE sans condition ma future condition de vampire (12PA)</a><br>
            <br>
            <a href="tran_vamp.php?methode=non">JE REFUSE !!</a>
            <?php
            break;
        case "oui":
            //
            // on lance la procédure d'acceptation
            //
            $req = "select accepte_vampire($perso_cod) as resultat";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>", $result['resultat'];
            break;
        case "non":
            $texte_evt = "[cible] a refusé d'être transformé an vampire par [attaquant]";
            $req = "insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible) ";
            $req = $req . "values(nextval('seq_levt_cod'),27,now(),1,$pere,e'" . pg_escape_string($texte_evt) . "','N','N',$pere,$perso_cod)";
            $stmt = $pdo->query($req);
            $req = "insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible) ";
            $req = $req . "values(nextval('seq_levt_cod'),27,now(),1,$perso_cod,e'" . pg_escape_string($texte_evt) . "','O','N',$pere,$perso_cod)";
            $stmt = $pdo->query($req);
            echo "<p>La proposition a bien été refusée";
            break;
    }
} else {
    echo "<p>Vous n'avez pas accès à cette page !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";