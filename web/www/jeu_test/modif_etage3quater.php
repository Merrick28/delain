<?php
/* #LAG - +++ 2018-01-25 +++ - Création, modification des lieux */

include "blocks/_header_page_jeu.php";

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';                            //Facilité le developpement avec du jquery
echo '<script src="../scripts/admin_etage_modif3.js"></script>';     // Scripts des traitements des clics dans la map

//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur = 0;
include "blocks/_test_droit_modif_etage.php";

$db2 = new base_delain;
$pdo = new bddpdo;

$log = '';
$resultat = '';

//---------------------------------------------------------------------------------------------------------------------------
// Objectif: 
//		1- Saisie d'un etage source
//		2- Duplication de l'étage (caracs, positions, murs, lieux, etc...)
//---------------------------------------------------------------------------------------------------------------------------

if ($erreur == 0)
{
    //echo "<pre>"; print_r($_POST); echo "</pre>";

    if ((!isset($_POST["dupliquer"])) && (!isset($_POST["supprimer"])) && (!isset($_POST["do_supprimer"])))
    {

        echo "<table width='100%' class='bordiv'><tr><td><p><strong>DUPLICATION D’ETAGE :</strong></p>
        <form method='post' action='$PHP_SELF'>
        <input type='hidden' value='dupliquer' name='dupliquer' />
        Choisir l'étage à dupliquer : <select name='etage'>" . $html->etage_select($admin_etage) ."</select>";
        echo"<br><br><u><strong>Options:</strong></u><br>
            Nom du nouvel étage : <input type='text' name='etage_libelle' size='55'><br>
            Dupliquer aussi les lieux: <input type='checkbox' name='dupliquer_lieux'><br>
            <br><input type='submit' value=\"Dupliquer l'étage\" class='test'/>
            </form></td><td></table>";

        echo "<br><table width='100%' class='bordiv'><tr><td><p><strong>SUPPRESSION D'ETAGE :</strong></p>
        <form method='post' action='$PHP_SELF'>
        <input type='hidden' value='supprimer' name='supprimer' />
        Choisir l'étage à supprimer : <select name='etage'>" . $html->etage_select($admin_etage) ."</select>";
        echo "<br><br> <u>Nota</u>: La suppression n'est pas possible s'il reste des persos à cet étage.<br> Seront supprimés:<br> - L'étage ses caracs, ses positions et murs.<br> - Tous les objets et l'or, qu'il contient<br> - Tous les monstres et PNJ aussi<br> - Tous les lieux de l'étage<br>";
        echo"<br><br><input type='submit' value=\"Supprimer l'étage\" class='test'/>
            </form></td><td></table>";

    }

    // Vérification commune a supprimer et do_supprimer
    if (isset($_POST["supprimer"]) || isset($_POST["do_supprimer"]))
    {
        $erreur_message = "";
        if (!isset($_POST["etage"]))
        {
            $erreur_message.= "Erreur sur la sélection de l'étage à supprimer!!<br>";
        }

        // Charger l'étage à supprimer
        $etage = new etage();
        if (!$etage->getByNumero($_POST["etage"]))
        {
            $erreur_message.= "Impossible de charger l'étage à supprimer!!<br>";
        }

        // Compter les persos encore sur l'étage
        $req ="SELECT count(*) count_total, sum(case when perso_actif='O' then 1 else 0 end) count_actif from perso_position join positions on pos_cod=ppos_pos_cod join perso on perso_cod=ppos_perso_cod
                      WHERE pos_etage = :pos_etage and perso_type_perso=1 and perso_pnj!=1 ; ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
        if (!$result = $stmt->fetch())
        {
            $erreur_message.= "Impossible de vérifier les perso sur l'étage!!<br>";
        }
        else
        {
            if ((int)$result["count_total"]>0)
            {
                $erreur_message.= "Impossible de supprimer l'étage, il contient encore {$result['count_total']} perso(s) dont {$result['count_actif']} actif(s)!!<br>";
            }
        }
    }

    if (isset($_POST["supprimer"]))
    {
        // on s'assure que l'on peut supprimer et l'on demande confirmation
        echo "<table width='100%' class='bordiv'><tr><td><p><strong>CONFIRMATION SUPPRESSION D’ETAGE :</strong></p><tr><td>";
        if ($erreur_message != "")
        {
            echo "<br><strong>Erreur lors de la suppression d'étage:</strong><br><br>$erreur_message ";
        }
        else
        {
            Echo "Etage # <strong>{$etage->etage_numero}</strong> - <strong>{$etage->etage_libelle}</strong><br><br>";
            Echo "L'étage contient:<br>";
            // Compter les monstres encore sur l'étage
            $req ="SELECT count(*) count_total, sum(case when perso_actif='O' then 1 else 0 end) count_actif from perso_position join positions on pos_cod=ppos_pos_cod join perso on perso_cod=ppos_perso_cod
                      WHERE pos_etage = :pos_etage and (perso_type_perso=3 or perso_pnj=1); ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Monstres ou PNJ: <strong>{$result['count_total']}</strong> dont <strong>".(int)$result['count_actif']."</strong> actif(s)<br>";
            }

            // Compter les objets encore sur l'étage
            $req ="SELECT count(*) count_total from objet_position join positions on pos_cod=pobj_pos_cod WHERE pos_etage = :pos_etage ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Objets: <strong>{$result['count_total']}</strong><br>";
            }

            // Compter l'or
            $req ="SELECT sum(por_qte) sum_total from or_position join positions on pos_cod=por_pos_cod WHERE pos_etage = :pos_etage ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Or: <strong>{$result['sum_total']}</strong> Bz<br>";
            }

            echo "<br><strong>Voulez-vous vraiment supprimer cet étage?</strong><br><br>
                <form method='post' action='{$PHP_SELF}'>
                <input type='hidden' value='do_supprimer' name='do_supprimer' />
                <input type='hidden' value='{$_POST['etage']}' name='etage' />
                <input type='submit' value=\"Supprimer l'étage\" class='test'/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"{$PHP_SELF}\"><input type='button' value=\"NON!!!\" class='test'/></a></form>";

        }
        echo "</td><td></table>";

        echo '<br><div style="text-align:center"><a href="modif_etage3quater.php">Retour à la page de duplication / suppression</a></div>';
    }

    // Menu Supression d'étage----------------------------------------------------------------
    if (isset($_POST["do_supprimer"]))
    {
        echo "<table width='100%' class='bordiv'><tr><td><p><strong>SUPPRESSION D’ETAGE :</strong></p><tr><td>";

        if ($erreur_message != "")
        {
            echo "<br><strong>Erreur lors de la suppression d'étage:</strong><br><br>$erreur_message ";
        }
        else
        {
            echo "Suppression des monstres et PNJ de l'étage...<br>";
            $req ="SELECT efface_perso(perso_cod) from perso_position join positions on pos_cod=ppos_pos_cod join perso on perso_cod=ppos_perso_cod
                      WHERE pos_etage = :pos_etage and (perso_type_perso=3 or perso_pnj=1); ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            echo "Suppression des objets de l'étage...<br>";
            $req ="SELECT f_del_objet(pobj_obj_cod) from objet_position join positions on pos_cod=pobj_pos_cod WHERE pos_etage = :pos_etage ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            echo "Suppression de l'or de l'étage...<br>";
            $req ="DELETE from or_position using positions where pos_cod=por_pos_cod and pos_etage = :pos_etage ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            // On poursuit avec la supression des lieux !
            // Boucle sur les lieux à supprimer
            echo "Suppression des lieux de l'étage...<br>";
            $req ="SELECT lieu_cod, lpos_cod from lieu_position 
                      join lieu on lieu_cod=lpos_lieu_cod 
                      join positions on pos_cod=lpos_pos_cod
                      WHERE pos_etage = :pos_etage; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            while ($result = $stmt->fetch())
            {
                $req ="DELETE FROM lieu_position where lpos_cod = :lpos_cod ; ";
                $stmt2   = $pdo->prepare($req);
                $stmt2   = $pdo->execute(array(":lpos_cod" => $result["lpos_cod"]), $stmt2);

                $req ="DELETE FROM lieu where lieu_cod = :lieu_cod ; ";
                $stmt2   = $pdo->prepare($req);
                $stmt2   = $pdo->execute(array(":lieu_cod" => $result["lieu_cod"]), $stmt2);
            }

            echo "Suppression de l'automap...<br>";
            $req ="drop table perso_vue_pos_".$etage->etage_cod.";";
            $stmt   = $pdo->prepare($req);
            $pdo->query($req);

            // On commence par supprimer les murs !
            echo "Suppression des murs de l'étage...<br>";
            $req ="DELETE FROM murs where mur_pos_cod in (select pos_cod from positions where pos_etage = :pos_etage) ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            // supression des positions !
            echo "Suppression des positions de l'étage...<br>";
            $req ="DELETE FROM positions where pos_etage = :pos_etage ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            // supression des caracs d'arène !
            echo "Suppression des caracs d'arène (si c'est le cas)...<br>";
            $req ="DELETE FROM carac_arene where carene_etage_numero = :carene_etage_numero ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":carene_etage_numero" => $etage->etage_numero), $stmt);

            // supression de la repartition des monstres !
            echo "Suppression dela réartition des monstres...<br>";
            $req ="DELETE FROM repart_monstre where rmon_etage_cod = :rmon_etage_cod ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":rmon_etage_cod" => $etage->etage_numero), $stmt);

            // Puis l'étage lui-même !
            echo "Suppression des caracs de l'étage...<br>";
            $req ="DELETE FROM etage where etage_cod = :etage_cod ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":etage_cod" => $etage->etage_cod), $stmt);

            // Loguer pour le suivi admin
            $req ="select compt_nom from compte where compt_cod = :compt_cod ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
            $result = $stmt->fetch();

            $log = date("d/m/y - H:i") . "\tCompte ".$result["compt_nom"]." ($compt_cod)\t";
            $log.= "Suppression de l'étage #{$etage->etage_numero} - {$etage->etage_libelle}";
            writelog($log, 'lieux_etages');

            echo "L'étage a été supprimé.<br>";
        }
        echo "</td><td></table>";

        echo '<br><div style="text-align:center"><a href="modif_etage3quater.php">Retour à la page de duplication / suppression</a></div>';
    }

    // Menu Duplication d'étage----------------------------------------------------------------
    if (isset($_POST["dupliquer"]))
    {

        echo "<table width='100%' class='bordiv'><tr><td><p><strong>DUPLICATION D’ETAGE :</strong></p><tr><td>";

        $erreur_message = "";
        if (!isset($_POST["etage"]))
        {
            $erreur_message.= "Erreur sur la sélection de l'étage à dupliquer!!<br>";
        }
        if ((!isset($_POST["etage_libelle"]) || $_POST["etage_libelle"]==""))
        {
            $erreur_message.= "Le nom du nouvel étage n'est pas défini!!<br>";
        }

        // Vérifier que le nom du nouvel étage n'existe pas déjà
        $etage = new etage();
        if ($etage->getBy_etage_libelle($_POST["etage_libelle"]))
        {
            $erreur_message.= "Le nom du nouvel étage existe déjà!!<br>";
        }

        // Charger l'étage a dupliquer
        if (!$etage->getByNumero($_POST["etage"]))
        {
            $erreur_message.= "Impossible de charger l'étage à dupliquer ({$_POST["etage"]})!!<br>";
        }

        // Si tout est bon, on calcul un _cod pour le nouvel étage
        if ($erreur_message == "")
        {
            $req = "select nextval('seq_etage_cod') as etage_cod";
            $stmt = $pdo->query($req);
            if(!$result = $stmt->fetch())
            {
                $erreur_message.= "Erreur lors de la récupération de etage_cod!!<br>";
            }
            else
            {
                $etage_cod = $result["etage_cod"];
            }

        }

        if ($erreur_message != "")
        {
            echo "<br><strong>Erreur lors de la duplication d'étage:</strong><br><br>$erreur_message ";
        }
        else
        {
            // Faire la duplication (d'abord les caracs de l'etage)
            echo "Duplication des caracteristiques de l'étage...<br>";
            $req ="INSERT INTO etage(
                        etage_cod, etage_numero, etage_libelle, etage_reference, etage_description,
                        etage_affichage, etage_mort, etage_arene, etage_mine, etage_retour_rune_monstre,
                        etage_mine_type, etage_mine_richesse, etage_quatrieme_perso,
                        etage_quatrieme_mortel, etage_type_arene, etage_familier_actif,
                        etage_duree_imp_p, etage_duree_imp_f, etage_autor_rappel_cot,
                        etage_autor_glyphe, etage_perte_xp) 
                    SELECT :etage_cod, :etage_numero, :etage_libelle::text as etage_libelle, etage_reference, etage_description, 
                        etage_affichage, etage_mort, etage_arene, etage_mine, etage_retour_rune_monstre, 
                        etage_mine_type, etage_mine_richesse, etage_quatrieme_perso, 
                        etage_quatrieme_mortel, etage_type_arene, etage_familier_actif, 
                        etage_duree_imp_p, etage_duree_imp_f, etage_autor_rappel_cot, 
                        etage_autor_glyphe, etage_perte_xp
                        FROM etage where etage_cod = :ref_etage_cod ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":etage_cod" => $etage_cod, ":etage_numero" => $etage_cod, ":etage_libelle" => $_POST["etage_libelle"] ,":ref_etage_cod" => $etage->etage_cod), $stmt);

            // Duplication dans la table des positions
            echo "Duplication des positions...<br>";
            $req ="INSERT INTO positions(
                        pos_x, pos_y, pos_etage, pos_key, pos_type_aff, pos_magie,
                        pos_decor, pos_decor_dessus, pos_fonction_arrivee, pos_passage_autorise,
                        pos_modif_pa_dep, pos_fonction_dessus, pos_entree_arene, pos_anticipation,
                        pos_pvp)
                      SELECT pos_x, pos_y, :pos_etage, pos_key, pos_type_aff, pos_magie, 
                        pos_decor, pos_decor_dessus, pos_fonction_arrivee, pos_passage_autorise, 
                        pos_modif_pa_dep, pos_fonction_dessus, pos_entree_arene, pos_anticipation, 
                        pos_pvp
                        FROM positions where pos_etage = :ref_pos_etage ;";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);

            // Duplication des murs
            echo "Duplication des murs...<br>";
            $req ="INSERT INTO murs( mur_pos_cod, mur_type, mur_tangible, mur_creusable, mur_usure,  mur_richesse)
                      SELECT p2.pos_cod as mur_pos_cod, mur_type, mur_tangible, mur_creusable, mur_usure, mur_richesse
                      FROM murs join positions p1 on mur_pos_cod=p1.pos_cod join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage 
                      WHERE p1.pos_etage = :ref_pos_etage; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);

            // Duplication des caracs d'arènes (si c'est le cas)
            echo "Duplication des caracs d'arène (si c'est le cas)...<br>";
            $req ="INSERT INTO carac_arene( carene_etage_numero, carene_level_max, carene_ouverte)
                      SELECT :carene_etage_numero, carene_level_max, carene_ouverte
                      FROM carac_arene  
                      WHERE carene_etage_numero = :ref_pos_etage; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":carene_etage_numero" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);

            // Duplication de la répartition de monstre
            echo "Duplication de la répartition des monstres...<br>";
            $req ="INSERT INTO repart_monstre( rmon_gmon_cod, rmon_etage_cod, rmon_poids, rmon_max)
                      SELECT rmon_gmon_cod, :rmon_etage_cod, rmon_poids, rmon_max
                      FROM repart_monstre  
                      WHERE rmon_etage_cod = :ref_pos_etage; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":rmon_etage_cod" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);

            echo "Creation de l'automap...<br>";
            $req ="create table perso_vue_pos_".$etage_cod." (  pvue_perso_cod INT not null, pvue_pos_cod INT not null )";
            $stmt   = $pdo->prepare($req);
            $pdo->query($req);
            $req = "ALTER TABLE perso_vue_pos_".$etage_cod." ADD CONSTRAINT pk_perso_vue_pos_".$etage_cod." PRIMARY KEY (pvue_perso_cod, pvue_pos_cod)";
            $stmt   = $pdo->prepare($req);
            $pdo->query($req);
            $req = "ALTER TABLE perso_vue_pos_".$etage_cod." ADD CONSTRAINT fk_pvue_perso_cod".$etage_cod." FOREIGN KEY (pvue_perso_cod) REFERENCES perso (perso_cod) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;";
            $stmt   = $pdo->prepare($req);
            $pdo->query($req);
            $req = "ALTER TABLE perso_vue_pos_".$etage_cod." OWNER TO delain;";
            $stmt   = $pdo->prepare($req);
            $pdo->query($req);

            // Maintenant que les table sont créé ont met à jour l'automap
            $req = "select init_automap($etage_cod) ";
            $stmt   = $pdo->prepare($req);
            $pdo->query($req);

            //dupliquer_lieux
            if (isset($_POST["dupliquer_lieux"]))
            {
                echo "Duplication des lieux...<br>";
                $lieu = new lieu();

                // Boucle sur les lieux a dupliquer
                $req ="SELECT lieu_cod, p2.pos_cod pos_cod from lieu_position 
                      join lieu on lieu_cod=lpos_lieu_cod 
                      join positions p1 on p1.pos_cod=lpos_pos_cod
                      join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                      WHERE p1.pos_etage = :ref_pos_etage; ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);
                while ($result = $stmt->fetch())
                {
                    $lieu->charge($result["lieu_cod"]);
                    // vérification du ciblage des lieux de destination sur l'étage lui même
                    if ($lieu->lieu_dest>0)
                    {
                        $req ="SELECT p2.pos_cod pos_cod from positions p1
                              join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                              WHERE p1.pos_cod=:pos_cod and p1.pos_etage = :ref_pos_etage; ";
                        $stmt2   = $pdo->prepare($req);
                        $stmt2   = $pdo->execute(array(":pos_cod" => $lieu->lieu_dest, ":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt2);
                        if ($result2 = $stmt2->fetch())
                        {
                            // Si la destination du lieu était vers l'étage dupliqué on recalibre sur la copie !
                            $lieu->lieu_dest = $result2["pos_cod"];
                        }
                    }
                    $lieu->stocke(true);    // Dupliquer

                    $lieu_position = new lieu_position();
                    $lieu_position->lpos_lieu_cod = $lieu->lieu_cod ;
                    $lieu_position->lpos_pos_cod = $result["pos_cod"] ;
                    $lieu_position->stocke(true);    // Créer nouveau !
                }
            }

            // Loguer pour le suivi admin
            $req ="select compt_nom from compte where compt_cod = :compt_cod ; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
            $result = $stmt->fetch();

            $log = date("d/m/y - H:i") . "\tCompte ".$result["compt_nom"]." ($compt_cod)\t";
            $log.= "Duplication de l'étage #{$etage->etage_numero} - {$etage->etage_libelle} vers etage #{$etage_cod} - ".$_POST["etage_libelle"];
            writelog($log, 'lieux_etages');

            echo "L'étage a été dupliqué.<br>";

        }
        echo "</td><td></table>";
        echo '<br><div style="text-align:center"><a href="modif_etage3quater.php">Retour à la page de duplication / suppression</a></div>';
    }


    //---------------------------------------------------------------------------------------------------------------------------
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";


