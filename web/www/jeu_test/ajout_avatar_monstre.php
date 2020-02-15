<?php
include "blocks/_header_page_jeu.php";
ob_start();
$methode          = get_request_var('methode', 'debut');


$droit_modif = 'dcompt_controle_admin';
include "blocks/_test_droit_modif_generique.php";


if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            $req = "select gmon_cod,gmon_nom from monstre_generique order by gmon_nom";
            $stmt = $pdo->query($req);
            ?>
            <p>Choisissez le compte à controler :
            <form action="<?php echo $PHP_SELF; ?>" metod="post">
                <input type="hidden" name="methode" value="et2">
                <select name="vmonstre">
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        ?>
                        <option value="<?php echo $result['gmon_cod']; ?>"><?php echo $result['gmon_nom']; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <input type="submit" class="test centrer" value="Suite !"><
            </form>
            <?php
            break;
        case "et2":
            $req = "select gmon_nom, gmon_avatar from monstre_generique where gmon_cod = $vmonstre";
            $stmt = $pdo->query($req);
            ?>
            <div class="centrer">
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Nom du monstre générique</strong></td>
                        <td class="soustitre2"><strong>Avatar</strong></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        ?>
                        <tr>
                            <td><?php echo $result['gmon_nom']; ?></td>
                            <td class="soustitre2"><img
                                        alt="avatar"
                                        src=http://www.jdr-delain.net/images/avatars/<?php echo $result['gmon_avatar']; ?>>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            </form>
            <?php
            break;
    }


} else
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";