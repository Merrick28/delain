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
    default:
    //==============================================================================================
    die('{"resultat":-1, "message":"demande inconne"}');
}

//==============================================================================================
// Output si réussi!
die('{"resultat":0, "data":'.json_encode($resultat ).'}');
?>