<?php
include "blocks/_header_page_jeu.php";
ob_start();
if (isset($_POST['methode']))
{
    switch ($methode)
    {
        case "valide_modif":
            //echo "modification de la description";
            
            $req_guilde = "select pguilde_rang_cod,rguilde_admin from guilde_perso,guilde_rang
															where pguilde_perso_cod = $perso_cod
															and pguilde_guilde_cod = rguilde_guilde_cod
															and pguilde_guilde_cod = $num_guilde
															and pguilde_rang_cod = rguilde_rang_cod
															and pguilde_valide = 'O' ";
            $stmt = $pdo->query($req_guilde);
            $result = $stmt->fetch();
            $admin = $result['rguilde_admin'];
            if ($admin == 'O')
            {
                if (!isset($_POST['noBR']) or ($noBR != "true"))
                {
                    $desc = nl2br($desc);
                }
                $desc = str_replace(";", chr(127), $desc);
                $desc = pg_escape_string($desc);
                $req_modif = "update guilde set guilde_description = e'$desc' where guilde_cod = $num_guilde ";
                $stmt = $pdo->query($req_modif);
                echo "<p><strong>La description de la guilde a été modifiée.</strong></p>";
            } else
            {
                echo "<p><strong>Vous n'êtes pas administrateur de cette guilde ! Vous ne pouvez pas modifier la description de cette guilde</strong></p>";
            }
            break;
    }
}
$req_desc = "select pguilde_guilde_cod,guilde_nom,guilde_description from guilde_perso,guilde where pguilde_perso_cod = $perso_cod and pguilde_guilde_cod = guilde_cod ";
$stmt = $pdo->query($req_desc);
$result = $stmt->fetch();
$num_guilde = $result['pguilde_guilde_cod'];
?>
    <form name="modif_guilde" method="post" action="modif_guilde.php">
        <input type="hidden" name="methode" value="valide_modif">
        <input type="hidden" name="num_guilde" value="<?php echo $num_guilde; ?>">
        <table>
            <tr>
                <td class="soustitre2"><p>Nom de la guilde :</td>
                <td class="soustitre2"><p><?php echo $result['guilde_nom']; ?></td>
            </tr>
            <?php
            $description = $result['guilde_description'];
            $desc = str_replace("<br />", "", $result['guilde_description']);
            ?>
            <tr>
                <td class="soustitre2"><p>Description :</td>
                <td><p><textarea name="desc" cols="100"
                                 rows="20"><?php echo str_replace(chr(127), ";", $desc); ?></textarea></td>
            </tr>

            <tr>
                <td colspan="2"><span class="centrer">Si votre description est rédigée en html et que vous ne voulez pas que les balises &lt;br&gt;
s'ajoutent automatiquement à la fin de chaque ligne cochez cette case:<input type="checkbox" class="test" name="noBR"
                                                                             value="true"></span></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" class="test centrer" value="Valider les changements"></td>
            </tr>
        </table>
    </form>

    <HR>
    <p> DESCRIPTION ACTUELLE:</p>

<?php

echo '<table width="100%"><tr><td class="titre"><p class="titre">', $result['guilde_nom'], '</td></tr></table>';
echo "<p>" . str_replace(chr(127), ";", $description) . "</p>";


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";


