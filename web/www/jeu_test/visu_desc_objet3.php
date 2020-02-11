<?php
include "blocks/_header_page_jeu.php";
ob_start();
$objet = $_GET['objet'];
if (!preg_match('/^[0-9]*$/i', $objet)) {
    echo "<p>Anomalie sur numéro objet !</p>";
    exit();
}
$autorise = 0;
// on regarde si l'objet est dans l'inventaire et identifié
$req = "select perobj_cod from perso_objets
	where perobj_perso_cod = $perso_cod
	and perobj_obj_cod = $objet
	and perobj_identifie = 'O' ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() != 0)
    $autorise = 1;
// on regarde si l'objet est dans une échoppe sur laquelle on est

if ($db->is_lieu($perso_cod)) {
    $tab_lieu = $db->get_lieu($perso_cod);
    $lieu_cod = $tab_lieu['lieu_cod'];
    $req = "select mstock_obj_cod from stock_magasin
		where mstock_lieu_cod = $lieu_cod
		and mstock_obj_cod = $objet";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
        $autorise = 1;
}
if ($autorise == 1) {
    // on prend les valeurs de force et dex du perso pour la suite
    $req = "select perso_for,perso_dex, perso_niveau from perso where perso_cod = $perso_cod ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $force = $result['perso_for'];
    $dex = $result['perso_dex'];
    $niveau_perso = $result['perso_niveau'];
    $req = "select obj_nom, gobj_tobj_cod, tobj_libelle, obj_poids, gobj_pa_normal, gobj_pa_eclair, gobj_distance, gobj_deposable,
			gobj_comp_cod, obj_description, coalesce(obj_seuil_force, 0) as obj_seuil_force, obj_seuil_dex,
			coalesce(obj_bonus_vue, 0) as obj_bonus_vue, coalesce(obj_critique, 0) as obj_critique, obj_armure,
			coalesce(obj_vampire, 0) as obj_vampire, coalesce(obj_aura_feu, 0) as obj_aura_feu, obj_enchantable, obj_poison, obj_regen,
			gobj_image, obj_des_degats, obj_val_des_degats, obj_bonus_degats, obj_niveau_min, 
			coalesce(obj_chute, 0) as obj_chute, coalesce(obj_portee, 0) as obj_portee
		from objets
		inner join objet_generique on gobj_cod = obj_gobj_cod
		inner join type_objet on tobj_cod = gobj_tobj_cod
		where obj_cod = $objet 
			and (gobj_visible is null or gobj_visible != 'N') ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0) {
        $result = $stmt->fetch();
        $seuil_for = $result['obj_seuil_force'];
        $seuil_dex = $result['obj_seuil_dex'];
        $vampire = $result['obj_vampire'];
        $aura_feu = $result['obj_aura_feu'];
        $armure = $result['obj_armure'];
        $regen = $result['obj_regen'];
        $poison = $result['obj_poison'];
        $enchantable = $result['obj_enchantable'];
        $niveau_min = $result['obj_niveau_min'];
        $Recup_image = $result['gobj_image'];
        $image = 'http://www.jdr-delain.net/images/' . $Recup_image;
        $t_etat = 0;
        $comp = $result['gobj_comp_cod'];
        $desc = $result['obj_description'];
        echo "<p class=\"titre\">" . $result['obj_nom'] . "</p>";
        echo "<center><table>";

        echo "<tr>";
        echo "<tr><td>";
        if ($Recup_image != "")
            echo "<img class=\"img_descr\" src='$image' />";
        echo "</td></tr>";
        echo "<td class=\"soustitre2\">Type d’objet</td>";
        echo "<td>" . $result['tobj_libelle'];
        if ($result['gobj_deposable'] == 'N') {
            echo " <strong>non déposable !</strong>";
        }
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td class=\"soustitre2\">Poids</td>";
        echo "<td>" . $result['obj_poids'] . "</td>";
        echo "</tr>";

        if ($result['obj_bonus_vue'] != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Modificateur de vue</td>";
            echo "<td>" . $result['obj_bonus_vue'] . "</td>";
            echo "</tr>";
        }
        if ($result['obj_critique'] != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Protection contre les critiques / spéciaux</td>";
            echo "<td>" . $result['obj_critique'] . " %</td>";
            echo "</tr>";
        }
        if ($result['gobj_tobj_cod'] == 1) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Coût en PA pour une attaque normale</td>";
            echo "<td>" . $result['gobj_pa_normal'] . "</td>";
            echo "</tr>";

            if ($result['gobj_distance'] == 'N') {
                echo "<tr>";
                echo "<td class=\"soustitre2\">Coût en PA pour une attaque foudroyante</td>";
                echo "<td>" . $result['gobj_pa_eclair'] . "</td>";
                echo "</tr>";
            }

            echo "<tr>";
            echo "<td class=\"soustitre2\">Dégâts</td>";
            echo "<td>" . $result['obj_des_degats'] . "D" . $result['obj_val_des_degats'] . "+" . $result['obj_bonus_degats'] . "</td>";
            echo "</tr>";
            if ($seuil_dex != 0) {
                $cpl_class = '';
                if ($dex < ($seuil_dex - 3))
                    $cpl_class = '_rouge';
                else if ($dex < $seuil_dex)
                    $cpl_class = '_orange';
                else if ($dex > ($seuil_dex + 3))
                    $cpl_class = '_vert';
                echo "<tr>";
                echo '<td class="soustitre2' . $cpl_class . '">Seuil d’efficacité en dextérité</td>';
                echo "<td>" . $seuil_dex . "</td>";
                echo "</tr>";
            }
            if ($seuil_for != 0) {
                $cpl_class = '';
                if ($force < ($seuil_for - 3))
                    $cpl_class = '_rouge';
                else if ($force < $seuil_for)
                    $cpl_class = '_orange';
                else if ($force > ($seuil_for + 3))
                    $cpl_class = '_vert';
                echo "<tr>";
                echo '<td class="soustitre2' . $cpl_class . '">Seuil d’efficacité en force</td>';
                echo "<td>" . $seuil_for . "</td>";
                echo "</tr>";
            }
            if ($niveau_min > 0) {
                $cpl_class = '';
                if ($niveau_perso < $niveau_min)
                    $cpl_class = '_rouge';
                else
                    $cpl_class = '_vert';
                echo "<tr>";
                echo '<td class="soustitre2' . $cpl_class . '">Niveau minimum pour équiper</td>';
                echo "<td>" . $niveau_min . "</td>";
                echo "</tr>";
            }

            if ($result['gobj_distance'] == 'O') {
                echo "<tr>";
                echo "<td class=\"soustitre2\">Portée </td>";
                echo "<td>" . $result['obj_portee'] . "</td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td class=\"soustitre2\">Chute </td>";
                echo "<td>" . $result['obj_chute'] . "</td>";
                echo "</tr>";
            }

            $req = "select comp_libelle from competences where comp_cod = $comp ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<tr>";
            echo "<td class=\"soustitre2\">Compétence utilisée</td>";
            echo "<td>" . $result['comp_libelle'] . "</td>";
            echo "</tr>";
        }
        if ($armure != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Armure</td>";
            echo "<td>" . $armure . "</td>";
            echo "</tr>";
        }
        if ($vampire != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Vampirisme</td>";
            echo "<td>" . $vampire * 100 . " %</td>";
            echo "</tr>";
        }
        if ($aura_feu != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Aura de feu</td>";
            echo "<td>" . $aura_feu * 100 . " %</td>";
            echo "</tr>";
        }
        if ($regen != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Bonus à la régénération</td>";
            echo "<td>" . $regen . " à l’initialisation de DLT</td>";
            echo "</tr>";
        }
        if ($poison != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Dégâts infligés par poison</td>";
            echo "<td>" . $poison . " à l’initialisation de DLT de la victime</td>";
            echo "</tr>";
        }
        if ($enchantable == 1) {
            echo "<tr>";
            echo "<td colspan=\"2\" class=\"soustitre2\"><strong>Objet enchantable !</strong></td>";
            echo "</tr>";
        }
        if ($enchantable == 2) {
            echo "<tr>";
            echo "<td colspan=\"2\" class=\"soustitre2\"><strong>Objet enchanté !</strong></td>";
            echo "</tr>";
        }

        //2019-01-07@Marlyza : Gestion des objets avec des sorts rattachés
        // Il faudrait passer toute la page en PDO, mais en attendant faisons en sorte que les nouveautées le soient déjà!
        $obj = new objets();
        $obj->charge($objet);
        if ($sorts_attaches = $obj->get_sorts_attaches())
        {
            echo "<tr>";
            echo "<td colspan=\"2\" class=\"soustitre2\"><strong>L'objet est magique, liste des sorts qu'il vous permet de lancer:</strong></td>";
            echo "</tr>";
            foreach ($sorts_attaches as $objsort)
            {
                echo "<tr><td class=\"soustitre2\">".($objsort->objsort_equip_requis ? "Equipé" : "Inventaire")."</td>";
                echo "<td><strong>". $objsort->getNom()."</strong> <em>(". $objsort->getCout()." PA)</em></td>";
                echo "<tr>";
            }
        }

        if ($bm_attaches = $obj->get_bm_attaches())
        {
            echo "<tr>";
            echo "<td colspan=\"2\" class=\"soustitre2\"><strong>L'objet assene des bonus/malus:</strong></td>";
            echo "</tr>";
            foreach ($bm_attaches as $objbm)
            {
                $tbonus = new bonus_type();
                $tbonus->charge($objbm->objbm_tbonus_cod);
                $typebm = (($tbonus->tbonus_gentil_positif=="t" && $objbm->objbm_bonus_valeur>0)||($tbonus->tbonus_gentil_positif!="t" && $objbm->objbm_bonus_valeur<0)) ? "Bonus" : "Malus" ;
                if (is_file(__DIR__ . "/../images/interface/bonus/".$tbonus->tbonus_libc.".png"))
                {
                    $img = '<img src="/../images/interface/bonus/'.$tbonus->tbonus_libc.'.png">';
                }
                else
                {
                    $img = '<img src="/../images/interface/bonus/'.strtoupper($typebm).'.png">';
                }

                echo "<tr><td class=\"soustitre2\">".($typebm=="Bonus" ? "<strong style='color:darkblue;'>Bonus</strong>" : "<strong style='color:#800000;'>Malus</strong>")."</td>";
                echo "<td>".$img." <strong>".($objbm->objbm_bonus_valeur>0 ? "+" : "").$objbm->objbm_bonus_valeur."</strong> : ". $tbonus->tonbus_libelle."</td>";
                echo "<tr>";
            }
        }

        if ($objelem_attaches = $obj->get_condition_equipement())
        {
            echo "<tr>";
            echo "<td colspan=\"2\" class=\"soustitre2\"><strong>L'objet possède des conditions pour l'équiper:</strong></td>";
            echo "</tr>";
            foreach ($objelem_attaches as $objelem)
            {
                $carac = new aquete_type_carac();
                $carac->charge($objelem->objelem_misc_cod);
                $conj = $objelem->objelem_param_num_1 == 0 ? "ET" : "OU" ;

                $aff = $carac->element_language_humain($objelem);

                echo "<tr><td class=\"soustitre2\">{$conj}</td>";
                echo "<td>{$aff}</td>";
                echo "<tr>";
            }
            if ($obj->est_equipable($perso_cod)) {
                echo "<tr><td colspan=\"2\" class=\"soustitre2\">Vous remplissez les conditions pour équiper cet objet</td></tr>";
            }
            else{
                echo "<tr><td colspan=\"2\" class=\"soustitre2\"><strong>Vous ne pouvez pas équiper cet objet</strong></td></tr>";
            }
        }


        echo "<tr>";
        echo "<td class=\"soustitre2\">Description</td>";
        echo "<td>" . $desc . "</td>";
        echo "</tr>";

        if (isset($bon)) {
            $req = "select obon_libelle from bonus_objets where obon_cod = $bon ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<tr>";
            echo "<td class=\"soustitre2\">Bonus</td>";
            echo "<td>" . $result['obon_libelle'] . "</td>";
            echo "</tr>";
        }

        echo "</table></center>";
    } else {
        echo "Aucun objet trouvé !";
    }
} else {
    echo "Vous n’avez pas accès au détail de cet objet !";
}
$retour = "inventaire.php";
if ($origine == 'e') {
    $retour = "lieu.php?methode=acheter";
}
if ($origine == 'a') {
    $retour = "admin_echoppe_tarif.php";
}
echo "<p style=\"text-align:center;\"><a href=\"$retour\">Retour !</a></p>";
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
