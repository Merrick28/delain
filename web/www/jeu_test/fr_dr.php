<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = new perso;
$perso     = $verif_connexion->perso;

ob_start();

if (!isset($t_frdr) || $t_frdr === '')
    $t_frdr = 0;

if (!isset($_GET['aj']))
{
$is_locked = $perso->is_locked();
if ($is_locked)
    $ong[0] = 'Fuite';
else
    $ong[0] = 'Déplacement';
$ong[1] = 'Automap';
$ong[2] = 'Détail';
$ong[3] = 'Journal';
//

$nb = count($ong);
?>
    <script type="text/javascript">
        function changeOnglet_frdr(onglet, dest_action, dest_cadre) {
            <?php        for ($i = 0; $i < $nb; $i++)
            echo "		document.getElementById('onglet_frdr$i').className = 'pas_onglet';";
            ?>
            document.forms['destdroite'].action.value = dest_action;
            document.forms['destdroite'].destcadre.value = dest_cadre;
            document.getElementById('onglet_frdr' + onglet).className = 'onglet';
            //getdata('fr_dr.php?aj=1&t_frdr=' + onglet, 'frdr_contenu');

            $.ajax({
                url: 'fr_dr.php?aj=1&t_frdr=' + onglet,
                context: document.body
            }).done(function (data) {
                $("#frdr_contenu").html(data);
            });
        }
    </script>
<table cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <?php
        for ($cpt = 0; $cpt < $nb; $cpt++)
        {
            switch ($cpt)
            {
                case 0:
                    $destcadre  = "le_tout";
                    $destaction = "action.php";
                    break;
                case 1:
                    $destcadre  = "le_tout";
                    $destaction = "action.php";
                    break;
                case 2:
                    $destcadre  = "vue_droite";
                    $destaction = "fr_dr.php";
                    break;
                case 3:
                    $destcadre  = "vue_droite";
                    $destaction = "ong_journal.php";
                    break;
            }
            if ($cpt == $t_frdr)
            {
                $style = 'onglet';
            } else
            {
                $style = 'pas_onglet';
            }
            $lien   =
                "<a href='javascript:void(0);' onclick='changeOnglet_frdr($cpt, \"$destaction\", \"$destcadre\");'>";
            $f_lien = '</a>';
            echo '<td class="' . $style . '" id="onglet_frdr' . $cpt . '"><p style="text-align:center">' . $lien . $ong[$cpt] . $f_lien . '</p></td>';
        }
        ?>
    </tr>
    <tr>
        <td colspan="<?php echo $nb; ?> " class="reste_onglet" id="frdr_contenu">
            <?php
            }
            switch ($t_frdr)
            {
                case "0": // déplacement
                    include "ong_dep.php";
                    break;
                case "1": // automap
                    include "ong_automap.php";
                    break;
                case "2": // detail
                    include "ong_detail.php";
                    break;
                case "3": // journal
                    include "ong_journal.php";
                    break;
                case "4": // journal
                    include "ong_automapV2.php";
                    break;
                default: // déplacement
                    include "ong_dep.php";
                    break;
            }

            if (!isset($_GET['aj']))
            {
            ?>
        </td>
    </tr>
</table>
<?php
}
$vue_droite = ob_get_contents();
ob_end_clean();
if (!defined('APPEL_VUE'))
    echo $vue_droite;
?>
