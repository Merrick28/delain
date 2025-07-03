<?php
/* #LAG - +++ 2018-01-25 +++ - Création, modification des lieux */

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include_once '../includes/tools.php';

include('variables_menu.php');


//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur  = 0;
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";

//=======================================================================================
echo '<link href="../css/select2.min.css?v'.$__VERSION.'" rel="stylesheet">';
echo '<script language="javascript" src="../js/select2.min.js?v'.$__VERSION.'"></script>';
echo '<script src="../scripts/admin_etage_modif3.js'.$__VERSION.'"></script>';     // Scripts des traitements des clics dans la map
echo '<script type="text/javascript">//# sourceURL=modif_etage3quiquies.js
        $(document).ready(function() {                
			$("#idcleantype").select2({});
			$("#idcleangeneric").select2({});
	    });
        
        function setCleanAreaZone() {
            radiobtn = document.getElementById("id_clean_area");
            radiobtn.checked = true;
        }
        </script>';



$pdo = new bddpdo;

$log      = '';
$resultat = '';

//---------------------------------------------------------------------------------------------------------------------------
// Objectif:
//		1- Saisie des éléments à nettoyer
//		2- Saisie de la zone à nettoyer
//---------------------------------------------------------------------------------------------------------------------------

if ($erreur == 0)
{
    $admin_etage = get_request_var('admin_etage', 0);

    //echo "<pre>"; print_r($_POST); echo "</pre>";

    // Affichage des options de nettoyage
    if (!isset($_POST["nettoyer"]))
    {
        $phpself = $_SERVER['PHP_SELF'];

        $req_tobj = "select tobj_cod, tobj_libelle from type_objet order by tobj_libelle ";
        $req_gobj = "select gobj_cod, gobj_nom from objet_generique order by gobj_nom ";


        echo "<table width='100%' class='bordiv'><tr><td><p><strong>NETTOYAGE DES OBJETS ET BROUZOUFS D’ETAGE :</strong></p>
        <form method='post' action='$phpself'>
        <input type='hidden' value='nettoyer' name='nettoyer' />
        {$admin_etage} Choisir l'étage à nettoyer : <select name='admin_etage'>" . $html->etage_select($admin_etage) . "</select>";
        echo "<br><br><u><strong>Elements à supprimer:</strong></u><br>
            Brouzoufs : <input type='checkbox' name='clean_bzf' ><br>
            Tous les objets : <input type='checkbox' name='clean_obj' ><br>
            Type d'objet : <select id='idcleantype'  name='cleantype[]' multiple='multiple' style='width: 400px'>". $html->select_from_query($req_tobj, 'tobj_cod', 'tobj_libelle') . "</select><br><br>
            Objet générique : <select id='idcleangeneric'  name='cleangeneric[]' multiple='multiple' style='width: 400px'>". $html->select_from_query($req_gobj, 'gobj_cod', 'gobj_nom') . "</select><br><br>

            <br>
            <u><strong>Zone à nettoyer:</strong></u><br>
            Seulement dans les murs : <input type='checkbox' name='clean_wall' > <br>
            <input checked type='radio' name='clean_area' value='floor'> Sur tout l'étage <br>
             <input type='radio' id='id_clean_area' name='clean_area' value='zone'> Seulement dans cette zone :
                De X=<input onchange='setCleanAreaZone();' type='text' name='area1_x' size='2'> Y=<input onchange='setCleanAreaZone();' type='text' name='area1_y' size='2'> 
                à X=<input onchange='setCleanAreaZone();' type='text' name='area2_x' size='2'> Y=<input onchange='setCleanAreaZone();' type='text' name='area2_y' size='2'><br>
            <br><input type='submit' value=\"Nettoyer l'étage\" class='test'/>
            </form></td><td></table>";


    }

    if (isset($_POST["nettoyer"]) || isset($_POST["do_nettoyer"]))
    {
        $erreur_message = "";
        $message_details = "";
        // Vérifier que l'étage est bien sélectionné
        if (!isset($_POST["admin_etage"]) || $_POST["admin_etage"] == "")
        {
            $erreur_message .= "Erreur sur la sélection de l'étage à nettoyer!!<br>";
        }

        // Charger l'étage à nettoyer
        $etage = new etage();
        if (!$etage->getByNumero($_POST["admin_etage"]))
        {
            $erreur_message .= "Impossible de charger l'étage à nettoyer!!<br>";
        }
        $message_details.= "Nettoyage de l'étage <b>{$etage->etage_libelle}</b> (numéro {$etage->etage_numero})<br>";

        // preparer les parametre de recheche
        $arr_param = [];
        $where_pos_cod = "where pos_etage = :pos_etage ";
        $arr_param[":pos_etage"] = $etage->etage_numero;


        if (isset($_POST["clean_wall"]) && $_POST["clean_wall"] == "on")
        {
            // on ne nettoie que les objets seulement dans les murs
            $where_pos_cod .= " and mur_pos_cod is not null ";
            $message_details.= "Nettoyage limités au items coincés dans les murs<br>";
        }

        if (isset($_POST["clean_area"]) && $_POST["clean_area"] == "zone")
        {
            // on ne nettoie que les objets dans la zone
            if (isset($_POST["area1_x"]) && isset($_POST["area1_y"]) && isset($_POST["area2_x"]) && isset($_POST["area2_y"]))
            {
                $x1 = intval($_POST["area1_x"]);
                $y1 = intval($_POST["area1_y"]);
                $x2 = intval($_POST["area2_x"]);
                $y2 = intval($_POST["area2_y"]);

                if ($x1 > $x2) {
                    $x_swap = $x2; $x2 = $x1; $x1 = $x_swap;;
                }
                if ($y1 > $y2) {
                    $y_swap = $y2; $y2 = $y1; $y1 = $y_swap;
                }

                $where_pos_cod .= " and pos_x >= :pos_x1 and pos_x <= :pos_x2 and pos_y >= :pos_y1 and pos_y <= :pos_y2 ";
                $arr_param[":pos_x1"] = $x1;
                $arr_param[":pos_x2"] = $x2;
                $arr_param[":pos_y1"] = $y1;
                $arr_param[":pos_y2"] = $y2;

                $message_details.= "Nettoyage de la zone allant de X={$x1} Y={$y1} à X={$x2} Y={$y2}<br>";
            } else {
                $erreur_message .= "Erreur sur la zone à nettoyer!!<br>";
            }
        }

        if (isset($_POST['clean_bzf'])) {
            $message_details.= "Nettoyage des brouzoufs<br>";
        }

        $arr_param_obj = [] ;
        $where_obj = "(false)";
        if (isset($_POST["clean_obj"]) && $_POST["clean_obj"] == "on") {
            // on nettoie tous les objets
            $where_obj = "(true)";
            $message_details.= "Nettoyage de TOUS les objets<br>";
        }

        if (($where_obj != "(true)") && isset($_POST["cleantype"]) && is_array($_POST["cleantype"]) ) {
            $message_details.= "Nettoyage des objets du type: ";
            $where_obj .= " or gobj_tobj_cod in (";
            foreach ( $_POST["cleantype"] as $k => $v) {
                $where_obj .= ":tobj_cod_{$k},";
                $arr_param_obj[":tobj_cod_{$k}"] = intval($v);
                $tobjet = new type_objet();
                $tobjet->charge($v);
                $message_details.= "<strong>{$tobjet->tobj_libelle}</strong>, ";

            }
            $where_obj = substr($where_obj, 0, -1) . ")";
            $message_details.= "<br>";
        }

        if (($where_obj != "(true)") && isset($_POST["cleangeneric"]) && is_array($_POST["cleangeneric"] )) {
            $message_details.= "Nettoyage des objets génériques: ";
            $where_obj .= " or obj_gobj_cod in (";
            foreach ( $_POST["cleangeneric"] as $k => $v) {
                $where_obj .= ":gobj_cod_{$k},";
                $arr_param_obj[":gobj_cod_{$k}"] = intval($v);
                $objgenerique = new objet_generique();
                $objgenerique->charge($v);
                $message_details.= "<strong>{$objgenerique->gobj_nom}</strong>, ";
            }
            $where_obj = substr($where_obj, 0, -1) . ")";
            $message_details.= "<br>";
        }

    }

    // Demander la confirmation de la suppression des objets d'étage
    if (isset($_POST["nettoyer"]))
    {

        if ($erreur_message != "")
        {
            echo "<br><strong>Erreur lors du nettoyage d'étage:</strong><br><br>$erreur_message ";
        } else {
            // on s'assure que l'on peut supprimer et l'on demande confirmation
            echo "<table width='100%' class='bordiv'><tr><td><p><strong>CONFIRMATION NETTOYAGE D’ETAGE :</strong></p><tr><td>";
            echo "<br>{$message_details}<br>";
            echo "Vous allez supprimer sur <b>{$etage->etage_libelle}</b>:<br>";
            // Compter bzf
            if (isset($_POST['clean_bzf'])) {

                $req = "select coalesce(sum(por_qte), 0) bzf from or_position join positions on pos_cod=por_pos_cod left join murs on mur_pos_cod=pos_cod {$where_pos_cod};";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute($arr_param, $stmt);
                if ($result = $stmt->fetch()) {
                    echo "Brouzouf: <strong>{$result['bzf']}</strong> Bzf<br>";
                }
            }


            $req = "select count(obj_cod) obj from objets join objet_generique on gobj_cod=obj_gobj_cod join objet_position on pobj_obj_cod=obj_cod join positions on pos_cod=pobj_pos_cod left join murs on mur_pos_cod=pos_cod {$where_pos_cod}  and ({$where_obj});";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array_merge($arr_param, $arr_param_obj), $stmt);
            if ($result = $stmt->fetch()) {
                echo "Objets: <strong>{$result['obj']}</strong> Objets(s)<br>";
            }

            echo "<br><strong>Voulez-vous vraiment nettoyer cet étage?</strong><br><br>
                <form method='post' action='{$_SERVER['PHP_SELF']}'>
                <input type='hidden' value='{$admin_etage}' name='admin_etage' />";
            // inject les paramètres de nettoyage
            if (isset($_POST["clean_wall"]) && $_POST["clean_wall"] == "on"){
                echo "<input type='hidden' value='on' name='clean_wall' />";
            }

            if (isset($_POST["clean_bzf"]) && $_POST["clean_bzf"] == "on"){
                echo "<input type='hidden' value='on' name='clean_bzf' />";
            }
            
            if (isset($_POST["clean_obj"]) && $_POST["clean_obj"] == "on"){
                echo "<input type='hidden' value='on' name='clean_obj' />";
            }

            if (isset($_POST["clean_area"]) && $_POST["clean_area"] == "zone"){
                echo "<input type='hidden' value='zone' name='clean_area' />";
                echo "<input type='hidden' value='{$_POST["area1_x"]}' name='area1_x' />";
                echo "<input type='hidden' value='{$_POST["area1_y"]}' name='area1_y' />";
                echo "<input type='hidden' value='{$_POST["area2_x"]}' name='area2_x' />";
                echo "<input type='hidden' value='{$_POST["area2_y"]}' name='area2_y' />";
            } else {
                echo "<input type='hidden' value='floor' name='clean_area' />";
            }

            if (isset($_POST["cleantype"]) && is_array($_POST["cleantype"] )){
                foreach ($_POST["cleantype"] as $k => $v) {
                    echo "<input type='hidden' value='{$v}' name='cleantype[]' />";
                }
            }

            if (isset($_POST["cleangeneric"]) && is_array($_POST["cleangeneric"] )){
                foreach ($_POST["cleangeneric"] as $k => $v) {
                    echo "<input type='hidden' value='{$v}' name='cleangeneric[]' />";
                }
            }

            echo "<input type='hidden' value='do_nettoyer' name='do_nettoyer' />
                <input type='submit' value=\"Nettoyer l'étage\" class='test'/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"{$_SERVER['PHP_SELF']}?admin_etage={$admin_etage}\"><input type='button' value=\"NON!!!\" class='test'/></a></form>";

            echo "</td><td></table>";
        }

        echo '<br><div style="text-align:center"><a href="modif_etage3quinquies.php?admin_etage='.$admin_etage.'">Retour à la page de nettoyage d\'étage</a></div>';
    }

    // Menu Suppression des objets d'étage----------------------------------------------------------------
    if (isset($_POST["do_nettoyer"]))
    {
        echo "<table width='100%' class='bordiv'><tr><td><p><strong>NETTOYAGE D’ETAGE :</strong></p><tr><td>";

        if ($erreur_message != "")
        {
            echo "<br><strong>Erreur lors du nettoyage d'étage:</strong><br><br>$erreur_message ";
        } else
        {
            if (isset($_POST['clean_bzf'])) {

                $req = "delete from or_position where por_cod in (select por_cod from or_position join positions on pos_cod=por_pos_cod left join murs on mur_pos_cod=pos_cod {$where_pos_cod} ) ;";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute($arr_param, $stmt);
            }


            $req = "select f_del_objet(f.obj_cod) from (select obj_cod from objets join objet_generique on gobj_cod=obj_gobj_cod join objet_position on pobj_obj_cod=obj_cod join positions on pos_cod=pobj_pos_cod left join murs on mur_pos_cod=pos_cod {$where_pos_cod}  and ({$where_obj}) ) f;";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array_merge($arr_param, $arr_param_obj), $stmt);

            // logger les resultat dans lieu_etages:
            $fonctions = new fonctions();
            $fonctions->ecrireResultatEtLoguerLoguer($message_details, true);

            echo "L'étage a été nettoyé.<br>";
        }
        echo "</td><td></table>";

        echo '<br><div style="text-align:center"><a href="modif_etage3quinquies.php?admin_etage='.$admin_etage.'">Retour à la page de nettoyage d\'étage</a></div>';
    }

    //---------------------------------------------------------------------------------------------------------------------------
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";


