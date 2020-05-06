<?php

$droit_modif = 'dcompt_modif_perso';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";
if ($erreur != 0)
{
    die("Pas d'accès à cette page");
}

$fonctions = new fonctions();

// RECUPERATION DES INFORMATIONS POUR LE LOG
$req       = "select compt_nom from compte where compt_cod = $compt_cod";
$stmt      = $pdo->query($req);
$result    = $stmt->fetch();
$compt_nom = $result['compt_nom'];
$req_pers  = "select perso_nom,perso_type_perso from perso where perso_cod = $mod_perso_cod ";
$stmt      = $pdo->query($req_pers);
if ($result = $stmt->fetch())
{
    $perso_mod_nom  = $result['perso_nom'];
    $perso_mod_type = $result['perso_type_perso'];
    if ($perso_mod_type == 1)
    {
        $perso_mod_type = 'Perso';
    } else if ($perso_mod_type == 2)
    {
        $perso_mod_type = 'Monstre';
    } else if ($perso_mod_type == 3)
    {
        $perso_mod_type = 'Familier';
    }
}
$log     =
    date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) modifie le " . $perso_mod_type . " " . $perso_mod_nom . ", numero : $mod_perso_cod\n";
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case "update_perso":
        $fields = array(
            "perso_for",
            "perso_dex",
            "perso_int",
            "perso_con",
            "perso_sex",
            "perso_race_cod",
            "perso_pv",
            "perso_pv_max",
            "perso_amelioration_degats",
            "perso_amel_deg_dex",
            "perso_amelioration_armure",
            "perso_amelioration_vue",
            "perso_temps_tour",
            "perso_pa",
            "perso_vue",
            "perso_des_regen",
            "perso_valeur_regen",
            "perso_po",
            "perso_nb_esquive",
            "perso_niveau",
            "perso_type_perso",
            "perso_px",
            "perso_tangible",
            "perso_nb_tour_intangible",
            "perso_enc_max",
            "perso_amelioration_nb_sort",
            "perso_capa_repar",
            "perso_nb_amel_repar",
            "perso_nb_receptacle",
            "perso_nb_amel_chance_memo",
            "perso_nb_mort",
            "perso_nb_monstre_tue",
            "perso_nb_joueur_tue",
            "perso_renommee_magie",
            "perso_kharma",
            "perso_renommee",
            "perso_nb_des_degats",
            "perso_val_des_degats",
            "perso_nb_amel_comp",
            "perso_actif",
            "perso_prestige",
            "perso_pnj",
            "perso_effets_auto",
            "perso_taille",
            "perso_voie_magique");
        // SELECT POUR LES VALEURS PRECEDENTES
        $req_sel_perso = "select perso_cod,perso_nom";
        foreach ($fields as $i => $value)
        {
            $req_sel_perso = $req_sel_perso . "," . $fields[$i];
        }
        $req_sel_perso = $req_sel_perso . " from perso where perso_cod = $mod_perso_cod";
        //echo $req_sel_perso;
        $stmt_perso   = $pdo->query($req_sel_perso);
        $result_perso = $stmt_perso->fetch();
        foreach ($fields as $i => $value)
        {
            if (!($_POST[$fields[$i]] == $result_perso[$fields[$i]]))
            {
                $log = $log . "Modification du champ " . $fields[$i] . " : " . $result_perso[$fields[$i]] . " => "
                       . $_POST[$fields[$i]] . "\n";
            }
        }
        // CAS SPÉCIFIQUE POUR LE NOM
        if ($_POST['mod_perso_nom'] != $result_perso['perso_nom'])
        {
            $log =
                $log . "Modification du champ Nom : " . $result_perso['perso_nom'] . " => " . $_POST['mod_perso_nom'] . "\n";
        }
        if (!isset($mod_perso_nom))
        {
            $mod_perso_nom = "";
        } else
        {
            $mod_perso_nom = str_replace("'", "’", $mod_perso_nom);
        }
        $req_upd_perso = "update perso set "
                         . "perso_nom = e'" . pg_escape_string($mod_perso_nom) . "',"
                         . "perso_for = $perso_for,"
                         . "perso_dex = $perso_dex,"
                         . "perso_int = $perso_int,"
                         . "perso_con = $perso_con,"
                         . "perso_amelioration_degats = $perso_amelioration_degats,"
                         . "perso_amel_deg_dex = $perso_amel_deg_dex,"
                         . "perso_amelioration_armure = $perso_amelioration_armure,"
                         . "perso_amelioration_vue = $perso_amelioration_vue,"
                         . "perso_sex = '$perso_sex',"
                         . "perso_race_cod = $perso_race_cod,"
                         . "perso_pv = $perso_pv,"
                         . "perso_pv_max = $perso_pv_max,"
                         . "perso_temps_tour = $perso_temps_tour,"
                         . "perso_pa = $perso_pa,"
                         . "perso_des_regen = $perso_des_regen,"
                         . "perso_valeur_regen = $perso_valeur_regen,"
                         . "perso_vue = $perso_vue,"
                         . "perso_po = $perso_po,"
                         . "perso_nb_esquive = $perso_nb_esquive,"
                         . "perso_niveau = $perso_niveau,"
                         . "perso_type_perso = " . $_POST['perso_type_perso'] . ","    // La variable est modifiée dans les includes de connexion de admin_perso_edit.php
                         . "perso_px = $perso_px,"
                         . "perso_tangible = '$perso_tangible',"
                         . "perso_nb_tour_intangible = $perso_nb_tour_intangible,"
                         . "perso_nb_mort = $perso_nb_mort,"
                         . "perso_nb_monstre_tue = $perso_nb_monstre_tue,"
                         . "perso_nb_joueur_tue = $perso_nb_joueur_tue,"
                         . "perso_renommee_magie = $perso_renommee_magie,"
                         . "perso_kharma = $perso_kharma,"
                         . "perso_renommee = $perso_renommee,"
                         . "perso_enc_max = $perso_enc_max,"
                         . "perso_amelioration_nb_sort = $perso_amelioration_nb_sort,"
                         . "perso_capa_repar = $perso_capa_repar,"
                         . "perso_nb_amel_repar = $perso_nb_amel_repar,"
                         . "perso_nb_receptacle = $perso_nb_receptacle,"
                         . "perso_nb_amel_chance_memo = $perso_nb_amel_chance_memo,"
                         . "perso_nb_des_degats = $perso_nb_des_degats,"
                         . "perso_val_des_degats = $perso_val_des_degats,"
                         . "perso_nb_amel_comp = $perso_nb_amel_comp,"
                         . "perso_actif = '$perso_actif',"
                         . "perso_pnj = '$perso_pnj',"
                         . "perso_prestige = $perso_prestige,"
                         . "perso_effets_auto = $perso_effets_auto,"
                         . "perso_taille = $perso_taille,"
                         . "perso_voie_magique = $perso_voie_magique"
                         . " where perso_cod = '$mod_perso_cod'";

        //echo $req_upd_perso."<br>";
        $stmt_perso = $pdo->query($req_upd_perso);

        echo "<div class='bordiv'>MAJ du perso<br /><pre>$log</pre></div>";
        writelog($log, 'perso_edit');
        break;

    case "update_competences":
        foreach ($_POST as $i => $value)
        {
            if (substr($i, 0, 11) == "PERSO_COMP_")
            {
                $cmp_cod         = substr($i, 11);
                $req_sel_comp    = "select comp_libelle,pcomp_modificateur from perso_competences,competences "
                                   . " where pcomp_perso_cod = $mod_perso_cod"
                                   . " and pcomp_pcomp_cod = $cmp_cod"
                                   . " and comp_cod = $cmp_cod";
                $stmt_upd_comp   = $pdo->query($req_sel_comp);
                $result_upd_comp = $stmt_upd_comp->fetch();
                if ($result_upd_comp['pcomp_modificateur'] != $value)
                {
                    $log           =
                        $log . "Modification de la compétence " . $result_upd_comp['comp_libelle'] . " : " . $result_upd_comp['pcomp_modificateur'] . " => " . $value . "\n";
                    $req_upd_comp  = "update perso_competences set "
                                     . "pcomp_modificateur = $value"
                                     . " where pcomp_perso_cod = '$mod_perso_cod'"
                                     . "and pcomp_pcomp_cod = '$cmp_cod' ";
                    $stmt_upd_comp = $pdo->query($req_upd_comp);
                }
            }
        }
        echo "<div class='bordiv'>MAJ des compétences<br /><pre>$log</pre></div>";
        writelog($log, 'perso_edit');
        break;

    case "add_competence":
        $req_upd_comp = "insert into perso_competences (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur ) "
                        . " values ($mod_perso_cod,$comp_cod,$comp_modificateur)";

        $stmt_upd_comp   = $pdo->query($req_upd_comp);
        $req_comp        = "select comp_libelle from competences where comp_cod = $comp_cod ";
        $stmt_upd_comp   = $pdo->query($req_comp);
        $result_upd_comp = $stmt_upd_comp->fetch();
        $log             .= "Ajout d’une compétence : $comp_cod - " . $result_upd_comp['comp_libelle'] . "\n";
        writelog($log, 'perso_edit');
        echo "<div class='bordiv'>Ajout d’une compétence<br /><pre>$log</pre></div>";
        break;

    case "suppr_competence":
        if (!($comp_cod == ""))
        {
            $req_upd_comp =
                "delete from perso_competences where pcomp_pcomp_cod  = $comp_cod and pcomp_perso_cod = $mod_perso_cod";

            $stmt_upd_comp = $pdo->query($req_upd_comp);

            $req_comp        = "select comp_libelle from competences where comp_cod = $comp_cod ";
            $stmt_upd_comp   = $pdo->query($req_comp);
            $result_upd_comp = $stmt_upd_comp->fetch();
            $log             .= "Suppression d’une competence : $comp_cod - " . $result_upd_comp['comp_libelle'] . "\n";
            writelog($log, 'perso_edit');
            echo "<div class='bordiv'>Suppression d’une competence<br /><pre>$log</pre></div>";
        }
        break;

    case "update_bonmal":

        // On liste les bonus / malus, et pour chacun, on regarde ce qui a changé.
        $req_bm = "select tonbus_libelle, bonus_cod, bonus_tbonus_libc, bonus_valeur, bonus_nb_tours
			from bonus
			inner join bonus_type on tbonus_libc = bonus_tbonus_libc
			where bonus_perso_cod = $mod_perso_cod";
        $stmt   = $pdo->query($req_bm);
        while ($result = $stmt->fetch())
        {
            $lib     = $result['tonbus_libelle'];
            $anc_val = $result['bonus_valeur'];
            $anc_dur = $result['bonus_nb_tours'];
            $tbon    = $result['bonus_cod'];
            $id      = $tbon;
            if (isset($_POST["PERSO_BM_val_$id"]) && isset($_POST["PERSO_BM_dur_$id"]))
            {
                $nouv_val = $_POST["PERSO_BM_val_$id"];
                $nouv_dur = $_POST["PERSO_BM_dur_$id"];
                if ($nouv_val != $anc_val || $nouv_dur != $anc_dur)
                {
                    $log         =
                        $log . "Modification du bonus/malus « $lib » : { $anc_val / $anc_dur tours } => { $nouv_val / $nouv_dur tours }\n";
                    $req_bm      = "update bonus set 
							bonus_valeur = $nouv_val,
							bonus_nb_tours = $nouv_dur
						where bonus_perso_cod = $mod_perso_cod
							and bonus_cod = '$tbon' ";
                    $stmt_upd_bm = $pdo->query($req_bm);
                }
            }
        }
        echo "<div class='bordiv'>MAJ des bonus / malus<br /><pre>$log</pre></div>";
        writelog($log, 'perso_edit');
        break;

    case "add_bonmal":
        if (!isset($bonmal_cumul)) $bonmal_cumul = "";

        $bonmal_type = $bonmal_cod . ($bonmal_cumul == "on" ? "+" : "");
        $req_bm      = "select ajoute_bonus ($mod_perso_cod, '$bonmal_type', $bonmal_duree, $bonmal_valeur)";
        $stmt_upd_bm = $pdo->query($req_bm);

        $req_bm        = "select tonbus_libelle from bonus_type where tbonus_libc = '$bonmal_cod'";
        $stmt_upd_bm   = $pdo->query($req_bm);
        $result_upd_bm = $stmt_upd_bm->fetch();
        $log           .= "Ajout d’un bonus/malus « " . $result_upd_bm['tonbus_libelle'] . " » : { $bonmal_valeur / $bonmal_duree tours }" . ($bonmal_cumul == "on" ? " [cumulatif]" : "") . "\n";
        writelog($log, 'perso_edit');
        echo "<div class='bordiv'>Ajout d’un bonus/malus<br /><pre>$log</pre></div>";
        break;

    case "suppr_bonmal":
        //print_r($_REQUEST); die();
        if (!($bonmal_cod == "") && !($type_bm == ""))
        {


            if ($type_bm == "CARAC")
            {
                // ATTENTION: PAS de DELETE dans le cas des bonus de caracs, sinon le perso garderait son bonus à vie!!!!!
                $req_bm      =
                    "update carac_orig set corig_nb_tours=0, corig_dfin=null where corig_cod = '$bonmal_cod'; ";
                $stmt_upd_bm = $pdo->query($req_bm);
                $req_bm      = "select f_remise_caracs($mod_perso_cod); ";
                $stmt_upd_bm = $pdo->query($req_bm);
            } else
            {
                $req_bm         =
                    "select tonbus_libelle from bonus join bonus_type on tbonus_libc=bonus_tbonus_libc where bonus_cod = '$bonmal_cod'";
                $stmt_upd_bm    = $pdo->query($req_bm);
                $result_upd_bm  = $stmt_upd_bm->fetch();
                $tonbus_libelle = $result_upd_bm['tonbus_libelle'];

                $req_bm      = "delete from bonus where
				bonus_perso_cod = $mod_perso_cod
				and bonus_valeur = $bonmal_valeur_debut
				and bonus_cod = '$bonmal_cod'";
                $stmt_upd_bm = $pdo->query($req_bm);
            }

            $log .= "Suppression du bonus/malus « " . $tonbus_libelle . " »\n";
            writelog($log, 'perso_edit');
            echo "<div class='bordiv'>Suppression de bonus/malus<br /><pre>$log</pre></div>";
        }
        break;

    case "update_inventaire":
        // OBJETS DISPARUS DE L’INVENTAIRE
        $req_inv         = "select distinct obj_gobj_cod ";
        $req_inv         = $req_inv . "from perso_objets,objets, objet_generique ";
        $req_inv         = $req_inv . "where perobj_perso_cod = $mod_perso_cod ";
        $req_inv         = $req_inv . "and perobj_equipe <> 'O' ";
        $req_inv         = $req_inv . "and obj_gobj_cod = gobj_cod ";
        $req_inv         = $req_inv . "and obj_nom = gobj_nom ";
        $req_inv         = $req_inv . "and perobj_obj_cod = obj_cod ";
        $stmt_inventaire = $pdo->query($req_inv);
        while ($result_inventaire = $stmt_inventaire->fetch())
        {
            $obj_cod = $result_inventaire['obj_gobj_cod'];
            $to_find = ";" . $result_inventaire['obj_gobj_cod'] . ":";
            $pos1    = strpos(";" . $compiledInv, $to_find);
            //$pos1 = stripos($compiledInv, $to_find);
            if ($pos1 === false)
            {
                //echo "The string '$to_find' was not found in the string";
                $req_add_obj      = "select obj_cod from perso_objets,objets where " .
                                    "perobj_perso_cod = $mod_perso_cod and perobj_obj_cod = obj_cod and obj_gobj_cod = $obj_cod";
                $stmt_inventaire2 = $pdo->query($req_add_obj);
                while ($result_inventaire2 = $stmt_inventaire2->fetch())
                {
                    $del_obj_cod           = $result_inventaire2['obj_cod'];
                    $req                   = 'select gobj_nom,obj_nom from objets,objet_generique
						where obj_cod = ' . $del_obj_cod . '
						and obj_gobj_cod = gobj_cod ';
                    $stmt                  = $pdo->query($req);
                    $result                = $stmt->fetch();
                    $log                   .= 'Effacement de l’objet ' . $del_obj_cod . ' - ' . $result['obj_nom'] . ' (objet générique : ' . $result['gobj_nom'] . ')' . "\n";
                    $req_supr_obj          = "select f_del_objet($del_obj_cod)";
                    $stmt_inventaire_suppr = $pdo->query($req_supr_obj);
                }
            }
        }

        $objs = explode(";", $compiledInv);

        $nb_ajoute = 0;
        for ($i = 0; $i < count($objs); $i++)
        {
            if ($objs[$i] != "")
            {
                $obj        = explode(":", $objs[$i]);
                $obj_cod    = $obj[0];
                $obj_nb     = $obj[1];
                $obj_nb_pre = 0;
                // NOMBRE D’OBJETS DE CE TYPE DÉJÀ DANS L’INVENTAIRE
                $req_inv         = "select count(obj_cod) as nombre ";
                $req_inv         = $req_inv . "from perso_objets,objets ";
                $req_inv         = $req_inv . "where perobj_perso_cod = $mod_perso_cod ";
                $req_inv         = $req_inv . "and perobj_equipe <> 'O' ";
                $req_inv         = $req_inv . "and perobj_obj_cod = obj_cod ";
                $req_inv         = $req_inv . "and obj_gobj_cod = $obj_cod ";
                $stmt_inventaire = $pdo->query($req_inv);
                if ($result_inventaire = $stmt_inventaire->fetch())
                {
                    $obj_nb_pre = $result_inventaire['nombre'];
                }
                // NBR OBJETS A AJOUTER
                $obj_nb_add = $obj_nb - $obj_nb_pre;
                if ($obj_nb_add > 0)
                {
                    // AJOUT D’OBJETS
                    $req_add_obj     = "select cree_objet_perso_nombre($obj_cod,$mod_perso_cod,$obj_nb_add)";
                    $stmt_inventaire = $pdo->query($req_add_obj);

                    $req_inv           = "select gobj_nom from objet_generique where gobj_cod = $obj_cod ";
                    $stmt_inventaire   = $pdo->query($req_inv);
                    $result_inventaire = $stmt_inventaire->fetch();
                    $log               =
                        $log . "Ajout d’objets dans l’inventaire : $obj_cod - " . $result_inventaire['gobj_nom'] . ". Quantité : $obj_nb_add\n";
                    $nb_ajoute++;
                } else if ($obj_nb_add < 0)
                {
                    // SUPPRESSION D’OBJETS
                    $req_inv           = "select gobj_nom from objet_generique where gobj_cod = $obj_cod ";
                    $stmt_inventaire   = $pdo->query($req_inv);
                    $result_inventaire = $stmt_inventaire->fetch();
                    $log               =
                        $log . "Suppression d’objets dans l’inventaire : $obj_cod - " . $result_inventaire['gobj_nom'] . ". Quantité : $obj_nb_add\n";
                    $nb_ajoute++;

                    $obj_nb_add      = -1 * $obj_nb_add;
                    $req_add_obj     = "select obj_cod from perso_objets,objets, objet_generique where " .
                                       " perobj_perso_cod = $mod_perso_cod  and perobj_obj_cod = obj_cod and obj_gobj_cod = $obj_cod " .
                                       " and obj_gobj_cod = gobj_cod and obj_nom = gobj_nom LIMIT $obj_nb_add ";
                    $stmt_inventaire = $pdo->query($req_add_obj);
                    while ($result_inventaire = $stmt_inventaire->fetch())
                    {
                        $del_obj_cod           = $result_inventaire['obj_cod'];
                        $req                   = 'select gobj_nom,obj_nom from objets,objet_generique
							where obj_cod = ' . $del_obj_cod . '
							and obj_gobj_cod = gobj_cod ';
                        $stmt                  = $pdo->query($req);
                        $result                = $stmt->fetch();
                        $log                   .= 'Effacement de l’objet ' . $del_obj_cod . ' - ' . $result['obj_nom'] . ' (objet générique : ' . $result['gobj_nom'] . ')' . "\n";
                        $req_supr_obj          = "select f_del_objet($del_obj_cod)";
                        $stmt_inventaire_suppr = $pdo->query($req_supr_obj);
                    }
                }
            }
        }
        if ($log != '')
        {
            writelog($log, 'perso_edit');
        }
        echo "<div class='bordiv'>Modification d’inventaire : <br /><pre>$log</pre></div>";
        break;

    case "update_sorts":
        // SORTS SUPPRIMES
        $req_sm    = "select psort_sort_cod from perso_sorts "
                     . "where psort_perso_cod = $mod_perso_cod ";
        $stmt_sort = $pdo->query($req_sm);
        while ($result_sort = $stmt_sort->fetch())
        {
            $sort_cod = $result_sort['psort_sort_cod'];
            if (!isset($_POST["PERSO_SORT_" . $sort_cod]))
            {
                $req_sm            = "select sort_cod,sort_nom from sorts where sort_cod = $sort_cod ";
                $stmt_sort_suppr   = $pdo->query($req_sm);
                $result_sort_suppr = $stmt_sort_suppr->fetch();
                $log               .= "Suppression d’un sort : $sort_cod - " . $result_sort_suppr['sort_nom'] . "\n";
                writelog($log, 'perso_edit');

                $req_suppr_sort  =
                    "delete from perso_sorts where psort_perso_cod = $mod_perso_cod and psort_sort_cod= $sort_cod";
                $stmt_sort_suppr = $pdo->query($req_suppr_sort);
            }
        }

        foreach ($_POST as $i => $value)
        {
            if (substr($i, 0, 11) == "PERSO_SORT_")
            {
                $sort_cod  = substr($i, 11);
                $req_sm    = "select psort_sort_cod from perso_sorts "
                             . "where psort_perso_cod = $mod_perso_cod "
                             . "and psort_sort_cod = $sort_cod ";
                $stmt_sort = $pdo->query($req_sm);
                if (!$result_sort = $stmt_sort->fetch())
                {
                    // Le sort non trouvé -> AJOUT
                    $req_add_sort = "insert into perso_sorts (psort_perso_cod,psort_sort_cod) "
                                    . " values ($mod_perso_cod,$sort_cod)";
                    $stmt_sort    = $pdo->query($req_add_sort);

                    $req_sm      = "select sort_cod,sort_nom from sorts where sort_cod = $sort_cod ";
                    $stmt_sort   = $pdo->query($req_sm);
                    $result_sort = $stmt_sort->fetch();
                    $log         .= "Ajout d’un sort : $sort_cod - " . $result_sort['sort_nom'] . "\n";
                    writelog($log, 'perso_edit');
                }
            }
        }
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "update_titres":
        if (!($ptitre_titre == ""))
        {
            $ptitre_titre = str_replace("''", "\'", $ptitre_titre);
            $req_upd_titr = "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date) "
                            . " values ($mod_perso_cod,e'" . pg_escape_string($ptitre_titre) . "',now())";
            //echo "REQ= ".$req_upd_titr."<br>\n";
            $stmt_upd_titr = $pdo->query($req_upd_titr);
            $log           .= "Ajout d’un titre: \"$ptitre_titre\" \n";
            echo "<div class='bordiv'><pre>$log</pre></div>";
            writelog($log, 'perso_edit');
        }
        break;

    case "update_titre":
        if (!($ptitre_titre == ""))
        {
            $req_upd_titre    = "select ptitre_titre from perso_titre where ptitre_cod  = $ptitre_cod ";
            $stmt_upd_titre   = $pdo->query($req_upd_titre);
            $result_upd_titre = $stmt_upd_titre->fetch();
            $log              .= "Modification du titre : " . $result_upd_titre['ptitre_titre'] . " -> $ptitre_titre\n";
            writelog($log, 'perso_edit');

            $ptitre_titre   = str_replace("’", "\'", $ptitre_titre);
            $req_upd_titre  = "update perso_titre set "
                              . "ptitre_titre = e'" . pg_escape_string($ptitre_titre) . "'"
                              . " where ptitre_cod = $ptitre_cod";
            $stmt_upd_titre = $pdo->query($req_upd_titre);
            echo "<div class='bordiv'><pre>$log</pre></div>";
        }
        break;

    case "supr_titre":
        $req_upd_titre    = "select ptitre_titre from perso_titre where ptitre_cod  = $ptitre_cod ";
        $stmt_upd_titre   = $pdo->query($req_upd_titre);
        $result_upd_titre = $stmt_upd_titre->fetch();
        $log              .= "Suppression du titre : " . $result_upd_titre['ptitre_titre'] . "\n";
        writelog($log, 'perso_edit');
        $req_upd_titre  = "delete from perso_titre where ptitre_cod  = $ptitre_cod";
        $stmt_upd_titre = $pdo->query($req_upd_titre);
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "move_perso":
        $new_etage = $_REQUEST['etage'];
        $err_depl  = 0;
        $req       = "select pos_cod, etage_arene,
				pos_x::text || ', ' || pos_y::text || ', ' || pos_etage::text || ' (' || etage_libelle || ')' as position from positions
			inner join etage on etage_numero = pos_etage
			where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $new_etage ";
        $stmt      = $pdo->query($req);
        if ($stmt->rowCount() == 0)
        {
            echo "<div class='bordiv'>Erreur ! Aucune position trouvée à ces coordonnées</div>";
            $err_depl = 1;
        }
        $result      = $stmt->fetch();
        $pos_cod     = $result['pos_cod'];
        $arene       = $result['etage_arene'];
        $nv_position = $result['position'];
        $req         = "select mur_pos_cod from murs where mur_pos_cod = $pos_cod ";
        $stmt        = $pdo->query($req);
        if ($stmt->rowCount() != 0)
        {
            echo "<div class='bordiv'>Erreur ! Impossible de déplacer le perso : un mur en destination.</div>";
            $err_depl = 1;
        }
        if ($err_depl == 0)
        {
            // insertion dun évènement
            $texte_evt             = "[perso_cod1] a été déplacé par un admin quête.";
            $levt                  = new ligne_evt();
            $levt->levt_tevt_cod   = 43;
            $levt->levt_perso_cod1 = $mod_perso_cod;
            $levt->levt_texte      = $texte_evt;
            $levt->levt_attaquant  = $mod_perso_cod;
            $levt->levt_cible      = $mod_perso_cod;
            $levt->levt_lu         = 'N';
            $levt->levt_visible    = 'N';
            $levt->stocke(true);
            unset($levt);
            // effacement des locks
            $req  = "delete from lock_combat where lock_cible = $mod_perso_cod ";
            $stmt = $pdo->query($req);
            $req  = "delete from lock_combat where lock_attaquant = $mod_perso_cod ";
            $stmt = $pdo->query($req);

            // Position de départ
            $req_position = "select pos_cod, pos_x::text || ', ' || pos_y::text || ', ' || pos_etage::text || ' (' || etage_libelle || ')' as position,
					etage_arene
				from perso_position
				inner join positions on pos_cod = ppos_pos_cod
				inner join etage on etage_numero = pos_etage
				where ppos_perso_cod = $mod_perso_cod ";
            $stmt         = $pdo->query($req_position);
            $result       = $stmt->fetch();
            $anc_pos_cod  = $result['pos_cod'];
            $anc_arene    = $result['etage_arene'];
            $anc_position = $result['position'];
            $log          .= "Déplacement de $anc_position vers $nv_position.";

            // déplacement
            $req  = "update perso_position set ppos_pos_cod = $pos_cod where ppos_perso_cod = $mod_perso_cod ";
            $stmt = $pdo->query($req);

            switch ($anc_arene . $arene)
            {
                case 'NO':    // D’un étage normal vers une arène
                    $req  = "delete from perso_arene where parene_perso_cod = $mod_perso_cod ";
                    $stmt = $pdo->query($req);
                    $req  = "insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree)
						values($mod_perso_cod, $new_etage, $anc_pos_cod, now()) ";
                    $stmt = $pdo->query($req);
                    $log  .= "\nCette position est en arène : le personnage en ressortira à sa position d’origine.";
                    break;

                case 'OO':    // D’une arène vers une autre
                    $req  =
                        "update perso_arene set parene_etage_numero = $new_etage where parene_perso_cod = $mod_perso_cod";
                    $stmt = $pdo->query($req);
                    $log  .= "\nCette position est en arène, le perso était déjà dans une arène : il ressortira à la position d’où il est rentré dans la première arène.";
                    break;

                case 'ON':    // D’une arène vers un étage normal
                    $req  = "delete from perso_arene where parene_perso_cod = $mod_perso_cod ";
                    $stmt = $pdo->query($req);
                    $log  .= "\nAttention ! Le perso était en arène : sa position d’entrée dans l’arène est perdue !";
                    // Si on ne le supprimait pas, on empêcherait le perso de rentrer à nouveau en arène...
                    break;

                case 'NN':    // D’un étage normal vers un étage normal
                    // Rien à faire
                    break;
            }

            writelog($log, 'perso_edit');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        }
        break;

    case "add_new_object":
        // recherche du num objet generique
        $req                = "select nextval('seq_gobj_cod') as gobj";
        $stmt               = $pdo->query($req);
        $result             = $stmt->fetch();
        $gobj_cod           = $result['gobj'];
        $nom_objet          = pg_escape_string(htmlspecialchars(str_replace('’', '\'', $nom_objet)));
        $nom_objet_non_iden = pg_escape_string(htmlspecialchars(str_replace('’', '\'', $nom_objet_non_iden)));
        $desc               = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $desc))));
        // création dans les objets génériques
        $req  = "insert into objet_generique (gobj_cod,gobj_nom,gobj_nom_generique,gobj_tobj_cod,gobj_valeur,gobj_poids,gobj_description,gobj_deposable,gobj_visible,gobj_echoppe) 
		values ($gobj_cod,'$nom_objet','$nom_objet_non_iden',11,0,$poids_objet,'$desc','O','O','N')";
        $stmt = $pdo->query($req);
        // insertion dun évènement
        $texte_evt             = "Un admin quête a créé un objet dans l’inventaire de [perso_cod1].";
        $levt                  = new ligne_evt();
        $levt->levt_tevt_cod   = 43;
        $levt->levt_perso_cod1 = $mod_perso_cod;
        $levt->levt_texte      = $texte_evt;
        $levt->levt_attaquant  = $mod_perso_cod;
        $levt->levt_cible      = $mod_perso_cod;
        $levt->levt_lu         = 'N';
        $levt->levt_visible    = 'N';
        $levt->stocke(true);
        unset($levt);
        // création
        $req  = "select cree_objet_perso_nombre($gobj_cod,$mod_perso_cod,1)";
        $stmt = $pdo->query($req);

        $log .= "Création d’un nouvel objet, $nom_objet, dans l’inventaire.";
        writelog($log, 'perso_edit');
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "update_religion":
        $req_upd_rel  =
            "update dieu_perso set dper_dieu_cod = $dper_dieu_cod,dper_niveau = $dper_niveau, dper_points= $dper_points where dper_perso_cod = $mod_perso_cod";
        $stmt_upd_rel = $pdo->query($req_upd_rel);
        $log          .= "Modification de la religion\n";
        writelog($log, 'perso_edit');
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "add_religion":
        $req_upd_rel  = "insert into dieu_perso(dper_perso_cod,dper_dieu_cod,dper_niveau,dper_points) "
                        . "values ($mod_perso_cod,$dper_dieu_cod,$dper_niveau,$dper_points)";
        $stmt_upd_rel = $pdo->query($req_upd_rel);
        $log          .= "Ajout de la religion\n";
        writelog($log, 'perso_edit');
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "supr_religion":
        $req_upd_rel  = "delete from dieu_perso where dper_perso_cod  = $mod_perso_cod";
        $stmt_upd_rel = $pdo->query($req_upd_rel);
        $log          .= "Suppression de la religion\n";
        writelog($log, 'perso_edit');
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "update_familier":
        $req     =
            "select coalesce(pfam_duree_vie, 0) as pfam_duree_vie from perso_familier where pfam_perso_cod = $mod_perso_cod";
        $stmt_fm = $pdo->query($req);
        if ($result_fm = $stmt_fm->fetch() && isset($fam_duree_vie))
        {
            $anc_duree     = $result_fm['pfam_duree_vie'];
            $fam_duree_vie = ($fam_duree_vie == 0) ? 'NULL' : $fam_duree_vie;
            $req           =
                "update perso_familier set pfam_duree_vie = $fam_duree_vie where pfam_perso_cod = $mod_perso_cod";
            $stmt_fm       = $pdo->query($req);
            $log           .= "Modification du familier ; durée de vie : $anc_duree => $fam_duree_vie tour(s)\n";
            writelog($log, 'perso_edit');
        } else
        {
            $log .= "Erreur de paramètres !";
        }
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "ajout_familier":
        // Vérifications :
        // 0) Paramètres
        // 1) Le nouveau familier est de type monstre.
        // 2) Le nouveau familier est libre
        // 3) Le perso est libre
        // 4) Le perso et le nouveau familier sont sur la même case
        // 5) Le perso est un monstre ou un aventurier (pas un familier)
        $erreur     = false;
        $txt_erreur = '';
        $pos_fam    = -2;
        $pos_perso  = -1;
        if (!isset($fam_cod) || !isset($fam_duree_vie))
        {
            $txt_erreur = 'Erreur de paramètres.';
            $erreur     = true;
        }
        // Vérifs sur le familier
        if (!$erreur)
        {
            $req     = "select ppos_pos_cod, perso_type_perso, coalesce(pfam_perso_cod, 0) as pfam_perso_cod, perso_nom
				from perso
				inner join perso_position on ppos_perso_cod = perso_cod
				left outer join perso_familier on pfam_familier_cod = perso_cod
				where perso_cod = $fam_cod";
            $stmt_fm = $pdo->query($req);
            if ($result_fm = $stmt_fm->fetch())
            {
                $pos_fam    = $result_fm['ppos_pos_cod'];
                $fam_type   = $result_fm['perso_type_perso'];
                $fam_maitre = $result_fm['pfam_perso_cod'];
                $fam_nom    = $result_fm['perso_nom'];
                if ($fam_maitre > 0)
                {
                    $txt_erreur = "Erreur ! $fam_nom ($fam_cod) a déjà un maître !";
                    $erreur     = true;
                }
                if ($fam_type != 2)
                {
                    $txt_erreur = "Erreur ! $fam_nom ($fam_cod) n’est pas un monstre !";
                    $erreur     = true;
                }
            } else
            {
                $txt_erreur = "Erreur ! Monstre n°$fam_cod introuvable.";
                $erreur     = true;
            }
        }
        // Vérifs sur le perso
        if (!$erreur)
        {
            $req     = "select ppos_pos_cod, perso_type_perso, coalesce(pfam_familier_cod, 0) as pfam_familier_cod, perso_nom
				from perso
				inner join perso_position on ppos_perso_cod = perso_cod
				left outer join perso_familier on pfam_perso_cod = perso_cod
				where perso_cod = $mod_perso_cod";
            $stmt_fm = $pdo->query($req);
            if ($result_fm = $stmt_fm->fetch())
            {
                $pos_perso  = $result_fm['ppos_pos_cod'];
                $perso_type = $result_fm['perso_type_perso'];
                $perso_fam  = $result_fm['pfam_familier_cod'];
                $perso_nom  = $result_fm['perso_nom'];
                if ($perso_fam > 0)
                {
                    $txt_erreur = "Erreur ! $perso_nom ($mod_perso_cod) a déjà un familier !";
                    $erreur     = true;
                }
                if ($perso_type != 1 && $perso_type != 2)
                {
                    $txt_erreur =
                        "Erreur ! $perso_nom ($mod_perso_cod) n’est pas un aventurier ni un monstre ! (C’est sans doute un familier).";
                    $erreur     = true;
                }
                if ($pos_perso != $pos_fam)
                {
                    $txt_erreur =
                        "Erreur ! $perso_nom ($mod_perso_cod) et $fam_nom ($fam_cod) ne sont pas sur la même case !";
                    $erreur     = true;
                }
            } else
            {
                $txt_erreur = "Erreur ! Perso n°$mod_perso_cod introuvable.";
                $erreur     = true;
            }
        }
        if (!$erreur)
        {
            $fam_duree_vie = ($fam_duree_vie == 0) ? 'NULL' : $fam_duree_vie;
            $req           = "insert into perso_familier(pfam_perso_cod, pfam_familier_cod, pfam_duree_vie) "
                             . "values ($mod_perso_cod, $fam_cod, $fam_duree_vie)";
            $stmt_fm       = $pdo->query($req);

            $req     = "update perso set perso_type_perso = 3 where perso_cod  = $fam_cod";
            $stmt_fm = $pdo->query($req);

            $log .= "Ajout du familier $fam_nom ($fam_cod)\n";
            writelog($log, 'perso_edit');
        } else
            $log .= $txt_erreur;
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "suppr_familier":
        $req     = "select pfam_familier_cod from perso_familier where pfam_perso_cod  = $mod_perso_cod";
        $stmt_fm = $pdo->query($req);

        if ($result_fm = $stmt_fm->fetch())
        {
            $num_familier = $result_fm['pfam_familier_cod'];
            $req          = "update perso set perso_type_perso = 2 where perso_cod  = $num_familier";
            $stmt_fm      = $pdo->query($req);
            $req          = "delete from perso_familier where pfam_perso_cod  = $mod_perso_cod";
            $stmt_fm      = $pdo->query($req);
        }
        $log .= "Détachement du familier.\n";
        writelog($log, 'perso_edit');
        echo "<div class='bordiv'><pre>$log</pre></div>";
        break;

    case "add_effet_auto":
        // Modification des effets automatiques
        $fonctions_supprimees = explode(',', trim(fonctions::format($_POST['fonctions_supprimees']), ','));
        $fonctions_annulees   = explode(',', trim(fonctions::format($_POST['fonctions_annulees']), ','));
        $fonctions_existantes = explode(',', trim(fonctions::format($_POST['fonctions_existantes']), ','));
        $fonctions_ajoutees   = explode(',', trim(fonctions::format($_POST['fonctions_ajoutees']), ','));

        $message = '';
        // Ajout d’effet
        foreach ($fonctions_ajoutees as $numero)
        {
            if (!empty($numero) || $numero === 0)
            {
                if (!in_array($numero, $fonctions_annulees))
                {
                    $fonc_type         = $_POST['declenchement_' . $numero];
                    $fonc_nom          = $_POST['fonction_type_' . $numero];
                    $fonc_effet        = $_POST['fonc_effet' . $numero];
                    if (!empty($_POST['fonc_cumulatif' . $numero]) && ($_POST['fonc_cumulatif' . $numero] == "on")) $fonc_effet .= "+";

                    $fonc_unite_valid  = 1 * $_POST['fonc_validite_unite' . $numero];
                    $fonc_validite     = $fonc_unite_valid * $_POST['fonc_validite' . $numero];
                    $fonc_validite_sql = ($fonc_validite === 0) ? "NULL" : "now() + '$fonc_validite minutes'::interval";

                    $fonc_trigger_param = array() ;
                    foreach($_POST as $key => $val){
                        if ((substr($key, 0, 10)=="fonc_trig_") && (substr($key, -strlen("{$numero}"))==$numero)) {
                            $fonc_trigger_param[substr($key, 0, -strlen("{$numero}"))]= $val ;
                        }
                    }

                    $req       = "INSERT INTO fonction_specifique (fonc_nom, fonc_perso_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message, fonc_trigger_param, fonc_date_limite)
						VALUES (:fonc_nom, :fonc_perso_cod, :fonc_type, :fonc_effet, :fonc_force, :fonc_duree, :fonc_type_cible, :fonc_nombre_cible, :fonc_portee, :fonc_proba, :fonc_message, :fonc_trigger_param, {$fonc_validite_sql})";
                    $stmt      = $pdo->prepare($req);
                    $stmt      = $pdo->execute(array(
                        ":fonc_nom"              => $fonc_nom,
                        ":fonc_perso_cod"        => $mod_perso_cod,
                        ":fonc_type"             => $fonc_type,
                        ":fonc_effet"            => $fonc_effet,
                        ":fonc_force"            => $_POST['fonc_force' . $numero],
                        ":fonc_duree"            => $_POST['fonc_duree' . $numero],
                        ":fonc_type_cible"       => $_POST['fonc_cible' . $numero],
                        ":fonc_nombre_cible"     => $_POST['fonc_nombre' . $numero],
                        ":fonc_portee"           => $_POST['fonc_portee' . $numero],
                        ":fonc_proba"            => $_POST['fonc_proba' . $numero],
                        ":fonc_message"          => $_POST['fonc_message' . $numero],
                        ":fonc_trigger_param"    => json_encode($fonc_trigger_param)
                    ), $stmt);

                    $texteDeclenchement = '';
                    switch ($fonc_type)
                    {
                        case 'D':
                            $texteDeclenchement = 'le déclenchement de la DLT.';
                            break;
                        case 'T':
                            $texteDeclenchement = 'la mort de la cible du personnage.';
                            break;
                        case 'M':
                            $texteDeclenchement = 'la mort du personnage.';
                            break;
                        case 'A':
                            $texteDeclenchement = 'une attaque portée.';
                            break;
                        case 'AE':
                            $texteDeclenchement = 'une attaque esquivée.';
                            break;
                        case 'AT':
                            $texteDeclenchement = 'une attaque portée qui touche.';
                            break;
                        case 'AC':
                            $texteDeclenchement = 'une attaque subie.';
                            break;
                        case 'ACE':
                            $texteDeclenchement = 'une esquive.';
                            break;
                        case 'ACT':
                            $texteDeclenchement = 'une attaque subie qui touche.';
                            break;
                    }
                    if (!empty($message))
                        $message .= "			";
                    $message .= "Ajout d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";
                }
            }
        }

        // Modification d’effet
        foreach ($fonctions_existantes as $numero)
        {
            if (!empty($numero) || $numero === 0)
            {
                $fonc_cod = fonctions::format($_POST['fonc_id' . $numero]);
                if (!in_array($fonc_cod, $fonctions_supprimees))
                {
                    //require "blocks/_block_admin_traitement_type_monstre_edit.php";
                    $fonc_effet  = $_POST['fonc_effet' . $numero];
                    if (!empty($_POST['fonc_cumulatif' . $numero]) && ($_POST['fonc_cumulatif' . $numero] == "on")) $fonc_effet .= "+";

                    $fonc_unite_valid  = $_POST['fonc_validite_unite' . $numero];
                    $fonc_validite     = $fonc_unite_valid * $_POST['fonc_validite' . $numero];
                    $fonc_validite_sql = ($fonc_validite === 0) ? "NULL" : "now() + '$fonc_validite minutes'::interval";
                    if (!empty($_POST['fonc_cumulatif' . $numero]) && ($_POST['fonc_cumulatif' . $numero] == "on")) $fonc_effet .= "+";

                    $fonc_trigger_param = array() ;
                    foreach($_POST as $key => $val){
                        if ((substr($key, 0, 10)=="fonc_trig_") && (substr($key, -strlen("{$numero}"))==$numero)) {
                            $fonc_trigger_param[substr($key, 0, -strlen("{$numero}"))]= $val ;
                        }
                    }

                    $req       = "UPDATE fonction_specifique
						SET fonc_effet = :fonc_effet,
							fonc_force = :fonc_force,
							fonc_duree = :fonc_duree,
							fonc_type_cible = :fonc_type_cible,
							fonc_nombre_cible = :fonc_nombre_cible,
							fonc_portee =:fonc_portee,
							fonc_proba = :fonc_proba,
							fonc_message = :fonc_message,
							fonc_trigger_param = :fonc_trigger_param, 
							fonc_date_limite = {$fonc_validite_sql}
						WHERE fonc_cod = :fonc_cod
						RETURNING fonc_nom, fonc_type";
                    $stmt      = $pdo->prepare($req);
                    $stmt      = $pdo->execute(array(
                        ":fonc_cod"              => $fonc_cod,
                        ":fonc_effet"            => $fonc_effet,
                        ":fonc_force"            => $_POST['fonc_force' . $numero],
                        ":fonc_duree"            => $_POST['fonc_duree' . $numero],
                        ":fonc_type_cible"       => $_POST['fonc_cible' . $numero],
                        ":fonc_nombre_cible"     => $_POST['fonc_nombre' . $numero],
                        ":fonc_portee"           => $_POST['fonc_portee' . $numero],
                        ":fonc_proba"            => $_POST['fonc_proba' . $numero],
                        ":fonc_message"          => $_POST['fonc_message' . $numero],
                        ":fonc_trigger_param"    => json_encode($fonc_trigger_param)
                    ), $stmt);
                    $result    = $stmt->fetch();
                    $fonc_nom  = $result['fonc_nom'];
                    $fonc_type = $result['fonc_type'];

                    $texteDeclenchement = '';
                    switch ($fonc_type)
                    {
                        case 'D':
                            $texteDeclenchement = 'le déclenchement de la DLT.';
                            break;
                        case 'T':
                            $texteDeclenchement = 'la mort de la cible du personnage.';
                            break;
                        case 'M':
                            $texteDeclenchement = 'la mort du personnage.';
                            break;
                        case 'A':
                            $texteDeclenchement = 'une attaque portée.';
                            break;
                        case 'AE':
                            $texteDeclenchement = 'une attaque esquivée.';
                            break;
                        case 'AT':
                            $texteDeclenchement = 'une attaque portée qui touche.';
                            break;
                        case 'AC':
                            $texteDeclenchement = 'une attaque subie.';
                            break;
                        case 'ACE':
                            $texteDeclenchement = 'une esquive.';
                            break;
                        case 'ACT':
                            $texteDeclenchement = 'une attaque subie qui touche.';
                            break;
                    }
                    if (!empty($message))
                        $message .= "			";
                    $message .= "Modification d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";
                }
            }
        }

        // Suppression d’effet
        foreach ($fonctions_supprimees as $id)
        {
            if (!empty($id) || $id === 0)
            {
                $req          = "SELECT fonc_nom, fonc_type FROM fonction_specifique WHERE fonc_cod = $id";
                $stmt_add_fun = $pdo->query($req);
                if ($result_add_fun = $stmt_add_fun->fetch())
                {
                    $fonc_type    = $result_add_fun['fonc_type'];
                    $fonc_nom     = $result_add_fun['fonc_nom'];
                    $req          = "DELETE FROM fonction_specifique WHERE fonc_cod = $id";
                    $stmt_add_fun = $pdo->query($req);
                }

                $texteDeclenchement = '';
                switch ($fonc_type)
                {
                    case 'D':
                        $texteDeclenchement = 'le déclenchement de la DLT.';
                        break;
                    case 'T':
                        $texteDeclenchement = 'la mort de la cible du personnage.';
                        break;
                    case 'M':
                        $texteDeclenchement = 'la mort du personnage.';
                        break;
                    case 'A':
                        $texteDeclenchement = 'une attaque portée.';
                        break;
                    case 'AE':
                        $texteDeclenchement = 'une attaque esquivée.';
                        break;
                    case 'AT':
                        $texteDeclenchement = 'une attaque portée qui touche.';
                        break;
                    case 'AC':
                        $texteDeclenchement = 'une attaque subie.';
                        break;
                    case 'ACE':
                        $texteDeclenchement = 'une esquive.';
                        break;
                    case 'ACT':
                        $texteDeclenchement = 'une attaque subie qui touche.';
                        break;
                }
                if (!empty($message))
                    $message .= "			";
                $message .= "Suppression d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";
            }
        }

        writelog($log . $message);
        echo "<div class='bordiv'><pre>" . nl2br($message) . "</pre></div>";
        break;
}