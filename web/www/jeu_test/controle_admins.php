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
            $req = "select compt_cod,compt_nom from compte where (compt_admin = 'O' or compt_monstre = 'O') and compt_actif = 'O' order by compt_nom";
            $db->query($req);
            ?>
            <p>Choisissez le compte à controler :
            <form action="<?php echo $PHP_SELF; ?>" metod="post">
                <input type="hidden" name="methode" value="et2">
                <select name="vcompte">
                    <?php
                    while ($db->next_record())
                    {
                        ?>
                        <option value="<?php echo $db->f("compt_cod"); ?>"><?php echo $db->f("compt_nom"); ?></option>
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
            <form method="post" name="controle" action="<?php echo $PHP_SELF; ?>">
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
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');

