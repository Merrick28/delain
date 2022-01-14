<?php
define('APPEL', 1);
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
    <p class="titre">Création d’un piège</p>
<?php

$droit_modif = 'dcompt_modif_perso';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{

    $mode             = get_request_var('mode', 'normal');
    $methode          = get_request_var('methode', 'debut');

    switch ($methode)
    {
        case "debut":
            ?>
            <p>Choisissez votre méthode :</p>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=cre">Création d’un nouveau piège ?</a><br>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste">Liste des pièges existants et possibilités de
                modification</a><br><br>
            <hr>;
            <?php
            $included = true;
            include "modif_etage6.php";
            break;
        case "cre": // création d’un nouveau piège
            echo '<strong><a href="' . $_SERVER['PHP_SELF'] . '?methode=debut">Retour au début</a></strong><br>';
            ?>
            <form name="cre" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <br> Pour créer un piège, il suffit d’indiquer les valeurs nécessaires à sa création.
                <br> Dans le cas où une valeur serait manquante, un 0 sera automatiquement intégré.
                <br> Si il y a indiqué "positif", cela signifie que la valeur à rentrer doit être positive, et si
                "négatif", avec un signe moins devant
                <br><strong>ATTENTION !</strong>, Si cela n’est pas respecté, pour certaines valeurs, cela peut poser un
                vrai problème ensuite ! (exemple du poison)
                <hr>
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
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Décalage de DLT en mn (positif)</td>
                            <td><input type="text" name="malus_dlt"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Malus de poison (positif)</td>
                            <td><input type="text" name="mal_poison"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Malus de déplace en PA en plus par déplacement (positif)</td>
                            <td><input type="text" name="mal_deplacement"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Malus d’esquive (négatif)</td>
                            <td><input type="text" name="mal_esquive"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Malus de dégâts (négatif)</td>
                            <td><input type="text" name="mal_deg"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Malus de vue (négatif)</td>
                            <td><input type="text" name="mal_vue"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Malus pour toucher (négatif)</td>
                            <td><input type="text" name="mal_touche"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Malus pour entendre les messages (positif)</td>
                            <td><input type="text" name="mal_son"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Malus pour les attaques (en PA, valeur positive)</td>
                            <td><input name="mal_attaque" type="text"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Dés de dégâts subits (correspond à un nombre de dés 10)</td>
                            <td><input name="mal_blessure" type="text"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Pourcentage de chances de déclencher le piège</td>
                            <td><input name="declenchement" type="text"></td>

                        </tr>
                        <tr>
                            <td class="soustitre2">Texte qui s’affichera lorsque le piège sera actionné<br><strong>Ne
                                    Pas utiliser les caractères # ou % dedans !</strong></td>
                            <td><textarea name="texte_event" cols="70" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" class="test centrer" value="Valider !"></td>
                        </tr>

                    </table>
                </div>
            </form>
            <?php
            break;
        case "cre1":
            // vérification de la présence d’une position
            $erreur = 0;
            $req      = 'select pos_cod,pos_fonction_arrivee
				from positions
				where pos_x = ' . $_POST['pos_x'] . '
				AND pos_y = ' . $_POST['pos_y'] . '
				AND pos_etage = ' . $_POST['pos_etage'];
            $stmt     = $pdo->query($req);
            if (!$result = $stmt->fetch())
            {
                /*********************************/
                /* Il n'existe pas de position ! */
                /*********************************/
                echo 'Aucune position trouvée !<br>
					<a href="' . $_SERVER['PHP_SELF'] . '?methode=cre">Retour au début</a>';
                break;
            } else
            {
                /*********************************/
                /* on stocke le pos_cod et le    */
                /* pos_fonction_arrivee          */
                /*********************************/

                $pos_cod             = $result['pos_cod'];
                $pos_fonction_arrive = $result['pos_fonction_arrivee'];
            }

            if ($pos_fonction_arrive == '')
            {
                /**************************************************/
                /* Il n’existe pas de fonction sur cette position */
                /**************************************************/
                // mise à 0 des valeurs vides pour les malus
                require_once "blocks/_admin_piege.php";
                echo "<p>L’insertion du piège s’est bien déroulée en " . $_POST['pos_x'] . "," . $_POST['pos_y'] . " au " . $_POST['pos_etage'] . " 
				<br>Le texte affiché sera : " . $_POST['texte_event'] . "
				<br> (si vide, texte standard)";
                ?>
                <br><strong><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=debut">Retour</a></strong><br>
                <?php
            } else
            {
                /******************************************/
                /* Il existe une fonction sur la position */
                /******************************************/
                //
                /*************************************************/
                /* on prend la variable $valide pour validation  */
                /* Si elle est à 1 on force l’update, sinon      */
                /* on affiche un message                         */
                /*************************************************/
                $valide = get_request_var('valide', 0);
                if ($valide != 1)
                {
                    require "blocks/_admin_fontaine_piege.php";
                } else
                    // Rien de bloquant, on met à jour
                {
                    require "blocks/_admin_piege.php";
                    echo "<p>L’insertion du piège s’est bien déroulée en " . $_POST['pos_x'] . "," . $_POST['pos_y'] . " au " . $_POST['pos_etage'] . " 
						<br>Le texte affiché sera : " . $_POST['texte_event'] . "
						<br><em> (si vide, texte standard)</em>";
                }
            }
            break;//Fin du process de création

        //Liste de l’ensemble des pièges existants
        case "liste":
            echo '<strong><a href="' . $_SERVER['PHP_SELF'] . '?methode=debut">Retour au début</a></strong><br>';
            $req  = "select pos_cod,pos_x,pos_y,pos_etage,pos_fonction_arrivee,etage_libelle 
				from positions,etage 
				where pos_etage = etage_numero 
				AND SUBSTR(pos_fonction_arrivee,1,5) = 'piege'
				order by pos_etage,pos_x,pos_y";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $pos_cod = $result['pos_cod'];
                echo '<br><strong>Piège :</strong>' . $result['pos_fonction_arrivee'] . '
				<br><strong>X : ' . $result['pos_x'] . ' / Y : ' . $result['pos_y'] . ' / Étage : </strong>' . $result['etage_libelle'] . '<br>
				<a href="' . $_SERVER['PHP_SELF'] . '?pos_cod=' . $pos_cod . '&methode=mod">Modifier la définition de ce piège ?</a><br><br>
				<a href="' . $_SERVER['PHP_SELF'] . '?pos_cod=' . $pos_cod . '&methode=sup">Supprimer ce piège ? <strong><em>(ATTENTION, action définitive !)</em></strong></a><hr>';
            }
            break;

        // Modification d’un piège existant
        case "mod":
            $req = "select pos_fonction_arrivee 
				from positions 
				where pos_cod = " . $pos_cod;
            $stmt     = $pdo->query($req);
            $result   = $stmt->fetch();
            $fonction = $result['pos_fonction_arrivee'];
            $fonction = str_replace('\\', '', $fonction);
            echo "Fonction d’origine : " . $fonction;
            $fonction  = str_replace(array(')', '\''), '', $fonction);
            $fac_piege = explode(",", $fonction);

            echo "<br><strong><a href=\"" . $_SERVER['PHP_SELF'] . "?methode=debut\">Retour au début</a></strong>";
            ?>
        <form name="mod" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <br> Pour modifier un piège, il suffit de corriger les valeurs présentes.
            <br>Un piège ne peut pas être déplacé, il faut le supprimer et le recréer ailleurs.
            <br> Dans le cas où une valeur serait manquante, un 0 sera automatiquement intégré.
            <br> Si il y a indiqué "positif", cela signifie que la valeur à rentrer doit être positive, et si "négatif",
            avec un signe moins devant
            <br><strong>ATTENTION !</strong>, Si cela n'est pas respecté, pour certaines valeurs, cela peut poser un
            vrai problème ensuite ! (exemple du poison)<br>
            <input type="hidden" name="methode" value="mod1">
            <input type="hidden" name="pos_cod" value="<?php echo $pos_cod ?>">
            <div class="centrer">
            <table>

                <tr>
                    <td class="soustitre2">Décalage de DLT en mn (positif)</td>
                    <td><input type="text" name="malus_dlt" value="<?php echo $fac_piege[1] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Malus de poison (positif)</td>
                    <td><input type="text" name="mal_poison" value="<?php echo $fac_piege[2] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Malus de déplace en PA en plus par déplacement (positif)</td>
                    <td><input type="text" name="mal_deplacement" value="<?php echo $fac_piege[3] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Malus d’esquive (négatif)</td>
                    <td><input type="text" name="mal_esquive" value="<?php echo $fac_piege[4] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Malus de dégâts (négatif)</td>
                    <td><input type="text" name="mal_deg" value="<?php echo $fac_piege[5] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Malus de vue (négatif)</td>
                    <td><input type="text" name="mal_vue" value="<?php echo $fac_piege[6] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Malus pour toucher (négatif)</td>
                    <td><input type="text" name="mal_touche" value="<?php echo $fac_piege[7] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Malus pour entendre les messages (positif)</td>
                    <td><input type="text" name="mal_son" value="<?php echo $fac_piege[8] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Malus pour les attaques (en PA, valeur positive)</td>
                    <td><input name="mal_attaque" type="text" value="<?php echo $fac_piege[9] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Dés de dégâts subits (correspond à un nombre de dés 10)</td>
                    <td><input name="mal_blessure" type="text" value="<?php echo $fac_piege[10] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Pourcentage de chances de déclencher le piège</td>
                    <td><input name="declenchement" type="text" value="<?php echo $fac_piege[11] ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Texte qui s’affichera lorsque le piège sera actionné<br><strong>Ne Pas
                            utiliser les caractères # ou % dedans !</strong></td>
                    <td><textarea name="texte_event" cols="70" rows="5"> <?php echo $fac_piege[12] ?></textarea></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" class="test centrer" value="Valider !"></td>
                </tr>
            </table>
            <?php
            break;

        //La modification  d’un piège existant est traité ci-dessous en terme de MAJ
        case "mod1": // Résultat de la modification
            require "blocks/_admin_piege.php";
            echo "<p>Le piège a bien été modifié
				<br>Le texte affiché sera : " . $_POST['texte_event'] . "
				<br><em> (si vide, texte standard)</em>
				<br><a href=\"" . $_SERVER['PHP_SELF'] . "?methode=debut\">Retour au début</a>";
            break;

        case "sup": //Suppression d’un piège existant
            $req  = "update positions set pos_fonction_arrivee = '' where pos_cod = " . $pos_cod;
            $stmt = $pdo->query($req);
            echo "Piège supprimé
				<br><a href=\"" . $_SERVER['PHP_SELF'] . "?methode=debut\">Retour au début</a>";
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

