<?php

include "blocks/_header_page_jeu.php";
$param = new parametres();
ob_start();
// on regarde si le joueur est bien sur une échoppe

$type_lieu = 11;
$nom_lieu  = 'un magasin';

define('APPEL', 1);
include "blocks/_test_lieu.php";
$perso = $verif_connexion->perso;

if ($erreur == 0)
{
    echo "<p><strong>" . $tab_lieu['nom'] . "<strong><br>";
    $desc = str_replace(chr(127), ";", $tab_lieu['description']);
    echo "<em>" . $desc . "</em>";
    $controle_gerant = '';
    $req             = "select mger_perso_cod from magasin_gerant where mger_lieu_cod = " . $lieu;
    $stmt            = $pdo->query($req);
    $result          = $stmt->fetch();
    if ($result['mger_perso_cod'] == $perso_cod)
    {
        $controle_gerant = 'OK';
    }
    $lieu    = $tab_lieu['lieu_cod'];
    $req     = "select mod_vente($perso_cod,$lieu) as modificateur ";
    $stmt    = $pdo->query($req);
    $result  = $stmt->fetch();
    $modif   = $result['modificateur'];
    $methode = get_request_var('methode', 'entree');
    //
    // phrase à modifier par la suite en fonction des alignements
    //
    switch ($methode)
    {
        case "entree":
            echo "<p>Bonjour aventurier.";
            ?>
        <form name="echoppe" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="methode">
            <p>Voulez-vous :
            <ul>
                <li><a href="javascript:document.echoppe.methode.value='acheter';document.echoppe.submit()">Acheter de
                        l'équipement ?</a>
                <li><a href="javascript:document.echoppe.methode.value='vendre';document.echoppe.submit()">Vendre de
                        l'équipement ?</a>
                <li><a href="javascript:document.echoppe.methode.value='identifier';document.echoppe.submit()">Faire
                        identifier
                        de l'équipement ?</a>
                <li><a href="javascript:document.echoppe.methode.value='repare';document.echoppe.submit()">Faire réparer
                        de
                        l'équipement ?</a>
            </ul>
            <?php
            if ($controle_gerant == 'OK')
            {
                ?>
                <li><a href="javascript:document.echoppe.methode.value='mule';document.echoppe.submit()">Récupérer un
                        familier mûle
                        dans votre échoppe ?</a> <em>(Attention, ceci est une action définitive)</em>
                </li>
                </form>
                <?php
            }
            break;
        case "acheter":
            echo "<p class=\"titre\">Achat d'équipement</p>";

            echo "<p>Vous avez actuellement <strong>" . $perso->perso_po . "</strong> brouzoufs. ";
            $po       = $perso->perso_po;
            $lieu_cod = $tab_lieu['lieu_cod'];
            $req      = "select 0 as type,0 as a,obj_nom,tobj_libelle,gobj_cod,f_prix_obj_perso_a($perso_cod,$lieu_cod,obj_cod) as valeur_achat,coalesce(obj_obon_cod, 0) as obj_obon_cod,count(*) as nombre,comp_libelle
				from objets,objet_generique,stock_magasin,type_objet,competences
				where mstock_lieu_cod = $lieu_cod
				and mstock_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod
				and obj_nom = gobj_nom
				and gobj_comp_cod = comp_cod
				group by obj_nom,a,tobj_libelle,gobj_cod,valeur_achat,obj_obon_cod
				union
				select 1 as type,obj_cod as a,obj_nom,tobj_libelle,gobj_cod,f_prix_obj_perso_a($perso_cod,$lieu_cod,obj_cod) as valeur_achat,coalesce(obj_obon_cod, 0) as obj_obon_cod,count(*) as nombre,comp_libelle
				from objets,objet_generique,stock_magasin,type_objet,competences
				where mstock_lieu_cod = $lieu_cod
				and mstock_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod
				and obj_nom != gobj_nom
				and gobj_comp_cod = comp_cod
				group by obj_nom,a,tobj_libelle,gobj_cod,valeur_achat,obj_obon_cod
				order by tobj_libelle,gobj_comp_cod,valeur_achat,obj_nom ";
            //die ($req);
            $stmt = $pdo->query($req);

            if ($stmt->rowCount() == 0)
            {
                echo "<p>Désolé, mais les stocks sont vides, nous n'avons rien à vendre en ce moment.";
            } else
            {
                echo "<form name=\"achat\" action=\"action.php\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_achat\">";
                echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
                echo "<input type=\"hidden\" name=\"objet\">";
                echo "<div class='centrer'><table>";
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong><em>Compétence</em></strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Prix</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Quantité disponible</strong></td>";
                echo "<td></td>";
                while ($result = $stmt->fetch())
                {
                    $req   = "select obon_cod,obon_libelle,obon_prix from bonus_objets ";
                    $req   = $req . "where obon_cod = " . $result['obj_obon_cod'];
                    $stmt2 = $pdo->query($req);
                    if ($stmt2->rowCount() != 0)
                    {
                        $result2  = $stmt2->fetch();
                        $bonus    = " (" . $result2['obon_libelle'] . ")";
                        $prix_bon = $result2['obon_prix'];
                        $url_bon  = "&bon=" . $result2['obon_cod'];
                    } else
                    {
                        $bonus    = "";
                        $prix_bon = 0;
                        $url_bon  = "";
                    }
                    $prix = $result['gobj_valeur'] + $prix_bon;
                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><p><strong>";
                    if ($result['type'] == 0)
                    {

                        $req     = "select obj_cod from objets,	stock_magasin
							where obj_gobj_cod = " . $result['gobj_cod'] . "
							and obj_cod = mstock_obj_cod
							and mstock_lieu_cod = $lieu_cod
							limit 1";
                        $stmt2   = $pdo->query($req);
                        $result2 = $stmt2->fetch();
                        echo "<a href=\"visu_desc_objet3.php?objet=" . $result2['obj_cod'] . "&origine=e", $url_bon, "\">";
                    } else
                    {
                        echo "<a href=\"visu_desc_objet3.php?objet=" . $result['a'] . "&origine=e", $url_bon, "\">";
                    }
                    echo $result['obj_nom'], $bonus;
                    echo "</a>";
                    echo "</strong></td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['comp_libelle'] . "</td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['valeur_achat'] . " brouzoufs</td>";

                    echo "<td><p>", $result['nombre'], "</td>";
                    echo "<td><p>";
                    echo "<input type=\"text\" name=\"";
                    if ($result['type'] == 0)
                    {
                        echo "gobj[", $result['gobj_cod'], "-", $result['obj_obon_cod'], "]\" value=\"0\">";
                    } else
                    {
                        echo "uobj[", $result['a'], "]\" value=\"0\">";
                    }
                    echo "</td>";
                    echo "</tr>\n";
                }
                echo "</table></div>";
                echo "<input type=\"submit\" class=\"test centrer\" value=\"Acheter les quantités sélectionnées !\">";
                echo "</form>";
            }
            break;
        case "vendre":
            $generique = false;
            require "blocks/_echoppe_vendre.php";
            break;
        case "identifier":
            require "blocks/_echoppe_identifie.php";
            break;
        case "repare":
            require "blocks/_echoppe_repare.php";
            break;
        case "mule":
            /* on regarde s'il n'y a pas déjà un familier*/
            $req   = "select pfam_familier_cod from perso_familier,perso
								where pfam_perso_cod = cible
								and pfam_familier_cod = " . $perso_cod . "
								and perso_actif = 'O'";
            $stmt2 = $pdo->query($req);
            if ($stmt2->rowCount() != 0)
            {
                echo "<p>Vous ne pouvez pas récupérer un familier mule ici. Vous êtes déjà en charge d'un autre familier, deux seraient trop à gérer.";
                break;
            }
            /* on créé le familier*/
            $req      =
                "select ppos_pos_cod,perso_nom from perso_position,perso where ppos_perso_cod = perso_cod and ppos_perso_cod = " . $perso_cod;
            $stmt2    = $pdo->query($req);
            $result2  = $stmt2->fetch();
            $position = $result2['ppos_pos_cod'];
            $nom      = $result2['perso_nom'];
            $req      = "select cree_monstre_pos(193," . $position . ") as familier_num";
            $stmt2    = $pdo->query($req);
            $result2  = $stmt2->fetch();
            $num_fam  = $result2['familier_num'];
            $req      =
                "update perso set perso_nom = 'Familier de " . $nom . "',perso_lower_perso_nom = 'familier de " . strtolower($nom) . ",perso_type_perso = 3,perso_kharma=0 where perso_cod = " . $num_fam;
            /* on le rattache au perso*/
            $req   = "insert into perso_familier (pfam_perso_cod,pfam_familier_cod) values ($perso_cod, $num_fam)";
            $stmt2 = $pdo->query($req);
            break;
        default:
            echo "<p>Anomalie : aucune methode passée !";
            break;
    }
}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
