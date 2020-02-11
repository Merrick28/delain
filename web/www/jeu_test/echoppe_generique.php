<?php
include "blocks/_tests_appels_page_externe.php";

$param = new parametres();

// test sur le type de lieu
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
    echo("<p>Erreur ! Vous n'êtes pas sur un magasin !!!");
    $erreur = 1;
}
if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    if ($tab_lieu['type_lieu'] == 14)
    {
        define("TYPE_ECHOPPE", "MAGIE");
    } else if ($tab_lieu['type_lieu'] == 21)
    {
        define("TYPE_ECHOPPE", "MARCHE_NOIR");
    } else if ($tab_lieu['type_lieu'] == 11)
    {
        define("TYPE_ECHOPPE", "ECHOPPE_ROYALE");
    } else
    {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur un magasin !!!");
    }
}

if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    $lieu = $tab_lieu['lieu_cod'];
    $controle_gerant = '';
    $req = "SELECT mger_perso_cod FROM magasin_gerant WHERE mger_lieu_cod = " . $lieu;
    $stmt = $pdo->query($req);
    if($result = $stmt->fetch())
    {
        if ($result['mger_perso_cod'] == $perso_cod)
        {
            $controle_gerant = 'OK';
        }
    }

    $req = "select mod_vente($perso_cod,$lieu) as modificateur ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $modif = $result['modificateur'];


    // TRAITEMENT DES ACTIONS
    $resultat = "";
    if (isset($methode))
    {
        switch ($methode)
        {
            case "mule":
                /*Vérification du gérant*/
                if ($controle_gerant != 'OK')
                {
                    echo "<p>Vous n’êtes pas le gérant de cette échoppe, vous ne pouvez donc pas récupérer de mule ici.</p>";
                    break;
                }
                /* on regarde s’il n’y a pas déjà un familier*/
                $req
                    = "SELECT pfam_familier_cod FROM perso_familier,perso
					WHERE  pfam_familier_cod = perso_cod
						AND perso_actif IN ('O','H')
						AND pfam_perso_cod = " . $perso_cod;
                $stmt = $pdo->query($req);
                if ($stmt->rowCount() != 0)
                {
                    echo "<br><strong><p>Vous ne pouvez pas récupérer un familier mule ici. Vous êtes déjà en charge d’un autre familier, deux seraient trop à gérer.</p></strong>";
                    break;
                }
                /* on créé le familier*/
                $req = "select ajoute_familier(440, $perso_cod) as resultat";
                $stmt = $pdo->query($req);
                $result = $stmt->fetch();
                $resultat = explode(';', $result['resultat']);
                if ($resultat[0] == '1')
                {
                    echo '<p>' . $resultat[1] . '</p>';
                } else
                {
                    echo '<p>La mule au rapport ! Prenez-en bien soin.</p>';
                }
                break;

            case "nv_magasin_achat":
                foreach ($gobj as $key => $val)
                {
                    if ($val > 0)
                    {
                        //echo "ACHAT: code=".$key." nb=".$val;
                        for ($i = 0; $i < $val; $i++)
                        {
                            $req = "select magasin_achat_generique($perso_cod,$lieu," . $key . ") as resultat ";
                            $stmt = $pdo->query($req);
                            $result = $stmt->fetch();
                            $resultat .= $result['resultat'];
                        }
                        //echo $result2['resultat'];
                    }
                }
                break;

            case "nv_magasin_vente":
                foreach ($obj as $key => $val)
                {
                    //echo "VENTE: code=".$key." nb=".$val;
                    $req = "select magasin_vente_generique($perso_cod,$lieu,$key) as resultat ";
                    $stmt = $pdo->query($req);
                    $result = $stmt->fetch();
                    $resultat .= $result['resultat'];
                }
                break;

            case "delete_tran":
                $req = "delete from transaction_echoppe where tran_cod = $transaction_cod";
                $stmt = $pdo->query($req);
                $resultat .= "Transaction refusée.";
                break;

            case "valider_tran":
                $req = "select tran_gobj_cod,tran_vendeur,tran_acheteur,tran_prix,tran_quantite,tran_type from transaction_echoppe where tran_cod = $transaction_cod";
                $stmt = $pdo->query($req);
                if (!$result = $stmt->fetch())
                {
                    $resultat .= "Erreur: La transaction n’existe pas.";
                } else
                {
                    $objet_cod = $result['tran_gobj_cod'];
                    $vendeur = $result['tran_vendeur'];
                    $acheteur = $result['tran_acheteur'];
                    $prix = $result['tran_prix'];
                    $quantite = $result['tran_quantite'];
                    $tran_type = $result['tran_type'];
                    $obj_gobj_cod = $result['tran_gobj_cod'];
                    // VERIFICATIONS
                    $erreur = 0;
                    if ($tran_type == 'M2')
                    {
                        $req_verif = "select obj_valeur as prix_mini,obj_gobj_cod from objets where obj_cod = $objet_cod";
                        $stmt = $pdo->query($req_verif);
                        if (!$result = $stmt->fetch())
                        {
                            echo "Erreur: Quantité insuffisante";
                            $erreur = 1;
                        } else
                        {
                            $obj_gobj_cod = $result['obj_gobj_cod'];
                            if ($result['prix_mini'] > $prix)
                            {
                                echo "Erreur: Le prix minimal de cette transaction est de " . $result['prix_mini'] . ".";
                                $erreur = 1;
                            }
                        }
                    } else
                    {
                        $req_verif
                            = "select gobj_valeur*$quantite as prix_mini,mgstock_nombre  as qte_dispo from objet_generique,stock_magasin_generique
						where gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = $vendeur
						and gobj_cod = $objet_cod";
                        $stmt = $pdo->query($req_verif);
                        if (!$result = $stmt->fetch())
                        {
                            echo "Erreur: Quantité insuffisante";
                            $erreur = 1;
                        } else
                        {
                            if ($result['qte_dispo'] < $quantite)
                            {
                                echo "Erreur: Quantité insuffisante";
                                $erreur = 1;
                            }
                            if ($result['prix_mini'] > $prix)
                            {
                                echo "Erreur: Le prix minimal de cette transaction est de " . $result['prix_mini'] . ".";
                                $erreur = 1;
                            }
                        }
                    }
                    if ($acheteur != $perso_cod)
                    {
                        echo "Erreur: Erreur sur l'acheteur !";
                        $erreur = 1;
                    }
                    if ($vendeur != $lieu)
                    {
                        echo "Erreur: Erreur sur le lieu de vente !";
                        $erreur = 1;
                    }
                    $req_verif = "select perso_po from perso where perso_cod = $perso_cod";
                    $stmt = $pdo->query($req_verif);
                    if (!$result = $stmt->fetch())
                    {
                        echo "Erreur: Acheteur non trouvé";
                        $erreur = 1;
                    } else
                    {
                        if ($result['perso_po'] < $prix)
                        {
                            echo "Erreur: Pas assez de Brouzoufs en bourse !";
                            $erreur = 1;
                        }
                    }

                    if ($erreur == 0)
                    {
                        if ($tran_type == 'M2')
                        {
                            echo "Test valider tran !";
                            // On retire les Br
                            $req_tran = "update perso set perso_po = perso_po - $prix where perso_cod = $perso_cod";
                            $stmt = $pdo->query($req_tran);
                            // On les ajoute à la caisse
                            $req_tran = "update lieu set lieu_compte = lieu_compte + $prix where lieu_cod = $lieu";
                            $stmt = $pdo->query($req_tran);
                            // On retire l'objet du stock
                            $req_tran = "delete from stock_magasin where mstock_obj_cod = $objet_cod";
                            $stmt = $pdo->query($req_tran);
                            // On l'ajoute à l'inventaire
                            $req_tran = "insert into perso_objets(perobj_perso_cod,perobj_obj_cod,perobj_identifie) values ($perso_cod,$objet_cod,'O')";
                            $stmt = $pdo->query($req_tran);
                            // Supression de la transaction
                            $req_tran = "delete from transaction_echoppe where tran_gobj_cod = $objet_cod";
                            $stmt = $pdo->query($req_tran);
                            // Ajout de la ligne de Log
                            echo $obj_gobj_cod;
                            $req_tran
                                = "insert into mag_tran_generique
								(mgtra_lieu_cod,mgtra_perso_cod,mgtra_gobj_cod,mgtra_sens,mgtra_montant,mgtra_nombre)
								values ($lieu,$perso_cod,$obj_gobj_cod,4,$prix,1)";
                            $stmt = $pdo->query($req_tran);
                        } else
                        {
                            // Diminution du stock
                            $req = "select magasin_valider_transaction($transaction_cod) as resultat ";
                            $stmt = $pdo->query($req);
                            $result = $stmt->fetch();
                            $resultat .= $result['resultat'];
                            $resultat .= "Transaction acceptée.";
                        }
                    }
                }
                break;

            default:
                //RIEN A FAIRE
                break;
        }
    }
    if (!isset($affichage))
    {
        $affichage = 'entree';
    }
    ?>
    <p>Bonjour aventurier.</p>

    <?php if (TYPE_ECHOPPE != "MAGIE")
{
    $req = "select perso_nom from perso,magasin_gerant where mger_lieu_cod = $lieu and mger_perso_cod = perso_cod ";
    $stmt = $pdo->query($req);
    if($result = $stmt->fetch())
    {
        ?>
        <form name="message" method="post" action="messagerie2.php">
            <input type="hidden" name="m" value="2">
            <input type="hidden" name="n_dest" value="<?php echo $result['perso_nom'] ?>;">
            <input type="hidden" name="dmsg_cod">
        </form>
        <p> Cette échoppe est gérée par <strong><?php echo $result['perso_nom'] ?></strong> (<a
            href="javascript:document.message.submit();">Envoyer un message</a>)
        <?php
    }
}
    if ($controle_gerant == 'OK')
    {
        echo '<li><a href="' . $PHP_SELF . '?methode=mule">Récupérer <strong>un familier mûle</strong> dans votre échoppe ?</a>  <em>(Attention, ceci est une action définitive)</em>';
    }
    ?>


    <form name="echoppe" method="post" action="<?php echo $PHP_SELF; ?>">
        <input type="hidden" name="affichage">
    </form>
    <p>Voulez-vous :</p>
    <ul>
        <li><a href="javascript:document.echoppe.affichage.value='acheter';document.echoppe.submit()">Acheter de
                l'équipement ?</a>
        <li><a href="javascript:document.echoppe.affichage.value='vendre';document.echoppe.submit()">Vendre de
                l'équipement
                ?</a>
            <?php if (TYPE_ECHOPPE != "MAGIE")
            { ?>
        <li><a href="javascript:document.echoppe.affichage.value='identifier';document.echoppe.submit()">Faire
                identifier de
                l'équipement ?</a>
        <li><a href="javascript:document.echoppe.affichage.value='repare';document.echoppe.submit()">Faire réparer de
                l'équipement ?</a>
            <?php
            $req_stock
                = "select count(tran_cod) as num
		  from transaction_echoppe
		where
		tran_acheteur = $perso_cod
		and tran_type IN ('M1','M2')
		and tran_vendeur = $lieu
		";
            $stmt = $pdo->query($req_stock);
            $result = $stmt->fetch();
            $count = $result['num'];
            ?>
        <li>
            <?php
            if ($count > 0)
            {
                echo "<strong> ", $result['num'], " Transactions en attente </strong>";
            }
            ?><a href="javascript:document.echoppe.affichage.value='view_tran';document.echoppe.submit()">Voir les
                transactions ?</a>
            <?php }
            ?>
    </ul>
    <?php
    switch ($affichage)
    {
        case "entree":
            echo "<p><strong>" . $tab_lieu['nom'] . "<strong><br>";
            $desc = str_replace(chr(127), ";", $tab_lieu['description']);
            echo "<em>" . $desc . "</em>";
            break;
        case "acheter":
            
            $req = "select perso_po from perso where perso_cod = $perso_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            ?>
            <HR/><p class="titre">Achat d'équipement</p>
            <p>Vous avez actuellement <strong><?php echo $result['perso_po'] ?></strong> brouzoufs.

                <form name="achat" method="post">
                    <input type="hidden" name="methode" value="nv_magasin_achat">
                    <input type="hidden" name="affichage" value="resultats">
                    <input type="hidden" name="lieu" value="<?php echo $lieu ?>">
                    <input type="hidden" name="objet">
                    <div class="centrer">
                        <table>
                            <tr>
                                <td class="soustitre2"><p><strong>Nom</strong></p></td>
            <td class="soustitre2"><p><strong>Type</strong></p></td>
            <td class="soustitre2"><p><strong><em>Compétence</em></strong></p></td>
            <td class="soustitre2"><p><strong>Prix</strong></p></td>
            <td class="soustitre2"><p><strong>Quantité disponible</strong></p></td>
            <td></td>
            </tr>
            <?php
            $po = $result['perso_po'];
            $lieu_cod = $tab_lieu['lieu_cod'];

            $req
                = "select gobj_cod,gobj_nom,gobj_bonus_cod,tobj_libelle,gobj_tobj_cod,gobj_valeur,mgstock_nombre,f_prix_obj_perso_a_generique($perso_cod,$lieu_cod,gobj_cod) as valeur_achat,comp_libelle
						      from objet_generique,stock_magasin_generique,type_objet,competences
						      where gobj_cod = mgstock_gobj_cod
						      and mgstock_lieu_cod = $lieu_cod
						      and gobj_tobj_cod = tobj_cod
						      and mgstock_vente_persos = 'O'
						      and gobj_comp_cod = comp_cod
						      order by gobj_tobj_cod,gobj_comp_cod,valeur_achat,gobj_nom";

            $stmt = $pdo->query($req);


            if ($stmt->rowCount() == 0)
            {
                ?>
                <tr>
                    <td colspan="5"><p>Désolé, mais les stocks sont vides, nous n'avons rien à vendre en ce moment.</p>
                    </td>
                </tr>
                </table></div>
                </form>
                <?php
            } else
            {
                while ($result = $stmt->fetch())
                {
                    $bonus = "";
                    $prix_bon = 0;
                    $url_bon = "";

                    if (TYPE_ECHOPPE != "MAGIE" && $result['gobj_bonus_cod'])
                    {
                        $req = "SELECT obon_cod,obon_libelle,obon_prix FROM bonus_objets ";
                        $req = $req . "where obon_cod = " . $result['gobj_bonus_cod'];
                        $stmt2 = $pdo->query($req);
                        if ($stmt2->rowCount() != 0)
                        {
                            $result2 = $stmt2->fetch();
                            if (!empty($result2['obon_libelle']))
                            {
                                $bonus = " (" . $result2['obon_libelle'] . ")";
                            }

                            $prix_bon = $result2['obon_prix'];
                            $url_bon = "&bon=" . $result2['obon_cod'];
                        }
                    }
                    $comp = '';
                    if ($result['gobj_tobj_cod'] == 1)
                    {
                        $comp = $result['comp_libelle'];
                    }
                    $prix = $result['gobj_valeur'] + $prix_bon;

                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><p><strong>";
                    echo "<a href=\"visu_desc_objet2.php?objet=" . $result['gobj_cod'] . "&origine=e", $url_bon, "\">";
                    echo $result['gobj_nom'], $bonus;
                    echo "</a>";
                    echo "</strong></td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
                    echo "<td class=\"soustitre2\"><p><em>" . $comp . "</em></td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['valeur_achat'] . " brouzoufs</td>";

                    echo "<td><p>", $result['mgstock_nombre'], "</td>";
                    echo "<td><p>";
                    echo "<input type=\"text\" name=\"gobj[", $result['gobj_cod'], "]\" value=\"0\">";
                    echo "</td>";
                    echo "</tr>\n";
                }
                ?>
                </table></div>
                <input type="submit" class="test centrer" value="Acheter les quantités sélectionnées !">
                </form>
                <!-- ARTICLES EN RESERVE-->
                <?php
            }
            $req
                = "select gobj_cod,gobj_nom,gobj_bonus_cod,tobj_libelle,gobj_tobj_cod,gobj_valeur,mgstock_nombre,f_prix_obj_perso_a_generique($perso_cod,$lieu_cod,gobj_cod) as valeur_achat,comp_libelle
						      from objet_generique,stock_magasin_generique,type_objet,competences
						      where gobj_cod = mgstock_gobj_cod
						      and mgstock_lieu_cod = $lieu_cod
						      and gobj_tobj_cod = tobj_cod
						      and mgstock_vente_persos <> 'O'
						 			and gobj_comp_cod = comp_cod
						      order by gobj_tobj_cod,gobj_comp_cod,valeur_achat,gobj_nom";
            $stmt = $pdo->query($req);

            if ($stmt->rowCount() > 0)
            {
                ?>
                <div class="centrer">
                    <p>Les articles suivants sont en réserve, contactez le gérant de cette échoppe si vous souhaitez les
                        acheter
                        :</p>
                    <table>
                        <tr>
                            <td class="soustitre2"><p><strong>Nom</strong></td>
                            <td class="soustitre2"><p><strong>Type</strong></td>
                            <td class="soustitre2"><p><strong><em>Compétence</em></strong></td>
                            <td class="soustitre2"><p><strong>Quantité disponible</strong></td>
                            <td></td>
                        </tr>
                        <?php while ($result = $stmt->fetch())
                        {
                            $comp = '';
                            if ($result['gobj_tobj_cod'] == 1)
                            {
                                $comp = $result['comp_libelle'];
                            }
                            echo "<tr>";
                            echo "<td class=\"soustitre2\"><p><strong>";
                            echo "<a href=\"visu_desc_objet2.php?objet=" . $result['gobj_cod'] . "&origine=e", $url_bon, "\">";
                            echo $result['gobj_nom'], $bonus;
                            echo "</a>";
                            echo "</strong></td>";
                            echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
                            echo "<td class=\"soustitre2\"><p><em>" . $comp . "</em></td>";
                            echo "<td><p>", $result['mgstock_nombre'], "</td>";
                            echo "</tr>\n";
                        } ?>
                    </table>
                </div>
            <?php } ?>

            <strong>Près du comptoir se trouve une vitrine fermée à clé, c'est là qu'on trouve les objets exceptionnels.
                (Il
                faut
                bien sûr s'adresser au gérant pour en savoir un peu plus)</strong><BR/>
            <div class="centrer">
                <table>
                    <?php
                    $req_stock
                        = "select obj_cod,obj_nom,gobj_cod
from objets,objet_generique,stock_magasin
where mstock_lieu_cod = $lieu_cod
				and mstock_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and obj_nom != gobj_nom
				order by obj_nom";
                    $stmt = $pdo->query($req_stock);
                    if ($stmt->rowCount() == 0)
                    {
                        echo "<tr><td class=\"soustitre2\">Aucun objet remarquable !</td></tr>";
                    } else
                    {
                        while ($result = $stmt->fetch())
                        {
                            echo "<tr><td class=\"soustitre2\"><strong>" . $result['obj_nom'] . "</strong></td></tr>";
                        }
                    }
                    ?>
                </table>
            </div>
            <?php
            break;
        case "vendre":
            
            $taux_rachat = $param->getparm(47);
            $lieu_cod = $tab_lieu['lieu_cod'];
            echo "<HR /><p class=\"titre\">Vente d'équipement</p>";
            $req = "select obj_cod,obj_etat,obj_nom as nom,f_prix_obj_perso_v($perso_cod,$lieu_cod,obj_cod) as valeur,tobj_libelle ";
            $req = $req . "from objet_generique,objets,perso_objets,type_objet ";
            $req = $req . "where perobj_perso_cod = $perso_cod ";
            $req = $req . "and perobj_obj_cod = obj_cod ";
            $req = $req . "and perobj_identifie = 'O' ";
            $req = $req . "and perobj_equipe != 'O' ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_deposable = 'O' ";
            $req = $req . "and gobj_tobj_cod = tobj_cod ";
            if (TYPE_ECHOPPE == "MAGIE")
            {
                $req = $req . "and tobj_cod in (5, 20, 21, 22, 23, 24) ";
            } else if (TYPE_ECHOPPE == "MARCHE_NOIR")
            {
                $req = $req . "and tobj_cod in (1,2,4,5,17,19,25) ";
            } else
            {
                $req = $req . "and tobj_cod in (1,2,4,17,19,25) ";
            }
            $req = $req . "union all
								select obj_cod,obj_etat,obj_nom as nom,f_prix_obj_perso_v($perso_cod,$lieu_cod,obj_cod) as valeur,tobj_libelle
								from objet_generique,objets,perso_objets,type_objet
								where perobj_perso_cod = $perso_cod
								and perobj_obj_cod = obj_cod
								and perobj_equipe != 'O'
								and obj_gobj_cod = gobj_cod
								and obj_deposable = 'O'
								and gobj_echoppe_vente = 'O'
								and gobj_tobj_cod = tobj_cod
								and tobj_cod = 11 ";


            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<p>Vous n'avez aucun équipement à  vendre pour l'instant.</p>";
            } else
            { ?>

                <form name="vente" method="post">
                <input type="hidden" name="methode" value="nv_magasin_vente">
                <input type="hidden" name="affichage" value="resultats">
                <input type="hidden" name="lieu" value="<?php echo $lieu ?>">
                <input type="hidden" name="objet">
                <div class="centrer">
                <table>
                <tr>
                <td class="soustitre2"><p><strong>Nom</strong></td>
                <td class="soustitre2"><p><strong>Type</strong></td>
                <td class="soustitre2"><p><strong>Prix</strong></td>
                <td></td>
                <?php
                while ($result = $stmt->fetch())
                {
                    $req = "SELECT obon_cod,obon_libelle,obon_prix FROM bonus_objets,objets ";
                    $req = $req . "where obj_cod = " . $result['obj_cod'] . " and obj_obon_cod = obon_cod ";
                    $stmt2 = $pdo->query($req);
                    if ($stmt2->rowCount() != 0)
                    {
                        $result2 = $stmt2->fetch();
                        $bonus = " (" . $result2['obon_libelle'] . ")";
                        $prix_bon = $result2['obon_prix'];
                        $url_bon = "&bon=" . $result2['obon_cod'];
                    } else
                    {
                        $bonus = "";
                        $prix_bon = 0;
                        $url_bon = "";
                    }
                    $prix = $result['valeur'] + $prix_bon;
                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><p><strong>" . $result['nom'] . "</strong></td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['valeur'] . " brouzoufs</td>";
                    echo "<td><p><input type=\"checkbox\" name=\"obj[", $result['obj_cod'], "]\"></td>";

                }
                echo "</table></div>";
                echo "<input type=\"submit\" class=\"test centrer\" value=\"Vendre les objets sélectionnées !\">";
                echo "</form>";
            }
            break;
        case "resultats":
            ?>
            <HR/>
            <p><?php echo $resultat ?></p>
            <?php
            break;
        case "view_tran":
            $req = "select perso_po from perso where perso_cod = $perso_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            ?>
            <HR/><p class="titre">Transactions</p> <BR/>
            <p>Vous avez actuellement <strong><?php echo $result['perso_po'] ?></strong> brouzoufs.
            <p><?php echo $resultat ?></p>
            Transactions en cours:
            <form name="cancel_tran" method="post">
                <input type="hidden" name="affichage" value="view_tran">
                <input type="hidden" name="methode" value="delete_tran">
                <input type="hidden" name="transaction_cod" value="">
            </form>
            <form name="valider_tran" method="post">
                <input type="hidden" name="affichage" value="view_tran">
                <input type="hidden" name="methode" value="valider_tran">
                <input type="hidden" name="transaction_cod" value="">
            </form>
            <table width="100%">
                <tr>
                    <td class="soustitre2">Objet</td>
                    <td class="soustitre2">Quantité</td>
                    <td class="soustitre2">Prix</td>
                    <td class="soustitre2">Accepter</td>
                    <td class="soustitre2">Refuser</td>
                </tr>
                <?php
                $req_stock
                    = "select tran_cod,gobj_nom,tran_quantite,tran_prix
  from transaction_echoppe,objet_generique
where
tran_acheteur = $perso_cod
and tran_type = 'M1'
and tran_vendeur = $lieu
and gobj_cod = tran_gobj_cod
";
                $stmt = $pdo->query($req_stock);
                while ($result = $stmt->fetch())
                { ?>
                    <tr>
                        <td class="soustitre2"><?php echo $result['gobj_nom'] ?></td>
                        <td class="soustitre2"><?php echo $result['tran_quantite'] ?></td>
                        <td class="soustitre2"><?php echo $result['tran_prix'] ?></td>
                        <td class="soustitre2"><a
                                    href="javascript:document.valider_tran.transaction_cod.value=<?php echo $result['tran_cod']; ?>;document.valider_tran.submit();">Accepter</a>
                        </td>
                        <td class="soustitre2"><a
                                    href="javascript:document.cancel_tran.transaction_cod.value=<?php echo $result['tran_cod']; ?>;document.cancel_tran.submit();">Refuser</a>
                        </td>
                    </tr>
                    <?php
                }
                $req_stock
                    = "select tran_cod,obj_nom,tran_quantite,tran_prix
  from transaction_echoppe,objets
where
tran_acheteur = $perso_cod
and tran_type = 'M2'
and tran_vendeur = $lieu
and obj_cod = tran_gobj_cod
";
                $stmt = $pdo->query($req_stock);
                while ($result = $stmt->fetch())
                { ?>
                    <tr>
                        <td class="soustitre2"><?php echo $result['obj_nom'] ?></td>
                        <td class="soustitre2"><?php echo $result['tran_quantite'] ?></td>
                        <td class="soustitre2"><?php echo $result['tran_prix'] ?></td>
                        <td class="soustitre2"><a
                                    href="javascript:document.valider_tran.transaction_cod.value=<?php echo $result['tran_cod']; ?>;document.valider_tran.submit();">Accepter</a>
                        </td>
                        <td class="soustitre2"><a
                                    href="javascript:document.cancel_tran.transaction_cod.value=<?php echo $result['tran_cod'] ?>;document.cancel_tran.submit();">Refuser</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>

            </table>
            <?php
            break;
        case "identifier":
            $lieu_cod = $tab_lieu['lieu_cod'];
            echo "<p class=\"titre\">Identification d'équipement</p>";
            $req = "select perso_po from perso where perso_cod = $perso_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>Vous avez actuellement <strong>" . $result['perso_po'] . "</strong> brouzoufs. ";
            $req = "select lieu_marge from lieu where lieu_cod = $lieu_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $prix = $result['lieu_marge'] + 100;
            $req = "select obj_cod,gobj_nom_generique,tobj_libelle ";
            $req = $req . "from objet_generique,objets,perso_objets,type_objet ";
            $req = $req . "where perobj_perso_cod = $perso_cod ";
            $req = $req . "and perobj_obj_cod = obj_cod ";
            $req = $req . "and perobj_identifie != 'O' ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_tobj_cod = tobj_cod ";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<p>Vous n'avez aucun équipement à faire identifier pour l'instant.";
            } else
            {
                echo "<form name=\"identifie\" action=\"action.php\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_identifie\">";
                echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
                echo "<input type=\"hidden\" name=\"objet\">";

                echo "<div class='centrer'><table>";
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Prix</strong></td>";
                echo "<td></td>";
                while ($result = $stmt->fetch())
                {

                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><p><strong>" . $result['gobj_nom_generique'] . "</strong></td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
                    echo "<td class=\"soustitre2\"><p>" . $prix . " brouzoufs</td>";
                    echo "<td><p><input type=\"checkbox\" name=\"obj[", $result['obj_cod'], "]\"></td>";
                }
                echo "</table></div>";
                echo "<input type=\"submit\" class=\"test centrer\" value=\"Identifier les objets sélectionnées !\">";
                echo "</form>";

            }
            break;
        case "repare":
            
            $lieu_cod = $tab_lieu['lieu_cod'];
            echo "<p class=\"titre\">Réparation d'équipement</p>";
            $req = "select perso_po from perso where perso_cod = $perso_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>Vous avez actuellement <strong>" . $result['perso_po'] . "</strong> brouzoufs. ";
            $req = "select obj_cod,obj_etat,gobj_nom as nom,f_prix_objet($lieu_cod,obj_cod) as valeur,tobj_libelle ";
            $req = $req . "from objet_generique,objets,perso_objets,type_objet ";
            $req = $req . "where perobj_perso_cod = $perso_cod ";
            $req = $req . "and perobj_obj_cod = obj_cod ";
            $req = $req . "and perobj_identifie = 'O' ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_deposable = 'O' ";
            $req = $req . "and gobj_tobj_cod = tobj_cod ";
            $req = $req . "and tobj_cod in (1,2,4) ";
            $req = $req . "and obj_etat < 100 ";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<p>Vous n'avez aucun équipement à  réparer pour l'instant.";
            } else
            {
                echo "<form name=\"vente\" action=\"action.php\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_repare\">";
                echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
                echo "<input type=\"hidden\" name=\"objet\">";
                echo "<div class='centrer'><table>";
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Prix</strong></td>";
                echo "<td></td>";
                while ($result = $stmt->fetch())
                {
                    $req = "SELECT obon_cod,obon_libelle,obon_prix FROM bonus_objets,objets ";
                    $req = $req . "where obj_cod = " . $result['obj_cod'] . " and obj_obon_cod = obon_cod ";
                    $stmt2 = $pdo->query($req);
                    if ($stmt2->rowCount() != 0)
                    {
                        $result2 = $stmt2->fetch();
                        $bonus = " (" . $result2['obon_libelle'] . ")";
                        $prix_bon = $result2['obon_prix'];
                        $url_bon = "&bon=" . $result2['obon_cod'];
                    } else
                    {
                        $bonus = "";
                        $prix_bon = 0;
                        $url_bon = "";
                    }
                    $etat = $result['obj_etat'];
                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><p><strong>" . $result['nom'] . "</strong></td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
                    $prix = ($result['valeur'] + $prix_bon) * 0.2 / $modif;
                    $prix = $prix * (100 - $etat);
                    $prix = $prix / 100;
                    echo "<td class=\"soustitre2\"><p>" . floor($prix) . " brouzoufs</td>";
                    echo "<td><p><input type=\"checkbox\" name=\"obj[", $result['obj_cod'], "]\"></td>";

                }
                echo "</table></div>";
                echo "<input type=\"submit\" class=\"test centrer\" value=\"Réparer les objets sélectionnées !\">";
                echo "</form>";
            }


            break;
        default:
            echo "<p>Anomalie : aucune methode passée !";
            break;
    }
}

echo "</form>";


