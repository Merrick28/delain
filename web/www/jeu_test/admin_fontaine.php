<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
    <p class="titre">Création d’une fontaine</p>
<?php
$erreur = 0;

$droit_modif = 'modif_perso';
include "blocks/_test_droit_modif_generique.php";

if($erreur == 0) {
    if (!isset($mode))
        $mode = "normal";
    if (!isset($methode))
        $methode = "debut";
    switch ($methode) {
        case "debut":
            ?>
            <p>Choisissez votre méthode :</p>
            <a href="<?php echo $PHP_SELF; ?>?methode=cre">Création d’une nouvelle fontaine ?</a><br>
            <a href="<?php echo $PHP_SELF; ?>?methode=liste">Liste des existantes existants et possibilités de
                modification</a>
            <br><br>
            <hr>
            <?php
            $included = true;
            include "modif_etage6.php";
            break;

        case "cre": // Création d’une nouvelle fontaine
            echo '<strong><a href="' . $PHP_SELF . '?methode=debut">Retour au début</a></strong><br>';
            ?>
            <form name="cre" method="post" action="<?php echo $PHP_SELF; ?>">
                <br> Pour créer une nouvelle fontaine, il suffit d’indiquer les valeurs nécessaires à sa création.
                <br> Attention à bien remplir toutes les informations
                <input type="hidden" name="methode" value="cre1">
                <div class="centrer">
                    <table>
                        <tr>
                            <td class="soustitre2">Position en X</td>
                            <td><input type="text" name="pos_x" value="<?php echo $pos_x ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Position en Y</td>
                            <td><input type="text" name="pos_y" value="<?php echo $pos_y ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Étage</td>
                            <td><select name="pos_etage">
                                    <?php
                                    echo $html->etage_select($pos_e);
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Gain en pv : nombre de dés</td>
                            <td><input type="text" name="gain_pv_nbre_des"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Gain en pv : valeur des dés</td>
                            <td><input type="text" name="gain_pv_des"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Gains en régénération (valeur du bonus)</td>
                            <td><input type="text" name="gain_regen"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Nombre de dlt du gain en régén</td>
                            <td><input type="text" name="gain_regen_nbre_dlt"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="centrer"><input type="submit" class="test" value="Valider !"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
            <?php
            break;

        case "cre1":
            // vérification de la présence d’une position
            $erreur = 0;
            $req = 'select pos_cod,pos_fonction_arrivee
				from positions
				where pos_x = ' . $_POST['pos_x'] . '
				AND pos_y = ' . $_POST['pos_y'] . '
				AND pos_etage = ' . $_POST['pos_etage'];
            $db->query($req);
            if ($db->nf() == 0) {
                /*********************************/
                /* Il n’existe pas de position ! */
                /*********************************/
                echo 'Aucune position trouvée !<br>
					<a href="' . $PHP_SELF . '?methode=cre">Retour au début</a>';
                break;
            } else {
                /*********************************/
                /* on stocke le pos_cod et le    */
                /* pos_fonction_arrivee          */
                /*********************************/
                $db->next_record();
                $pos_cod = $db->f("pos_cod");
                $pos_fonction_arrive = $db->f("pos_fonction_arrivee");
            }

            if ($pos_fonction_arrive == '') {
                /**************************************************/
                /* Il n’existe pas de fonction sur cette position */
                /**************************************************/
                // mise à 0 des valeurs vides pour les malus
                $fields = array(
                    'gain_pv_nbre_des',
                    'gain_pv_des',
                    'gain_regen',
                    'gain_regen_nbre_dlt',
                );
                foreach ($fields as $i => $value) {
                    if ($_POST[$fields[$i]] == '')
                        $_POST[$fields[$i]] = 0;
                }
                // modif de la table positions pour intégrer la fonction d’arrivée
                $piege = "deplace_fontaine([perso]," . $_POST['gain_pv_nbre_des'] . "," . $_POST['gain_pv_des'] . "," . $_POST['gain_regen'] . "," . $_POST['gain_regen_nbre_dlt'] . ")";
                echo($piege);
                $req = "update positions
					set pos_fonction_arrivee = '$piege'
					,pos_decor = 101
					where pos_cod = " . $pos_cod;
                $db->query($req);
                echo "<p>L’insertion de cette fontaine s’est bien déroulée en " . $_POST['pos_x'] . "," . $_POST['pos_y'] . " au " . $_POST['pos_etage'];
                ?>
                <br><strong><a href="<?php echo $PHP_SELF; ?>?methode=debut">Retour</a></strong><br>
                <?php
            } else {
                /******************************************/
                /* Il existe une fonction sur la position */
                /******************************************/
                //
                /*************************************************/
                /* on prend la variable $valide pour validation  */
                /* Si elle est à 1 on force l’update, sinon      */
                /* on affiche un message                         */
                /*************************************************/
                if (!isset($valide))
                    $valide = 0;
                if ($valide != 1) {
                    $db->next_record();
                    echo '<form method="post" name="piege" action="' . $PHP_SELF . '">';
                    //
                    // on remet les variables post qui vont bien
                    //
                    foreach ($_POST as $key => $val)
                        echo '<input type="hidden" name="' . $key . '" value="' . $val . '">';
                    // on rajoute la variable valide, et soit mise à jour, soit annulation
                    echo '<input type="hidden" name="valide" value="1">';
                    echo '</form>';
                    echo "<p> Cette case contient déjà un élément <br><strong>" . $pos_fonction_arrive;
                    echo "</strong><br> Souhaitez vous quand même effectuer la mise à jour ? <br><em>ATTENTION : cette mise à jour ne doit se réaliser que si il s’agit d’un autre piège, et pas pour une autre fonction !</em>";
                    echo '<br><a href="' . $PHP_SELF . '"?methode=cre">Non ?</a>';
                    echo '<br><a href="javascript:document.piege.submit();">Oui ?</a>';
                } else // Rien de bloquant, on met à jour
                {
                    $fields = array(
                        'gain_pv_nbre_des',
                        'gain_pv_des',
                        'gain_regen',
                        'gain_regen_nbre_dlt',
                    );
                    foreach ($fields as $i => $value) {
                        if ($_POST[$fields[$i]] == '')
                            $_POST[$fields[$i]] = 0;
                    }
                    // modif de la table positions pour intégrer la fonction d'arrivée
                    $piege = "deplace_fontaine([perso]," . $_POST['gain_pv_nbre_des'] . "," . $_POST['gain_pv_des'] . "," . $_POST['gain_regen'] . "," . $_POST['gain_regen_nbre_dlt'] . "," . $_POST['mal_deg'] . "," . $_POST['mal_vue'] . "," . $_POST['mal_touche'] . "," . $_POST['mal_son'] . "," . $_POST['mal_attaque'] . ")";
                    echo($piege);
                    $req = "update positions
						set pos_fonction_arrivee = '$piege'
						,pos_decor = 101
						where pos_cod = " . $pos_cod;
                    $db->query($req);
                    echo "<p>L’insertion de cette fontaine s’est bien déroulée en " . $_POST['pos_x'] . "," . $_POST['pos_y'] . " au " . $_POST['pos_etage'];
                }
            }
            break;//Fin du process de création

//Liste de l’ensemble des fontaines existantes
        case "liste":
            echo '<strong><a href="' . $PHP_SELF . '?methode=debut">Retour au début</a></strong><br>';
            $req = "select pos_cod,pos_x,pos_y,pos_etage,pos_fonction_arrivee,etage_libelle
				from positions,etage
				where pos_etage = etage_numero
					AND SUBSTR(pos_fonction_arrivee,1,16) = 'deplace_fontaine'
				order by pos_etage,pos_x,pos_y";
            $db->query($req);
            while ($db->next_record()) {
                $pos_cod = $db->f("pos_cod");
                echo '<br><strong>Fontaine :</strong>' . $db->f('pos_fonction_arrivee') . '
				<br><strong>X : ' . $db->f('pos_x') . ' / Y : ' . $db->f('pos_y') . ' / Étage : </strong>' . $db->f('etage_libelle') . '<br>
				<a href="' . $PHP_SELF . '?pos_cod=' . $pos_cod . '&methode=mod">Modifier la définition de cette fontaine ?</a><br><br>
				<a href="' . $PHP_SELF . '?pos_cod=' . $pos_cod . '&methode=sup">Supprimer cette fontaine ? <strong><em>(ATTENTION, action définitive !)</em></strong></a><hr>';
            }
            break;

// Modification d’une fontaine existante
        case "mod":
            $req = "select pos_fonction_arrivee
				from positions
				where pos_cod = " . $pos_cod;
            $db->query($req);
            $db->next_record();
            $fonction = $db->f("pos_fonction_arrivee");
            echo "Fonction d’origine : " . $fonction;
            $fac_piege = explode(",", $fonction);

            echo "<br><strong><a href=\"" . $PHP_SELF . "?methode=debut\">Retour au début</a></strong>";
            ?>
        <form name="mod" method="post" action="<?php echo $PHP_SELF; ?>">
            <br> Pour modifier une fontaine, il suffit de corriger les valeurs présentes.
            <br>Une fontaine ne peut pas être déplacée, il faut la supprimer et la recréer ailleurs
            <br> Dans le cas où une valeur serait manquante, un 0 sera automatiquement intégré.
            <input type="hidden" name="methode" value="mod1">
            <input type="hidden" name="pos_cod" value="<?php echo $pos_cod ?>">
            <div class="centrer">
            <table>
                <tr>
                    <td class="soustitre2">Gain de PV : nombre de PV</td>
                    <td><input type="text" name="gain_pv_nbre_des" value="<?php echo $fac_piege[1] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Gain PV : valeur des dés</td>
                    <td><input type="text" name="gain_pv_des" value="<?php echo $fac_piege[2] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Valeur du gain de régén</td>
                    <td><input type="text" name="gain_regen" value="<?php echo $fac_piege[3] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Nombre de dlt du gain de régén</td>
                    <td><input type="text" name="gain_regen_nbre_dlt" value="<?php echo $fac_piege[4] ?>"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="centrer"><input type="submit" class="test" value="Valider !"></div>
                    </td>
                </tr>
            </table>
            <?php
            break;

        //La modification  d’une fontaine existante est traitée ci-dessous en terme de MAJ
        case "mod1": // Résultat de la modification
            $fields = array(
                'gain_pv_nbre_des',
                'gain_pv_des',
                'gain_regen',
                'gain_regen_nbre_dlt',
            );
            foreach ($fields as $i => $value) {
                if ($_POST[$fields[$i]] == '')
                    $_POST[$fields[$i]] = 0;
            }
            // modif de la table positions pour intégrer la fonction d’arrivée
            $piege = "piege_param([perso]," . $_POST['gain_pv_nbre_des'] . "," . $_POST['gain_pv_des'] . "," . $_POST['gain_regen'] . "," . $_POST['gain_regen_nbre_dlt'] . "," . $_POST['mal_deg'] . "," . $_POST['mal_vue'] . "," . $_POST['mal_touche'] . "," . $_POST['mal_son'] . "," . $_POST['mal_attaque'] . ")";
            echo($piege);
            $req = "update positions set pos_fonction_arrivee = '$piege',pos_decor = 101 where pos_cod = " . $pos_cod;
            $db->query($req);
            echo "<p>La fontaine a bien été modifiée
				<br><a href=\"" . $PHP_SELF . "?methode=debut\">Retour au début</a>";
            break;

        case "sup": //Suppression d’une fontaine existante
            $req = "update positions set pos_fonction_arrivee = '',pos_decor = 101 where pos_cod = " . $pos_cod;
            $db->query($req);
            echo "Fontaine supprimée
				<br><a href=\"" . $PHP_SELF . "?methode=debut\">Retour au début</a>";
            break;
    }
}
if ($mode == 'popup')
    echo '<script type="text/javascript">document.getElementById("colonne1").style.display="none";
		document.getElementById("colonne2").style.marginLeft="0";
		</script>';

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
