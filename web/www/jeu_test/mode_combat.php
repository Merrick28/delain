<?php 
if (!isset($perso_cod))
    die ("Erreur d’appel de la page");

if (isset($methode) && $methode == 'mode_combat')
{
    $req = "select change_mcom_cod($perso_cod, $mode) as resultat ";
    $db->query($req);
}

$req = "select (perso_dchange_mcom + '24 hours'::interval) > now() as depasse,
            (perso_dcreat + '24 hours'::interval) > now() as date_creation,
            mcom_nom, mcom_cod,
            to_char(perso_dchange_mcom + '24 hours'::interval,'DD/MM/YYYY hh24:mi:ss') as dch
            from perso, mode_combat
    	where perso_cod = $perso_cod
		and perso_mcom_cod = mcom_cod";
$db->query($req);
$db->next_record();
$actu = $db->f("mcom_cod");
$contenu_include = 'Vous êtes actuellement en mode ';

if ($db->f("depasse") != 'f' && $db->f("date_creation") == 'f')
{
	$contenu_include = 'Votre posture de combat est ';
    $contenu_include .= '<b>' . $db->f("mcom_nom") . '</b>. (Vous ne pourrez la changer qu’à partir de ' . $db->f("dch") . ').';
}
else
{
    $contenu_include = '<form action="#" method="post" style="margin-left:0px; padding-left:0px;">
    <input type="hidden" name="methode" value="mode_combat">
    Votre posture de combat est <select name="mode">';
    $req = "select mcom_nom,mcom_cod from mode_combat order by mcom_cod ";
    $db->query($req);
    while($db->next_record())
    {
        $selected = ($actu == $db->f("mcom_cod")) ? ' selected="selected"' : '';
        $contenu_include .= '<option value="' . $db->f("mcom_cod") . '"' . $selected . '>' . $db->f("mcom_nom") . '</option>';
    }
    $contenu_include .= '</select>.&nbsp;<input type="submit" class="test" value="Changer !" title="Attention, ce mode ne peut être changé qu’une fois toutes les 24 heures, hormis le jour de création du personnage où la modification est illimitée."> (Attention, ce mode ne peut être changé qu’une fois toutes les 24 heures, excepté le jour de création du personnage.)</form>';
}
?>
