<?php
include "blocks/_header_page_jeu.php";
ob_start();
$erreur = 0;
$perso  = new perso;
$perso  = $verif_connexion->perso;
if ($perso->is_milice() == 0)
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
$req = "select pguilde_rang_cod from guilde_perso where pguilde_perso_cod = $perso_cod and pguilde_rang_cod = 16 ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0) {
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0) {
    $methode          = get_request_var('methode', 'debut');
    switch ($methode) {
        case "debut":
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=prison\">Voir les joueurs en prison</a><br>";
            break;
        case "prison":
            $req = "select perso_cod,perso_nom,lower(perso_nom) as minusc from perso,perso_position,positions ";
            $req = $req . "where perso_actif = 'O' ";
            $req = $req . "and perso_type_perso = 1 ";
            $req = $req . "and perso_cod = ppos_perso_cod ";
            $req = $req . "and ppos_pos_cod = pos_cod ";
            $req = $req . "and pos_etage = 5 ";
            $req = $req . "order by minusc ";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0) {
                echo "<p>Aucun joueur en prison à ce jour.";
            } else {
                echo "<table>";
                echo "<tr>";
                echo "<td class=\"soustitre2\"><strong>Nom</strong></td>";
                echo "<td></td>";
                echo "</tr>";
                while ($result = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><strong>", $result['perso_nom'], "</strong></td>";
                    echo "<td><a href=\"", $_SERVER['PHP_SELF'], "?methode=ouvrir&perso=", $result['perso_cod'], "\">Ouvrir la porte ?</a>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            break;
        case "ouvrir":
            // nom
            $req = "select ouvrir_prison($perso_cible,$perso_cod) as resultat ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo $result['resultat'];
            break;

    }
    echo "<hr><a href=\"", $_SERVER['PHP_SELF'], "\">Retour à la page principale du geolier.</a><br>";
    echo "<a href=\"milice.php\">Retour à la page milice</a><br>";


}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
