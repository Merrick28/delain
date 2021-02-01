<?php


$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = $verif_connexion->perso;
$methode   = get_request_var('methode', '');
ob_start();
include "../includes/fonctions.php";
$parm = new parametres();
//
//log_debug('Debut de page inventaire');
//
// Récupération des données du perso
$req_or     = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
$stmt       = $pdo->query($req_or);
$qte_or     = ($result = $stmt->fetch()) ? $result['pbank_or'] : 0;
$cout_repar = $parm->getparm(40);

$req_perso        =
    "select perso_enc_max, perso_po, perso_gmon_cod, perso_pa, perso_type_perso, gmon_type_ia from perso left join monstre_generique on gmon_cod=perso_gmon_cod where perso_cod = $perso_cod ";
$stmt             = $pdo->query($req_perso);
$result           = $stmt->fetch();
$poids_total      = $result['perso_enc_max'];
$perso_po         = $result['perso_po'];
$perso_gmon_cod   = $result['perso_gmon_cod'];
$gmon_type_ia     = $result['gmon_type_ia'];
$is_golem_brz     = $gmon_type_ia == 12;    // 12 = ia "Golem de brouzoufs"
$is_golem_arm     = $gmon_type_ia == 13;    // 13 = ia "Golem d'armes et d'armures"
$is_golem_pps     = $gmon_type_ia == 16;    // 16 = ia "Golem de pierres précieuses"
$is_golem         = $is_golem_brz || $is_golem_arm || $is_golem_pps;
$pa               = $result['perso_pa'];
$perso_type_perso = $result['perso_type_perso'];

if (!isset($dr))
{
    $dr = 0;
}
if (!isset($dq))
{
    $dq = 0;
}
if (!isset($dcompo))
{
    $dcompo = 0;
}
if (!isset($dgrisbi))
{
    $dgrisbi = 0;
}

?>
    <STYLE>
        .secret {
            text-decoration: none;
            color: black;
            background: transparent;
            cursor: text;
        }

        .secret:link {
            text-decoration: none;
            color: black;
            background: transparent;
            cursor: text;
        }

        .secret:visited {
            text-decoration: none;
            color: black;
            background: transparent;
            cursor: text;
        }

        .secret:active {
            text-decoration: none;
            color: black;
            background: transparent;
            cursor: text;
        }

        .secret:hover {
            text-decoration: none;
            color: black;
            background: transparent;
            cursor: text;
        }
    </STYLE>
<?php //Réalisation des actions

switch ($methode)
{
    case "remettre":
        if ($pa >= 2)
        {
            $perobj       = get_request_var('perobj');
            $req_remettre = "select remettre_objet($perso_cod,$perobj) as equipe";
            $stmt         = $pdo->query($req_remettre);
            $result       = $stmt->fetch();
            $tab_remettre = $result['equipe'];
            if ($tab_remettre == "0")
            {
                echo("<br><strong>L’équipement a été remis dans votre inventaire</strong><br>");
            } else
            {
                echo("<br><strong>$tab_remettre</strong><br>");
            }
        } else
        {
            ?>
            <br><strong>Vous n’avez pas assez de PA pour effectuer cette action !</strong><br>
            <?php
        }
        break;

    case "equiper":
        $erreur = 0;
        if ($pa < 2)
        {
            echo("<br><strong>Vous n’avez pas assez de PA pour effectuer cette action !</strong><br>");
            $erreur = 1;
        }

        // En commentaire car aussi fait par equipe_objet()
        //// vérifier si l'objet possède des prérequis pour l'équiper
        // if ($perso_type_perso == 3)
        // {
        //    echo "<br><strong>Un familier ne peut pas équiper d’objet !</strong><br>";
        //    $erreur = 1;
        //}
        $objet = get_request_var('objet');
        if ($erreur == 0)
        {
            $req_remettre = "select equipe_objet($perso_cod,$objet) as equipe";
            $stmt         = $pdo->query($req_remettre);
            $result       = $stmt->fetch();
            $tab_remettre = $result['equipe'];
            if ($tab_remettre == 0)
            {
                echo("<br><strong>L’objet a été équipé avec succès.</strong><br>");
            } else
            {
                $tab_remettre = explode(';', $tab_remettre);
                $texte        = (isset($tab_remettre[1])) ? $tab_remettre[1] : $tab_remettre[0];
                echo("<br><strong>$texte</strong><br>");
            }
        }
        break;

    case "abandonner":
        $req_defi = "select 1 from defi where defi_statut = 1 and $perso_cod in (defi_lanceur_cod, defi_cible_cod)
			UNION ALL select 1 from defi
				inner join perso_familier on pfam_perso_cod in (defi_lanceur_cod, defi_cible_cod)
				where defi_statut = 1 and pfam_familier_cod = $perso_cod";
        $stmt     = $pdo->query($req_defi);
        if ($stmt->rowCount() > 0 && !isset($validerabandon))
        {
            echo "<br><strong>Vous êtes actuellement en plein défi ! Si vous abandonnez cet objet, vous ne pourrez plus le ramasser à nouveau.
				<a href='inventaire.php?methode=abandonner&objet=$objet&validerabandon=1'>Abandonner quand même ! (1PA)</a></strong>";
        } else
        {

            $req      =
                'select depose_objet(perso_cod,' . $_REQUEST['objet'] . ') as resultat from perso where perso_cod = '
                . $perso_cod;
            $stmt     = $pdo->query($req);
            $result   = $stmt->fetch();
            $resultat = $result['resultat'];
            echo "<br><strong>$resultat</strong><br>";
        }
        break;

    case "manger":
        if ($is_golem_brz && $pa > 5)
        {
            $px = 20;
            if ($perso->perso_px + 1 <= max($perso->perso_px + $px, $perso->perso_po))
            {
                $nv_px           = intval(max($perso->perso_px + $px, $perso->perso_po));
                $perso->perso_pa = $perso->perso_pa - 6;
                $perso->perso_px = $perso->perso_px + $nv_px;
                $perso->stocke();
                echo "<br><strong>Miam scrountch miom !</strong> Cette action vous a redonné des PX, en fonction de la quantité de brouzoufs possédée...<br>";
            } else
            {
                echo "<br><strong>Scrountch ? A pas scrountch :(</strong> Vous ne possédez pas assez de brouzoufs pour gagner des PX de cette façon...<br>";
            }
        } else if (($is_golem_arm || $is_golem_pps) && $pa > 5)
        {
            $req    = 'select golem_digestion(' . $perso_cod . ') as resultat ';
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<br><strong>Miam scrountch miom !</strong> " . $result['resultat'] . "<br>";
        } else
        {
            echo "<br><strong>Erreur !</strong> Seuls les golems savent digérer leur inventaire... Et il leur faut assez de PA !<br>";
        }
        break;
}

$req_poids   = "select get_poids($perso_cod) as poids";
$stmt        = $pdo->query($req_poids);
$result      = $stmt->fetch();
$poids_porte = $result['poids'];

// identification auto de certains objets (runes, objets de quêtes, poissons, etc...)
$req_id = "update perso_objets
	set perobj_identifie = 'O'
	where perobj_perso_cod = " . $perso_cod . "
	and perobj_identifie != 'O'
	and exists
	(select 1 from objets,objet_generique,type_objet
	where perobj_obj_cod = obj_cod
	and obj_gobj_cod = gobj_cod
	and gobj_tobj_cod = tobj_cod
	and tobj_identifie_auto = 1 ) ";
$req_id = "select identifie_perso_objet($perso_cod)";
$stmt   = $pdo->query($req_id);
//
//log_debug('Fin ident auto');
//log_debug($req_id);
//

?>
    <div class="centrer">
        <table width="100%" cellspacing="2" cellpadding="2">
            <tr>
                <td>Encombrement : <?php echo $poids_porte . "/" . $poids_total; ?></td>
            </tr>

            <tr>
                <td>Vous avez <?php echo $perso_po; ?> brouzoufs <em>(<?php echo $qte_or; ?> en banque)</em>-- <a
                            href="deposer_or.php">Déposer des brouzoufs (1 PA)</a>.
                </td>
            </tr>
            <?php if ($perso_type_perso != 2)
            {
                echo '<tr><td>Bilan des runes, composants, objets de quête, etc... &nbsp;: <a href="inventaire_persos.php">pour tous mes persos</a>. </td></tr>';

            }
            if ($is_golem)
            {
                echo '<tr><td><a href="?methode=manger">Digérer tout ça ! (6 PA)</a></td></tr>';
            }
            ?>
        </table>
        <?php
        /**************************/
        /* Etape 2 : matos équipé */
        /**************************/
        $req_equipe     =
            "select obj_etat,obj_etat_max,obj_cod,tobj_cod,gobj_cod,tobj_libelle,obj_nom,perobj_cod,obj_poids,gobj_pa_normal,gobj_pa_eclair,gobj_url from perso_objets,objets,objet_generique,type_objet ";
        $req_equipe     = $req_equipe . "where perobj_perso_cod = $perso_cod ";
        $req_equipe     = $req_equipe . "and perobj_equipe = 'O' ";
        $req_equipe     = $req_equipe . "and perobj_obj_cod = obj_cod ";
        $req_equipe     = $req_equipe . "and obj_gobj_cod = gobj_cod ";
        $req_equipe     = $req_equipe . "and gobj_tobj_cod = tobj_cod ";
        $req_equipe     = $req_equipe . "order by tobj_libelle";
        $stmt           = $pdo->query($req_equipe);
        $nb_equipe      = $stmt->rowCount();
        $poid_catégorie = 0;

        //
        //log_debug('Fin requête équipé');
        //
        ?>
        <table width="100%" cellspacing="2" cellpadding="2">

            <?php
            if ($nb_equipe != 0)
            {
                ?>
                <tr>
                    <td class="soustitre2">Type</td>
                    <td class="soustitre2">Objet</td>
                    <td class="soustitre2">Poids</td>
                    <td class="soustitre2">Etat</td>
                    <td class="soustitre2">PA/att.</td>

                    <td></td>
                    <td></td>
                </tr>
                <form name="remettre" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="perobj">
                    <input type="hidden" name="methode" value="remettre">
                    <?php

                    while ($result = $stmt->fetch())
                    {
                        $poid_catégorie += $result["obj_poids"] > 0 ? $result["obj_poids"] : 0;

                        $examiner = "";
                        if ($result['gobj_url'] != null)
                        {
                            $examiner = " (<a href=\"objets/" . $result['gobj_url'] . "\">Voir le détail</a>) ";
                        }

                        $req   = "select obon_cod,obon_libelle from bonus_objets,objets ";
                        $req   = $req . "where obj_cod = " . $result['obj_cod'] . " and obj_obon_cod = obon_cod ";
                        $stmt2 = $pdo->query($req);
                        if ($stmt2->rowCount() != 0)
                        {
                            $result2 = $stmt2->fetch();
                            $bonus   = " (" . $result2['obon_libelle'] . ")";
                            $url_bon = "&bon=" . $result2['obon_cod'];
                        } else
                        {
                            $bonus   = "";
                            $url_bon = "";
                        }
                        $obj_etat     = $result['obj_etat'];
                        $obj_etat_max = $result['obj_etat_max'];
                        $cpl_class    = '';
                        if ($obj_etat < 60)
                            $cpl_class = '_vert';
                        if ($obj_etat < 40)
                            $cpl_class = '_orange';
                        if ($obj_etat < 20)
                            $cpl_class = '_rouge';
                        echo "<tr>";
                        echo "<td class=\"soustitre2" . $cpl_class . "\" >" . $result['tobj_libelle'] . $examiner, "</td>";
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet3.php?objet=" . $result['obj_cod'] . "&origine=i", $url_bon, "\">" . $result['obj_nom'], $bonus, "</a></td>";
                        echo "<td class=\"soustitre2\"><div style=\"text-align:right\">" . $result['obj_poids'] . "</div></td>";
                        echo "<td class=\"soustitre2\">" . get_etat($result['obj_etat']) . "</td>";
                        echo "<td class=\"soustitre2\"><div style=\"text-align:right\">" . $result['gobj_pa_normal'] . "</div></td>";
                        echo "<td><a href=\"javascript:document.remettre.perobj.value=" . $result['perobj_cod'] . ";document.remettre.submit();\">";
                        echo "Remettre dans l’inventaire (2PA)</a>";

                        ?>
                        </td>
                        <td nowrap>
                            <?php
                            if (($result['tobj_cod'] == 1) && ($result['obj_etat'] < 100))
                            {
                                echo "<a href=\"action.php?methode=repare&type=1&objet=" . $result['obj_cod'] . "\">Réparer (" . $cout_repar . " PA)</a>";
                            }
                            ?>
                        </td>
                        </tr>
                        <?php
                    }
                    ?>
                </form>
                <?php
            } else
            {
                ?>
                <tr>
                    <td colspan="7">Aucun matériel équipé</td>
                </tr>
                <?php
            }
            ?>
            <thead>
            <tr>
                <td colspan="7" class="titre">
                    <div class="titre">Matériel équipé <em style="font-size: 9px;">(<?php echo $poid_catégorie; ?>
                            Kg)</em></div>
                </td>
            </tr>
            </thead>
        </table>
        <?php
        /*********************************************/
        /* Etape 3 : matos non équipé, non identifié */
        /*********************************************/
        $req_matos =
            "select tobj_libelle,obj_nom_generique,obj_cod,obj_poids from perso_objets,objets,objet_generique,type_objet ";
        $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
        $req_matos = $req_matos . "and perobj_identifie = 'N' ";
        $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
        $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
        $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
        $req_matos = $req_matos . "order by tobj_libelle";
        $stmt      = $pdo->query($req_matos);
        $nb_matos  = $stmt->rowCount();

        $poid_catégorie = 0;
        //
        //log_debug('Fin non esquipe non identifie');
        //
        ?>
        <table width="100%" cellspacing="2" cellpadding="2">

            <?php
            if ($nb_matos == 0)
            {
                echo("<tr><td colspan=\"6\">Aucun matériel non identifié</td></tr>");
            }
            else
            {
            ?>
            <tr>
                <td class="soustitre2">Type</td>
                <td class="soustitre2">Objet</td>
                <td class="soustitre2">Poids</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <form name="identifier" method="post" action="identifier.php"><input type="hidden" name="objet">
                <input type="hidden" name="methode" value="depose_objet">
                <?php
                while ($result = $stmt->fetch())
                {
                    $poid_catégorie += $result['obj_poids'] > 0 ? $result["obj_poids"] : 0;

                    //$tab_matos = pg_fetch_array($res_matos,$cpt);
                    echo("<tr>");
                    printf("<td class=\"soustitre2\">%s</td>", $result['tobj_libelle']);
                    printf("<td class=\"soustitre2\">%s</td>", $result['obj_nom_generique']);
                    printf("<td class=\"soustitre2\"><div style=\"text-align:right\">%s</div></td>", $result['obj_poids']);
                    echo "<td></td>";
                    echo "<td></td>";
                    echo("<td>");
                    printf("<a href=\"javascript:document.identifier.action='identifier.php';document.identifier.objet.value=%s;document.identifier.submit();\">Identifier (2PA)</a>", $result['obj_cod']);

                    echo("</td>");
                    echo("<td>");

                    printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?methode=abandonner&objet=%s\">Abandonner (1PA)</a>",
                           $result['obj_cod']);

                    echo("</td>");

                    echo("</tr>");
                }
                echo("</form>");
                }
                ?>
                <thead>
                <tr>
                    <td colspan="6" class="titre">
                        <div class="titre">Matériel non identifié <em
                                    style="font-size: 9px;">(<?php echo $poid_catégorie; ?> Kg)</em></div>
                    </td>
                </tr>
                </thead>
        </table>
        <?php

        /*****************************************/
        /* Etape 4 : matos non équipé, identifié */
        /*****************************************/

        $req_matos      = "select obj_etat, tobj_cod, gobj_cod, tobj_libelle, obj_nom, obj_cod, obj_poids, gobj_tobj_cod, gobj_pa_normal, tobj_equipable, gobj_url, COALESCE(obon_cod, -1) as obon_cod, obon_libelle
                        from perso_objets
                        INNER JOIN objets ON obj_cod = perobj_obj_cod
                        INNER JOIN objet_generique ON gobj_cod = obj_gobj_cod
                        INNER JOIN type_objet ON tobj_cod = gobj_tobj_cod
                        LEFT OUTER JOIN bonus_objets ON obon_cod = obj_obon_cod
                        WHERE perobj_perso_cod = " . $perso_cod . "
                            and perobj_identifie = 'O'
                            and perobj_equipe = 'N'
                            and gobj_tobj_cod not in (5,11,12,14,22,28,30,34,42)
                        order by tobj_libelle,gobj_nom ";
        $stmt           = $pdo->query($req_matos);
        $nb_matos       = $stmt->rowCount();
        $poid_catégorie = 0;
        //
        //log_debug('Fin non equipe, identifie');
        //
        ?>
        <table width="100%" cellspacing="2" cellpadding="2">

            <?php
            if ($nb_matos != 0)
            {
            ?>
            <tr>
                <td class="soustitre2">Type</td>
                <td class="soustitre2">Objet</td>
                <td class="soustitre2">
                    <div style="text-align:right">Poids</div>
                </td>
                <td class="soustitre2">Etat</td>
                <td class="soustitre2">PA/att.</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <form name="equiper" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"><input type="hidden"
                                                                                                   name="objet">
                <?php
                while ($result = $stmt->fetch())
                {

                    $potion_buvable =
                        ($result['tobj_cod'] == 21 && $result['gobj_cod'] != 412 && $result['gobj_cod'] != 561);
                    //if ($result['obon_cod'] >= 0)
                    $poid_catégorie += max($result['obj_poids'], 0);


                    if ($result['obon_cod'] >= 0)
                    {
                        $bonus   = " (" . $result['obon_libelle'] . ")";
                        $url_bon = "&bon=" . $result['obon_cod'];
                    } else
                    {
                        $bonus   = "";
                        $url_bon = "";
                    }
                    $examiner = "";
                    if ($result['gobj_url'] != null)
                    {
                        $examiner = " (<a href=\"objets/" . $result['gobj_url'] . "\">Voir le détail</a>) ";
                    }
                    $boire = "";
                    echo "<tr>";
                    echo "<td class=\"soustitre2\">" . $result['tobj_libelle'], "</td>";
                    echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet3.php?objet=" . $result['obj_cod'] . "&origine=i", $url_bon, "\">" . $result['obj_nom'], $bonus, "</a></td>";
                    echo "<td class=\"soustitre2\">" . $result['obj_poids'] . "</td>";
                    echo "<td class=\"soustitre2\">" . get_etat($result['obj_etat']) . "</td>";
                    echo "<td class=\"soustitre2\">" . $result['gobj_pa_normal'] . $examiner . "</td>";
                    echo "<td>";

                    if ($result['tobj_equipable'] == 1)
                    {
                        printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?methode=equiper&objet=%s\">Equiper (2PA)</a>",
                               $result['obj_cod']);
                    }

                    if ($potion_buvable)
                    {
                        //echo '<a href="potions_utilisation.php?methode=potion_inventaire1&potion=' . $result['gobj_cod'] . '">Boire (2PA)</a>';
                        echo '<a href="choix_potion.php?&obj_cod=' . $result['obj_cod'] . '">Utiliser (2PA)</a>';
                    }
                    echo("</td>");
                    echo("<td class=\"soustitre2\">");

                    printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?methode=abandonner&objet=%s\">Abandonner (1PA)</a>",
                           $result['obj_cod']);

                    echo("</td>");
                    echo "<td>";
                    echo "<a href=\"action.php?methode=repare&type=" . $result['tobj_cod'] . "&objet=" . $result['obj_cod'] . "\">Réparer (" . $cout_repar . "PA)</a>";
                    echo "</td>";
                    echo("</tr>");
                }
                echo("</form>");
                }
                else
                {
                    echo("<tr><td colspan=\"8\">Aucun matériel identifié</td></tr>");
                }
                ?>
                <thead>
                <tr>
                    <td colspan="8" class="titre">
                        <div class="titre">Matériel identifié <em
                                    style="font-size: 9px;">(<?php echo $poid_catégorie; ?> Kg)</em></div>
                    </td>
                </tr>
                </thead>
        </table>
        <table width="100%" cellspacing="2" cellpadding="2">
            <?php
            /*****************************************/
            /* Etape 5 : Runes */
            /*****************************************/
            $req            =
                "select sum(obj_poids) as poids from perso_objets,objets,objet_generique  where gobj_tobj_cod = 5 and perobj_perso_cod = :perso_cod and perobj_identifie = 'O'  and perobj_obj_cod = obj_cod and obj_gobj_cod = gobj_cod ";
            $stmt           = $pdo->prepare($req);
            $stmt           = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            $poid_catégorie = $stmt->fetch();
            if ($dr == 0)
            {
                $req_matos = "select obj_nom,sum(obj_poids) as poids,obj_frune_cod,obj_famille_rune as frune_desc,count(*) as nombre from perso_objets,objets,objet_generique
	where perobj_perso_cod = $perso_cod
	and perobj_identifie = 'O'
	and perobj_equipe = 'N'
	and perobj_obj_cod = obj_cod
	and obj_gobj_cod = gobj_cod
	and gobj_tobj_cod = 5
	group by obj_nom,obj_frune_cod,obj_famille_rune
	order by obj_frune_cod,obj_nom ";
                $stmt      = $pdo->query($req_matos);
                $nb_matos  = $stmt->rowCount();
                //log_debug($req_matos);
                echo("<tr>");
                echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Runes <em style=\"font-size: 9px;\">(" . (1 * $poid_catégorie["poids"]) . " Kg)</em> <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=1&dcompo=$dcompo&dgrisbi=$dgrisbi\">(montrer le détail)</A></div></td>");
                echo("</tr>");
                if ($nb_matos != 0)
                {
                    ?>
                    <tr>
                        <td colspan="2" class="soustitre2">Rune</td>
                        <td class="soustitre2">
                            <div style="text-align:right">Poids</div>
                        </td>
                        <td class="soustitre2">Nombre</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        echo("<tr>");
                        printf("<td colspan=\"2\" class=\"soustitre2\"><strong>%s</strong> (%s)</td>", $result['obj_nom'], $result['frune_desc']);
                        printf("<td class=\"soustitre2\">%s</td>", $result['poids']);
                        printf("<td class=\"soustitre2\">%s</td>", $result['nombre']);
                        echo("<td></td><td></td>");
                        echo("</tr>");

                    }

                } else
                {
                    echo("<tr><td colspan=\"6\">Aucune rune</td></tr>");
                }
            } else
            {
                $req_matos =
                    "select obj_cod,gobj_cod,tobj_libelle,obj_nom,obj_cod,obj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
                $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
                $req_matos = $req_matos . "and perobj_identifie = 'O' ";
                $req_matos = $req_matos . "and perobj_equipe = 'N' ";
                $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
                $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = 5 ";
                $req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
                $stmt      = $pdo->query($req_matos);
                $nb_matos  = $stmt->rowCount();
                echo("<tr>");
                echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Runes <em style=\"font-size: 9px;\">(" . (1 * $poid_catégorie["poids"]) . " Kg)</em> <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=0&dcompo=$dcompo&dgrisbi=$dgrisbi\">(cacher le détail)</A></div></td>");
                echo("</tr>");
                if ($nb_matos != 0)
                {
                    ?>
                    <tr>
                        <td class="soustitre2">Type</td>
                        <td class="soustitre2">Objet</td>
                        <td class="soustitre2">
                            <div style="text-align:right">Poids</div>
                        </td>
                        <td class="soustitre2">PA/att.</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        //$tab_matos = pg_fetch_array($res_matos,$cpt);
                        echo("<tr>");
                        printf("<td class=\"soustitre2\">%s</td>", $result['tobj_libelle']);
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet2.php?objet=" . $result['gobj_cod'] . "&origine=i\">" . $result['obj_nom'] . "</a></td>";
                        printf("<td class=\"soustitre2\">%s</td>", $result['obj_poids']);
                        printf("<td class=\"soustitre2\">%s</td>", $result['gobj_pa_normal']);
                        echo("<td>");
                        echo("</td>");
                        echo("<td>");

                        printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?methode=abandonner&objet=%s\">Abandonner (1PA)</a>", $result['obj_cod']);

                        echo("</td>");
                        echo("</tr>");
                    }
                } else
                {
                    echo("<tr><td colspan=\"6\">Aucune rune</td></tr>");
                }
            }
            //
            //log_debug('Fin runes');
            //
            /*****************************************/
            /* Etape 6 : quete */
            /*****************************************/
            $req            =
                "select sum(obj_poids) as poids from perso_objets,objets,objet_generique  where gobj_tobj_cod in (11,12) and perobj_perso_cod = :perso_cod and perobj_identifie = 'O'  and perobj_obj_cod = obj_cod and obj_gobj_cod = gobj_cod ";
            $stmt           = $pdo->prepare($req);
            $stmt           = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            $poid_catégorie = $stmt->fetch();

            if ($dq == 0)
            {
                $req_matos = "select A.obj_cod, A.obj_nom, A.poids, A.nombre, A.gobj_url ";
                $req_matos = $req_matos . "from ( ";
                $req_matos =
                    $req_matos . "select 1 as obj_cod, obj_nom,sum(obj_poids) as poids,count(*) as nombre,gobj_url ";
                $req_matos = $req_matos . "from perso_objets,objets,objet_generique,type_objet ";
                $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
                $req_matos = $req_matos . "and perobj_identifie = 'O' ";
                $req_matos = $req_matos . "and perobj_equipe = 'N' ";
                $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
                $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod in (11,12) ";
                $req_matos = $req_matos . "and gobj_url is null ";
                $req_matos = $req_matos . "group by obj_nom,gobj_url ";
                $req_matos = $req_matos . "UNION ";
                $req_matos = $req_matos . "select obj_cod, obj_nom, obj_poids as poids, 1 as nombre,gobj_url ";
                $req_matos = $req_matos . "from perso_objets,objets, objet_generique,type_objet ";
                $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
                $req_matos = $req_matos . "and perobj_identifie = 'O' ";
                $req_matos = $req_matos . "and perobj_equipe = 'N' ";
                $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
                $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod in (11,12) ";
                $req_matos = $req_matos . "and gobj_url is not null) A ";
                $req_matos = $req_matos . "order by A.obj_nom ";
                //echo $req_matos;
                $stmt     = $pdo->query($req_matos);
                $nb_matos = $stmt->rowCount();
                echo("<tr>");
                echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Objets de quête <em style=\"font-size: 9px;\">(" . (1 * $poid_catégorie["poids"]) . " Kg)</em> <a class=\"titre\" href=\"inventaire.php?dq=1&dr=$dr&dcompo=$dcompo&dgrisbi=$dgrisbi\">(montrer le détail)</A></div></td>");
                echo("</tr>");
                if ($nb_matos != 0)
                {
                    ?>
                    <tr>
                        <td colspan="2" class="soustitre2">Objet</td>
                        <td class="soustitre2">
                            <div style="text-align:right">Poids</div>
                        </td>
                        <td class="soustitre2">Nombre</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        $examiner = "";
                        if ($result['gobj_url'] != null)
                        {
                            $examiner =
                                " (<a href=\"objets/" . $result['gobj_url'] . "?objet=" . $result['obj_cod'] . " \">Voir le détail</a>) ";

                        }
                        echo("<tr>");
                        echo "<td colspan=\"2\" class=\"soustitre2\"><strong>" . $result['obj_nom'] . $examiner . "</strong></td>";
                        printf("<td class=\"soustitre2\">%s</td>", $result['poids']);
                        printf("<td class=\"soustitre2\">%s</td>", $result['nombre']);
                        echo("<td></td><td></td>");
                        echo("</tr>");

                    }
                } else
                {
                    echo("<tr><td colspan=\"6\">Aucun objet de quête</td></tr>");
                }
            } else
            {
                $req_matos =
                    "select obj_cod,gobj_cod,tobj_libelle,obj_nom,obj_cod,obj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
                $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
                $req_matos = $req_matos . "and perobj_identifie = 'O' ";
                $req_matos = $req_matos . "and perobj_equipe = 'N' ";
                $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
                $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod in (11,12) ";
                $req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
                $stmt      = $pdo->query($req_matos);
                $nb_matos  = $stmt->rowCount();
                echo("<tr>");
                echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Objets de quête <em style=\"font-size: 9px;\">(" . (1 * $poid_catégorie["poids"]) . " Kg)</em> <a class=\"titre\" href=\"inventaire.php?dq=0&dr=$dr&dcompo=$dcompo&dgrisbi=$dgrisbi\">(cacher le détail)</A></div></td>");
                echo("</tr>");
                if ($nb_matos != 0)
                {
                    ?>
                    <tr>
                        <td class="soustitre2">Type</td>
                        <td class="soustitre2">Objet</td>
                        <td class="soustitre2">
                            <div style="text-align:right">Poids</div>
                        </td>
                        <td class="soustitre2">PA/att.</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        echo("<tr>");
                        printf("<td class=\"soustitre2\">%s</td>", $result['tobj_libelle']);
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet3.php?objet=" . $result['obj_cod'] . "&origine=i\">" . $result['obj_nom'] . "</a></td>";
                        printf("<td class=\"soustitre2\">%s</td>", $result['obj_poids']);
                        printf("<td class=\"soustitre2\">%s</td>", $result['gobj_pa_normal']);
                        echo("<td>");
                        echo("</td>");
                        echo("<td>");

                        printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?methode=abandonner&objet=%s\">Abandonner (1PA)</a>", $result['obj_cod']);

                        echo("</td>");
                        echo("</tr>");
                    }
                } else
                {
                    echo("<tr><td colspan=\"6\">Aucun objet de quête</td></tr>");
                }
            }
            //
            //log_debug('Fin quetes');
            //
            /*****************************************/
            /* Etape 7 : poissons */
            /*****************************************/
            /*
            $req_matos = "select obj_cod,gobj_cod,tobj_libelle,gobj_nom,obj_cod,gobj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
            $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
            $req_matos = $req_matos . "and perobj_identifie = 'O' ";
            $req_matos = $req_matos . "and perobj_equipe = 'N' ";
            $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
            $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
            $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
            $req_matos = $req_matos . "and gobj_tobj_cod = 14 ";
            $req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
            $stmt = $pdo->query($req_matos);
            $nb_matos = $stmt->rowCount();
            if ($nb_matos != 0)
            {
                ?>
                <p class="titre">Poissons</p>
                <div class="centrer"><table>
                <tr>
                <td class="soustitre2"><p>Objet</p></td>
                <td class="soustitre2"><p style="text-align:right">Poids</p></td>
                <td></td>
                </tr>
                <?
                while($result = $stmt->fetch())
                {
                    ?>
                    <tr>
                    <td class="soustitre2"><p><a href="visu_desc_objet2.php?objet=<?=$result['gobj_cod'];?>&origine=i\"><?=$result['gobj_nom'];?></p></td>
                    <td class="soustitre2"><?=$result['gobj_poids'];?></td>
                    <td><p><a href="donne_poisson.php?obj=<?=$result['obj_cod'];?>">Donner le poisson ? (1 PA)</a></td>
                    </tr>
                    <?
                }
                ?>
                </table></div>
                <?
            }
            */
            /*****************************************/
            /* Etape 9 : Les composants de potion		 */
            /*****************************************/
            $req            =
                "select sum(obj_poids) as poids from perso_objets,objets,objet_generique  where gobj_tobj_cod in (22, 28, 30,34) and perobj_perso_cod = :perso_cod and perobj_identifie = 'O'  and perobj_obj_cod = obj_cod and obj_gobj_cod = gobj_cod ";
            $stmt           = $pdo->prepare($req);
            $stmt           = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            $poid_catégorie = $stmt->fetch();
            if ($dcompo == 0)
            {
                $req_matos =
                    "select obj_nom,sum(obj_poids) as poids,count(*) as nombre,gobj_url from perso_objets,objets,objet_generique,type_objet ";
                $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
                $req_matos = $req_matos . "and perobj_identifie = 'O' ";
                $req_matos = $req_matos . "and perobj_equipe = 'N' ";
                $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
                $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
                $req_matos =
                    $req_matos . "and (gobj_tobj_cod = 22 or gobj_tobj_cod = 28 or gobj_tobj_cod = 30 or gobj_tobj_cod = 34)";
                $req_matos = $req_matos . "group by obj_nom,gobj_url ";
                $stmt      = $pdo->query($req_matos);
                $nb_matos  = $stmt->rowCount();
                if ($nb_matos != 0)
                {
                    echo("<tr>");
                    echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Composants d'alchimie <em style=\"font-size: 9px;\">(" . (1 * $poid_catégorie["poids"]) . " Kg)</em> <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=$dr&dcompo=1&dgrisbi=$dgrisbi\">(montrer le détail)</A></div></td>");
                    echo("</tr>");

                    ?>
                    <tr>
                        <td colspan="2" class="soustitre2">Objet</td>
                        <td class="soustitre2">
                            <div style="text-align:right">Poids</div>
                        </td>
                        <td class="soustitre2">Nombre</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        $examiner = "";
                        if ($result['gobj_url'] != null)
                        {
                            $examiner = " (<a href=\"objets/" . $result['gobj_url'] . "\">Voir le détail</a>) ";
                        }
                        echo("<tr>");
                        echo "<td colspan=\"2\" class=\"soustitre2\"><strong>" . $result['obj_nom'] . $examiner . "</strong></td>";
                        printf("<td class=\"soustitre2\">%s</td>", $result['poids']);
                        printf("<td class=\"soustitre2\">%s</td>", $result['nombre']);
                        echo("<td></td><td></td>");
                        echo("</tr>");

                    }

                }
            } else
            {
                $req_matos =
                    "select obj_cod,gobj_cod,tobj_libelle,obj_nom,obj_cod,obj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
                $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
                $req_matos = $req_matos . "and perobj_identifie = 'O' ";
                $req_matos = $req_matos . "and perobj_equipe = 'N' ";
                $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
                $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
                $req_matos =
                    $req_matos . "and (gobj_tobj_cod = 22 or gobj_tobj_cod = 28 or gobj_tobj_cod = 30 or gobj_tobj_cod = 34)";
                $req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
                $stmt      = $pdo->query($req_matos);
                $nb_matos  = $stmt->rowCount();
                echo("<tr>");
                echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Composants d'alchimie <em style=\"font-size: 9px;\">(" . (1 * $poid_catégorie["poids"]) . " Kg)</em> <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=$dr&dcompo=0&dgrisbi=$dgrisbi\">(cacher le détail)</A></div></td>");
                echo("</tr>");
                if ($nb_matos != 0)
                {
                    ?>
                    <tr>
                        <td class="soustitre2">Type</td>
                        <td class="soustitre2">Objet</td>
                        <td class="soustitre2">
                            <div style="text-align:right">Poids</div>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        echo("<tr>");
                        printf("<td class=\"soustitre2\">%s</td>", $result['tobj_libelle']);
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet2.php?objet=" . $result['gobj_cod'] . "&origine=i\">" . $result['obj_nom'] . "</a></td>";
                        printf("<td class=\"soustitre2\">%s</td>", $result['obj_poids']);
                        echo("<td>");
                        echo("</td>");
                        echo("<td>");

                        printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?methode=abandonner&objet=%s\">Abandonner (1PA)</a>", $result['obj_cod']);

                        echo("</td>");
                        echo("</tr>");
                    }
                } else
                {
                    echo("<tr><td colspan=\"6\">Aucun composant pour potion</td></tr>");
                }
            }
            /*****************************************/
            /* Etape 10 : Le grisbi                  */
            /*****************************************/
            $req            =
                "select sum(obj_poids) as poids from perso_objets,objets,objet_generique  where gobj_tobj_cod =42 and perobj_perso_cod = :perso_cod and perobj_identifie = 'O'  and perobj_obj_cod = obj_cod and obj_gobj_cod = gobj_cod ";
            $stmt           = $pdo->prepare($req);
            $stmt           = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            $poid_catégorie = $stmt->fetch();
            if ($dgrisbi == 0)
            {
                $req_matos =
                    "select obj_nom,sum(obj_poids) as poids,count(*) as nombre,gobj_url from perso_objets,objets,objet_generique,type_objet ";
                $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
                $req_matos = $req_matos . "and perobj_identifie = 'O' ";
                $req_matos = $req_matos . "and perobj_equipe = 'N' ";
                $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
                $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
                $req_matos = $req_matos . "and (gobj_tobj_cod = 42)";
                $req_matos = $req_matos . "group by obj_nom,gobj_url ";
                $stmt      = $pdo->query($req_matos);
                $nb_matos  = $stmt->rowCount();
                if ($nb_matos != 0)
                {
                    echo("<tr>");
                    echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Monnaie d'échange <em style=\"font-size: 9px;\">(" . (1 * $poid_catégorie["poids"]) . " Kg)</em> <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=$dr&dcompo=$dcompo&dgrisbi=1\">(montrer le détail)</A></div></td>");
                    echo("</tr>");

                    ?>
                    <tr>
                        <td colspan="2" class="soustitre2">Objet</td>
                        <td class="soustitre2">
                            <div style="text-align:right">Poids</div>
                        </td>
                        <td class="soustitre2">Nombre</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        $examiner = "";
                        if ($result['gobj_url'] != null)
                        {
                            $examiner = " (<a href=\"objets/" . $result['gobj_url'] . "\">Voir le détail</a>) ";
                        }
                        echo("<tr>");
                        echo "<td colspan=\"2\" class=\"soustitre2\"><strong>" . $result['obj_nom'] . $examiner . "</strong></td>";
                        printf("<td class=\"soustitre2\">%s</td>", $result['poids']);
                        printf("<td class=\"soustitre2\">%s</td>", $result['nombre']);
                        echo("<td></td><td></td>");
                        echo("</tr>");

                    }

                }
            } else
            {
                $req_matos =
                    "select obj_cod,gobj_cod,tobj_libelle,obj_nom,obj_cod,obj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
                $req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
                $req_matos = $req_matos . "and perobj_identifie = 'O' ";
                $req_matos = $req_matos . "and perobj_equipe = 'N' ";
                $req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
                $req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
                $req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
                $req_matos = $req_matos . "and (gobj_tobj_cod = 42)";
                $req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
                $stmt      = $pdo->query($req_matos);
                $nb_matos  = $stmt->rowCount();
                echo("<tr>");
                echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Monnaie d'échange <em style=\"font-size: 9px;\">(" . (1 * $poid_catégorie["poids"]) . " Kg)</em> <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=$dr&dcompo=$dcompo&dgrisbi=0\">(cacher le détail)</A></div></td>");
                echo("</tr>");
                if ($nb_matos != 0)
                {
                    ?>
                    <tr>
                        <td class="soustitre2">Type</td>
                        <td class="soustitre2">Objet</td>
                        <td class="soustitre2">
                            <div style="text-align:right">Poids</div>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        echo("<tr>");
                        printf("<td class=\"soustitre2\">%s</td>", $result['tobj_libelle']);
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet2.php?objet=" . $result['gobj_cod'] . "&origine=i\">" . $result['obj_nom'] . "</a></td>";
                        printf("<td class=\"soustitre2\">%s</td>", $result['obj_poids']);
                        echo("<td>");
                        echo("</td>");
                        echo("<td>");

                        printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?methode=abandonner&objet=%s\">Abandonner (1PA)</a>", $result['obj_cod']);

                        echo("</td>");
                        echo("</tr>");
                    }
                } else
                {
                    echo("<tr><td colspan=\"6\">Aucun composant pour potion</td></tr>");
                }
            }
            ?>
        </table>
    </div>
<?php


$contenu_page = ob_get_contents();
ob_end_clean();

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');


$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'PERSO'        => $perso,
    'PHP_SELF'     => $_SERVER['PHP_SELF'],
    'CONTENU_PAGE' => $contenu_page

);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));
