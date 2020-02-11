<?php
include "blocks/_header_page_jeu.php";
ob_start();

$droit_modif = 'dcompt_modif_gmon';
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    include "admin_edition_header.php";

    // TRAITEMENT DE FORMULAIRE
    if (isset($_POST['methode']))
    {
        switch ($methode)
        {
            case "creer_serie":
                $req  =
                    "insert into serie_equipement (seequ_nom,seequ_proba_sans_objet ) values ('$seequ_nom',$seequ_proba_sans_objet)";
                $stmt = $pdo->query($req);
                echo "<p>CREATION</p>";
                break;
            case "modifier_serie":
                $req  = "update serie_equipement set seequ_nom = '$seequ_nom'"
                        . ",seequ_proba_sans_objet = $seequ_proba_sans_objet"
                        . " where seequ_cod = $seequ_cod";
                $stmt = $pdo->query($req);
                echo "<p>MODIFICATION</p>";
                break;
            case "ajouter_serie_element":
                if ($seequo_gobj_cod != 'null')
                {
                    $req  =
                        "insert into serie_equipement_objet (seequo_seequ_cod,seequo_gobj_cod,seequo_proba,seequo_etat_min,seequo_etat_max ) values ($seequo_seequ_cod,$seequo_gobj_cod,$seequo_proba,$seequo_etat_min,$seequo_etat_max)";
                    $stmt = $pdo->query($req);
                    echo "<p>AJOUT</p>";
                }
                break;
            case "modifier_serie_element":
                if ($seequo_gobj_cod != 'null')
                {
                    $req  =
                        "update serie_equipement_objet set seequo_gobj_cod = $seequo_gobj_cod,seequo_proba = $seequo_proba,seequo_etat_min = $seequo_etat_min,seequo_etat_max = $seequo_etat_max where seequo_cod = $seequo_cod";
                    $stmt = $pdo->query($req);
                    echo "<p>MAJ</p>";
                }
                break;
            case "supprimer_serie_element":
                $req  = "delete from serie_equipement_objet where seequo_cod = $seequo_cod";
                $stmt = $pdo->query($req);
                echo "<p>DELETE</p>";
                break;

        }
    }


    $req  = "select seequ_cod,seequ_nom,seequ_proba_sans_objet from serie_equipement order by seequ_cod";
    $stmt = $pdo->query($req);
    ?>
    <HR>
    CREER UNE NOUVELLE SERIE:<BR>
    <form action="admin_serie_equipements.php" method="post">
        <input type="hidden" name="methode" value="creer_serie">
        SERIE <input type="text" name="seequ_nom" value="">
        Chances sans objet:<input type="text" name="seequ_proba_sans_objet" value="">
        <input type="submit" value="Créer">
    </form>
    <?php
    while ($result = $stmt->fetch())
    {
        ?>
        <HR>
        <form action="admin_serie_equipements.php" method="post">
            <input type="hidden" name="methode" value="modifier_serie">
            <input type="hidden" name="seequ_cod" value="<?php echo $result['seequ_cod'] ?>">
            SERIE <input type="text" name="seequ_nom" value="<?php echo $result['seequ_nom'] ?>">
            Chances sans objet:<input type="text" name="seequ_proba_sans_objet"
                                      value="<?php echo $result['seequ_proba_sans_objet'] ?>">
            <input type="submit" value="Modifier">
        </form>
        <table width="80%" align="center" border="1">
            <tr>
                <td>Arme</td>
                <td>Chance</td>
                <td>Etat mini</td>
                <td>Etat maxi</td>
            </tr>
            <form action="admin_serie_equipements.php" method="post">
                <input type="hidden" name="methode" value="ajouter_serie_element">
                <input type="hidden" name="seequo_seequ_cod" value="<?php echo $result['seequ_cod'] ?>">
                <tr>
                    <td>
                        <SELECT name="seequo_gobj_cod">
                            <option value="null">aucune</option>
                            <?php // LISTE DES ARMES ET ARMURES
                            $req_armes =
                                "select 	gobj_cod,gobj_nom from objet_generique where gobj_tobj_cod IN (1,2) order by gobj_nom";
                            $db_armes  = new base_delain;
                            $stmt2     = $pdo->query($req_armes);
                            while ($result2 = $stmt2->fetch)
                            {
                                $arme_cod = $result2['gobj_cod'];
                                echo "<OPTION value=\"$arme_cod\">" . $result2['gobj_nom'] . "</OPTION>\n";
                            }
                            ?>
                        </SELECT>
                    </td>
                    <td>
                        <input type="text" name="seequo_proba" value="">
                    </td>
                    <td><input type="text" name="seequo_etat_min" value="100"></td>
                    <td><input type="text" name="seequo_etat_max" value="100"></td>
                    <td><input type="submit" value="Ajouter"></td>
                </tr>
            </form>
            <?php
            $seequ_cod = $result['seequ_cod'];

            $req       =
                "select seequo_cod,seequo_gobj_cod,seequo_proba,seequo_etat_min,seequo_etat_max from  	serie_equipement_objet where seequo_seequ_cod = $seequ_cod ";
            $stmt2 = $pdo->query($req);
            while ($result2 = $stmt2->fetch())
            {
                ?>
                <form action="admin_serie_equipements.php" method="post">
                    <input type="hidden" name="methode" value="modifier_serie_element">
                    <input type="hidden" name="seequo_cod" value="<?php echo $result2['seequo_cod'] ?>">
                    <tr>
                        <td>
                            <SELECT name="seequo_gobj_cod">
                                <option value="null">aucune</option>
                                <?php // LISTE DES ARMES ET ARMURES
                                $req_armes =
                                    "select 	gobj_cod,gobj_nom from objet_generique where gobj_tobj_cod IN (1,2) order by gobj_nom";

                                $stmt3 = $pdo->query($req_armes);
                                while ($result3 = $stmt3->fetch())
                                {
                                    $arme_cod = $result3['gobj_cod'];
                                    $sel      = "";
                                    if ($arme_cod == $result2['seequo_gobj_cod'])
                                    {
                                        $sel = "selected";
                                    }
                                    echo "<OPTION value=\"$arme_cod\" $sel>" . $result3['gobj_nom'] . "</OPTION>\n";
                                }
                                ?>
                            </SELECT></td>
                        <td><input type="text" name="seequo_proba" value="<?php echo $result2['seequo_proba'] ?>"></td>
                        <td><input type="text" name="seequo_etat_min" value="<?php echo $result2['seequo_etat_min'] ?>">
                        </td>
                        <td><input type="text" name="seequo_etat_max" value="<?php echo $result2['seequo_etat_max'] ?>">
                        </td>
                        <td><input type="submit" value="Modifier"></td>
                </form>
                <form action="admin_serie_equipements.php" method="post">
                    <input type="hidden" name="methode" value="supprimer_serie_element">
                    <input type="hidden" name="seequo_cod" value="<?php echo $result2['seequo_cod'] ?>">
                    <td><input type="submit" value="Supprimer"></td>
                </form>
                </tr>

                <?php
            } ?>
        </table>
        <?php
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
