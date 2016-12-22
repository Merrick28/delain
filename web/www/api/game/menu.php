<?php

define('NOGOOGLE', 1);

if (!function_exists('apc_fetch'))
{
    function apc_fetch($arg)
    {
        switch ($arg)
        {
            case 'g_url':
                return G_URL;
            case 'g_che':
                return G_CHE;
            case 'g_images':
                return G_IMAGES;
            case 'nom_cook':
                return NOM_COOK;
            case 'img_path':
                return IMG_PATH;
        }
        return '';
    }
}

$type_auth = $_REQUEST['type_auth'];
$cle_connect = $_REQUEST['cle_connect'];
$compt_cod = $_REQUEST['compt_cod'];
$ext_perso_cod = $_REQUEST['ext_perso_cod'];
$typesort = $_REQUEST['typesort'];

require_once G_CHE . "jeu_test/verif_connexion.php";

$pa_dep = $db->get_pa_dep($perso_cod);

// on commence à aller chercher les infos
$is_intangible = $db->is_intangible($perso_cod);
$intangible = 0;
if ($is_intangible)
{
    $intangible = 1;
}


$is_enlumineur = $db->is_enlumineur($perso_cod);
$enlumineur = 0;
if ($is_enlumineur)
{
    $enlumineur = 1;
}


$is_refuge = $db->is_refuge($perso_cod);
$refuge = 0;
if ($is_refuge)
{
    $refuge = 1;
}


$is_milice = $db->is_milice($perso_cod);
$milice = 0;
if ($is_milice)
{
    $milice = 1;
}


$req = "select mger_perso_cod from magasin_gerant where mger_perso_cod = $perso_cod ";
$db->query($req);
$gerant = 'N';
if ($db->nf() != 0)
{
    $gerant = 'O';
}

$req = "select dper_niveau from dieu_perso where dper_perso_cod = $perso_cod and dper_niveau > 3";
$db->query($req);
$admin_dieu = 'N';
if ($db->nf() != 0)
{
    $admin_dieu = 'O';
}

$req = "select tfid_perso_cod from temple_fidele where tfid_perso_cod = $perso_cod ";
$db->query($req);
$fidele_gerant = 'N';
if ($db->nf() != 0)
{
    $fidele_gerant = 'O';
}


$req = "select perso_pa,perso_nom,perso_niveau_vampire,perso_admin_echoppe,perso_admin_echoppe_noir,perso_type_perso,perso_pv,perso_pv_max,
	floor(perso_px) as perso_px,limite_niveau($perso_cod) as limite_niveau,perso_energie,f_armure_perso($perso_cod) as armure_perso,degats_perso($perso_cod) as degats_perso, perso_gmon_cod,
	limite_niveau_actuel($perso_cod) as limite
	from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();

$pa = $db->f("perso_pa");
$nom_perso = $db->f("perso_nom");
$energie = $db->f("perso_energie");
$armure_perso = $db->f("armure_perso");
$degats_perso = $db->f("degats_perso");
$det_deg = explode(";", $degats_perso);
$px_actuel = $db->f("perso_px");
$px_limite = $db->f("limite_niveau");
$niveau_xp = ($db->f("perso_px") - $db->f("limite"));
$div_xp = ($db->f("limite_niveau") - $db->f("limite"));
$niveau_xp = $niveau_xp / $div_xp;
$pv = $db->f("perso_pv");
$px = $db->f("perso_px");
$pv_max = $db->f("perso_pv_max");
$admin_echoppe = $db->f("perso_admin_echoppe");
$admin_echoppe_noir = $db->f("perso_admin_echoppe_noir");
$is_vampire = $db->f("perso_niveau_vampire");
if ($db->f("perso_pv_max") == 0)
{
    $hp = 0;
}
else
{
    $hp = $db->f("perso_pv") / $db->f("perso_pv_max");
}
$is_fam = 0;
if ($db->f("perso_type_perso") == 3)
{
    $is_fam = 1;
}


// énergie du familier divin
$is_fam_divin = 0;
$energie_divine = -1;
$barre_divine = -1;
if ($db->f("perso_gmon_cod") == 441)
{
    $is_fam_divin = 1;
    $db_divin = new base_delain;
    $req = "select dper_points from dieu_perso where dper_perso_cod = $perso_cod";
    $db_divin->query($req);
    $db_divin->next_record();
    $energie_divine = $db_divin->f("dper_points");
    $barre_divine = floor(($energie_divine / 200) * 10) * 10;
    if ($barre_divine >= 100)
    {
        $barre_divine = 100;
    }
}

$passage_niveau = 0;
if ($px_actuel >= $px_limite)
{
    $passage_niveau = 1;
}


$barre_hp = '0';
if ($hp >= 0.1)
{
    $barre_hp = 10;
}
if ($hp >= 0.2)
{
    $barre_hp = 20;
}
if ($hp >= 0.3)
{
    $barre_hp = 30;
}
if ($hp >= 0.4)
{
    $barre_hp = 40;
}
if ($hp >= 0.5)
{
    $barre_hp = 50;
}
if ($hp >= 0.6)
{
    $barre_hp = 60;
}
if ($hp >= 0.7)
{
    $barre_hp = 70;
}
if ($hp >= 0.8)
{
    $barre_hp = 80;
}
if ($hp >= 0.9)
{
    $barre_hp = 90;
}
if ($hp == 1)
{
    $barre_hp = 100;
}

$barre_xp = '0';
if ($db->f("perso_px") - $db->f("limite") < 0)
{
    $barre_xp = 'negative';
}
if ($niveau_xp >= 0.1)
{
    $barre_xp = '10';
}
if ($niveau_xp >= 0.2)
{
    $barre_xp = '20';
}
if ($niveau_xp >= 0.3)
{
    $barre_xp = '30';
}
if ($niveau_xp >= 0.4)
{
    $barre_xp = '40';
}
if ($niveau_xp >= 0.5)
{
    $barre_xp = '50';
}
if ($niveau_xp >= 0.6)
{
    $barre_xp = '60';
}
if ($niveau_xp >= 0.7)
{
    $barre_xp = '70';
}
if ($niveau_xp >= 0.8)
{
    $barre_xp = '80';
}
if ($niveau_xp >= 0.9)
{
    $barre_xp = '90';
}
if ($niveau_xp >= 1)
{
    $barre_xp = '100';
}

// position
$tab = $db->get_pos($perso_cod);
$position_x = $tab['x'];
$position_y = $tab['y'];
$position_etage = $tab['etage_libelle'];

// enchanteur
$enchanteur = 0;
$barre_energie = 0;
$perso_energie = 0;
$is_enchanteur = $db->is_enchanteur($perso_cod);
if ($is_enchanteur)
{
    $enchanteur = 1;
    $hp = 0;
    $hp = $energie / 100;
    $barre_energie = '0';
    if ($hp >= 0.1)
    {
        $barre_energie = 10;
    }
    if ($hp >= 0.2)
    {
        $barre_energie = 20;
    }
    if ($hp >= 0.3)
    {
        $barre_energie = 30;
    }
    if ($hp >= 0.4)
    {
        $barre_energie = 40;
    }
    if ($hp >= 0.5)
    {
        $barre_energie = 50;
    }
    if ($hp >= 0.6)
    {
        $barre_energie = 60;
    }
    if ($hp >= 0.7)
    {
        $barre_energie = 70;
    }
    if ($hp >= 0.8)
    {
        $barre_energie = 80;
    }
    if ($hp >= 0.9)
    {
        $barre_energie = 90;
    }
    if ($hp == 1)
    {
        $barre_energie = 100;
    }
}

$is_perso_quete = $db->is_perso_quete($perso_cod); /* Fonction qui se trouve dans classe.php */

$quete = 0;
if ($is_perso_quete)
{
    $quete = 1;
}


// lieux
$lieu = 0;
$nom_lieu = 0;
$libelle = 0;
$is_lieu = $db->is_lieu($perso_cod);
if ($is_lieu)
{
    $lieu = 1;
    $tab_lieu = $db->get_lieu($perso_cod);
    if ($tab_lieu['url'] != null)
    {
        $nom_lieu = $tab_lieu['nom'];
        $libelle = (!empty($tab_lieu['evo_libelle'])) ? $tab_lieu['evo_libelle'] : $tab_lieu['libelle'];
    }
}

// messagerie
$req_msg = "select count(*) as nombre from messages_dest where dmsg_perso_cod = $perso_cod 
	and dmsg_lu = 'N' and dmsg_archive = 'N' ";
$db->query($req_msg);
$db->next_record();
$nb_mess = $db->f('nombre');

// locké
$is_locked = $db->is_locked($perso_cod);
$locke = 0;
if ($is_locked)
{
    $locke = 1;
}


// ramasser
$objet_case = 0;
$pa_ramasse = 0;
$param = new parametres();
if (($db->nb_obj_sur_case($perso_cod) != 0) || ($db->nb_or_sur_case($perso_cod)))
{
    $objet_case = 1;
    if ($is_intangible)
    {
        $pa_ramasse = $param->getparm(42);
    }
    else
    {
        $pa_ramasse = $param->getparm(41);
    }
}
$req_tran = "select * from transaction where (tran_vendeur = $perso_cod or tran_acheteur = $perso_cod)";
$db->query($req_tran);
$transaction = $db->nf();

// potions
$req2 = "select pcomp_pcomp_cod from perso_competences where pcomp_pcomp_cod in (97,100,101) and pcomp_perso_cod = $perso_cod";
$db->query($req2);
$potions = 0;
if ($db->nf() != 0)
{
    $potions = 1;
}


// religion
$req = "select dper_dieu_cod,dper_niveau from dieu_perso where dper_perso_cod = $perso_cod and dper_niveau >= 2 ";
$db->query($req);
$religion = 0;
if ($db->nf() != 0)
{
    $religion = 1;
}

$data = array(
    array('name' => 'nom', 'valeur' => $perso_nom),
    array('name' => 'pa', 'valeur' => $pa),
    array('name' => 'is_fam', 'valeur' => $is_fam),
    array('name' => 'barre_hp', 'valeur' => $barre_hp),
    array('name' => 'pv', 'valeur' => $pv),
    array('name' => 'pv_max', 'valeur' => $pv_max),
    array('name' => 'barre_xp', 'valeur' => $barre_xp),
    array('name' => 'perso_px', 'valeur' => $px),
    array('name' => 'armure', 'valeur' => $armure_perso),
    array('name' => 'degats', 'valeur' => $det_deg[0] . '-' . $det_deg[1]),
    array('name' => 'etage', 'valeur' => $position_etage),
    array('name' => 'posx', 'valeur' => $position_x),
    array('name' => 'posy', 'valeur' => $position_y),
    array('name' => 'passage_niveau', 'valeur' => $passage_niveau),
    array('name' => 'enchanteur', 'valeur' => $enchanteur),
    array('name' => 'barre_energie', 'valeur' => $barre_energie),
    array('name' => 'perso_energie', 'valeur' => $energie),
    array('name' => 'quete', 'valeur' => $quete),
    array('name' => 'intangible', 'valeur' => $intangible),
    array('name' => 'lieu', 'valeur' => $lieu),
    array('name' => 'nom_lieu', 'valeur' => str_replace("&", "&amp;", $nom_lieu)),
    array('name' => 'desc_lieu', 'valeur' => $libelle),
    array('name' => 'nb_mess', 'valeur' => $nb_mess),
    array('name' => 'lock', 'valeur' => $locke),
    array('name' => 'pa_dep', 'valeur' => $pa_dep),
    array('name' => 'pa_ramasse', 'valeur' => $pa_ramasse),
    array('name' => 'objet_case', 'valeur' => $objet_case),
    array('name' => 'transaction', 'valeur' => $transaction),
    array('name' => 'prochain_niveau', 'valeur' => $px_limite),
    array('name' => 'refuge', 'valeur' => $refuge),
    array('name' => 'milice', 'valeur' => $milice),
    array('name' => 'fidele_gerant', 'valeur' => $fidele_gerant),
    array('name' => 'gerant', 'valeur' => $gerant),
    array('name' => 'admin_echoppe', 'valeur' => $admin_echoppe),
    array('name' => 'admin_echoppe_noir', 'valeur' => $admin_echoppe_noir),
    array('name' => 'is_vampire', 'valeur' => $is_vampire),
    array('name' => 'religion', 'valeur' => $religion),
    array('name' => 'admin_dieu', 'valeur' => $admin_dieu),
    array('name' => 'potions', 'valeur' => $potions),
    array('name' => 'enlumineur', 'valeur' => $enlumineur),
    array('name' => 'is_fam_divin', 'valeur' => $is_fam_divin),
    array('name' => 'energie_divine', 'valeur' => $energie_divine),
    array('name' => 'barre_divine', 'valeur' => $barre_divine)
);
// à partir d'ici, on a toutes les infos de base
// on peut partir à la recherche de quelques infos particulières, qui ne seront pas systématiquement affichées
// on passe au template 
require G_CHE . "/../includes/Smarty/Smarty.class.php";
$smarty = new Smarty();
$smarty->assign('data', $data);
$smarty->assign('type', "perso");

$smarty->template_dir = G_CHE . '/../api/game/template';
$smarty->compile_dir = G_CHE . '/../api/compile';
$smarty->cache_dir = G_CHE . '/../api/cache';
$smarty->assign('variable', '<b>Contenu de ma variable</b>');
if (!isset($typesort))
{
    $typesort = 'xml';
}
switch ($typesort)
{
    case 'xml':
        header('Content-Type: text/xml', true);
        $smarty->display('gen_xml.tpl');
        break;
    case 'json':
        $smarty->display('gen_json.tpl');
        break;
    default:
        $smarty->display('gen_json.tpl');
        break;
}
