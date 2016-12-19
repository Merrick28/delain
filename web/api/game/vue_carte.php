<?php
//require '/home/delain/public_html/www/includes/delain_header.php';
define('NOGOOGLE',1);
//require_once G_CHE . "jeu/verif_connexion.php";
include 'classes.php';
// on commence à aller chercher les infos
$req = "select * from vue_perso6(50)";
$db->query($req);
$i = 0;
$data = array('case_x' => 'x',
				'x' => array('position' => 'x',
						'x' => 'x')
						);
while($db->next_record())
{
	$data_temp = array('case_' . $db->f('tvue_num') => $db->f('tvue_num'),
				$db->f('tvue_num') => array('position' => $db->f('t_pos_cod'),
						'x' => $db->f('t_x'))
						);
	$data = array_merge($data_temp,$data);
	/*print_r($data);
	echo '<hr>';
	print_r($data_temp);*/
}

/*
$data = array('Contacts',
    array('fax' => '555-222-9876',
          'email' => 'zaphod@slartibartfast.example.com',
          'phone' => array('home' => '555-444-3333',
                           'cell' => '555-111-1234')
                           )
           );*/

// on complète ?
print_r($data);
/*echo '<hr>';
foreach($data as $key => $val)
{
	echo $key . " - " . $val . "<br>";
	foreach ($data[$key] as $key2 => $val2)
		echo $key2 . " - " . $val2 . "<br>";
}*/
// à partir d'ici, on a toutes les infos de base
// on peut partir à la recherche de quelques infos particulières, qui ne seront pas systématiquement affichées
// on passe au template 
require "smarty/Smarty.class.php";
$smarty = new Smarty();
$smarty->assign('data',$data);
$smarty->assign('type',"perso");

$smarty->template_dir = '/home/delain/public_html/api/game/template';
$smarty->compile_dir = '/home/delain/public_html/api/compile';
$smarty->cache_dir = '/home/delain/public_html/api/cache';
$smarty->assign('variable', '<b>Contenu de ma variable</b>');
if(!isset($typesort))
	$typesort = 'xml';
switch($typesort)
{
	case 'xml':
		header('Content-Type: text/xml',true);
		$smarty->display('gen_xml_2.tpl');
		break;
	case 'json':
		$smarty->display('gen_json.tpl');
		break;
	default:
		$smarty->display('gen_json.tpl');
		break;
}	
//require '/home/delain/public_html/www/includes/delain_footer.php';
?>
