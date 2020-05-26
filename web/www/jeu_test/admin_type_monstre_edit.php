<?php

include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;


//	Préparer la liste des images d'avatar déjà présete sur le serveur.
$baseimage   = "../images/avatars";
$rep         = opendir($baseimage);
$images_list = "";
$img         = 0;
while (false !== ($filename = readdir($rep)))
{
    $avatar_perso_cod = 1 * (int)substr($filename, 0, -4);
    if ($avatar_perso_cod == 0)
    {   // si le pattern correspond, c'est une image de perso (pas de monstre) on ne l'affiche pas!
        $imagesize = @getimagesize($baseimage . '/' . $filename);
        if (($imagesize[0] > 28) && ($imagesize[1] > 28))
        {     // on ne prend que des images de taille raisonnable
            $images_list .= "<div style=\"margin - left:5px; display:inline-block;\"><img onclick=\"select_imglist({$img})\" height=\"60px\" id=\"img-serveur-{$img}\" src=\"{$baseimage}/{$filename}\"></div>";
            $img++;
        }
    }
}

function bm_progressivite($fonc_effet, $fonc_force)
{
    $pdo = new bddpdo;

    $req = "select bonus_progressivite(:bm, :force) as progressivite";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array( ":bm" => $fonc_effet, ":force" => $fonc_force ), $stmt);
    if ($progres = $stmt->fetch())
    {
        return $progres["progressivite"];
    }
    else
    {
        return 'O' ;
    }
}

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
    <link href="../css/multiple-select.min.css?v<?php echo $__VERSION; ?>" rel="stylesheet">
    <SCRIPT language="javascript" src="../scripts/controlUtils.js"></script>
    <script language="javascript" src="../scripts/validation.js?<?php echo $__VERSION; ?>"></script>
    <script language="javascript" src="../scripts/manip_css.js"></script>
    <script language="javascript" src="../scripts/admin_effets_auto.js?v<?php echo $__VERSION; ?>"></script>
    <script language="javascript" src="../js/multiple-select.min.js?v<?php echo $__VERSION; ?>"></script>
    <script language="javascript">//# sourceURL=admin_type_monstre_edit.js
        function updatePv() {
            objet = document.getElementById("ChampPvCalcul");
            constit = parseInt(document.getElementById("constit").value);
            niveau = parseInt(document.getElementById("niveau").value);
            objet.innerHTML = parseInt(2 * constit + (niveau - 1) * (constit + 12) / 8);
        }

        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('output_image');
                output.src = reader.result;
                $("#type-img-avatar").val("upload");
                $("#id-gmon_avatar").val("defaut.png");     // en cas de mauvais upload de l'image
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function open_imglist() {
            if ($("#images-container").css("display") == "none") {
                $("#images-container").css("width", $("#images-container").parent().width());
                $("#images-container").css("white-space", "nowrap");
                $("#images-container").css("display", "");
            } else {
                $("#images-container").css("display", "none");
            }
        }

        function select_imglist(img) {
            $("#output_image")[0].src = $("#img-serveur-" + img)[0].src;
            $("#images-container").css("display", "none");
            $("#type-img-avatar").val("server");
            var path_image = $("#img-serveur-" + img)[0].src.split("/");
            var file_image = path_image[path_image.length - 1];
            $("#id-gmon_avatar").val(file_image);
        }
    </script>
<?php
$droit_modif = 'dcompt_modif_gmon';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    if (isset($_POST['methode']))
    {
        //echo"<pre>"; print_r($_POST); echo"</pre>";  die();
        include "admin_traitement_type_monstre_edit.php";
    }
    include "admin_edition_header.php";


    $methode2 = get_request_var('methode2', 'debut');
    switch ($methode2)
    {
        case "debut"://ECRAN PRESENTATION
            ?>
            RECHERCHE
            <TABLE width="80%" align="center">
                <TR>
                    <TD>
                        <form name="edit" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="methode2" value="edit">
                            <input type="hidden" name="sel_method" value="edit">
                            Selectionner un monstre générique
                            <select name="gmon_cod">
                                <?php
                                // LISTE DES MONSTRES GENERIQUES
                                $req_gmon = "select gmon_cod,gmon_nom from monstre_generique order by gmon_nom";

                                $stmt = $pdo->query($req_gmon);
                                while ($result = $stmt->fetch())
                                {
                                    $gen_mon_cod = $result['gmon_cod'];
                                    echo "<OPTION value=\"$gen_mon_cod\">" . $result['gmon_nom'] . "</OPTION>\n";
                                }
                                ?>
                            </select>
                            <input type="submit" value="Modifier">
                        </form>
                    </TD>
                </TR>
                <TR>
                    <TD>
                        <form name="new" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="methode2" value="edit">
                            <input type="hidden" name="sel_method" value="new">
                            <input type="submit" value="Créer un nouveau monstre générique">
                        </form>
                    </TD>
                </TR>
                <TR>
                    <TD>
                        <form name="new_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="methode2" value="edit">
                            <input type="hidden" name="sel_method" value="new_from">
                            <input type="submit" value="Créer un nouveau monstre générique a partir du type : ">
                            <select name="gmon_cod">
                                <?php
                                $stmt = $pdo->query($req_gmon);
                                while ($result = $stmt->fetch())
                                {
                                    $gen_mon_cod = $result['gmon_cod'];
                                    echo "<OPTION value=\"$gen_mon_cod\">" . $result['gmon_nom'] . "</OPTION>\n";
                                }
                                ?>
                            </select>

                        </form>
                    </TD>
                </TR>
                <TR>
                    <TD>
                        <form name="liste" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="methode2" value="liste_monstre">
                            <input type="submit" value="Voir les monstres existants">
                        </form>
                    </TD>
                </TR>
            </TABLE>
            <br>
            <HR>
            <br>
            <?php break;

        case "liste_monstre":    // LISTE DES MONSTRES GENERIQUES
            $req_gmon = "select * 
				from monstre_generique as monstre

				left outer join (select seequ_nom as armure_nom,seequ_cod as armure_cod from serie_equipement) as armure_equipement
				on monstre.gmon_serie_armure_cod = armure_equipement.armure_cod

				left outer join (select seequ_nom as arme_nom,seequ_cod as arme_cod from serie_equipement) as arme_equipement
				on monstre.gmon_serie_arme_cod = arme_equipement.arme_cod

				left outer join (select gobj_cod,gobj_nom as arme,obcar_des_degats,obcar_val_des_degats,obcar_bonus_degats
				from objet_generique,objets_caracs where gobj_cod = obcar_cod) as objets
				on monstre.gmon_arme = objets.gobj_cod

				left outer join (select gobj_cod,gobj_nom as armure,obcar_armure
				from objet_generique,objets_caracs where gobj_cod = obcar_cod) as objets2
				on monstre.gmon_armure = objets2.gobj_cod

				left outer join (select ia_nom,ia_type from type_ia) as ia on ia.ia_type = monstre.gmon_type_ia
				";
            if (!isset($sort))
            {
                $sort    = 'nom';
                $sens    = 'asc';
                $nv_sens = 'desc';
            }
            if (!isset($sens))
            {
                $sens = 'asc';
            }
            if (!isset($_POST['autresens']))
            {
                $autresens = 'desc';
            } else
            {
                $autresens = $_POST['autresens'];
            }
            if (($sens != 'desc') && ($sens != 'asc'))
            {
                echo "<p>Anomalie sur sens !";
                exit();
            }
            $tableau_sort = array(
                'DLT'          => 'gmon_temps_tour',
                'nom'          => 'gmon_nom',
                'for'          => 'gmon_for',
                'dex'          => 'gmon_dex',
                'niveau'       => 'gmon_niveau',
                'cons'         => 'gmon_con',
                'int'          => 'gmon_int',
                'serie_arme'   => 'gmon_serie_arme_cod',
                'serie_armure' => 'gmon_serie_armure_cod',
                'type_IA'      => 'ia_nom'
            );
            foreach ($tableau_sort as $key => $val)
            {
                $gras[$key]    = '';
                $fingras[$key] = '';
            }
            if (!isset($tableau_sort[$sort]))
            {
                echo "<p>Anomalie sur tri !";
                exit();
            }
            $req_gmon  = $req_gmon . " order by " . $tableau_sort[$sort] . " $sens";
            $autresens = $sens;
            $sens      = ($sens == 'desc') ? 'asc' : 'desc';
            $stmt      = $pdo->query($req_gmon);

            $gras[$sort]    = '<strong>';
            $fingras[$sort] = '</strong>';

            echo '<form name="fsort" method="post" action="admin_type_monstre_edit.php">
				<input type="hidden" name="sort">
				<input type="hidden" name="sens" value="$sens">
				<input type="hidden" name="autresens">
				<input type="hidden" name="visu">
				<input type="hidden"  name="methode2" value="liste_monstre">
				<table>';
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"nom\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['nom']}Nom{$fingras['nom']}</a></p></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"niveau\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['niveau']}Niveau{$fingras['niveau']}</a></p></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"type_IA\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['type_IA']}Type d’IA{$fingras['type_IA']}</a></p></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"for\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['for']}For{$fingras['for']}</a></p></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"dex\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['dex']}Dex{$fingras['dex']}</a></p></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"int\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['int']}Int{$fingras['int']}</a></p></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"cons\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['cons']}Const{$fingras['cons']}</a></p></td>";
            echo "<td class='soustitre2'><p>Dég</p></a></td>";
            echo "<td class='soustitre2'><p>Rég</p></a></td>";
            echo "<td class='soustitre2'><p>Vue</p></a></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"DLT\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['DLT']}DLT{$fingras['DLT']}</a></p></td>";
            echo "<td class='soustitre2'><p>Armure</p></a></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"serie_arme\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['serie_arme']}Eqt armes{$fingras['serie_arme']}</a></p></td>";
            echo "<td class='soustitre2'><p><a href='javascript:document.fsort.sort.value=\"serie_armure\";document.fsort.sens.value=\"$sens\";document.fsort.submit();'>{$gras['serie_armure']}Eqt armures{$fingras['serie_armure']}</a></p></td>";
            echo "<td class='soustitre2'><p>Nb récept.</p></a></td>";
            echo "<td class='soustitre2'><p>Sorts</p></a></td>";
            echo "<td class='soustitre2'><p>Br.</p></a></td>";
            echo "<td class='soustitre2'><p>Contrat chasse</p></a></td>";

            while ($result = $stmt->fetch())
            {
                $gen_mon_cod      = $result['gmon_cod'];
                $equip_cod_arme   = $result['gmon_serie_arme_cod'];
                $equip_cod_armure = $result['gmon_serie_armure_cod'];
                if ($equip_cod_armure == NULL)
                {
                    $armure = "" . $result['armure'];
                } else
                {
                    $armure =
                        "<a href=\"admin_serie_equipements.php?methode=modif_serie_unitaire&serie_monstre=$equip_cod_armure\">Série : " . $result['armure_nom'] . "</a>";
                }
                if ($equip_cod_arme == NULL)
                {
                    $arme = "" . $result['arme'];
                } else
                {
                    $arme =
                        "<a href=\"admin_serie_equipements.php?methode=modif_serie_unitaire&serie_monstre=$equip_cod_arme\">Série : " . $result['arme_nom'] . "</a>";
                }
                $req_m_sorts =
                    "select sort_nom from sorts_monstre_generique,sorts where sgmon_gmon_cod  = $gen_mon_cod and sgmon_sort_cod = sort_cod";

                $stmt2    = $pdo->query($req_m_sorts);
                $comments = "";
                while ($result2 = $stmt2->fetch())
                {
                    $comments .= $result2['sort_nom'] . ",";
                }
                echo "<TR><TD><a href=\"admin_type_monstre_edit.php?methode2=edit&sel_method=edit&gmon_cod=$gen_mon_cod\">", $result['gmon_nom'], "</a>
					</TD><TD>", $result['gmon_niveau'],
                "</TD><TD>", $result['ia_nom'],
                "</TD><TD>", $result['gmon_for'],
                "</TD><TD>", $result['gmon_dex'],
                "</TD><TD>", $result['gmon_int'],
                "</TD><TD>", $result['gmon_con'],
                "</TD><TD>", $result['gmon_nb_des_degats'], "D", $result['gmon_val_des_degats'], "(+", $result²['gmon_amelioration_degats'], ")",
                "</TD><TD>", $result['gmon_des_regen'], "D", $result['gmon_valeur_regen'], "(+", $result['gmon_amelioration_regen'], ")",
                "</TD><TD>", $result['gmon_vue'],
                "</TD><TD>", $result['gmon_temps_tour'],
                "</TD><TD>", $result['obcar_armure'] * 1, "(+", $result['gmon_amelioration_armure'], ")",
                "</TD><TD>", $arme,
                "</TD><TD>", $armure, "
					</TD><TD>", $result['gmon_nb_receptacle'],
                "</TD><TD> ($comments)",
                "</TD><TD>", $result['gmon_or'], " br",
                "</TD><TD>", $result['gmon_quete'],
                "</TD></TR>";
            }
            echo "</table>";
            break;

        case "edit":// MODIFICATION DE MONSTRE GENERIQUE EXISTANT
            if ($sel_method == "edit" or $sel_method == "new_from")
            {
                // Calcul du nombre de sorts de soutien, pour informer l'admin s'il doit en ajouter

                $req_gmon        =
                    "SELECT count(*) nb_sort_soutien FROM sorts_monstre_generique JOIN sorts ON sort_cod=sgmon_sort_cod and sort_soutien = 'O' WHERE sgmon_gmon_cod = $gmon_cod";
                $stmt            = $pdo->query($req_gmon);
                $result          = $stmt->fetch();
                $nb_sort_soutien = $result['nb_sort_soutien'];

                $req_gmon    = "select gmon_cod,gmon_nom"
                               . ",gmon_for,gmon_dex,gmon_int,gmon_con,gmon_avatar"
                               . ",gmon_race_cod,gmon_temps_tour,gmon_des_regen,gmon_valeur_regen,gmon_vue"
                               . ",gmon_amelioration_vue,gmon_amelioration_regen,gmon_amelioration_degats,gmon_amelioration_armure"
                               . ",gmon_niveau,gmon_nb_des_degats,gmon_val_des_degats,gmon_or,gmon_arme,gmon_armure"
                               . ",gmon_soutien,gmon_amel_deg_dist,gmon_vampirisme,gmon_taille,gmon_description"
                               . ",gmon_pv,gmon_pourcentage_aleatoire,gmon_serie_arme_cod,gmon_serie_armure_cod"
                               . ",gmon_nb_receptacle,gmon_type_ia,gmon_quete,gmon_duree_vie,gmon_voie_magique,gmon_sex"
                               . " from monstre_generique where gmon_cod = $gmon_cod";
                $stmt        = $pdo->query($req_gmon);
                $result      = $stmt->fetch();
                $gmon_nom    = $result['gmon_nom'];
                $gmon_avatar = $result['gmon_avatar'];
                $race        = $result['gmon_race_cod'];

                ?>
                <br>
                <form name="modif_monstre" method="post" enctype="multipart/form-data">

                    <?php if ($sel_method == "edit")
                    {
                        ?>
                        <input type="hidden" name="methode2" value="edit">
                        <input type="hidden" name="sel_method" value="edit">
                        <input type="hidden" name="methode" value="update_mon">
                        <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                    <?php } else
                    {
                        ?>
                        <input type="hidden" name="methode" value="create_mon">
                    <?php }
                    ?>
                    <TABLE width="80%" align="center">
                        <TR>
                            <?php if ($sel_method == "edit")
                            {
                            ?>
                            <TD colspan="2">Type de monstre n°<?php echo $gmon_cod ?>. Nom : <input type="text"
                                                                                                    name="gmon_nom"
                                                                                                    value="<?php echo $gmon_nom ?>">
                            </TD>
                            <TD colspan="2"><img alt="avatar" onclick="open_imglist();" style="vertical-align:top;"
                                                 id="output_image" height="60px"
                                                 src="/images/avatars/<?php echo $gmon_avatar ?>">

                                <div style="display:inline-block"><input type="file" name="avatar_file" accept="image/*"
                                                                         onchange="preview_image(event);"><br>
                                    <strong>ou</strong><br><input type="button" style="margin-top: 5px;" class="test"
                                                                  name="nouvel_avatar"
                                                                  value="Sélectionner une image existante sur le serveur"
                                                                  onclick="open_imglist();"></div>
                                <?php }
                                else
                                {
                                ?>
                            <TD colspan="2">Original : <?php echo $gmon_nom ?>. Nom de la copie : <input type="text"
                                                                                                         name="gmon_nom"
                                                                                                         value="<?php echo $gmon_nom ?> Bis"><BR>
                            <TD colspan="2"><img alt="avatar" onclick="open_imglist();" style="vertical-align:top;"
                                                 id="output_image" height="60px"
                                                 src="/images/avatars/<?php echo $gmon_avatar ?>">

                                <div style="display:inline-block"><input type="file" name="avatar_file" accept="image/*"
                                                                         onchange="preview_image(event);"><br>
                                    <strong>ou</strong><br><input type="button" style="margin-top: 5px;" class="test"
                                                                  name="nouvel_avatar"
                                                                  value="Sélectionner une image existante sur le serveur"
                                                                  onclick="open_imglist();"></div>
                                <?php }
                                ?>
                            </TD>

                        </TR>
                        <TR>
                            <TD colspan="4">
                                <input id="type-img-avatar" type="hidden" name="type-img-avatar" value="">
                                <input id="id-gmon_avatar" type="hidden" name="gmon_avatar"
                                       value="<?php echo $gmon_avatar ?>">
                                <div id="images-container"
                                     style="display:none; height:80px; width: 100%; overflow-x:scroll;"><?php echo $images_list; ?></div>
                            </TD>
                        <TR>
                        <TR>
                            <TH width="25%">CHAMP</TH>
                            <TH width="25%">VALEUR</TH>
                            <TH width="25%">CHAMP</TH>
                            <TH width="25%">VALEUR</TH>
                        </TR>
                        <TR>
                            <TD>Force</TD>
                            <TD><INPUT type="text" name="gmon_for" value="<?php echo $result['gmon_for']; ?>"></TD>
                            <TD>Amélioration dégâts CàC</TD>
                            <TD><INPUT type="text" name="gmon_amelioration_degats"
                                       value="<?php echo $result['gmon_amelioration_degats']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Dextérité</TD>
                            <TD><INPUT type="text" name="gmon_dex" value="<?php echo $result['gmon_dex']; ?>"></TD>
                            <TD>Amélioration dégâts Dist</TD>
                            <TD><INPUT type="text" name="gmon_amel_deg_dist"
                                       value="<?php echo $result['gmon_amel_deg_dist']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Intelligence</TD>
                            <TD><INPUT type="text" name="gmon_int" value="<?php echo $result['gmon_int']; ?>"></TD>
                            <TD>Amélioration Vue</TD>
                            <TD><INPUT type="text" name="gmon_amelioration_vue"
                                       value="<?php echo $result['gmon_amelioration_vue']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Constitution</TD>
                            <TD><INPUT id="constit" type="text" name="gmon_con"
                                       value="<?php echo $result['gmon_con']; ?>" onChange="updatePv()"></TD>
                            <TD>Amélioration armure</TD>
                            <TD><INPUT type="text" name="gmon_amelioration_armure"
                                       value="<?php echo $result['gmon_amelioration_armure']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Temps de tour</TD>
                            <TD><INPUT type="text" name="gmon_temps_tour"
                                       value="<?php echo $result['gmon_temps_tour']; ?>"></TD>
                            <TD>Amélioration Régénération</TD>
                            <TD><INPUT type="text" name="gmon_amelioration_regen"
                                       value="<?php echo $result['gmon_amelioration_regen']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Dés de régen</TD>
                            <TD><INPUT type="text" name="gmon_des_regen"
                                       value="<?php echo $result['gmon_des_regen']; ?>"></TD>
                            <TD>Valeur dés régen</TD>
                            <TD><INPUT type="text" name="gmon_valeur_regen"
                                       value="<?php echo $result['gmon_valeur_regen']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Vue</TD>
                            <TD><INPUT type="text" name="gmon_vue" value="<?php echo $result['gmon_vue']; ?>"></TD>
                            <TD>Race</TD>
                            <TD>
                                <SELECT name="gmon_race_cod">
                                    <?php // LISTE DES RACES
                                    $req_races = "select race_cod,race_nom from race order by race_nom";
                                    $stmt_race = $pdo->query($req_races);
                                    while ($result_race = $stmt_race->fetch())
                                    {
                                        $race_cod = $result_race['race_cod'];
                                        $sel      = ($race_cod == $race) ? "selected" : "";
                                        echo "<OPTION value=\"$race_cod\" $sel>" . $result_race['race_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                        </TR>

                        <TR>
                            <TD>Dés de dégâts</TD>
                            <TD><INPUT type="text" name="gmon_nb_des_degats"
                                       value="<?php echo $result['gmon_nb_des_degats']; ?>"></TD>
                            <TD>Valeur dés dégâts</TD>
                            <TD><INPUT type="text" name="gmon_val_des_degats"
                                       value="<?php echo $result['gmon_val_des_degats']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Niveau</TD>
                            <TD><INPUT id="niveau" type="text" name="gmon_niveau"
                                       value="<?php echo $result['gmon_niveau']; ?>" onChange="updatePv()"></TD>
                            <TD>Brouzoufs</TD>
                            <TD><INPUT type="text" name="gmon_or" value="<?php echo $result['gmon_or']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Arme</TD>
                            <TD>
                                <SELECT name="gmon_arme">
                                    <option value="null">aucune</option>
                                    <?php // LISTE DES ARMES
                                    $arme      = $result['gmon_arme'];
                                    $req_armes =
                                        "select 	gobj_cod,gobj_nom from objet_generique where gobj_tobj_cod = 1 order by gobj_nom";

                                    $stmt_armes = $pdo->query($req_armes);
                                    while ($result_armes = $stmt_armes->fetch())
                                    {
                                        $arme_cod = $result_armes['gobj_cod'];
                                        $sel      = ($arme_cod == $arme) ? "selected" : "";
                                        echo "<OPTION value=\"$arme_cod\" $sel>" . $result_armes['gobj_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                            <TD>Armure</TD>
                            <TD>
                                <SELECT name="gmon_armure">
                                    <option value="null">aucune</option>
                                    <?php // LISTE DES ARMURES
                                    $armure      = $result['gmon_armure'];
                                    $req_armures =
                                        "select 	gobj_cod,gobj_nom from objet_generique where gobj_tobj_cod = 2 order by gobj_nom";

                                    $stmt_armures = $pdo->query($req_armures);
                                    while ($result_armures = $stmt_armures->fetch())
                                    {
                                        $armure_cod = $result_armures['gobj_cod'];
                                        $sel        = ($armure_cod == $armure) ? "selected" : "";
                                        echo "<OPTION value=\"$armure_cod\" $sel>" . $result_armures['gobj_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                        </TR>

                        <TR>
                            <TD>Soutien (<?php echo $nb_sort_soutien == 0 ? "pas de " : $nb_sort_soutien; ?> sorts)</TD>
                            <TD><INPUT type="text" name="gmon_soutien" value="<?php echo $result['gmon_soutien']; ?>">
                            </TD>
                            <TD>Vampirisme</TD>
                            <TD><INPUT type="text" name="gmon_vampirisme"
                                       value="<?php echo $result['gmon_vampirisme']; ?>"></TD>
                        </TR>

                        <TR>
                            <TD>Taille</TD>
                            <TD><INPUT type="text" name="gmon_taille" value="<?php echo $result['gmon_taille']; ?>">
                            </TD>
                            <TD>Nombre de réceptacles</TD>
                            <TD><INPUT type="text" name="gmon_nb_receptacle"
                                       value="<?php echo $result['gmon_nb_receptacle']; ?>"></TD>
                        </TR>

                        <?php $gmon_sex = $result['gmon_sex']; ?>
                        <TR>
                            <TD>PV (calculés)</TD>
                            <TD><SPAN id="ChampPvCalcul"/></TD>
                            <TD>Sexe</TD>
                            <TD>
                                <SELECT name="gmon_sex">
                                    <OPTION value="" <?php if ($gmon_sex == "")
                                    {
                                        echo "selected";
                                    } ?>>Aléatoire (Mâle ou Femelle)
                                    </OPTION>
                                    <OPTION value="M" <?php if ($gmon_sex == "M")
                                    {
                                        echo "selected";
                                    } ?>>Mâle
                                    </OPTION>
                                    <OPTION value="F" <?php if ($gmon_sex == "F")
                                    {
                                        echo "selected";
                                    } ?>>Femelle
                                    </OPTION>
                                    <OPTION value="A" <?php if ($gmon_sex == "A")
                                    {
                                        echo "selected";
                                    } ?>>Androgyne
                                    </OPTION>
                                    <OPTION value="H" <?php if ($gmon_sex == "H")
                                    {
                                        echo "selected";
                                    } ?>>Hermaphrodite
                                    </OPTION>
                                    <OPTION value="I" <?php if ($gmon_sex == "I")
                                    {
                                        echo "selected";
                                    } ?>>Inconnu
                                    </OPTION>

                                </SELECT></TD>
                            </TD>
                        </TR>

                        <TR>
                            <TD>Description</TD>
                            <TD colspan="3">
                                <textarea name="gmon_description"
                                          cols="80"><?php echo $result['gmon_description']; ?></textarea>
                            </TD>
                        </TR>
                        <TR>
                            <TD>Monstre utilisé ou non pour les contrats de chasse (O ou N)</TD>
                            <TD>
                                <INPUT type="text" name="gmon_quete" value="<?php echo $result['gmon_quete']; ?>">
                            </TD>
                            <TD>Durée de vie du monstre (en jours ; 0 ou vide pour un monstre classique)</TD>
                            <TD>
                                <INPUT type="text" name="gmon_duree_vie"
                                       value="<?php echo $result['gmon_duree_vie']; ?>">
                            </TD>
                        </TR>
                        <TR>
                            <TD colspan="4">
                                <span class="rouge">UNIQUEMENT POUR LE TEST POUR LE MOMENT !</span>
                            </TD>
                        </TR>
                        <TR>
                            <TD>PVs</TD>
                            <TD><INPUT type="text" name="gmon_pv" value="<?php echo $result['gmon_pv']; ?>"></TD>
                            <TD>Pourcentage aléatoire</TD>
                            <TD><INPUT type="text" name="gmon_pourcentage_aleatoire"
                                       value="<?php echo $result['gmon_pourcentage_aleatoire']; ?>"></TD>
                        </TR>
                        <TR>
                            <TD>Série d’Armes</TD>
                            <TD>
                                <SELECT name="gmon_serie_arme_cod">
                                    <option value="null">aucune</option>
                                    <?php // LISTE DES ARMES
                                    $arme       = $result['gmon_serie_arme_cod'];
                                    $req_armes  =
                                        "select 	seequ_cod,seequ_nom from  serie_equipement  order by seequ_nom";
                                    $stmt_armes = $pdo->query($req_armes);
                                    while ($result_armes = $stmt_armes->fetch())
                                    {
                                        $arme_cod = $result_armes['seequ_cod'];
                                        $sel      = ($arme_cod == $arme) ? "selected" : "";
                                        echo "<OPTION value=\"$arme_cod\" $sel>" . $result_armes['seequ_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                            <TD>Séries d’Armures</TD>
                            <TD>
                                <SELECT name="gmon_serie_armure_cod">
                                    <option value="null">aucune</option>
                                    <?php // LISTE DES ARMES
                                    $armure       = $result['gmon_serie_armure_cod'];
                                    $req_armures  =
                                        "select 	seequ_cod,seequ_nom from  serie_equipement  order by seequ_nom";
                                    $stmt_armures = $pdo->query($req_armures);
                                    while ($result_armures = $stmt_armures->fetch())
                                    {
                                        $armure_cod = $result_armures['seequ_cod'];
                                        $sel        = ($armure_cod == $armure) ? "selected" : "";
                                        echo "<OPTION value=\"$armure_cod\" $sel>" . $result_armures['seequ_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                        </TR>
                        <TR>
                            <TD>Choix du type d’IA</TD>
                            <TD>
                                <SELECT name="gmon_ia">
                                    <option value="null">aucune</option>
                                    <?php // LISTE DES IA possibles
                                    $ia     = $result['gmon_type_ia'];
                                    $req_ia = "select ia_type,ia_nom from type_ia order by ia_type";

                                    $stmt_ia = $pdo->query($req_ia);
                                    while ($result_ia = $stmt_ia->fetch())
                                    {
                                        $ia_cod = $result_ia['ia_type'];
                                        $sel    = ($ia_cod == $ia) ? "selected" : "";
                                        echo "<OPTION value=\"$ia_cod\" " . $sel . ">" . $result_ia['ia_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                            <TD>Voie magique</TD>
                            <TD>
                                <SELECT name="gmon_voie_magique">
                                    <option value="null">aucune</option>
                                    <?php // LISTE DES IA possibles
                                    $voie     = $result['gmon_voie_magique'];
                                    $req_voie =
                                        "select mvoie_cod,mvoie_libelle from voie_magique order by mvoie_libelle";

                                    $stmt_voie = $pdo->query($req_voie);
                                    while ($result_voie = $stmt_voie->fetch())
                                    {
                                        $mvoie_cod = $result_voie['mvoie_cod'];
                                        $sel       = ($mvoie_cod == $voie) ? "selected" : "";
                                        echo "<OPTION value=\"$mvoie_cod\" " . $sel . ">" . $result_voie['mvoie_libelle'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                        </TR>
                        <TR>
                            <TD colspan="4"><input type="submit" value="Enregistrer le modèle"></TD>
                        </TR>
                    </TABLE>
                </form>

                <?php if ($sel_method == "edit")
            {
                ?>
                <hr>
                SORTS
                <TABLE width="80%" align="center">
                    <tr>
                        <th>Sort</th>
                        <th>--</th>
                    </tr>
                    <?php $req_m_sorts =
                        "select sgmon_sort_cod,sort_nom,sgmon_gmon_cod,sgmon_chance,sort_aggressif, sort_soutien from sorts_monstre_generique,sorts where sgmon_gmon_cod  = $gmon_cod and sgmon_sort_cod = sort_cod";

                    $stmt_m_sorts = $pdo->query($req_m_sorts);
                    while ($result_m_sorts = $stmt_m_sorts->fetch())
                    {
                        $sort_nom         = $result_m_sorts['sort_nom'];
                        $sgmon_chance     = $result_m_sorts['sgmon_chance'];
                        $sort_nom_advance =
                            $result_m_sorts['sort_aggressif'] == 'O' ? ' <em>(agressif)</em>' : ($result_m_sorts['sort_soutien'] == 'O' ? ' <em>(soutien)</em>' : '');
                        ?>
                        <TR>
                            <TD><?php echo $sort_nom . $sort_nom_advance; ?></TD>
                            <TD>
                                <form method="post">
                                    <input type="hidden" name="methode2" value="edit">
                                    <input type="hidden" name="sel_method" value="edit">
                                    <input type="hidden" name="methode" value="delete_mon_sort">
                                    <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                                    <input type="hidden" name="sort_cod"
                                           value="<?php echo $result_m_sorts['sgmon_sort_cod'] ?>">
                                    <input type="submit" value="Supprimer">
                                </form>
                            </TD>
                        </TR>
                    <?php }
                    ?>
                    <TR>
                        <form method="post">
                            <input type="hidden" name="methode2" value="edit">
                            <input type="hidden" name="sel_method" value="edit">
                            <input type="hidden" name="methode" value="add_mon_sort">
                            <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                            <TD>Ajouter le sort:
                                <select name="sort_cod">
                                    <?php $req_m_sorts = "select sort_cod,
                                                              CASE WHEN sort_aggressif='O' THEN  sort_nom || ' (agressif)'
                                                                   WHEN sort_soutien='O' THEN  sort_nom || ' (soutien)'
                                                                   ELSE sort_nom END AS sort_nom
                                                    from sorts where not exists(select 1 from sorts_monstre_generique where sgmon_gmon_cod  = $gmon_cod and sgmon_sort_cod = sort_cod) order by sort_nom";
                                    $stmt_m_sorts      = $pdo->query($req_m_sorts);
                                    while ($result_m_sorts = $stmt_m_sorts->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $result_m_sorts['sort_cod'] ?>"><?php echo $result_m_sorts['sort_nom'] ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </TD>
                            <TD><input type="submit" value="Ajouter"></TD>
                        </form>
                    </TR>
                </TABLE>

                <hr>
                IMMUNITÉS
                <TABLE width="80%" align="center">
                    <tr>
                        <th>Sort</th>
                        <th>Y compris<br>lancers runiques</th>
                        <th>Valeur (entre 0 et 1)</th>
                        <th>--</th>
                    </tr>
                    <?php $req_m_sorts = "select immun_sort_cod, sort_nom, immun_gmon_cod, immun_runes, immun_valeur
						from monstre_generique_immunite
						inner join sorts on sort_cod = immun_sort_cod
						where immun_gmon_cod  = $gmon_cod";

                    $stmt_m_sorts = $pdo->query($req_m_sorts);
                    while ($result_m_sorts = $stmt_m_sorts->fetch())
                    {
                        $sort_nom     = $result_m_sorts['sort_nom'];
                        $immun_valeur = $result_m_sorts['immun_valeur'];
                        $immun_runes  = $result_m_sorts['immun_runes'];
                        ?>
                        <TR>
                            <TD><?php echo $sort_nom; ?></TD>
                            <TD><?php echo $immun_runes; ?></TD>
                            <TD><?php echo $immun_valeur; ?></TD>
                            <TD>
                                <form method="post">
                                    <input type="hidden" name="methode2" value="edit"/>
                                    <input type="hidden" name="sel_method" value="edit"/>
                                    <input type="hidden" name="methode" value="delete_mon_immunite"/>
                                    <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>"/>
                                    <input type="hidden" name="sort_cod"
                                           value="<?php echo $result_m_sorts['immun_sort_cod'] ?>"/>
                                    <input type="submit" value="Supprimer"/>
                                </form>
                            </TD>
                        </TR>
                    <?php }
                    ?>
                    <TR>
                        <form method="post">
                            <input type="hidden" name="methode2" value="edit">
                            <input type="hidden" name="sel_method" value="edit">
                            <input type="hidden" name="methode" value="add_mon_immunite">
                            <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                            <TD>Ajouter une immunité à :<br/>
                                <select name="sort_cod">
                                    <?php $req_m_sorts = "select sort_cod, sort_nom 
						from sorts 
						where not exists (
							select 1 from monstre_generique_immunite
							where immun_gmon_cod = $gmon_cod and immun_sort_cod = sort_cod)
						order by sort_nom";
                                    $stmt_m_sorts      = $pdo->query($req_m_sorts);
                                    while ($result_m_sorts = $stmt_m_sorts->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $result_m_sorts['sort_cod'] ?>"><?php echo $result_m_sorts['sort_nom'] ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </TD>
                            <TD><input type="checkbox" name="immun_rune" value="O"/></TD>
                            <TD><input type="text" value="" name="immun_valeur"/></TD>
                            <TD><input type="submit" value="Ajouter"/></TD>
                        </form>
                    </TR>
                </TABLE>

                <hr>
                COMPETENCES

                <TABLE width="80%" align="center">
                    <tr>
                        <th>Competence</th>
                        <th>Valeur</th>
                    </tr>
                    <?php $req_m_comps =
                        "select typc_libelle,gtypc_typc_cod,gtypc_valeur from  	gmon_type_comp,  	type_competences where gtypc_gmon_cod  = $gmon_cod and gtypc_typc_cod = typc_cod";


                    $stmt_m_comps = $pdo->query($req_m_comps);
                    while ($result_m_comps = $stmt_m_comps->fetch())
                    {

                        $typc_libelle = $result_m_comps['typc_libelle'];
                        $gtypc_valeur = $result_m_comps['gtypc_valeur'];
                        $gtypc_cod    = $result_m_comps['gtypc_typc_cod'];

                        $req_detail_comp  =
                            "select comp_libelle from competences where comp_typc_cod = $gtypc_cod and comp_connu = 'O'";
                        $stmt_detail_comp = $pdo->query($req_detail_comp);
                        $liste_comp       = "";
                        while ($result_detail_comp = $stmt_detail_comp->fetch())
                        {
                            $liste_comp = $liste_comp . $result_detail_comp['comp_libelle'] . ", ";
                        }
                        ?>
                        <TR>
                            <TD width="40%"><?php echo $typc_libelle; ?> (<?php echo $liste_comp; ?>)</TD>
                            <TD>
                                <form method="post">
                                    <input type="hidden" name="methode2" value="edit">
                                    <input type="hidden" name="sel_method" value="edit">
                                    <input type="hidden" name="methode" value="mod_comp_mon">
                                    <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                                    <input type="hidden" name="typc_cod"
                                           value="<?php echo $result_m_comps['gtypc_typc_cod']; ?>">
                                    <INPUT type="text" name="valeur"
                                           value="<?php echo $result_m_comps['gtypc_valeur']; ?>">
                                    <input type="submit" value="Modifier">
                                </form>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="methode2" value="edit">
                                    <input type="hidden" name="sel_method" value="edit">
                                    <input type="hidden" name="methode" value="supr_comp_mon">
                                    <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                                    <input type="hidden" name="typc_cod"
                                           value="<?php echo $result_m_comps['gtypc_typc_cod']; ?>">
                                    <input type="submit" value="Supprimer">
                                </form>
                            </TD>
                        </TR>
                    <?php }
                    ?>
                    <TR>
                        <form method="post">
                            <input type="hidden" name="methode2" value="edit">
                            <input type="hidden" name="sel_method" value="edit">
                            <input type="hidden" name="methode" value="add_mon_comp">
                            <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                            <TD colspan="3">Ajouter la Competence:
                                <select name="typc_cod">
                                    <?php $req_m_comps =
                                        "select typc_cod,typc_libelle from type_competences where not exists(select 1 from gmon_type_comp where gtypc_gmon_cod  = $gmon_cod and gtypc_typc_cod = typc_cod) order by typc_libelle";
                                    $stmt_m_comps      = $pdo->query($req_m_comps);
                                    while ($result_m_comps = $stmt_m_comps->fetch())
                                    {
                                        $gtypc_cod        = $result_m_comps['typc_cod'];
                                        $req_detail_comp  =
                                            "select comp_libelle from competences where comp_typc_cod = $gtypc_cod and comp_connu = 'O'";
                                        $stmt_detail_comp = $pdo->query($req_detail_comp);
                                        $liste_comp       = "";
                                        while ($result_detail_comp = $stmt_detail_comp->fetch())
                                        {
                                            $liste_comp = $liste_comp . $result_detail_comp['comp_libelle'] . ", ";
                                        }

                                        ?>
                                        <option value="<?php echo $result_m_comps['typc_cod'] ?>"><?php echo $result_m_comps['typc_libelle'] ?>
                                            ( <?php echo $liste_comp ?> )
                                        </option>
                                    <?php }
                                    ?>
                                </select>
                            </TD>
                    </TR>
                    <TR>
                        <TD>Valeur: <input type="text" name="valeur" value="0"></TD>
                        <TD><input type="submit" value="Ajouter"></TD>
                        </form>
                    </TR>
                </TABLE>
                <hr>
                COMPETENCES SPECIFIQUES

                <TABLE width="80%" align="center">
                    <tr>
                        <th>Competence</th>
                        <th>Valeur</th>
                    </tr>

                    <?php $req_m_comps =
                        "select comp_cod,  comp_libelle,gmoncomp_valeur,gmoncomp_chance from competences, monstre_generique_comp "
                        . "where gmoncomp_gmon_cod  = $gmon_cod and gmoncomp_comp_cod = comp_cod";

                    $stmt_m_comps = $pdo->query($req_m_comps);
                    while ($result_m_comps = $stmt_m_comps->fetch())
                    {
                        ?>
                        <TR>
                            <TD width="40%"><?php echo $result_m_comps['comp_libelle']; ?> Pourcentage :
                                (<?php echo $result_m_comps['gmoncomp_valeur'] ?> %) Chance :
                                (<?php echo $result_m_comps['gmoncomp_chance'] ?> %)
                            </TD>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="methode2" value="edit">
                                    <input type="hidden" name="sel_method" value="edit">
                                    <input type="hidden" name="methode" value="supr_comp_mon_spe">
                                    <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                                    <input type="hidden" name="typc_cod"
                                           value="<?php echo $result_m_comps['comp_cod']; ?>">
                                    <input type="submit" value="Supprimer">
                                </form>
                            </TD>
                        </TR>
                    <?php }
                    ?>

                    <TR>
                        <form method="post">
                            <input type="hidden" name="methode2" value="edit">
                            <input type="hidden" name="sel_method" value="edit">
                            <input type="hidden" name="methode" value="add_mon_comp_spe">
                            <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                            <TD colspan="2">Ajouter la Competence:
                                <select name="typc_cod">
                                    <?php $req_m_comps =
                                        "select comp_cod,  comp_libelle from competences where comp_connu <> 'O'   "
                                        . "and not exists(select 1 from monstre_generique_comp where gmoncomp_gmon_cod  = $gmon_cod and gmoncomp_comp_cod = comp_cod) order by comp_libelle";
                                    $stmt_m_comps      = $pdo->query($req_m_comps);
                                    while ($result_m_comps = $stmt_m_comps->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $result_m_comps['comp_cod'] ?>"><?php echo $result_m_comps['comp_libelle'] ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </TD>
                            <TD>Pourcentage :<INPUT type="text" name="valeur" value="100"></TD>
                            <TD>Chance :<INPUT type="text" name="chance" value="100"></TD>
                            <TD><input type="submit" value="Ajouter"></TD>
                        </form>
                    </TR>
                </TABLE>
                <HR>EFFETS AUTOMATIQUES<br><br>
                <?php // Liste des monstres
                $req = 'select gmon_nom, gmon_cod from monstre_generique order by gmon_nom';
                echo '<select id="liste_monstre_modele" style="display:none;">' . $html->select_from_query($req, 'gmon_cod', 'gmon_nom') . '</select>';

                // Liste des Bonus-malus
                $req = "select tbonus_libc, CASE WHEN tbonus_compteur='O' THEN '[compteur] - ' ELSE '' END || tonbus_libelle || case when tbonus_gentil_positif then ' (+)' else ' (-)' end as tonbus_libelle
						from bonus_type
						order by tonbus_libelle ";
                echo '<select id="liste_bm_modele" style="display:none;">' . $html->select_from_query($req, 'tbonus_libc', 'tonbus_libelle') . '</select>';

                // Liste des Bonus-malus compteur
                $req = "select tbonus_libc, CASE WHEN tbonus_compteur='O' THEN '[compteur] - ' ELSE '' END || tonbus_libelle || case when tbonus_gentil_positif then ' (+)' else ' (-)' end as tonbus_libelle
						from bonus_type
						order by tonbus_libelle ";
                echo '<select id="liste_bmc_modele" style="display:none;">' . $html->select_from_query($req, 'tbonus_libc', 'tonbus_libelle') . '</select>';

                // Liste des sorts
                $req = "select distinct dsort_dieu_cod, sort_cod, case when dsort_dieu_cod is null then '' else 'Divin - ' end || sort_nom || ' (' || case when sort_case='O' then 'case/' else '' end || case when sort_aggressif='O' then 'agressif)' when sort_soutien='O' then 'soutien)' else 'neutre)' end sort_nom
                        from sorts
                        left outer join dieu_sorts ON dsort_sort_cod = sort_cod
                        where dsort_dieu_cod is null                        
                        order by dsort_dieu_cod desc ,sort_nom
                                 ";
                echo '<select id="liste_sort_modele" style="display:none;">' . $html->select_from_query($req, 'sort_cod', 'sort_nom') . '</select>';

                // Liste des races
                $req = "select race_cod, race_nom from race order by race ";
                echo '<select id="liste_race_modele" style="display:none;">' . $html->select_from_query($req, 'race_cod', 'race_nom') . '</select>';


                ?>
                <form method="post" onsubmit="return Validation.Valide ();">
                    <input type="hidden" name="methode2" value="edit">
                    <input type="hidden" name="methode" value="add_mon_fonction">
                    <input type="hidden" name="sel_method" value="edit">
                    <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                    <input type='hidden' name='fonctions_supprimees' id='fonctions_supprimees' value=''/>
                    <input type='hidden' name='fonctions_ajoutees' id='fonctions_ajoutees' value=''/>
                    <input type='hidden' name='fonctions_annulees' id='fonctions_annulees' value=''/>
                    <input type='hidden' name='fonctions_existantes' id='fonctions_existantes' value=''/>
                    <div id="liste_fonctions"></div>
                    <?php $req = "select fonc_cod, fonc_nom, fonc_type, case when fonc_nom='deb_tour_generique' then substr(fonc_effet,1,3) else fonc_effet end as fonc_effet, case when fonc_nom='deb_tour_generique' and substr(fonc_effet,4,1)='+' then 'O' else 'N' end as fonc_cumulatif, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message, fonc_trigger_param
						from fonction_specifique where fonc_gmon_cod = $gmon_cod order by fonc_cod ";
                    $stmt      = $pdo->query($req);
                    while ($result = $stmt->fetch())
                    {
                        $fonc_id                = $result['fonc_cod'];
                        $fonc_type              = $result['fonc_type'];
                        $fonc_nom               = $result['fonc_nom'];
                        $fonc_effet             = $result['fonc_effet'];
                        $fonc_cumulatif         = $result['fonc_cumulatif'];
                        $fonc_force             = $result['fonc_force'];
                        $fonc_duree             = $result['fonc_duree'];
                        $fonc_type_cible        = $result['fonc_type_cible'];
                        $fonc_nombre_cible      = $result['fonc_nombre_cible'];
                        $fonc_portee            = $result['fonc_portee'];
                        $fonc_proba             = $result['fonc_proba'];
                        $fonc_message           = $result['fonc_message'];
                        $fonc_trigger_param     = $result['fonc_trigger_param'] == "" ? "{}" : $result['fonc_trigger_param'];

                        // on va enjoliver le champs cumulatif à l'affichage pour afficher les valeurs de progressivité.
                        if ($fonc_cumulatif=='O') $fonc_cumulatif = bm_progressivite($fonc_effet, $fonc_force);

                        echo "<script>EffetAuto.EcritEffetAutoExistant(\"$fonc_type\", \"$fonc_nom\", $fonc_id, \"$fonc_force\", \"$fonc_duree\", \"$fonc_message\", \"$fonc_effet\", \"$fonc_cumulatif\", \"$fonc_proba\", \"$fonc_type_cible\", \"$fonc_portee\", \"$fonc_nombre_cible\", $fonc_trigger_param);</script>";
                    }
                    ?>
                    <div style='clear: both;'>
                        <a onclick='EffetAuto.NouvelEffetAuto (); return false;'>Nouvel effet</a><br/><br/>
                        <input type="submit" value="Valider les suppressions / modifications / ajouts d’effets !"
                               class='test'/>
                    </div>
                </form>
                <hr/>
                OBJETS
                <TABLE width="80%" align="center">
                    <tr>
                        <th>Objet</th>

                        <th>Chance de posséder (SUR 10.000 !!!)</th>
                        <th style="text-align: center">Chance de drop <em style="font-size: 10px;">(si possédé)</em>
                        </th>
                    </tr>
                    <?php $req_drops =
                        "select gobj_nom,ogmon_gobj_cod,ogmon_equipe,ogmon_chance,COALESCE(gobj_chance_drop_monstre,100) as gobj_chance_drop_monstre from objets_monstre_generique,objet_generique where ogmon_gmon_cod = $gmon_cod and ogmon_gobj_cod = gobj_cod";

                    $stmt_drops = $pdo->query($req_drops);
                    //echo $req_drops;
                    while ($result_drops = $stmt_drops->fetch())
                    {

                        $gobj_nom        = $result_drops['gobj_nom'];
                        $drop_obj_chance = $result_drops['ogmon_chance'];
                        ?>
                        <TR>
                            <TD><?php echo $gobj_nom; ?></TD>
                            <TD>
                                <form method="post">
                                    <input type="hidden" name="methode2" value="edit">
                                    <input type="hidden" name="sel_method" value="edit">
                                    <input type="hidden" name="methode" value="mod_drop_mon">
                                    <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                                    <input type="hidden" name="gobj_cod"
                                           value="<?php echo $result_drops['ogmon_gobj_cod']; ?>">
                                    <INPUT type="text" name="valeur"
                                           value="<?php echo $result_drops['ogmon_chance']; ?>">
                                    &nbsp;Equiper:<input type="checkbox"
                                                         name="ogmon_equipe" <?php echo $result_drops['ogmon_equipe'] == "t" ? "checked" : ""; ?>>
                                    <input type="submit" value="Modifier">
                                </form>
                            </td>
                            <td style="text-align: center"><a target="_blank"
                                                              href="admin_objet_generique_edit.php?&methode=mod2&gobj_cod=<?php echo $result_drops['ogmon_gobj_cod']; ?>"><?php echo $result_drops['gobj_chance_drop_monstre'] . '</a> %'; ?>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="methode2" value="edit">
                                    <input type="hidden" name="sel_method" value="edit">
                                    <input type="hidden" name="methode" value="supr_drop_mon">
                                    <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                                    <input type="hidden" name="gobj_cod"
                                           value="<?php echo $result_drops['ogmon_gobj_cod']; ?>">
                                    <input type="submit" value="Supprimer">
                                </form>
                            </td>
                        </TR>
                    <?php }
                    ?>
                    <TR>
                        <form method="post">
                            <input type="hidden" name="methode2" value="edit">
                            <input type="hidden" name="sel_method" value="edit">
                            <input type="hidden" name="methode" value="add_mon_drop">
                            <input type="hidden" name="gmon_cod" value="<?php echo $gmon_cod ?>">
                            <TD>Ajouter l’objet:
                                <select name="gobj_cod">
                                    <?php $req_drops =
                                        "select gobj_nom,gobj_cod from objet_generique where not exists(select 1 from objets_monstre_generique where ogmon_gmon_cod = $gmon_cod and ogmon_gobj_cod = gobj_cod) order by gobj_nom";
                                    $stmt_drops      = $pdo->query($req_drops);
                                    while ($result_drops = $stmt_drops->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $result_drops['gobj_cod'] ?>"><?php echo $result_drops['gobj_nom'] ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </TD>
                            <TD style="padding-left:5px;">
                                <INPUT type="text" name="valeur" value="0">
                                &nbsp;Equiper:<input type="checkbox" name="ogmon_equipe">
                                <input type="submit" value="Ajouter"></TD>
                            <td colspan="2"></td>
                        </form>
                    </tr>
                </TABLE>
            <?php }
            } else
            {

                // NOUVEAU MONSTRE GENERIQUE
                ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="methode2" value="edit">
                    <input type="hidden" name="methode" value="create_mon">

                    <TABLE width="80%" align="center">
                        <TR>
                            <TD colspan="2">Perso N&deg;<?php echo $gmon_cod ?> Nom:<input type="text" name="gmon_nom"
                                                                                           value=""><BR>
                            </TD>
                            <TD colspan="2"><img alt="avatar" onclick="open_imglist();" style="vertical-align:top;"
                                                 id="output_image"
                                                 height="60px" src="/images/avatars/defaut.png">

                                <div style="display:inline-block"><input type="file" name="avatar_file" accept="image/*"
                                                                         onchange="preview_image(event);"><br>
                                    <strong>ou</strong><br><input type="button" style="margin-top: 5px;" class="test"
                                                                  name="nouvel_avatar"
                                                                  value="Sélectionner une image existante sur le serveur"
                                                                  onclick="open_imglist();"></div>

                        </TR>
                        <TR>
                            <TD colspan="4">
                                <input id="type-img-avatar" type="hidden" name="type-img-avatar" value="">
                                <input id="id-gmon_avatar" type="hidden" name="gmon_avatar"
                                       value="<?php echo $gmon_avatar ?>">
                                <div id="images-container"
                                     style="display:none; height:80px; width: 100%; overflow-x:scroll;"><?php echo $images_list; ?></div>
                            </TD>
                        <TR>
                        <TR>
                        <TR>
                            <TH width="25%">CHAMP</TH>
                            <TH width="25%">VALEUR</TH>
                            <TH width="25%">CHAMP</TH>
                            <TH width="25%">VALEUR</TH>
                        </TR>
                        <TR>
                            <TD>Force</TD>
                            <TD><INPUT type="text" name="gmon_for" value=""></TD>
                            <TD>Amélioration dégats CaC</TD>
                            <TD><INPUT type="text" name="gmon_amelioration_degats" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Dextérité</TD>
                            <TD><INPUT type="text" name="gmon_dex" value=""></TD>
                            <TD>Amélioration dégats Dist</TD>
                            <TD><INPUT type="text" name="gmon_amel_deg_dist" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Intelligence</TD>
                            <TD><INPUT type="text" name="gmon_int" value=""></TD>
                            <TD>Amélioration Vue</TD>
                            <TD><INPUT type="text" name="gmon_amelioration_vue" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Constitution</TD>
                            <TD><INPUT type="text" name="gmon_con" value=""></TD>
                            <TD>Amélioration armure</TD>
                            <TD><INPUT type="text" name="gmon_amelioration_armure" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Temps de tour</TD>
                            <TD><INPUT type="text" name="gmon_temps_tour" value=""></TD>
                            <TD>Amélioration Régénération</TD>
                            <TD><INPUT type="text" name="gmon_amelioration_regen" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Dés de régen</TD>
                            <TD><INPUT type="text" name="gmon_des_regen" value=""></TD>
                            <TD>Valeur dés régen</TD>
                            <TD><INPUT type="text" name="gmon_valeur_regen" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Vue</TD>
                            <TD><INPUT type="text" name="gmon_vue" value=""></TD>
                            <TD>Race</TD>
                            <TD>
                                <SELECT name="gmon_race_cod">
                                    <?php // LISTE DES RACES
                                    $req_races = "select race_cod,race_nom from race order by race_nom";

                                    $stmt_race = $pdo->query($req_races);
                                    while ($result_race = $stmt_race->fetch())
                                    {
                                        $race_cod = $result_race['race_cod'];
                                        $sel      = "";
                                        echo "<OPTION value=\"$race_cod\" $sel>" . $result_race['race_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                        </TR>

                        <TR>
                            <TD>Dés de dégâts</TD>
                            <TD><INPUT type="text" name="gmon_nb_des_degats" value=""></TD>
                            <TD>Valeur dés dégâts</TD>
                            <TD><INPUT type="text" name="gmon_val_des_degats" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Niveau</TD>
                            <TD><INPUT type="text" name="gmon_niveau" value=""></TD>
                            <TD>Brouzoufs</TD>
                            <TD><INPUT type="text" name="gmon_or" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Arme</TD>
                            <TD>
                                <SELECT name="gmon_arme">
                                    <option value="null">aucune</option>
                                    <?php // LISTE DES ARMES
                                    $req_armes =
                                        "select 	gobj_cod,gobj_nom from objet_generique where gobj_tobj_cod = 1 order by gobj_nom";

                                    $stmt_armes = $pdo->query($req_armes);
                                    while ($result_armes = $stmt_armes->fetch())
                                    {
                                        $arme_cod = $result_armes['gobj_cod'];
                                        $sel      = "";
                                        echo "<OPTION value=\"$arme_cod\" $sel>" . $result_armes['gobj_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                            <TD>Armure</TD>
                            <TD>
                                <SELECT name="gmon_armure">
                                    <option value="null">aucune</option>
                                    <?php // LISTE DES ARMES
                                    $req_armures =
                                        "select 	gobj_cod,gobj_nom from objet_generique where gobj_tobj_cod = 2 order by gobj_nom";

                                    $stmt_armures = $pdo->query($req_armures);
                                    while ($result_armures = $stmt_armures->fetch())
                                    {
                                        $armure_cod = $result_armures['gobj_cod'];
                                        $sel        = "";
                                        echo "<OPTION value=\"$armure_cod\" $sel>" . $result_armures['gobj_nom'] . "</OPTION>\n";
                                    }
                                    ?>
                                </SELECT>
                            </TD>
                        </TR>

                        <TR>
                            <TD>Soutien</TD>
                            <TD><INPUT type="text" name="gmon_soutien" value=""></TD>
                            <TD>Vampirisme</TD>
                            <TD><INPUT type="text" name="gmon_vampirisme" value=""></TD>
                        </TR>

                        <TR>
                            <TD>Taille</TD>
                            <TD><INPUT type="text" name="gmon_taille" value=""></TD>
                            <TD>Sexe</TD>
                            <TD><SELECT name="gmon_sex">
                                    <OPTION value="">
                                        Aléatoire (Mâle ou Femelle)
                                    </OPTION>
                                    <OPTION value="M">
                                        Mâle
                                    </OPTION>
                                    <OPTION value="F">
                                        Femelle
                                    </OPTION>
                                    <OPTION value="A">
                                        Androgyne
                                    </OPTION>
                                    <OPTION value="H">
                                        Hermaphrodite
                                    </OPTION>
                                    <OPTION value="I">
                                        Inconnu
                                    </OPTION>
                                </SELECT></TD>z
                        </TR>

                        <TR>
                            <TD>Description</TD>
                            <TD colspan="3">
                                <textarea name="gmon_description" cols="80"></textarea>
                            </TD>
                        </tr>

                        <tr>
                            <TD>Monstre utilisé ou non pour les contrats de chasse (O ou N)</TD>
                            <TD>
                                <INPUT type="text" name="gmon_quete" value="">
                            </TD>
                            <TD>Durée de vie du monstre (en jours ; 0 ou vide pour un monstre classique)</TD>
                            <TD>
                                <INPUT type="text" name="gmon_duree_vie" value="">
                            </TD>
                        </TR>
                        <TR>
                            <TD colspan="4"><input type="submit" value="Enregistrer le modèle"></TD>
                        </TR>
                    </TABLE>
                </form>
            <?php }
            break;
    }
}


?>

    <a class="centrer" href="<?php echo $_SERVER['PHP_SELF'] ?>">Retour au début</a>

<?php //include"../logs/monstre_edit.log";
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
