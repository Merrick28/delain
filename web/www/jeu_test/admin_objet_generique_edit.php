<?php
include "blocks/_header_page_jeu.php";
ob_start();

?>

    <p class="titre">Édition d’un objet générique</p>
<?php
$erreur          = 0;
$droit_modif     = 'dcompt_objet';
include "blocks/_test_droit_modif_generique.php";
if ($erreur == 0)
{
    if (($methode == "mod3" && ISSET($_POST["cancel"])) || ($methode == "cre2" && ISSET($_POST["cancel"])))
    {
        $methode = "mod";
    }

    if (!isset($methode))
    {
        $methode = "mod";
    }
    switch ($methode)
    {
        case "debut":
            ?>
            <p>Choisissez votre méthode :</p>
            <a href="<?php echo $PHP_SELF; ?>?methode=cre">Création d’un nouvel objet ?</a><br>
            <a href="<?php echo $PHP_SELF; ?>?methode=mod">Modification d’un objet existant</a>
            <?php
            break;
        case "cre": // création d'un nouvel objet
            ?>
            <form name="cre" method="post" action="<?php echo $PHP_SELF; ?>">
                <input type="hidden" name="methode" value="cre2">
                <div class="centrer">
                    <table>
                        <tr>
                            <td class="soustitre2">Nom de l’objet (identifié)</td>
                            <td><input type="text" name="gobj_nom"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Nom de l’objet (non identifié)</td>
                            <td><input type="text" name="gobj_nom_generique"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Type d’objet</td>
                            <td><select name="gobj_tobj_cod">
                                    <?php
                                    $req = "select tobj_libelle,tobj_cod from type_objet where tobj_cod not in (3,5,9,10) order by tobj_cod ";
                                    $db->query($req);
                                    while ($db->next_record())
                                    {
                                        echo '<option value="' . $db->f("tobj_cod") . '">' . $db->f('tobj_libelle') . '</option>';
                                    }
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Valeur</td>
                            <td><input type="text" name="gobj_valeur"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Dégâts (armes uniquement)</td>
                            <td><input type="text" size="5" name="obcar_des_degats"> D <input type="text" size="5"
                                                                                              name="obcar_val_des_degats">
                                + <input type="text" size="5" name="obcar_bonus_degats"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Armure (armures uniquement)</td>
                            <td><input type="text" name="obcar_armure"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Arme à distance ? (armes uniquement)</td>
                            <td><select name="gobj_distance">
                                    <option value="O">Oui</option>
                                    <option value="N">Non</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Distance max (armes à distance uniquement)</td>
                            <td><input type="text" name="gobj_portee"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Chute (armes à distance uniquement)</td>
                            <td><input type="text" name="obcar_chute"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Compétence utilisée (armes uniquement)</td>
                            <td><select name="gobj_comp_cod">
                                    <option value="30">Mains nues</option>
                                    <?php
                                    $req = "select comp_libelle,comp_cod from competences where comp_typc_cod in (6,7,8) order by comp_cod ";
                                    $db->query($req);
                                    while ($db->next_record())
                                    {
                                        echo '<option value="' . $db->f("comp_cod") . '">' . $db->f('comp_libelle') . '</option>';
                                    }
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Poids</td>
                            <td><input type="text" name="gobj_poids"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Coût en PA pour une attaque normale (armes uniquement)</td>
                            <td><input type="text" name="gobj_pa_normal"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Coût en PA pour une attaque foudroyante (armes uniquement)</td>
                            <td><input type="text" name="gobj_pa_eclair"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Description</td>
                            <td><textarea name="gobj_description"></textarea></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Objet déposable ?</td>
                            <td><select name="gobj_deposable">
                                    <option value="O">Oui</option>
                                    <option value="N">Non</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Usure par utilisation</td>
                            <td><input type="text" name="gobj_usure"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Vendable dans les échoppes ?</td>
                            <td><select name="gobj_echoppe">
                                    <option value="O">Oui</option>
                                    <option value="N">Non</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Postable dans les relais poste?</td>
                            <td><select name="gobj_postable">
                                    <option value="O">Oui</option>
                                    <option selected value="N">Non</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Vampirisme (armes uniquement) en numérique (ex : 0.2 pour 20%)</td>
                            <td><input type="text" name="gobj_vampire"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Seuil d’utilisation en force</td>
                            <td><input type="text" name="gobj_seuil_force"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Seuil d’utilisation en dextérité</td>
                            <td><input type="text" name="gobj_seuil_dex"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Seuil d’utilisation en niveau</td>
                            <td><input type="text" name="gobj_niveau_min"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Nombre de mains (armes uniquement)</td>
                            <td><input type="text" name="gobj_nb_mains"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Bonus/malus à la régénération</td>
                            <td><input type="text" name="gobj_regen"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Aura de feu - en numérique (ex : 0.2 pour 20%)</td>
                            <td><input type="text" name="gobj_aura_feu"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Bonus/malus à la vue</td>
                            <td><input type="text" name="gobj_bonus_vue"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Protection contre les critiques (en %)</td>
                            <td><input type="text" name="gobj_critique"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Bonus à l’armure (artefacts et casques)</td>
                            <td><input type="text" name="gobj_bonus_armure"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Chance de drop à la mort du joueur (en %)</td>
                            <td><input type="text" name="gobj_chance_drop"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Chance de drop à la mort du monstre (en %)</td>
                            <td><input type="text" name="gobj_chance_drop_monstre">&nbsp; <em style="font-size: 9px;">à
                                    n'utiliser que si l'objet a 100% de chance d'être possèdé par le monstre</em></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Chance d’avoir un objet enchantable (en %)</td>
                            <td><input type="text" name="gobj_chance_enchant"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Objet déséquipable ? (O: oui ; N:non)</td>
                            <td><select name="gobj_desequipable">
                                    <option value="O">Oui</option>
                                    <option value="N">Non</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Stabilité (potion uniquement)</td>
                            <td><input type="text" name="gobj_stabilite"></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" class="test centrer" name="cancel" value="Annuler">&nbsp;&nbsp;<input
                                        type="submit" class="test centrer" value="Valider !"></td>
                        </tr>

                    </table>
                </div>
            </form>
            <?php
            break;
        case "mod": // modification d'un objet existant

            echo '<br><a href="' . $PHP_SELF . '?methode=cre">Création d’un nouvel objet ?</a><br><br><hr><strong>Modification d’un objet existant</strong> (<em>recherche par type<em>):<br><br>';

            // LISTE DES OBJETS POSSIBLES
            echo '<SCRIPT language="javascript"> var listeBase = new Array();';
            $nb_tobj  = 0;
            $req_tobj = "select gobj_cod, gobj_nom, tobj_libelle, gobj_valeur from objet_generique
                    inner join type_objet on tobj_cod = gobj_tobj_cod where gobj_tobj_cod not in (3,5,9,10) 
                    order by tobj_libelle, gobj_nom";
            $db->query($req_tobj);
            while ($db->next_record())
            {
                $gobj_nom     = $db->f("gobj_nom");
                $gobj_nom     = str_replace("\"", "", $gobj_nom);
                $tobj_libelle = str_replace("\"", "", $db->f("tobj_libelle"));
                $gobj_valeur  = $db->f("gobj_valeur");
                echo("listeBase[$nb_tobj] = new Array(0); \n");
                echo("listeBase[$nb_tobj][0] = \"" . $db->f("gobj_cod") . "\"; \n");
                echo("listeBase[$nb_tobj][1] = \"" . $gobj_nom . "\"; \n");
                echo("listeBase[$nb_tobj][2] = \"" . $tobj_libelle . "\"; \n");
                echo("listeBase[$nb_tobj][3] = \"" . $gobj_valeur . "\"; \n");
                $nb_tobj++;
            }
            ?>
            </SCRIPT>
            <form name="mod" action="<?php echo $PHP_SELF ?>" method="post">
            <select id="tobj" style="width: 280px;" name="selecttype"><option value="">Tous types d’objets</option>';
            <?php
            $req_tobj = "select distinct tobj_cod,tobj_libelle from type_objet order by tobj_libelle";
            $db->query($req_tobj);
            while ($db->next_record())
            {
                $tobj_libelle = str_replace("\"", "", $db->f("tobj_libelle"));
                echo "<option data-gobj=\"" . $db->f('tobj_cod') . "\" value=\"" . $db->f('tobj_cod') . "\">$tobj_libelle</option>";
            }

            echo '
            </select><br />
            <select style="width: 280px;" id="gobj_valeur" name="selectvaleur">
                <option value="">Valeur indéfinie</option>
                <option value="0;1000">Moins de 1 000 brouzoufs</option>
                <option value="1000;5000">Entre 1 000 et 5 000 brouzoufs</option>
                <option value="5000;10000">Entre 5 000 et 10 000 brouzoufs</option>
                <option value="10000;20000">Entre 10 000 et 20 000 brouzoufs</option>
                <option value="20000;50000">Entre 20 000 et 50 000 brouzoufs</option>
                <option value="50000;100000">Entre 50 000 et 100 000 brouzoufs</option>
                <option value="100000;100000000">Plus de 100 000 brouzoufs</option>
            </select><br /><br>Choisissez l’objet à modifier :<br>
            <input type="hidden" name="methode" value="mod2">
            <select name="gobj_cod" id="gobj" style="width:280px;">';

            $gobj = new objet_generique();
            $liste_obj = $gobj->getAll();
            foreach($liste_obj as $detail_obj)
            {
                echo '<option value="' . $detail_obj->gobj_cod . '">' . $detail_obj->gobj_nom . '</option>';
            }

            echo '</select>
            <input type="submit" value="Valider" class="test">
            </form>';


            // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
            $style_tr        = "display: block;";
            $param_id        = 0;
            $row             = 0;
            $row_id          = "row-$param_id-$row-";
            $aqelem_misc_nom = "";
            echo '<form name="mod" action="' . $PHP_SELF . '" method="post"><input type="hidden" name="methode" value="mod2">';
            echo '<br><hr><br><strong>Modification d’un objet existant</strong> (<em>recherche par nom<em>)<br>Code de l\'objet générique :
                    <input data-entry="val" id="' . $row_id . 'aqelem_cod" name="aqelem_cod[' . $param_id . '][]" type="hidden" value="">
                    <input name="aqelem_type[' . $param_id . '][]" type="hidden" value="">
                    <input data-entry="val" name="gobj_cod" id="' . $row_id . 'aqelem_misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\'' . $row_id . 'aqelem_misc_nom\', \'objet_generique\', $(\'#' . $row_id . 'aqelem_misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'aqelem_misc_nom">' . $aqelem_misc_nom . '</span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'aqelem_misc","objet_generique","Rechercher un objet générique");\'>
                    &nbsp;<br><input type="submit" value="Valider" class="test"></form><br><br>';


            break;
        case "mod2":
            $db2 = new base_delain;
            $db3 = new base_delain;
            $req = "select * from objet_generique
				where gobj_cod =  $gobj_cod ";
            $db->query($req);
            $db->next_record();
            if ($db->f("gobj_obcar_cod") != '')
            {
                $req = "select * from objets_caracs where obcar_cod = " . $db->f("gobj_obcar_cod");
                $db2->query($req);
                if ($db2->nf() != 0)
                {
                    $db2->next_record();
                    $obcar_cod = $db2->f("obcar_cod");
                }
                else
                {
                    $obcar_cod = 0;
                }

            }
            else
            {
                $obcar_cod = 0;
            }
            ?>
            <form name="cre" method="post" action="<?php echo $PHP_SELF; ?>">
                <input type="hidden" name="methode" value="mod3">
                <input type="hidden" name="objet" value="<?php echo $gobj_cod; ?>">
                <input type="hidden" name="objet_car" value="<?php echo $obcar_cod; ?>">
                <div class="centrer">
                    <table>
                        <tr>
                            <td class="soustitre2">Nom de l’objet (identifié)</td>
                            <td><input type="text" name="gobj_nom" value="<?php echo $db->f("gobj_nom"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Nom de l’objet (non identifié)</td>
                            <td><input type="text" name="gobj_nom_generique"
                                       value="<?php echo $db->f("gobj_nom_generique"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Type d’objet</td>
                            <td><select name="gobj_tobj_cod">
                                    <?php
                                    $req = "select tobj_libelle,tobj_cod from type_objet where tobj_cod not in (3,5,9,10) order by tobj_cod ";
                                    $db3->query($req);
                                    while ($db3->next_record())
                                    {
                                        echo '<option value="' . $db3->f("tobj_cod") . '" ';
                                        if ($db3->f('tobj_cod') == $db->f("gobj_tobj_cod"))
                                        {
                                            echo " selected ";
                                        }
                                        echo '>' . $db3->f('tobj_libelle') . '</option>';
                                    }
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Valeur</td>
                            <td><input type="text" name="gobj_valeur" value="<?php echo $db->f("gobj_valeur"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Dégâts (armes uniquement)</td>
                            <td><input type="text" size="5" name="obcar_des_degats"
                                       value="<?php echo $db2->f("obcar_des_degats"); ?>"> D <input type="text" size="5"
                                                                                                    name="obcar_val_des_degats"
                                                                                                    value="<?php echo $db2->f("obcar_val_des_degats"); ?>">
                                + <input type="text" size="5" name="obcar_bonus_degats"
                                         value="<?php echo $db2->f("obcar_bonus_degats"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Armure (armures uniquement)</td>
                            <td><input type="text" name="obcar_armure" value="<?php echo $db2->f("obcar_armure"); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Arme à distance ? (armes uniquement)</td>
                            <td><select name="gobj_distance">
                                    <option value="O"
                                        <?php
                                        if ($db->f("gobj_distance") == 'O')
                                        {
                                            echo " selected";
                                        }
                                        ?>
                                    >Oui
                                    </option>
                                    <option value="N"
                                        <?php
                                        if ($db->f("gobj_distance") == 'N')
                                        {
                                            echo " selected";
                                        }
                                        ?>
                                    >Non
                                    </option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Distance max (armes à distance uniquement)</td>
                            <td><input type="text" name="gobj_portee" value="<?php echo $db->f("gobj_portee"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Chute (armes à distance uniquement)</td>
                            <td><input type="text" name="obcar_chute" value="<?php echo $db2->f("obcar_chute"); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Compétence utilisée (armes uniquement)</td>
                            <td><select name="gobj_comp_cod">
                                    <option value="30"
                                        <?php
                                        if ($db->f("gobj_comp_cod") == 30)
                                        {
                                            echo " selected ";
                                        }
                                        ?>

                                    >Mains nues
                                    </option>
                                    <?php
                                    $req = "select comp_libelle,comp_cod from competences where comp_typc_cod in (6,7,8) order by comp_cod ";
                                    $db3->query($req);
                                    while ($db3->next_record())
                                    {
                                        echo '<option value="' . $db3->f("comp_cod") . '" ';
                                        if ($db3->f('comp_cod') == $db->f("gobj_comp_cod"))
                                        {
                                            echo " selected ";
                                        }
                                        echo '>' . $db3->f('comp_libelle') . '</option>';
                                    }
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Poids</td>
                            <td><input type="text" name="gobj_poids" value="<?php echo $db->f("gobj_poids"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Coût en PA pour une attaque normale (armes uniquement)</td>
                            <td><input type="text" name="gobj_pa_normal"
                                       value="<?php echo $db->f("gobj_pa_normal"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Coût en PA pour une attaque foudroyante (armes uniquement)</td>
                            <td><input type="text" name="gobj_pa_eclair"
                                       value="<?php echo $db->f("gobj_pa_eclair"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Description</td>
                            <td><textarea name="gobj_description"><?php echo $db->f("gobj_description"); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Objet déposable ?</td>
                            <td><select name="gobj_deposable">
                                    <option value="O"
                                        <?php
                                        if ($db->f("gobj_deposable") == 'O')
                                        {
                                            echo " selected";
                                        }
                                        ?>
                                    >Oui
                                    </option>
                                    <option value="N"
                                        <?php
                                        if ($db->f("gobj_deposable") == 'N')
                                        {
                                            echo " selected";
                                        }
                                        ?>
                                    >Non
                                    </option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Usure par utilisation</td>
                            <td><input type="text" name="gobj_usure" value="<?php echo $db->f("gobj_usure"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Vendable dans les échoppes ?</td>
                            <td><select name="gobj_echoppe">
                                    <option value="O"
                                        <?php
                                        if ($db->f("gobj_echoppe") == 'O')
                                        {
                                            echo " selected";
                                        }
                                        ?>
                                    >Oui
                                    </option>
                                    <option value="N"
                                        <?php
                                        if ($db->f("gobj_echoppe") == 'N')
                                        {
                                            echo " selected";
                                        }
                                        ?>
                                    >Non
                                    </option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Objet postable dans les relais poste ?</td>
                            <td><select name="gobj_postable">
                                    <option value="O"
                                        <?php
                                        if ($db->f("gobj_postable") == 'O')
                                        {
                                            echo " selected";
                                        }
                                        ?>
                                    >Oui
                                    </option>
                                    <option value="N"
                                        <?php
                                        if ($db->f("gobj_postable") == 'N')
                                        {
                                            echo " selected";
                                        }
                                        ?>
                                    >Non
                                    </option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Vampirisme (armes uniquement) en numérique (ex : 0.2 pour 20%)</td>
                            <td><input type="text" name="gobj_vampire" value="<?php echo $db->f("gobj_vampire"); ?>">
                            </td>
                        </tr>
                        </tr>
                        <tr>
                            <td class="soustitre2">Seuil d’utilisation en force</td>
                            <td><input type="text" name="gobj_seuil_force"
                                       value="<?php echo $db->f("gobj_seuil_force"); ?>"></td>
                        </tr>
                        </tr>
                        <tr>
                            <td class="soustitre2">Seuil d’utilisation en dextérité</td>
                            <td><input type="text" name="gobj_seuil_dex"
                                       value="<?php echo $db->f("gobj_seuil_dex"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Seuil d’utilisation en niveau</td>
                            <td><input type="text" name="gobj_niveau_min"
                                       value="<?php echo $db->f("gobj_niveau_min"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Nombre de mains (armes uniquement)</td>
                            <td><input type="text" name="gobj_nb_mains" value="<?php echo $db->f("gobj_nb_mains"); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Bonus/malus à la régénération</td>
                            <td><input type="text" name="gobj_regen" value="<?php echo $db->f("gobj_regen"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Aura de feu - en numérique (ex : 0.2 pour 20%)</td>
                            <td><input type="text" name="gobj_aura_feu" value="<?php echo $db->f("gobj_aura_feu"); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Bonus/malus à la vue</td>
                            <td><input type="text" name="gobj_bonus_vue"
                                       value="<?php echo $db->f("gobj_bonus_vue"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Protection contre les critiques (en %)</td>
                            <td><input type="text" name="gobj_critique" value="<?php echo $db->f("gobj_critique"); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Bonus à l’armure (artefacts et casques)</td>
                            <td><input type="text" name="gobj_bonus_armure"
                                       value="<?php echo $db->f("gobj_bonus_armure"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Chance de drop à la mort du joueur (en %)</td>
                            <td><input type="text" name="gobj_chance_drop"
                                       value="<?php echo $db->f("gobj_chance_drop"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Chance de drop à la mort du monstre (en %)</td>
                            <td><input type="text" name="gobj_chance_drop_monstre"
                                       value="<?php echo $db->f("gobj_chance_drop_monstre"); ?>">&nbsp; <em
                                        style="font-size: 9px;">à n'utiliser que si l'objet a 100% de chance d'être
                                    possèdé par le monstre</em></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Chance d’avoir un objet enchantable (en %)</td>
                            <td><input type="text" name="gobj_chance_enchant"
                                       value="<?php echo $db->f("gobj_chance_enchant"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Objet déséquipable (O: oui ; N: non)</td>
                            <td><input type="text" name="gobj_desequipable"
                                       value="<?php echo $db->f("gobj_desequipable"); ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Stabilité (potions uniquement)</td>
                            <td><input type="text" name="gobj_stabilite"
                                       value="<?php echo $db->f("gobj_stabilite"); ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" class="test centrer" name="cancel"
                                       value="Annuler">&nbsp;&nbsp;<input type="submit" class="test centrer"
                                                                          value="Valider !">
                            </td>
                        </tr>

                    </table>
                </div>
            </form>


            <?php
            break;
        case "cre2":
            // détermination du obcar_cod
            $req = 'select nextval(\'seq_obcar_cod\') as resultat ';
            $db->query($req);
            $db->next_record();
            $obcar_cod = $db->f('resultat');
            // mise à 0 des valeurs vides pour objets_caracs
            $fields = array(
                'obcar_des_degats',
                'obcar_val_des_degats',
                'obcar_bonus_degats',
                'obcar_chute',
                'obcar_armure'
            );
            foreach ($fields as $i => $value)
            {
                if ($_POST[$fields[$i]] == '')
                {
                    $_POST[$fields[$i]] = 0;
                }
            }
            // insertion dans objets_caracs
            $req = "insert into objets_caracs
				(obcar_cod,obcar_des_degats,obcar_val_des_degats,obcar_bonus_degats,obcar_chute,obcar_armure)
				values
				(" . $obcar_cod . "," . $_POST['obcar_des_degats'] . "," . $_POST['obcar_val_des_degats'] . "," . $_POST['obcar_bonus_degats'] . "," . $_POST['obcar_chute'] . "," . $_POST['obcar_armure'] . ")";
            $db->query($req);

            // mise à NULL des valeurs vides pour objets_generique
            $fields = array(
                'gobj_chance_drop_monstre',
            );
            foreach ($fields as $i => $value)
            {
                if ($_POST[$fields[$i]] == '')
                {
                    $_POST[$fields[$i]] = "NULL";
                }
            }

            // mise à 0 des valeurs vides pour objets_generique
            $fields = array(
                'gobj_valeur',
                'gobj_portee',
                'gobj_poids',
                'gobj_pa_normal',
                'gobj_pa_eclair',
                'gobj_usure',
                'gobj_vampire',
                'gobj_seuil_force',
                'gobj_seuil_dex',
                'gobj_nb_mains',
                'gobj_aura_feu',
                'gobj_bonus_vue',
                'gobj_critique',
                'gobj_bonus_armure',
                'gobj_regen',
                'gobj_chance_drop',
                'gobj_chance_enchant',
                'gobj_stabilite',
                'gobj_niveau_min'
            );
            foreach ($fields as $i => $value)
            {
                if ($_POST[$fields[$i]] == '')
                {
                    $_POST[$fields[$i]] = 0;
                }
            }
            // insertion dans objets_generique
            $req = "insert into objet_generique
				(gobj_obcar_cod,gobj_nom,gobj_nom_generique,gobj_tobj_cod,gobj_valeur,gobj_distance,gobj_portee,gobj_comp_cod,gobj_poids,
				gobj_pa_normal,gobj_pa_eclair,gobj_description,gobj_deposable,gobj_postable,gobj_usure,gobj_echoppe,gobj_vampire,
				gobj_seuil_force,gobj_seuil_dex,gobj_nb_mains,gobj_regen,gobj_aura_feu,gobj_bonus_vue,gobj_critique,gobj_bonus_armure,
				gobj_chance_drop,gobj_chance_drop_monstre,gobj_chance_enchant,gobj_desequipable,gobj_stabilite, gobj_niveau_min)
				values
				($obcar_cod,e'" . pg_escape_string($gobj_nom) . "',e'" . pg_escape_string($gobj_nom_generique) . "'," . $_POST['gobj_tobj_cod'] . "," . $_POST['gobj_valeur'] .
                   ",'$gobj_distance'," . $_POST['gobj_portee'] . "," . $_POST['gobj_comp_cod'] . "," . $_POST['gobj_poids'] . "," . $_POST['gobj_pa_normal'] . "," .
                   $_POST['gobj_pa_eclair'] . ",e'" . pg_escape_string($gobj_description) . "','$gobj_deposable','" . $_POST['gobj_postable'] . "'," . $_POST['gobj_usure'] . ",'$gobj_echoppe'," .
                   $_POST['gobj_vampire'] . ",	" . $_POST['gobj_seuil_force'] . "," . $_POST['gobj_seuil_dex'] . "," . $_POST['gobj_nb_mains'] . "," . $_POST['gobj_regen'] .
                   "," . $_POST['gobj_aura_feu'] . "," . $_POST['gobj_bonus_vue'] . "," . $_POST['gobj_critique'] . "," . $_POST['gobj_bonus_armure'] . "," . $_POST['gobj_chance_drop'] . "," . $_POST['gobj_chance_drop_monstre'] .
                   "," . $_POST['gobj_chance_enchant'] . ",'$gobj_desequipable'," . $_POST['gobj_stabilite'] . ", " . $_POST['gobj_niveau_min'] . ") ";
            $db->query($req);
            echo "<p>L'insertion s'est bien déroulée.<br><br><a href=\"" . $PHP_SELF . "?methode=mod\">Créer/Modifier d'autres objets</a>";
            break;
        case "mod3":
            // détermination du obcar_cod
            $obcar_cod = $_POST['objet_car'];
            // mise à 0 des valeurs vides pour objets_caracs
            $fields = array(
                'obcar_des_degats',
                'obcar_val_des_degats',
                'obcar_bonus_degats',
                'obcar_chute',
                'obcar_armure'
            );
            foreach ($fields as $i => $value)
            {
                if ($_POST[$fields[$i]] == '')
                {
                    $_POST[$fields[$i]] = 0;
                }
            }
            // update dans objets_caracs
            $req = "update objets_caracs
				set obcar_des_degats = " . $_POST['obcar_des_degats'] . ",obcar_val_des_degats = " . $_POST['obcar_val_des_degats'] . ",
				obcar_bonus_degats = " . $_POST['obcar_bonus_degats'] . ",obcar_chute = " . $_POST['obcar_chute'] . ",obcar_armure = " . $_POST['obcar_armure'] . "
				where obcar_cod = $obcar_cod";
            $db->query($req);

            // mise à NULL des valeurs vides pour objets_generique
            $fields = array(
                'gobj_chance_drop_monstre',
            );
            foreach ($fields as $i => $value)
            {
                if ($_POST[$fields[$i]] == '')
                {
                    $_POST[$fields[$i]] = "NULL";
                }
            }

            // mise à 0 des valeurs vides pour objets_generique
            $fields = array(
                'gobj_valeur',
                'gobj_portee',
                'gobj_poids',
                'gobj_pa_normal',
                'gobj_pa_eclair',
                'gobj_usure',
                'gobj_vampire',
                'gobj_seuil_force',
                'gobj_seuil_dex',
                'gobj_nb_mains',
                'gobj_aura_feu',
                'gobj_bonus_vue',
                'gobj_critique',
                'gobj_bonus_armure',
                'gobj_regen',
                'gobj_chance_drop',
                'gobj_chance_enchant',
                'gobj_desequipable',
                'gobj_stabilite',
                'gobj_niveau_min'
            );
            foreach ($fields as $i => $value)
            {
                if ($_POST[$fields[$i]] == '')
                {
                    $_POST[$fields[$i]] = 0;
                }
            }

            // insertion dans objets_generique
            $req = "update objet_generique
				set gobj_nom = e'" . pg_escape_string($gobj_nom) . "',gobj_nom_generique = e'" . pg_escape_string($gobj_nom_generique) . "',gobj_tobj_cod = " . $_POST['gobj_tobj_cod'] . ",
				gobj_obcar_cod = $obcar_cod, gobj_valeur = " . $_POST['gobj_valeur'] . ", gobj_distance='$gobj_distance',gobj_portee = " . $_POST['gobj_portee'] . ",
				gobj_comp_cod = " . $_POST['gobj_comp_cod'] . ", gobj_poids = " . $_POST['gobj_poids'] . ",
				gobj_pa_normal = " . $_POST['gobj_pa_normal'] . ",gobj_pa_eclair = " . $_POST['gobj_pa_eclair'] . ",gobj_description=e'" . pg_escape_string($gobj_description) . "',
				gobj_deposable = '$gobj_deposable', gobj_postable = '" . $_POST['gobj_postable'] . "',gobj_usure = " . $_POST['gobj_usure'] . ",gobj_echoppe = '$gobj_echoppe',
				gobj_vampire = " . $_POST['gobj_vampire'] . ",gobj_seuil_force = " . $_POST['gobj_seuil_force'] . ",gobj_seuil_dex = " . $_POST['gobj_seuil_dex'] . ",
				gobj_nb_mains = " . $_POST['gobj_nb_mains'] . ",gobj_regen = " . $_POST['gobj_regen'] . ",gobj_aura_feu = " . $_POST['gobj_aura_feu'] . ",
				gobj_bonus_vue = " . $_POST['gobj_bonus_vue'] . ",gobj_critique = " . $_POST['gobj_critique'] . ",gobj_bonus_armure = " . $_POST['gobj_bonus_armure'] . ",
				gobj_chance_drop = " . $_POST['gobj_chance_drop'] . ", gobj_chance_drop_monstre = " . $_POST['gobj_chance_drop_monstre'] . ", gobj_chance_enchant = " . $_POST['gobj_chance_enchant'] . ", gobj_desequipable = '$gobj_desequipable', gobj_stabilite = " . $_POST['gobj_stabilite'] . ", 
				gobj_niveau_min = " . $_POST['gobj_niveau_min'] . " where gobj_cod = $objet ";
            $db->query($req);
            echo "<p>L’insertion s’est bien déroulée.";
            //MAJ des objets individuels déjà existants. ATTENTION, certains champs ne sont bizarrement pas présents !
            $req = "update objets set obj_nom = e'" . pg_escape_string($gobj_nom) . "',obj_nom_generique = e'" . pg_escape_string($gobj_nom_generique) . "',
			obj_des_degats = " . $_POST['obcar_des_degats'] . ",obj_val_des_degats = " . $_POST['obcar_val_des_degats'] . ",obj_bonus_degats = " . $_POST['obcar_bonus_degats'] . ",
			obj_valeur = " . $_POST['gobj_valeur'] . ",obj_distance='$gobj_distance',obj_portee = " . $_POST['gobj_portee'] . ",
			obj_poids = " . $_POST['gobj_poids'] . ",obj_description=e'" . pg_escape_string($gobj_description) . "',obj_deposable = '$gobj_deposable',
			obj_usure = " . $_POST['gobj_usure'] . ",obj_vampire = " . $_POST['gobj_vampire'] . ",obj_seuil_force = " . $_POST['gobj_seuil_force'] . ",
			obj_seuil_dex = " . $_POST['gobj_seuil_dex'] . ",obj_regen = " . $_POST['gobj_regen'] . ",obj_aura_feu = " . $_POST['gobj_aura_feu'] . ",
			obj_bonus_vue = " . $_POST['gobj_bonus_vue'] . ",obj_critique = " . $_POST['gobj_critique'] . ",
			obj_chance_drop = " . $_POST['gobj_chance_drop'] . ",obj_stabilite = " . $_POST['gobj_stabilite'] . ",obj_niveau_min = " . $_POST['gobj_niveau_min'] . "
			where obj_gobj_cod = $objet and obj_modifie = 0";
            $db->query($req);
            echo "<p><br>La mise à jour des anciens objets aussi<br><br><a href=\"" . $PHP_SELF . "?methode=mod\">Créer/Modifier d'autres objets</a><br><br>";
            break;

    }
}
?>
<SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
