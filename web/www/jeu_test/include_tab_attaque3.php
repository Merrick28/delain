<?php 
include "../includes/constantes.php";
$db = new base_delain;
$req_portee = "select portee_attaque($perso_cod) as portee,ppos_pos_cod,pos_etage,type_arme($perso_cod) as type_arme,distance_vue($perso_cod) as distance_vue,pos_x,pos_y ";
$req_portee = $req_portee . "from perso_position,positions ";
$req_portee = $req_portee . "where ppos_perso_cod = $perso_cod ";
$req_portee = $req_portee . "and ppos_pos_cod = pos_cod ";
$db->query($req_portee);
$db->next_record();
$x = $db->f("pos_x");
$y = $db->f("pos_y");
$distance_vue = $db->f("distance_vue");
if ($db->f("distance_vue") > $db->f("portee"))
{
	$portee = $db->f("portee");
}
else
{
	$portee = $db->f("distance_vue");
}
if ($db->f("type_arme") == 2)
{
	$type_arme = 2;
}
else
{
	$type_arme = 1;
}
$pos_cod = $db->f("ppos_pos_cod");
$etage = $db->f("pos_etage");
$req = "select lock_cod from lock_combat where lock_cible = $perso_cod ";
$db->query($req);
if ($db->nf() == 0)
{
/*$req_vue_joueur = "select trajectoire_vue($pos_cod,pos_cod) as traj,perso_nom,pos_x,pos_y,pos_etage,race_nom,distance($pos_cod,pos_cod) as distance,pos_cod,perso_cod,case when perso_type_perso = 1 then 1 else 2 end as perso_type_perso,perso_pv,perso_pv_max,is_surcharge(perso_cod,$perso_cod) as surcharge , (select count(1) from trajectoire_perso($pos_cod,pos_cod) as (nv_cible int, v_pos int, type_perso int) 
where not exists (select 1 from perso_position,lieu,lieu_position where ppos_pos_cod = v_pos and ppos_perso_cod = nv_cible and lpos_pos_cod = v_pos and lpos_lieu_cod = lieu_cod and lieu_refuge = 'O')
) as obstruction ";*/
$req_vue_joueur = "select trajectoire_vue($pos_cod,pos_cod) as traj,perso_nom,pos_x,pos_y,pos_etage,race_nom,distance($pos_cod,pos_cod) as distance,pos_cod,perso_cod,case when perso_type_perso = 1 then 1 else 2 end as perso_type_perso,perso_pv,perso_pv_max,is_surcharge(perso_cod,$perso_cod) as surcharge , (select count(1) from trajectoire_perso($pos_cod,pos_cod) as (nv_cible int, v_pos int, type_perso int) 
) as obstruction ";
$req_vue_joueur = $req_vue_joueur . "from perso,positions,perso_position,race ";
$req_vue_joueur = $req_vue_joueur . "where pos_x between ($x-$portee) and ($x+$portee) ";
$req_vue_joueur = $req_vue_joueur . "and pos_y between ($y-$portee) and ($y+$portee) ";
$req_vue_joueur = $req_vue_joueur . "and pos_cod = ppos_pos_cod ";
$req_vue_joueur = $req_vue_joueur . "and pos_etage = $etage ";
$req_vue_joueur = $req_vue_joueur . "and ppos_perso_cod = perso_cod ";
$req_vue_joueur = $req_vue_joueur . "and perso_cod != $perso_cod ";
$req_vue_joueur = $req_vue_joueur . "and perso_actif = 'O' ";
$req_vue_joueur = $req_vue_joueur . "and perso_tangible = 'O' ";
$req_vue_joueur = $req_vue_joueur . "and perso_race_cod = race_cod ";
$req_vue_joueur = $req_vue_joueur . "and not exists ";
$req_vue_joueur = $req_vue_joueur . "(select 1 from lieu,lieu_position ";
$req_vue_joueur = $req_vue_joueur . "where lpos_pos_cod = ppos_pos_cod ";
$req_vue_joueur = $req_vue_joueur . "and lpos_lieu_cod = lieu_cod ";
$req_vue_joueur = $req_vue_joueur . "and lieu_refuge = 'O') ";
$req_vue_joueur = $req_vue_joueur . "and not exists ";
$req_vue_joueur = $req_vue_joueur . "(select 1 from perso_familier ";
$req_vue_joueur = $req_vue_joueur . "where pfam_perso_cod = $perso_cod ";
$req_vue_joueur = $req_vue_joueur . "and pfam_familier_cod = perso_cod) ";
if (($compt_cod != 'monstre') && ($compt_cod != 'admin'))
{
	$req_vue_joueur = $req_vue_joueur . "and not exists ";
	$req_vue_joueur = $req_vue_joueur . "(select 1 from perso_compte ";
	$req_vue_joueur = $req_vue_joueur . "where pcompt_compt_cod = $compt_cod ";
	$req_vue_joueur = $req_vue_joueur . "and pcompt_perso_cod = perso_cod) ";
/*Rajout pour ne pas pouvoir attaquer un perso d'un compte sitté + 2018-09-06 - Marlyza - ne pas attaquer les fam du compte */
	$req_vue_joueur = $req_vue_joueur . "	and perso_cod not in";
    $req_vue_joueur = $req_vue_joueur . "((select pfam_familier_cod from perso_compte join perso_familier on pfam_perso_cod=pcompt_perso_cod join perso on perso_cod=pfam_familier_cod  where pcompt_compt_cod = $compt_cod and perso_actif='O')";
    $req_vue_joueur = $req_vue_joueur . "	union";
	$req_vue_joueur = $req_vue_joueur . "(select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now())";
	$req_vue_joueur = $req_vue_joueur . "	union";
	$req_vue_joueur = $req_vue_joueur . "(select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now())";
	$req_vue_joueur = $req_vue_joueur . "	union";
	$req_vue_joueur = $req_vue_joueur . "(select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod)";
	$req_vue_joueur = $req_vue_joueur . "	union";
	$req_vue_joueur = $req_vue_joueur . "(select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod))";
/*Fin rajout*/
}
$req_vue_joueur = $req_vue_joueur . "order by perso_type_perso desc, distance,pos_x,pos_y,perso_nom ";
}
else
{
	$req_vue_joueur = "select trajectoire_vue($pos_cod,pos_cod) as traj,perso_nom,pos_x,pos_y,pos_etage,race_nom,distance($pos_cod,pos_cod) as distance,pos_cod,perso_cod,case when perso_type_perso = 1 then 1 else 2 end as perso_type_perso,perso_pv,perso_pv_max,is_surcharge(perso_cod,$perso_cod) as surcharge, 0 as obstruction ";
	$req_vue_joueur = $req_vue_joueur . "from perso,positions,perso_position,race,lock_combat ";
	$req_vue_joueur = $req_vue_joueur . "where pos_x between ($x-$portee) and ($x+$portee) ";
	$req_vue_joueur = $req_vue_joueur . "and pos_y between ($y-$portee) and ($y+$portee) ";
	$req_vue_joueur = $req_vue_joueur . "and pos_cod = ppos_pos_cod ";
	$req_vue_joueur = $req_vue_joueur . "and pos_etage = $etage ";
	$req_vue_joueur = $req_vue_joueur . "and ppos_perso_cod = perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "and perso_cod != $perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "and perso_actif = 'O' ";
	$req_vue_joueur = $req_vue_joueur . "and perso_tangible = 'O' ";
	$req_vue_joueur = $req_vue_joueur . "and perso_race_cod = race_cod ";
	$req_vue_joueur = $req_vue_joueur . "and not exists ";
	$req_vue_joueur = $req_vue_joueur . "(select 1 from lieu,lieu_position ";
	$req_vue_joueur = $req_vue_joueur . "where lpos_pos_cod = ppos_pos_cod ";
	$req_vue_joueur = $req_vue_joueur . "and lpos_lieu_cod = lieu_cod ";
	$req_vue_joueur = $req_vue_joueur . "and lieu_refuge = 'O') ";
	$req_vue_joueur = $req_vue_joueur . "and not exists ";
	$req_vue_joueur = $req_vue_joueur . "(select 1 from perso_familier ";
	$req_vue_joueur = $req_vue_joueur . "where pfam_perso_cod = $perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "and pfam_familier_cod = perso_cod) ";
	if (($compt_cod != 'monstre') && ($compt_cod != 'admin'))
	{
		$req_vue_joueur = $req_vue_joueur . "and not exists ";
		$req_vue_joueur = $req_vue_joueur . "(select 1 from perso_compte ";
		$req_vue_joueur = $req_vue_joueur . "where pcompt_compt_cod = $compt_cod ";
		$req_vue_joueur = $req_vue_joueur . "and pcompt_perso_cod = perso_cod) ";
	}
	$req_vue_joueur = $req_vue_joueur . "and lock_cible = $perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "and lock_attaquant = perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "union ";
	$req_vue_joueur = $req_vue_joueur . "select trajectoire_vue($pos_cod,pos_cod) as traj,perso_nom,pos_x,pos_y,pos_etage,race_nom,distance($pos_cod,pos_cod) as distance,pos_cod,perso_cod,perso_type_perso,perso_pv,perso_pv_max,is_surcharge(perso_cod,$perso_cod) as surcharge, 0 as obstruction ";
	$req_vue_joueur = $req_vue_joueur . "from perso,positions,perso_position,race,lock_combat ";
	$req_vue_joueur = $req_vue_joueur . "where pos_x between ($x-$portee) and ($x+$portee) ";
	$req_vue_joueur = $req_vue_joueur . "and pos_y between ($y-$portee) and ($y+$portee) ";
	$req_vue_joueur = $req_vue_joueur . "and pos_cod = ppos_pos_cod ";
	$req_vue_joueur = $req_vue_joueur . "and pos_etage = $etage ";
	$req_vue_joueur = $req_vue_joueur . "and ppos_perso_cod = perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "and perso_cod != $perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "and perso_actif = 'O' ";
	$req_vue_joueur = $req_vue_joueur . "and perso_tangible = 'O' ";
	$req_vue_joueur = $req_vue_joueur . "and perso_race_cod = race_cod ";
	$req_vue_joueur = $req_vue_joueur . "and not exists ";
	$req_vue_joueur = $req_vue_joueur . "(select 1 from lieu,lieu_position ";
	$req_vue_joueur = $req_vue_joueur . "where lpos_pos_cod = ppos_pos_cod ";
	$req_vue_joueur = $req_vue_joueur . "and lpos_lieu_cod = lieu_cod ";
	$req_vue_joueur = $req_vue_joueur . "and lieu_refuge = 'O') ";
	$req_vue_joueur = $req_vue_joueur . "and not exists ";
	$req_vue_joueur = $req_vue_joueur . "(select 1 from perso_familier ";
	$req_vue_joueur = $req_vue_joueur . "where pfam_perso_cod = $perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "and pfam_familier_cod = perso_cod) ";
	if (($compt_cod != 'monstre') && ($compt_cod != 'admin'))
	{
		$req_vue_joueur = $req_vue_joueur . "and not exists ";
		$req_vue_joueur = $req_vue_joueur . "(select 1 from perso_compte ";
		$req_vue_joueur = $req_vue_joueur . "where pcompt_compt_cod = $compt_cod ";
		$req_vue_joueur = $req_vue_joueur . "and pcompt_perso_cod = perso_cod) ";
	}
	$req_vue_joueur = $req_vue_joueur . "and lock_cible = perso_cod ";
	$req_vue_joueur = $req_vue_joueur . "and lock_attaquant = $perso_cod ";
	//echo "debug $req_vue_joueur ";
}

echo("<input type=\"hidden\" name=\"type_arme\" value=\"$type_arme\">");
// On recherche les autres joueurs en vue

$db->query($req_vue_joueur);
$nb_joueur_en_vue = $db->nf();
?>

<?php 
if ($nb_joueur_en_vue != 0)
{
	?>
	<table width="100%" cellspacing="2" cellapdding="2">
	<tr><td colspan="6" class="soustitre"><p class="soustitre">Cibles</td></tr>
	<tr>
	<td></td>
	<td class="soustitre2"><strong>Nom</strong></td>
	<td class="soustitre2"><strong>Race</strong></td>
	<td class="soustitre2"><strong>X</strong></td>
	<td class="soustitre2"><strong>Y</strong></td>
	<td class="soustitre2"><strong>Distance</strong></td>
	</tr>
	<script language="JavaScript" type="text/JavaScript">
  	var liste = new Array();
  	<?php 
  	$i = 0;
  	$jAttaquable = 0;
  	while($db->next_record())
	{
		if ($db->f("traj") == 1)
		{
		$pv = $db->f("perso_pv");
		$pv_max = $db->f("perso_pv_max");
		$niveau_blessures = '';
		if ($pv/$pv_max < 0.75)
		{
			$niveau_blessures = ' - ' . $tab_blessures[0];
		}
		if ($pv/$pv_max < 0.5)
		{
			$niveau_blessures = ' - ' . $tab_blessures[1];
		}
		if ($pv/$pv_max < 0.25)
		{
			$niveau_blessures = ' - ' . $tab_blessures[2];
		}
		if ($pv/$pv_max < 0.15)
		{
			$niveau_blessures = ' - ' . $tab_blessures[3];
		}
		$nom = str_replace("\\"," ",$db->f("perso_nom"));
		$nom = str_replace("'","\'",$nom);
		$type_perso = $db->f("perso_type_perso");
		$type = $perso_type_perso[$type_perso];
		$perso_cible = $db->f("perso_cod");
		$race = $db->f("race_nom");
		$x = $db->f("pos_x");
		$y = $db->f("pos_y");
		$distance = $db->f("distance");
		if ($db->f("distance") <= $portee)
		{
			$attaquable = 1;
			$jAttaquable = $jAttaquable + 1;
			
			$listePersoAttaquable[$jAttaquable] = $perso_cible;	
		}
		else
		{
			$attaquable = 0;
		}
		$style = "soustitre2";
		if ($db->f("surcharge") == 1)
		{
			$style = "surcharge1";
		}
		if ($db->f("surcharge") == 2)
		{
			$style = "surcharge2";
		}
		if ($db->f("obstruction") > 0)
		{
			$style = "soustitre2 obstruction1";
		}
		if ($db->f("obstruction") > 5)
		{
			$style = "soustitre2 obstruction2";
		}

		echo("liste[$i] = ['$perso_cible','$nom','$type','$race','$x','$y','$distance','$attaquable','$niveau_blessures','$style'];\r\n");
		$i = $i + 1;
		}
	}

		?>
	var i;
	for (i=0; i<liste.length; i++)
	{
		document.write('<tr>');
		document.write('<td><input type="radio" name="cible" class="change_class_on_click" data-class-normal="navoff" data-class-onclick="navon" data-class-dest="cell' + liste[i][0] + '" value="' + liste[i][0] + '"  id="bouton' + liste[i][0] + '"></td>');
        /*document.write('<td><input type="radio" name="cible" class="change_class_on_click" value="' + liste[i][0] + '" onClick="changeStyles(\'cell' + liste[i][0] + '\',1)" onBlur="changeStyles(\'cell' +  liste[i][0] + '\',0)" id="bouton' + liste[i][0] + '"></td>');*/
		document.write('<td id="cell' + liste[i][0] + '" class="' + liste[i][9] + '"><label for="bouton' + liste[i][0] + '"><strong>' + liste[i][1] + '</strong> (' + liste[i][2] + '<strong>' + liste[i][8] + '</strong>)</label></td>');
		document.write('<td>' + liste[i][3] + '</td>');
		document.write('<td>' + liste[i][4] + '</td>');
		document.write('<td>' + liste[i][5] + '</td>');
		document.write('<td>' + liste[i][6] + '</td>');
		document.write('</tr>');
	}
	</script>
	</table>

<?php

    // on regarde si la cible ne subit pas un malus de désorientation (sort Morsure du soleil) pour message de prévention !!!
    $req_malus_desorientation = " select valeur_bonus($perso_cod, 'DES') as desorientation";
    $db->query($req_malus_desorientation);
    $db->next_record();
    if ($db->f("desorientation") > 0) {
        echo "<strong>ATTENTION, vous subissez une désorientation, le choix de votre cible n'est pas assuré!</strong><br>";
    }
    
}
else
{
	echo("Pas de cible en vue !");
}
