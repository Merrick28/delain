<?php
include "blocks/_header_page_jeu.php";
$param                    = new parametres();
//
//Contenu de la div de droite
//
ob_start();
?>
    <script>
        function valActionAdd(pcod, pcod2) {
            document.action_aut.aut_add.value = document.action_aut.aut_add.value + pcod + "#" + pcod2 + ";";
        }

        function valActionSup(pcod, pcod2) {
            document.action_aut.aut_sup.value = document.action_aut.aut_sup.value + pcod + "#" + pcod2 + ";";
        }

        function toutCocherligne(formulaire, nom) {
            for (i = 0; i < formulaire.elements.length; ++i) {
                if (formulaire.elements[i].name.substring(0, nom.length) == nom) {
                    formulaire.elements[i].checked = !formulaire.elements[i].checked;
                    if (formulaire.elements[i].checked == 1) {
                        document.action_aut.aut_add.value = document.action_aut.aut_add.value + formulaire.elements[i].value + ";";
                    } else {
                        document.action_aut.aut_sup.value = document.action_aut.aut_sup.value + formulaire.elements[i].value + ";";
                    }
                }
            }
        }

        function toutCochercolonne(formulaire, nom) {
            for (i = 0; i < formulaire.elements.length; ++i) {
                if (formulaire.elements[i].id.substring(0, nom.length) == nom) {
                    formulaire.elements[i].checked = !formulaire.elements[i].checked;
                    if (formulaire.elements[i].checked == 1) {
                        document.action_aut.aut_add.value = document.action_aut.aut_add.value + formulaire.elements[i].value + ";";
                    } else {
                        document.action_aut.aut_sup.value = document.action_aut.aut_sup.value + formulaire.elements[i].value + ";";
                    }
                }
            }
        }
    </script>
<?php
$methode           = get_request_var('methode', 'debut');
define('APPEL', 1);
include "blocks/_test_admin_echoppe.php";
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            echo "<p><a href=\"gerant_echoppe.php\">Affecter les gérances des échoppes</a>";
            echo "<p><a href=\"admin_echoppe_tarif.php\">Modifier les prix génériques</a>";
            echo "<p><a href=\"admin_echoppe_guilde.php\">Modifier les remises de guilde</a>";
            echo "<p><a href=\"admin_echoppe_stats.php\">Voir les stats de vente des magasins</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=guilde&met_guilde=debut\">Gérer les meta-guilde</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=voir_meta\">Voir les meta guildés</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=stats_biere\">Voir les stats sur les futs de bière</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=stats_paq\">Voir les stats sur les paquets bruns</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=mag_aut\">Gestion des autorisations de ventes des objets par échoppe</a>";
            break;
        case "guilde":
            // champ générique pour ren=prendre sur les autres pages
            $champ = 'guilde_meta_caravane';
            $champ_perso  = 'pguilde_meta_caravane';
            switch ($_REQUEST['met_guilde'])
            {
                case "debut":
                    require "_admin_echoppe_met_guilde.php";

                    break;
                case "suite":
                    require "_admin_echoppe_suite.php";
                    break;
            }
            break;
        case "voir_meta":
            $champ_perso  = 'pguilde_meta_caravane';
            require "_admin_echoppe_voir_meta.php";
            break;
        case "stats_biere":
            echo "<p>Futs vendus par les postes d'entrée aux aventuriers (ce mois-ci/total) : <strong>", $param->getparm(77), "/", $param->getparm(76), "</strong>";
            echo "<p>Futs vendus par les aventuriers (ce mois-ci/total) : <strong>", $param->getparm(79), "/", $param->getparm(78), "</strong>";
            break;
        case "stats_paq":
            echo "<p>Paquets vendus par les postes d'entrée aux aventuriers (ce mois-ci/total) : <strong>", $param->getparm(81), "/", $param->getparm(80), "</strong>";
            echo "<p>Paquets vendus par les aventuriers (ce mois-ci/total) : <strong>", $param->getparm(84), "/", $param->getparm(83), "</strong>";
            break;
        case "mag_aut":
            if ($_REQUEST['lieu_cod'] != "")
            {
                $var_lieu = "and lieu_cod = " . $lieu_cod;
            } else
            {
                $var_lieu = "";
            }

            echo "<p><strong>Liste des autorisations de ventes pour les différents magasins :</strong><br>";
            echo '<br>En cliquant sur le nom du magasin, toute la colonne sera sélectionnée (si un objet était déjà sélectionné, cela va le supprimer
			<br>En cliquant sur un objet, on le rendra disponible en approvisionnement dans tous les magasins (sauf si il était déjà sélectionné et dans ce cas, il deviendra non disponible pour ce magasin)
			<br><br>methode : ' . $methode . ' Lieu = ' . $var_lieu . ' / ' . $lieu . ' / ' . $lieu_cod . '<br>
			<form name="action_aut" method="post" action="' . $_SERVER['PHP_SELF'] . '">
						<input type="hidden" name="methode" value="modif_aut">
						<input type="hidden" name="aut_add" value="">
						<input type="hidden" name="aut_sup" value="">
						<input type="submit" value="Valider les modifications">';
            $req  = "select lieu_nom,etage_libelle,lieu_cod 
                from lieu,lieu_position,positions,etage 
			    where lieu_tlieu_cod = 11 
			      and lieu_cod = lpos_lieu_cod 
			      and lpos_pos_cod = pos_cod 
			      and pos_etage = etage_numero 
                order by lieu_cod";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $ch .= '<option value="' . $result['lieu_cod'] . '">' . $result['lieu_nom'] . ' / ' . $result['etage_libelle'] . '</option>';
            }
            $req  = "select lieu_nom,etage_libelle,lieu_cod 
                from lieu,lieu_position,positions,etage 
			    where lieu_tlieu_cod = 11 
			      and lieu_cod = lpos_lieu_cod 
			      and lpos_pos_cod = pos_cod 
			      and pos_etage = etage_numero " . $var_lieu . " order by lieu_cod";
            $stmt = $pdo->query($req);
            $cpt  = 0;
            while ($result = $stmt->fetch())
            {
                $lieu .= '<td class="soustitre2">' . $result['etage_libelle'] . '<br><br><a href="javascript:toutCochercolonne(document.action_aut,\'ID_' . $result['lieu_cod'] . '\')"><strong>' . $result['lieu_nom'] . '</strong></a></td>';
            }

            //A revoir pour le onchange ...
            echo '<br><select name="lieu" onChange="window.location.href=\'' . $_SERVER['PHP_SELF'] . '?methode=mag_aut&lieu_cod=\'+this.value;"> 
			<option value="">---------------</option>
			<option value=""><strong>Tous les magasins</strong></option>';
            echo $ch;
            echo '</select><br>';

            $req  = "select lieu_cod,lieu_nom,gobj_cod,gobj_nom,mgaut_gobj_cod,mgaut_lieu_cod 
                from objet_generique 
				right outer join lieu on (lieu_tlieu_cod = 11)
				left outer join stock_magasin_autorisations on (gobj_cod = mgaut_gobj_cod and mgaut_lieu_cod = lieu_cod) 
				where gobj_tobj_cod in (1,2,4,25) and gobj_deposable = 'O' and gobj_echoppe_stock = 'O' " . $var_lieu . " order by gobj_tobj_cod,gobj_nom,lieu_cod";
            $stmt = $pdo->query($req);

            echo "<table cellspacing=\"2\" cellpadding=\"2\"><tr><td></td>" . $lieu;
            $objet = 0;
            while ($result = $stmt->fetch())
            {
                if ($result['gobj_cod'] != $objet)
                {
                    echo '</tr><tr><td><a href="javascript:toutCocherligne(document.action_aut,\'DISPO_PUBLIC_' . $result['gobj_cod'] . '\')">' . $result['gobj_nom'] . '</a></td>';
                }
                if ($result['mgaut_gobj_cod'] != Null)
                {
                    $checked_aut = "checked";
                    $clic        = 'onclick="valActionSup(' . $result['gobj_cod'] . ',' . $result['lieu_cod'] . ')"';
                } else
                {
                    $checked_aut = "";
                    $clic        = 'onclick="valActionAdd(' . $result['gobj_cod'] . ',' . $result['lieu_cod'] . ')"';
                }

                echo '<td>
					<input type="checkbox" ' . $clic . ' id="ID_' . $result['lieu_cod'] . '[' . $result['gobj_cod'] . ']" name="DISPO_PUBLIC_' . $result['gobj_cod'] . '[' . $result['lieu_cod'] . ']" ' . $checked_aut . ' value="' . $result['gobj_cod'] . '#' . $result['lieu_cod'] . '">
					</td>';
                $objet = $result['gobj_cod'];
                $cpt++;
            }
            echo '</tr></table>
						</form><br><br>';
            break;
        case "modif_aut":
            $list = explode(";", $_POST['aut_add']);
            for ($i = 0; $i < count($list); $i++)
            {
                $temp_aut     = $list[$i];
                $temp_aut_add = explode("#", $temp_aut);
                $lieu         = $temp_aut_add[1];
                $objet        = $temp_aut_add[0];
                if ($temp_aut)
                {
                    $req  =
                        "select * from stock_magasin_autorisations 
                        where mgaut_lieu_cod = :lieu
                        and mgaut_gobj_cod = :objet";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":lieu"  => $lieu,
                                                ":objet" => $objet), $stmt);
                    if (!$result = $stmt->fetch())
                    {
                        $req  = "insert into stock_magasin_autorisations (mgaut_lieu_cod,mgaut_gobj_cod) 
																values (:lieu,:objet)";
                        $stmt = $pdo->prepare($req);
                        $stmt = $pdo->execute(array(":lieu"  => $lieu,
                                                    ":objet" => $objet), $stmt);
                    }
                }
            }
            $list = explode(";", $_POST['aut_sup']);
            for ($i = 0; $i < count($list); $i++)
            {
                $temp_aut     = $list[$i];
                $temp_aut_add = explode("#", $temp_aut);
                $lieu         = $temp_aut_add[1];
                $objet        = $temp_aut_add[0];
                if ($temp_aut)
                {
                    $req  =
                        "delete from stock_magasin_autorisations 
                        where mgaut_lieu_cod = :lieu
                        and mgaut_gobj_cod = :objet";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":lieu"  => $lieu,
                                                ":objet" => $objet), $stmt);
                }
            }
            echo "<br>modifications bien réalisées<br>
				<br><a href=\"", $_SERVER['PHP_SELF'], "?methode=mag_aut\"><strong>Retour</strong></a>";
            break;
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
