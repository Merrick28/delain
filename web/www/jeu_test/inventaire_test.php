<?php
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="delain2.css">
<link rel="stylesheet" type="text/css" href="humanmsg.css">
<link rel="stylesheet" type="text/css" href="jquery.cluetip.css" />

<title>Les souterrains de Delain</title>
<link type="image/gif" href="images/drake_head_red.ico" rel="shortcut icon"/>

<!-- Javascript -->
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/qtip.js" type="text/javascript"></script>
<script src="js/humanmsg.js" type="text/javascript"></script>
<script src="js/bibliotheque2.js" type="text/javascript"></script>
<script src="js/sorttable.js" type="text/javascript"></script>
<script src="js/jquery.hoverIntent.js" type="text/javascript"></script>
<script src="js/jquery.bgiframe.min.js" type="text/javascript"></script>
<script src="js/jquery.cluetip.js" type="text/javascript"></script>

<script language = "javascript" >
    $(document).ready(function ()
    {
        $('#popup_runes').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: true, arrows: true, activation: 'click'});
        $('#popup_quete').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: true, arrows: true, activation: 'click'});
        $('#popup_composant').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: true, arrows: true, activation: 'click'});
        //$('#link_url').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: true, arrows: true, activation: 'click'});		
    });

</script>

<?php
/* Les includes */
//include "verif_connexion.php";
//include "../includes/fonctions.php";
//include "../includes/img_pack.php";

/**
 *
 *
 *
 *
 *
 *
 * */
function get_img_usure($etat, $path)
{
    $img_usure = "";

    if ($etat >= 90)
        $img_usure = $path . "Usure_100.png";
    if ($etat < 90)
        $img_usure = $path . "Usure_90.png";
    if ($etat < 70)
        $img_usure = $path . "Usure_70.png";
    if ($etat < 50)
        $img_usure = $path . "Usure_50.png";
    if ($etat < 35)
        $img_usure = $path . "Usure_35.png";
    if ($etat < 10)
        $img_usure = $path . "Usure_10.png";

    return $img_usure;
}

function get_class_usure($etat, $type, $equipe)
{
    $class_usure       = "";
    //if ( ($type == 1) or ($type == 2) or ($type == 4) )
    //{
    $img_objets_equipe = "";
    if ($equipe == 'O')
        $img_objets_equipe = "<img src=\"images/equiper10.png\" class=\"img_obj_equipe\" title=\"Objet Equipé\">";

    if ($etat >= 90)
        $class_usure = "<div class=\"top_usure_100\">" . $img_objets_equipe . "</div>";
    if ($etat < 90)
        $class_usure = "<div class=\"top_usure_90\">" . $img_objets_equipe . "</div>";
    if ($etat < 70)
        $class_usure = "<div class=\"top_usure_70\">" . $img_objets_equipe . "</div>";
    if ($etat < 50)
        $class_usure = "<div class=\"top_usure_50\">" . $img_objets_equipe . "</div>";
    if ($etat < 35)
        $class_usure = "<div class=\"top_usure_35\">" . $img_objets_equipe . "</div>";
    if ($etat < 10)
        $class_usure = "<div class=\"top_usure_10\">" . $img_objets_equipe . "</div>";
    //}

    return $class_usure;
}

/**
 * 
 * Fonction get_infos_arme
 *
 * Retourne le texte de l'image et les informations correspondant à l'arme
 *
 * @var $class: class de l'image
 * @var $path: repertoires des images
 * @var $img: nom du fichier image
 * @var $nom: nom de l'objet
 * @var $poids: poids
 * @var $desc: description
 * @var $etat: etat
 * @var $pa: nombre de PA pour utiliser l'objet
 * @var $distance: booléen indiquant si l'objet est "à distance" ou pas
 * @var $af: nombre de PA pour utiliser l'objet sous Attaque Foudroyante
 * @var $des: nombre de Dés
 * @var $val_des: valeur du Dé
 * @var $bonus: bonus 
 * @var $seuil_dex: Seuil de dextérité d'utilisation 
 * @var $seuil_for: Seuil de force d'utilisation 
 * @var $comp: compétence utilisé par l'objet
 * @var $bool_equipe: booléen (-1,0,1) indiquant si l'objet est équipé ou non
 * @var $bonus_vue: bonus de vue accordé par l'objet
 * @var $vampire: valeur de la caractéristique vampirisme
 * @var $aura_feu: valeur de la caractéristique aura de feu
 * @var $regen: valeur de la caractéristique régénération
 * @var $poison: valeur de la caractéristique poison
 * @var $enchantable: booléen indiquant si l'objet est enchantable ou pas
 * @var $deposable: booléen indiquant si l'objet est déposable ou pas
 * @var $perso_pa: nombre de PA du joueur
 * @var $dex: dextérite du joueur
 * @var $force: force du joueur
 *
 * @return $info:  texte de l'image et les informations correspondant à l'arme
 *
 * */
function get_infos_arme($class, $path, $img, $id = 0, $nom, $poids = 0, $desc = "", $etat = 0, $pa = 0, $distance = "", $chute = 0, $af = 0, $des = 0, $val_des = 0, $bonus = 0, $seuil_dex = 0, $seuil_for = 0, $comp = "", $bool_equipe = 0, $bonus_vue = 0, $critique = 0, $vampire = 0, $aura_feu = 0, $regen = 0, $poison = 0, $enchantable = 0, $deposable = 1, $perso_pa = "", $dex = 0, $force = 0, $url = "")
{
    $info = "";
    $info .= "<img class=\"" . $class . "\" src=\"" . $path . $img . "\" title=\"" . $nom . "\" ";
    if ($desc != "")
        $info .= "desc=\"" . $desc . "\" ";
    if ($poids != 0)
        $info .= "poids=\"" . $poids . "\" ";
    if ($id != 0)
        $info .= "num=\"" . $id . "\" ";

    $lib_etat = "";
    if ($etat != 0)
    {
        $lib_etat  = get_etat($etat);
        $img_usure = get_img_usure($etat, $path);
    }
    if ($lib_etat != "")
        $info .= "etat=\"" . $lib_etat . "\" ";
    if ($etat != 0)
        $info .= " img_etat=\"" . $img_usure . "\" ";
    if ($pa != 0)
        $info .= "pa=\"" . $pa . "\" ";
    if ($af != 0)
        $info .= "af=\"" . $af . "\" ";
    if (($distance == 'Y') && ($chute != 0))
        $info .= "chute=\"" . $chute . "\" ";
    if ($val_des != 0)
        $info .= "degats=\"" . $des . "D" . $val_des . "+" . $bonus . "\" ";

    $dex_class = "";
    if ($seuil_dex != 0)
    {
        if ($dex < ($seuil_dex - 3))
            $dex_class = " <img src ='" . $path . "rouge.gif'/>";
        else if ($dex < $seuil_dex)
            $dex_class = " <img src ='" . $path . "orange.gif'/>";
        else if ($dex > ($seuil_dex + 3))
            $dex_class = " <img src ='" . $path . "vert.gif'/>";

        $info .= "dexterite=\"" . $seuil_dex . "\" ";
        $info .= "class_dex=\"" . $dex_class . "\" ";
    }

    $for_class = "";
    if ($seuil_for != 0)
    {
        if ($force < ($seuil_for - 3))
            $for_class = " <img src ='" . $path . "rouge.gif'/>";
        else if ($force < $seuil_for)
            $for_class = " <img src ='" . $path . "orange.gif'/>";
        else if ($force > ($seuil_for + 3))
            $for_class = " <img src ='" . $path . "vert.gif'/>";

        $info .= "force=\"" . $seuil_for . "\" ";
        $info .= "class_for=\"" . $for_class . "\" ";
    }

    if ($comp != "")
        $info .= "competence=\"" . $comp . "\" ";

    if ($perso_pa >= 2)
        $info .= "equipe=\"" . $bool_equipe . "\" ";

    if (($bool_equipe != 1) && ($perso_pa >= 1))
        $info .= "abandonne=\"1\" ";

    if (($perso_pa >= 2))  // && assez de cométence pour reparer
        $info .= "repare=\"1\" ";

    /* if (($perso_pa >=2) && ($ok_equipe == 1) )
      $info .= "equipe=\"1\" "; */ // affiche désequiper
    //  Modificateur de vue :
    if ($bonus_vue != 0)
        $info .= "bonus_vue=\"" . $bonus_vue . "\" ";

    // Protection contre les critiques/spéciaux :
    if ($critique != 0)
        $info .= "critique=\"" . $critique . " %\" ";

    // Vampirisme :
    if ($vampire != 0)
        $info .= "vampirisme=\"" . $vampire . "\" ";

    // Aura de feu
    if ($aura_feu != 0)
        $info .= "aura_de_feu=\"" . $aura_feu . "\" ";

    // Bonus à la régénération
    if ($regen != 0)
        $info .= "regen=\"" . $regen . "\" ";

    // Dégâts infligés par poison :
    if ($poison != 0)
        $info .= "poison=\"" . $poison . "\" ";

    if ($enchantable == 1)
        $info .= "enchantable=\"1\" ";

    if ($deposable !== 0)
        $info .= "deposable=\"1\" ";

    if ($url != "")
        $info .= "url=\"" . $url . "\" ";
    $info .= " />";

    return $info;
}

/**
 * 
 * Fonction get_infos_armure
 *
 * Retourne le texte de l'image et les informations correspondant à l'arme
 *
 * @var $class: class de l'image
 * @var $path: repertoires des images
 * @var $img: nom du fichier image
 * @var $nom: nom de l'objet
 * @var $poids: poids
 * @var $desc: description
 * @var $etat: etat
 * @var $pa: nombre de PA pour utiliser l'objet
 * @var $distance: booléen indiquant si l'objet est "à distance" ou pas
 * @var $af: nombre de PA pour utiliser l'objet sous Attaque Foudroyante
 * @var $des: nombre de Dés
 * @var $val_des: valeur du Dé
 * @var $bonus: bonus 
 * @var $seuil_dex: Seuil de dextérité d'utilisation 
 * @var $seuil_for: Seuil de force d'utilisation 
 * @var $comp: compétence utilisé par l'objet
 * @var $bool_equipe: booléen (-1,0,1) indiquant si l'objet est équipé ou non
 * @var $bonus_vue: bonus de vue accordé par l'objet
 * @var $vampire: valeur de la caractéristique vampirisme
 * @var $aura_feu: valeur de la caractéristique aura de feu
 * @var $regen: valeur de la caractéristique régénération
 * @var $poison: valeur de la caractéristique poison
 * @var $enchantable: booléen indiquant si l'objet est enchantable ou pas
 * @var $deposable: booléen indiquant si l'objet est déposable ou pas
 * @var $perso_pa: nombre de PA du joueur
 * @var $dex: dextérite du joueur
 * @var $force: force du joueur
 *
 * @return $info:  texte de l'image et les informations correspondant à l'arme
 *
 * */
function get_infos_armure($class, $path, $img, $id = 0, $nom, $poids = 0, $desc = "", $etat = 0, $armure = 0, $bool_equipe = 0, $bonus_vue = 0, $critique = 0, $vampire = 0, $aura_feu = 0, $regen = 0, $poison = 0, $enchantable = 0, $deposable = 1, $perso_pa = "", $dex = 0, $force = 0, $url = "")
{
    $info = "";
    $info .= "<img class=\"" . $class . "\" src=\"" . $path . $img . "\" title=\"" . $nom . "\" ";
    if ($desc != "")
        $info .= "desc=\"" . $desc . "\" ";
    if ($poids != 0)
        $info .= "poids=\"" . $poids . "\" ";
    if ($id != 0)
        $info .= "num=\"" . $id . "\" ";

    $lib_etat = "";
    if ($etat != 0)
    {
        $lib_etat  = get_etat($etat);
        $img_usure = get_img_usure($etat, $path);
    }
    if ($lib_etat != "")
        $info .= "etat=\"" . $lib_etat . "\" ";
    if ($etat != 0)
        $info .= " img_etat=\"" . $img_usure . "\" ";
    if ($armure != 0)
        $info .= "armure=\"" . $armure . "\" ";

    /*
      if($comp != "")
      $info .= "competence=\"".$comp."\" ";
     */

    if ($perso_pa >= 2)
        $info .= "equipe=\"" . $bool_equipe . "\" ";
    //echo  "equipe=\"".$perso_pa."\" ";
    //else $info .= "equipe=\"0\" ";

    if (($bool_equipe != 1) && ($perso_pa >= 1))
        $info .= "abandonne=\"1\" ";

    if (($bool_equipe != 1) && ($perso_pa >= 2)) // && assez de cométence pour reparer
        $info .= "repare=\"1\" ";

    /* if (($perso_pa >=2) && ($ok_equipe == 1) )
      $info .= "equipe=\"1\" "; */ // affiche désequiper
    //  Modificateur de vue :
    if ($bonus_vue != 0)
        $info .= "bonus_vue=\"" . $bonus_vue . "\" ";

    // Protection contre les critiques/spéciaux :
    if ($critique != 0)
        $info .= "critique=\"" . $critique . " %\" ";

    // Vampirisme :
    if ($vampire != 0)
        $info .= "vampirisme=\"" . $vampire . "\" ";

    // Aura de feu
    if ($aura_feu != 0)
        $info .= "aura_de_feu=\"" . $aura_feu . "\" ";

    // Bonus à la régénération
    if ($regen != 0)
        $info .= "regen=\"" . $regen . "\" ";

    // Dégâts infligés par poison :
    if ($poison != 0)
        $info .= "poison=\"" . $poison . "\" ";

    if ($enchantable == 1)
        $info .= "enchantable=\"1\" ";

    if ($deposable !== 0)
        $info .= "deposable=\"1\" ";

    if ($url != "")
        $info .= "url=\"" . $url . "\" ";

    $info .= " />";

    return $info;
}

/**
 * 
 * Fonction get_infos_casque
 *
 * Retourne le texte de l'image et les informations correspondant à l'arme
 *
 * @var $class: class de l'image
 * @var $path: repertoires des images
 * @var $img: nom du fichier image
 * @var $nom: nom de l'objet
 * @var $poids: poids
 * @var $desc: description
 * @var $etat: etat
 * @var $pa: nombre de PA pour utiliser l'objet
 * @var $distance: booléen indiquant si l'objet est "à distance" ou pas
 * @var $af: nombre de PA pour utiliser l'objet sous Attaque Foudroyante
 * @var $des: nombre de Dés
 * @var $val_des: valeur du Dé
 * @var $bonus: bonus 
 * @var $seuil_dex: Seuil de dextérité d'utilisation 
 * @var $seuil_for: Seuil de force d'utilisation 
 * @var $comp: compétence utilisé par l'objet
 * @var $bool_equipe: booléen (-1,0,1) indiquant si l'objet est équipé ou non
 * @var $bonus_vue: bonus de vue accordé par l'objet
 * @var $vampire: valeur de la caractéristique vampirisme
 * @var $aura_feu: valeur de la caractéristique aura de feu
 * @var $regen: valeur de la caractéristique régénération
 * @var $poison: valeur de la caractéristique poison
 * @var $enchantable: booléen indiquant si l'objet est enchantable ou pas
 * @var $deposable: booléen indiquant si l'objet est déposable ou pas
 * @var $perso_pa: nombre de PA du joueur
 * @var $dex: dextérite du joueur
 * @var $force: force du joueur
 *
 * @return $info:  texte de l'image et les informations correspondant à l'arme
 *
 * */
function get_infos_casque($class, $path, $img, $id = 0, $nom, $poids = 0, $desc = "", $etat = 0, $bool_equipe = 0, $bonus_vue = 0, $critique = 0, $vampire = 0, $aura_feu = 0, $regen = 0, $poison = 0, $enchantable = 0, $deposable = 1, $perso_pa = "", $dex = 0, $force = 0, $url = "")
{
    $info = "";
    $info .= "<img class=\"" . $class . "\" src=\"" . $path . $img . "\" title=\"" . $nom . "\" ";
    if ($desc != "")
        $info .= "desc=\"" . $desc . "\" ";
    if ($poids != 0)
        $info .= "poids=\"" . $poids . "\" ";
    if ($id != 0)
        $info .= "num=\"" . $id . "\" ";

    $lib_etat = "";
    if ($etat != 0)
    {
        $lib_etat  = get_etat($etat);
        $img_usure = get_img_usure($etat, $path);
    }
    if ($lib_etat != "")
        $info .= "etat=\"" . $lib_etat . "\" ";
    if ($etat != 0)
        $info .= " img_etat=\"" . $img_usure . "\" ";

    /*
      if ($comp != "")
      $info .= "competence=\"".$comp."\" ";
     */
    //echo "bool_equipe: ".$bool_equipe."<br/>";

    if ($perso_pa >= 2)
        $info .= "equipe=\"" . $bool_equipe . "\" ";
    //else $info .= "equipe=\"0\" ";

    if (($bool_equipe != 1) && ($perso_pa >= 1))
        $info .= "abandonne=\"1\" ";

    if (($bool_equipe != 1) && ($perso_pa >= 2)) // && assez de cométence pour reparer
        $info .= "repare=\"1\" ";

    /* if (($perso_pa >=2) && ($ok_equipe == 1) )
      $info .= "equipe=\"1\" "; */ // affiche désequiper
    //  Modificateur de vue :
    if ($bonus_vue != 0)
        $info .= "bonus_vue=\"" . $bonus_vue . "\" ";

    // Protection contre les critiques/spéciaux :
    if ($critique != 0)
        $info .= "critique=\"" . $critique . " %\" ";

    // Vampirisme :
    if ($vampire != 0)
        $info .= "vampirisme=\"" . $vampire . "\" ";

    // Aura de feu
    if ($aura_feu != 0)
        $info .= "aura_de_feu=\"" . $aura_feu . "\" ";

    // Bonus à la régénération
    if ($regen != 0)
        $info .= "regen=\"" . $regen . "\" ";

    // Dégâts infligés par poison :
    if ($poison != 0)
        $info .= "poison=\"" . $poison . "\" ";

    if ($enchantable == 1)
        $info .= "enchantable=\"1\" ";

    if ($deposable != 0)
        $info .= "deposable=\"" . $deposable . "\" ";

    if ($url != "")
        $info .= "url=\"" . $url . "\" ";

    $info .= " />";

    return $info;
}

function get_infos_equipement($type, $class, $path, $img, $id = 0, $nom, $poids = 0, $desc = "", $etat = 0, $pa = 0, $armure = 0, $distance = "", $chute = 0, $af = 0, $des = 0, $val_des = 0, $bonus = 0, $seuil_dex = 0, $seuil_for = 0, $comp = "", $bool_equipe = 0, $bonus_vue = 0, $critique = 0, $vampire = 0, $aura_feu = 0, $regen = 0, $poison = 0, $enchantable = 0, $deposable = 1, $perso_pa = "", $dex = 0, $force = 0, $url = "")
{
    $info = "";
    $info .= "<img class=\"" . $class . "\" src=\"" . $path . $img . "\" title=\"" . $nom . "\" ";
    if ($desc != "")
        $info .= "desc=\"" . $desc . "\" ";
    if ($poids != 0)
        $info .= "poids=\"" . $poids . "\" ";
    if ($id != 0)
        $info .= "num=\"" . $id . "\" ";


    $lib_etat = "";
    if ($etat != 0)
    {
        $lib_etat  = get_etat($etat);
        $img_usure = get_img_usure($etat, $path);
    }
    if ($lib_etat != "")
        $info .= "etat=\"" . $lib_etat . "\" ";
    if ($etat != 0)
        $info .= " img_etat=\"" . $img_usure . "\" ";
    if ($armure != 0)
        $info .= "armure=\"" . $armure . "\" ";

    if ($pa != 0)
        $info .= "pa=\"" . $pa . "\" ";
    if ($af != 0)
        $info .= "af=\"" . $af . "\" ";
    if (($distance == 'Y') && ($chute != 0))
        $info .= "chute=\"" . $chute . "\" ";
    if ($val_des != 0)
        $info .= "degats=\"" . $des . "D" . $val_des . "+" . $bonus . "\" ";

    $dex_class = "";
    if ($seuil_dex != 0)
    {
        if ($dex < ($seuil_dex - 3))
            $dex_class = " <img src ='" . $path . "rouge.gif'/>";
        else if ($dex < $seuil_dex)
            $dex_class = " <img src ='" . $path . "orange.gif'/>";
        else if ($dex > ($seuil_dex + 3))
            $dex_class = " <img src ='" . $path . "vert.gif'/>";

        $info .= "dexterite=\"" . $seuil_dex . "\" ";
        $info .= "class_dex=\"" . $dex_class . "\" ";
    }

    $for_class = "";
    if ($seuil_for != 0)
    {
        if ($force < ($seuil_for - 3))
            $for_class = " <img src ='" . $path . "rouge.gif'/>";
        else if ($force < $seuil_for)
            $for_class = " <img src ='" . $path . "orange.gif'/>";
        else if ($force > ($seuil_for + 3))
            $for_class = " <img src ='" . $path . "vert.gif'/>";

        $info .= "force=\"" . $seuil_for . "\" ";
        $info .= "class_for=\"" . $for_class . "\" ";
    }

    if ($comp != "")
        $info .= "competence=\"" . $comp . "\" ";

    if ($perso_pa >= 2)
        $info .= "equipe=\"" . $bool_equipe . "\" ";
    //else $info .= "equipe=\"0\" ";

    if (($bool_equipe != 1) && ($perso_pa >= 1))
        $info .= "abandonne=\"1\" ";

    if (( ($type == 1) || ( ( ($type == 2) || ($type == 3)) && ( $bool_equipe != 1 ) ) ) && ($perso_pa >= 2))  // && assez de cométence pour reparer
        $info .= "repare=\"1\" ";

    /* if (($perso_pa >=2) && ($ok_equipe == 1) )
      $info .= "equipe=\"1\" "; */ // affiche désequiper
    //  Modificateur de vue :
    if ($bonus_vue != 0)
        $info .= "bonus_vue=\"" . $bonus_vue . "\" ";

    // Protection contre les critiques/spéciaux :
    if ($critique != 0)
        $info .= "critique=\"" . $critique . " %\" ";

    // Vampirisme :
    if ($vampire != 0)
        $info .= "vampirisme=\"" . $vampire . "\" ";

    // Aura de feu
    if ($aura_feu != 0)
        $info .= "aura_de_feu=\"" . $aura_feu . "\" ";

    // Bonus à la régénération
    if ($regen != 0)
        $info .= "regen=\"" . $regen . "\" ";

    // Dégâts infligés par poison :
    if ($poison != 0)
        $info .= "poison=\"" . $poison . "\" ";

    if ($enchantable == 1)
        $info .= "enchantable=\"1\" ";

    if ($deposable !== 0)
        $info .= "deposable=\"1\" ";

    if ($url != "")
        $info .= "url=\"" . $url . "\" ";
    $info .= " />";

    return $info;
}

/**
 * 
 * Fonction get_infos_objet
 *
 * Retourne le texte de l'image et les informations correspondant à l'arme
 *
 * @var $class: class de l'image
 * @var $path: repertoires des images
 * @var $img: nom du fichier image
 * @var $nom: nom de l'objet
 * @var $poids: poids
 * @var $desc: description
 * @var $etat: etat
 * @var $pa: nombre de PA pour utiliser l'objet
 * @var $distance: booléen indiquant si l'objet est "à distance" ou pas
 * @var $af: nombre de PA pour utiliser l'objet sous Attaque Foudroyante
 * @var $des: nombre de Dés
 * @var $val_des: valeur du Dé
 * @var $bonus: bonus 
 * @var $seuil_dex: Seuil de dextérité d'utilisation 
 * @var $seuil_for: Seuil de force d'utilisation 
 * @var $comp: compétence utilisé par l'objet
 * @var $bool_equipe: booléen (-1,0,1) indiquant si l'objet est équipé ou non
 * @var $bonus_vue: bonus de vue accordé par l'objet
 * @var $vampire: valeur de la caractéristique vampirisme
 * @var $aura_feu: valeur de la caractéristique aura de feu
 * @var $regen: valeur de la caractéristique régénération
 * @var $poison: valeur de la caractéristique poison
 * @var $enchantable: booléen indiquant si l'objet est enchantable ou pas
 * @var $deposable: booléen indiquant si l'objet est déposable ou pas
 * @var $perso_pa: nombre de PA du joueur
 * @var $dex: dextérite du joueur
 * @var $force: force du joueur
 *
 * @return $info:  texte de l'image et les informations correspondant à l'arme
 *
 * */
function get_infos_objet($class, $path, $img, $id = 0, $nom, $poids = 0, $desc = "", $etat = 0, $bool_equipe = 0, $bonus_vue = 0, $critique = 0, $vampire = 0, $aura_feu = 0, $regen = 0, $poison = 0, $enchantable = 0, $deposable = 1, $perso_pa = "", $dex = 0, $force = 0, $url = "")
{
    $info = "";
    $info .= "<img class=\"" . $class . "\" src=\"" . $path . $img . "\" title=\"" . $nom . "\" ";
    if ($desc != "")
        $info .= "desc=\"" . $desc . "\" ";
    if ($poids != 0)
        $info .= "poids=\"" . $poids . "\" ";
    if ($id != 0)
        $info .= "num=\"" . $id . "\" ";

    $lib_etat = "";
    if ($etat != 0)
    {
        $lib_etat  = get_etat($etat);
        $img_usure = get_img_usure($etat, $path);
    }
    if ($lib_etat != "")
        $info .= "etat=\"" . $lib_etat . "\" ";
    if ($etat != 0)
        $info .= " img_etat=\"" . $img_usure . "\" ";


    if (($bool_equipe != 1) && ($perso_pa >= 1))
        $info .= "abandonne=\"1\" ";

    /*
      if ( ($perso_pa >= 2) )
      $info .= "repare=\"1\" ";
     */

    //  Modificateur de vue :
    if ($bonus_vue != 0)
    {
        $info .= "bonus_vue=\"" . $bonus_vue . "\" ";
    }

    // Protection contre les critiques/spéciaux :
    if ($critique != 0)
    {
        $info .= "critique=\"" . $critique . " %\" ";
    }

    // Vampirisme :
    if ($vampire != 0)
    {
        $info .= "vampirisme=\"" . $vampire . "\" ";
    }

    // Aura de feu
    if ($aura_feu != 0)
    {
        $info .= "aura_de_feu=\"" . $aura_feu . "\" ";
    }

    // Bonus à la régénération
    if ($regen != 0)
    {
        $info .= "regen=\"" . $regen . "\" ";
    }

    // Dégâts infligés par poison :
    if ($poison != 0)
    {
        $info .= "poison=\"" . $poison . "\" ";
    }

    if ($enchantable == 1)
        $info .= "enchantable=\"1\" ";

    if ($deposable !== 0)
        $info .= "deposable=\"1\" ";

    if ($url != "")
        $info .= "url=\"" . $url . "\" ";
    $info .= " />";

    return $info;
}

$debug = 0;


//G_IMAGES_sav = G_IMAGES;
define(G_IMAGES,"./images/");

$db_form = new base_delain;

$resultat_form = "";


/* * ********************************* */
/* * ***** Début Gestion Depot Or ****** */
/* * ******************************** */

/* Variables des formulaires proventant des popup */
$quantite = (isset($_POST['quantite']) && ($_POST['quantite'] != '')) ? $_POST['quantite'] : -1;
//echo "quantite: $quantite.<br/>";
if ($quantite != -1)
{
    echo "<div class=\"resultat_formulaire\">";
    if ($quantite <= 0)
    {
        $resultat_form .= "<div class=\"resultat_formulaire\">La somme que vous voulez mettre au sol n'est pas valide !</div>";
    }
    else
    {
        $req_depose = "select depose_or($perso_cod,$quantite) as depose";
        $db_form->query($req_depose);
        $db_form->next_record();
        if ($db_form->f("depose") == 0)
        {
            $resultat_form .= "<div class=\"resultat_formulaire\">Vous avez déposé avec succès $quantite brouzoufs au sol.</div>";
        }
        else
        {
            $resultat_form .= "<div class=\"resultat_formulaire\">Une erreur est survenue : " . $db_form->f("depose") . "</div>";
        }
    }
    echo "</div>";
}
/* * ****************************** */
/* * ***** Fin Gestion Depot Or ****** */
/* * ****************************** */

/* Traitement actions du formulaire */
$methode  = isset($_POST['methode']) ? $_POST['methode'] : -1;
$idObjet  = isset($_POST['idObjet']) ? $_POST['idObjet'] : -1;
$nomObjet = isset($_POST['nomObjet']) ? $_POST['nomObjet'] : -1;

/*
  echo "methode: ".$methode."<br/>";
  echo "idObjet: ".$idObjet."<br/>";
 */

if (($methode != -1) && ($idObjet != -1))
{
    switch ($methode)
    {
        case "remettre":
            $req_persobj_cod = "select perobj_cod from perso_objets where perobj_perso_cod = $perso_cod and perobj_obj_cod = $idObjet and perobj_equipe = 'O' ";
            $db_form->query($req_persobj_cod);
            $nb_a_remetre    = $db_form->nf();
            $db_form->next_record();
            $perobj          = $db_form->f("perobj_cod");

            $req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
            $db_form->query($req_pa);
            $db_form->next_record();
            $pa     = $db_form->f("perso_pa");
            if ($nb_a_remetre >= 1)
            {
                if ($pa >= 2)
                {
                    $req_remettre = "select remettre_objet($perso_cod,$perobj)";
                    //echo $req_remettre;
                    $db_form->query($req_remettre);
                    $db_form->next_record();

                    $resultat_form .= "<div class=\"resultat_formulaire\">L'équipement " . $nomObjet . " a été remis dans votre inventaire</div>";
                }
                else
                {
                    $resultat_form .= "<div class=\"resultat_formulaire\">Vous n'avez pas assez de PA pour effectuer cette action !</div>";
                }
            }
            else
            {
                $resultat_form .= "<div class=\"resultat_formulaire\">L'équipement " . $nomObjet . " est déjà dans votre inventaire</div>";
            }
            break;

        case "equiper":
            $req_pa = "select perso_pa,perso_type_perso from perso where perso_cod = $perso_cod";
            $db_form->query($req_pa);
            $db_form->next_record();
            if ($db_form->f("perso_pa") < 2)
            {
                $resultat_form .= "<div class=\"resultat_formulaire\">Vous n'avez pas assez de PA pour effectuer cette action !</div>";
                $erreur = 1;
            }
            if ($db_form->f("perso_type_perso") == 3)
            {
                $resultat_form .= "<div class=\"resultat_formulaire\">Un familier ne peut pas équiper d'objet !</div>";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $req_remettre = "select equipe_objet($perso_cod,$idObjet) as equipe";
                $db_form->query($req_remettre);
                $db_form->next_record();
                $tab_remettre = $db_form->f("equipe");
                if ($tab_remettre == 0)
                {
                    $resultat_form .= "<div class=\"resultat_formulaire\">L'objet " . $nomObjet . " a été équipé avec succès.</div>";
                }
                else
                {
                    $resultat_form .= "<div class=\"resultat_formulaire\">" . substr(strstr($tab_remettre, ';'), 1) . "</div>";
                }
            }
            break;

        case "abandonner":

            $req = 'select depose_objet(' . $perso_cod . ',' . $idObjet . ') as resultat ';
            //echo $req;
            $db_form->query($req);
            $db_form->next_record();
            $resultat_form .= "<div class=\"resultat_formulaire\">" . $nomObjet . " : " . $db_form->f('resultat') . "</div>";

            break;

        case "identifier":
            $limite_exp     = $db_form->getparm_n(1);
            $req_identifier = "select identifier_objet($perso_cod,$idObjet) as identifie";
            //echo $req_identifier."<br/>";
            $db_form->query($req_identifier);
            $db_form->next_record();
            $resultat       = $db_form->f("identifie");
            $tab_res        = explode(";", $resultat);
            $resultat_form .= "<div class=\"resultat_formulaire\">";
            if ($tab_res[0] == -1)
            {
                $resultat_form .="<p>Une erreur est survenue : $tab_res[1]";
            }
            else
            {
                $resultat_form .= "<p>Vous tentez d'identifier l'objet " . $nomObjet . "</p>";
                $resultat_form .= "<p>Vous avez utilisé la compétence $tab_res[2] ($tab_res[3] %)</p>";
                $resultat_form .= "<p>Votre lancer de dés est <b>$tab_res[4]</b>, ";
                if ($tab_res[5] == -1)
                {
                    $resultat_form .= "il s'agit donc d'un échec automatique.";
                }
                if ($tab_res[5] == 0)
                {
                    $resultat_form .= "vous avez donc échoué dans cette compétence.<br>";
                    if ($tab_res[3] <= $limite_exp)
                    {
                        $resultat_form .= "Votre compétence est inférieure à $limite_exp %. Votre jet d'amélioration est de <b>$tab_res[6]</b>.<br>";
                        if ($tab_res[7] == 0)
                        {
                            $resultat_form .= "Vous n'avez pas réussi à améliorer cette compétence.";
                        }
                        if ($tab_res[7] == 1)
                        {
                            $resultat_form .= "Vous avez réussi à améliorer cette compétence. Sa nouvelle valeur est <b>$tab_res[8]%</b>";
                        }
                    }
                }
                if ($tab_res[5] == 1)
                {
                    $resultat_form .= "vous avez réussi cette compétence !</p>";
                    $req_objet = "select obj_nom,obj_enchantable from objets ";
                    $req_objet = $req_objet . "where obj_cod = $tab_res[6] ";
                    $db_form->query($req_objet);
                    $db_form->next_record();

                    $resultat_form .= "<p>L'objet identifié est : <b>" . $db_form->f("obj_nom") . "</b>. Vous pouvez maintenant l'utiliser.</p>";
                    if ($db_form->f('obj_enchantable') == 1)
                        $resultat_form .= "<p>De plus, cet objet est <b>enchantable</b>";
                    $resultat_form .= "<hr>";
                    $resultat_form .= "<p>Vous gagnez $tab_res[7] PX.</p>";
                    $resultat_form .= "<hr>";
                    $resultat_form .= "<p>Votre jet d'amélioration est de $tab_res[8]. ";
                    if ($tab_res[9] == 0)
                    {
                        $resultat_form .= "<p>Vous n'avez pas réussi à améliorer cette compétence.";
                    }
                    else
                    {
                        $resultat_form .= "<p>Vous avez réussi à améliorer cette compétence. Sa nouvelle valeur est de $tab_res[10].";
                    }
                }
            }
            $resultat_form .= "</div>";
            break;

        case 'reparer':
            $type_rep[1] = 'arme';
            $type_rep[2] = 'armure';
            $type_rep[4] = 'casque';
            $autorise    = 0;
            $resultat_form .= "<div class=\"resultat_formulaire\">";
            $resultat_form .= "<p>Vous tentez de réparer l'objet " . $nomObjet . "</p>";

            $req_type = "select gobj_tobj_cod from objet_generique where gobj_cod = (select obj_gobj_cod from objets where obj_cod = " . $idObjet . " ) ";
            //echo $req_type."<br/>";
            $db_form->query($req_type);
            $db_form->next_record();
            $type     = $db_form->f('gobj_tobj_cod');

            $query_val     = "select gobj_tobj_cod
								from objets,objet_generique 
								where obj_gobj_cod = gobj_cod
								and obj_cod = " . $idObjet;
            //echo $query_val."<br/>";
            $db_form->query($query_val);
            $db_form->next_record();
            $type_controle = $db_form->f('gobj_tobj_cod');
            if ($type_controle != $type)
            {
                $query_val = "insert into trace2 (trace2_texte) values ($perso_cod)";
                //echo $query_val."<br/>";
                $db_form->query($query_val);
            }
            $query_val = "select perobj_cod
								from perso_objets
								where perobj_perso_cod = " . $perso_cod . "
								and perobj_obj_cod = " . $idObjet . " 
								and perobj_identifie = 'O' 
								and ( (perobj_equipe = 'N' and " . $type . " in (2,4) )
									  or ( " . $type . " = 1)
									)";
            //echo $query_val."<br/>";
            $db_form->query($query_val);
            if ($db_form->nf() != 0)
                $autorise  = 1;


            //if (($type != 1) && ($type != 2) && ($type != 4))
            if ($autorise != 1)
            {
                $resultat_form .= '<p>Inutile d\'essayer de réparer ce genre d\'objets....';
            }
            else
            {
                $req = 'select f_repare_' . $type_rep[$type] . '(' . $perso_cod . ',' . $idObjet . ') as resultat';
                $db_form->query($req);
                $db_form->next_record();
                $resultat_form .= $db_form->f('resultat');
            }
            //$contenu_page .= '<center><a href="inventaire.php">Retour à l\'inventaire</a></center>';
            $resultat_form .= "</div>";
            break;
    }
}

/* Les données */
$req_or = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
$db->query($req_or);
$nb_or  = $db->nf();
if ($nb_or == 0)
    $qte_or = 0;
else
{
    $db->next_record();
    $qte_or = $db->f("pbank_or");
}

/* a mettre dans les pages dédiées */
$cout_repar = $db->getparm_n(40);

// Calcul du poids porté
$req_poids   = "select get_poids($perso_cod) as poids,perso_enc_max from perso where perso_cod = $perso_cod ";
$db->query($req_poids);
$db->next_record();
$poids_porte = $db->f("poids");
$poids_total = $db->f("perso_enc_max");


// identification auto de certains objets (runes, objets de quêtes, poissons, etc...)
$req_id = "update perso_objets 
					set perobj_identifie = 'O' 
					where perobj_perso_cod = " . $perso_cod . "
					and perobj_identifie <> 'O'
					and exists (select 1 
								from objets,objet_generique,type_objet 
								where perobj_obj_cod = obj_cod 
								and obj_gobj_cod = gobj_cod 
								and gobj_tobj_cod = tobj_cod
								and tobj_identifie_auto = 1 ) ";
$db->query($req_id);


/* armure */
$req_armure = "select obj_armure from perso_objets,objets,objet_generique,objets_caracs where perobj_perso_cod = " . $perso_cod . " ";
$req_armure = $req_armure . "and perobj_equipe = 'O' and perobj_obj_cod = obj_cod and obj_gobj_cod = gobj_cod and gobj_tobj_cod = 2 ";
$req_armure = $req_armure . "and gobj_obcar_cod = obcar_cod";
$db_armure  = new base_delain;
$db_armure->query($req_armure);
$nb_armure  = $db_armure->nf();
$la_armure  = 0;
if ($nb_armure != 0)
{
    $db_armure->next_record();
    $la_armure = $db_armure->f("obj_armure");
}

/* armes */
$req_arme = "select obj_chute, obj_des_degats,obj_val_des_degats,obj_bonus_degats from perso_objets,objets,objet_generique,objets_caracs where perobj_perso_cod = " . $perso_cod . " ";
$req_arme = $req_arme . "and perobj_equipe = 'O' and perobj_obj_cod = obj_cod and obj_gobj_cod = gobj_cod and gobj_tobj_cod = 1 and gobj_obcar_cod = obcar_cod";
$db_arme  = new base_delain;
$db_arme->query($req_arme);
$nb_arme  = $db_arme->nf();

$le_nb_des  = 1;
$le_val_des = 3;
$le_bonus   = 0;
$la_chute   = 0;
if ($nb_arme != 0)
{
    $db_arme->next_record();
    $le_nb_des  = $db_arme->f("obj_des_degats");
    $le_val_des = $db_arme->f("obj_val_des_degats");
    $le_bonus   = $db_arme->f("obj_bonus_degats");
    $la_chute   = $db_armure->f("obj_chute");
}

/* arme à distance */
$arme_distance = $db->arme_distance($perso_cod);

/* caractéristiques */
$requete = "select perso_amelioration_armure, perso_amelioration_degats, perso_amel_deg_dex, 
					perso_for, perso_dex, perso_int, perso_con
					from perso
					where perso_cod = " . $perso_cod;
$db->query($requete);
$db->next_record();

if (!$arme_distance)
    $amelioration_arme = $db->f('perso_amelioration_degats');
else
    $amelioration_arme = $db->f('perso_amel_deg_dex');
$amel_armure       = $db->f("perso_amelioration_armure");
$force             = $db->f("perso_for");
$dexterite         = $db->f("perso_dex");
$intelligence      = $db->f("perso_int");
$constitution      = $db->f("perso_con");


/* Infos Perso */
$requete = "select perso_pa, perso_po, perso_sex, to_char(perso_dcreat,'DD/MM/YYYY hh24:mi:ss') as date_cre, race_nom
			from perso, race
			where perso_cod = $perso_cod
			and perso_race_cod = race_cod ";

$db->query($requete);
$db->next_record();
$sexe          = $db->f("perso_sex");
$race          = $db->f('race_nom');
$date_creation = $db->f("date_cre");
$po            = $db->f("perso_po");
$perso_pa      = $db->f("perso_pa");


/* Matériel équipé */
$img_croix         = "croix.png";
$img_croix_verte   = "croix_verte.png";
$Recup_image_corps = $img_croix;
$Recup_image_bd    = $img_croix;
$Recup_image_bg    = $img_croix;
$Recup_image_tete  = $img_croix;

$nom_croix = "Rien";
$nom_corps = "Ton Corps";
/*
  $nom_bd = "Ton Bras Droit";
  $nom_bg = "Ton Bras Gauche";
 */
$nom_bd    = "Ton Bras";
$nom_bg    = "Ton Bras";
$nom_tete  = "Ta Tête";

$req_equipe = "select perobj_obj_cod, obj_des_degats, obj_val_des_degats, obj_bonus_degats, obj_chute, obj_armure, obj_etat, obj_etat_max, obj_cod, tobj_cod, ";
$req_equipe = $req_equipe . " gobj_cod, perobj_cod, gobj_tobj_cod, obj_nom, obj_nom_generique, ";
$req_equipe = $req_equipe . " tobj_libelle, obj_poids, gobj_pa_normal, gobj_pa_eclair, gobj_distance, gobj_deposable, ";
$req_equipe = $req_equipe . " obj_description, coalesce(obj_seuil_force,0) as obj_seuil_force, gobj_url, ";
$req_equipe = $req_equipe . " obj_seuil_dex, coalesce(obj_bonus_vue,0) as obj_bonus_vue, coalesce(obj_critique,0) as obj_critique, ";
$req_equipe = $req_equipe . " coalesce(obj_vampire,0) as obj_vampire, coalesce(obj_aura_feu,0) as obj_aura_feu, ";
$req_equipe = $req_equipe . " obj_enchantable, obj_poison, obj_regen, gobj_image , gobj_image_generique, ";
$req_equipe = $req_equipe . " ( select comp_libelle from competences where comp_cod = gobj_comp_cod and gobj_tobj_cod = 1) as comp_libelle ";
$req_equipe = $req_equipe . " from perso_objets, objets, objet_generique, type_objet  ";
$req_equipe = $req_equipe . " where perobj_perso_cod = $perso_cod ";
$req_equipe = $req_equipe . " and perobj_equipe = 'O' ";
$req_equipe = $req_equipe . " and perobj_obj_cod = obj_cod ";
$req_equipe = $req_equipe . " and obj_gobj_cod = gobj_cod ";
$req_equipe = $req_equipe . " and gobj_tobj_cod = tobj_cod ";
$req_equipe = $req_equipe . " order by tobj_cod ";
//echo "<p>".$req_equipe."</p>";
$db->query($req_equipe);
$nb_equipe  = $db->nf();

$ok_corps             = 0;
$ok_bd                = 0;
$ok_bg                = 0;
$ok_tete              = 0;
for ($i = 0; $i < 25; $i++)
    $nb_obj_equipable[$i] = 0;

while ($db->next_record())
{
    $seuil_for             = $db->f("obj_seuil_force");
    $seuil_dex             = $db->f("obj_seuil_dex");
    $Recup_image           = $db->f("gobj_image");
    $Recup_image_generique = $db->f("gobj_image_generique");
    $image                 = G_IMAGES . $Recup_image;
    $t_etat                = 0;
    $comp                  = $db->f("gobj_comp_cod");
    $libelle_comp          = $db->f("comp_libelle");
    $desc                  = htmlspecialchars($db->f("obj_description"));
    $etat                  = $db->f("obj_etat");
    $etat_max              = $db->f("obj_etat_max");
    $objet_cod             = $db->f("obj_cod");
    $tobj_cod              = $db->f("tobj_cod");
    $nb_obj_equipable[$tobj_cod] ++; // on ajoute l'objet equipé
    $gobj_cod              = $db->f("gobj_cod");
    $perobj_cod            = $db->f("perobj_cod");
    $gobj_tobj_cod         = $db->f("gobj_tobj_cod");
    $id                    = $db->f("perobj_obj_cod");
    $nom                   = $db->f("obj_nom");
    $libelle_type          = $db->f("tobj_libelle");
    $poids                 = $db->f("obj_poids");
    $pa                    = $db->f("gobj_pa_normal");
    $armure                = $db->f("obj_armure");
    $af                    = $db->f("gobj_pa_eclair");
    $chute                 = $db->f("obj_chute");
    $equip_des             = $db->f("obj_des_degats");
    $equip_val_des         = $db->f("obj_val_des_degats");
    $equip_bonus           = $db->f("obj_bonus_degats");
    $nom_objet             = $db->f("obj_nom_generique");
    $url                   = $db->f("gobj_url");

    $equip_distance = $db->f("gobj_distance");
    $bool_distance  = 0;
    if ($equip_distance == 'O')
        $bool_distance  = 1;

    $deposable      = $db->f("gobj_deposable");
    $bool_deposable = 0;
    if ($deposable == 'N')
        $bool_deposable = 1;

    //if ($perso_pa >= 2)
    $bool_equipe = 1;
    //else $bool_equipe = 0;

    $enchantable = $db->f("obj_enchantable");
    //  Modificateur de vue :
    $bonus_vue   = $db->f("obj_bonus_vue");
    // Protection contre les critiques/spéciaux :
    $critique    = $db->f("obj_critique");
    // Vampirisme :
    $vampirisme  = $db->f("obj_vampire");
    // Aura de feu
    $aura_feu    = $db->f("obj_aura_feu");
    // Bonus à la régénération
    $regen       = $db->f("obj_regen");
    // Dégâts infligés par poison :
    $poison      = $db->f("obj_poison");


    /* Debug */
    if ($debug == 1)
    {
        echo "<p>";
        echo "seuil_for: " . $seuil_for . "<br/>";
        echo "seuil_dex: " . $seuil_dex . "<br/>";
        echo "Recup_image: " . $Recup_image . "<br/>";
        echo "image: " . $image . "<br/>";
        echo "t_etat: " . $t_etat . "<br/>";
        echo "comp: " . $comp . "<br/>";
        echo "libelle_comp: " . $libelle_comp . "<br/>";
        echo "desc: " . $desc . "<br/>";
        echo "etat: " . $etat . "<br/>";
        echo "etat_max: " . $etat_max . "<br/>";
        echo "objet_cod: " . $objet_cod . "<br/>";
        echo "tobj_cod: " . $tobj_cod . "<br/>";
        echo "gobj_cod: " . $gobj_cod . "<br/>";
        echo "perobj_cod: " . $perobj_cod . "<br/>";
        echo "gobj_tobj_cod: " . $gobj_tobj_cod . "<br/>";
        echo "nom: " . $nom . "<br/>";
        echo "libelle_type: " . $libelle_type . "<br/>";
        echo "poids: " . $poids . "<br/>";
        echo "pa: " . $pa . "<br/>";
        echo "af: " . $af . "<br/>";
        echo "chute: " . $chute . "<br/>";
        echo "des: " . $equip_des . "<br/>";
        echo "val_des: " . $equip_val_des . "<br/>";
        echo "bonus: " . $equip_bonus . "<br/>";
        echo "nom_objet: " . $nom_objet . "<br/>";
        echo "url: " . $url . "<br/>";
        echo "bool_distance: " . $bool_distance . "<br/>";
        echo "bool_deposable: " . $bool_deposable . "<br/>";
        echo "bool_distance: " . $bool_distance . "<br/>";
        echo "bool_equipe: " . $bool_equipe . "<br/>";
        echo "enchantable: " . $enchantable . "<br/>";
        echo "bonus_vue: " . $bonus_vue . "<br/>";
        echo "critique: " . $critique . "<br/>";
        echo "vampirisme: " . $vampirisme . "<br/>";
        echo "aura_feu: " . $aura_feu . "<br/>";
        echo "regen: " . $regen . "<br/>";
        echo "poison: " . $poison . "<br/>";
        echo "</p>";
    }

    // arme
    if ($tobj_cod == 1)
    {
        $ok_bd             = 1;
        $img_bd            = "daikatana.gif";
        $libelle_comp_bd   = $libelle_comp;
        $tobj_cod_bd       = $tobj_cod;
        $gobj_cod_bd       = $gobj_cod;
        $perobj_cod_bd     = $perobj_cod;
        $gobj_tobj_cod_bd  = $gobj_tobj_cod;
        $Recup_image_bd    = $Recup_image;
        if ($Recup_image == "")
            $Recup_image_bd    = $img_croix_verte;
        $chute_bd          = $chute;
        $des_bd            = $equip_des;
        $val_des_bd        = $equip_val_des;
        $bonus_bd          = $equip_bonus;
        $nom_objet_bd      = $nom_objet;
        $bool_distance_bd  = $bool_distance;
        $bool_deposable_bd = $bool_deposable;
        $bool_equipe_bd    = $bool_equipe;
        $url_bd            = $url;
        $nom_bd            = $nom;
        $id_bd             = $id;
        $seuil_for_bd      = $seuil_for;
        $seuil_dex_bd      = $seuil_dex;
        $image_bd          = $image;
        $comp_bd           = $comp;
        $desc_bd           = $desc;
        $etat_bd           = $etat;
        $etat_max_bd       = $etat_max;
        $objet_cod_bd      = $objet_cod;
        $libelle_type_bd   = $libelle_type;
        $pa_bd             = $pa;
        $af_bd             = $af;
        $poids_bd          = $poids;
        $deposable_bd      = $deposable;
        $bonus_vue_bd      = $bonus_vue;
        $critique_bd       = $critique;
        $vampirisme_bd     = $vampirisme;
        $aura_feu_bd       = $aura_feu;
        $regen_bd          = $regen;
        $poison_bd         = $poison;
        $enchantable_bd    = $enchantable;
    }

    // armure
    if ($tobj_cod == 2)
    {
        $ok_corps             = 1;
        //echo "ok: ".$ok_corps;
        //$img_corps = $img_croix;			
        $img_corps            = "armuredecuir.png";
        $armure_corps         = $armure;
        $libelle_comp_corps   = $libelle_comp;
        $tobj_cod_corps       = $tobj_cod;
        $gobj_cod_corps       = $gobj_cod;
        $perobj_cod_corps     = $perobj_cod;
        $gobj_tobj_cod_corps  = $gobj_tobj_cod;
        $Recup_image_corps    = $Recup_image;
        if ($Recup_image == "")
            $Recup_image_corps    = $img_croix_verte;
        $chute_corps          = $chute;
        $des_corps            = $equip_des;
        $val_des_corps        = $equip_val_des;
        $bonus_corps          = $equip_bonus;
        $nom_objet_corps      = $nom_objet;
        $bool_distance_corps  = $bool_distance;
        $bool_deposable_corps = $bool_deposable;
        $bool_equipe_corps    = $bool_equipe;
        $url_corps            = $url;
        $nom_corps            = $nom;
        $id_corps             = $id;
        $seuil_for_corps      = $seuil_for;
        $seuil_dex_corps      = $seuil_dex;
        $image_corps          = $image;
        $comp_corps           = $comp;
        $desc_corps           = $desc;
        $etat_corps           = $etat;
        $etat_max_corps       = $etat_max;
        $objet_cod_corps      = $objet_cod;
        $libelle_type_corps   = $libelle_type;
        $pa_corps             = $pa;
        $af_corps             = $af;
        $poids_corps          = $poids;
        $deposable_corps      = $deposable;
        $bonus_vue_corps      = $bonus_vue;
        $critique_corps       = $critique;
        $vampirisme_corps     = $vampirisme;
        $aura_feu_corps       = $aura_feu;
        $regen_corps          = $regen;
        $poison_corps         = $poison;
        $enchantable_corps    = $enchantable;
    }

    // instrument
    if ($tobj_cod == 15)
    {
        $ok_bg            = 1;
        $img_bg           = $img_croix;
        //$img_bg = "armuredecuir.png";
        $libelle_comp_bg  = $libelle_comp;
        $tobj_cod_bg      = $tobj_cod;
        $gobj_cod_bg      = $gobj_cod;
        $perobj_cod_bg    = $perobj_cod;
        $gobj_tobj_cod_bg = $gobj_tobj_cod;
        $Recup_image_bg   = $Recup_image;
        if ($Recup_image == "")
            $Recup_image_bg   = $img_croix_verte;

        $chute_bg          = $chute;
        $des_bg            = $equip_des;
        $val_des_bg        = $equip_val_des;
        $bonus_bg          = $equip_bonus;
        $nom_objet_bg      = $nom_objet;
        $bool_distance_bg  = $bool_distance;
        $bool_deposable_bg = $bool_deposable;
        $bool_equipe_bg    = $bool_equipe;
        $url_bg            = $url;
        $nom_bg            = $nom;
        $id_bg             = $id;
        $seuil_for_bg      = $seuil_for;
        $seuil_dex_bg      = $seuil_dex;
        $image_bg          = $image;
        $comp_bg           = $comp;
        $desc_bg           = $desc;
        $etat_bg           = $etat;
        $etat_max_bg       = $etat_max;
        $objet_cod_bg      = $objet_cod;
        $libelle_type_bg   = $libelle_type;
        $pa_bg             = $pa;
        $af_bg             = $af;
        $poids_bg          = $poids;
        $deposable_bg      = $deposable;
        $bonus_vue_bg      = $bonus_vue;
        $critique_bg       = $critique;
        $vampirisme_bg     = $vampirisme;
        $aura_feu_bg       = $aura_feu;
        $regen_bg          = $regen;
        $poison_bg         = $poison;
        $enchantable_bg    = $enchantable;
    }

    // casque
    if ($tobj_cod == 4)
    {
        $ok_tete            = 1;
        $img_tete           = $img_croix;
        //$img_tete = "armuredecuir.png";
        $libelle_comp_tete  = $libelle_comp;
        $tobj_cod_tete      = $tobj_cod;
        $gobj_cod_tete      = $gobj_cod;
        $perobj_cod_tete    = $perobj_cod;
        $gobj_tobj_cod_tete = $gobj_tobj_cod;
        $Recup_image_tete   = $Recup_image;
        if ($Recup_image == "")
            $Recup_image_tete   = $img_croix_verte;

        $chute_tete          = $chute;
        $des_tete            = $equip_des;
        $val_des_tete        = $equip_val_des;
        $bonus_tete          = $equip_bonus;
        $nom_objet_tete      = $nom_objet;
        $bool_distance_tete  = $bool_distance;
        $bool_deposable_tete = $bool_deposable;
        $bool_equipe_tete    = $bool_equipe;
        $url_tete            = $url;
        $nom_tete            = $nom;
        $id_tete             = $id;
        $seuil_for_tete      = $seuil_for;
        $seuil_dex_tete      = $seuil_dex;
        $image_tete          = $image;
        $comp_tete           = $comp;
        $desc_tete           = $desc;
        $etat_tete           = $etat;
        $etat_max_tete       = $etat_max;
        $objet_cod_tete      = $objet_cod;
        $libelle_type_tete   = $libelle_type;
        $pa_tete             = $pa;
        $af_tete             = $af;
        $poids_tete          = $poids;
        $deposable_tete      = $deposable;
        $bonus_vue_tete      = $bonus_vue;
        $critique_tete       = $critique;
        $vampirisme_tete     = $vampirisme;
        $aura_feu_tete       = $aura_feu;
        $regen_tete          = $regen;
        $poison_tete         = $poison;
        $enchantable_tete    = $enchantable;
    }
}

$libelle_etat = $etat;

$req_type_obj_equipe = "select tobj_max_equip, tobj_cod ";
$req_type_obj_equipe = $req_type_obj_equipe . "from type_objet ";

$db->query($req_type_obj_equipe);
$nb_obj_equipe = $db->nf();

for ($i = 0; $i < 25; $i++)
    $type[$i] = 0;

while ($db->next_record())
{
    $type[$db->f("tobj_cod")] = $db->f("tobj_max_equip");
}

/*  equipables */
$req_equipable = "select perobj_obj_cod,
						obj_nom,
						obj_etat,
						obj_etat_max, 
						perobj_identifie,
						perobj_equipe,
						obj_nom_generique,
						gobj_tobj_cod,
						tobj_libelle,
						obj_poids,
						gobj_pa_normal, 
						gobj_pa_eclair, 
						gobj_distance,
						gobj_deposable,
						gobj_comp_cod,
						obj_description,
						obj_chute,
						obj_des_degats, 
						obj_val_des_degats, 
						obj_bonus_degats,
						obj_armure,
						coalesce(obj_bonus_vue,0) as obj_bonus_vue,
						coalesce(obj_critique,0) as obj_critique,
						coalesce(obj_vampire,0) as obj_vampire,
						coalesce(obj_aura_feu,0) as obj_aura_feu,
						coalesce(obj_seuil_force,0) as obj_seuil_force,
						obj_seuil_dex, 
						obj_enchantable,
						obj_poison,
						obj_regen,
						gobj_image,
						gobj_image_generique,
						gobj_url,
						obj_cod, 
						tobj_cod,
						gobj_cod, 
						perobj_cod, 
						tobj_max_equip,
						( select comp_libelle from competences where comp_cod = gobj_comp_cod and gobj_tobj_cod = 1) as comp_libelle  ";
$req_equipable = $req_equipable . "from perso_objets, objets, objet_generique, type_objet ";
$req_equipable = $req_equipable . "where perobj_perso_cod = $perso_cod ";
$req_equipable = $req_equipable . "and tobj_equipable = 1 ";
$req_equipable = $req_equipable . "and (gobj_visible is null or gobj_visible != 'N') ";
$req_equipable = $req_equipable . "and perobj_obj_cod = obj_cod ";
$req_equipable = $req_equipable . "and obj_gobj_cod = gobj_cod ";
$req_equipable = $req_equipable . "and gobj_tobj_cod = tobj_cod ";
$req_equipable = $req_equipable . "order by perobj_equipe desc, perobj_identifie, tobj_cod, obj_nom_generique ";
//echo $req_equipable;


/*  inequipables */
$req_inequipable = "select perobj_obj_cod,
						obj_nom,
						obj_etat,
						obj_etat_max, 
						perobj_identifie,
						perobj_equipe,
						obj_nom_generique,
						gobj_tobj_cod,
						tobj_libelle,
						obj_poids,
						gobj_pa_normal, 
						gobj_pa_eclair, 
						gobj_distance,
						gobj_deposable,
						gobj_comp_cod,
						obj_description,
						obj_chute,
						obj_des_degats, 
						obj_val_des_degats, 
						obj_bonus_degats,
						obj_armure,
						coalesce(obj_bonus_vue,0) as obj_bonus_vue,
						coalesce(obj_critique,0) as obj_critique,
						coalesce(obj_vampire,0) as obj_vampire,
						coalesce(obj_aura_feu,0) as obj_aura_feu,
						coalesce(obj_seuil_force,0) as obj_seuil_force,
						obj_seuil_dex, 
						obj_enchantable,
						obj_poison,
						obj_regen,
						gobj_image,
						gobj_image_generique,
						gobj_url,
						obj_cod, 
						tobj_cod,
						gobj_cod, 
						perobj_cod, 
						tobj_max_equip,
						( select comp_libelle from competences where comp_cod = gobj_comp_cod and gobj_tobj_cod = 1) as comp_libelle  ";
$req_inequipable = $req_inequipable . "from perso_objets, objets, objet_generique, type_objet ";
$req_inequipable = $req_inequipable . "where perobj_perso_cod = $perso_cod ";
$req_inequipable = $req_inequipable . "and tobj_equipable <> 1 ";
$req_inequipable = $req_inequipable . "and (gobj_visible is null or gobj_visible != 'N') ";
$req_inequipable = $req_inequipable . "and perobj_obj_cod = obj_cod ";
$req_inequipable = $req_inequipable . "and obj_gobj_cod = gobj_cod ";
$req_inequipable = $req_inequipable . "and gobj_tobj_cod = tobj_cod ";
$req_inequipable = $req_inequipable . "and tobj_cod not in (5,11,22) ";
$req_inequipable = $req_inequipable . "order by perobj_identifie, tobj_cod, obj_nom ";
//echo $req_inequipable;


$max_par_ligne_equipable = 4;
$min_par_ligne_equipable = 4;
$min_ligne_equipable     = 5;

$max_par_ligne_inequipable = 6;
$min_par_ligne_inequipable = 6;
$min_ligne_inequipable     = 3;
?>
</head>

<body class="delain" onload="retour();">
    <!-- DEBUT CONTAINER -->
    <div id="container" class="container">

        <div class="barrL">
            <div class="barrR">
                <div class="barrC"> </div>
            </div>
        </div>

        <div class="contenu">

            <div class="barrLbord">
                <div class="barrRbord">
                    <div class="inside_content">
                        <form name="trait_equipement" id="trait_equipement" method="post" action="#">
                            <input type="hidden" name="idObjet" id="idObjet" value="-1" style="background-color: Bisque; float: left;"/>
                            <input type="hidden" name="nomObjet" id="nomObjet" value="-1" style="background-color: Bisque; float: left;"/>
                            <input type="hidden" name="methode" id="methode" value="-1" style="background-color: Bisque; float: left;" />

                            <!-- comment <div class="titre">julien(M - Humain) -  Date de création : 08/06/2005 12:44:08&nbsp;&nbsp;Perso n°494110</div> -->

                            <div class="avatar_perso">

                                <!-- panneau du bonhomme -->
<?php
if ($race == "Nain")
{
    if ($sexe == "M")
        $class_ombre = "ombreNain";
    else
        $class_ombre = "ombreNaine";
}
else if ($race == "Humain")
{
    if ($sexe == "M")
        $class_ombre = "ombreHomme";
    else
        $class_ombre = "ombreFemme";
}
else if ($race == "Elfe")
{
    if ($sexe == "M")
        $class_ombre = "ombreElfeM";
    else
        $class_ombre = "ombreElfeF";
}
else /* les autres */
{
    if ($sexe == "M")
        $class_ombre = "ombreHomme";
    else
        $class_ombre = "ombreFemme";
}
?>
                                <div class="<?php echo $class_ombre; ?>"></div>
                                <div class="equipement">
                                    <div class="tete_ombre">
                                        Tête
                                        <br/>
                                <?php
                                if ($ok_tete == 1)
                                    $retour = get_infos_casque("img_equipement", G_IMAGES, $Recup_image_tete, $id_tete, $nom_tete, $poids_tete, $desc_tete, $etat_tete, $bool_equipe_tete, $bonus_vue_tete, $critique_tete, $vampirisme_tete, $aura_feu_tete, $regen_tete, $poison_tete, $enchantable_tete, $bool_deposable_tete, $perso_pa, $dexterite, $force);
                                else
                                    $retour = get_infos_casque("img_equipement", G_IMAGES, $Recup_image_tete, $id_tete, $nom_tete, $poids_tete, $desc_tete, $etat_tete, $bool_equipe_tete, $bonus_vue_tete, $critique_tete, $vampirisme_tete, $aura_feu_tete, $regen_tete, $poison_tete, $enchantable_tete, 1, 0, $dexterite, $force);

                                echo $retour;
                                ?> 
                                    </div>
                                    <div class="corps_ombre">
                                        Corps
                                        <br/>
                                <?php
                                if ($ok_corps == 1)
                                    $retour = get_infos_armure("img_equipement", G_IMAGES, $Recup_image_corps, $id_corps, $nom_corps, $poids_corps, $desc_corps, $etat_corps, $armure_corps, $bool_equipe_corps, $bonus_vue_corps, $critique_corps, $vampirisme_corps, $aura_feu_corps, $regen_corps, $poison_corps, $enchantable_corps, $bool_deposable_corps, $perso_pa, $dexterite, $force);
                                else
                                    $retour = get_infos_armure("img_equipement", G_IMAGES, $Recup_image_corps, $id_corps, $nom_corps, $poids_corps, $desc_corps, $etat_corps, $armure_corps, $bool_equipe_corps, $bonus_vue_corps, $critique_corps, $vampirisme_corps, $aura_feu_corps, $regen_corps, $poison_corps, $enchantable_corps, 1, 0, $dexterite, $force);
                                echo $retour;
                                ?> 
                                    </div>
                                    <div class="bras_ombre">
                                        <div class="bras_gauche_ombre">
                                            Bras <!-- Gauche -->
                                            <br/>
                                        <?php
                                        if ($ok_bg == 1)
                                            $retour = get_infos_arme("img_equipement", G_IMAGES, $Recup_image_bg, $id_bg, $nom_bg, $poids_bg, $desc_bg, $etat_bg, $pa_bg, $bool_distance_bg, $nb_des, $af_bg, null, null, null, $seuil_dex_bg, $seuil_for_bg, $libelle_comp_bg, $bool_equipe_bg, $bonus_vue_bg, $critique_bg, $vampirisme_bg, $aura_feu_bg, $regen_bg, $poison_bg, $enchantable_bg, $bool_deposable_bg, $perso_pa, $dexterite, $force);
                                        else
                                            $retour = get_infos_arme("img_equipement", G_IMAGES, $Recup_image_bg, $id_bg, $nom_bg, $poids_bg, $desc_bg, $etat_bg, 0, $bool_distance_bg, $nb_des, $af_bg, null, null, null, $seuil_dex_bg, $seuil_for_bg, $libelle_comp_bg, $bool_equipe_bg, $bonus_vue_bg, $critique_bg, $vampirisme_bg, $aura_feu_bg, $regen_bg, $poison_bg, $enchantable_bg, 1, 0, $dexterite, $force);

                                        echo $retour;
                                        ?>
                                        </div>
                                        <div class="bras_droit_ombre">
                                            Bras <!-- Droit -->
                                            <br/>
                                        <?php
                                        if ($ok_bd == 1)
                                            $retour = get_infos_arme("img_equipement", G_IMAGES, $Recup_image_bd, $id_bd, $nom_bd, $poids_bd, $desc_bd, $etat_bd, $pa_bd, $bool_distance_bd, $chute_bd, $af_bd, $des_bd, $val_des_bd, $bonus_bd, $seuil_dex_bd, $seuil_for_bd, $libelle_comp_bd, $bool_equipe_bd, $bonus_vue_bd, $critique_bd, $vampirisme_bd, $aura_feu_bd, $regen_bd, $poison_bd, $enchantable_bd, $bool_deposable_bd, $perso_pa, $dexterite, $force);
                                        else
                                            $retour = get_infos_arme("img_equipement", G_IMAGES, $Recup_image_bd, $id_bd, $nom_bd, $poids_bd, $desc_bd, $etat_bd, $pa_bd, $bool_distance_bd, $chute_bd, $af_bd, $des_bd, $val_des_bd, $bonus_bd, $seuil_dex_bd, $seuil_for_bd, $libelle_comp_bd, $bool_equipe_bd, $bonus_vue_bd, $critique_bd, $vampirisme_bd, $aura_feu_bd, $regen_bd, $poison_bd, $enchantable_bd, 1, 0, $dexterite, $force);

                                        echo $retour;
                                        ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="partie_gauche">
                                <!-- DEBUT PANNEAU Objets Equipables -->
                                <div id="tab_equipable" class="tab_equipable"> 
                                    <!-- DEBUT LISTE DES ITEMS -->

                                    <div id="list_items" class="list_items">

                                        <div class="titre_menu">&nbsp;</div>

                                        <div class="liste_equipable" id="liste_equipable">
                                            <?php
                                            $db->query($req_equipable);
                                            $nb_equipable = $db->nf();

                                            $nb_ligne = 0;
                                            $nb_col   = 3;

                                            echo "<table  border=\"2\" cellspacing=\"0\" cellpadding=\"3\" bordercolor=\"black\">";
                                            echo "<tr>";
                                            echo "<td>";
                                            echo "<div class=\"container2\">";
                                            echo "<a href=\"#\" id=\"popup_runes\" rel=\"popup_runes.php\" title=\"Mes Runes\"><img style=\"width:70px; height:70px; margin:-2px;\" src=\"./images/bourse_runes.png\" title=\"Mes Runes\" alt=\"bourse_rune\" /></a>";
                                            echo "</div>";
                                            echo "</td>";
                                            echo "<td>";
                                            echo "<div class=\"container2\">";
                                            echo "<a href=\"#\" id=\"popup_quete\" rel=\"popup_quete.php\" title=\"Mes Objets de Quete\"><img style=\"width:70px; height:70px; margin:-2px;\" src=\"./images/objets_quete.png\" title=\"Mes Objets de Quete\" alt=\"objets_quete\" /></a>";
                                            echo "</div>";
                                            echo "</td>";
                                            echo "<td>";
                                            echo "<div class=\"container2\">";
                                            echo "<a href=\"#\" id=\"popup_composant\" rel=\"popup_composant.php\" title=\"Mes Composants de Potion\"><img style=\"width:70px; height:70px; margin:-2px;\" src=\"./images/composants_potions.png\" title=\"Mes Composants de Potion\" alt=\"composants_potions\" /></a>";
                                            echo "</div>";
                                            echo "</td>";
                                            while ($db->next_record())
                                            {
                                                $seuil_for      = $db->f("obj_seuil_force");
                                                $seuil_dex      = $db->f("obj_seuil_dex");
                                                $image          = G_IMAGES . $Recup_image;
                                                $comp           = $db->f("gobj_comp_cod");
                                                $libelle_comp   = $db->f("comp_libelle");
                                                $etat_max       = $db->f("obj_etat_max");
                                                $objet_cod      = $db->f("obj_cod");
                                                $tobj_cod       = $db->f("tobj_cod");
                                                $gobj_cod       = $db->f("gobj_cod");
                                                $perobj_cod     = $db->f("perobj_cod");
                                                $libelle_type   = $db->f("tobj_libelle");
                                                $pa             = $db->f("gobj_pa_normal");
                                                $af             = $db->f("gobj_pa_eclair");
                                                $equip_distance = $db->f("gobj_distance");
                                                $bool_distance  = 0;
                                                if ($equip_distance == 'O')
                                                    $bool_distance  = 1;

                                                $id_object = $db->f("perobj_obj_cod");
                                                $identifie = $db->f("perobj_identifie");

                                                $equipe = $db->f("perobj_equipe");

                                                $bool_equipe = 0;

                                                if ($nb_obj_equipable[$tobj_cod] < $type[$tobj_cod])
                                                    $bool_equipe = -1;

                                                if ($equipe == 'O')
                                                    $bool_equipe = 1;

                                                $deposable      = $db->f("gobj_deposable");
                                                $bool_deposable = 0;
                                                if ($deposable == 'N')
                                                    $bool_deposable = 1;

                                                $aura_feu              = $db->f("obj_aura_feu");
                                                $regen                 = $db->f("obj_regen");
                                                $poison                = $db->f("obj_poison");
                                                $enchantable           = $db->f("obj_enchantable");
                                                $Recup_image           = $db->f("gobj_image");
                                                $Recup_image_generique = $db->f("gobj_image_generique");
                                                $url                   = $db->f("gobj_url");
                                                $chute                 = $db->f("obj_chute");
                                                $des                   = $db->f("obj_des_degats");
                                                $val_des               = $db->f("obj_val_des_degats");
                                                $bonus                 = $db->f("obj_bonus_degats");

                                                $image             = '../images/' . $Recup_image . '';
                                                $etat              = $db->f("obj_etat");
                                                $comp              = $db->f("gobj_comp_cod");
                                                $type_obj          = $db->f("gobj_tobj_cod");
                                                $nb_type_equipable = $db->f("tobj_max_equip");

                                                $desc      = htmlspecialchars($db->f("obj_description"));
                                                $nom       = $db->f("obj_nom");
                                                $poids     = $db->f("obj_poids");
                                                $armure    = $db->f("obj_armure");
                                                $nom_objet = $db->f("obj_nom_generique");

                                                //  Modificateur de vue :
                                                $bonus_vue  = $db->f("obj_bonus_vue");
                                                // Protection contre les critiques/spéciaux :
                                                $critique   = $db->f("obj_critique");
                                                // Vampirisme :
                                                $vampirisme = $db->f("obj_vampire");

                                                if ($nb_col == $max_par_ligne_equipable)
                                                {
                                                    $nb_col = 0;
                                                    $nb_ligne ++;

                                                    echo "</tr>";
                                                    echo "<tr>";
                                                }
                                                $nb_col++;

                                                if ($equipe == 'O')
                                                    echo "<td class=\"selected_item\">";
                                                else
                                                    echo "<td>";

                                                echo "<div class=\"container2\">";

                                                if ($identifie == 'O')
                                                {
                                                    if ($Recup_image == "")
                                                        $Recup_image = $Recup_image_generique;
                                                    //$Recup_image = "croix_verte.png";

                                                    $class_usure = get_class_usure($etat, $type_obj, $equipe);
                                                    //if ( ($type_obj == 1) or ($type_obj == 2) or ($type_obj == 4) )
                                                    $retour      = get_infos_equipement($type_obj, "img_item_usure", G_IMAGES, $Recup_image, $id_object, $nom, $poids, $desc, $etat, $pa, $armure, $bool_distance, $chute, $af, $des, $val_des, $bonus, $seuil_dex, $seuil_for, $libelle_comp, $bool_equipe, $bonus_vue, $critique, $vampirisme, $aura_de_feu, $regen, $poison, $enchantable, $bool_deposable, $perso_pa, $dexterite, $force, $url);

                                                    /* else 
                                                      $retour = get_infos_equipement($type_obj, "img_item", G_IMAGES, $Recup_image, $id_object, $nom, $poids, $desc, $etat, $pa, $armure, $bool_distance,
                                                      $chute, $af, $des, $val_des, $bonus, $seuil_dex, $seuil_for, $libelle_comp,
                                                      $bool_equipe, $bonus_vue, $critique, $vampirisme, $aura_de_feu, $regen, $poison,
                                                      $enchantable, $bool_deposable, $perso_pa, $dexterite, $force, $url);
                                                     */
                                                    echo $class_usure;
                                                    echo $retour;
                                                }
                                                else
                                                {
                                                    $img_ombre = $Recup_image_generique;
                                                    echo "<img class=\"img_item\" src=\"" . G_IMAGES . $img_ombre . "\" ";
                                                    echo "title=\"" . $nom_objet . "\" ";
                                                    echo "poids=\"" . $poids . "\" ";
                                                    echo "num=\"" . $id_object . "\" ";
                                                    if (($bool_equipe != 1) && ($perso_pa >= 1))
                                                        echo "abandonne=\"1\" ";
                                                    if ($perso_pa >= 2)
                                                        echo "identifier=\"1\" ";
                                                    echo " />";
                                                }

                                                echo "</div>";
                                                echo "</td>";
                                            }


                                            if ($nb_ligne >= $min_ligne_equipable)
                                                while ($nb_col < $min_par_ligne_equipable)
                                                {
                                                    echo "<td>";
                                                    echo "<div class=\"container2\"></div>";
                                                    echo "</td>";
                                                    $nb_col ++;
                                                }
                                            else
                                            {
                                                while ($nb_col < $min_par_ligne_equipable)
                                                {
                                                    echo "<td>";
                                                    echo "<div class=\"container2\"></div>";
                                                    echo "</td>";
                                                    $nb_col ++;
                                                }
                                                echo "</tr><tr>";
                                                $nb_ligne ++;

                                                while ($nb_ligne < $min_ligne_equipable)
                                                {
                                                    echo "</tr><tr>";
                                                    echo "<td>";
                                                    echo "<div class=\"container2\"></div>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class=\"container2\"></div>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class=\"container2\"></div>";
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo "<div class=\"container2\"></div>";
                                                    echo "</td>";
                                                    $nb_ligne ++;
                                                }
                                            }

                                            echo "</tr>";
                                            echo "</table>";
                                            ?>
                                        </div>

                                    </div>
                                    <!-- FIN LISTE DES ITEMS -->
                                    &nbsp;
                                </div>			
                                <!-- FIN PANNEAU Objets Equipables-->


                                <!-- fiche du personnage -->
                                <div id ="fiche_perso" class="fiche_perso">

                                    <div class="clear">&nbsp;</div>
                                    <div class="clear"><div class="titre_fiche_perso2">Force : </div><div class="info_fiche_perso_gras2"><?php echo $force; ?></div></div>
                                    <div class="clear"><div class="titre_fiche_perso2">Intelligence : </div><div class="info_fiche_perso_gras2"><?php echo $intelligence; ?></div></div>
                                    <div class="clear"><div class="titre_fiche_perso2">Dextérité : </div><div class="info_fiche_perso_gras2"><?php echo $dexterite; ?></div></div>
                                    <div class="clear"><div class="titre_fiche_perso2">Constitution : </div><div class="info_fiche_perso_gras2"><?php echo $constitution; ?></div></div>

                                    <div class="tresor">
                                        <img class="pos_tresor" title="tresor" src="images/tresor.png" /> 
                                    </div>

                                    <div class="clear"><div class="titre_fiche_perso">Dégats (+ amélioration) : </div><div class="info_fiche_perso2"><?php echo $le_nb_des . "D" . $le_val_des . "+" . $le_bonus . " (+" . $amelioration_arme . ")"; ?></div></div>
                                    <div class="clear"><div class="titre_fiche_perso">Armure (+ amélioration) : </div><div class="info_fiche_perso2"><?php echo $la_armure . " (+" . $amel_armure . ")"; ?></div></div>
                                    <div class="clear"><div class="titre_fiche_perso">Encombrement : </div><div class="info_fiche_perso2"><?php if ($poids_porte > $poids_total) echo "<b>" . $poids_porte . "</b>/" . $poids_total;
                                            else echo $poids_porte . "/" . $poids_total; ?></div></div>
                                    <div class="clear"><div class="titre_fiche_perso">Brouzoufs : </div><div class="info_fiche_perso2"><?php echo $po; ?> (<?php echo $qte_or; ?> en banque) </div></div>
                                    <div class="clear">
                                        <div class="titre_fiche_perso">&nbsp;</div>
                                        <div class="info_fiche_perso2">
                                            <div class="action_deposer_brouzouf_vide" id="action_deposer_brouzouf_vide">
                                            <?php
                                            if (($perso_pa >= 1) && ($po > 0))
                                                echo "<a href=\"#\" class=\"rien\"><img class=\"action_deposer_brouzouf\" style=\"display:block; margin:-2px;\" class=\"pointeur\" src=\"images/deposer_brouzouf50.png\" title=\"Déposer des brouzoufs\" /></a>";
                                            ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear">
                                        <div class="depot_or" id="depot_or">
                                            <div class="clear"><div class="champ_depot_or">Déposer <input type="text" name="quantite" class="nb_grand"/> !</div></div>
                                            <div class="clear"><div class="champ_depot_or"><input type="submit" class="bouton" value="Déposer"></div></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- Fin Partie Gauche -->

                            <div class="clear" ></div>
                            <div class="actions">
                                <div class="action_abandonner_vide"><img class="action_abandonner" style="display:none;"  src="images/abandonner50.png" title="Abandonner" /></div>
                                <div class="action_reparer_vide"><img class="action_reparer" style="display:none;" class="pointeur" src="images/reparer50.png" title="Réparer" /></div>
                                <div class="action_identifier_vide"><img class="action_identifier" style="display:none;" class="pointeur" src="images/identifier50.png" title="Identifier" /></div>
                                <div class="action_equiper_vide"><img class="action_equiper" style="display:none;" class="pointeur" src="images/equiper50.png" title="Equiper" /></div>
                                <div class="action_desequiper_vide"><img class="action_desequiper" style="display:none;" class="pointeur" src="images/desequiper50.png" title="Remettre dans l'inventaire" /></div>
                            </div>


                            <div class="clear"> </div>					
                            <!-- DEBUT PANNEAU Description Objet -->
                            <div id="desc_panel" class="desc_panel">

                                <!-- DEBUT FICHE ITEM -->
                                <div id="fiche_item" class="fiche_item">
                                    <div id="resultat_action" style="display:block;">
<?php
/* * ************************************ */
/* * ***** Début Gestion Action Popup ****** */
/* * ************************************ */

/* Variables des formulaires proventant des popup */
$idObjet           = isset($_POST['idObjet']) ? $_POST['idObjet'] : -1;
$typeObjet         = isset($_POST['typeObjet']) ? $_POST['typeObjet'] : "";
$nomObjetGenerique = isset($_POST['nomObjetGenerique']) ? $_POST['nomObjetGenerique'] : "";
$nomObjet          = isset($_POST['nomObjet']) ? $_POST['nomObjet'] : "";
$nbObjet           = (isset($_POST['nbObjet']) && $_POST['nbObjet'] <> "") ? $_POST['nbObjet'] : 1;
$methode_divers    = isset($_POST['methode_divers']) ? $_POST['methode_divers'] : "";

if (( $idObjet != -1) && ($methode_divers == "abandonner"))
{
    /*
      echo "idObjet: ".$idObjet."<br/>";
      echo "typeObjet: ".$typeObjet."<br/>";
      echo "nomObjetGenerique: ".$nomObjetGenerique."<br/>";
      echo "nomObjet: ".$nomObjet."<br/>";
      echo "nbObjet: ".$nbObjet."<br/>";
      echo "methode_divers: ".$methode_divers."<br/>";
     */

    $nbObjetASupprimer = $nbObjet;
    $list_idObjet      = array();
    $list_idObjet[0]   = $idObjet;

    if ($nbObjet > 1)
    {
        $req_objet_popup = "select perobj_obj_cod ";
        $req_objet_popup = $req_objet_popup . "from perso_objets, objets, objet_generique, type_objet ";
        $req_objet_popup = $req_objet_popup . "where perobj_perso_cod = " . $perso_cod . " ";
        $req_objet_popup = $req_objet_popup . "and tobj_cod = " . $typeObjet . " ";
        $req_objet_popup = $req_objet_popup . "and perobj_obj_cod = obj_cod ";
        $req_objet_popup = $req_objet_popup . "and obj_gobj_cod = gobj_cod ";
        $req_objet_popup = $req_objet_popup . "and gobj_tobj_cod = tobj_cod ";
        $req_objet_popup = $req_objet_popup . "and obj_nom = '" . addslashes($nomObjet) . "' ";
        $req_objet_popup = $req_objet_popup . "and obj_nom_generique = '" . addslashes($nomObjetGenerique) . "' ";
        //echo $req_objet_popup."<br/>";

        $i              = 0;
        $db->query($req_objet_popup);
        $nbObjetPossede = $db->nf();

        $nbObjetASupprimer = min($nbObjetPossede, $nbObjet);

        while ($db->next_record())
        {
            $idObjet          = $db->f("perobj_obj_cod");
            $list_idObjet[$i] = $idObjet;
            $i++;
        }
    }

    for ($i = 1; $i <= $nbObjetASupprimer; $i++)
    {
        $req = 'select depose_objet(' . $perso_cod . ',' . $list_idObjet[$i - 1] . ') as resultat ';
        //echo $req."<br/>";
        $db->query($req);
        $db->next_record();

        if ($i == 1)
        {
            echo "<div class=\"resultat_formulaire\">";
            echo "<p>Vous tentez d'abandonner <b>" . $nbObjet . "</b> " . $nomObjet . "</p>";
        }
        echo $i . ": " . $db->f('resultat');
        if ($i == $nbObjetASupprimer)
            echo "</div>";
    }
}
/* * ********************************** */
/* * ***** Fin Gestion Action Popup ******* */
/* * ********************************** */

echo $resultat_form;
?>
                                    </div>

                                    <div id="id_nom_item"><div class="titre_nom_item" ></div><div class="nom_item" ></div></div>
                                    <div id="id_desc_item"><div class="titre_desc_item" ></div><div class="desc_item" ></div></div>
                                    <div id="id_competence_item"><div class="titre_competence_item" ></div><div class="competence_item" ></div></div>
                                    <div id="id_poids_item"><div class="titre_poids_item" ></div><div class="poids_item" ></div></div>

                                    <div id="id_etat_item"><div class="titre_etat_item" ></div><div class="etat_item" ></div></div>
                                    <div id="id_pa_item"><div class="titre_pa_item" ></div><div class="pa_item" ></div></div>
                                    <div id="id_af_item"><div class="titre_af_item" ></div><div class="af_item" ></div></div>
                                    <div id="id_degats_item"><div class="titre_degats_item" ></div><div class="degats_item" ></div></div>
                                    <div id="id_force_item"><div class="titre_force_item" ></div><div class="force_item" ></div></div>
                                    <div id="id_dexterite_item"><div class="titre_dexterite_item" ></div><div class="dexterite_item" ></div></div>
                                    <div id="id_chute_item"><div class="titre_chute_item" ></div><div class="chute_item" ></div></div>
                                    <div id="id_armure_item"><div class="titre_armure_item" ></div><div class="armure_item" ></div></div>
                                    <div id="id_critique_item"><div class="titre_critique_item" ></div><div class="critique_item" ></div></div>

                                    <div id="id_bonus_vue_item"><div class="titre_bonus_vue_item" ></div><div class="bonus_vue_item" ></div></div>
                                    <div id="id_vampirisme_item"><div class="titre_vampirisme_item" ></div><div class="vampirisme_item" ></div></div>
                                    <div id="id_aura_de_feu_item"><div class="titre_aura_de_feu_item" ></div><div class="aura_de_feu_item" ></div></div>
                                    <div id="id_regen_item"><div class="titre_regen_item" ></div><div class="regen_item" ></div></div>
                                    <div id="id_poison_item"><div class="titre_poison_item" ></div><div class="poison_item" ></div></div>
                                    <div id="id_url_item"><div class="titre_url_item" ></div><div class="url_item" ><a id="link_url" rel=" " title="Voir le Détail"></a></div></div>

                                    <br/><br/>
                                    <div id="id_enchantable_item"><div class="titre_enchantable_item" ></div></div>
                                    <div id="id_deposable_item"><div class="titre_deposable_item" ></div></div>

                                    <br/>
                                    <!-- Pour chaque ajout, ne pas oublier les css coreespondant -->
                                    <!-- <div class="clear" id="id_XXX"><div class="titre_XXX" ></div><div class="XXX" ></div></div> -->
                                </div>
                                <!-- FIN FICHE ITEM -->


                                <!-- Debut Tableau objets non equipables -->
                                <div class="objets_panel_right">
                                    <div class="objets_panel">&nbsp;
                                        <div class="titre_objets_panel">&nbsp;</div>

                                        <?php
                                        $db->query($req_inequipable);
                                        $nb_inequipable = $db->nf();

                                        $nb_ligne = 0;
                                        $nb_col   = 0;

                                        echo "<table  border=\"2\" cellspacing=\"0\" cellpadding=\"3\" bordercolor=\"black\">";
                                        echo "<tr>";
                                        while ($db->next_record())
                                        {
                                            $id_object = $db->f("perobj_obj_cod");

                                            $identifie = $db->f("perobj_identifie");

                                            /*
                                              $equipe = $db->f("perobj_equipe");
                                             */
                                            $bool_equipe    = 0;
                                            $deposable      = $db->f("gobj_deposable");
                                            $bool_deposable = 0;
                                            if ($deposable == 'N')
                                                $bool_deposable = 1;

                                            $aura_feu              = $db->f("obj_aura_feu");
                                            $regen                 = $db->f("obj_regen");
                                            $poison                = $db->f("obj_poison");
                                            $enchantable           = $db->f("obj_enchantable");
                                            $Recup_image           = $db->f("gobj_image");
                                            $Recup_image_generique = $db->f("gobj_image_generique");
                                            $url                   = $db->f("gobj_url");

                                            /*
                                              $chute = $db->f("obj_chute");
                                              $des = $db->f("obj_des_degats");
                                              $val_des = $db->f("obj_val_des_degats");
                                              $bonus = $db->f("obj_bonus_degats");
                                              $armure = $db->f("obj_armure");
                                             */

                                            $image    = '../images/' . $Recup_image . '';
                                            $etat     = $db->f("obj_etat");
                                            $comp     = $db->f("gobj_comp_cod");
                                            $type_obj = $db->f("gobj_tobj_cod");
                                            // $nb_type_equipable = $db->f("tobj_max_equip");

                                            $desc      = htmlspecialchars($db->f("obj_description"));
                                            $nom       = $db->f("obj_nom");
                                            $poids     = $db->f("obj_poids");
                                            $nom_objet = $db->f("obj_nom_generique");

                                            //  Modificateur de vue :
                                            $bonus_vue  = $db->f("obj_bonus_vue");
                                            // Protection contre les critiques/spéciaux :
                                            $critique   = $db->f("obj_critique");
                                            // Vampirisme :
                                            $vampirisme = $db->f("obj_vampire");

                                            if ($nb_col == $max_par_ligne_inequipable)
                                            {
                                                $nb_col = 0;
                                                $nb_ligne ++;

                                                echo "</tr>";
                                                echo "<tr>";
                                            }
                                            $nb_col++;

                                            echo "<td>";

                                            echo "<div class=\"container_item\">";

                                            if ($identifie == 'O')
                                            {
                                                if ($Recup_image == "")
                                                    $Recup_image = $Recup_image_generique;

                                                $retour = get_infos_objet("desc_tab_item", G_IMAGES, $Recup_image, $id_object, $nom, $poids, $desc, $etat, $bool_equipe, $bonus_vue, $critique, $vampirisme, $aura_de_feu, $regen, $poison, $enchantable, $bool_deposable, $perso_pa, $dex, $force, $url);

                                                echo $retour;
                                            }
                                            else
                                            {
                                                $img_ombre = $Recup_image_generique;

                                                echo "<img class=\"desc_tab_item\" src=\"" . G_IMAGES . $img_ombre . "\" ";
                                                echo "title=\"" . $nom_objet . "\" ";
                                                echo "poids=\"" . $poids . "\" ";
                                                echo "num=\"" . $id_object . "\" ";
                                                if (($bool_equipe != 1) && ($perso_pa >= 1))
                                                    echo "abandonne=\"1\" ";
                                                if ($perso_pa >= 2)
                                                    echo "identifier=\"1\" ";
                                                echo " />";
                                                if ($perso_pa >= 2)
                                                    echo "<img class=\"petit_identifier\" src=\"images/identifier10.png\" />";
                                                else
                                                    echo "<img class=\"petit_identifier\" src=\"images/desac_identifier10.png\" />";
                                            }

                                            echo "</div>";
                                            echo "</td>";
                                        }


                                        if ($nb_ligne >= $min_ligne_inequipable)
                                            while ($nb_col < $min_par_ligne_inequipable)
                                            {
                                                echo "<td>";
                                                echo "<div class=\"img_item_vide\"></div>";
                                                echo "</td>";
                                                $nb_col ++;
                                            }
                                        else
                                        {
                                            while ($nb_col < $min_par_ligne_inequipable)
                                            {
                                                echo "<td>";
                                                echo "<div class=\"img_item_vide\"></div>";
                                                echo "</td>";
                                                $nb_col ++;
                                            }
                                            echo "</tr><tr>";
                                            $nb_ligne ++;

                                            while ($nb_ligne < $min_ligne_inequipable)
                                            {
                                                echo "</tr><tr>";
                                                echo "<td>";
                                                echo "<div class=\"img_item_vide\"></div>";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<div class=\"img_item_vide\"></div>";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<div class=\"img_item_vide\"></div>";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<div class=\"img_item_vide\"></div>";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<div class=\"img_item_vide\"></div>";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<div class=\"img_item_vide\"></div>";
                                                echo "</td>";
                                                $nb_ligne ++;
                                            }
                                        }

                                        echo "</tr>";
                                        echo "</table>";
                                        ?>
                                    </div>
                                </div>
                                <!-- Fin Tableau objets non equipables -->

                            </div>
                            <!-- FIN PANNEAU Description Objet -->


                            <div class="clear"></div>
                        </form>
                    </div>
                </div>
            </div>	
        </div>
        <div class="barrL">
            <div class="barrR">
                <div class="barrC"> </div>
            </div>
        </div>


        <p class="clear"></p>


        <!-- DEBUT PIED DE PAGE -->
        <!--
        <div id="footer" class="footer">
                <div class="infos_bas_page">Date et heure serveur :  </div>
                <div class="copyright">© Delain</div>
                <br/>
        </div>
        -->
        <!-- FIN PIED DE PAGE -->
    </div>
    <!-- FIN CONTAINER -->
                                        <?php
                                        $contenu_page = ob_get_contents();
                                        ob_end_clean();
                                        $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
                                        $t->parse('Sortie', 'FileRef');
                                        $t->p('Sortie');
                                        ?>	

