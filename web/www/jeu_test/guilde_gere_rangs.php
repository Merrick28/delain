<?php
include "blocks/_header_page_jeu.php";
ob_start();
if (!isset($methode)) {
    $methode = 'debut';
}
if ($db->is_admin_guilde($perso_cod)) {
    $req_guilde = "select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod from guilde,guilde_perso,guilde_rang ";
    $req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
    $req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
    $req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod ";
    $req_guilde = $req_guilde . "and rguilde_rang_cod = pguilde_rang_cod ";
    $req_guilde = $req_guilde . "and pguilde_valide = 'O' ";
    $db->query($req_guilde);
    $db->next_record();
    $num_guilde = $db->f("guilde_cod");
    switch ($methode) {
        case "debut":
            $tab_admin['O'] = 'Administrateur';
            $tab_admin['N'] = 'Membre';
            $req = "select rguilde_libelle_rang,rguilde_rang_cod,rguilde_cod,rguilde_admin ";
            $req = $req . "from guilde_rang ";
            $req = $req . "where rguilde_guilde_cod = $num_guilde ";
            $req = $req . "order by rguilde_cod ";
            $db->query($req);
            ?>
            <table>
            <tr>
            <td class="soustitre2"><strong>Nom du rang</strong></td>
            <td class="soustitre2"><strong>Fonction</strong></td>
            <td></td>
            <td></td>
            <?php
            while ($db->next_record()) {
                $radmin = $db->f("rguilde_admin");
                echo "<tr>";
                echo "<td class=\"soustitre2\"><strong>", $db->f("rguilde_libelle_rang"), "</strong></td>";
                echo "<td>", $tab_admin[$radmin], "</td>";
                echo "<td><a href=\"guilde_gere_rangs.php?methode=modif&rang=", $db->f("rguilde_cod"), "\">Renommer ? </a></td>";
                echo "<td>";
                if ($tab_admin[$radmin] == 'Membre' and $db->f("rguilde_rang_cod") == 1) {
                    echo "<em>(rang par défaut des nouveaux membres) </em>";
                }
                if ($db->f("rguilde_rang_cod") > 1) {
                    echo "<a href=\"guilde_gere_rangs.php?methode=efface&rang=", $db->f("rguilde_cod"), "\">Supprimer ? </a>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<a href=\"guilde_gere_rangs.php?methode=ajout\" class='centrer'>Ajouter un nouveau rang ?</a>";
            break;
        case "ajout":
            ?>
            <form name="ajout" method="post" action="guilde_gere_rangs.php">
                <input type="hidden" name="methode" value="ajout2">
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Nom du rang</strong></td>
                        <td><input type="text" name="nom"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2"><strong>Fonction</strong></td>
                        <td>
                            <select name="fonction">
                                <option value="N">Membre</option>
                                <option value="O">Administrateur</option>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" class="test centrer" value="Valider !"></td>
                    </tr>
                </table>
            </form>

            <?php
            break;
        case "ajout2":
            $req = "select max(rguilde_rang_cod) as resultat from guilde_rang where rguilde_guilde_cod = $num_guilde ";
            $db->query($req);
            $db->next_record();
            $rang = $db->f("resultat") + 1;
            $nom = htmlspecialchars($nom);
            $nom = str_replace(";", chr(127), $nom);
            $nom = str_replace("\\", " ", $nom);
            $nom = pg_escape_string($nom);
            $req = "insert into guilde_rang (rguilde_rang_cod,rguilde_guilde_cod,rguilde_libelle_rang,rguilde_admin) ";
            $req = $req . "values ($rang,$num_guilde,e'$nom','$fonction') ";
            if ($db->query($req)) {
                echo "<p>Le nouveau rang a été inséré !";
            } else {
                echo "<p>Une anomalie est survenue !";
            }
            echo "<a href=\"guilde_gere_rangs.php\" class='centrer'>Retour à la gestion des rangs</a>";
            break;
        case "modif":
            $req = "select rguilde_libelle_rang from guilde_rang where rguilde_cod = $rang ";
            $db->query($req);
            $db->next_record();
            ?>
            Entrez ici le nouveau rang souhaité en remplacement de
            <strong><?php echo $db->f("rguilde_libelle_rang"); ?></strong>
            <form name="modif" method="post" action="guilde_gere_rangs.php">
                <input type="hidden" name="methode" value="modif2">
                <input type="hidden" name="rang" value="<?php echo $rang; ?>">
                <input type="text" name="nom"> <input type="submit" class="test" value="Valider !">
            </form>
            <?php
            break;
        case "modif2":
            $nom = htmlspecialchars($nom);
            $nom = str_replace(";", chr(127), $nom);
            $nom = str_replace("\\", " ", $nom);
            $nom = pg_escape_string($nom);
            $req = "update guilde_rang set rguilde_libelle_rang = e'$nom' where rguilde_cod = $rang ";
            if ($db->query($req)) {
                echo "<p>Le rang a été modifié !";
            } else {
                echo "<p>Une anomalie est survenue !";
            }
            echo "<a href=\"guilde_gere_rangs.php\" class='centrer'>Retour à la gestion des rangs</a>";
            break;
        case "efface":
            $req = "select rguilde_rang_cod from guilde_rang where rguilde_cod = $rang ";
            $db->query($req);
            $db->next_record();
            $erreur = 0;
            $rg = $db->f("rguilde_rang_cod");
            $req = "select pguilde_cod from guilde_perso,perso where pguilde_guilde_cod = $num_guilde ";
            $req = $req . "and pguilde_rang_cod = $rg ";
            $req = $req . "and pguilde_valide = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "and perso_actif != 'N' ";
            $db->query($req);
            if ($db->nf() == 0) {
                $req = "delete from guilde_rang where rguilde_cod = $rang ";
                $db->query($req);
                echo "Le rang a bien été supprimé.";
            } else {
                echo "<p>Erreur ! Il existe encore des membres portant ce rang, il est impossible de le supprimer !";
            }
            echo "<a class='centrer' href=\"guilde_gere_rangs.php\">Retour à la gestion des rangs</a>";
            break;
    }
} else {
    echo "<p>Vous n'êtes pas un administrateur de guilde !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

