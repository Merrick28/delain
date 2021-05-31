<?php
include "blocks/_header_page_jeu.php";

ob_start();

//
//log_debug('Debut de page inventaire persos');
//

$pdo   = new bddpdo;

// Récupérer la liste de perso (et bzf au passage) ============================================================
$req   = "SELECT perso_cod, perso_type_perso, perso_pnj, ordre, perso_nom, perso_po, pbank_or FROM (

                    select perso_cod, perso_type_perso, perso_cod as ordre, perso_nom, perso_pnj, perso_po
                    from compte  
                    join perso_compte on compt_cod=:compt_cod and pcompt_compt_cod=compt_cod 
                    join perso on perso_cod=pcompt_perso_cod
                    where perso_actif='O' and perso_type_perso = 1
                    
                    union
                    
                    select pf.perso_cod, pf.perso_type_perso, pfam_perso_cod as ordre, pf.perso_nom,  pp.perso_pnj, pf.perso_po
                    from compte  
                    join perso_compte on compt_cod=:compt_cod and pcompt_compt_cod=compt_cod 
                    join perso_familier on pfam_perso_cod=pcompt_perso_cod 
                    join perso pf on pf.perso_cod=pfam_familier_cod 
                    join perso pp on pp.perso_cod=pfam_perso_cod 
                    where pf.perso_actif='O' and pf.perso_type_perso = 3
          
                ) as p LEFT JOIN perso_banque ON pbank_perso_cod=perso_cod ORDER BY perso_pnj, perso_type_perso, ordre ";
$stmt  = $pdo->prepare($req);
$stmt  = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$persos  = $stmt->fetchAll();

$quatrieme = false ;
$perso_cod_list = "" ;
foreach ($persos as $p){
    if ($p["perso_pnj"] == 2) $quatrieme = true ;
    $perso_cod_list.=",".$p["perso_cod"];
}
$perso_cod_list=substr($perso_cod_list, 1);

// Ajout du stockage coffre
$cc = new compte_coffre();
$cc->loadBy_ccompt_compt_cod($compt_cod);
$coffre = ($cc->ccompt_cod) ? true : false ;


#=======================================================================================================================
# RUNES
#=======================================================================================================================
$req   = "SELECT perobj_perso_cod as perso_cod, obj_famille_rune, obj_nom || ' (' || obj_famille_rune || ')' as obj_nom,sum(obj_poids) as poids,obj_frune_cod,count(*) as count
            FROM perso_objets,objets,objet_generique
            WHERE perobj_perso_cod in ($perso_cod_list)
                AND perobj_obj_cod = obj_cod
                AND obj_gobj_cod = gobj_cod
	            AND perobj_identifie = 'O'                 
                AND gobj_tobj_cod = 5
            GROUP BY obj_frune_cod,obj_famille_rune,obj_nom, perobj_perso_cod
            
          UNION ALL 
           
          SELECT 0 as perso_cod, obj_famille_rune, obj_nom || ' (' || obj_famille_rune || ')' as obj_nom,sum(obj_poids) as poids,obj_frune_cod,count(*) as count 
            FROM coffre_objets,objets,objet_generique
            WHERE coffre_compt_cod = :compt_cod
                AND coffre_obj_cod = obj_cod
                AND obj_gobj_cod = gobj_cod             
                AND gobj_tobj_cod = 5
            GROUP BY obj_frune_cod,obj_famille_rune,obj_nom
                        
            ORDER BY obj_frune_cod,obj_famille_rune,obj_nom, perso_cod            
            ";
$stmt  = $pdo->prepare($req);
$stmt  = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$result  = $stmt->fetchAll();
$runes = array() ;
$perso_runes = array() ;
$last = "" ;
foreach ($result as $r){
    if ($r["obj_nom"]!=$last) $runes[] = $r["obj_nom"] ;
    $last = $r["obj_nom"] ;
    if (! isset($perso_runes[$r["perso_cod"]])) $perso_runes[$r["perso_cod"]] = array();
    $perso_runes[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"] ;
    $perso_runes[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"] ;
}


#=======================================================================================================================
# OBJETS QUETE
#=======================================================================================================================
$req   = "SELECT perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod in (11,12) 
            GROUP BY obj_nom, perobj_perso_cod
            
          UNION ALL 
           
          SELECT 0 as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM coffre_objets,objets,objet_generique
            WHERE coffre_compt_cod = :compt_cod
                AND coffre_obj_cod = obj_cod
                AND obj_gobj_cod = gobj_cod             
                AND gobj_tobj_cod in (11,12) 
            GROUP BY obj_nom
                                                            
            ORDER BY obj_nom, perso_cod ";
$stmt  = $pdo->prepare($req);
$stmt  = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$result  = $stmt->fetchAll();
$quetes = array() ;
$perso_quetes = array() ;
$last = "" ;
foreach ($result as $r){
    if ($r["obj_nom"]!=$last) $quetes[] = $r["obj_nom"] ;
    $last = $r["obj_nom"] ;
    if (! isset($perso_quetes[$r["perso_cod"]])) $perso_quetes[$r["perso_cod"]] = array();
    $perso_quetes[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"] ;
    $perso_quetes[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"] ;
}

#=======================================================================================================================
# COMPOS
#=======================================================================================================================
$req   = "SELECT perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod in (28,30,34) 
            GROUP BY obj_nom, perobj_perso_cod
            
          UNION ALL 
           
          SELECT 0 as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM coffre_objets,objets,objet_generique
            WHERE coffre_compt_cod = :compt_cod
                AND coffre_obj_cod = obj_cod
                AND obj_gobj_cod = gobj_cod             
                AND gobj_tobj_cod in (28,30,34) 
            GROUP BY obj_nom
                        
            ORDER BY obj_nom, perso_cod ";
$stmt  = $pdo->prepare($req);
$stmt  = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$result  = $stmt->fetchAll();
$compos = array() ;
$perso_compos = array() ;
$last = "" ;
foreach ($result as $r){
    if ($r["obj_nom"]!=$last) $compos[] = $r["obj_nom"] ;
    $last = $r["obj_nom"] ;
    if (! isset($perso_compos[$r["perso_cod"]])) $perso_compos[$r["perso_cod"]] = array();
    $perso_compos[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"] ;
    $perso_compos[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"] ;
}

#=======================================================================================================================
# MONNAIES
#=======================================================================================================================
$req   = "SELECT perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod in (42) 
            GROUP BY obj_nom, perobj_perso_cod
            
          UNION ALL 
           
          SELECT 0 as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM coffre_objets,objets,objet_generique
            WHERE coffre_compt_cod = :compt_cod
                AND coffre_obj_cod = obj_cod
                AND obj_gobj_cod = gobj_cod             
                AND gobj_tobj_cod in (42) 
            GROUP BY obj_nom
                                    
            ORDER BY obj_nom, perso_cod ";
$stmt  = $pdo->prepare($req);
$stmt  = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$result  = $stmt->fetchAll();
$monnaies = array() ;
$perso_monnaies = array() ;
$last = "" ;
foreach ($result as $r){
    if ($r["obj_nom"]!=$last) $monnaies[] = $r["obj_nom"] ;
    $last = $r["obj_nom"] ;
    if (! isset($perso_monnaies[$r["perso_cod"]])) $perso_monnaies[$r["perso_cod"]] = array();
    $perso_monnaies[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"] ;
    $perso_monnaies[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"] ;
}

#=======================================================================================================================
# DIVERS
#=======================================================================================================================
$req   = "SELECT tobj_libelle, perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod in (17, 18, 19, 20, 21, 22, 24, 39) 
            GROUP BY tobj_libelle, obj_nom, perobj_perso_cod
            
          UNION ALL 
           
          SELECT tobj_libelle, 0 as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM coffre_objets,objets,objet_generique,type_objet 
            WHERE coffre_compt_cod = :compt_cod
                AND coffre_obj_cod = obj_cod
                AND obj_gobj_cod = gobj_cod    
                AND gobj_tobj_cod = tobj_cod          
                AND gobj_tobj_cod in (17, 18, 19, 20, 21, 22, 24, 39) 
            GROUP BY tobj_libelle, obj_nom
                          
            ORDER BY tobj_libelle, obj_nom, perso_cod ";
$stmt  = $pdo->prepare($req);
$stmt  = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$result  = $stmt->fetchAll();
$divers = array() ;
$divers_type = array() ;
$perso_divers = array() ;
$last = "" ;
foreach ($result as $r){
    if ($r["obj_nom"]!=$last) {
        $divers[] = $r["obj_nom"] ;
        $divers_type[$r["obj_nom"]] = $r["tobj_libelle"] ;
    }
    $last = $r["obj_nom"] ;
    if (! isset($perso_divers[$r["perso_cod"]])) $perso_divers[$r["perso_cod"]] = array();
    $perso_divers[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"] ;
    $perso_divers[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"] ;
}


#=======================================================================================================================
# MATOS
#=======================================================================================================================
$req   = "SELECT tobj_libelle, perobj_perso_cod as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM perso_objets,objets,objet_generique,type_objet 
            WHERE  perobj_perso_cod in ($perso_cod_list)
                AND perobj_identifie = 'O' 
                AND perobj_obj_cod = obj_cod 
                AND obj_gobj_cod = gobj_cod 
                AND gobj_tobj_cod = tobj_cod 
                AND gobj_tobj_cod not in (5, 11, 12, 17, 18, 19, 20, 21, 22, 24, 39, 42, 28, 30, 34) 
            GROUP BY tobj_libelle, obj_nom, perobj_perso_cod
            
          UNION ALL 
           
          SELECT tobj_libelle, 0 as perso_cod, obj_nom, sum(obj_poids) as poids, count(*) as count 
            FROM coffre_objets,objets,objet_generique,type_objet 
            WHERE coffre_compt_cod = :compt_cod
                AND coffre_obj_cod = obj_cod
                AND obj_gobj_cod = gobj_cod    
                AND gobj_tobj_cod = tobj_cod          
                AND gobj_tobj_cod not in (5, 11, 12, 17, 18, 19, 20, 21, 22, 24, 39, 42, 28, 30, 34) 
            GROUP BY tobj_libelle, obj_nom
                          
            ORDER BY tobj_libelle, obj_nom, perso_cod ";
$stmt  = $pdo->prepare($req);
$stmt  = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$result  = $stmt->fetchAll();
$matos = array() ;
$matos_type = array() ;
$perso_matos = array() ;
$last = "" ;
foreach ($result as $r){
    if ($r["obj_nom"]!=$last) {
        $matos[] = $r["obj_nom"] ;
        $matos_type[$r["obj_nom"]] = $r["tobj_libelle"] ;
    }
    $last = $r["obj_nom"] ;
    if (! isset($perso_matos[$r["perso_cod"]])) $perso_matos[$r["perso_cod"]] = array();
    $perso_matos[$r["perso_cod"]][$r["obj_nom"]]["count"] = $r["count"] ;
    $perso_matos[$r["perso_cod"]][$r["obj_nom"]]["poids"] = $r["poids"] ;
}

$template     = $twig->load('inventaire_persos.twig');
$options_twig = array(

    'PERSO'          => $perso,
    'PERSOS'         => $persos,
    'COFFRE'         => $coffre,
    'QUATRIEME'      => $quatrieme,
    'RUNES'          => $runes,
    'PERSO_RUNES'    => $perso_runes,
    'QUETES'         => $quetes,
    'PERSO_QUETES'   => $perso_quetes,
    'COMPOS'         => $compos,
    'PERSO_COMPOS'   => $perso_compos,
    'DIVERS'         => $divers,
    'DIVERS_TYPE'    => $divers_type,
    'PERSO_DIVERS'   => $perso_divers,
    'MATOS'         => $matos,
    'MATOS_TYPE'    => $matos_type,
    'PERSO_MATOS'   => $perso_matos,
    'MONNAIES'       => $monnaies,
    'PERSO_MONNAIES' => $perso_monnaies,
    'NBPERSO'        => count($persos),
    'PHP_SELF'       => $PHP_SELF,
    'CONTENU_PAGE'   => $contenu_page

);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));


