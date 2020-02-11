<?php
include "blocks/_header_page_jeu.php";
// scripts JS
$javascript = "<script language=\"javascript\"> 
function valide_form1() { 
	if (document.attaquer1.ctl.value == 0)
	{
		document.attaquer1.ctl.value = 1;
		document.attaquer1.submit();
		return true;
	}
	else
	{
		document.location.replace('combat.php');
	}
}
function valide_form2() { 
	if (document.attaquer2.ctl.value == 0)
	{
		document.attaquer2.ctl.value = 1;
		document.attaquer2.submit();
		return true;
	}
	else
	{
		document.location.replace('combat.php');
	}
}

</script>";

// contenu de la page
$contenu_page = "";
if (!$db->is_admin($compt_cod))
{
    $req = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $pos = $result['ppos_pos_cod'];
    $req = "select perso_cod  from perso,perso_position
		where perso_race_cod = 53
		and perso_cod = ppos_perso_cod
		and ppos_pos_cod = $pos ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
        $contenu_page .= '<p>Vous rencontrez un lutin rouge. Vous pouvez <a href="action.php?methode=don_cadeau_rouge">lui donner un cadeau (' . $param->getparm(99) . ' PA)</a>.';
    $req = "select perso_cod  from perso,perso_position
		where perso_race_cod = 54
		and perso_cod = ppos_perso_cod
		and ppos_pos_cod = $pos ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
        $contenu_page .= '<p>Vous rencontrez un lutin noir. Vous pouvez <a href="action.php?methode=don_cadeau_noir">lui donner un cadeau (' . $param->getparm(99) . ' PA)</a>.';
} else
{
    $contenu_page .= "<p>Vous ne pouvez pas valider des actions en étant administrateur !";
}
include "blocks/_footer_page_jeu.php";