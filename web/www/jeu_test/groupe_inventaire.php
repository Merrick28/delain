<?php
include "blocks/_header_page_jeu.php";

ob_start();

$pdo = new bddpdo;

$req = 'select pgroupe_groupe_cod, is_visible_groupe(pgroupe_perso_cod,:perso_cod) as is_visible from groupe_perso where pgroupe_perso_cod = :perso_cod and pgroupe_statut > 0 and pgroupe_statut > 0 AND pgroupe_montre_matos = 1 ';
$stmt = $pdo->prepare($req);
$stmt = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);

if (!$result = $stmt->fetch()) {

    $contenu_page = 'Vous n’appartenez à aucune coterie ou vous ne montrez pas votre matériel.';
    $contenu_page .= '<hr /><p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
    include "blocks/_footer_page_jeu.php";

} else if ($result["is_visible"] != "1") {

    $contenu_page = 'Vous êtes trop éloigné du chef de groupe pour avoir les informations.';
    $contenu_page .= '<hr /><p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
    include "blocks/_footer_page_jeu.php";

} else {

    $num_groupe = 1 * (int)$result["pgroupe_groupe_cod"];


//log_debug('Debut de page inventaire groupe');
//

// Récupérer la liste de perso  ============================================================

    $req = 'SELECT perso_cod, perso_type_perso, perso_nom
                FROM perso,groupe_perso
                WHERE pgroupe_groupe_cod = :pgroupe_groupe_cod
                    AND pgroupe_statut = 1
                    AND pgroupe_perso_cod = perso_cod
                    AND pgroupe_perso_cod = perso_cod
                    AND pgroupe_montre_matos = 1
                    AND is_visible_groupe(pgroupe_groupe_cod,perso_cod) = 1
                ORDER BY perso_type_perso, perso_cod';

    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(":pgroupe_groupe_cod" => $num_groupe), $stmt);
    $persos = $stmt->fetchAll();

    $perso_cod_list = "";
    foreach ($persos as $p) {
        $perso_cod_list .= "," . $p["perso_cod"];
    }
    $perso_cod_list = substr($perso_cod_list, 1);


    $req = "SELECT perobj_perso_cod as perso_cod, obj_nom || ' (' || obj_famille_rune || ')' as obj_nom,sum(obj_poids) as poids,obj_frune_cod,count(*) as count 
            FROM perso_objets,objets,objet_generique
            WHERE perobj_perso_cod in ($perso_cod_list)
                AND perobj_obj_cod = obj_cod
                AND obj_gobj_cod = gobj_cod
	            AND perobj_identifie = 'O'                 
                AND gobj_tobj_cod = 5
            GROUP BY obj_frune_cod,obj_famille_rune,obj_nom, perobj_perso_cod
            ORDER BY obj_frune_cod,obj_famille_rune,obj_nom, perobj_perso_cod";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(), $stmt);
    $result = $stmt->fetchAll();
    $runes = array();
    $perso_runes = array();
    $last = "";
    foreach ($result as $r) {
        if ($r["obj_nom"] != $last) $runes[] = $r["obj_nom"];
        $last = $r["obj_nom"];
        if (!isset($perso_runes[$r["perso_cod"]])) $perso_runes[$r["perso_cod"]] = array();
        $perso_runes[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"];
        $perso_runes[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"];
    }


    $req = "SELECT perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod in (11,12) 
            GROUP BY obj_nom, perobj_perso_cod
            ORDER BY obj_nom, perobj_perso_cod ";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(), $stmt);
    $result = $stmt->fetchAll();
    $quetes = array();
    $perso_quetes = array();
    $last = "";
    foreach ($result as $r) {
        if ($r["obj_nom"] != $last) $quetes[] = $r["obj_nom"];
        $last = $r["obj_nom"];
        if (!isset($perso_quetes[$r["perso_cod"]])) $perso_quetes[$r["perso_cod"]] = array();
        $perso_quetes[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"];
        $perso_quetes[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"];
    }

    $req = "SELECT perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod in (22,28,30,34) 
            GROUP BY obj_nom, perobj_perso_cod
            ORDER BY obj_nom, perobj_perso_cod ";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(), $stmt);
    $result = $stmt->fetchAll();
    $compos = array();
    $perso_compos = array();
    $last = "";
    foreach ($result as $r) {
        if ($r["obj_nom"] != $last) $compos[] = $r["obj_nom"];
        $last = $r["obj_nom"];
        if (!isset($perso_compos[$r["perso_cod"]])) $perso_compos[$r["perso_cod"]] = array();
        $perso_compos[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"];
        $perso_compos[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"];
    }

    $req = "SELECT perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod in (42) 
            GROUP BY obj_nom, perobj_perso_cod
            ORDER BY obj_nom, perobj_perso_cod ";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(), $stmt);
    $result = $stmt->fetchAll();
    $monnaies = array();
    $perso_monnaies = array();
    $last = "";
    foreach ($result as $r) {
        if ($r["obj_nom"] != $last) $monnaies[] = $r["obj_nom"];
        $last = $r["obj_nom"];
        if (!isset($perso_monnaies[$r["perso_cod"]])) $perso_monnaies[$r["perso_cod"]] = array();
        $perso_monnaies[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"];
        $perso_monnaies[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"];
    }

    $req = "SELECT tobj_libelle, perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod in (17, 18, 19, 20, 21, 22, 24, 39) 
            GROUP BY tobj_libelle, obj_nom, perobj_perso_cod
            ORDER BY tobj_libelle, obj_nom, perobj_perso_cod ";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(), $stmt);
    $result = $stmt->fetchAll();
    $divers = array();
    $divers_type = array();
    $perso_divers = array();
    $last = "";
    foreach ($result as $r) {
        if ($r["obj_nom"] != $last) {
            $divers[] = $r["obj_nom"];
            $divers_type[$r["obj_nom"]] = $r["tobj_libelle"];
        }
        $last = $r["obj_nom"];
        if (!isset($perso_divers[$r["perso_cod"]])) $perso_divers[$r["perso_cod"]] = array();
        $perso_divers[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"];
        $perso_divers[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"];
    }

    $template = $twig->load('groupe_inventaire.twig');
    $options_twig = array(

        '__VERSION' => $__VERSION,
        'PERSO' => $perso,
        'PERSOS' => $persos,
        'RUNES' => $runes,
        'PERSO_RUNES' => $perso_runes,
        'QUETES' => $quetes,
        'PERSO_QUETES' => $perso_quetes,
        'COMPOS' => $compos,
        'PERSO_COMPOS' => $perso_compos,
        'DIVERS' => $divers,
        'DIVERS_TYPE' => $divers_type,
        'PERSO_DIVERS' => $perso_divers,
        'MONNAIES' => $monnaies,
        'PERSO_MONNAIES' => $perso_monnaies,
        'NBPERSO' => count($persos),
        'PHP_SELF' => $PHP_SELF,
        'CONTENU_PAGE' => $contenu_page

    );
    echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));


}