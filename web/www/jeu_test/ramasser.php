<?php
include "blocks/_header_page_jeu.php";

ob_start();
$methode = get_request_var('methode', 'debut');
//début 2nd tableau

$perso = new perso;
$perso = $verif_connexion->perso;


switch ($methode)
{
    case "debut":
        if ($perso->nb_or_case() > 2)
        {
            ?>
            <a href="regrouper.php">Regrouper les brouzoufs (2PA)</a><br>
            <?php
        }
        ?>
        <script type='text/javascript'>
            var nombreObjets = 0;

            function cocheDecoche(valeur) {
                if (valeur)
                    nombreObjets++;
                else
                    nombreObjets--;
                if (nombreObjets > 1) {
                    document.getElementById('boutonH').value = 'Ramasser les ' + nombreObjets + ' objets cochés !';
                    document.getElementById('boutonB').value = 'Ramasser les ' + nombreObjets + ' objets cochés !';
                } else if (nombreObjets == 1) {
                    document.getElementById('boutonH').value = 'Ramasser l’objet coché !';
                    document.getElementById('boutonB').value = 'Ramasser l’objet coché !';
                } else {
                    document.getElementById('boutonH').value = 'Cochez les objets à ramasser.';
                    document.getElementById('boutonB').value = 'Cochez les objets à ramasser.';
                }
            }
        </script>
        <form name="ramasser" method="post" action="ramasser.php">
            <table width="100%" cellspacing="2" cellapdding="2">
                <input type="hidden" name="methode" value="suite">
                <?php

                // On recherche les position et vue du perso
                $req_info_joueur = "select pos_cod from perso,perso_position,positions ";
                $req_info_joueur = $req_info_joueur . "where perso_cod = $perso_cod ";
                $req_info_joueur = $req_info_joueur . "and ppos_perso_cod = perso_cod ";
                $req_info_joueur = $req_info_joueur . "and ppos_pos_cod = pos_cod";
                $stmt            = $pdo->query($req_info_joueur);
                $result          = $stmt->fetch();
                $position        = $result['pos_cod'];

                ?>
                <tr>
                    <td colspan="3" class="soustitre"><span class="soustitre">Ramasser des objets</span></td>
                </tr>
                <tr>
                    <td colspan="3" class="soustitre2"><input type="submit" class="test"
                                                              value="Cochez les objets à ramasser." id='boutonH'/></td>
                </tr>
                <?php

                //******************************************
                //            O B J E T S                 **
                //******************************************
                // On recherche les objets en vue
                $req_vue_joueur = "select obj_nom_generique,tobj_libelle,obj_cod,obj_nom ";
                $req_vue_joueur = $req_vue_joueur . "from objet_generique,objet_position,objets,type_objet  ";
                $req_vue_joueur = $req_vue_joueur . "where pobj_pos_cod = $position ";
                $req_vue_joueur = $req_vue_joueur . "and pobj_obj_cod = obj_cod ";
                $req_vue_joueur = $req_vue_joueur . "and obj_gobj_cod = gobj_cod ";
                $req_vue_joueur = $req_vue_joueur . "and gobj_tobj_cod = tobj_cod ";
                $req_vue_joueur = $req_vue_joueur . "order by tobj_libelle, obj_nom_generique, obj_cod ";
                $stmt           = $pdo->query($req_vue_joueur);

                // on affiche la ligne d'en tête objets
                ?>
                <tr>
                    <td class="soustitre2" width="20"></td>
                    <td class="soustitre2"><strong>Nom</strong></td>
                    <td class="soustitre2"><strong>Type objet</strong></td>
                </tr>
                <?php
                if ($stmt->rowCount() != 0)
                {

                    $nb_objets = 1;
                    // on boucle sur les joueurs "visibles"
                    while ($result = $stmt->fetch())
                    {
                        echo("<tr>");
                        $objet     = $result['obj_cod'];
                        $identifie = $perso->is_identifie_objet($objet);
                        $ramassable = $perso->is_ramasse_objet($objet);
                        echo "<td><input type=\"checkbox\" ".($ramassable ? "" : " disabled " )."class=\"vide\" name=\"objet[" . $result['obj_cod'] . "]\" value=\"0\" id=\"" . $result['obj_cod'] . "\" onchange=\"cocheDecoche(this.checked);\"></td>";
                        if ($identifie)
                        {
                            echo "<td class=\"soustitre2\"><label for=\"" . $result['obj_cod'] . "\"><strong>" . $result['obj_nom'] . "</strong></label></td>";
                        } else
                        {
                            echo "<td class=\"soustitre2\"><label for=\"" . $result['obj_cod'] . "\"><strong>" . $result['obj_nom_generique'] . "</strong></label></td>";
                        }
                        echo "<td>" . $result['tobj_libelle'] . "</td>";

                        echo "</tr>";
                    }
                }

                //******************************************
                //            T H U N E                   **
                //******************************************
                // On recherche les brouzoufs en vue
                $req_vue_joueur = "select por_qte,por_cod ";
                $req_vue_joueur = $req_vue_joueur . "from or_position ";
                $req_vue_joueur = $req_vue_joueur . "where por_pos_cod = $position ";
                $stmt           = $pdo->query($req_vue_joueur);
                if ($stmt->rowCount() != 0)
                {
                    $nb_objets = 1;
                    // on boucle sur les joueurs "visibles"
                    while ($result = $stmt->fetch())
                    {
                        echo "<tr>";
                        echo "<td><input type=\"checkbox\" class=\"vide\" name=\"br[" . $result['por_cod'] . "]\" value=\"0\" id=\"" . $result['por_cod'] . "\"  onchange='cocheDecoche(this.checked)'></td>";
                        echo "<td class=\"soustitre2\" colspan=\"2\"><label for=\"" . $result['por_cod'] . "\"><strong>" . $result['por_qte'] . " brouzoufs</strong></label></td>";

                        echo "</tr>";
                    }
                }

                //fin 2nd tableau
                ?>
            </table>
            <input type="submit" class="test" value="Cochez les objets à ramasser." id='boutonB'/>
        </form>
        <?php
        break;
    case "suite":
        $pa     = $perso->perso_pa;
        $erreur = 0;
        $total  = 0;
        $non_ramassable  = 0;
        if (isset($_REQUEST['objet']))
        {

            foreach ($_REQUEST['objet'] as $key => $val)
            {
                $total = $total + 1;
                if (! $perso->is_ramasse_objet($key) ){
                    $non_ramassable ++ ;
                }
            }
        }
        if (isset($_REQUEST['br']))
        {
            foreach ($_REQUEST['br'] as $key => $val)
            {
                $total = $total + 1;
            }
        }
        if ($pa < $total)
        {
            echo "<p>Vous n’avez <b>pas assez de PA</b> pour ramasser tous ces objets !</p>";
            $erreur = 1;
        }
        if ($non_ramassable > 0)
        {
            echo "<p>Vous avez sélection des objets <b>non-ramassables!</b></p>";
            $erreur = 1;
        }
        if ($erreur == 0)
        {
            if (isset($_REQUEST['objet']))
            {
                foreach ($_REQUEST['objet'] as $key => $val)
                {
                    $req_ramasser = "select ramasse_objet($perso_cod,$key) as resultat";
                    $stmt         = $pdo->query($req_ramasser);
                    $result       = $stmt->fetch();
                    echo $result['resultat'];
                }
            }
            if (isset($_REQUEST['br']))
            {
                foreach ($_REQUEST['br'] as $key => $val)
                {
                    $req_ramasser = "select ramasse_or($perso_cod,$key) as resultat";
                    $stmt         = $pdo->query($req_ramasser);
                    $result       = $stmt->fetch();
                    echo $result['resultat'];
                }
            }
        }

        break;
}
$contenu_page = ob_get_contents();
ob_end_clean();

include "blocks/_footer_page_jeu.php";
