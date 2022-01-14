<?php
include "blocks/_header_page_jeu.php";
ob_start();
$methode          = get_request_var('methode', 'debut');

$droit_modif = 'dcompt_controle_admin';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            $req =
                "select compt_cod,compt_nom from compte where (compt_admin = 'O' or compt_monstre = 'O') and compt_actif = 'O' order by compt_nom";
            $stmt = $pdo->query($req);
            ?>
            <p>Choisissez le compte à controler :
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" metod="post">
                <input type="hidden" name="methode" value="et2">
                <select name="vcompte">
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        ?>
                        <option value="<?php echo $result['compt_cod']; ?>"><?php echo $result['compt_nom']; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <input type="submit" class="test centrer" value="Suite !">
            </form>
            <?php
            break;
        case "et2":
            if (!isset($evt_start))
            {
                $evt_start = 0;
            }
            if ($evt_start < 0)
            {
                $evt_start = 0;
            }

            ?>
            <form method="post" name="controle" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="evt_start">
                <input type="hidden" name="methode" value="et2">
                <input type="hidden" name="vcompte" value="<?php echo $vcompte; ?>">
                <div class="centrer">
                    <table>
                        <tr>
                            <td class="soustitre2"><strong>Numéro de perso</strong></td>
                            <td class="soustitre2"><strong>Nom de perso</strong></td>
                            <td class="soustitre2"><strong>Date</strong></td>
                            <td class="soustitre2"><strong>page vue</strong></td>
                        </tr>

                        <tr>
                            <td>
                                <?php
                                if ($evt_start != 0)
                                {
                                    ?>
                                    <div align="left"><a
                                                href="javascript:document.controle.evt_start.value=<?php echo $evt_start; ?>-50;document.controle.submit();"><==
                                            Précédent</a></div>
                                    <?php
                                }
                                ?>
                            </td>
                            <td colspan="2"></td>
                            <td>
                            <td>
                                <div align="right"><a
                                            href="javascript:document.controle.evt_start.value=<?php echo $evt_start; ?>+50;document.controle.submit();">Suivant
                                        ==></a></div>
                            </td>
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

