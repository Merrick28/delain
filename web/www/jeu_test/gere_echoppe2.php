<?php
include "blocks/_header_page_jeu.php";
define('APPEL', 1);
$perso    = new perso;
$perso    = $verif_connexion->perso;
$tab_lieu = $perso->get_lieu();
ob_start();
$erreur = 0;
if (!isset($mag))
{
    echo "<p>Erreur sur la transmission du lieu_cod ";
    $erreur = 1;
}
if ($erreur == 0)
{
    $req  = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle,lieu_alignement ";
    $req  = $req . "from lieu,lieu_position,positions,etage,magasin_gerant ";
    $req  = $req . "where lieu_cod = lpos_lieu_cod ";
    $req  = $req . "and lieu_tlieu_cod in (11,14,21) ";
    $req  = $req . "and lpos_pos_cod = pos_cod ";
    $req  = $req . "and pos_etage = etage_numero ";
    $req  = $req . "and mger_lieu_cod = lieu_cod ";
    $req  = $req . "and mger_perso_cod = $perso_cod ";
    $req  = $req . "and lieu_cod = $mag ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() == 0)
    {
        echo "<p>Erreur, vous n'êtes pas en gérance de ce magasin !";
        $erreur = 1;
    } else
    {
        $result = $stmt->fetch();
    }
}
if ($erreur == 0)
{
    $methode        = get_request_var('methode', 'debut');
    echo "<p class=\"titre\">Gestion de : ", $result['lieu_nom'], " - (", $result['pos_x'], ", ", $result['pos_y'], ", ", $result['etage_libelle'], ")</p>";
    switch ($methode)
    {
        case "debut":
            require "blocks/_gere_echoppe_debut.php";
            break;
        case "mod":

            $req    = "select lieu_compte, lieu_marge, lieu_prelev, lieu_neutre, lieu_alignement ";
            $req    = $req . "from lieu ";
            $req    = $req . "where lieu_cod = $mag ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<div class='centrer'><table>";
            echo "<td class=\"soustitre2\"><p>Etat de la caisse</td>";
            echo "<td><p>" . $result['lieu_compte'] . " brouzoufs</td>";
            echo "<td><p><a href=\"gere_echoppe2.php?mag=$mag&methode=banque\">Faire un retrait ?</a><br>";
            echo "<a href=\"gere_echoppe2.php?mag=$mag&methode=depot\">Faire un depot ?</a></td>";
            echo "</tr>";

            echo "<td class=\"soustitre2\"><p>Marge effectuée</td>";
            echo "<td><p>" . $result['lieu_marge'] . " %</td>";
            echo "<td><p><a href=\"gere_echoppe2.php?mag=$mag&methode=marge\">Changer la marge ?</a></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Protection</td>";
            if ($result['lieu_prelev'] == 15)
            {
                $protection = "Votre magasin n'est pas un refuge";
            } else
            {
                $protection = "Votre magasin est un refuge";
            }
            echo "<td><p>" . $protection . "</td>";
            echo "<td><p><a href=\"gere_echoppe2.php?mag=$mag&methode=statut\">Changer le statut ?</a></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Alignement</td>";
            $neutre[0]  = "non neutre ";
            $neutre[1]  = "neutre ";
            $idx_neutre = $result['lieu_neutre'];
            echo "<td><p>", $result['lieu_alignement'], " - ", $neutre[$idx_neutre], "</td>";
            echo "<td><p><a href=\"gere_echoppe2.php?mag=$mag&methode=align\">Changer l'alignement ?</a></td>";
            echo "</tr>";


            echo "</table></div>";

            break;
        case "banque":
            $req    = "select lieu_compte,perso_po ";
            $req    = $req . "from lieu,perso ";
            $req    = $req . "where lieu_cod = $mag ";
            $req    = $req . "and perso_cod = $perso_cod ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>Vous avez actuellement <strong>" . $result['perso_po'] . "</strong> brouzoufs sur vous, et il y a <strong>" . $result['lieu_compte'] . "</strong> brouzoufs dans les caisses de l'échoppe.";
            echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"banque2\">";
            echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
            echo "<p>Retirer <input type=\"text\" name=\"qte\" value=\"0\"> brouzoufs du compte de l'échoppe ?";
            echo "<input type=\"submit\" class=\"test centrer\" value=\"Valider le transfert ?\">";
            echo "</form>";
            break;
        case "banque2":
            require "blocks/_gere_echoppe_banque2.php";
            break;
        case "depot":
            $req    = "select lieu_compte,perso_po ";
            $req    = $req . "from lieu,perso ";
            $req    = $req . "where lieu_cod = $mag ";
            $req    = $req . "and perso_cod = $perso_cod ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>Vous avez actuellement <strong>" . $result['perso_po'] . "</strong> brouzoufs sur vous, et il y a <strong>" . $result['lieu_compte'] . "</strong> brouzoufs dans les caisses de l'échoppe.";
            echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"depot2\">";
            echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
            echo "<p>Déposer <input type=\"text\" name=\"qte\" value=\"0\"> brouzoufs sur le compte de l'échoppe ?";
            echo "<input type=\"submit\" class=\"test centrer\" value=\"Valider le transfert ?\">";
            echo "</form>";
            break;
        case "depot2":
            require "blocks/_echoppe_depot2.php";
            break;
        case "marge":
            $v_ancienne_marge = false;
            require "blocks/_gere_echoppe_marge.php";
            break;
        case "marge2":
            $req    = "select lieu_prelev ";
            $req    = $req . "from lieu ";
            $req    = $req . "where lieu_cod = $mag ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            $prelev = $result['lieu_prelev'];
            $erreur = 0;
            if (!isset($qte))
            {
                echo "<p>Erreur ! marge non définie !";
                $erreur = 1;
            }
            if ($qte < $prelev)
            {
                echo "<p>Erreur ! Marge inférieur à l'autorisé ($prelev %) !";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $req  = "update lieu set lieu_marge = $qte where lieu_cod = $mag ";
                $stmt = $pdo->query($req);
                echo "<p>La modification a été effectuée.";
            }
            break;
        case "statut";
            $req    = "select lieu_prelev,lieu_marge ";
            $req    = $req . "from lieu ";
            $req    = $req . "where lieu_cod = $mag ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            if ($result['lieu_prelev'] == 15)
            {
                echo "<p>Votre magasin n'est pas un refuge. Si vous souhaitez le transformer en refuge, les prélèvements de l'administration passeront automatiquement à 30%.<br>";
                if ($result['lieu_marge'] < 30)
                {
                    echo "Votre marge est insuffisante pour accomplir cette action.";
                } else
                {
                    echo "<a href=\"gere_echoppe2.php?mag=$mag&methode=statut2&ref=o\">Passer cette échoppe en refuge ?</a>";
                }
            } else
            {
                echo "<p>Votre magasin est un refuge. Si vous souhaitez abandonner cette fonctionnalité, les prélèvements de l'administration passeront automatiquement à 15%.<br>";
                echo "<a href=\"gere_echoppe2.php?mag=$mag&methode=statut2&ref=n\">Abandonner le statut de refuge pour cette échoppe ?</a>";
            }

            break;
        case "statut2";
            if ($ref == 'n')
            {
                $req  = "update lieu set lieu_refuge = 'N',lieu_prelev = 15 where lieu_cod = $mag";
                $stmt = $pdo->query($req);
                echo "<p>La modification a été effectuée.";
            }
            if ($ref == 'o')
            {
                $req  = "update lieu set lieu_refuge = 'O',lieu_prelev = 30 where lieu_cod = $mag";
                $stmt = $pdo->query($req);
                echo "<p>La modification a été effectuée.";
            }
            break;
        case "nom";
            echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"nom2\">";
            echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
            $req    = "select lieu_nom,lieu_description from lieu ";
            $req    = $req . "where lieu_cod = $mag ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();

            echo "<table>";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Nom du magasin (70 caracs maxi)</td>";
            echo "<td><input type=\"text\" name=\"nom\" size=\"50\" value=\"" . $result['lieu_nom'] . "\"></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Description</td>";
            $desc = str_replace(chr(127), ";", $result['lieu_description']);
            echo "<td><textarea name=\"desc\" rows=\"10\" cols=\"50\">" . $desc . "</textarea></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td colspan=\"2\"><input type=\"submit\" class=\"test\" value=\"Valider les changements\"></td>";
            echo "</tr>";

            echo "</table>";

            echo "</form>";

            break;
        case "nom2":
            echo "<p><strong>Aperçu : " . $desc;
            $desc = str_replace(";", chr(127), $desc);
            $req  =
                "update lieu set lieu_nom = e'" . pg_escape_string($nom) . "', lieu_description = e'" . pg_escape_string($desc) . "' where lieu_cod = $mag ";
            $stmt = $pdo->query($req);
            echo "<p>Les changements sont validés !";
            break;
        case "vente_adm":
            require "blocks/_echoppe_vente_adm.php";
            break;
        case "vente_adm2":
            require "blocks/_echoppe_vente_adm2.php";
            break;
        case "achat_adm":
            require "blocks/_echoppe_achat_adm.php";
            break;
        case "achat_adm2":
            require "blocks/_echoppe_achat_adm2.php";
            break;
        case "fix_prix":
            require "blocks/_echoppe_fix_prix.php";
            break;
        case "fix_prix2":
            $req    = "select gobj_nom,gobj_valeur,f_prix_gobj($mag,gobj_cod) from objet_generique ";
            $req    = $req . "where gobj_cod = $gobj ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            $min    = floor(($result['gobj_valeur'] * 0.8));
            $max    = floor(($result['gobj_valeur'] * 1.2));
            echo "<p>Le tarif officiel est de " . $result['gobj_valeur'] . " brouzoufs<br>";
            echo "<p>Vous pouvez fixer un nouveau tarif qui ne doit pas être inférieur ou supérieur à 20% du tarif officiel (entre $min et $max brouzoufs).";
            echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"fix_prix3\">";
            echo "<input type=\"hidden\" name=\"annul\" value=\"n\">";
            echo "<input type=\"hidden\" name=\"gobj\" value=\"$gobj\">";
            echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
            echo "<p>Entrez le nouveau prix : <input type=\"text\" name=\"n_prix\"> brouzoufs<br>";
            echo "ou bien <a href=\"javascript:document.echoppe.annul.value='o';document.echoppe.submit();\">cliquez ici</a> pour utiliser le tarif officiel.";
            echo "<input type=\"submit\" value=\"Valider\" class=\"test centrer\">";
            echo "</form>";
            break;
        case "fix_prix3":
            $erreur = 0;
            if ($annul == 'n')
            {
                $req    = "select gobj_nom,gobj_valeur,f_prix_gobj($mag,gobj_cod) from objet_generique ";
                $req    = $req . "where gobj_cod = $gobj ";
                $stmt   = $pdo->query($req);
                $result = $stmt->fetch();
                $min    = floor(($result['gobj_valeur'] * 0.8));
                $max    = floor(($result['gobj_valeur'] * 1.2));
                if ($n_prix < $min)
                {
                    echo "<p>Le tarif fixé est inférieur au tarif possible ($min brouzoufs).";
                    $erreur = 1;
                }
                if ($n_prix > $max)
                {
                    echo "<p>Le tarif fixé est supérieur au tarif possible ($max brouzoufs).";
                    $erreur = 1;
                }
                if ($erreur == 0)
                {
                    $req  = "delete from magasin_tarif ";
                    $req  = $req . "where mtar_lieu_cod = $mag ";
                    $req  = $req . "and mtar_gobj_cod = $gobj ";
                    $stmt = $pdo->query($req);
                    $req  =
                        "insert into magasin_tarif  (mtar_lieu_cod,mtar_gobj_cod,mtar_prix) values  ($mag,$gobj,$n_prix) ";
                    $stmt = $pdo->query($req);
                    echo "<p>Le tarif a été changé !";


                }
            } else
            {
                $req  = "delete from magasin_tarif ";
                $req  = $req . "where mtar_lieu_cod = $mag ";
                $req  = $req . "and mtar_gobj_cod = $gobj ";
                $stmt = $pdo->query($req);
                echo "<p>Votre échoppe vendra maintenant cet objet au tarif officiel.";
            }
            break;
        case "stats":
            $sens[1] = "vente (magasin vers aventurier) ";
            $sens[2] = "achat (aventurier vers magasin) ";
            $req     = "select gobj_nom,mtra_sens,sum(mtra_montant) as somme,count(mtra_cod) as nombre ";
            $req     = $req . "from objet_generique,objets,mag_tran ";
            $req     = $req . "where mtra_lieu_cod = $mag ";
            $req     = $req . "and mtra_obj_cod = obj_cod ";
            $req     = $req . "and obj_gobj_cod = gobj_cod ";
            $req     = $req . "group by gobj_nom,mtra_sens ";
            $stmt    = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<p>Aucune transaction enregistrée dans votre échoppe.";
            } else
            {
                ?>
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Nom</strong></td>
                        <td class="soustitre2"><strong>Sens</strong></td>
                        <td class="soustitre2"><strong>Montant global</strong></td>
                        <td class="soustitre2"><strong>Nombre</strong></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        echo "<tr>";
                        echo "<td class=\"soustitre2\">", $result['gobj_nom'], "</td>";
                        $idx_sens = $result['mtra_sens'];
                        echo "<td>", $sens[$idx_sens], "</td>";
                        echo "<td class=\"soustitre2\">", $result['somme'], "</td>";
                        echo "<td>", $result['nombre'], "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <?php
                echo "<a class=\"centrer\" href=\"gere_echoppe2.php?mag=$mag&methode=stats2\">Voir le détail</a>";
            }


            break;
        case "stats2":
            $sens[1] = "vente (magasin vers aventurier) ";
            $sens[2] = "achat (aventurier vers magasin) ";
            $req     =
                "select mtra_date,gobj_nom,mtra_sens,mtra_montant,perso_nom,to_char(mtra_date,'DD/MM/YYYY hh24:mi:ss') as date_tran ";
            $req     = $req . "from objet_generique,objets,mag_tran,perso ";
            $req     = $req . "where mtra_lieu_cod = $mag ";
            $req     = $req . "and mtra_obj_cod = obj_cod ";
            $req     = $req . "and obj_gobj_cod = gobj_cod ";
            $req     = $req . "and mtra_perso_cod = perso_cod ";
            $req     = $req . "order by mtra_date ";
            $stmt    = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<p>Aucune transaction enregistrée dans votre échoppe.";
            } else
            {
                ?>
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Objet</strong></td>
                        <td class="soustitre2"><strong>Perso</strong></td>
                        <td class="soustitre2"><strong>Sens</strong></td>
                        <td class="soustitre2"><strong>Montant</strong></td>
                        <td class="soustitre2"><strong>Date</strong></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        echo "<tr>";
                        echo "<td class=\"soustitre2\">", $result['gobj_nom'], "</td>";
                        echo "<td>", $result['perso_nom'], "</td>";
                        $idx_sens = $result['mtra_sens'];
                        echo "<td class=\"soustitre2\">", $sens[$idx_sens], "</td>";
                        echo "<td>", $result['mtra_montant'], "</td>";
                        echo "<td class=\"soustitre2\">", $result['date_tran'], "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <?php
            }


            break;
        case "align":
            $stmt = $pdo->query("select lieu_alignement from lieu where lieu_cod = $mag ");
            $result  = $stmt->fetch();
            ?>
            <form name="aligne" method="post" action="gere_echoppe2.php">
                <input type="hidden" name="methode" value="align2">
                <input type="hidden" name="mag" value="<?php echo $mag; ?>">
                Alignement : <input type="text" name="valeur" value="<?php echo $result['lieu_alignement']; ?>"><br>
                Cochez cette case pour faire de cette échoppe une zone neutre (les prix ne dépendent plus du karma)
                <input type="checkbox" name="neutre" value="1">
                <input type="submit" class="test centrer" value="Valider les changements !">
            </form>
            <?php
            break;
        case "align2":
            $erreur = 0;
            if (!isset($valeur))
            {
                echo "<p>Erreur ! Valeur non fixée ! ";
                $erreur = 1;
            }
            if ($valeur == '')
            {
                echo "<p>Erreur ! Valeur non fixée ! ";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $req  = "update lieu set lieu_alignement = $valeur where lieu_cod = $mag ";
                $stmt = $pdo->query($req);
                if ($neutre == 1)
                {
                    $req  = "update lieu set lieu_neutre = 1 where lieu_cod = $mag ";
                    $stmt = $pdo->query($req);
                } else
                {
                    $req  = "update lieu set lieu_neutre = 0 where lieu_cod = $mag ";
                    $stmt = $pdo->query($req);

                }
                echo "<p>Modifications effectuées !";
            }


            break;
    }
    echo "<a class=\"centrer\# href=\"gere_echoppe2.php?mag=$mag\">Retour à la gestion de l'échoppe</a>";
    echo "<a class=\"centrer\# href=\"gere_echoppe.php\">Retour à la liste des échoppes gérees</a>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
