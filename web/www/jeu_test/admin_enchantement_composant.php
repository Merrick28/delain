<?php
include "blocks/_header_page_jeu.php";
ob_start();
define('APPEL', 1);
// initialisation de la méthode

$methode2        = get_request_var('methode2', 'entree');
switch ($methode2)
{
    case "debut":
        ?>
        <table>
            <?php
            $req  = 'select gobj_cod,gobj_nom,gobj_description,gobj_tobj_cod,tobj_libelle 
                from objet_generique,type_objet
                where gobj_tobj_cod = tobj_cod
                and gobj_cod in (select oenc_gobj_cod from enc_objets)
                and not exists (select 1 from formule_produit where frmpr_gobj_cod = gobj_cod ) 
                order by tobj_libelle,gobj_nom';
            $stmt = $pdo->query($req);

            echo '<br><hr><td class="titre">Liste des composants d\'enchantement sans formule de création</td><br><br><table>
						<td><strong>Nom du composant</strong></td><td><strong>Type d\'objet</strong></td>';
            while ($result = $stmt->fetch())
            {
                echo '<tr><td class="soustitre2"><br><a href="' . $_SERVER['PHP_SELF'] . '?methode2=ajout&pot=' . $result['gobj_cod'] . '">' . $result['gobj_nom'] . '</a></td>
						<td class="soustitre2">' . $result['tobj_libelle'] . '</td></tr>';

            }
            ?>
        </table>
        <hr><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode2=ajout">Ou ajouter une nouvelle formule permettant
        d'obtenir un
        composant d'enchantement</a>
        <?php
        $req  =
            'select 	frmpr_frm_cod,frmpr_gobj_cod,frmpr_num,frm_cod,frm_type,frm_nom,
                    frm_comp_cod,frm_temps_travail 
                    from formule_produit,formule 
                    where frm_type = 3 
                    and frm_cod = frmpr_frm_cod 
                    order by frm_nom ';
        $stmt = $pdo->query($req);
        echo '<br><table><td class="titre">Liste des Composants déjà reliés à une pierre précieuse :</td><tr><br><br>
						<td><strong>Nom du composant</strong></td><td><strong>Objet nécessaire et quantités </strong></td><td><strong>Energie nécessaire</strong></td><td><strong>Compétence nécessaire</strong></td>';

        $req_composant = "select 	frmco_frm_cod,frmco_gobj_cod,frmco_num,gobj_nom 
                    from formule_composant,objet_generique	
                    where frmco_frm_cod = :cod_enchantement 
                    and frmco_gobj_cod = gobj_cod";
        $stmt2         = $pdo->prepare($req_composant);

        $req_comp = "select comp_libelle from competences	
				where comp_cod = :comp";
        $stmt3    = $pdo->prepare($req_comp);

        while ($result = $stmt->fetch())
        {
            $cod_enchantement = $result['frm_cod'];
            $comp             = $result['frm_comp_cod'];
            echo '<tr><td class="soustitre2"><br><a href="' . $_SERVER['PHP_SELF'] . '?methode2=modif&pot=' . $cod_enchantement . '">' . $result['frm_nom'] . '</a></td>';

            $stmt2 = $pdo->execute(array(":cod_enchantement" => $cod_enchantement), $stmt2);
            echo "<td>";
            while ($result2 = $stmt2->fetch())
            {
                echo $result2['gobj_nom'] . " \t" . $result2['frmco_num'] . "<br>";
            }

            echo "</td><td class=\"soustitre2\">" . $result['frm_temps_travail'] . "</td>";

            $stmt3   = $pdo->execute(array(":comp" => $comp), $stmt3);
            $result3 = $stmt3->fetch();
            echo "	<td class=\"soustitre2\">" . $result3['comp_libelle'] . "</td></tr>";

        }
        ?>
        </table>

        <?php
        break;
    case "ajout":
        $req = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where gobj_cod in (select oenc_gobj_cod from enc_objets)';
        if ($_REQUEST['pot'] != null)
        {
            $req .= 'and gobj_cod = ' . $_REQUEST['pot'];
        }
        $req    .= 'order by gobj_nom';
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        ?>
        <table>
        <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="methode2" value="ajout2">
        <tr>
            <td class="soustitre2">Nom / Description de la formule du composant (conserver le nom du composant
                dedans)
            </td>
            <td><textarea cols="50" rows="10" name="nom"><?php echo $result['gobj_nom'] ?></textarea></td>
        </tr>
        <tr>
            <td class="soustitre2">Energie nécessaire <em>(Le coût en énergie sera celui qui fera diminuer la
                    jauge d'énergie)</em></td>
            <td><input type="text" name="temps" value="40"></td>
        </tr>
        <tr>
            <td class="soustitre2">Cout en brouzoufs <em>non utilisé</em></td>
            <td><input type="text" name="pot_cout" value="0"></td>
        </tr>
        <tr>
            <td class="soustitre2">Résultat <em>(Non utilisé pour l'instant)</em></td>
            <td><input type="text" name="resultat" value="0"></td>
        </tr>
        <tr>
            <td class="soustitre2">Compétence</em></td>
            <td>
                <select name="competence">
                    <option value="88">Forgeamage Niveau 1</option>
                    ';
                    <option value="102">Forgeamage Niveau 2</option>
                    ';
                    <option value="103">Forgeamage Niveau 3</option>
                    ';
                </select>
                <em> <br>Cela correspond au niveau de forgeamage nécessaire.
                    <br>Mais on peut imaginer plusieurs formules pour un même composant, avec des compétences
                    différentes / <br><strong> Pas sûr que cela marche pour l'instant !</strong></em>

            </td>
        </tr>
        <tr>
        <td class="soustitre2">Composant d'enchantement concerné</em></td>
        <td>
        <select name="composant">
        <?php
        $req = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where gobj_cod in (select oenc_gobj_cod from enc_objets)';
        require "blocks/_admin_enchantement_potions.php";

        break;
    case "ajout2":
        $req_form_cod = "select nextval('seq_frm_cod') as numero";
        $stmt    = $pdo->query($req_form_cod);
        $result  = $stmt->fetch();
        $req     = 'insert into formule
								(frm_cod,frm_type,frm_nom,frm_temps_travail,frm_cout,frm_resultat,frm_comp_cod)
								values(:num_form,3,:nom,:temps,:cout,:resultat,:competence)';
        $stmt    = $pdo->prepare($req);
        $stmt    = $pdo->execute(array(
                                     ":num_form"   => $_REQUEST['num_form'],
                                     ":nom"        => $_REQUEST['nom'],
                                     ":temps"      => $_POST['temps'],
                                     ":cout"       => $_POST['pot_cout'],
                                     ":resultat"   => $_POST['resultat'],
                                     ":competence" => $_POST['competence']
                                 ), $stmt);
        $req     = 'insert into formule_produit
								(frmpr_frm_cod,frmpr_gobj_cod,frmpr_num)
								values(:num_form,:composant,:nombre)';
        $stmt    = $pdo->prepare($req);
        $stmt    = $pdo->execute(array(
                                     ":num_form"  => $_REQUEST['num_form'],
                                     ":composant" => $_REQUEST['composant'],
                                     ":nombre"    => $_POST['nombre']
                                 ), $stmt);
        echo "<p>La formule de base du composant d'enchantement a bien été insérée !<br>
				Pensez à inclure la pierre précieuse nécessaire pour ce composant. Autrement, il ne pourra jamais être produit<br>";
        ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode2=serie_obj&pot=<?php echo $num_form; ?>">Modifier la
        pierre
        précieuse associée à ce composant</a><br>
        <strong>Règle pour un composant d'enchantement :</strong>
        <br>Un composant est produit à partir d'une seule pierre précieuse. Pas d'autre règle pour les objets. L'énergie nécessaire est déterminée à partir de la formule de base (écran précédent)
        <br>
        <hr>
        <?php
        $action = get_request_var('action', '');
        require "_admin_enchantement_composant_blok1.php";
        break;
    case "modif":
        $req = 'select * from formule,formule_produit where frm_cod = :pot and frm_cod = frmpr_frm_cod';
        $stmt    = $pdo->prepare($req);
        $stmt    = $pdo->execute(array(":pot" => $pot), $stmt);
        $result  = $stmt->fetch();
        $cod_pot = $result['frmpr_gobj_cod'];
        ?>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode2=serie_obj&pot=<?php echo $pot; ?>">Modifier la liste
            d'objets</a><br>
        <table>
            <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode2" value="modif2">
                <input type="hidden" name="pot" value="<?php echo $pot; ?>">
                <input type="hidden" name="nom" value="<?php echo $result['frm_nom']; ?>">

                <tr>
                    <td class="soustitre2">Nom / Description de la formule du composant d'enchantement (conserver le nom
                        du composant dedans)
                    </td>
                    <td><textarea cols="50" rows="10" name="nom"><?php echo $result['frm_nom']; ?></textarea></td>
                </tr>
                <tr>
                    <td class="soustitre2">Energie nécessaire <em></em></td>
                    <td><input type="text" name="temps" value="<?php echo $result['frm_temps_travail']; ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Cout en brouzoufs <em>(Non utilisé pour l'instant)</em></td>
                    <td><input type="text" name="pot_cout" value="<?php echo $result['frm_cout']; ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Résultat <em>(Non utilisé pour l'instant)</em></td>
                    <td><input type="text" name="resultat" value="<?php echo $result['frm_resultat']; ?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Compétence</em></td>
                    <td>
                        <select name="competence">
                            <?php $s = $result['frm_comp_cod'];
                            $s1      = '';
                            $s2      = '';
                            $s3      = '';
                            if ($s == '88')
                            {
                                $s1 = 'selected';
                            } else if ($s == '102')
                            {
                                $s2 = 'selected';
                            } else if ($s == '103')
                            {
                                $s3 = 'selected';
                            }
                            ?>
                            <option value="88" <?php echo $s1 ?> >Forgeamage Niveau 1</option>
                            ';
                            <option value="102" <?php echo $s2 ?> >Forgeamage Niveau 2</option>
                            ';
                            <option value="103" <?php echo $s3 ?> >Forgeamage Niveau 3</option>
                            ';
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="soustitre2">Composant d'enchantement concerné</em></td>
                    <td>
                        <select name="potion">
                            <?php
                            $req_pot = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where gobj_cod in (select oenc_gobj_cod from enc_objets)
											order by gobj_nom';
                            $stmt    = $pdo->query($req);
                            while ($result = $stmt->fetch())
                            {
                                $sel    = '';
                                $potion = $result2['gobj_cod'];
                                if ($potion == $cod_pot)
                                {
                                    $sel = "selected";
                                }
                                echo '<option value="' . $result2['gobj_cod'] . '" ' . $sel . '> ' . $result2['gobj_nom'] . '</option>';
                            }
                            echo '</select><br>'; ?>
                    </td>
                </tr>
                <tr>
                    <td class="soustitre2">Nombre de composants produits <em>(Non utilisé pour l'instant)</em></td>
                    <td><input type="text" name="nombre" value="<?php echo $result['frmpr_num']; ?>"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" class="test" value="Valider"></td>
                </tr>


            </form>
        </table>
        <?php
        break;
    case "modif2":
        $req = 'update formule
								set frm_nom = :nom,
								frm_temps_travail = :temps,
								frm_cout = :cout,
								frm_resultat = :resultat,
								frm_comp_cod = :competence
								where frm_cod = :pot';
        $stmt    = $pdo->prepare($req);
        $stmt    = $pdo->execute(array(
                                     ":nom"        => $_POST['nom'],
                                     ":temps"      => $_POST['temps'],
                                     ":cout"       => $_POST['pot_cout'],
                                     ":resultat"   => $_POST['resultat'],
                                     ":competence" => $_POST['competence'],
                                     ":pot"        => $pot
                                 ), $stmt);
        $req     = 'update formule_produit
									set frmpr_gobj_cod = :potion,
									frmpr_num = :nombre
									where frmpr_frm_cod = :pot';
        $stmt    = $pdo->prepare($req);
        $stmt    = $pdo->execute(array(
                                     ":potion" => $_POST['potion'],
                                     ":nombre" => $_POST['nombre'],
                                     ":pot"    => $pot
                                 ), $stmt);
        if ($_POST['competence'] == '88')
        {
            $comp = 1;
        } else if ($_POST['competence'] == '102')
        {
            $comp = 2;
        } else if ($_POST['competence'] == '103')
        {
            $comp = 3;
        }

        echo "<p>La formule de base du composant d'enchantement a bien été modifiée !<br>
							Vous pouvez aussi modifier la pierre précieuse associée.<br>";
        ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode2=serie_obj&pot=<?php echo $pot; ?>">Modifier la pierre
        précieuse
        associée</a><br>
        <?php
        break;
    case "serie_obj":
        ?>
        <strong>Règle pour un composant d'enchantement :</strong>
        <br>Un composant est produit à partir d'une seule pierre précieuse. Pas d'autre règle pour les objets. L'énergie nécessaire est déterminée à partir de la formule de base (écran précédent)
        <br>
        <hr>
        <?php

        $action = get_request_var('action', '');
        require "_admin_enchantement_composant_blok1.php";
        break;
}
?>
    <p style="text-align:center;"><a href="<?php $_SERVER['PHP_SELF'] ?>?methode2=debut ">Retour au début</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";