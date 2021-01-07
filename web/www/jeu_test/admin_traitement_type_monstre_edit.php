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
                            . ",gmon_soutien,gmon_amel_deg_dist,gmon_vampirisme,gmon_taille,gmon_description,gmon_quete,gmon_duree_vie, gmon_avatar, gmon_sex) values ($gmon_cod, e'$gmon_nom'"
                            . ",$gmon_for,$gmon_dex,$gmon_int,$gmon_con"
                            . ",$gmon_race_cod,$gmon_temps_tour,$gmon_des_regen,$gmon_valeur_regen,$gmon_vue"
                            . ",$gmon_amelioration_vue,$gmon_amelioration_regen,$gmon_amelioration_degats,$gmon_amelioration_armure"
                            . ",$gmon_niveau,$gmon_nb_des_degats,$gmon_val_des_degats,$gmon_or,$gmon_arme,$gmon_armure"
                            . ",'$gmon_soutien',$gmon_amel_deg_dist,$gmon_vampirisme,$gmon_taille, e'$gmon_description', '$gmon_quete',$gmon_duree_vie, e'$gmon_avatar', $gmon_sex)";
            $pdo->query($req_cre_gmon);
        }
        writelog($log . "Nouveau type de monstre : $gmon_nom \n", 'monstre_edit');
        echo "Nouveau modèle<br>";
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
        $sort_cod    = $_REQUEST['sort_cod'];
        $req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Ajout d'un sort : $sort_cod - " . $result['sort_nom'] . "\n", 'monstre_edit');
        $req_upd_mon =
            "insert into sorts_monstre_generique (sgmon_gmon_cod,sgmon_sort_cod) values ($gmon_cod,$sort_cod)";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Ajout d'un sort";
        break;

    case "delete_mon_terrain":
        $ter_cod    = $_REQUEST['ter_cod'];
        $req_upd_mon = "select ter_nom from terrain where ter_cod = $ter_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Suppression d'un terrain : $ter_cod - " . $result['ter_nom'] . "\n", 'monstre_edit');

        $req_upd_mon =
            "delete from monstre_terrain where tmon_gmon_cod  = $gmon_cod and tmon_ter_cod = $ter_cod";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Suppression d'un sort";
        break;

    case "add_mon_terrain":
        $ter_cod    = $_REQUEST['ter_cod'];
        $req_upd_mon = "select ter_nom from terrain where ter_cod = $ter_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Ajout d'un terrain : $ter_cod - " . $result['ter_nom'] . "\n", 'monstre_edit');

        $tmon_accessible = (isset($_POST['tmon_accessible'])) ? 'O' : 'N';
        $tmon_terrain_pa = $_POST['tmon_terrain_pa'] ;
        $tmon_event_chance = $_POST['tmon_event_chance'] ;
        $tmon_event_pa = $_POST['tmon_event_pa'] ;
        $tmon_message = str_replace("'", "''", $_POST['tmon_message'] );

        $req_upd_mon =
            "insert into monstre_terrain (tmon_gmon_cod,tmon_ter_cod, tmon_accessible, tmon_terrain_pa, tmon_event_chance, tmon_event_pa, tmon_message) values ($gmon_cod,$ter_cod, '$tmon_accessible', '$tmon_terrain_pa', '$tmon_event_chance', '$tmon_event_pa', '$tmon_message')";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Ajout d'un sort";
        break;

    case "delete_mon_immunite":
        $sort_cod    = $_REQUEST['sort_cod'];
        $req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Suppression d'une immunité : $sort_cod - " . $result['sort_nom'] . "\n", 'monstre_edit');

        $req_upd_mon =
            "delete from monstre_generique_immunite where immun_gmon_cod  = $gmon_cod and immun_sort_cod = $sort_cod";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Suppression d’une immunité";
        break;

    case "add_mon_immunite":
        $sort_cod    = $_REQUEST['sort_cod'];
        $req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Ajout d'une immunité : $sort_cod - " . $result['sort_nom'] . "\n", 'monstre_edit');
        $immun_rune = (isset($_POST['immun_rune'])) ? 'O' : 'N';

        $req_upd_mon =
            "insert into monstre_generique_immunite (immun_sort_cod, immun_gmon_cod, immun_valeur, immun_runes) values ($sort_cod, $gmon_cod, $immun_valeur, '$immun_rune')";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Ajout d’une immunité";
        break;

    case "add_mon_comp":
        $req_upd_mon = "select typc_libelle from type_competences where typc_cod = $typc_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Ajout d'un type de competences : $typc_cod - " . $result['typc_libelle'] . " Valeur: $valeur\n", 'monstre_edit');

        $req_upd_mon =
            "insert into gmon_type_comp (gtypc_gmon_cod,gtypc_typc_cod,gtypc_valeur) values ($gmon_cod,$typc_cod,$valeur)";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Ajout d'une competence";
        break;

    case "mod_comp_mon":
        $req_upd_mon =
            "select typc_libelle,gtypc_valeur from gmon_type_comp,type_competences where gtypc_gmon_cod = $gmon_cod and gtypc_typc_cod = $typc_cod and typc_cod = $typc_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Modification d’une compétence : $typc_cod - " . $result['typc_libelle'] . " Chances: " .
                 $result['gtypc_valeur'] . " -> $valeur\n", 'monstre_edit');

        $req_upd_mon =
            "update gmon_type_comp set gtypc_valeur = $valeur where gtypc_gmon_cod = $gmon_cod and gtypc_typc_cod = $typc_cod";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Modification d’une compétence";
        break;

    case "supr_comp_mon":
        $req_upd_mon = "delete from gmon_type_comp  where gtypc_gmon_cod = $gmon_cod and gtypc_typc_cod = $typc_cod";
        //echo $req_upd_mon;
        $stmt = $pdo->query($req_upd_mon);

        $req_upd_mon = "select typc_libelle from type_competences where typc_cod = $typc_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Supression d’un type de compétences : $typc_cod - " . $result['typc_libelle'] .
                 "\n", 'monstre_edit');
        echo "Suppression d’une competence";
        break;

    case "add_mon_comp_spe":
        $req_upd_mon = "select comp_libelle from competences where comp_cod = $typc_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Ajout d'une competence : $typc_cod - " . $result['comp_libelle'], 'monstre_edit');

        $req_upd_mon =
            "insert into monstre_generique_comp (gmoncomp_gmon_cod,gmoncomp_comp_cod,gmoncomp_valeur,gmoncomp_chance) values ($gmon_cod,$typc_cod,$valeur,$chance)";
        //echo $req_upd_mon;
        $stmt = $pdo->query($req_upd_mon);
        echo "Ajout d’une compétence";
        break;

    case "supr_comp_mon_spe":
        $req_upd_mon = "select comp_libelle from competences where comp_cod = $typc_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Supression d'une competence : $typc_cod - " . $result['comp_libelle'], 'monstre_edit');

        $req_upd_mon =
            "delete from monstre_generique_comp where gmoncomp_gmon_cod = $gmon_cod and gmoncomp_comp_cod = $typc_cod";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Supression d’une compétence";
        break;

    case "add_mon_drop":
        // AJOUT D'UN DROP
        $req_upd_mon  = "select gobj_nom from objet_generique where gobj_cod = $gobj_cod";
        $stmt         = $pdo->query($req_upd_mon);
        $result       = $stmt->fetch();
        $ogmon_equipe = isset($_REQUEST["ogmon_equipe"]) ? "true" : "false";
        writelog($log . "Ajout d’un Drop : $gobj_cod - " . $result['gobj_nom'] . " Chances: $valeur Equiper: {$ogmon_equipe}\n", 'monstre_edit');

        $req_upd_mon =
            "insert into objets_monstre_generique (ogmon_gmon_cod,ogmon_gobj_cod,ogmon_chance,ogmon_equipe) values ($gmon_cod,$gobj_cod,$valeur, $ogmon_equipe)";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Ajout d’un drop";
        break;

    case "mod_drop_mon":
        // MODIFIACTION DES CHANCES D'UN DROP
        $req_upd_mon  =
            "select gobj_nom,ogmon_chance from objets_monstre_generique,objet_generique where gobj_cod = $gobj_cod and ogmon_gmon_cod = $gmon_cod and ogmon_gobj_cod = $gobj_cod";
        $stmt         = $pdo->query($req_upd_mon);
        $result       = $stmt->fetch();
        $ogmon_equipe = isset($_REQUEST["ogmon_equipe"]) ? "true" : "false";
        writelog($log . "Modification d’un Drop : $gobj_cod - " . $result['gobj_nom'] . " Chances: " .
                 $result['ogmon_chance'] . " -> $valeur Equiper: {$ogmon_equipe}\n", 'monstre_edit');

        $req_upd_mon =
            "update objets_monstre_generique set ogmon_chance = $valeur, ogmon_equipe=$ogmon_equipe where ogmon_gmon_cod = $gmon_cod and ogmon_gobj_cod = $gobj_cod";
        $stmt        = $pdo->query($req_upd_mon);
        echo "Modification d’un drop";
        break;

    case "supr_drop_mon":
        // SUPPRESSION D'UN DROP
        $req_upd_mon =
            "delete from objets_monstre_generique where ogmon_gmon_cod = $gmon_cod and ogmon_gobj_cod = $gobj_cod";
        //echo $req_upd_mon;
        $stmt = $pdo->query($req_upd_mon);

        $req_upd_mon = "select gobj_nom from objet_generique where gobj_cod = $gobj_cod";
        $stmt        = $pdo->query($req_upd_mon);
        $result      = $stmt->fetch();
        writelog($log . "Suppression d’un Drop : $gobj_cod - " . $result['gobj_nom'] . "\n", 'monstre_edit');
        echo "Suppression d’un drop";
        break;

    case "add_mon_fonction":

        // Sauvegarder les modifications des effets-auto => save_effet_auto($post, $fonc_gmon_cod, $fonc_perso_cod)
        $message = save_effet_auto($_POST, $gmon_cod, null) ;

        writelog($log . $message, 'monstre_edit');
        echo nl2br($message);
        break;
}