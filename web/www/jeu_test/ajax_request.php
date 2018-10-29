<?php
/*
 * Créé le 10/4/2018 par Marlyza
 *
 * Ces requêtes ajax servent à faire des échanges entre le FRONT (browser) et le BACK(database)
 * Il ne peut-y avoir d'interaction avec l'utilisateur ici (ni saisie de formulaire, ni affichage)
 */
include_once 'classes.php';

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
    case "add_favoris":
    //==============================================================================================
        $type = $_REQUEST["type"];
        $misc_cod = 1*$_REQUEST["misc_cod"];
        // on épure le nom des caractèred interdits (sinon c'est interprété par le template twig
        $nom = str_replace('{', '',str_replace('}', '',$_REQUEST["nom"]));
        $list_function_cout_pa = array(
            "sort1" =>   "cout_pa_magie($perso_cod,$misc_cod,1)",
            "sort3" =>   "cout_pa_magie($perso_cod,$misc_cod,3)"
        );
        $list_link = array(
            "sort1" =>     "javascript:post('/jeu_test/choix_sort.php',['sort','type_lance'],[$misc_cod,1])",
            "sort3" =>     "javascript:post('/jeu_test/choix_sort.php',['sort','type_lance'],[$misc_cod,3])"
        );

        if ($nom=="") die('{"resultat":1, "message":"Impossible d\'ajouter un favoris san nom."}');

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


        $req  = "SELECT sort_nom nom,  $list_function_cout_pa[$type] as cout_pa FROM sorts WHERE sort_cod=:sort_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":sort_cod" => $misc_cod), $stmt);
        if (!$result = $stmt->fetch()) die('{"resultat":1, "message":"Anomalie sur le nom du favoris"}');
        //$nom = $result["nom"] ;   # vrai nom du sort versus le nom du favoris
        $cout_pa = $result["cout_pa"] ;

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
            $resultat["message"] = "<font color='#006400'>Le fond $fond_id pour le style $style est pas utilisé <b>$usage</b> fois</font>" ;
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
            $resultat["message"] = "<font color='#006400'>Le mur $mur_id pour le style $style est pas utilisé <b>$usage</b> fois</font>" ;
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
            $resultat["message"] = "<font color='#006400'>Le décor $decor_id est utilisé <b>$usage</b> fois</font>" ;
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
            if ($params["perso_pnj"]=="true") $filter .= "and perso_pnj=1 ";
            if ($params["perso_monstre"]!="true") $filter .= "and perso_type_perso<>2 ";
            if ($params["perso_fam"]!="true") $filter .= "and perso_type_perso<>3 ";

            // requete de comptage
            $req = "select count(*) from perso where perso_actif='O' {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select perso_cod cod, perso_nom nom from perso where perso_actif='O' {$filter} ORDER BY perso_nom LIMIT 10";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            break;

        case 'lieu':
            $filter = "";

            // requete de comptage
            $req = "select count(*) from lieu where lieu_nom ilike ? {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select lieu_cod cod, lieu_nom nom from lieu where lieu_nom ilike ?  {$filter} ORDER BY lieu_nom LIMIT 10";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
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

            // requete de comptage
            $req = "select count(*) from objet_generique join type_objet on tobj_cod=gobj_tobj_cod where {$filter} ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($search_string, $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select gobj_cod cod, gobj_nom || ' (' || tobj_libelle || ')' nom from objet_generique join type_objet on tobj_cod=gobj_tobj_cod where {$filter} ORDER BY gobj_nom LIMIT 10";
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
            $req = "select gmon_cod cod, gmon_nom nom from monstre_generique where {$filter} ORDER BY gmon_nom LIMIT 10";
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
            $req = "select tlieu_cod cod, tlieu_libelle nom from lieu_type where tlieu_libelle ilike ?  {$filter} ORDER BY tlieu_libelle LIMIT 10";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            break;

        case 'etape':
            $filter = "";
            if (1*$params["aquete_cod"]>0) $filter .= "and aqetape_aquete_cod = ".(1*$params["aquete_cod"]);
            if (1*$params["aqetape_cod"]>0) $filter .= "and aqetape_cod <> ".(1*$params["aqetape_cod"]);

            // requete de comptage
            $req = "select count(*) from quetes.aquete_etape where aqetape_nom ilike ? {$filter}";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array("%{$recherche}%"), $stmt);
            $row = $stmt->fetch();
            $count = $row['count'];

            // requete de recherche
            $req = "select aqetape_cod cod, aqetape_nom nom from quetes.aquete_etape where aqetape_nom ilike ? {$filter} ORDER BY aqetape_nom LIMIT 10";
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
        $resultat = array("count" => $count, "data" => $result, "filter" =>$params);
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
            case 'monstre_generique':
                $req = "select gmon_nom nom from monstre_generique where gmon_cod = ? ";
                break;
            case 'element':
                $req = "select aqetape_nom || ' / paramètre #' || aqelem_param_id::text as nom from quetes.aquete_element join quetes.aquete_etape on aqetape_cod = aqelem_aqetape_cod where aqelem_cod = ? ";
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

            case 'perso_pos':     // nom du perso et sa position à partir perso_cod
                $req = "select perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso 
                        join perso_position on ppos_perso_cod=perso_cod 
                        join positions on pos_cod=ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where perso_cod = ? ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(1*$_REQUEST["perso_cod"]), $stmt);
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

            case 'perso_coterie_pos':     // nom des persos de la même coterie que perso_cod avec leur position
                $req = "select perso_cod, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso
                        join groupe_perso coterie on coterie.pgroupe_perso_cod=?
                        join groupe_perso on groupe_perso.pgroupe_perso_cod=perso_cod and groupe_perso.pgroupe_groupe_cod=coterie.pgroupe_groupe_cod
                        join perso_position on ppos_perso_cod=perso_cod 
                        join positions on pos_cod=ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where perso_type_perso=1 order by perso_nom "; // que des perso et et pas le perso lui-même
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(1*$_REQUEST["perso_cod"]), $stmt);
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'perso_zone_pos':     // nom des persos sur la même position que perso_cod avec leur position
                $req = "select perso_cod, perso_nom, pos_x, pos_y, pos_etage,etage_libelle from perso
                        join perso_position zone on zone.ppos_perso_cod = ?
                        join perso_position on perso_position.ppos_pos_cod = zone.ppos_pos_cod and perso_position.ppos_perso_cod=perso_cod
                        join positions on pos_cod=perso_position.ppos_pos_cod
                        join etage on etage_numero=pos_etage
                        where perso_type_perso=1 order by perso_nom "; // sauf les familier et perso lui-même
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(1*$_REQUEST["perso_cod"]), $stmt);
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
?>