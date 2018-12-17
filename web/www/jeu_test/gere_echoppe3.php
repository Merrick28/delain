<?php
/*
FORMULE
FRM_COD
FRM_TYPE
FRM_NOM
FRM_TEMPS_TRAVAIL
FRM_COUT
FRM_RESULTAT
FRM_COMP_COD

FORMULE_COMPOSANT
FRM_COD
FRM_GOBJ_COD
FRM_NUM

FORMULE_PRODUIT
FRM_COD
FRM_GOBJ_COD
FRM_NUM

FORMULE_REALISATION

ECHOPPE_LOG
ELOG_COD
ELOG_LIEU_COD
ELOG_PERSO_COD
ELOG_TYPE_COD
ELOG_GOBJ_COD
ELOG_OBJ_COD
ELOG_DATE
ELOG_DESCRIPTION
ELOG_CREDIT
ELOG_DEBIT


*/


include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$db2 = new base_delain;


function startPane($tab, $index, $active_index)
{
    if ($active_index == $index)
    {
        echo "<div class='centrer' id=\"pane$index\">";
    }
    else
    {
        echo "<div class='centrer' id=\"pane$index\" style=\"display:none;\">";
    }
    echo "<table class=\"tableauPane\">";
    echo "<tr>";
    foreach ($tab as $i => $vali)
    {
        if ($i == $index)
        {
            echo "<td class=\"activePane\"><strong>$vali</strong></td>";
        }
        else
        {
            echo "<td class=\"inactivePane\"><a href=\"javascript:switchPane('pane$i');\">$vali</a></td>";
        }
    }

    echo "</tr>";
    echo "<tr><td colspan=\"" . count($tab) . "\" class=\"centerPane\">";

}

function endPane()
{
    echo "</td></tr>";
    echo "</table>";
    echo "</div>";
}

$liste_panels = array("Stock", "Prix", "Transactions", "Contrats", "Livre de Comptes", "Atelier");

echo '	<link rel="stylesheet" type="text/css" href="../styles/onglets.css" title="essai">
		<script language="javascript" src="../scripts/onglets.js"></script>
';

include "tab_haut.php";
$erreur = 0;
if (!isset($mag))
{
    echo "<p>Erreur sur la transmission du lieu_cod ";
    $erreur = 1;
}
if ($erreur == 0)
{
    $req = "select perso_admin_echoppe_noir,perso_admin_echoppe from perso where perso_cod = $perso_cod ";
    $db->query($req);
    $db->next_record();
    $perso_admin_echoppe_noir = $db->f("perso_admin_echoppe_noir");
    $perso_admin_echoppe      = $db->f("perso_admin_echoppe");
    $req                      = "select lieu_cod,lieu_tlieu_cod,lieu_nom,pos_cod,pos_x,pos_y,etage_libelle,lieu_alignement,lieu_compte,mger_perso_cod ";
    $req                      = $req . "from lieu,lieu_position,positions,etage ";
    $req                      = $req . "left outer join magasin_gerant on mger_lieu_cod = $mag ";
    $req                      = $req . "where lieu_cod = lpos_lieu_cod ";
    $req                      = $req . "and lieu_tlieu_cod in (11,14,21) ";
    $req                      = $req . "and lpos_pos_cod = pos_cod ";
    $req                      = $req . "and pos_etage = etage_numero ";
    //$req = $req . "and mger_lieu_cod = lieu_cod ";
    //$req = $req . "and mger_perso_cod = $perso_cod ";
    $req = $req . "and lieu_cod = $mag ";
    $db->query($req);
    if ($db->nf() == 0)
    {
        echo "<p>Erreur, vous n'êtes pas en gérance de ce magasin !";
        $erreur = 1;
    }
    else
    {
        $acces_ok = 0;
        $db->next_record();
        $type_lieu = $db->f("lieu_tlieu_cod");
        if ($type_lieu == 21 && $perso_admin_echoppe_noir == 'O')
        {
            $acces_ok = 1;
        }
        if ($type_lieu == 11 && $perso_admin_echoppe == 'O')
        {
            $acces_ok = 1;
        }
        if ($type_lieu == 14 && $perso_admin_echoppe == 'O')
        {
            $acces_ok = 1;
        }
        if ($perso_cod == $db->f("mger_perso_cod"))
        {
            $acces_ok = 1;
        }
        if ($acces_ok == 0)
        {
            echo "<p>Erreur, vous n'êtes pas en gérance de ce magasin !</p>";
            $erreur = 1;
        }

        $pos_actuelle  = $db->f("pos_cod");
        $lieu_cod      = $db->f("lieu_cod");
        $lieu_nom      = $db->f("lieu_nom");
        $pos_x         = $db->f("pos_x");
        $pos_y         = $db->f("pos_y");
        $etage_libelle = $db->f("etage_libelle");
        $lieu_compte   = $db->f("lieu_compte");

        if ($type_lieu == 14)
        {
            define("TYPE_ECHOPPE", "MAGIE");
        }
        else
        {
            if ($type_lieu == 21)
            {
                define("TYPE_ECHOPPE", "MARCHE_NOIR");
            }
            else
            {
                if ($type_lieu == 11)
                {
                    define("TYPE_ECHOPPE", "ECHOPPE_ROYALE");
                }
            }
        }
    }
}
if ($erreur == 0)
{
if ($methode == "visu")
{
    $req_stock
        = "select gobj_seuil_dex, gobj_seuil_force, gobj_tobj_cod, tobj_libelle, gobj_poids, gobj_pa_normal,
				gobj_pa_eclair, gobj_distance, gobj_deposable, gobj_comp_cod, gobj_description, gobj_cod, gobj_nom,
				gobj_valeur, gobj_echoppe_stock, gobj_echoppe_destock, mgstock_nombre, mgstock_vente_persos,
				mgstock_vente_echoppes, comp_libelle
			FROM (select * from objet_generique, type_objet where gobj_cod = $objet and gobj_tobj_cod = tobj_cod) t1
			LEFT OUTER JOIN stock_magasin_generique ON (gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = $lieu_cod) ";
    if ($perso_cod == 451072 or $perso_cod == 185)
    {     /* Test bizarre ... La requête ne peut pas marcher avec ça ...*/
        $req_stock = $req_stock . "LEFT outer JOIN competences ON (gobj_comp_cod = comp_cod and gobj_tobj_cod in (1,9)) where (gobj_echoppe_vente = 'O' or mgstock_nombre > 0)";
    }
    else
    {
        if (TYPE_ECHOPPE == "MAGIE")
        {
            $req_stock = $req_stock . "LEFT OUTER JOIN competences ON (gobj_comp_cod = comp_cod) where (gobj_tobj_cod = 5 or mgstock_nombre > 0) ";
        }
        else
        {
            $req_stock = $req_stock . "LEFT OUTER JOIN competences ON (gobj_comp_cod = comp_cod and gobj_tobj_cod in (1,9)) where gobj_echoppe_stock = 'O' or mgstock_nombre > 0 and gobj_tobj_cod in (1,9) ";
        }
    }
    $req_stock = $req_stock . " order by gobj_tobj_cod,gobj_nom";
    $db->query($req_stock);
    if ($db->nf() != 0)
    {
        $db->next_record();
        $t_etat      = 0;
        $comp        = $db->f("gobj_comp_cod");
        $desc        = $db->f("gobj_description");
        $distance    = $db->f("gobj_distance");
        $type_objet  = $db->f("gobj_tobj_cod");
        $seuil_force = $db->f("gobj_seuil_force");
        $seuil_dex   = $db->f("gobj_seuil_dex");
        echo "<p class=\"titre\">" . $db->f("gobj_nom") . "</p>";
        echo "<div class='centrer'><table>";

        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Type d’objet :</td>";
        echo "<td><p>" . $db->f("tobj_libelle");
        if ($db->f("gobj_deposable") == 'N')
        {
            echo " <strong>non déposable !</strong>";
        }
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Poids :</td>";
        echo "<td><p>" . $db->f("gobj_poids") . "</td>";
        echo "</tr>";

        if ($type_objet == 1)
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque normale :</td>";
            echo "<td><p>" . $db->f("gobj_pa_normal") . "</td>";
            echo "</tr>";

            if ($db->f("gobj_distance") == 'N')
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque foudroyante :</td>";
                echo "<td><p>" . $db->f("gobj_pa_eclair") . "</td>";
                echo "</tr>";
            }

            $req = "select obcar_des_degats,obcar_val_des_degats,obcar_bonus_degats ";
            $req = $req . "from objets_caracs,objet_generique ";
            $req = $req . "where gobj_cod = $objet ";
            //$req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_obcar_cod = obcar_cod ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Dégats</td>";
            echo "<td><p>" . $db->f("obcar_des_degats") . "D" . $db->f("obcar_val_des_degats") . "+" . $db->f("obcar_bonus_degats") . "</td>";
            echo "</tr>";


            $req = "select comp_libelle from competences where comp_cod = $comp ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Compétence utilisée</td>";
            echo "<td><p>" . $db->f("comp_libelle") . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Seuil de force</td>";
            echo "<td><p>" . $seuil_force . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Seuil de dextérité</td>";
            echo "<td><p>" . $seuil_dex . "</td>";
            echo "</tr>";
        }
        if ($type_objet == 2)
        {
            $req = "select obcar_armure ";
            $req = $req . "from objets_caracs,objet_generique,objets ";
            $req = $req . "where gobj_cod = $objet ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_obcar_cod = obcar_cod ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Armure</td>";
            echo "<td><p>" . $db->f("obcar_armure") . "</td>";
            echo "</tr>";
        }
        if ($type_objet == 1 and $distance == 'O')
        {
            $req = "select obcar_chute ";
            $req = $req . "from objets_caracs,objet_generique,objets ";
            $req = $req . "where gobj_cod = $objet ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_obcar_cod = obcar_cod ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Chute</td>";
            echo "<td><p>" . $db->f("obcar_chute") . "</td>";
            echo "</tr>";
        }
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Description</td>";
        echo "<td><p>" . $desc . "</td>";
        echo "</tr>";

        if (isset($bon))
        {
            $req = "select obon_libelle from bonus_objets where obon_cod = $bon ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Bonus</td>";
            echo "<td><p>" . $db->f("obon_libelle") . "</td>";
            echo "</tr>";
        }

        echo "</table></div>";
    }
    else
    {
        echo "Vous n'avez pas le droit d'accéder à la description de cet objet</br>";
    }
    include "tab_bas.php";
}

if ($methode == "visu2")
{
    $req_stock
        = "select obj_seuil_dex, obj_seuil_force, gobj_tobj_cod, tobj_libelle, obj_poids, gobj_pa_normal, gobj_pa_eclair,
				obj_distance, obj_deposable, gobj_comp_cod, obj_description, obj_des_degats, obj_val_des_degats, obj_bonus_degats,
				obj_armure, obj_gobj_cod, obj_nom, obj_valeur, gobj_echoppe_stock, gobj_echoppe_destock, comp_libelle 
			FROM (select * from objets, objet_generique, type_objet where obj_cod = $objet and gobj_tobj_cod = tobj_cod and obj_gobj_cod = gobj_cod) t1
			LEFT OUTER JOIN stock_magasin ON (obj_cod = mstock_obj_cod and mstock_lieu_cod = $lieu_cod)
			LEFT OUTER JOIN competences ON (gobj_comp_cod = comp_cod and gobj_tobj_cod in (1,9))
			ORDER BY gobj_tobj_cod, obj_nom";
    $db->query($req_stock);
    if ($db->nf() != 0)
    {
        $db->next_record();
        $t_etat         = 0;
        $comp           = $db->f("gobj_comp_cod");
        $desc           = $db->f("obj_description");
        $distance       = $db->f("obj_distance");
        $type_objet     = $db->f("gobj_tobj_cod");
        $seuil_force    = $db->f("obj_seuil_force");
        $seuil_dex      = $db->f("obj_seuil_dex");
        $armure         = $db->f("obj_armure");
        $chute          = $db->f("obj_seuil_dex");
        $des_degats     = $db->f("obj_des_degats");
        $val_des_degats = $db->f("obj_val_des_degats");
        $bonus_degats   = $db->f("obj_bonus_degats");

        echo "<p class=\"titre\">" . $db->f("obj_nom") . "</p>";
        echo "<div class='centrer'><table>";

        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Type d'objet :</td>";
        echo "<td><p>" . $db->f("tobj_libelle");
        if ($db->f("obj_deposable") == 'N')
        {
            echo " <strong>non déposable !</strong>";
        }
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Poids :</td>";
        echo "<td><p>" . $db->f("obj_poids") . "</td>";
        echo "</tr>";

        if ($type_objet == 1)
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque normale :</td>";
            echo "<td><p>" . $db->f("gobj_pa_normal") . "</td>";
            echo "</tr>";
            if ($db->f("obj_distance") == 'N')
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque foudroyante :</td>";
                echo "<td><p>" . $db->f("gobj_pa_eclair") . "</td>";
                echo "</tr>";
            }
            echo "<tr>";
            $des_degats = $db->f("obj_des_degats");
            echo "<td class=\"soustitre2\"><p>Dégâts</td>";
            echo "<td><p>" . $des_degats . "D" . $val_des_degats . "+" . $bonus_degats . "</td>";
            echo "</tr>";


            $req = "select comp_libelle from competences where comp_cod = $comp ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Compétence utilisée</td>";
            echo "<td><p>" . $db->f("comp_libelle") . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Seuil de force</td>";
            echo "<td><p>" . $seuil_force . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Seuil de dextérité</td>";
            echo "<td><p>" . $seuil_dex . "</td>";
            echo "</tr>";
        }
        if ($type_objet == 2)
        {
            $req = "select obcar_armure ";
            $req = $req . "from objets_caracs,objet_generique,objets ";
            $req = $req . "where gobj_cod = $objet ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_obcar_cod = obcar_cod ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Armure</td>";
            echo "<td><p>" . $db->f("obcar_armure") . "</td>";
            echo "</tr>";
        }
        if ($type_objet == 1 and $distance == 'O')
        {
            $req = "select obcar_chute ";
            $req = $req . "from objets_caracs,objet_generique,objets ";
            $req = $req . "where gobj_cod = $objet ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_obcar_cod = obcar_cod ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Chute</td>";
            echo "<td><p>" . $db->f("obcar_chute") . "</td>";
            echo "</tr>";
        }
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Description</td>";
        echo "<td><p>" . $desc . "</td>";
        echo "</tr>";

        if (isset($bon))
        {
            $req = "select obon_libelle from bonus_objets where obon_cod = $bon ";
            $db->query($req);
            $db->next_record();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Bonus</td>";
            echo "<td><p>" . $db->f("obon_libelle") . "</td>";
            echo "</tr>";
        }

        echo "</table></div>";
    }
    else
    {
        echo "Vous n’avez pas le droit d’accéder à la description de cet objet</br>";
    }
    include "tab_bas.php";
}

$select_pane = 0;
if (isset($_POST['pane']))
{
    $select_pane = $pane;
}

if (!isset($methode))
{
    $methode = "debut";
}
if (isset($_POST['methode']))
{
    switch ($methode)
    {
        case "create_mag_tran":
            echo "Nouvelle transaction</br>";
            $erreur = 0;
            $req_verif
                    = "SELECT gobj_valeur * $tran_quantite as prix_mini, mgstock_nombre as qte_dispo
					FROM objet_generique, stock_magasin_generique
					WHERE gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = $lieu_cod
						AND gobj_cod = $article_cod";
            $db->query($req_verif);
            if (!$db->next_record())
            {
                echo "Erreur: Quantité insuffisante";
                $erreur = 1;
            }
            else
            {
                if ($db->f("qte_dispo") < $tran_quantite)
                {
                    echo "Erreur : Quantité insuffisante";
                    $erreur = 1;
                }
                if ($db->f("prix_mini") > $tran_prix)
                {
                    echo "Erreur : Le prix minimal de cette transaction est de " . $db->f("prix_mini") . ".";
                    $erreur = 1;
                }
            }
            if ($dest_lieu_cod == "-")
            {
                echo "Erreur : Sélectionner un client dans le magasin !";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $req
                    = "insert into transaction_echoppe
							(tran_gobj_cod, tran_vendeur, tran_acheteur, tran_nb_tours, tran_prix, tran_identifie, tran_quantite, tran_type)
						values ($article_cod, $lieu_cod, $dest_lieu_cod, 3, $tran_prix, 'O', $tran_quantite, 'MM')";
                $db->query($req);
                echo "OK : transaction créée.";
            }
            break;

        case "create_tran":
            echo "Nouvelle transaction</br>";
            $erreur = 0;
            if (isset($_POST['article_cod']) && $article_cod)
            {
                // ARTICLE NORMAL
                $req_verif
                    = "select gobj_valeur*$tran_quantite as prix_mini,mgstock_nombre  as qte_dispo from objet_generique,stock_magasin_generique
						where gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = $lieu_cod
							and gobj_cod = $article_cod";
                $db->query($req_verif);
                if (!$db->next_record())
                {
                    echo "Erreur: Quantité insuffisante";
                    $erreur = 1;
                }
                else
                {
                    if ($db->f("qte_dispo") < $tran_quantite)
                    {
                        echo "Erreur: Quantité insuffisante";
                        $erreur = 1;
                    }
                    if ($db->f("prix_mini") > $tran_prix)
                    {
                        echo "Erreur: Le prix minimal de cette transaction est de " . $db->f("prix_mini") . ".";
                        $erreur = 1;
                    }
                }

                if ($dest_perso_cod == "-")
                {
                    echo "Erreur: Sélectionner un client dans le magasin !";
                    $erreur = 1;
                }
                if ($erreur == 0)
                {
                    $req = "insert into transaction_echoppe (	tran_gobj_cod,tran_vendeur,tran_acheteur,tran_nb_tours,tran_prix,tran_identifie,	tran_quantite,tran_type) "
                        . " values ($article_cod,$lieu_cod,$dest_perso_cod,3,$tran_prix,'O',$tran_quantite,'M1')";
                    $db->query($req);
                    echo "OK : transaction créée.";
                }
            }
            else
            {
                // ARTICLE SPECIAL
                $req_verif
                    = "select obj_cod,obj_nom,gobj_cod,obj_valeur
						from objets,objet_generique,stock_magasin
						where mstock_lieu_cod = $lieu_cod
							and mstock_obj_cod = obj_cod
							and obj_gobj_cod = gobj_cod
							and obj_cod = $article_special_cod";
                //echo  $req_verif;
                $db->query($req_verif);
                if (!$db->next_record())
                {
                    echo "Erreur: Objet non disponible";
                    $erreur = 1;
                }
                if ($db->f("obj_valeur") > $tran_prix)
                {
                    echo "Erreur: Le prix minimal de cette transaction est de " . $db->f("obj_valeur") . ".";
                    $erreur = 1;
                }
                if ($dest_perso_cod == "-")
                {
                    echo "Erreur: Sélectionner un client dans le magasin !";
                    $erreur = 1;
                }
                if ($erreur == 0)
                {
                    $req = "insert into transaction_echoppe (	tran_gobj_cod,tran_vendeur,tran_acheteur,tran_nb_tours,tran_prix,tran_identifie,	tran_quantite,tran_type) "
                        . " values ($article_special_cod,$lieu_cod,$dest_perso_cod,3,$tran_prix,'O',1,'M2')";
                    $db->query($req);
                    echo "OK: transaction créée.";
                }
            }
            break;

        case "delete_tran":
            echo "Transaction supprimée.";
            $req = "delete from transaction_echoppe where tran_cod = $transaction_cod";
            $db->query($req);
            break;

        case "accepter_tran":
            echo "Transaction acceptée.";
            $req = "select tran_gobj_cod,tran_vendeur,tran_acheteur,tran_prix,tran_quantite,tran_type from transaction_echoppe where tran_cod = $transaction_cod";
            $db->query($req);
            if (!$db->next_record())
            {
                $resultat .= "Erreur: La transaction n'existe pas.";
            }
            else
            {
                $objet_cod    = $db->f("tran_gobj_cod");
                $vendeur      = $db->f("tran_vendeur");
                $acheteur     = $db->f("tran_acheteur");
                $prix         = $db->f("tran_prix");
                $quantite     = $db->f("tran_quantite");
                $tran_type    = $db->f("tran_type");
                $obj_gobj_cod = $db->f("tran_gobj_cod");
                // VERIFICATIONS
                $req_verif
                    = "select gobj_valeur*$quantite as prix_mini,mgstock_nombre  as qte_dispo from objet_generique,stock_magasin_generique
						where gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = $vendeur
							and gobj_cod = $objet_cod";
                $db->query($req_verif);
                if (!$db->next_record())
                {
                    echo "Erreur: Quantité insuffisante";
                    $erreur = 1;
                }
                else
                {
                    if ($db->f("qte_dispo") < $quantite)
                    {
                        echo "Erreur: Quantité insuffisante";
                        $erreur = 1;
                    }
                    if ($db->f("prix_mini") > $prix)
                    {
                        echo "Erreur: Le prix minimal de cette transaction est de " . $db->f("prix_mini") . ".";
                        $erreur = 1;
                    }
                }
                // caisse de l'acheteur
                if ($lieu_compte < $prix * 1.1)
                {
                    echo "Erreur: Pas assez de Br en caisse !";
                    $erreur = 1;
                }
                if ($erreur == 0)
                {
                    // On retire les Br à la caisse de l'acheteur
                    $req_tran = "update lieu set lieu_compte = lieu_compte - ($prix*1.1) where lieu_cod = $acheteur";
                    $db->query($req_tran);
                    // On les ajoute à la caisse du vendeur
                    $req_tran = "update lieu set lieu_compte = lieu_compte + $prix where lieu_cod = $vendeur";
                    $db->query($req_tran);
                    // On retire l'objet du stock du vendeur
                    $req_tran = "update stock_magasin_generique set mgstock_nombre = mgstock_nombre - $quantite where mgstock_lieu_cod = $vendeur "
                        . "and mgstock_gobj_cod = $objet_cod";
                    $db->query($req_tran);
                    // On ajoute l'objet au stock de l'acheteur
                    $req_tran = "select mgstock_nombre from stock_magasin_generique where mgstock_lieu_cod = $acheteur "
                        . "and mgstock_gobj_cod = $objet_cod";
                    $db->query($req_tran);
                    if ($db->next_record())
                    {
                        $req_tran = "update stock_magasin_generique set mgstock_nombre = mgstock_nombre + $quantite where mgstock_lieu_cod = $acheteur "
                            . "and mgstock_gobj_cod = $objet_cod";
                        $db->query($req_tran);
                    }
                    else
                    {
                        $req_tran
                            = "insert into stock_magasin_generique (mgstock_nombre,mgstock_lieu_cod,mgstock_gobj_cod)
								values( $quantite,$acheteur,$objet_cod)";
                        $db->query($req_tran);
                    }
                    // Supression de la transaction
                    $req_tran = "delete from transaction_echoppe where tran_gobj_cod = $objet_cod";
                    $db->query($req_tran);
                    // Ajout de la ligne de Log
                    //impossible: problème de contrainte avec perso__cod
                    /*
                    $req_tran =  "insert into mag_tran_generique
                    (mgtra_lieu_cod,mgtra_perso_cod,mgtra_gobj_cod,mgtra_sens,mgtra_montant,mgtra_nombre)
                    values
                    ($vendeur,$acheteur,$obj_gobj_cod,5,$prix,$quantite)";
                    $db->query($req_tran);
                    */
                }
            }
            break;

        case "refuser_tran":
            echo "Transaction refusée.";
            $req = "delete from transaction_echoppe where tran_cod = $transaction_cod";
            $db->query($req);
            break;

        case "stocker":
            echo "Stockage";
            foreach ($_POST as $i => $value)
            {
                if ($value != null && substr($i, 0, 5) == "STOCK")
                {
                    $gobj_cod = substr($i, 5);
                    //echo "HA obj=".$gobj_cod." NB=".$value;
                    $req = "select magasin_stocker($perso_cod,$lieu_cod,$gobj_cod,$value) as resultat";
                    $db->query($req);
                    $db->next_record();
                    echo "<p>", $db->f("resultat"), "</p>";
                }
            }
            break;

        case "destocker":
            echo "Destockage";
            foreach ($_POST as $i => $value)
            {
                if ($value != null && substr($i, 0, 7) == "DESTOCK")
                {
                    $gobj_cod = substr($i, 7);
                    //echo "HA obj=".$gobj_cod." NB=".$value;
                    $req = "select magasin_destocker($perso_cod,$lieu_cod,$gobj_cod,$value) as resultat";
                    $db->query($req);
                    $db->next_record();
                    echo "<p>", $db->f("resultat"), "</p>";
                }
            }
            break;

        case "autorisations":
            echo "Autorisations";
            foreach ($_POST as $i => $value)
            {
                if (substr($i, 0, 18) == "DISPO_PUBLIC_ACTU_")
                {
                    $gobj_cod = substr($i, 18);
                    //echo "HA obj=".$gobj_cod." NB=".$value;
                    $public_nouv = $_POST["DISPO_PUBLIC_" . $gobj_cod];
                    $ech_actu    = $_POST["DISPO_PRO_ACTU_" . $gobj_cod];
                    $ech_nouv    = $_POST["DISPO_PRO_" . $gobj_cod];

                    if ($value != $public_nouv || $ech_actu != $ech_nouv)
                    {
                        $req = "update stock_magasin_generique set mgstock_vente_persos = '$public_nouv', mgstock_vente_echoppes = '$ech_nouv' "
                            . "where mgstock_lieu_cod = $lieu_cod "
                            . "and mgstock_gobj_cod = $gobj_cod";
                        $db->query($req);
                        $db->next_record();
                    }
                    //echo "<p>Mise à jour des autorisations</p>";
                }
            }
            break;

        case "realiser_formule":
            echo "Atelier";
            $req = "select magasin_realiser_formule($perso_cod,$lieu_cod,$frm_cod) as resultat";
            //echo $req;
            $db->query($req);
            $db->next_record();
            echo "<p>", $db->f("resultat"), "</p>";
            if ($nombre != '')
            {
                $compt = 0;
                while ($compt != $nombre - 1)
                {
                    $req = "select magasin_realiser_formule($perso_cod,$lieu_cod,$frm_cod) as resultat";
                    $db->query($req);
                    $db->next_record();
                    echo "<p>", $db->f("resultat"), "</p>";
                    $compt = $compt + 1;
                }
            }
            echo "<p>nombre : ", $nombre, "</p>";
            break;

        case "nom";
            echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"nom2\">";
            echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
            $req = "SELECT lieu_nom,lieu_description FROM lieu ";
            $req = $req . "where lieu_cod = $mag ";
            $db->query($req);
            $db->next_record();

            echo "<table>";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Nom du magasin (70 caracs maxi)</td>";
            echo "<td><input type=\"text\" name=\"nom\" size=\"50\" value=\"" . $db->f("lieu_nom") . "\"></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Description</td>";
            $desc = str_replace(chr(127), ";", $db->f("lieu_description"));
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
            $req  = "update lieu set lieu_nom = e'" . pg_escape_string($nom) . "', lieu_description = e'" . pg_escape_string($desc) . "' where lieu_cod = $mag ";
            $db->query($req);
            echo "<p>Les changements sont validés !";
            break;
    }
}

?>
<script>
    currentPane = 'pane<?php echo $select_pane?>';
</script>

<p class="titre">Gestion de : <?php echo $lieu_nom ?> - (<?php echo $pos_x ?>, <?php echo $pos_y ?>
    , <?php echo $etage_libelle ?>, <?php echo $lieu_compte ?> Br en caisse)</p>
<div class="centrer">
    <div id="intro" class="tableau2">
        <p>Information aux gérants </p>
        <p>Ceci est une page en cours de développement, ce qui veut principalement dire qu'elle ne fonctionne pas. Elle
            va changer, contenir des essais et des idées qui viendront plus ou moins vite (ou pas du tout). Mais vous
            pouvez faire des essais et des remarques si vous voulez. Pour cela contactez Mirreck ou votre admin
            commercial préféré.</p>
        <p><strong>26/02/2006</strong> Nouveau stock</p>
        <p><strong>09/08/2006</strong> Prêt pour les permiers beta-tests</p>
        <p><strong>31/01/2008</strong> Ajout de la fonctionnalité pour changer la description et le nom du magasin</p>
        <p><strong>28/12/2009</strong> De nombreuses modifications / Corrections ont été apportées. L'une va vous toucher
            directement, avec la limitation des approvisionnements en fonction de l'échoppe. Certains articles ne
            pourront plus être approvisionnés librement. Adressez vous à votre Maitre Marchand préféré ...</p>
        Clients présents dans l’échoppe:
        <?php
        $liste_clients = "";
        $req_vue
                       = "select lower(perso_cod) as minusc, perso_cod, perso_nom
		from perso, perso_position
		where ppos_pos_cod = $pos_actuelle
			and ppos_perso_cod = perso_cod
			and perso_type_perso in (1,2,3)
			and perso_actif = 'O'
		order by perso_type_perso,minusc";
        $db->query($req_vue);
        while ($db->next_record())
        {
            $liste_clients .= $db->f("perso_nom") . ";";
            echo $db->f("perso_nom") . ", ";
        }
        ?>
        <BR/>
        <form name="message" method="post" action="messagerie2.php">
            <input type="hidden" name="m" value="2">
            <input type="hidden" name="n_dest" value="<?php echo $liste_clients ?>">
            <input type="hidden" name="dmsg_cod">
        </form>

            <a class="centrer" href="javascript:document.message.submit();">Envoyer un message à tous les clients !</a><br>

        <form name="description" method="post" action="gere_echoppe4.php">
            <input type="hidden" name="mag" value="<?php echo $mag ?>">
            <input type="hidden" name="methode" value="nom">
        </form>
        <strong><a class="centrer" href="javascript:document.description.submit();">Changer le nom et la
                    description</a></strong>

            <form name="refuge" method="post" action="gere_echoppe4.php">
                <input type="hidden" name="mag" value="<?php echo $mag ?>">
                <input type="hidden" name="methode" value="statut2">

                <?php
                $req = "select lieu_prelev,lieu_marge ";
                $req = $req . "from lieu ";
                $req = $req . "where lieu_cod = $mag ";
                $db->query($req);
                $db->next_record();
                if ($db->f("lieu_prelev") == 15)
                {
                echo "<p>Votre magasin n'est pas un refuge. Si vous souhaitez le transformer en refuge, les prélèvements de l'administration passeront automatiquement à 30%.<br>";
                if ($db->f("lieu_marge") < 30)
                {
                    echo "Votre marge est insuffisante pour accomplir cette action.";
                }
                else
                {
                ?>
                <input type="hidden" name="ref" value="o">
        <p style=text-align:left><strong><a href="javascript:document.refuge.submit();">Passer en mode refuge <em>(Cette
                        fonctionnalité sera dorénavant controlée)</em></a></strong>
            <?php
            }
            }
            else
            {
            ?>
            <input type="hidden" name="ref" value="n">
        <p>Votre magasin est un refuge. Si vous souhaitez abandonner cette fonctionnalité, les prélèvements de
            l'administration passeront automatiquement à 15%.<br>
        <p style=text-align:left><strong><a href="javascript:document.refuge.submit();">Abandonner le statut de refuge pour
                cette échoppe ? <em>(Cette fonctionnalité sera dorénavant controlée)</em></a></strong></p>
            <?php
            }
            ?>
            </form>
            <form name="marge" method="post" action="gere_echoppe4.php">
                <input type="hidden" name="mag" value="<?php echo $mag ?>">
                <input type="hidden" name="methode" value="marge">
        <p>Votre magasin réalise pour l'instant <?php echo $db->f("lieu_marge") ?>% de marge<br>
        <p style=text-align:left><strong><a href="javascript:document.marge.submit();">Changer cette donnée ?</a></strong></p>

        </form>


    </div>
    <br>
    <?php
    startPane($liste_panels, 0, $select_pane);
    ?>
    <p>

        <strong>Objets Uniques en vitrine:</strong><BR/>
        <?php
        $req_stock
            = "select obj_cod,obj_nom,gobj_cod
from objets,objet_generique,stock_magasin
where mstock_lieu_cod = $lieu_cod
				and mstock_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
                and obj_nom != gobj_nom
				order by obj_nom";
        $db->query($req_stock);
        if ($db->nf() == 0)
        {
            echo "Aucun objet remarquable !<BR/>";
        }
        else
        {
            while ($db->next_record())
            {
                echo '<a href="' . $PHP_SELF . '?methode=visu2&objet=' . $db->f('obj_cod') . '&mag=' . $mag . '">' . $db->f("obj_nom") . '</a><BR />';
            }
        }
        ?>


        <?php
        $req_stock
            = "select gobj_cod,gobj_nom,gobj_valeur,gobj_echoppe_stock,gobj_echoppe_destock,mgstock_nombre,mgstock_vente_persos,mgstock_vente_echoppes,comp_libelle,mgaut_gobj_cod from objet_generique
 LEFT OUTER JOIN stock_magasin_autorisations ON (gobj_cod = mgaut_gobj_cod and mgaut_lieu_cod = $lieu_cod)
 LEFT OUTER JOIN stock_magasin_generique ON (gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = $lieu_cod) ";
        if ($perso_cod == 451072 or $perso_cod == 185)
        {     /* Test bizarre ... La requête ne peut pas marcher avec ça ...*/
            $req_stock = $req_stock . "LEFT outer JOIN competences ON (gobj_comp_cod = comp_cod and gobj_tobj_cod in (1,9)) where (gobj_echoppe_vente = 'O' or mgstock_nombre > 0)";
        }
        else
        {
            if (TYPE_ECHOPPE == "MAGIE")
            {
                $req_stock = $req_stock . "LEFT OUTER JOIN competences ON (gobj_comp_cod = comp_cod) where (gobj_tobj_cod = 5 or mgstock_nombre > 0) ";
            }
            else
            {
                $req_stock = $req_stock . "LEFT OUTER JOIN competences ON (gobj_comp_cod = comp_cod and gobj_tobj_cod in (1,9)) where gobj_echoppe_stock = 'O' or mgstock_nombre > 0 ";
            }
        }
        $req_stock = $req_stock . " order by gobj_tobj_cod,gobj_nom";
        $db->query($req_stock);
        ?>
        <script language="javascript">
            function valideStock() {
                document.modifstock.methode.value = 'stocker';
                document.modifstock.submit();
            }
            function valideDeStock() {
                document.modifstock.methode.value = 'destocker';
                document.modifstock.submit();
            }
            function valideAutorisations() {
                document.modifstock.methode.value = 'autorisations';
                document.modifstock.submit();
            }
        </script>
    <form method="post" name="modifstock">
        <input type="hidden" name="methode" value="stocker">
        <table width="100%">
            <tr>
                <td>Article</td>
                <td>Prix de base</td>
                <td>Qté en stock</td>
                <td>Stocker</td>
                <td>Déstocker</td>
                <td colspan="2">Disponible<br/> pour les clients</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td><a href="javascript:valideStock();">Valider le stockage</a></td>
                <td><a href="javascript:valideDeStock();">Valider le déstockage</a></td>
                <td colspan="1"><a href="javascript:valideAutorisations();">Valider les autorisations</a></td>
            </tr>
            <?php
            while ($db->next_record())
            {
                if ($db->f("mgstock_vente_persos") == 'O')
                {
                    $checked_v_perso = "checked";
                }
                else
                {
                    $checked_v_perso = "";
                }
                if ($db->f("mgstock_vente_echoppes") == 'O')
                {
                    $checked_v_ech = "checked";
                }
                else
                {
                    $checked_v_ech = "";
                }
                $comp_libelle = $db->f('comp_libelle');
                echo "<tr><td><a href=\"" . $PHP_SELF . "?methode=visu&objet=" . $db->f('gobj_cod') . "&mag=$mag\">",
                $db->f("gobj_nom"), '</a> ', ($comp_libelle != NULL ? '(' . $comp_libelle . ')' : ''),
                "</td><td>",
                $db->f("gobj_valeur"),
                "</td><td>",
                $db->f("mgstock_nombre"),
                "</td>";
                if (($db->f("gobj_echoppe_stock") == 'O' and $db->f("mgaut_gobj_cod") != NULL) || $perso_cod == 451072)
                {
                    echo "<td><input type=\"text\" name=\"STOCK", $db->f("gobj_cod"), "\" size=\"4\"></td>";
                }
                else
                {
                    echo "<td>&nbsp;</td>";
                }
                echo "<td><input type=\"text\" name=\"DESTOCK", $db->f("gobj_cod"), "\" size=\"4\">",
                "</td><td>";
                if ($db->f("mgstock_nombre") != null)
                {
                    echo "<input type=\"hidden\" name=\"DISPO_PUBLIC_ACTU_", $db->f("gobj_cod"), "\" value=\"", $db->f("mgstock_vente_persos"), "\" $checked_v_perso >",
                    "<input type=\"checkbox\" name=\"DISPO_PUBLIC_", $db->f("gobj_cod"), "\" value=\"O\" $checked_v_perso >",
                    "</td><td>",
                    "<input type=\"hidden\" name=\"DISPO_PRO_ACTU_", $db->f("gobj_cod"), "\" value=\"", $db->f("mgstock_vente_echoppes"), "\" $checked_v_perso >",
                    "<input type=\"checkbox\" name=\"DISPO_PRO_", $db->f("gobj_cod"), "\" value=\"O\" $checked_v_ech >";
                }
                else
                {
                    echo "X</td><td>X";
                }
                echo "</td></tr>\n";
            }
            ?>
            <tr>
                <td colspan="3"></td>
                <td><a href="javascript:valideStock();">Valider le stockage</a></td>
                <td><a href="javascript:valideDeStock();">Valider le déstockage</a></td>
            </tr>
        </table>
    </form>


    </div><?php
    endPane();
    startPane($liste_panels, 1, $select_pane);
    ?><p>B</p><?php
    endPane();
    startPane($liste_panels, 2, $select_pane);
    // TRANSACTIONS
    ?>
    <div width="100%" align="left">


        <form method="post" name="begintransaction">
            <input type="hidden" name="methode" value="create_tran">
            <input type="hidden" name="pane" value="2">
            Client :<select name="dest_perso_cod">
                <option value="-">- Choisir un client présent dans le magasin -</option>
                <?php
                $req_vue = "select lower(perso_cod) as minusc,perso_cod,perso_nom from perso, perso_position where ppos_pos_cod = $pos_actuelle and ppos_perso_cod = perso_cod  and perso_type_perso in (1,2,3) and perso_actif = 'O' order by perso_type_perso,minusc";
                $db->query($req_vue);
                while ($db->next_record())
                {
                    echo "<option value=\"" . $db->f("perso_cod") . "\">" . $db->f("perso_nom") . "</option>";
                }
                ?>
            </select><br><br>

            Article :<select name="article_cod"
                             onChange="document.begintransaction.article_special_cod.selectedIndex = 0;">
                <option value="">Choisir un objet</option>
                <?php
                $req_stock = "select gobj_cod,gobj_nom,gobj_valeur,mgstock_nombre from objet_generique,stock_magasin_generique "
                    . " where gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = $lieu_cod"
                    . " and mgstock_nombre > 0"
                    . " order by gobj_tobj_cod,gobj_nom";
                $db->query($req_stock);
                while ($db->next_record())
                {
                    echo "<option value=\"" . $db->f("gobj_cod") . "\">" . $db->f("gobj_nom") . " (" . $db->f("mgstock_nombre") . " / prix : " . $db->f("gobj_valeur") . ")" . "</option>";
                }
                ?>
            </select><br><br>
            Article Special:<select name="article_special_cod"
                                    onChange="document.begintransaction.article_cod.selectedIndex = 0;">
                <option value="">Choisir un objet</option>
                <?php
                $req_stock
                    = "select obj_cod,obj_nom,obj_valeur,gobj_cod
from objets,objet_generique,stock_magasin
where mstock_lieu_cod = $lieu_cod
				and mstock_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				order by obj_nom";
                $db->query($req_stock);
                while ($db->next_record())
                {
                    echo "<option value=\"" . $db->f("obj_cod") . "\">" . $db->f("obj_nom") . "(prix : " . $db->f("obj_valeur") . ")</option>";
                }
                ?>
            </select><br><br>

            Quantité :<input type="text" name="tran_quantite" value="1"><br><br>
            Prix total Proposé :<input type="text" name="tran_prix" value="0"><br><br>
            <input type="submit" value="Valider la transaction !">
        </form>

        Transactions en cours:
        <form name="cancel_tran" method="post">
            <input type="hidden" name="pane" value="2">
            <input type="hidden" name="methode" value="delete_tran">
            <input type="hidden" name="transaction_cod" value="">
        </form>
        <table width="100%">
            <tr>
                <td>Client</td>
                <td>Objet</td>
                <td>Quantité</td>
                <td>Prix</td>
                <td>Annuler</td>
            </tr>
            <?php
            $req_stock
                = "select perso_nom,tran_cod,obj_nom,tran_quantite,tran_prix
  from transaction_echoppe,perso,objets
where
tran_acheteur = perso_cod
and tran_type = 'M2'
and tran_vendeur = $lieu_cod
and obj_cod = tran_gobj_cod
order by  perso_nom
";
            $db->query($req_stock);
            while ($db->next_record())
            { ?>
                <tr>
                    <td><?php echo $db->f("perso_nom") ?></td>
                    <td><?php echo $db->f("obj_nom") ?></td>
                    <td><?php echo $db->f("tran_quantite") ?></td>
                    <td><?php echo $db->f("tran_prix") ?></td>
                    <td>
                        <a href="javascript:document.cancel_tran.transaction_cod.value=<?php echo $db->f("tran_cod"); ?>;document.cancel_tran.submit();">Annuler</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            <?php
            $req_stock
                = "select perso_nom,tran_cod,gobj_nom,tran_quantite,tran_prix
  from transaction_echoppe,perso,objet_generique
where
tran_acheteur = perso_cod
and tran_type = 'M1'
and tran_vendeur = $lieu_cod
and gobj_cod = tran_gobj_cod
order by  perso_nom
";
            $db->query($req_stock);
            while ($db->next_record())
            { ?>
                <tr>
                    <td><?php echo $db->f("perso_nom") ?></td>
                    <td><?php echo $db->f("gobj_nom") ?></td>
                    <td><?php echo $db->f("tran_quantite") ?></td>
                    <td><?php echo $db->f("tran_prix") ?></td>
                    <td>
                        <a href="javascript:document.cancel_tran.transaction_cod.value=<?php echo $db->f("tran_cod") ?>;document.cancel_tran.submit();">Annuler</a>
                    </td>
                </tr>
                <?php
            }
            ?>

        </table>
    </div>
    <?php
    endPane();
    startPane($liste_panels, 3, $select_pane);
    //CONTRATS
    ?><p>
        Transactions avec d'autres échoppes
    </p>
    <div width="100%" align="left">


        <form method="post" name="beginmagtransaction">
            <input type="hidden" name="methode" value="create_mag_tran">
            <input type="hidden" name="pane" value="3">
            Client :<select name="dest_lieu_cod">
                <option value="-">- Choisir une autre échoppe -</option>
                <?php
                $req = "select lieu_cod,lieu_nom from lieu where lieu_tlieu_cod in (11,21) and lieu_cod != $lieu_cod";
                $db->query($req);
                while ($db->next_record())
                {
                    echo "<option value=\"" . $db->f("lieu_cod") . "\">" . $db->f("lieu_nom") . "</option>";
                }
                ?>
            </select><br><br>
            Article :<select name="article_cod"
                             onChange="document.begintransaction.article_special_cod.selectedIndex = 0;">
                <option value="">Choisir un objet</option>
                <?php
                $req_stock = "select gobj_cod,gobj_nom,gobj_valeur,mgstock_nombre from objet_generique,stock_magasin_generique "
                    . " where gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = $lieu_cod"
                    . " and mgstock_nombre > 0"
                    . " order by gobj_tobj_cod,gobj_nom";
                $db->query($req_stock);
                while ($db->next_record())
                {
                    echo "<option value=\"" . $db->f("gobj_cod") . "\">" . $db->f("gobj_nom") . " (" . $db->f("mgstock_nombre") . " / prix : " . $db->f("gobj_valeur") . ")</option>";
                }
                ?>
            </select><br><br>
            Quantité :<input type="text" name="tran_quantite" value="1"><br><br>
            Prix total Proposé :<input type="text" name="tran_prix" value="0"><br><br>
            <font color="red"> <strong>Attention: à ce prix sera ajouté 10% sur le montant total pour le transport
                    !</strong></font>
            <input type="submit" value="Valider la transaction !">
        </form>

        Mes transactions en cours:
        <form name="cancel_mag_tran" method="post">
            <input type="hidden" name="pane" value="3">
            <input type="hidden" name="methode" value="delete_tran">
            <input type="hidden" name="transaction_cod" value="">
        </form>
        <table width="100%">
            <tr>
                <td>Client</td>
                <td>Objet</td>
                <td>Quantité</td>
                <td>Prix</td>
                <td>Annuler</td>
            </tr>
            <?php
            $req_stock
                = "select lieu_nom,tran_cod,gobj_nom,tran_quantite,tran_prix
  from transaction_echoppe,lieu,objet_generique
where
tran_acheteur = lieu_cod
and tran_type = 'MM'
and tran_vendeur = $lieu_cod
and gobj_cod = tran_gobj_cod
order by  lieu_nom
";
            $db->query($req_stock);
            while ($db->next_record())
            { ?>
                <tr>
                    <td><?php echo $db->f("lieu_nom") ?></td>
                    <td><?php echo $db->f("gobj_nom") ?></td>
                    <td><?php echo $db->f("tran_quantite") ?></td>
                    <td><?php echo $db->f("tran_prix") ?></td>
                    <td>
                        <a href="javascript:document.cancel_mag_tran.transaction_cod.value=<?php echo $db->f("tran_cod") ?>;document.cancel_mag_tran.submit();">Annuler</a>
                    </td>
                </tr>
                <?php
            }
            ?>

        </table>
        <hr/>
        Les transactions qu'on me propose:
        <form name="accepter_mag_tran" method="post">
            <input type="hidden" name="pane" value="3">
            <input type="hidden" name="methode" value="accepter_tran">
            <input type="hidden" name="transaction_cod" value="">
        </form>
        <form name="refuser_mag_tran" method="post">
            <input type="hidden" name="pane" value="3">
            <input type="hidden" name="methode" value="refuser_tran">
            <input type="hidden" name="transaction_cod" value="">
        </form>
        <table width="100%">
            <tr>
                <td>Vendeur</td>
                <td>Objet</td>
                <td>Quantité</td>
                <td>Prix de base</td>
                <td>Frais de transport</td>
                <td>Prix total</td>
                <td>Action</td>
            </tr>
            <?php
            $req_stock
                = "select lieu_nom,tran_cod,gobj_nom,tran_quantite,tran_prix
  from transaction_echoppe,lieu,objet_generique
where
tran_acheteur = $lieu_cod
and tran_type = 'MM'
and tran_vendeur = lieu_cod
and gobj_cod = tran_gobj_cod
order by  lieu_nom
";
            $db->query($req_stock);
            while ($db->next_record())
            { ?>
                <tr>
                    <td><?php echo $db->f("lieu_nom") ?></td>
                    <td><?php echo $db->f("gobj_nom") ?></td>
                    <td><?php echo $db->f("tran_quantite") ?></td>
                    <td><?php echo $db->f("tran_prix") ?></td>
                    <td><?php echo 0.1 * $db->f("tran_prix") ?></td>
                    <td><?php echo 1.1 * $db->f("tran_prix") ?></td>
                    <td>
                        <a href="javascript:document.accepter_mag_tran.transaction_cod.value=<?php echo $db->f("tran_cod") ?>;document.accepter_mag_tran.submit();">Accepter</a>
                    </td>
                    <td>
                        <a href="javascript:document.refuser_mag_tran.transaction_cod.value=<?php echo $db->f("tran_cod") ?>;document.refuser_mag_tran.submit();">Refuser</a>
                    </td>
                </tr>
                <?php
            }
            ?>

        </table>


        <?php
        endPane();
        startPane($liste_panels, 4, $select_pane);
        $label_type = array("Achat (Av -> Ech)", "Vente (Ech -> Av)", "Stockage (Adm -> Ech)", "Destockage (Ech -> Adm)", "Transaction (Ech -> Av)");
        if (isset($_POST['tran_type']))
        {
            $t_type = $_POST['tran_type'];
        }
        else
        {
            $t_type = -1;
        }
        if (isset($_POST['tran_mois']))
        {
            $t_mois = $_POST['tran_mois'];
        }
        else
        {
            $t_mois = 0;
        }
        ?><p>Livre de comptes
            <?php

            $date_deb = mktime(0, 0, 0, date("m") + $t_mois, 1, date("Y"));
            $date_fin = mktime(0, 0, 0, date("m") + 1 + $t_mois, 0, date("Y"));
            echo "Entre le <strong>" . date('d/m/Y', $date_deb) . "</strong> et le <strong>" . date('d/m/Y', $date_fin) . "</strong>";
            ?>
            <a href="javascript:document.refresh_comptes.tran_mois.value=<?php echo $t_mois - 1 ?>;document.refresh_comptes.submit();">
                &lt;&lt; Mois précédent</a>
            <?php
            if ($t_mois < 0)
            {
                ?>
                <a href="javascript:document.refresh_comptes.tran_mois.value=<?php echo $t_mois + 1 ?>;document.refresh_comptes.submit();">Mois
                    suivant &gt;&gt;</a>
            <?php } ?>


        <form method="post" name="refresh_comptes">
            <input type="hidden" name="methode" value="refresh_comptes">
            <input type="hidden" name="pane" value="4">
            <input type="hidden" name="tran_mois" value="<?php echo $t_mois ?>">
            <select name="tran_type">
                <option value="-1">Tous</option>
                <?php
                foreach ($label_type as $i => $vali)
                {
                    $selected = ($t_type == $i) ? "selected" : "";
                    echo "<option value=\"$i\" $selected >$vali</option>";
                }
                ?>
            </select>

            <input type="submit" value="Voir">
        </form>
        </p>
        <table width="100%">
            <tr>
                <td class="soustitre2">Date</td>
                <td class="soustitre2">Personnage</td>
                <td class="soustitre2">Type de transaction</td>
                <td class="soustitre2">Objet</td>
                <td class="soustitre2">Nombre</td>
                <td class="soustitre2">Montant</td>
            </tr>
            <?php
            $req = "select perso_nom,gobj_nom,to_char(mgtra_date,'DD/MM/YYYY hh24:mi:ss') as date_tran,mgtra_sens,mgtra_montant,mgtra_nombre from mag_tran_generique tr ,perso per,objet_generique obj"
                . " where perso_cod = mgtra_perso_cod and gobj_cod = mgtra_gobj_cod and mgtra_lieu_cod = $lieu_cod";
            if ($t_type >= 0)
            {
                $req = $req . " and mgtra_sens = $t_type";
            }
            $req = $req . " and mgtra_date >= to_date('" . date('d/m/Y', $date_deb) . "','DD/MM/YYYY')";
            $req = $req . " and mgtra_date <= to_date('" . date('d/m/Y', $date_fin) . "','DD/MM/YYYY')";
            $req = $req . " order by mgtra_date";
            $db->query($req);
            while ($db->next_record())
            { ?>
                <tr>
                    <td><?php echo $db->f("date_tran") ?></td>
                    <td><?php echo $db->f("perso_nom") ?></td>
                    <td><?php echo $label_type[$db->f("mgtra_sens")] ?></td>
                    <td><?php echo $db->f("gobj_nom") ?></td>
                    <td><?php echo $db->f("mgtra_nombre") ?> </td>
                    <td><?php echo $db->f("mgtra_montant") ?> </td>
                </tr>
            <?php }
            ?>
        </table>
        <?php

        endPane();
        startPane($liste_panels, 5, $select_pane);
        $req_stock = "SELECT frm_cod,	frm_type,	frm_nom,	frm_temps_travail,	frm_cout,	frm_resultat,	frm_comp_cod FROM formule WHERE frm_type = 1"
            . " ORDER BY frm_nom";
        $db->query($req_stock);
        ?><p>Travaux disponibles:</p>
        <form name="realiser_formule" method="post">
            <input type="hidden" name="pane" value="5">
            <input type="hidden" name="methode" value="realiser_formule">
            <input type="hidden" name="frm_cod" value="-1">
        </form>
        <script language="javascript">
            function real_form(code) {
                document.realiser_formule.frm_cod.value = code;
                document.realiser_formule.submit();
            }
        </script>
        <?php
        while ($db->next_record())
        {
            ?>
            <div class="tableau">
                <strong><?php echo $db->f("frm_nom") ?></strong> Temps nécéssaire:
                <strong><?php echo $db->f("frm_temps_travail") ?></strong> h <br/>
                Composants: <strong><?php echo $db->f("frm_cout") ?></strong> Br<br/>
                <?php
                $req_comp
                    = "SELECT gobj_nom,frmco_num,mgstock_nombre FROM objet_generique,formule_composant 
		LEFT JOIN stock_magasin_generique ON (mgstock_gobj_cod = frmco_gobj_cod AND mgstock_lieu_cod = " . $lieu_cod . ")
		WHERE frmco_gobj_cod = gobj_cod AND frmco_frm_cod = " . $db->f("frm_cod") . "
		ORDER BY gobj_nom";
                $db2->query($req_comp);
                while ($db2->next_record())
                {
                    echo " - " . $db2->f("gobj_nom") . " (x" . $db2->f("frmco_num") . ") / Stock : " . $db2->f("mgstock_nombre") . "<br/>";
                }
                ?>
                <br/>
                Produits: <strong><?php echo $db->f("frm_resultat") ?></strong> Br<br/>
                <?php
                $req_comp = "SELECT gobj_nom,	frmpr_num FROM  	formule_produit,objet_generique"
                    . " WHERE frmpr_gobj_cod = gobj_cod AND frmpr_frm_cod = " . $db->f("frm_cod")
                    . " ORDER BY gobj_nom";
                $db2->query($req_comp);
                while ($db2->next_record())
                {
                    echo " - " . $db2->f("gobj_nom") . " (x" . $db2->f("frmpr_num") . ")<br/>";
                }
                ?>
                <form name="realise" method="post" action="<?php echo $PHP_SELF ?>">
                    <input type="text" name="nombre">
                    <input type="hidden" name="methode" value="realiser_formule">
                    <input type="hidden" name="mag" value="<?php echo $lieu_cod ?>">
                    <input type="hidden" name="pane" value="5">
                    <input type="hidden" name="frm_cod" value="<?php echo $db->f("frm_cod") ?>">
                    <input type="submit" class="test" value="Realiser !">
                    <!--<a href="javascript:real_form(<?php echo $db->f("frm_cod") ?>);">Realiser</a>-->
                </form>
                <br/><br/>
            </div>
            <?php
        }
        endPane();
        ?>
    </div>
    <?php

    }
    $contenu_page = ob_get_contents();
    ob_end_clean();
    $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
    $t->parse('Sortie', 'FileRef');
    $t->p('Sortie');
    ?>
