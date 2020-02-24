<?php
include "blocks/_header_page_jeu.php";
ob_start();
$methode = get_request_var('methode', 'debut');
$compte  = new compte;
$compte  = $verif_connexion->compte;
if (!$compte->is_admin())
{
    switch ($methode) {
        case "debut":
            $req = "select prepare_hibernation($compt_cod) as resultat ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            if ($result['resultat'] == 1) {
                echo "<p>Une hibernation est déjà préparée !";
            } else {
                echo "<p><strong>Attention !</strong> La mise en hibernation de votre compte rendra celui-ci inaccessible dans 48 heures, pendant 5 jours. !<br>";
                echo "<br>Après les 48 heures, il vous sera impossible de vous connecter à votre compte pendant cette période. Vos persos seront réactivés lors de votre prochaine connexion après la période d'hibernation, qui peut donc durer de 5 à 90 jours";
                $req_defi = "select * from defi
					inner join perso_compte lanceur on lanceur.pcompt_perso_cod = defi_lanceur_cod
					inner join perso_compte cible on cible.pcompt_perso_cod = defi_cible_cod
					where defi_statut <= 1 and $compt_cod in (lanceur.pcompt_compt_cod, cible.pcompt_compt_cod)";
                $stmt = $pdo->query($req_defi);
                if ($stmt->rowCount() > 0) {
                    echo '<br><strong>Vous êtes actuellement défié !</strong> Si vous demandez une hibernation, au bout du délai de 48h, le défi sera considéré comme abandonné (vous pourriez perdre un petit peu de renommée)<br />';
                }

                echo "Voulez-vous continuer ?";
                echo "<p style=\"text-align:center;\"><a href=\"hibernation.php?methode=oui\">OUI ! Je veux mettre mes persos en hibernation !";
            }
            break;
        case "oui":
            $req = "select prepare_hibernation($compt_cod) as resultat ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            if ($result['resultat'] == 1) {
                echo "<p>Une hibernation est déjà préparée !";
            } else {
                // on met le compte en hibernation
                $req = "update compte set compt_ddeb_hiber = now() + '2 day'::interval where compt_cod = $compt_cod ";
                $stmt = $pdo->query($req);

                // on cherche le compte
                $req = "select insere_evenement(pcompt_perso_cod, pcompt_perso_cod, 24, '[perso_cod1] a été mis en hibernation', 'O', null) from perso_compte where pcompt_compt_cod = $compt_cod ";
                $stmt = $pdo->query($req);
                echo "<p>Votre compte sera en hibernation dans 48 heures.</p>";
            }
            break;

    }
} else {
    echo "<p>Vous ne pouvez pas valider des actions en étant administrateur !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
