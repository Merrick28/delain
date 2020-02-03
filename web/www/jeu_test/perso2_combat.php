<?php
$param = new parametres();
$pdo   = new bddpdo();
$perso = new perso;
if (!$perso->charge($perso_cod))
{
    die('Erreur sur chargement perso');
}

$req             = "select obj_nom,perobj_dfin as dfin
	from objets,perso_objets
	where perobj_perso_cod = :perso
	and perobj_obj_cod = obj_cod
	and perobj_equipe = 'O' 
	and perobj_dfin is not null";
$stmt            = $pdo->prepare($req);
$stmt            = $pdo->execute(array(":perso" => $perso->perso_cod), $stmt);
$objets_possedes = $stmt->fetchAll();


include('mode_combat.php');


/*
 LEGITIMES DEFENSES 
 */

$cout_des       = $param->getparm(60);
$lock           = new lock_combat();
$lock_cible     = $lock->getByCible($perso->perso_cod);
$lock_attaquant = $lock->getByAttaquant($perso->perso_cod);



/*
LEGITIMES DEFENSES 
*/

$riposte           = new riposte;
$riposte_cible     = $riposte->getByCible($perso->perso_cod);
$riposte_attaquant = $riposte->getByAttaquant($perso->perso_cod);

$template     = $twig->load('_perso2_combat.twig');
$options_twig = array(

    'PERSO'             => $perso,
    'PHP_SELF'          => $PHP_SELF,
    'OBJETS_POSSEDES'   => $objets_possedes,
    'CONTENU_INCLUDE'   => $contenu_include,
    'COUT_DES'          => $param->getparm(60),
    'LOCK_CIBLE'        => $lock_cible,
    'LOCK_ATTAQUANT'    => $lock_attaquant,
    'RIPOSTE_ATTAQUANT' => $riposte_attaquant,
    'RIPOSTE_CIBLE'     => $riposte_cible

);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));