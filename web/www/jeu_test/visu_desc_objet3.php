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
$db->query($req);
if ($db->nf() != 0)
    $autorise = 1;
// on regarde si l'objet est dans une échoppe sur laquelle on est

if ($db->is_lieu($perso_cod)) {
    $tab_lieu = $db->get_lieu($perso_cod);
    $lieu_cod = $tab_lieu['lieu_cod'];
    $req = "select mstock_obj_cod from stock_magasin
		where mstock_lieu_cod = $lieu_cod
		and mstock_obj_cod = $objet";
    $db->query($req);
    if ($db->nf() != 0)
        $autorise = 1;
}
if ($autorise == 1) {
    // on prend les valeurs de force et dex du perso pour la suite
    $req = "select perso_for,perso_dex, perso_niveau from perso where perso_cod = $perso_cod ";
    $db->query($req);
    $db->next_record();
    $force = $db->f("perso_for");
    $dex = $db->f("perso_dex");
    $niveau_perso = $db->f("perso_niveau");
    $req = "select obj_nom, gobj_tobj_cod, tobj_libelle, obj_poids, gobj_pa_normal, gobj_pa_eclair, gobj_distance, gobj_deposable,
			gobj_comp_cod, obj_description, coalesce(obj_seuil_force, 0) as obj_seuil_force, obj_seuil_dex,
			coalesce(obj_bonus_vue, 0) as obj_bonus_vue, coalesce(obj_critique, 0) as obj_critique, obj_armure,
			coalesce(obj_vampire, 0) as obj_vampire, coalesce(obj_aura_feu, 0) as obj_aura_feu, obj_enchantable, obj_poison, obj_regen,
			gobj_image, obj_des_degats, obj_val_des_degats, obj_bonus_degats, obj_niveau_min
		from objets
		inner join objet_generique on gobj_cod = obj_gobj_cod
		inner join type_objet on tobj_cod = gobj_tobj_cod
		where obj_cod = $objet 
			and (gobj_visible is null or gobj_visible != 'N') ";
    $db->query($req);
    if ($db->nf() != 0) {
        $db->next_record();
        $seuil_for = $db->f("obj_seuil_force");
        $seuil_dex = $db->f("obj_seuil_dex");
        $vampire = $db->f("obj_vampire");
        $aura_feu = $db->f("obj_aura_feu");
        $armure = $db->f("obj_armure");
        $regen = $db->f("obj_regen");
        $poison = $db->f("obj_poison");
        $enchantable = $db->f("obj_enchantable");
        $niveau_min = $db->f("obj_niveau_min");
        $Recup_image = $db->f("gobj_image");
        $image = 'http://www.jdr-delain.net/images/' . $Recup_image;
        $t_etat = 0;
        $comp = $db->f("gobj_comp_cod");
        $desc = $db->f("obj_description");
        echo "<p class=\"titre\">" . $db->f("obj_nom") . "</p>";
        echo "<center><table>";

        echo "<tr>";
        echo "<tr><td>";
        if ($Recup_image != "")
            echo "<img class=\"img_descr\" src='$image' />";
        echo "</td></tr>";
        echo "<td class=\"soustitre2\">Type d’objet</td>";
        echo "<td>" . $db->f("tobj_libelle");
        if ($db->f("gobj_deposable") == 'N') {
            echo " <strong>non déposable !</strong>";
        }
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td class=\"soustitre2\">Poids</td>";
        echo "<td>" . $db->f("obj_poids") . "</td>";
        echo "</tr>";

        if ($db->f("obj_bonus_vue") != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Modificateur de vue</td>";
            echo "<td>" . $db->f("obj_bonus_vue") . "</td>";
            echo "</tr>";
        }
        if ($db->f("obj_critique") != 0) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Protection contre les critiques / spéciaux</td>";
            echo "<td>" . $db->f("obj_critique") . " %</td>";
            echo "</tr>";
        }
        if ($db->f("gobj_tobj_cod") == 1) {
            echo "<tr>";
            echo "<td class=\"soustitre2\">Coût en PA pour une attaque normale</td>";
            echo "<td>" . $db->f("gobj_pa_normal") . "</td>";
            echo "</tr>";

            if ($db->f("gobj_distance") == 'N') {
                echo "<tr>";
                echo "<td class=\"soustitre2\">Coût en PA pour une attaque foudroyante</td>";
                echo "<td>" . $db->f("gobj_pa_eclair") . "</td>";
                echo "</tr>";
            }

            echo "<tr>";
            echo "<td class=\"soustitre2\">Dégâts</td>";
            echo "<td>" . $db->f("obj_des_degats") . "D" . $db->f("obj_val_des_degats") . "+" . $db->f("obj_bonus_degats") . "</td>";
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


            $req = "select comp_libelle from competences where comp_cod = $comp ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\">Compétence utilisée</td>";
            echo "<td>" . $db->f("comp_libelle") . "</td>";
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


        echo "<tr>";
        echo "<td class=\"soustitre2\">Description</td>";
        echo "<td>" . $desc . "</td>";
        echo "</tr>";

        if (isset($bon)) {
            $req = "select obon_libelle from bonus_objets where obon_cod = $bon ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\">Bonus</td>";
            echo "<td>" . $db->f("obon_libelle") . "</td>";
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
