
<form name="det_cadre" method="post" action="frame_vue.php">
<input type="hidden" name="t_frdr" value="<?php  echo $t_frdr; ?>">
<input type="hidden" name="position">
<input type="hidden" name="dist">
<?php

if(isset($_REQUEST['position']))
{
    $position = 1 * $_REQUEST['position'];
}

if(!isset($db))
{
	include "verif_connexion.php";
}

if((!isset($position)) || ($position == ''))
{
	$sql = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
	$db->query($sql);
	$db->next_record();
	$position = $db->f("ppos_pos_cod");
}
$req = "select ppos_pos_cod,distance_vue($perso_cod) as dist from perso_position where ppos_perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
$pos_actu = $db->f("ppos_pos_cod");
$d_vue = $db->f("dist");
$req = "select distance($position,$pos_actu) as dist,trajectoire_vue($position,$pos_actu) as traj  ";
$db->query($req);
$db->next_record();
if ($db->f("dist") > $d_vue)
	{
	die("Position trop éloignée !");
	}
if ($db->f("traj") != 1)
	{
	die("Position non visible !");
	}

$req = "select pos_x,pos_y,etage_libelle from positions,etage
	where pos_cod = $position
	and pos_etage = etage_numero ";
$db->query($req);
$db->next_record();
?>
<center><?php echo $db->f("pos_x");?>, <?php echo $db->f("pos_y");?>, <?php echo $db->f("etage_libelle");?><br>

<?php 	$req = "select lieu_nom, tlieu_libelle, lieu_refuge
		from lieu
		inner join lieu_type on lieu_tlieu_cod = tlieu_cod
		inner join lieu_position on lpos_lieu_cod = lieu_cod
		where lpos_pos_cod = $position";
	$db->query($req);

	if ($db->next_record())
	{
		$lieu_nom = $db->f('lieu_nom');
		$tlieu_libelle = $db->f('tlieu_libelle');
		$lieu_refuge = ($db->f('lieu_refuge') == 'O') ? 'refuge' : 'non protégé';
		echo "$lieu_nom ($tlieu_libelle - $lieu_refuge)<br />";
	}
	
//#LAG: rechercher la liste de perso sur la case	
$req = "select lower(perso_nom) as minusc,etat_perso(perso_cod) as bless,perso_nom from perso,perso_position where ppos_pos_cod = $position
		and ppos_perso_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso = 1
		order by minusc";
$db->query($req);
?>

<table border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td class="soustitre2" valign="top"><strong><?php echo $db->nf();?> persos.</strong></br>
<?php 

if ($db->nf() != 0)
{
	while ($db->next_record())
	{
		echo $db->f("perso_nom");
		if (($db->f("bless") != "indemne") && ($db->f("bless") != "égratigné"))
			echo "<em> - " . $db->f("bless") , "</em>";
		echo "<br>";
	}
}



$req = "select lower(perso_nom) as minusc,etat_perso(perso_cod) as bless,perso_nom from perso,perso_position where ppos_pos_cod = $position
		and ppos_perso_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso in (2,3)
		order by minusc";
$db->query($req);
?>
</td><td class="soustitre2" valign="top"><strong><?php echo $db->nf();?> monstres : </strong><br>
<?php 
if ($db->nf() != 0)
{
	while ($db->next_record())
	{
		echo $db->f("perso_nom");
		if (($db->f("bless") != "indemne") && ($db->f("bless") != "égratigné"))
			echo "<em> - " . $db->f("bless") , "</em>";
		echo "<br>";
	}
}
?>
</td></tr>
<tr><td class="soustitre2" >
<?php 
$req = "select count(pobj_cod) as nombre from objet_position  where pobj_pos_cod = $position ";
$db->query($req);
$db->next_record();
echo "<strong>" , $db->f("nombre") , "&nbsp;objets au sol</strong>";
if ($db->f("nombre") != 0)
{
	echo "<p class=\"detail\">";
	$req = "select tobj_libelle,count(*) as nb from objets,objet_position,objet_generique,type_objet
				where pobj_pos_cod = $position 
				and pobj_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod
				group by tobj_libelle ";
	$db->query($req);
	while ($db->next_record())
	{
		echo $db->f("tobj_libelle") , "&nbsp;:&nbsp;" , $db->f("nb") , "<br>";
	}
}
?>
</td><td class="soustitre2" >
<?php 
$req = "select count(por_cod) as nombre from or_position  where por_pos_cod = $position ";
$db->query($req);
$db->next_record();
echo "<strong>" , $db->f("nombre") , "&nbsp;tas de brouzoufs au sol</strong>";
if ($db->f("nombre") != 0)
{
	echo "<br>";
	$req = "select sum(por_qte) as nb from or_position  where por_pos_cod = $position ";
	$db->query($req);
	$db->next_record();
	echo "<p class=\"detail\">Total&nbsp;:&nbsp;" , $db->f("nb") , "&nbsp;brouzoufs.";
}
?>
</td></tr></table></center>
