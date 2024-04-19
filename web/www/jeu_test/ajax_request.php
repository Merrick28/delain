<?php
/*
 * Créé le 10/4/2018 par Marlyza
 *
 * Ces requêtes ajax servent à faire des échanges entre le FRONT (browser) et le BACK(database)
 * Il ne peut-y avoir d'interaction avec l'utilisateur ici (ni saisie de formulaire, ni affichage)
 */
include_once G_CHE . '/includes/classes.php';

//---------------------------------------------------------------------------------------------------
// S'il n'ya pas de demande, c'est pas la peine d'aller plus loin
if (!isset($_REQUEST["request"])) die('{"resultat":-1, "message":"pas de demande!"}');

//---------------------------------------------------------------------------------------------------
//Vérification d'usage sur l'authentification
session_start();

header("Content-type: application/json; charset=utf-8");

$myAuth = new myauth;
$myAuth->start();
if (!$myAuth->verif_auth) die('{"resultat":-1, "message":"identification requise"}');

//---------------------------------------------------------------------------------------------------
// Ok, on est authentifié, récupération des infos du compte.
$compt_cod = $myAuth->id;
$compte = new compte;
if (!$compte->charge($compt_cod)) die('{"resultat":-1, "message":"erreur chargement du compte"}');

//---------------------------------------------------------------------------------------------------
// récupération des infos du perso.
$perso  = new perso;
if (!$perso->getByComptDerPerso($compte->compt_cod))  die('{"resultat":-1, "message":"erreur chargement du perso"}');

$perso_cod = $perso->perso_cod;

//---------------------------------------------------------------------------------------------------
// Tout est conforme on peut traiter les requests
//---------------------------------------------------------------------------------------------------
$resultat = array();
$pdo    = new bddpdo;

switch($_REQUEST["request"])
{
    //==============================================================================================
    case "save-qa-notes":
    //==============================================================================================

        $notes =  $_REQUEST["notes"] ; //  $notes = substr($_REQUEST["notes"], 0, 4096);

        $pnotes = new aquete_perso_notes();
        $isNew =  $pnotes->charge_par_perso($perso_cod) ? false : true  ;

        $pnotes->aqperson_perso_cod = $perso_cod ;
        $pnotes->aqperson_notes = $notes ;
        $pnotes->aqperson_date = date("Y-m-d H:i:s") ;
        $pnotes->stocke($isNew);

        //if ($notes != $_REQUEST["notes"]) die('{"resultat":-1, "message":"Vos notes ont été tronquées (à 4096 caractères)"}');

        break;

    //==============================================================================================
    case "add-qa-notes":
    //==============================================================================================

        $pnotes = new aquete_perso_notes();
        $isNew =  $pnotes->charge_par_perso($perso_cod) ? false : true  ;

        $notes =  $_REQUEST["notes"] ; // $notes = substr($_REQUEST["notes"], 0, 4092-strlen($pnotes->aqperson_notes));
        $pnotes->aqperson_perso_cod = $perso_cod ;
        $pnotes->aqperson_notes = $pnotes->aqperson_notes."<hr>".$notes ;
        $pnotes->aqperson_date = date("Y-m-d H:i:s") ;
        $pnotes->stocke($isNew);

        //if ($notes != $_REQUEST["notes"]) die('{"resultat":-1, "message":"Vos notes ont été tronquées (à 4096 caractères)"}');

        break;
    //==============================================================================================
    case "add_favoris":
    //==============================================================================================
        $type = $_REQUEST["type"];
        $misc_cod = 1*(int)$_REQUEST["misc_cod"];
        // on épure le nom des caractèred interdits (sinon c'est interprété par le template twig)
        $nom = str_replace('{', '',str_replace('}', '',$_REQUEST["nom"]));

        // Cas particulier des objets magiques (il faut qu'on recupère le n° du sort lancé)
        if ($type=="sort5")
        {
            $req  = "select objsort_sort_cod from objets_sorts join perso_objets on perobj_obj_cod=objsort_obj_cod where perobj_perso_cod=:perso_cod and objsort_cod=:objsort_cod;";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso_cod" => $perso_cod, ":objsort_cod" => $misc_cod), $stmt);
            if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur la recherche de l\objet magique"}');
            $sort_cod = (int)$result["objsort_sort_cod"];
        }
        else if ($type=="sort6")
        {
            $req  = "select objsortbm_tbonus_cod from objets_sorts_bm join perso_objets on perobj_obj_cod=objsortbm_obj_cod where perobj_perso_cod=:perso_cod and objsortbm_cod=:objsortbm_cod;";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso_cod" => $perso_cod, ":objsortbm_cod" => $misc_cod), $stmt);
            if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur la recherche de l\objet magique"}');
            $sort_cod = (int)$result["objsortbm_tbonus_cod"];
        }

        $list_function_cout_pa = array(
            "sort1" =>   "cout_pa_magie($perso_cod,$misc_cod,1)",
            "sort3" =>   "cout_pa_magie($perso_cod,$misc_cod,3)",
            "sort5" =>   "cout_pa_objet_sort($perso_cod,$misc_cod)",
            "sort6" =>   "cout_pa_objet_sort_bm($perso_cod,$misc_cod)",
            "sort7" =>   "cout_pa_combo($perso_cod,$misc_cod)"
        );
        $list_link = array(
            "sort1" =>     "javascript:post('/jeu_test/choix_sort.php',['sort','type_lance'],[$misc_cod,1])",
            "sort3" =>     "javascript:post('/jeu_test/choix_sort.php',['sort','type_lance'],[$misc_cod,3])",
            "sort5" =>     "javascript:post('/jeu_test/choix_sort.php',['sort','type_lance','objsort_cod'],[$sort_cod,5,$misc_cod])",
            "sort6" =>     "javascript:post('/jeu_test/choix_sort.php',['sort','type_lance','objsort_cod'],[$sort_cod,6,$misc_cod])",
            "sort7" =>     "javascript:post('/jeu_test/choix_sort.php',['sort','type_lance'],[$misc_cod,7])"
        );

        if ($nom=="") die('{"resultat":1, "message":"Impossible d\'ajouter un favoris sans nom."}');

        $req  = "SELECT count(*) count from perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod AND pfav_type=:pfav_type AND pfav_misc_cod=:pfav_misc_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pfav_perso_cod" => $perso_cod, ":pfav_type" => $type, "pfav_misc_cod" =>$misc_cod), $stmt);
        if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur le comptage des favoris"}');
        if ($result["count"]*1>=1) die('{"resultat":1, "message":"Ce sort est déjà dans vos favoris."}');


        $req  = "SELECT count(*) count from perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pfav_perso_cod" => $perso_cod), $stmt);
        if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur le comptage des favoris"}');
        if ($result["count"]*1>=10) die('{"resultat":1, "message":"Le nombre maximum de favoris est déjà atteint."}');

        if ($type=="sort7")
        {
            $req  = "SELECT $list_function_cout_pa[$type] as cout_pa  ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(), $stmt);
            if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur le nom du favoris"}');
            $cout_pa = $result["cout_pa"] ;
        }
        else if ($type=="sort6")
        {
            $req  = "SELECT tonbus_libelle nom,  $list_function_cout_pa[$type] as cout_pa FROM bonus_type WHERE tbonus_cod=:sort_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":sort_cod" => $sort_cod), $stmt);
            if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur le nom du favoris"}');
            //$nom = $result["nom"] ;   # vrai nom du sort versus le nom du favoris
            $cout_pa = $result["cout_pa"] ;
        }
        else if ($type=="sort5")
        {
            $req  = "SELECT sort_nom nom,  $list_function_cout_pa[$type] as cout_pa FROM sorts WHERE sort_cod=:sort_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":sort_cod" => $sort_cod), $stmt);
            if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur le nom du favoris"}');
            //$nom = $result["nom"] ;   # vrai nom du sort versus le nom du favoris
            $cout_pa = $result["cout_pa"] ;
        }
        else
        {
            $req  = "SELECT sort_nom nom,  $list_function_cout_pa[$type] as cout_pa FROM sorts WHERE sort_cod=:sort_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":sort_cod" => $misc_cod), $stmt);
            if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur le nom du favoris"}');
            //$nom = $result["nom"] ;   # vrai nom du sort versus le nom du favoris
            $cout_pa = $result["cout_pa"] ;
        }

        $req   = "INSERT INTO public.perso_favoris(pfav_perso_cod, pfav_type, pfav_misc_cod, pfav_nom, pfav_function_cout_pa, pfav_link) 
                    VALUES(:pfav_perso_cod, :pfav_type, :pfav_misc_cod, :pfav_nom, :pfav_function_cout_pa, :pfav_link) RETURNING pfav_cod";
        $stmt  = $pdo->prepare($req);
        $stmt  = $pdo->execute(array(
                        ":pfav_perso_cod" => $perso_cod,
                        ":pfav_type" => $type,
                        ":pfav_misc_cod" => $misc_cod,
                        ":pfav_nom" => $nom,
                        ":pfav_function_cout_pa" => $list_function_cout_pa[$type],
                        ":pfav_link" => $list_link[$type]
                    ), $stmt);
        if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur l\'ajout du favoris"}');

        $resultat["pfav_cod"] =  $result["pfav_cod"] ;
        $resultat["nom"] =  htmlspecialchars($nom). " (". $cout_pa . " PA)";
        $resultat["link"] =  $list_link[$type] ;
        $resultat["message"] = "Le sort \"$nom\" a bien été ajouté à vos favoris." ;
        break;

    //==============================================================================================
    case "del_favoris":
    //==============================================================================================
        $type = $_REQUEST["type"];
        $misc_cod = 1*$_REQUEST["misc_cod"];

        $req  = "SELECT pfav_cod,pfav_nom as nom from perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod AND pfav_type=:pfav_type AND pfav_misc_cod=:pfav_misc_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pfav_perso_cod" => $perso_cod, ":pfav_type" => $type, "pfav_misc_cod" =>$misc_cod), $stmt);
        if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur le comptage des favoris"}');
        $pfav_cod = $result["pfav_cod"] ;
        $nom = $result["nom"] ;

        $req  = "DELETE from perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod AND pfav_type=:pfav_type AND pfav_misc_cod=:pfav_misc_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pfav_perso_cod" => $perso_cod, ":pfav_type" => $type, "pfav_misc_cod" =>$misc_cod), $stmt);

        $resultat["pfav_cod"] = $pfav_cod ;
        $resultat["message"] = "Le sort \"$nom\" a bien été supprimé de vos favoris." ;
        break;

    //==============================================================================================
    case "admin_info_style_fonds":
    //==============================================================================================
        verif_admin($pdo, $compt_cod, "dcompt_modif_carte");      // Droit modification etage requis ! !! droit ne doit pas provenir de l'extérieur

        $style = $_REQUEST["style"];
        $fond_id = 1*$_REQUEST["fond_id"];

        $req = "select count(*) from positions inner join etage on etage_numero = pos_etage where pos_type_aff=? and etage_affichage=? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($fond_id,$style), $stmt);
        $row = $stmt->fetch();
        $usage = $row['count'];
        if ($usage>0)
            $resultat["message"] = "<font color='#006400'>Le fond $fond_id pour le style $style est pas utilisé <strong>$usage</strong> fois</font>" ;
        else
            $resultat["message"] = "<font color='#191970'>Le fond $fond_id pour le style $style, n'est pas utilisé</font>" ;
        break;

    //==============================================================================================
    case "admin_info_style_murs":
    //==============================================================================================
        verif_admin($pdo, $compt_cod, "dcompt_modif_carte");      // Droit modification etage requis ! !! droit ne doit pas provenir de l'extérieur

        $style = $_REQUEST["style"];
        $mur_id = 1*$_REQUEST["mur_id"];

        $req = "select count(*) from murs inner join positions on pos_cod = mur_pos_cod inner join etage on etage_numero = pos_etage where mur_type=? and etage_affichage=? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($mur_id,$style), $stmt);
        $row = $stmt->fetch();
        $usage = $row['count'];
        if ($usage>0)
            $resultat["message"] = "<font color='#006400'>Le mur $mur_id pour le style $style est pas utilisé <strong>$usage</strong> fois</font>" ;
        else
            $resultat["message"] = "<font color='#191970'>Le mur $mur_id pour le style $style, n'est pas utilisé</font>" ;
        break;

    //==============================================================================================
    case "admin_info_decors":
    //==============================================================================================
        verif_admin($pdo, $compt_cod, "dcompt_modif_carte");      // Droit modification etage requis ! !! droit ne doit pas provenir de l'extérieur

        $decor_id = 1*$_REQUEST["decor_id"];

        $req = "select count(*) from positions where pos_decor=? or pos_decor_dessus=? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($decor_id,$decor_id), $stmt);
        $row = $stmt->fetch();
        $usage = $row['count'];
        if ($usage>0)
            $resultat["message"] = "<font color='#006400'>Le décor $decor_id est utilisé <strong>$usage</strong> fois</font>" ;
        else
            $resultat["message"] = "<font color='#191970'>Le décor $decor_id n'est pas utilisé</font>" ;
        break;

    //==============================================================================
    case "get_table_cod":
    //==============================================================================================
        verif_admin($pdo, $compt_cod);      // Droit admin/monstre requis !

        $recherche = $_REQUEST["recherche"];
        $table = $_REQUEST["table"];
        $params = $_REQUEST["params"];
        $limit = isset($_REQUEST["limit"]) ? (int)$_REQUEST["limit"] : 10 ;

        switch ($table) {
        case 'perso':
            $words = explode(" ", $recherche);
            $search_string = array();

            $filter = "";
            foreach ($words as $k => $w)
            {
                $filter.= "AND (perso_nom ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }
            if ($params["perso_perso"]=="false")
            {      // limitation aux monstres
                $filter .= "and perso_type_perso=2 ";
            }
            else
            {
                if ($params["perso_monstre"]!="true") $filter .= "and perso_type_perso<>2 ";
                if ($params["perso_fam"]!="true") $filter .= "and perso_type_perso<>3 ";
            }

            // requete de comptage
            $req = "select count(*) from perso where perso_actif='O' {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select perso_cod cod, perso_nom nom from perso where perso_actif='O' {$filter} ORDER BY perso_nom LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'lieu':
            $words = explode(" ", $recherche);
            $search_string = array();

            $filter = "";
            foreach ($words as $k => $w)
            {
                if ($k>0)  $filter.= "AND ";
                $filter.= "(lieu_nom||' '||pos_x::text||' '||pos_y::text||' '||etage_libelle ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }

            // requete de comptage
            $req = "select count(*) 
                    from lieu 
                    inner join lieu_position on lpos_lieu_cod=lieu_cod 
                    inner join positions on pos_cod=lpos_pos_cod
                    inner join etage on etage_numero=pos_etage
                    where {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select lieu_cod cod, lieu_nom||' ('||pos_x::text||','||pos_y::text||' - '||etage_libelle||')' as nom from lieu 
                    inner join lieu_position on lpos_lieu_cod=lieu_cod 
                    inner join positions on pos_cod=lpos_pos_cod
                    inner join etage on etage_numero=pos_etage
                    where {$filter}
                    ORDER BY lieu_nom, etage_libelle LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'position':
            $words = explode(" ", $recherche);
            $search_string = array();

            $filter = "";
            foreach ($words as $k => $w)
            {
                if ($k>0)  $filter.= "AND ";
                $filter.= "(pos_cod::text||' '||etage_reference::text||': X='||pos_x::text||',Y='||pos_y::text||' - '||etage_libelle::text ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }
            if ($params["position_etage"]!="") { $filter .= "and etage_reference = :etage_reference ";  $search_string[":etage_reference"] = 1*$params["position_etage"]; }
            if ($params["position_x"]!="") { $filter .= "and pos_x = :pos_x ";  $search_string[":pos_x"] = 1*$params["position_x"]; }
            if ($params["position_y"]!="") { $filter .= "and pos_y = :pos_y ";  $search_string[":pos_y"] = 1*$params["position_y"]; }
            if (($params["position_lieu"]=="true")||(($params["position_etage"]=="")&&($params["position_x"]=="")&&($params["position_x"]==""))) $filter .= "and lieu_cod is not null ";

            // requete de comptage
            $req = "select count(*) 
                    from positions 
                    inner join etage on etage_numero=pos_etage
                    left join lieu_position on lpos_pos_cod=pos_cod 
                    left join lieu on lpos_lieu_cod=lieu_cod 
                    where {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select pos_cod cod, 'Etage:'||etage_reference::text||': X='||pos_x::text||',Y='||pos_y::text||' - '||etage_libelle::text||COALESCE(' ('||lieu_nom||')','') as nom  
                    from positions 
                    inner join etage on etage_numero=pos_etage
                    left join lieu_position on lpos_pos_cod=pos_cod 
                    left join lieu on lpos_lieu_cod=lieu_cod 
                    where {$filter}
                    ORDER BY lieu_nom, etage_libelle LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

            case 'monstre_generique':
                $words = explode(" ", $recherche);
                $search_string = array();

                $filter = "";
                foreach ($words as $k => $w)
                {
                    if ($k>0)  $filter.= "AND ";
                    $filter.= "(gmon_nom ilike :search$k) ";
                    $search_string[":search$k"] = "%{$w}%" ;
                }

                // requete de comptage
                $req = "select count(*) from monstre_generique where {$filter} ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute($search_string, $stmt);
                $row = $stmt->fetch();
                $count = $row['count'];

                // requete de recherche
                $req = "select gmon_cod cod, gmon_nom nom from monstre_generique where {$filter} ORDER BY gmon_nom LIMIT {$limit}";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute($search_string, $stmt);
                break;

        case 'objet_generique':
            $words = explode(" ", $recherche);
            $search_string = array();

            $filter = "";
            foreach ($words as $k => $w)
            {
                if ($k>0)  $filter.= "AND ";
                $filter.= "(tobj_libelle || ' ' || gobj_nom ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }
            if ($params["objet_generique_sort"]=="true")
            {  // limitation aux objet avec des sorts rattachés
                $filter .= ($filter!="" ? "AND " : "")."exists(select 1 from objets_sorts where objsort_gobj_cod=gobj_cod) ";
            }
            if ($params["objet_generique_bm"]=="true")
            {  // limitation aux objet avec des bonus/malus de rattachés
                $filter .= ($filter!="" ? "AND " : "")."exists(select 1 from objets_bm where objbm_gobj_cod=gobj_cod) ";
            }
            if ($params["objet_generique_sort_bm"]=="true")
            {  // limitation aux objet avec des bonus/malus de rattachés
                $filter .= ($filter!="" ? "AND " : "")."exists(select 1 from objets_sorts_bm where objsortbm_gobj_cod=gobj_cod) ";
            }
            if ($params["objet_generique_equipe"]=="true")
            {  // limitation aux objet avec des bonus/malus de rattachés
                $filter .= ($filter!="" ? "AND " : "")."exists(select 1 from objet_element where objelem_gobj_cod=gobj_cod and objelem_param_id=1 and objelem_type='perso_condition') ";
            }

            // requete de comptage
            $req = "select count(*) from objet_generique join type_objet on tobj_cod=gobj_tobj_cod where {$filter} ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select gobj_cod cod, gobj_nom || ' (' || tobj_libelle || ')' nom from objet_generique join type_objet on tobj_cod=gobj_tobj_cod where {$filter} ORDER BY gobj_nom LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'type_objet':
            $words = explode(" ", $recherche);
            $search_string = array();

            $filter = "";
            foreach ($words as $k => $w)
            {
                if ($k>0)  $filter.= "AND ";
                $filter.= "(tobj_libelle ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }

            // requete de comptage
            $req = "select count(*) from type_objet where {$filter} ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select tobj_cod cod, tobj_libelle nom from type_objet where {$filter} ORDER BY tobj_libelle LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'race':
            $words = explode(" ", $recherche);
            $search_string = array();

            $filter = "";
            foreach ($words as $k => $w)
            {
                if ($k>0)  $filter.= "AND ";
                $filter.= "(race_nom ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }

            // requete de comptage
            $req = "select count(*) from race where {$filter} ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select race_cod cod, race_nom nom from race where {$filter} ORDER BY race_nom LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'competence':
            $words = explode(" ", $recherche);
            $search_string = array();

            $filter = "";
            foreach ($words as $k => $w)
            {
                if ($k>0)  $filter.= "AND ";
                $filter.= "(comp_libelle||' ('||typc_libelle||')' ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }

            // requete de comptage
            $req = "select count(*) from competences join type_competences on comp_typc_cod=typc_cod where {$filter} ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select comp_cod cod, comp_libelle||' ('||typc_libelle||')' nom from competences join type_competences on comp_typc_cod=typc_cod where {$filter} ORDER BY comp_libelle LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'lieu_type':
            $filter = "";

            // requete de comptage
            $req = "select count(*) from lieu_type where tlieu_libelle ilike ? {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select tlieu_cod cod, tlieu_libelle nom from lieu_type where tlieu_libelle ilike ?  {$filter} ORDER BY tlieu_libelle LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            break;

        case 'bonus_type':
            $filter = "";

            // requete de comptage
            $req = "select count(*) from bonus_type where tonbus_libelle ilike ? {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select tbonus_cod cod, tonbus_libelle nom from bonus_type where tonbus_libelle ilike ?  {$filter} ORDER BY tonbus_libelle LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            break;

        case 'meca':
            $filter = "";

            if (1*$params["etage_cod"]>0) $filter .= "and meca_pos_etage = ".(1*$params["etage_cod"]);

            // requete de comptage
            $req = "select count(*) from meca join etage on etage_cod= meca_pos_etage where (etage_libelle || ' / ' || meca_nom) ilike ? {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select meca_cod cod, (etage_libelle || ' / ' || meca_nom) nom from meca join etage on etage_cod= meca_pos_etage where (etage_libelle || ' / ' || meca_nom) ilike ?  {$filter} ORDER BY (etage_libelle || ' / ' || meca_nom)  LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            break;

        case 'sort':
            $words = explode(" ", $recherche);
            $search_string = array();

            $filter = "";
            foreach ($words as $k => $w)
            {
                if ($k>0)  $filter.= "AND ";
                $filter.= "(sort_cod::text||' '||sort_nom||' ('||sort_cout::text||'PA)' ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }

            // requete de comptage
            $req = "select count(*) from sorts where {$filter} ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select sort_cod cod, sort_nom||' ('||sort_cout::text||'PA)' nom from sorts where {$filter} ORDER BY sort_nom LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'etape':
            $filter = "";
            $words = explode(" ", $recherche);
            $search_string = array();
            foreach ($words as $k => $w)
            {
                if ($k>0)  $filter.= "AND ";
                $filter.= "(aqetape_cod::text||' '||aqetape_nom||' ['||aquete_nom||']' ilike :search$k) ";
                $search_string[":search$k"] = "%{$w}%" ;
            }
            if (1*$params["aquete_cod"]>0) $filter .= "and aqetape_aquete_cod = ".(1*$params["aquete_cod"])." ";
            if (1*$params["aqetape_cod"]>0) $filter .= "and aqetape_cod <> ".(1*$params["aqetape_cod"])." ";

            // requete de comptage
            $req = "select count(*) from quetes.aquete_etape join quetes.aquete on aquete_cod=aqetape_aquete_cod where {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select aqetape_cod cod, aqetape_nom||' ['||aquete_nom||']' nom from quetes.aquete_etape join quetes.aquete on aquete_cod=aqetape_aquete_cod where {$filter} ORDER BY aqetape_nom LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'quete':
            $filter = "";

            // requete de comptage
            $req = "select count(*) from quetes.aquete where aquete_nom_alias ilike ? {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select aquete_cod cod, aquete_nom_alias nom from quetes.aquete where aquete_nom_alias ilike ? {$filter} ORDER BY aquete_nom_alias LIMIT {$limit}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            break;

        case 'element':
            // On va utiliser ce paramètre sans le passer par le pdo, il faut s'assurer qu'il fait bien partie des valeurs attendue)
            // Il y aurait un risque d'injection de coe sql si on ne le faisait pas.;
            $aquete_cod = 1*$params["aquete_cod"] ;
            $aqetape_cod = 1*$params["aqetape_cod"] ;
            $aqelem_type = $params["aqelem_type"] ;
            if ($aqelem_type!="" and !in_array($aqelem_type, array("perso", "lieu", "type_lieu", "objet_generique", "monstre_generique", "position")))  die('{"resultat":-1, "message":"aqelem_type type non supporté dans get_table_cod"}');

            if ($aqelem_type=="perso")  // cas particulier, le monstre generique est aussi un perso!
                $filter_type =  "aqelem_type in ('perso','monstre_generique')";
            else
                $filter_type =  "(aqelem_type='{$aqelem_type}' OR '{$aqelem_type}'='')" ;

            // requete de comptage
            $req = "SELECT count(*) FROM (
                        select aqelem_aqetape_cod cod , aqelem_param_id num1, aqetape_nom || ' paramètre #' || aqelem_param_id::text as nom, aqelem_type as info
                        from quetes.aquete_element 
                        join quetes.aquete_etape on aqetape_cod = aqelem_aqetape_cod
                        where aqelem_aquete_cod={$aquete_cod} and aqelem_aqperso_cod is null and {$filter_type} and aqelem_aqetape_cod<>{$aqetape_cod}
                        group by aqelem_aqetape_cod, aqelem_param_id, aqetape_nom, aqelem_type
                    ) as filter WHERE  nom ilike ?
            ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "SELECT * FROM (
                        select aqelem_aqetape_cod cod , aqelem_param_id num1, aqetape_nom || ' / paramètre #' || aqelem_param_id::text as nom, aqelem_type as info 
                        from quetes.aquete_element 
                        join quetes.aquete_etape on aqetape_cod = aqelem_aqetape_cod
                        where aqelem_aquete_cod={$aquete_cod} and aqelem_aqperso_cod is null and {$filter_type} and aqelem_aqetape_cod<>{$aqetape_cod}
                        group by aqelem_aqetape_cod, aqelem_param_id, aqetape_nom, aqelem_type
                    ) as filter WHERE  nom ilike ?
            ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            break;

         default:
             die('{"resultat":-1, "message":"table inconne dans get_table_cod"}');
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetchall
        $resultat = array("count" => $count, "table" => $table, "data" => $result, "filter" =>$params);
        break;

    //==============================================================================
    case "get_table_nom":
    //==============================================================================================
        verif_admin($pdo, $compt_cod);      // Droit admin/monstre requis !
        $cod = $_REQUEST["cod"];
        $table = $_REQUEST["table"];

        switch ($table) {
            case 'perso':
                $req = "select perso_nom nom from perso where perso_cod = ? ";
                break;
            case 'lieu':
                $req = "select lieu_nom nom from lieu where lieu_cod = ? ";
                break;
            case 'etape':
                if ($cod==0)
                    $req = "select 'Etape suivante' nom where ?=0 ";
                else if ($cod==-1)
                    $req = "select 'Quitter/Abandonner' nom where ?=-1 ";
                else if ($cod==-2)
                    $req = "select 'Terminer avec succès' nom where ?=-2 ";
                else if ($cod==-3)
                    $req = "select 'Echec de la quête' nom where ?=-3 ";
                else
                    $req = "select aqetape_nom nom from quetes.aquete_etape where aqetape_cod = ? ";
                break;
            case 'lieu_type':
                $req = "select tlieu_libelle nom from lieu_type where tlieu_cod = ? ";
                break;
            case 'objet_generique':
                $req = "select gobj_nom nom from objet_generique where gobj_cod = ? ";
                break;
            case 'type_objet':
                $req = "select tobj_libelle nom from type_objet where tobj_cod = ? ";
                break;
            case 'sort':
                $req = "select sort_nom nom from sorts where sort_cod = ? ";
                break;
            case 'race':
                $req = "select race_nom nom from race where race_cod = ? ";
                break;
            case 'monstre_generique':
                $req = "select gmon_nom nom from monstre_generique where gmon_cod = ? ";
                break;
            case 'element':
                $req = "select aqetape_nom || ' / paramètre #' || aqelem_param_id::text as nom from quetes.aquete_element join quetes.aquete_etape on aqetape_cod = aqelem_aqetape_cod where aqelem_cod = ? ";
                break;
            case 'bonus_type':
                $req = "select tbonus_libc  || ' (' || tonbus_libelle || ')' as nom from bonus_type where tbonus_cod = ? ";
                break;
            case 'bonus_type2':
                $req = "select tbonus_cod as cod,   tbonus_libc || ' (' || tonbus_libelle || ')' as nom from bonus_type where tbonus_libc = ? ";
                break;
            default:
                die('{"resultat":-1, "message":"table inconne dans get_table_cod"}');
        }

        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($cod), $stmt);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        break;

    //==============================================================================
    case "get_table_info":
    //==============================================================================================
        verif_admin($pdo, $compt_cod);      // Droit admin/monstre requis !
        $info = $_REQUEST["info"];

        switch ($info) {
            case 'pos_cod':     // pos_cod à partir x,y,etage
                //$req = "select pos_cod from positions left outer join murs on mur_pos_cod = pos_cod where pos_x = ? and pos_y = ? and pos_etage = ? ";
                $req = "select pos_cod from positions where pos_x = ? and pos_y = ? and pos_etage = ? ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($_REQUEST["pos_x"],$_REQUEST["pos_y"],$_REQUEST["pos_etage"]), $stmt);
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

            case 'perso_titre':     // nom du perso et sa position à partir perso_cod
                $req = "select distinct perso_cod, perso_type_perso, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso 
                        join perso_position on ppos_perso_cod=perso_cod 
                        join positions on pos_cod=ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        join perso_titre on ptitre_perso_cod=perso_cod
                        where ptitre_titre ilike ? ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($_REQUEST["titre"]), $stmt);
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'perso_liste':     // nom du perso et sa position à partir perso_cod
                $filter = "" ;
                if (strpos($_REQUEST["liste"], ";")>0) $delimiter=";"; else $delimiter=",";  // rechercher le delimiteur
                $words = explode($delimiter, $_REQUEST["liste"]);
                $search_string = array();
                foreach ($words as $k => $w)
                {
                    if ($w != "")
                    {
                        if ($filter!="")  $filter.= "OR ";
                        $filter.= "(perso_nom ilike :search$k) ";
                        $search_string[":search$k"] = trim($w) ;
                    }
                }

                $req = "select distinct perso_cod, perso_type_perso, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso 
                        join perso_position on ppos_perso_cod=perso_cod 
                        join positions on pos_cod=ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where ($filter) ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute($search_string, $stmt);
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'perso_gain_px':     // nom du perso et sa position à partir perso_cod
                $req = "select distinct perso_cod, perso_type_perso, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso 
                        join perso_position on ppos_perso_cod=perso_cod 
                        join positions on pos_cod=ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        join ligne_evt on levt_cible=perso_cod
                        where  levt_tevt_cod=48 and levt_attaquant=? and perso_type_perso in (1,3) ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($_REQUEST["perso_cod"]), $stmt);
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'perso_pos':     // nom du perso et sa position à partir perso_cod
                $req = "select perso_nom, perso_type_perso, pos_x, pos_y, pos_etage,etage_libelle from perso 
                        join perso_position on ppos_perso_cod=perso_cod 
                        join positions on pos_cod=ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where perso_cod = ? ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(1*$_REQUEST["perso_cod"]), $stmt);
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

            case 'perso_compte_pos':     // nom du perso, son compte joueur et sa position à partir perso_cod
                $req = "select pcompt_compt_cod as compt_cod, perso_nom, perso_type_perso, pos_x, pos_y, pos_etage,etage_libelle from perso 
                        join perso_position on ppos_perso_cod=perso_cod 
                        join positions on pos_cod=ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        join perso_compte on pcompt_perso_cod= case when perso_type_perso=3 then (select pfam_perso_cod from perso_familier where pfam_familier_cod=71 limit 1) else perso_cod end
                        where perso_cod = ? ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(1*$_REQUEST["perso_cod"]), $stmt);
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

            case 'perso_coterie_pos':     // nom des persos de la même coterie que perso_cod avec leur position
                $req = "select perso_cod, perso_type_perso, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso
                        join groupe_perso coterie on coterie.pgroupe_perso_cod=?
                        join groupe_perso on groupe_perso.pgroupe_perso_cod=perso_cod and groupe_perso.pgroupe_groupe_cod=coterie.pgroupe_groupe_cod
                        join perso_position on ppos_perso_cod=perso_cod 
                        join positions on pos_cod=ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where perso_actif='O' order by perso_nom ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(1*$_REQUEST["perso_cod"]), $stmt);
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'perso_zone_pos':     // nom des persos sur la même position que perso_cod avec leur position
                $req = "select perso_cod, perso_type_perso, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso
                        join perso_position zone on zone.ppos_perso_cod = ?
                        join perso_position on perso_position.ppos_pos_cod = zone.ppos_pos_cod and perso_position.ppos_perso_cod=perso_cod
                        join positions on pos_cod=perso_position.ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where perso_actif='O' order by perso_nom ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(1*$_REQUEST["perso_cod"]), $stmt);
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'perso_compte_pos':     // nom des persos controllé par le même joueur que perso_cod avec leur position
                $req = "select perso_cod, perso_type_perso, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso
                        join perso_compte controleur on controleur.pcompt_perso_cod = ?
                        join perso_compte on perso_compte.pcompt_compt_cod = controleur.pcompt_compt_cod and perso_compte.pcompt_perso_cod=perso_cod
                        join perso_position on ppos_perso_cod=perso_cod
                        join positions on pos_cod=perso_position.ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where perso_actif='O' order by perso_nom ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(1*$_REQUEST["perso_cod"]), $stmt);
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'perso_etage_pos':     // nom des persos situé sur l'étage
                $req = "select perso_cod, perso_type_perso, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso
                        join perso_position on ppos_perso_cod=perso_cod
                        join positions on pos_cod=perso_position.ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where perso_actif='O' and perso_type_perso=? and etage_numero=? order by perso_nom ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array((int)$_REQUEST["type_perso"], (int)$_REQUEST["etage_numero"]), $stmt);
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'position_description':     // une description de la position passée par pos_x, pos_y, et pos_etage
                //$req = "select pos_cod, COALESCE(CASE WHEN mur_pos_cod IS NOT NULL THEN 'Un mur' ELSE NULL end, lieu_nom || COALESCE('(' || lieu_description || ')', '') ) as position_desc
                $req = "select pos_cod, COALESCE(CASE WHEN mur_pos_cod IS NOT NULL THEN 'Un mur' ELSE NULL end, lieu_nom  ) as position_desc 
                        from positions 
                        left join  murs on mur_pos_cod=pos_cod
                        left join  lieu_position on lpos_pos_cod=pos_cod
                        left join  lieu on lieu_cod=lpos_lieu_cod
                        where pos_x = ? and pos_y = ? and pos_etage = ? ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($_REQUEST["pos_x"],$_REQUEST["pos_y"],$_REQUEST["pos_etage"]), $stmt);
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

            case 'objets_sorts':     // sort sur objet
                $req = "select * from objets_sorts where objsort_cod = ?  ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($_REQUEST["objsort_cod"]), $stmt);
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

            case 'objets_sorts_bm':     // sort sur objet
                $req = "select * from objets_sorts_bm where objsortbm_cod = ?  ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($_REQUEST["objsortbm_cod"]), $stmt);
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

            case 'objets_bm':     // sort sur objet
                $req = "select * from objets_bm where objbm_cod = ?  ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array($_REQUEST["objbm_cod"]), $stmt);
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

            default:
                die('{"resultat":-1, "message":"table inconne dans get_table_info"}');
        }

        break;

    //==============================================================================================
    default:
    //==============================================================================================
    die('{"resultat":-1, "message":"demande inconne"}');
}

//==============================================================================================
// Output si réussi!
die('{"resultat":0, "data":'.json_encode($resultat ).'}');

function verif_admin($pdo, $compt_cod, $droit="")
{
    // Attention la variable $droit peut-être injectée dans le code sql sans risque SI elle est toujours fourni en interne
    // NE JAMAIS PRENDRE UNE VALEUR FOURNIE PAR $_GET, $_POST ou $_REQUEST
    // Si droit n'est pas fourni, on verifi un droit admin ou monstre
    if ($droit != "")
    {
        $req = "select count(*) count from compt_droit where dcompt_compt_cod =? and $droit = 'O'";
    }
    else
    {
        $req = "select count(*) count from compte where compt_cod =? and (compt_admin='O' OR compt_monstre='O') ";
    }
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array( $compt_cod ), $stmt);
    $row = $stmt->fetch();
    $count = 1*$row['count'];

    if ( $count<=0 ) die('{"resultat":-1, "message":"Vous devez disposer de droit admin pour ça!"}');
}
