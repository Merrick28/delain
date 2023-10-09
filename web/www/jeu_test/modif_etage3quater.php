<?php
/* #LAG - +++ 2018-01-25 +++ - Création, modification des lieux */

include "blocks/_header_page_jeu.php";
$compte = $verif_connexion->compte;
echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';                            //Facilité le developpement avec du jquery
echo '<script src="../scripts/admin_etage_modif3.js"></script>';     // Scripts des traitements des clics dans la map

//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur  = 0;
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";


$pdo = new bddpdo;

$log      = '';
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
        $phpself = $_SERVER['PHP_SELF'];
        echo "<table width='100%' class='bordiv'><tr><td><p><strong>DUPLICATION D’ETAGE :</strong></p>
        <form method='post' action='$phpself'>
        <input type='hidden' value='dupliquer' name='dupliquer' />
        Choisir l'étage à dupliquer : <select name='etage'>" . $html->etage_select($admin_etage) . "</select>";
        echo "<br><br><u><strong>Options:</strong></u><br>
            Nom du nouvel étage : <input type='text' name='etage_libelle' size='55'><br>
            Dupliquer aussi les lieux: <input type='checkbox' name='dupliquer_lieux'><br>
            Dupliquer aussi les mécanismes, les EA et les QA d'étage: <input type='checkbox' name='dupliquer_meca'><br>
            <br><input type='submit' value=\"Dupliquer l'étage\" class='test'/>
            </form></td><td></table>";

        echo "<br><table width='100%' class='bordiv'><tr><td><p><strong>SUPPRESSION D'ETAGE :</strong></p>
        <form method='post' action='$phpself'>
        <input type='hidden' value='supprimer' name='supprimer' />
        Choisir l'étage à supprimer : <select name='etage'>" . $html->etage_select($admin_etage) . "</select>";
        echo "<br><br> <u>Nota</u>: La suppression n'est pas possible s'il reste des persos à cet étage.<br> Seront supprimés:<br> - L'étage ses caracs, ses positions et murs.<br> - Tous les objets et l'or, qu'il contient<br> - Tous les monstres et PNJ aussi<br> - Tous les lieux de l'étage<br> - Tous les mécanismes, EA et QA d'étage<br>";
        echo "<br><br><input type='submit' value=\"Supprimer l'étage\" class='test'/>
            </form></td><td></table>";

    }

    // Vérification commune a supprimer et do_supprimer
    if (isset($_POST["supprimer"]) || isset($_POST["do_supprimer"]))
    {
        $erreur_message = "";
        if (!isset($_POST["etage"]))
        {
            $erreur_message .= "Erreur sur la sélection de l'étage à supprimer!!<br>";
        }

        // Charger l'étage à supprimer
        $etage = new etage();
        if (!$etage->getByNumero($_POST["etage"]))
        {
            $erreur_message .= "Impossible de charger l'étage à supprimer!!<br>";
        }

        // Compter les persos encore sur l'étage
        $req  = "SELECT count(*) count_total, sum(case when perso_actif='O' then 1 else 0 end) count_actif from perso_position join positions on pos_cod=ppos_pos_cod join perso on perso_cod=ppos_perso_cod
                      WHERE pos_etage = :pos_etage and perso_type_perso=1 and perso_pnj!=1 ; ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
        if (!$result = $stmt->fetch())
        {
            $erreur_message .= "Impossible de vérifier les perso sur l'étage!!<br>";
        } else
        {
            if ((int)$result["count_total"] > 0)
            {
                $erreur_message .= "Impossible de supprimer l'étage, il contient encore {$result['count_total']} perso(s) dont {$result['count_actif']} actif(s)!!<br>";
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
        } else
        {
            Echo "Etage # <strong>{$etage->etage_numero}</strong> - <strong>{$etage->etage_libelle}</strong><br><br>";
            Echo "L'étage contient:<br>";
            // Compter les monstres encore sur l'étage
            $req  = "SELECT count(*) count_total, sum(case when perso_actif='O' then 1 else 0 end) count_actif from perso_position join positions on pos_cod=ppos_pos_cod join perso on perso_cod=ppos_perso_cod
                      WHERE pos_etage = :pos_etage and (perso_type_perso=3 or perso_pnj=1); ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Monstres ou PNJ: <strong>{$result['count_total']}</strong> dont <strong>" . (int)$result['count_actif'] . "</strong> actif(s)<br>";
            }

            // Compter les objets encore sur l'étage
            $req  =
                "SELECT count(*) count_total from objet_position join positions on pos_cod=pobj_pos_cod WHERE pos_etage = :pos_etage ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Objets: <strong>{$result['count_total']}</strong><br>";
            }

            // Compter l'or
            $req  =
                "SELECT sum(por_qte) sum_total from or_position join positions on pos_cod=por_pos_cod WHERE pos_etage = :pos_etage ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Or: <strong>".(1*$result['sum_total'])."</strong> Bz<br>";
            }

            // Compter les mécas
            $req  =
                "SELECT count(*) count_total from meca WHERE meca_pos_etage = :pos_etage ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Mécanisme: <strong>{$result['count_total']}</strong><br>";
            }

            // Compter les ea
            $req = "select count(*) as count_total from fonction_specifique
                        where   fonc_gmon_cod is null 
                            and fonc_perso_cod is null 
                            and fonc_type='POS'
                            and fonc_trigger_param->>'fonc_trig_pos_etage'::text=".((int)$etage->etage_numero);
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Effet-auto: <strong>{$result['count_total']}</strong><br>";
            }

            // Compter les qa
            $req = "select count(*) count_total from quetes.aquete where aquete_pos_etage = :pos_etage ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            if ($result = $stmt->fetch())
            {
                Echo "Quete-auto: <strong>{$result['count_total']}</strong><br>";
            }



            echo "<br><strong>Voulez-vous vraiment supprimer cet étage?</strong><br><br>
                <form method='post' action='{$_SERVER['PHP_SELF']}'>
                <input type='hidden' value='do_supprimer' name='do_supprimer' />
                <input type='hidden' value='{$_POST['etage']}' name='etage' />
                <input type='submit' value=\"Supprimer l'étage\" class='test'/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"{$_SERVER['PHP_SELF']}\"><input type='button' value=\"NON!!!\" class='test'/></a></form>";

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
        } else
        {
            echo "Suppression des monstres et PNJ de l'étage...<br>";
            $req  = "SELECT efface_perso(perso_cod) from perso_position join positions on pos_cod=ppos_pos_cod join perso on perso_cod=ppos_perso_cod
                      WHERE pos_etage = :pos_etage and (perso_type_perso=3 or perso_pnj=1); ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            echo "Suppression des objets de l'étage...<br>";
            $req  =
                "SELECT f_del_objet(pobj_obj_cod) from objet_position join positions on pos_cod=pobj_pos_cod WHERE pos_etage = :pos_etage ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            echo "Suppression de l'or de l'étage...<br>";
            $req  = "DELETE from or_position using positions where pos_cod=por_pos_cod and pos_etage = :pos_etage ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            // On poursuit avec la supression des lieux !
            // Boucle sur les lieux à supprimer
            echo "Suppression des lieux de l'étage...<br>";
            $req  = "SELECT lieu_cod, lpos_cod from lieu_position 
                      join lieu on lieu_cod=lpos_lieu_cod 
                      join positions on pos_cod=lpos_pos_cod
                      WHERE pos_etage = :pos_etage; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);
            while ($result = $stmt->fetch())
            {
                $req   = "DELETE FROM lieu_position where lpos_cod = :lpos_cod ; ";
                $stmt2 = $pdo->prepare($req);
                $stmt2 = $pdo->execute(array(":lpos_cod" => $result["lpos_cod"]), $stmt2);

                $req   = "DELETE FROM lieu where lieu_cod = :lieu_cod ; ";
                $stmt2 = $pdo->prepare($req);
                $stmt2 = $pdo->execute(array(":lieu_cod" => $result["lieu_cod"]), $stmt2);
            }

            echo "Suppression de l'automap...<br>";
            $req  = "drop table perso_vue_pos_" . $etage->etage_cod . ";";
            $stmt = $pdo->prepare($req);
            $pdo->query($req);

            // On commence par supprimer les murs !
            echo "Suppression des murs de l'étage...<br>";
            $req  =
                "DELETE FROM murs where mur_pos_cod in (select pos_cod from positions where pos_etage = :pos_etage) ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            // supression des positions !
            echo "Suppression des positions de l'étage...<br>";
            $req  = "DELETE FROM positions where pos_etage = :pos_etage ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            // supression des caracs d'arène !
            echo "Suppression des caracs d'arène (si c'est le cas)...<br>";
            $req  = "DELETE FROM carac_arene where carene_etage_numero = :carene_etage_numero ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":carene_etage_numero" => $etage->etage_numero), $stmt);

            // supression de la repartition des monstres !
            echo "Suppression dela réartition des monstres...<br>";
            $req  = "DELETE FROM repart_monstre where rmon_etage_cod = :rmon_etage_cod ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":rmon_etage_cod" => $etage->etage_numero), $stmt);

            // supression des mécas !
            echo "Suppression dela réartition des mécanismes...<br>";
            $req  = "DELETE from meca WHERE meca_pos_etage = :pos_etage ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            // supression des EA !
            echo "Suppression dela réartition des EA...<br>";
            $req  = "DELETE from fonction_specifique where fonc_gmon_cod is null and fonc_perso_cod is null and fonc_type='POS' and fonc_trigger_param->>'fonc_trig_pos_etage'::text=".((int)$etage->etage_numero) ."; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(), $stmt);

            // supression des QA !
            echo "Suppression dela réartition des QA...<br>";
            $req  = "DELETE from quetes.aquete WHERE aquete_pos_etage = :pos_etage ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage->etage_numero), $stmt);

            // Puis l'étage lui-même !
            echo "Suppression des caracs de l'étage...<br>";
            $req  = "DELETE FROM etage where etage_cod = :etage_cod ; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":etage_cod" => $etage->etage_cod), $stmt);

            // Loguer pour le suivi admin
            $log = date("d/m/y - H:i") . "\tCompte " . $compte->compt_nom . " ($compt_cod)\t";
            $log .= "Suppression de l'étage #{$etage->etage_numero} - {$etage->etage_libelle}";
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
            $erreur_message .= "Erreur sur la sélection de l'étage à dupliquer!!<br>";
        }
        if ((!isset($_POST["etage_libelle"]) || $_POST["etage_libelle"] == ""))
        {
            $erreur_message .= "Le nom du nouvel étage n'est pas défini!!<br>";
        }

        // Vérifier que le nom du nouvel étage n'existe pas déjà
        $etage = new etage();
        if ($etage->getBy_etage_libelle($_POST["etage_libelle"]))
        {
            $erreur_message .= "Le nom du nouvel étage existe déjà!!<br>";
        }

        // Charger l'étage a dupliquer
        if (!$etage->getByNumero($_POST["etage"]))
        {
            $erreur_message .= "Impossible de charger l'étage à dupliquer ({$_POST["etage"]})!!<br>";
        }

        // Si tout est bon, on calcul un _cod pour le nouvel étage
        if ($erreur_message == "")
        {
            $req  = "select nextval('seq_etage_cod') as etage_cod";
            $stmt = $pdo->query($req);
            if (!$result = $stmt->fetch())
            {
                $erreur_message .= "Erreur lors de la récupération de etage_cod!!<br>";
            } else
            {
                $etage_cod = $result["etage_cod"];
            }

        }

        if ($erreur_message != "")
        {
            echo "<br><strong>Erreur lors de la duplication d'étage:</strong><br><br>$erreur_message ";
        } else
        {
            // Faire la duplication (d'abord les caracs de l'etage)
            echo "Duplication des caracteristiques de l'étage...<br>";
            $req  = "INSERT INTO etage(
                        etage_cod, etage_numero, etage_libelle, etage_reference, etage_description,
                        etage_affichage, etage_mort, etage_arene, etage_mine, etage_retour_rune_monstre,
                        etage_mine_type, etage_mine_richesse, etage_quatrieme_perso,
                        etage_quatrieme_mortel, etage_type_arene, etage_familier_actif,
                        etage_duree_imp_p, etage_duree_imp_f, etage_autor_rappel_cot,
                        etage_autor_glyphe, etage_perte_xp, etage_monture) 
                    SELECT :etage_cod, :etage_numero, :etage_libelle::text as etage_libelle, etage_reference, etage_description, 
                        etage_affichage, etage_mort, etage_arene, etage_mine, etage_retour_rune_monstre, 
                        etage_mine_type, etage_mine_richesse, etage_quatrieme_perso, 
                        etage_quatrieme_mortel, etage_type_arene, etage_familier_actif, 
                        etage_duree_imp_p, etage_duree_imp_f, etage_autor_rappel_cot, 
                        etage_autor_glyphe, etage_perte_xp, etage_monture
                        FROM etage where etage_cod = :ref_etage_cod ; ";
            $stmt = $pdo->prepare($req);
            $stmt =
                $pdo->execute(array(":etage_cod" => $etage_cod, ":etage_numero" => $etage_cod, ":etage_libelle" => $_POST["etage_libelle"], ":ref_etage_cod" => $etage->etage_cod), $stmt);

            // Duplication dans la table des positions (attention on duplique l'tage avec ses positions de base, des mecanisme peuvent avoir changé l'état courrant)
            echo "Duplication des positions...<br>";
            $req  = "INSERT INTO positions(
                        pos_x, pos_y, pos_etage, pos_key, pos_type_aff, pos_magie,
                        pos_decor, pos_decor_dessus, pos_fonction_arrivee, pos_passage_autorise,
                        pos_modif_pa_dep, pos_ter_cod, pos_fonction_dessus, pos_entree_arene, pos_anticipation,
                        pos_pvp)
                      SELECT pos_x, pos_y, :pos_etage, pos_key, 
                        coalesce(pmeca_base_pos_type_aff, pos_type_aff) as pos_type_aff, 
                        pos_magie, 
                        coalesce(pmeca_base_pos_decor, pos_decor) as pos_decor, 
                        coalesce(pmeca_base_pos_decor_dessus, pos_decor_dessus) as pos_decor_dessus, 
                        pos_fonction_arrivee, 
                        coalesce(pmeca_base_pos_passage_autorise, pos_passage_autorise) as pos_passage_autorise, 
                        coalesce(coalesce(pmeca_base_pos_modif_pa_dep,pos_modif_pa_dep), 0) as pos_modif_pa_dep, 
                        coalesce(coalesce(pmeca_base_pos_ter_cod,pos_ter_cod), 0) as pos_ter_cod, 
                        pos_fonction_dessus, 
                        pos_entree_arene, 
                        pos_anticipation, 
                        pos_pvp
                        FROM positions 
                        left outer join (select distinct pmeca_pos_cod, pmeca_base_pos_decor, pmeca_base_pos_type_aff,pmeca_base_pos_decor_dessus,pmeca_base_pos_passage_autorise,pmeca_base_pos_modif_pa_dep,pmeca_base_pos_ter_cod, pmeca_base_mur_type, pmeca_base_mur_tangible, pmeca_base_mur_illusion from meca_position where pmeca_actif=1 and pmeca_pos_etage = :ref_pos_etage) as mpp on pmeca_pos_cod=pos_cod
                        where pos_etage = :ref_pos_etage ;";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);

            // Duplication des murs
            /* prenre en compte les murs là :
                    - où il n'y en a actuellment et où il n'y a pas de mecanisme actif
                    - où il y en a actuellement et où avec le mécanisme actif le mur est encore la
                    - où il n'y a pas de mur parcequ'un mecanisme actif l'a supprimé*/
            echo "Duplication des murs...<br>";
            $req  = "INSERT INTO murs( mur_pos_cod, mur_type, mur_tangible, mur_creusable, mur_usure,  mur_richesse, mur_illusion)
                      SELECT p2.pos_cod as mur_pos_cod, 
                          coalesce(CASE WHEN pmeca_pos_cod IS NOT NULL THEN pmeca_base_mur_type ELSE mur_type END, 0) as mur_type, 
                          coalesce(CASE WHEN pmeca_pos_cod IS NOT NULL THEN pmeca_base_mur_tangible ELSE mur_tangible END, 'O') as mur_tangible, 
                          coalesce(CASE WHEN pmeca_pos_cod IS NOT NULL THEN null ELSE mur_creusable END, 'N') as mur_creusable, 
                          coalesce(mur_usure, 1000) as mur_usure, 
                          coalesce(mur_richesse, 100) as mur_richesse,
                          coalesce(CASE WHEN pmeca_pos_cod IS NOT NULL THEN pmeca_base_mur_illusion ELSE mur_illusion END, 'N') as mur_illusion
                      FROM positions p1
                      join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                      left outer join murs on mur_pos_cod=p1.pos_cod 
                      left outer join (select distinct pmeca_pos_cod, meca_mur_type, pmeca_base_mur_type, pmeca_base_mur_tangible, pmeca_base_mur_illusion 
                                          from meca_position join meca on meca_cod=pmeca_meca_cod where pmeca_actif=1 and pmeca_pos_etage = :ref_pos_etage) as mpp on pmeca_pos_cod=p1.pos_cod 
                      WHERE p1.pos_etage = :ref_pos_etage and (
                                       (mur_pos_cod is not null and pmeca_base_mur_type is null) 
                                    or (mur_pos_cod is not null and pmeca_base_mur_type>0) 
                                    or (mur_pos_cod is null and meca_mur_type=-1)
                             ); ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);

            // Duplication des caracs d'arènes (si c'est le cas)
            echo "Duplication des caracs d'arène (si c'est le cas)...<br>";
            $req  = "INSERT INTO carac_arene( carene_etage_numero, carene_level_max, carene_level_min, carene_ouverte)
                      SELECT :carene_etage_numero, carene_level_max, carene_level_min, carene_ouverte
                      FROM carac_arene  
                      WHERE carene_etage_numero = :ref_pos_etage; ";
            $stmt = $pdo->prepare($req);
            $stmt =
                $pdo->execute(array(":carene_etage_numero" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);

            // Duplication de la répartition de monstre
            echo "Duplication de la répartition des monstres...<br>";
            $req  = "INSERT INTO repart_monstre( rmon_gmon_cod, rmon_etage_cod, rmon_poids, rmon_max)
                      SELECT rmon_gmon_cod, :rmon_etage_cod, rmon_poids, rmon_max
                      FROM repart_monstre  
                      WHERE rmon_etage_cod = :ref_pos_etage; ";
            $stmt = $pdo->prepare($req);
            $stmt =
                $pdo->execute(array(":rmon_etage_cod" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);

            echo "Creation de l'automap...<br>";
            $req  =
                "create table perso_vue_pos_" . $etage_cod . " (  pvue_perso_cod INT not null, pvue_pos_cod INT not null )";
            $stmt = $pdo->prepare($req);
            $pdo->query($req);
            $req  =
                "ALTER TABLE perso_vue_pos_" . $etage_cod . " ADD CONSTRAINT pk_perso_vue_pos_" . $etage_cod . " PRIMARY KEY (pvue_perso_cod, pvue_pos_cod)";
            $stmt = $pdo->prepare($req);
            $pdo->query($req);
            $req  =
                "ALTER TABLE perso_vue_pos_" . $etage_cod . " ADD CONSTRAINT fk_pvue_perso_cod" . $etage_cod . " FOREIGN KEY (pvue_perso_cod) REFERENCES perso (perso_cod) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;";
            $stmt = $pdo->prepare($req);
            $pdo->query($req);
            $req  = "ALTER TABLE perso_vue_pos_" . $etage_cod . " OWNER TO delain;";
            $stmt = $pdo->prepare($req);
            $pdo->query($req);

            // Maintenant que les table sont créé ont met à jour l'automap
            $req  = "select init_automap($etage_cod) ";
            $stmt = $pdo->prepare($req);
            $pdo->query($req);

            //dupliquer_lieux
            if (isset($_POST["dupliquer_lieux"]))
            {
                echo "Duplication des lieux...<br>";
                $lieu = new lieu();

                // Boucle sur les lieux a dupliquer
                $req  = "SELECT lieu_cod, p2.pos_cod pos_cod from lieu_position 
                      join lieu on lieu_cod=lpos_lieu_cod 
                      join positions p1 on p1.pos_cod=lpos_pos_cod
                      join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                      WHERE p1.pos_etage = :ref_pos_etage; ";
                $stmt = $pdo->prepare($req);
                $stmt =
                    $pdo->execute(array(":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt);
                while ($result = $stmt->fetch())
                {
                    $lieu->charge($result["lieu_cod"]);
                    // vérification du ciblage des lieux de destination sur l'étage lui même
                    if ($lieu->lieu_dest > 0)
                    {
                        $req   = "SELECT p2.pos_cod pos_cod from positions p1
                              join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                              WHERE p1.pos_cod=:pos_cod and p1.pos_etage = :ref_pos_etage; ";
                        $stmt2 = $pdo->prepare($req);
                        $stmt2 =
                            $pdo->execute(array(":pos_cod" => $lieu->lieu_dest, ":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt2);
                        if ($result2 = $stmt2->fetch())
                        {
                            // Si la destination du lieu était vers l'étage dupliqué on recalibre sur la copie !
                            $lieu->lieu_dest = $result2["pos_cod"];
                        }
                    }
                    $lieu->stocke(true);    // Dupliquer

                    $lieu_position                = new lieu_position();
                    $lieu_position->lpos_lieu_cod = $lieu->lieu_cod;
                    $lieu_position->lpos_pos_cod  = $result["pos_cod"];
                    $lieu_position->stocke(true);    // Créer nouveau !
                }
            }

            //les meca, EA et qA
            if (isset($_POST["dupliquer_meca"]))
            {
                echo "Duplication des Méca, EA, QA...<br>";

                // les MECA ============================================================================================
                $meca_map = [] ;

                // Boucle sur les méca a dupliquer
                $req  = "SELECT meca_cod from meca  WHERE meca_pos_etage = :ref_pos_etage; ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":ref_pos_etage" => $etage->etage_numero), $stmt);
                //echo "<pre>"; print_r(array("req"=>$req, ":ref_pos_etage" => $etage->etage_numero));
                while ($result = $stmt->fetch())
                {
                    $meca = new meca();
                    $meca->charge($result["meca_cod"]);
                    $meca->meca_pos_etage = $etage_cod ;
                    $meca->stocke(true);
                    $meca_map[$result["meca_cod"]] = $meca->meca_cod;

                    // dupliquer les position de mécanisme
                    $req  = "SELECT pmeca_cod from meca_position WHERE pmeca_meca_cod = :pmeca_meca_cod; ";
                    $stmt2 = $pdo->prepare($req);
                    $stmt2 = $pdo->execute(array(":pmeca_meca_cod" => $result["meca_cod"]), $stmt2);
                    //echo "<pre>"; print_r(array("req"=>$req, ":pmeca_meca_cod" => $result["meca_cod"]));
                    while ($result2 = $stmt2->fetch())
                    {
                        $pmeca = new meca_position();
                        $pmeca->charge($result2["pmeca_cod"]);

                        // trouver la même position sur le nouvel étage
                        $req   = "SELECT p2.pos_cod pos_cod from positions p1
                              join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                              WHERE p1.pos_cod=:pos_cod and p1.pos_etage = :ref_pos_etage; ";
                        $stmt3 = $pdo->prepare($req);
                        $stmt3 = $pdo->execute(array(":pos_cod" => $pmeca->pmeca_pos_cod, ":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt3);
                        //echo "<pre>"; print_r(array("req"=>$req, ":pos_cod" => $pmeca->pmeca_pos_cod, ":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero));
                        if ($result3 = $stmt3->fetch())
                        {
                            // On recalibre sur la copie et on sauvgarde !
                            $pmeca->pmeca_meca_cod = $meca->meca_cod ;
                            $pmeca->pmeca_pos_etage = $etage_cod;
                            $pmeca->pmeca_pos_cod = $result3["pos_cod"];
                            $pmeca->pmeca_actif = 0 ; // meca désactiver pour la copie !
                            $pmeca->stocke(true);
                        }
                    }
                }


                // les EA ============================================================================================
                $ea_map = [] ;

                // Dupliqurer les EA de l'étage
                $req  = "SELECT fonc_cod, fonc_nom, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_portee, fonc_proba, fonc_message, fonc_nombre_cible, fonc_date_limite, fonc_trigger_param 
                                    FROM fonction_specifique 
                                    WHERE fonc_gmon_cod is null and fonc_perso_cod is null and fonc_type='POS' and fonc_trigger_param->>'fonc_trig_pos_etage'::text=".((int)$etage->etage_numero);
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(), $stmt);
                while ($result = $stmt->fetch())
                {
                    $fonc_trigger_param = json_decode($result["fonc_trigger_param"]);

                    // bind sur le nouvel etage
                    $fonc_trigger_param->fonc_trig_pos_etage = $etage_cod ;

                    // reaffecter les positions de déclenchement
                    $pos_cods = explode(",", $fonc_trigger_param->fonc_trig_pos_cods);
                    array_walk($pos_cods, function(&$value, &$key){return $value = 1*$value ;} );
                    $pos_cods_list = implode(",", array_filter($pos_cods, function ($val) { return ( $val == 0 ? false : true ); } ));

                    if ($pos_cods_list != "")
                    {
                        // trouver les mêmes positions sur le nouvel étage
                        $req   = "SELECT ' '||STRING_AGG (p2.pos_cod,', ')||',' as pos_cods from positions p1
                                  join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                                  WHERE p1.pos_cod in ($pos_cods_list) and p1.pos_etage = :ref_pos_etage; ";
                        $stmt2 = $pdo->prepare($req);
                        $stmt2 = $pdo->execute(array(":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt2);
                        if ($result2 = $stmt2->fetch())
                        {
                            $fonc_trigger_param->fonc_trig_pos_cods = $result2["pos_cods"] ;
                        }
                    }


                    // recalibrer l'utilisation des menanismes (pour ceux du nouvel etage)
                    if ($result["fonc_nom"] =="ea_meca")
                    {
                        if (gettype($fonc_trigger_param->fonc_trig_meca)=="string") { $fonc_trigger_param->fonc_trig_meca = json_decode($fonc_trigger_param->fonc_trig_meca); }
                        foreach ($fonc_trigger_param->fonc_trig_meca as $row => $ea_meca)
                        {
                            $fonc_trigger_param->fonc_trig_meca[$row]->meca_cod = $meca_map[$ea_meca->meca_cod] ;
                            if (1*$ea_meca->pos_cod > 0)
                            {
                                // trouver la même position sur le nouvel étage
                                $req   = "SELECT p2.pos_cod pos_cod from positions p1
                                      join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                                      WHERE p1.pos_cod=:pos_cod and p1.pos_etage = :ref_pos_etage; ";
                                $stmt3 = $pdo->prepare($req);
                                $stmt3 = $pdo->execute(array(":pos_cod" => 1*$ea_meca->pos_cod, ":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt3);
                                if ($result3 = $stmt3->fetch())
                                {
                                    $fonc_trigger_param->fonc_trig_meca[$row]->pos_cod = $result3["pos_cod"] ;
                                }
                            }

                        }
                    }

                    // recalibrer l'utilisation des téléporatations (pour ceux du nouvel etage)
                    if ($result["fonc_nom"] == "ea_teleportation")
                    {
                        if (gettype($fonc_trigger_param->fonc_trig_pos_cod)=="string") $fonc_trigger_param->fonc_trig_pos_cod = json_decode($fonc_trigger_param->fonc_trig_pos_cod);
                        foreach ($fonc_trigger_param->fonc_trig_pos_cod as $row => $ea_teleport)
                        {
                            if (1*$ea_teleport->pos_cod > 0)
                            {
                                // trouver la même position sur le nouvel étage
                                $req   = "SELECT p2.pos_cod pos_cod from positions p1
                                      join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                                      WHERE p1.pos_cod=:pos_cod and p1.pos_etage = :ref_pos_etage; ";
                                $stmt3 = $pdo->prepare($req);
                                $stmt3 = $pdo->execute(array(":pos_cod" => 1*$ea_teleport->pos_cod, ":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt3);
                                if ($result3 = $stmt3->fetch())
                                {
                                    $fonc_trigger_param->fonc_trig_pos_cod[$row]->pos_cod = $result3["pos_cod"] ;
                                }
                            }

                        }
                    }

                    // remettre au format json !
                    $fonc_trigger_param = json_encode($fonc_trigger_param);

                    // appliquer les changement
                    $req  = "INSERT INTO fonction_specifique(fonc_nom, fonc_gmon_cod, fonc_perso_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_portee, fonc_proba, fonc_message, fonc_nombre_cible, fonc_date_limite, fonc_trigger_param)
                              VALUES (:fonc_nom, null, null, 'POS', :fonc_effet, :fonc_force, :fonc_duree, :fonc_type_cible, :fonc_portee, :fonc_proba, :fonc_message, :fonc_nombre_cible, :fonc_date_limite, :fonc_trigger_param)
                              RETURNING fonc_cod";
                    $stmt2 = $pdo->prepare($req);
                    $stmt2 = $pdo->execute(array(   "fonc_nom" => $result["fonc_nom"],
                                                    "fonc_effet" => $result["fonc_effet"],
                                                    "fonc_force" => $result["fonc_force"],
                                                    "fonc_duree" => $result["fonc_duree"],
                                                    "fonc_type_cible" => $result["fonc_type_cible"],
                                                    "fonc_portee" => $result["fonc_portee"],
                                                    "fonc_proba" => $result["fonc_proba"],
                                                    "fonc_message" => $result["fonc_message"],
                                                    "fonc_nombre_cible" => $result["fonc_nombre_cible"],
                                                    "fonc_date_limite" => $result["fonc_date_limite"],
                                                    "fonc_trigger_param" => $fonc_trigger_param), $stmt2);
                    $result2 = $stmt2->fetch();
                    $ea_map[ $result["fonc_cod"] ] = $result2["fonc_cod"];      // old => new !
                }

                // les MECA ============================================================================================
                // recalibrage des activations/desactivations de meca (necessite le mapping MECA et EA)
                // Boucle sur les mécas qui ont été dupliqués
                $req  = "SELECT meca_cod from meca  WHERE meca_pos_etage = :meca_pos_etage; ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":meca_pos_etage" => $etage_cod), $stmt);
                while ($result = $stmt->fetch())
                {
                    $meca = new meca();
                    $meca->charge($result["meca_cod"]);

                    $action_meca_active = json_decode($meca->meca_si_active);
                    foreach ($action_meca_active->meca as $row => $action_meca)
                    {
                        $action_meca_active->meca[$row]->meca_cod = $meca_map[$action_meca->meca_cod] ;
                    }
                    foreach ($action_meca_active->ea as $row => $action_ea)
                    {
                        $action_meca_active->ea[$row]->fonc_cod = $ea_map[$action_ea->fonc_cod] ;
                    }
                    $action_meca_desactive = json_decode($meca->meca_si_desactive);
                    foreach ($action_meca_desactive->meca as $row => $action_meca)
                    {
                        $action_meca_desactive->meca[$row]->meca_cod = $meca_map[$action_meca->meca_cod] ;
                    }
                    foreach ($action_meca_desactive->ea as $row => $ea_meca)
                    {
                        $action_meca_desactive->ea[$row]->fonc_cod = $ea_map[$ea_meca->fonc_cod] ;
                    }

                    $meca->meca_si_active = json_encode($action_meca_active);
                    $meca->meca_si_desactive = json_encode($action_meca_desactive);
                    $meca->stocke();
                }

                // les QA ============================================================================================
                $aquete_map = [] ;
                $aquete_etape_map = [] ;

                echo "<pre>";

                // Boucle sur les QA a dupliquer
                $req  = "SELECT aquete_cod from quetes.aquete WHERE aquete_pos_etage = :ref_pos_etage; ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":ref_pos_etage" => $etage->etage_numero), $stmt);
                while ($result = $stmt->fetch())
                {
                    $aquete = new aquete();
                    $aquete->charge($result["aquete_cod"]);
                    $aquete->aquete_pos_etage = $etage_cod;
                    $aquete->stocke(true);
                    $aquete_map[$result["aquete_cod"]] = $aquete->aquete_cod;

                    // dupliquer les étapes !
                    $req  = "SELECT aqetape_cod from quetes.aquete_etape WHERE aqetape_aquete_cod = :aqetape_aquete_cod; ";
                    $stmt2 = $pdo->prepare($req);
                    $stmt2 = $pdo->execute(array(":aqetape_aquete_cod" => $result["aquete_cod"]), $stmt2);
                    while ($result2 = $stmt2->fetch())
                    {
                        $etape = new aquete_etape();
                        $etape->charge($result2["aqetape_cod"]);
                        $etape->aqetape_aquete_cod =  $aquete->aquete_cod ;
                        $etape->stocke( true );
                        $aquete_etape_map[$result2["aqetape_cod"]] = $etape->aqetape_cod;

                        // dupliquer les éléments de l'étape !
                        $req  = "SELECT aqelem_cod from quetes.aquete_element WHERE aqelem_aquete_cod=:aqelem_aquete_cod and  aqelem_aqetape_cod = :aqelem_aqetape_cod and aqelem_aqperso_cod is null ";
                        $stmt3 = $pdo->prepare($req);
                        $stmt3 = $pdo->execute(array(":aqelem_aquete_cod" =>  $result["aquete_cod"], ":aqelem_aqetape_cod" => $result2["aqetape_cod"]), $stmt3);
                        while ($result3 = $stmt3->fetch())
                        {
                            $element = new aquete_element();
                            $element->charge($result3["aqelem_cod"]);
                            $element->aqelem_aquete_cod =  $aquete->aquete_cod ;
                            $element->aqelem_aqetape_cod =  $etape->aqetape_cod ;
                            $element->stocke( true );
                        }
                    }

                    // recalibrer la prelière étape de la quete
                    $aquete->aquete_etape_cod = $aquete_etape_map[ $aquete->aquete_etape_cod ];
                    $aquete->stocke();

                }

                // recalibrer le workflow d'étapes
                $req  = "SELECT aqetape_cod from quetes.aquete join quetes.aquete_etape on aqetape_aquete_cod=aquete_cod WHERE aquete_pos_etage = :aquete_pos_etage and aqetape_etape_cod is not null; ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":aquete_pos_etage" =>  $etage_cod), $stmt);
                while ($result = $stmt->fetch())
                {
                    $etape = new aquete_etape();
                    $etape->charge($result["aqetape_cod"]);
                    $etape->aqetape_etape_cod = $aquete_etape_map[ $etape->aqetape_etape_cod ] ;
                    $etape->stocke();
                }

                // recalibrer les éléments du type "position", "etape", "quete", "meca", etc...
                $req  = "SELECT aqelem_cod from quetes.aquete join quetes.aquete_element on aqelem_aquete_cod=aquete_cod 
                                  WHERE aquete_pos_etage = :aquete_pos_etage and aqelem_type in ('quete','choix','choix_etape','position','etape','quete_etape','meca', 'meca_etat'); ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":aquete_pos_etage" =>  $etage_cod), $stmt);
                while ($result = $stmt->fetch())
                {
                    $element = new aquete_element();
                    $element->charge($result["aqelem_cod"]);

                    //===== position
                    if  ( $element->aqelem_type == 'position' && $element->aqelem_misc_cod>0)
                    {
                        $req   = "SELECT p2.pos_cod pos_cod from positions p1
                                      join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                                      WHERE p1.pos_cod=:pos_cod and p1.pos_etage = :ref_pos_etage; ";
                        $stmt2 = $pdo->prepare($req);
                        $stmt2 = $pdo->execute(array(":pos_cod" => $element->aqelem_misc_cod, ":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt2);
                        if ($result2 = $stmt2->fetch())
                        {
                            $element->aqelem_misc_cod = $result2["pos_cod"] ;
                        }
                    }
                    //===== meca ou meca_etat
                    else if  ( ($element->aqelem_type == 'meca' || $element->aqelem_type == 'meca_etat') && $element->aqelem_misc_cod>0 && isset($meca_map[$element->aqelem_misc_cod]))
                    {
                        $element->aqelem_misc_cod = $meca_map[$element->aqelem_misc_cod] ;
                        if ($element->aqelem_param_num_3 > 0 )
                        {
                            $req   = "SELECT p2.pos_cod pos_cod from positions p1
                                      join positions p2 on p2.pos_x=p1.pos_x and p2.pos_y=p1.pos_y and p2.pos_etage=:pos_etage
                                      WHERE p1.pos_cod=:pos_cod and p1.pos_etage = :ref_pos_etage; ";
                            $stmt2 = $pdo->prepare($req);
                            $stmt2 = $pdo->execute(array(":pos_cod" => $element->aqelem_param_num_3, ":pos_etage" => $etage_cod, ":ref_pos_etage" => $etage->etage_numero), $stmt2);
                            if ($result2 = $stmt2->fetch())
                            {
                                $element->aqelem_param_num_3 = $result2["pos_cod"] ;
                            }
                        }
                    }
                    //===== quete
                    else if  ( $element->aqelem_type == 'quete' && $element->aqelem_misc_cod>0 && isset($aquete_map[$element->aqelem_misc_cod]))
                    {
                        $element->aqelem_misc_cod = $aquete_map[$element->aqelem_misc_cod] ;
                    }
                    //===== quete_etape
                    else if  ( $element->aqelem_type == 'quete_etape' && $element->aqelem_misc_cod>0 && isset($aquete_etape_map[$element->aqelem_misc_cod]))
                    {
                        $element->aqelem_misc_cod = $aquete_etape_map[$element->aqelem_misc_cod] ;
                    }
                    //===== etape
                    else if  ( $element->aqelem_type == 'etape' && $element->aqelem_misc_cod>0 && isset($aquete_etape_map[$element->aqelem_misc_cod]))
                    {
                        $element->aqelem_misc_cod = $aquete_etape_map[$element->aqelem_misc_cod] ;
                    }
                    //===== choix
                    else if  ( $element->aqelem_type == 'choix' && $element->aqelem_misc_cod>0 && isset($aquete_etape_map[$element->aqelem_misc_cod]))
                    {
                        $element->aqelem_misc_cod = $aquete_etape_map[$element->aqelem_misc_cod] ;
                    }
                    //===== choix_etape
                    else if  ( $element->aqelem_type == 'choix_etape' && $element->aqelem_misc_cod>0 && isset($aquete_etape_map[$element->aqelem_misc_cod]))
                    {
                        $element->aqelem_misc_cod = $aquete_etape_map[$element->aqelem_misc_cod] ;
                    }
                    $element->stocke();     // sauver les modifications !
                }
            }

            // Loguer pour le suivi admin
            $log = date("d/m/y - H:i") . "\tCompte " . $compte->compt_nom . " ($compt_cod)\t";
            $log .= "Duplication de l'étage #{$etage->etage_numero} - {$etage->etage_libelle} vers etage #{$etage_cod} - " . $_POST["etage_libelle"];
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


