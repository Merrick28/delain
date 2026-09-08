<?php // RECUPERATION DES INFORMATIONS POUR LE LOG
if (!defined('APPEL'))
{
    define('APPEL', 1);
}

$fonctions = new fonctions();

if (isset($_POST['gmon_cod']) and $methode != 'create_mon')
{
    $req_mons = "select gmon_nom from monstre_generique where gmon_cod = $gmon_cod ";
    $stmt     = $pdo->query($req_mons);

    if ($result = $stmt->fetch())
    {
        $pmons_mod_nom = $result['gmon_nom'];
    } else
    {
        $pmons_mod_nom = $gmon_nom;
    }
}
$log =
    date("d/m/y - H:i") . $perso->perso_nom . " (compte $compt_cod) modifie le type de monstre $pmons_mod_nom, numero: 
$gmon_cod\n";

// On traite d'abord un eventuel upload de fichier (avatar du monstre) identique pour creation/modification
if (($_POST["type-img-avatar"] == "upload") && ($_FILES["avatar_file"]["tmp_name"] != ""))
{
    $filename  = $_FILES["avatar_file"]["name"];
    $imagesize = @getimagesize($_FILES["avatar_file"]["tmp_name"]);
    if (($imagesize[0] <= 28) || ($imagesize[1] <= 28))
    {
        echo "<strong>Impossible d'ajouter l'image du monstre, elle est trop petite.</strong><br>";
        $_POST["gmon_avatar"] = "defaut.png";
        $gmon_avatar          = "defaut.png";
    } else if (file_exists($baseimage . '/' . $filename))
    {
        echo "<strong>Impossible d'ajouter l'image du monstre, le nom existe déjà sur le serveur.</strong><br>";
        $_POST["gmon_avatar"] = "defaut.png";
        $gmon_avatar          = "defaut.png";
    } else
    {
        $baseimage = "../images/avatars";
        move_uploaded_file($_FILES["avatar_file"]["tmp_name"], $baseimage . '/' . $filename);
        $log                  =
            $log . "Ajout/Modification de l'image sur le serveur : /images/avatars/" . $filename . "\n";
        $_POST["gmon_avatar"] = $filename;
        $gmon_avatar          = $filename;
    }
}
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case "create_mon":
        $req_cre_mon_cod = "select nextval('seq_gmon_cod') as cod";
        $stmt            = $pdo->query($req_cre_mon_cod);

        if ($gmon_duree_vie == '') $gmon_duree_vie = 0;
        $gmon_nom         = pg_escape_string(htmlspecialchars(str_replace('\'', '’', $gmon_nom)));
        $gmon_description = pg_escape_string(htmlspecialchars(str_replace('\'', '’', $gmon_description)));
        $gmon_avatar      = pg_escape_string(htmlspecialchars(str_replace('\'', '’', $gmon_avatar)));
        if (!in_array($gmon_sex, array("F", "M", "A", "H", "I"))) $gmon_sex = "NULL"; else $gmon_sex = "'$gmon_sex'";

        if ($result = $stmt->fetch())
        {
            $gmon_cod     = $result['cod'];
            $req_cre_gmon = "insert into monstre_generique (gmon_cod,gmon_nom"
                . ",gmon_for,gmon_dex,gmon_int,gmon_con"
                . ",gmon_race_cod,gmon_temps_tour,gmon_des_regen,gmon_valeur_regen,gmon_vue"
                . ",gmon_amelioration_vue,gmon_amelioration_regen,gmon_amelioration_degats,gmon_amelioration_armure"
                . ",gmon_niveau,gmon_nb_des_degats,gmon_val_des_degats,gmon_or,gmon_arme,gmon_armure"
                . ",gmon_soutien,gmon_amel_deg_dist,gmon_vampirisme,gmon_taille,gmon_description,gmon_quete,gmon_duree_vie, gmon_avatar, gmon_sex, gmon_type_ia, gmon_monture) values ($gmon_cod, e'$gmon_nom'"
                . ",$gmon_for,$gmon_dex,$gmon_int,$gmon_con"
                . ",$gmon_race_cod,$gmon_temps_tour,$gmon_des_regen,$gmon_valeur_regen,$gmon_vue"
                . ",$gmon_amelioration_vue,$gmon_amelioration_regen,$gmon_amelioration_degats,$gmon_amelioration_armure"
                . ",$gmon_niveau,$gmon_nb_des_degats,$gmon_val_des_degats,$gmon_or,$gmon_arme,$gmon_armure"
                . ",'$gmon_soutien',$gmon_amel_deg_dist,$gmon_vampirisme,$gmon_taille, e'$gmon_description', '$gmon_quete',$gmon_duree_vie, e'$gmon_avatar', $gmon_sex, $gmon_ia, '$gmon_monture')";
            $pdo->query($req_cre_gmon);

            // --- DUPLICATION DES PROPRIETES DEPUIS LE MONSTRE MODELE ---
            $gmon_cod_source = isset($_POST['gmon_cod_source']) ? (int)$_POST['gmon_cod_source'] : 0;
            if ($gmon_cod_source > 0) {
                // 1. DUPLICATION DES SORTS
                $req_copy_sorts = "INSERT INTO sorts_monstre_generique (sgmon_gmon_cod, sgmon_sort_cod, sgmon_chance)
                                   SELECT $gmon_cod, sgmon_sort_cod, sgmon_chance
                                   FROM sorts_monstre_generique
                                   WHERE sgmon_gmon_cod = $gmon_cod_source";
                $pdo->query($req_copy_sorts);

                // 2. DUPLICATION DES IMMUNITES
                $req_copy_immun = "INSERT INTO monstre_generique_immunite (immun_sort_cod, immun_gmon_cod, immun_valeur, immun_resistance, immun_runes)
                                   SELECT immun_sort_cod, $gmon_cod, immun_valeur, immun_resistance, immun_runes
                                   FROM monstre_generique_immunite
                                   WHERE immun_gmon_cod = $gmon_cod_source";
                $pdo->query($req_copy_immun);

                // 3. DUPLICATION DES COMPETENCES DE BASE
                $req_copy_comp = "INSERT INTO gmon_type_comp (gtypc_gmon_cod, gtypc_typc_cod, gtypc_valeur)
                                  SELECT $gmon_cod, gtypc_typc_cod, gtypc_valeur
                                  FROM gmon_type_comp
                                  WHERE gtypc_gmon_cod = $gmon_cod_source";
                $pdo->query($req_copy_comp);

                // 4. DUPLICATION DES COMPETENCES SPECIFIQUES
                $req_copy_comp_spe = "INSERT INTO monstre_generique_comp (gmoncomp_gmon_cod, gmoncomp_comp_cod, gmoncomp_valeur, gmoncomp_chance)
                                      SELECT $gmon_cod, gmoncomp_comp_cod, gmoncomp_valeur, gmoncomp_chance
                                      FROM monstre_generique_comp
                                      WHERE gmoncomp_gmon_cod = $gmon_cod_source";
                $pdo->query($req_copy_comp_spe);

                // 5. DUPLICATION DES EFFETS AUTOMATIQUES
                $req_copy_effets = "INSERT INTO fonction_specifique (fonc_nom, fonc_gmon_cod, fonc_perso_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message, fonc_trigger_param, fonc_date_limite, fonc_mode)
                                    SELECT fonc_nom, $gmon_cod, fonc_perso_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message, fonc_trigger_param, fonc_date_limite, fonc_mode
                                    FROM fonction_specifique
                                    WHERE fonc_gmon_cod = $gmon_cod_source";
                $pdo->query($req_copy_effets);

                // 6. DUPLICATION DES OBJETS / DROPS
                $req_copy_drops = "INSERT INTO objets_monstre_generique (ogmon_gmon_cod, ogmon_gobj_cod, ogmon_chance, ogmon_equipe)
                                   SELECT $gmon_cod, ogmon_gobj_cod, ogmon_chance, ogmon_equipe
                                   FROM objets_monstre_generique
                                   WHERE ogmon_gmon_cod = $gmon_cod_source";
                $pdo->query($req_copy_drops);

                // 7. DUPLICATION DES TERRAINS ET MONTURE
                $req_copy_terrains = "INSERT INTO monstre_terrain (tmon_gmon_cod, tmon_ter_cod, tmon_accessible, tmon_chevauchable, tmon_terrain_pa, tmon_event_chance, tmon_event_pa, tmon_message)
                                      SELECT $gmon_cod, tmon_ter_cod, tmon_accessible, tmon_chevauchable, tmon_terrain_pa, tmon_event_chance, tmon_event_pa, tmon_message
                                      FROM monstre_terrain
                                      WHERE tmon_gmon_cod = $gmon_cod_source";
                $pdo->query($req_copy_terrains);

            }

        }
        writelog($log . "Nouveau type de monstre : $gmon_nom \n", 'monstre_edit');
        echo "Nouveau modèle créé et dupliqué avec succès<br>";
        break;

    case "delete_mon":
        // Suppression des dépendances secondaires
        $pdo->query("DELETE FROM sorts_monstre_generique WHERE sgmon_gmon_cod = $gmon_cod");
        $pdo->query("DELETE FROM monstre_generique_immunite WHERE immun_gmon_cod = $gmon_cod");
        $pdo->query("DELETE FROM gmon_type_comp WHERE gtypc_gmon_cod = $gmon_cod");
        $pdo->query("DELETE FROM monstre_generique_comp WHERE gmoncomp_gmon_cod = $gmon_cod");
        $pdo->query("DELETE FROM objets_monstre_generique WHERE ogmon_gmon_cod = $gmon_cod");
        $pdo->query("DELETE FROM monstre_terrain WHERE tmon_gmon_cod = $gmon_cod");
        $pdo->query("DELETE FROM fonction_specifique WHERE fonc_gmon_cod = $gmon_cod");

        // Suppression du modèle de monstre
        $req_del_gmon = "DELETE FROM monstre_generique WHERE gmon_cod = $gmon_cod";
        $pdo->query($req_del_gmon);

        writelog($log . "Suppression du modèle de monstre : $pmons_mod_nom (ID: $gmon_cod)\n", 'monstre_edit');
        echo "Modèle de monstre supprimé avec succès.<br>";

        // Réinitialisation de la méthode pour repasser sur l'écran d'accueil
        $methode2 = 'debut';
        break;

    case "update_mon":
        if ($gmon_duree_vie == '') $gmon_duree_vie = 0;
        $fields = array("gmon_nom",
            "gmon_for",
            "gmon_dex",
            "gmon_int",
            "gmon_con",
            "gmon_race_cod",
            "gmon_temps_tour",
            "gmon_des_regen",
            "gmon_valeur_regen",
            "gmon_vue",
            "gmon_amelioration_vue",
            "gmon_amelioration_regen",
            "gmon_amelioration_degats",
            "gmon_amelioration_armure",
            "gmon_niveau",
            "gmon_nb_des_degats",
            "gmon_val_des_degats",
            "gmon_or",
            "gmon_arme",
            "gmon_armure",
            "gmon_soutien",
            "gmon_amel_deg_dist",
            "gmon_vampirisme",
            "gmon_taille",
            "gmon_serie_arme_cod",
            "gmon_serie_armure_cod",
            /*"gmon_pv",
            "gmon_pourcentage_aleatoire",*/
            "gmon_nb_receptacle",
            "gmon_type_ia",
            "gmon_description",
            "gmon_quete",
            "gmon_duree_vie",
            "gmon_avatar",
            "gmon_voie_magique",
            "gmon_sex",
            "gmon_monture");
        // SELECT POUR LES VALEURS PRECEDENTES
        $req_sel_mon = "select gmon_cod";
        foreach ($fields as $i => $value)
        {
            $req_sel_mon = $req_sel_mon . "," . $fields[$i];
        }
        $req_sel_mon = $req_sel_mon . " from monstre_generique where gmon_cod = $gmon_cod";
        //echo $req_sel_mon;
        $stmt   = $pdo->query($req_sel_mon);
        $result = $stmt->fetch();

        foreach ($fields as $i => $value)
        {
            if (isset($_POST[$fields[$i]]) and $result[$fields[$i]] != null and $_POST[$fields[$i]] !=
                $result[$fields[$i]])
            {
                $log =
                    $log . "Modification du champ " . $fields[$i] . " : " . $result[$fields[$i]] . " => " .
                    $_POST[$fields[$i]] . "\n";
            }
        }

        writelog($log, 'monstre_edit');

        if (!isset($_POST['gmon_vampirisme']) or $gmon_vampirisme == "")
            $gmon_vampirisme = "null";
        if (!isset($_POST['gmon_pv']) or $gmon_pv == "")
            $gmon_pv = "null";
        if (!isset($_POST['gmon_pourcentage_aleatoire']) or $gmon_pourcentage_aleatoire == "")
            $gmon_pourcentage_aleatoire = "null";
        $req_cre_gmon = "update monstre_generique set gmon_nom = e'" . pg_escape_string($gmon_nom) . "'"
            . ",gmon_for = $gmon_for,gmon_dex = $gmon_dex,gmon_int = $gmon_int,gmon_con = $gmon_con"
            . ",gmon_race_cod = $gmon_race_cod,gmon_temps_tour = $gmon_temps_tour,gmon_des_regen = $gmon_des_regen,gmon_valeur_regen = $gmon_valeur_regen,gmon_vue = $gmon_vue"
            . ",gmon_amelioration_vue = $gmon_amelioration_vue,gmon_amelioration_regen = $gmon_amelioration_regen,gmon_amelioration_degats = $gmon_amelioration_degats,gmon_amelioration_armure = $gmon_amelioration_armure"
            . ",gmon_niveau = $gmon_niveau,gmon_nb_des_degats = $gmon_nb_des_degats,gmon_val_des_degats = $gmon_val_des_degats,gmon_or = $gmon_or,gmon_arme = $gmon_arme,gmon_armure = $gmon_armure"
            . ",gmon_serie_arme_cod = $gmon_serie_arme_cod,gmon_serie_armure_cod = $gmon_serie_armure_cod,gmon_type_ia = $gmon_ia,gmon_pv = $gmon_pv,gmon_pourcentage_aleatoire = $gmon_pourcentage_aleatoire"
            . ",gmon_soutien = '$gmon_soutien',gmon_amel_deg_dist = $gmon_amel_deg_dist,gmon_vampirisme = $gmon_vampirisme,gmon_taille = $gmon_taille,gmon_description = e'" . pg_escape_string($gmon_description)
            . "',gmon_nb_receptacle = $gmon_nb_receptacle, gmon_quete = '$gmon_quete', gmon_duree_vie = $gmon_duree_vie, gmon_avatar = e'" . pg_escape_string($gmon_avatar) . "', gmon_voie_magique=$gmon_voie_magique, gmon_sex='" . pg_escape_string($gmon_sex) . "', gmon_monture='$gmon_monture' where gmon_cod = $gmon_cod";
        //echo $req_cre_gmon;
        $pdo->query($req_cre_gmon);
        echo "MAJ modèle<br>";
        break;

    case "delete_mon_sort":
        $sort_cod    = $_REQUEST['sort_cod'];
        $req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Suppression d'un sort : $sort_cod - " . $result['sort_nom'] . "\n", 'monstre_edit');

        $req_upd_mon =
            "delete from sorts_monstre_generique where sgmon_gmon_cod  = $gmon_cod and sgmon_sort_cod = $sort_cod";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Suppression d'un sort";
        break;

    case "add_mon_sort":
        // sort_cod arrive maintenant sous forme de tableau (select multiple) : on ajoute un sort par
        // sélection.
        $sort_cod_list = $_REQUEST['sort_cod'];
        if (!is_array($sort_cod_list))
        {
            $sort_cod_list = array($sort_cod_list);
        }

        foreach ($sort_cod_list as $sort_cod)
        {
            $sort_cod    = (int)$sort_cod;
            $req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
            $stmt        = $pdo->query($req_upd_mon);
            $result      = $stmt->fetch();
            writelog($log . "Ajout d'un sort : $sort_cod - " . $result['sort_nom'] . "\n", 'monstre_edit');

            $req_upd_mon =
                "insert into sorts_monstre_generique (sgmon_gmon_cod,sgmon_sort_cod) values ($gmon_cod,$sort_cod)";
            $stmt        = $pdo->query($req_upd_mon);
        }
        echo "Ajout d'un ou plusieurs sort(s)";
        break;

    case "update_mon_sorts":
        // Suppression multiple de sorts via cases à cocher (pas de valeur à modifier pour un sort,
        // contrairement aux immunités : la seule action possible ici est la suppression).
        $sort_cod_list = isset($_POST['sort_cod']) && is_array($_POST['sort_cod'])
            ? $_POST['sort_cod']
            : array();

        $sort_delete_list = isset($_POST['sort_delete']) && is_array($_POST['sort_delete'])
            ? $_POST['sort_delete']
            : array();

        $nb_suppressions = 0;

        foreach ($sort_cod_list as $sort_cod)
        {
            $sort_cod = (int)$sort_cod;

            if ($sort_cod <= 0 || !isset($sort_delete_list[$sort_cod]))
            {
                continue;
            }

            $req_upd_mon = "select sort_nom
                            from sorts
                            where sort_cod = $sort_cod";
            $stmt = $pdo->query($req_upd_mon);
            $result = $stmt->fetch();

            if (!$result)
            {
                continue;
            }

            $sort_nom = $result['sort_nom'];

            $req_upd_mon =
                "delete from sorts_monstre_generique
                 where sgmon_gmon_cod = $gmon_cod
                   and sgmon_sort_cod = $sort_cod";
            $pdo->query($req_upd_mon);

            writelog(
                $log . "Suppression d'un sort : $sort_cod - $sort_nom\n",
                'monstre_edit'
            );

            $nb_suppressions++;
        }

        echo "Modification des sorts : $nb_suppressions supprimé(s)";
        break;

    case "add_mon_terrain":

        $ter_cod_list = $_REQUEST['ter_cod'];

        if (!is_array($ter_cod_list))
        {
            $ter_cod_list = array($ter_cod_list);
        }

        $tmon_accessible   = (isset($_POST['tmon_accessible'])) ? 'O' : 'N';
        $tmon_chevauchable = (isset($_POST['tmon_chevauchable'])) ? 'O' : 'N';
        $tmon_terrain_pa   = $_POST['tmon_terrain_pa'];
        $tmon_event_chance = $_POST['tmon_event_chance'];
        $tmon_event_pa     = $_POST['tmon_event_pa'];
        $tmon_message      = str_replace("'", "''", $_POST['tmon_message']);

        foreach ($ter_cod_list as $ter_cod)
        {
            $ter_cod = (int)$ter_cod;

            $req_upd_mon = "select ter_nom
                        from terrain
                        where ter_cod = $ter_cod";

            $stmt = $pdo->query($req_upd_mon);
            $result = $stmt->fetch();

            writelog(
                $log .
                "Ajout d'un terrain : $ter_cod - "
                . $result['ter_nom']
                . "\n",
                'monstre_edit'
            );

            $req_upd_mon =
                "insert into monstre_terrain
            (
                tmon_gmon_cod,
                tmon_ter_cod,
                tmon_accessible,
                tmon_chevauchable,
                tmon_terrain_pa,
                tmon_event_chance,
                tmon_event_pa,
                tmon_message
            )
            values
            (
                $gmon_cod,
                $ter_cod,
                '$tmon_accessible',
                '$tmon_chevauchable',
                '$tmon_terrain_pa',
                '$tmon_event_chance',
                '$tmon_event_pa',
                '$tmon_message'
            )";

            $pdo->query($req_upd_mon);
        }

        echo "Ajout d’un ou plusieurs terrains";
        break;

    case "update_mon_terrains":
        // MODIFICATION / SUPPRESSION GROUPEE DES MONTURES (TERRAINS)
        $ter_cod_list = isset($_POST['tmon_ter_cod']) && is_array($_POST['tmon_ter_cod'])
            ? $_POST['tmon_ter_cod']
            : array();

        $tmon_accessible_list = isset($_POST['tmon_accessible']) && is_array($_POST['tmon_accessible'])
            ? $_POST['tmon_accessible']
            : array();

        $tmon_chevauchable_list = isset($_POST['tmon_chevauchable']) && is_array($_POST['tmon_chevauchable'])
            ? $_POST['tmon_chevauchable']
            : array();

        $tmon_terrain_pa_list = isset($_POST['tmon_terrain_pa']) && is_array($_POST['tmon_terrain_pa'])
            ? $_POST['tmon_terrain_pa']
            : array();

        $tmon_event_chance_list = isset($_POST['tmon_event_chance']) && is_array($_POST['tmon_event_chance'])
            ? $_POST['tmon_event_chance']
            : array();

        $tmon_event_pa_list = isset($_POST['tmon_event_pa']) && is_array($_POST['tmon_event_pa'])
            ? $_POST['tmon_event_pa']
            : array();

        $tmon_message_list = isset($_POST['tmon_message']) && is_array($_POST['tmon_message'])
            ? $_POST['tmon_message']
            : array();

        $tmon_delete_list = isset($_POST['tmon_delete']) && is_array($_POST['tmon_delete'])
            ? $_POST['tmon_delete']
            : array();

        $nb_modifications = 0;
        $nb_suppressions  = 0;

        foreach ($ter_cod_list as $ter_cod)
        {
            $ter_cod = (int)$ter_cod;

            $req_upd_mon = "select ter_nom
                            from terrain
                            where ter_cod = $ter_cod";
            $stmt = $pdo->query($req_upd_mon);
            $result = $stmt->fetch();

            if (!$result)
            {
                continue;
            }

            $ter_nom = $result['ter_nom'];

            if (isset($tmon_delete_list[$ter_cod]))
            {
                $req_upd_mon =
                    "delete from monstre_terrain
                     where tmon_gmon_cod = $gmon_cod
                       and tmon_ter_cod = $ter_cod";
                $pdo->query($req_upd_mon);

                writelog(
                    $log . "Suppression d'un terrain : $ter_cod - $ter_nom\n",
                    'monstre_edit'
                );

                $nb_suppressions++;
                continue;
            }

            $tmon_accessible   = isset($tmon_accessible_list[$ter_cod]) ? 'O' : 'N';
            $tmon_chevauchable = isset($tmon_chevauchable_list[$ter_cod]) ? 'O' : 'N';
            $tmon_terrain_pa   = isset($tmon_terrain_pa_list[$ter_cod]) ? $tmon_terrain_pa_list[$ter_cod] : 0;
            $tmon_event_chance = isset($tmon_event_chance_list[$ter_cod]) ? $tmon_event_chance_list[$ter_cod] : 0;
            $tmon_event_pa     = isset($tmon_event_pa_list[$ter_cod]) ? $tmon_event_pa_list[$ter_cod] : 0;
            $tmon_message      = isset($tmon_message_list[$ter_cod])
                ? str_replace("'", "''", $tmon_message_list[$ter_cod])
                : '';

            $req_upd_mon =
                "update monstre_terrain
                 set tmon_accessible   = '$tmon_accessible',
                     tmon_chevauchable = '$tmon_chevauchable',
                     tmon_terrain_pa   = '$tmon_terrain_pa',
                     tmon_event_chance = '$tmon_event_chance',
                     tmon_event_pa     = '$tmon_event_pa',
                     tmon_message      = '$tmon_message'
                 where tmon_gmon_cod = $gmon_cod
                   and tmon_ter_cod  = $ter_cod";

            $pdo->query($req_upd_mon);

            writelog(
                $log .
                "Modification d'un terrain : $ter_cod - $ter_nom" .
                " | Accessible: $tmon_accessible" .
                " | Chevauchable: $tmon_chevauchable" .
                " | PA: $tmon_terrain_pa" .
                " | Proba Evt: $tmon_event_chance" .
                " | Evt PA: $tmon_event_pa\n",
                'monstre_edit'
            );

            $nb_modifications++;
        }

        echo "Modification des montures : $nb_modifications modifiée(s), $nb_suppressions supprimée(s)";
        break;

    case "update_mon_immunites":
        $sort_cod_list = isset($_POST['immun_sort_cod']) && is_array($_POST['immun_sort_cod'])
            ? $_POST['immun_sort_cod']
            : array();

        $immun_valeur_list = isset($_POST['immun_valeur']) && is_array($_POST['immun_valeur'])
            ? $_POST['immun_valeur']
            : array();

        $immun_resistance_list = isset($_POST['immun_resistance']) && is_array($_POST['immun_resistance'])
            ? $_POST['immun_resistance']
            : array();

        $immun_runes_list = isset($_POST['immun_runes']) && is_array($_POST['immun_runes'])
            ? $_POST['immun_runes']
            : array();

        $immun_delete_list = isset($_POST['immun_delete']) && is_array($_POST['immun_delete'])
            ? $_POST['immun_delete']
            : array();

        $nb_modifications = 0;
        $nb_suppressions  = 0;

        foreach ($sort_cod_list as $sort_cod)
        {
            $sort_cod = (int)$sort_cod;

            if ($sort_cod <= 0)
            {
                continue;
            }

            $req_upd_mon = "select sort_nom
                            from sorts
                            where sort_cod = $sort_cod";
            $stmt = $pdo->query($req_upd_mon);
            $result = $stmt->fetch();

            if (!$result)
            {
                continue;
            }

            $sort_nom = $result['sort_nom'];

            if (isset($immun_delete_list[$sort_cod]))
            {
                $req_upd_mon =
                    "delete from monstre_generique_immunite
                     where immun_gmon_cod = $gmon_cod
                       and immun_sort_cod = $sort_cod";
                $pdo->query($req_upd_mon);

                writelog(
                    $log . "Suppression d'une immunité : $sort_cod - $sort_nom\n",
                    'monstre_edit'
                );

                $nb_suppressions++;
                continue;
            }

            $immun_rune = isset($immun_runes_list[$sort_cod]) ? 'O' : 'N';

            $immun_valeur = max(
                0,
                min(
                    1,
                    isset($immun_valeur_list[$sort_cod])
                        ? (float)$immun_valeur_list[$sort_cod]
                        : 0
                )
            );

            $immun_resistance = max(
                -1,
                min(
                    1,
                    isset($immun_resistance_list[$sort_cod])
                        ? (float)$immun_resistance_list[$sort_cod]
                        : 0
                )
            );

            $req_upd_mon =
                "update monstre_generique_immunite
                 set immun_valeur = $immun_valeur,
                     immun_resistance = $immun_resistance,
                     immun_runes = '$immun_rune'
                 where immun_sort_cod = $sort_cod
                   and immun_gmon_cod = $gmon_cod";

            $pdo->query($req_upd_mon);

            writelog(
                $log .
                "Modification d'une immunité : $sort_cod - $sort_nom" .
                " | Valeur: $immun_valeur" .
                " | Resistance/Faiblesse: $immun_resistance" .
                " | Runes: $immun_rune\n",
                'monstre_edit'
            );

            $nb_modifications++;
        }

        echo "Modification des immunités : $nb_modifications modifiée(s), $nb_suppressions supprimée(s)";
        break;

    case "add_mon_immunite":
        // sort_cod arrive maintenant sous forme de tableau (select multiple) : on ajoute une immunité par sort sélectionné,
        // toutes avec les mêmes paramètres (runes / valeur / résistance).
        $sort_cod_list = $_REQUEST['sort_cod'];
        if (!is_array($sort_cod_list))
        {
            $sort_cod_list = array($sort_cod_list);
        }

        $immun_rune = (isset($_POST['immun_rune'])) ? 'O' : 'N';
        $immun_valeur = max(0, min(1, (isset($_POST['immun_valeur'])) ? 1*(float)$_POST['immun_valeur'] : 0));
        $immun_resistance = max(-1, min(1, (isset($_POST['immun_resistance'])) ? 1*(float)$_POST['immun_resistance'] : 0));

        foreach ($sort_cod_list as $sort_cod)
        {
            $sort_cod    = (int)$sort_cod;
            $req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
            $stmt        = $pdo->query($req_upd_mon);
            $result      = $stmt->fetch();
            writelog($log . "Ajout d'une immunité : $sort_cod - " . $result['sort_nom'] . "\n", 'monstre_edit');

            $req_upd_mon =
                "insert into monstre_generique_immunite (immun_sort_cod, immun_gmon_cod, immun_valeur, immun_resistance, immun_runes) values ($sort_cod, $gmon_cod, $immun_valeur, $immun_resistance, '$immun_rune')";
            $stmt        = $pdo->query($req_upd_mon);
        }
        echo "Ajout d’une ou plusieurs immunité(s)";
        break;

    case "add_mon_comp":
        // typc_cod arrive maintenant sous forme de tableau (select multiple) : on ajoute un type de
        // compétence par sélection, tous avec la même valeur.
        $typc_cod_list = $_REQUEST['typc_cod'];
        if (!is_array($typc_cod_list))
        {
            $typc_cod_list = array($typc_cod_list);
        }

        foreach ($typc_cod_list as $typc_cod)
        {
            $typc_cod    = (int)$typc_cod;
            $req_upd_mon = "select typc_libelle from type_competences where typc_cod = $typc_cod";
            $stmt        = $pdo->query($req_upd_mon);
            $result      = $stmt->fetch();
            writelog($log . "Ajout d'un type de competences : $typc_cod - " . $result['typc_libelle'] . " Valeur: $valeur\n", 'monstre_edit');

            $req_upd_mon =
                "insert into gmon_type_comp (gtypc_gmon_cod,gtypc_typc_cod,gtypc_valeur) values ($gmon_cod,$typc_cod,$valeur)";
            $stmt = $pdo->query($req_upd_mon);
        }
        echo "Ajout d’une ou plusieurs competence(s)";
        break;

    case "update_mon_competences":
        // MODIFICATION / SUPPRESSION GROUPEE DES COMPETENCES
        $typc_cod_list = isset($_POST['typc_cod']) && is_array($_POST['typc_cod'])
            ? $_POST['typc_cod']
            : array();

        $gtypc_valeur_list = isset($_POST['gtypc_valeur']) && is_array($_POST['gtypc_valeur'])
            ? $_POST['gtypc_valeur']
            : array();

        $comp_delete_list = isset($_POST['comp_delete']) && is_array($_POST['comp_delete'])
            ? $_POST['comp_delete']
            : array();

        $nb_modifications = 0;
        $nb_suppressions  = 0;

        foreach ($typc_cod_list as $typc_cod)
        {
            $typc_cod = (int)$typc_cod;

            if ($typc_cod <= 0)
            {
                continue;
            }

            $req_upd_mon = "select typc_libelle from type_competences where typc_cod = $typc_cod";
            $stmt   = $pdo->query($req_upd_mon);
            $result = $stmt->fetch();

            if (!$result)
            {
                continue;
            }

            $typc_libelle = $result['typc_libelle'];

            if (isset($comp_delete_list[$typc_cod]))
            {
                $req_upd_mon =
                    "delete from gmon_type_comp
                     where gtypc_gmon_cod = $gmon_cod
                       and gtypc_typc_cod = $typc_cod";
                $pdo->query($req_upd_mon);

                writelog($log . "Supression d'un type de competences : $typc_cod - $typc_libelle\n", 'monstre_edit');

                $nb_suppressions++;
                continue;
            }

            $valeur = isset($gtypc_valeur_list[$typc_cod]) ? (int)$gtypc_valeur_list[$typc_cod] : 0;

            $req_upd_mon =
                "update gmon_type_comp
                 set gtypc_valeur = $valeur
                 where gtypc_gmon_cod = $gmon_cod
                   and gtypc_typc_cod = $typc_cod";

            $pdo->query($req_upd_mon);

            writelog(
                $log .
                "Modification d'un type de competences : $typc_cod - $typc_libelle" .
                " | Valeur: $valeur\n",
                'monstre_edit'
            );

            $nb_modifications++;
        }

        echo "Modification des compétences : $nb_modifications modifiée(s), $nb_suppressions supprimée(s)";
        break;

    case "add_mon_comp_spe":
        // typc_cod arrive maintenant sous forme de tableau (select multiple) : on ajoute une compétence par
        // sélection, toutes avec le même Pourcentage / Chance.
        $typc_cod_list = $_REQUEST['typc_cod'];
        if (!is_array($typc_cod_list))
        {
            $typc_cod_list = array($typc_cod_list);
        }

        $valeur = max(0, min(100, (int)$_REQUEST['valeur']));
        $chance = max(0, min(100, (int)$_REQUEST['chance']));

        foreach ($typc_cod_list as $typc_cod)
        {
            $typc_cod    = (int)$typc_cod;
            $req_upd_mon = "select comp_libelle from competences where comp_cod = $typc_cod";
            $stmt        = $pdo->query($req_upd_mon);
            $result      = $stmt->fetch();
            writelog($log . "Ajout d'une competence : $typc_cod - " . $result['comp_libelle'], 'monstre_edit');

            $req_upd_mon =
                "insert into monstre_generique_comp (gmoncomp_gmon_cod,gmoncomp_comp_cod,gmoncomp_valeur,gmoncomp_chance) values ($gmon_cod,$typc_cod,$valeur,$chance)";
            $stmt = $pdo->query($req_upd_mon);
        }
        echo "Ajout d’une ou plusieurs compétence(s)";
        break;

    case "update_mon_comp_spe":
        // MODIFICATION / SUPPRESSION GROUPEE DES COMPETENCES SPECIFIQUES
        $typc_cod_list = isset($_POST['typc_cod']) && is_array($_POST['typc_cod'])
            ? $_POST['typc_cod']
            : array();

        $gmoncomp_valeur_list = isset($_POST['gmoncomp_valeur']) && is_array($_POST['gmoncomp_valeur'])
            ? $_POST['gmoncomp_valeur']
            : array();

        $gmoncomp_chance_list = isset($_POST['gmoncomp_chance']) && is_array($_POST['gmoncomp_chance'])
            ? $_POST['gmoncomp_chance']
            : array();

        $comp_spe_delete_list = isset($_POST['comp_spe_delete']) && is_array($_POST['comp_spe_delete'])
            ? $_POST['comp_spe_delete']
            : array();

        $nb_modifications = 0;
        $nb_suppressions  = 0;

        foreach ($typc_cod_list as $typc_cod)
        {
            $typc_cod = (int)$typc_cod;

            if ($typc_cod <= 0)
            {
                continue;
            }

            $req_upd_mon = "select comp_libelle from competences where comp_cod = $typc_cod";
            $stmt   = $pdo->query($req_upd_mon);
            $result = $stmt->fetch();

            if (!$result)
            {
                continue;
            }

            $comp_libelle = $result['comp_libelle'];

            if (isset($comp_spe_delete_list[$typc_cod]))
            {
                $req_upd_mon =
                    "delete from monstre_generique_comp
                     where gmoncomp_gmon_cod = $gmon_cod
                       and gmoncomp_comp_cod = $typc_cod";
                $pdo->query($req_upd_mon);

                writelog($log . "Supression d'une competence : $typc_cod - $comp_libelle\n", 'monstre_edit');

                $nb_suppressions++;
                continue;
            }

            $valeur = max(
                0,
                min(
                    100,
                    isset($gmoncomp_valeur_list[$typc_cod]) ? (int)$gmoncomp_valeur_list[$typc_cod] : 0
                )
            );

            $chance = max(
                0,
                min(
                    100,
                    isset($gmoncomp_chance_list[$typc_cod]) ? (int)$gmoncomp_chance_list[$typc_cod] : 0
                )
            );

            $req_upd_mon =
                "update monstre_generique_comp
                 set gmoncomp_valeur = $valeur,
                     gmoncomp_chance = $chance
                 where gmoncomp_gmon_cod = $gmon_cod
                   and gmoncomp_comp_cod = $typc_cod";

            $pdo->query($req_upd_mon);

            writelog(
                $log .
                "Modification d'une competence : $typc_cod - $comp_libelle" .
                " | Pourcentage: $valeur" .
                " | Chance: $chance\n",
                'monstre_edit'
            );

            $nb_modifications++;
        }

        echo "Modification des compétences spécifiques : $nb_modifications modifiée(s), $nb_suppressions supprimée(s)";
        break;

    case "add_mon_drop":
        // AJOUT D'UN DROP

        $gobj_cod_list = $_REQUEST['gobj_cod'];

        if (!is_array($gobj_cod_list))
        {
            $gobj_cod_list = array($gobj_cod_list);
        }

        $ogmon_equipe = isset($_REQUEST["ogmon_equipe"]) ? "true" : "false";

        foreach ($gobj_cod_list as $gobj_cod)
        {
            $gobj_cod = (int)$gobj_cod;

            $req_upd_mon = "select gobj_nom
                        from objet_generique
                        where gobj_cod = $gobj_cod";

            $stmt = $pdo->query($req_upd_mon);
            $result = $stmt->fetch();

            writelog(
                $log .
                "Ajout d’un Drop : $gobj_cod - "
                . $result['gobj_nom']
                . " Chances: $valeur Equiper: {$ogmon_equipe}\n",
                'monstre_edit'
            );

            $req_upd_mon =
                "insert into objets_monstre_generique
            (ogmon_gmon_cod,ogmon_gobj_cod,ogmon_chance,ogmon_equipe)
            values
            ($gmon_cod,$gobj_cod,$valeur,$ogmon_equipe)";

            $pdo->query($req_upd_mon);
        }

        echo "Ajout d’un ou plusieurs objets";
        break;

    case "update_mon_drops":
        // MODIFICATION / SUPPRESSION GROUPEE DES OBJETS (DROPS)
        $gobj_cod_list = isset($_POST['gobj_cod']) && is_array($_POST['gobj_cod'])
            ? $_POST['gobj_cod']
            : array();

        $valeur_list = isset($_POST['valeur']) && is_array($_POST['valeur'])
            ? $_POST['valeur']
            : array();

        $ogmon_equipe_list = isset($_POST['ogmon_equipe']) && is_array($_POST['ogmon_equipe'])
            ? $_POST['ogmon_equipe']
            : array();

        $drop_delete_list = isset($_POST['drop_delete']) && is_array($_POST['drop_delete'])
            ? $_POST['drop_delete']
            : array();

        $nb_modifications = 0;
        $nb_suppressions  = 0;

        foreach ($gobj_cod_list as $gobj_cod)
        {
            $gobj_cod = (int)$gobj_cod;

            if ($gobj_cod <= 0)
            {
                continue;
            }

            $req_upd_mon = "select gobj_nom
                            from objet_generique
                            where gobj_cod = $gobj_cod";
            $stmt   = $pdo->query($req_upd_mon);
            $result = $stmt->fetch();

            if (!$result)
            {
                continue;
            }

            $gobj_nom = $result['gobj_nom'];

            if (isset($drop_delete_list[$gobj_cod]))
            {
                $req_upd_mon =
                    "delete from objets_monstre_generique
                     where ogmon_gmon_cod = $gmon_cod
                       and ogmon_gobj_cod = $gobj_cod";
                $pdo->query($req_upd_mon);

                writelog(
                    $log . "Suppression d’un Drop : $gobj_cod - $gobj_nom\n",
                    'monstre_edit'
                );

                $nb_suppressions++;
                continue;
            }

            $valeur = isset($valeur_list[$gobj_cod]) ? (int)$valeur_list[$gobj_cod] : 0;

            $ogmon_equipe = isset($ogmon_equipe_list[$gobj_cod]) ? "true" : "false";

            $req_upd_mon =
                "update objets_monstre_generique
                 set ogmon_chance = $valeur,
                     ogmon_equipe = $ogmon_equipe
                 where ogmon_gmon_cod = $gmon_cod
                   and ogmon_gobj_cod = $gobj_cod";

            $pdo->query($req_upd_mon);

            writelog(
                $log .
                "Modification d’un Drop : $gobj_cod - $gobj_nom" .
                " | Chance: $valeur" .
                " | Equiper: {$ogmon_equipe}\n",
                'monstre_edit'
            );

            $nb_modifications++;
        }

        echo "Modification des objets : $nb_modifications modifié(s), $nb_suppressions supprimé(s)";
        break;

    case "add_mon_fonction":

        // Sauvegarder les modifications des effets-auto => save_effet_auto($post, $fonc_gmon_cod, $fonc_perso_cod)
        $message = save_effet_auto($_POST, $gmon_cod, null) ;

        writelog($log . $message, 'monstre_edit');
        echo nl2br($message);
        break;
}