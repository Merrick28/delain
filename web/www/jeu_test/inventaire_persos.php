<?php
include "blocks/_header_page_jeu.php";

include_once "verif_connexion.php";
ob_start();
include "../includes/fonctions.php";
$parm = new parametres();
//
//log_debug('Debut de page inventaire');
//

$pdo   = new bddpdo;

// Récupérer la liste de perso ============================================================
$req   = "SELECT perso_cod, perso_type_perso, ordre, perso_nom FROM (

                    select perso_cod, perso_type_perso, perso_cod as ordre, perso_nom
                    from compte  
                    join perso_compte on compt_cod=:compt_cod and pcompt_compt_cod=compt_cod 
                    join perso on perso_cod=pcompt_perso_cod
                    where perso_actif='O'
                    
                    union
                    
                    select perso_cod, perso_type_perso, pfam_perso_cod as ordre, perso_nom
                    from compte  
                    join perso_compte on compt_cod=:compt_cod and pcompt_compt_cod=compt_cod 
                    join perso_familier on pfam_perso_cod=pcompt_perso_cod 
                    join perso on perso_cod=pfam_familier_cod where perso_actif='O' 
          
                ) as p ORDER BY perso_type_perso, ordre, perso_type_perso ";
$stmt  = $pdo->prepare($req);
$stmt  = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$persos  = $stmt->fetchAll();

$perso_cod_list = "" ;
foreach ($persos as $p){
    $perso_cod_list.=",".$p["perso_cod"];
}
$perso_cod_list=substr($perso_cod_list, 1);


$template     = $twig->load('inventaire_persos.twig');
$options_twig = array(

    'PERSO'        => $perso,
    'PERSOS'       => $persos,
    'NBCOL'       => count($persos),
    'PHP_SELF'     => $PHP_SELF,
    'CONTENU_PAGE' => $contenu_page

);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));


