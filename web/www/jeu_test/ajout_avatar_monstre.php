<?php
include "blocks/_header_page_jeu.php";
ob_start();
if (!isset($methode))
{
    $methode = "debut";
}
include "tab_haut.php";
$req = "select dcompt_controle_admin from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
    $droit['controle_admin'] = 'N';
} else
{
    $db->next_record();
    $droit['controle_admin'] = $db->f("dcompt_controle_admin");
}
if ($droit['controle_admin'] == 'O')
{
    switch ($methode)
    {
        case "debut":
            $req = "select gmon_cod,gmon_nom from monstre_generique order by gmon_nom";
            $db->query($req);
            ?>
            <p>Choisissez le compte à controler :
            <form action="<?php echo $PHP_SELF; ?>" metod="post">
                <input type="hidden" name="methode" value="et2">
                <select name="vmonstre">
                    <?php
                    while ($db->next_record())
                    {
                        ?>
                        <option value="<?php echo $db->f("gmon_cod"); ?>"><?php echo $db->f("gmon_nom"); ?></option>
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
            $db->query($req);
            ?>
            <div class="centrer">
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Nom du monstre générique</strong></td>
                        <td class="soustitre2"><strong>Avatar</strong></td>
                    </tr>
                    <?php
                    while ($db->next_record())
                    {
                        ?>
                        <tr>
                            <td><?php echo $db->f("gmon_nom"); ?></td>
                            <td class="soustitre2"><img
                                        alt="avatar"
                                        src=http://www.jdr-delain.net/images/avatars/<?php echo $db->f("gmon_avatar"); ?>>
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