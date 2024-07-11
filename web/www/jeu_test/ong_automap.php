<?php
include "../includes/fonctions.php";
?>
<form name="det_cadre" method="post" action="action.php">
<input type="hidden" name="methode" value="deplacement">
<input type="hidden" name="position">
<input type="hidden" name="dist">
</form>
<div class="centrer">


<?php
$y_encours = 999999999999999999;

$req_etage = "select pos_etage,pos_cod,perso_type_perso from perso_position,positions,perso
	where ppos_perso_cod = $perso_cod
	and ppos_pos_cod = pos_cod
	and perso_cod = $perso_cod ";
$stmt = $pdo->query($req_etage);
$result = $stmt->fetch();
$etage_actuel = $result['pos_etage'];
$pos_actuelle = $result['pos_cod'];
$type_perso = $result['perso_type_perso'];
if (!isset($etage))
{
	$etage = $etage_actuel;
}

$methode          = get_request_var('methode', 'normal');
switch($methode)
{
	case "normal":
/*
25	2	Mécanisme
27	2	Poste d'entrée de Sal'Morv
8	6	Portail démoniaque
15	2	Poste d'entrée
11	2	Magasin
30	2	Passage tunnel
12	5	Pancarte
14	2	Magasin runique
17	2	Temple
10	7	Passage
24	2	Dalle magique
20	2	Dispensaire carcéral
29	2	Passage ondulant
26	2	Boutique de l'enchanteur
6	2	Centre d'entrainement
13	2	Centre de maitrise magique
5	2	Poste de garde
16	7	Grand escalier
2	4	Dispensaire
7	2	Aire neutre
23	2	Poste d'entrée en ruine
1	2	Banque
3	7	Escalier
28	2	Bâtiment Neutre
4	2	Auberge
18	2	Batiment en construction
9	2	Batiment administratif
*/
		?>
    <a class="centrer" href="javascript:void(0);"
       onclick="getdata('fr_dr.php?t_frdr=<?php echo $t_frdr; ?>&methode=choix', 'vue_droite');">Changer d’étage</a>
    <table style="border-spacing : 0;">
        <td>
            <table style="border-spacing : 1px;" background="../images/fond5.gif" border="0" cellspacing="1"
                   cellpadding="0">
                <?php
                $req  =
                    "select dcompt_modif_perso,dcompt_modif_gmon,dcompt_controle,dcompt_monstre_automap from compt_droit where dcompt_compt_cod = $compt_cod ";
                $stmt = $pdo->query($req);
                if ($stmt->rowCount() == 0)
                {
                    $droit['modif_perso'] = 'N';
			$droit['modif_gmon'] = 'N';
			$droit['controle'] = 'N';
			$droit['monstre_automap'] = 'N';
		}
		else
		{
			$result = $stmt->fetch();
			$droit['modif_perso'] = $result['dcompt_modif_perso'];
			$droit['modif_gmon'] = $result['dcompt_modif_gmon'];
			$droit['controle'] = $result['dcompt_controle'];
			$droit['monstre_automap'] = $result['dcompt_monstre_automap'];
		}
		$erreur = false;
		$req_vue = "select distance_vue($perso_cod) as distance";
		$stmt = $pdo->query($req_vue);
		$result = $stmt->fetch();
		$distance_vue = $result['distance'];

		$req = "select etage_cod from etage where etage_numero = $etage ";
		$stmt = $pdo->query($req);
		$result = $stmt->fetch();
		$v_etage = $result['etage_cod'];
		if ($type_perso != 2)
		{
			$req_max = "select pos_cod ";
			$req_max = $req_max . "from perso_vue_pos_" . $v_etage . ",positions ";
			$req_max = $req_max . "where pvue_perso_cod = $perso_cod ";
			$req_max = $req_max . "and pvue_pos_cod = pos_cod ";
			$req_max = $req_max . "and pos_etage = $etage ";
			$stmt = $pdo->query($req_max);
			if ($stmt->rowCount() == 0)
			{
				echo "<p>Etage non visité !</p>";
				$erreur = true;
			}
		}
		$automap_ok = false;
		if (!$erreur)
		{
			$min_x = 0;
			$min_y = 0;
			$max_x = 0;
			$max_y = 0;
			$total = 0;
			if (($type_perso == 2) && ($droit['monstre_automap'] == 'O'))
			{
				$req_max = "select min(pos_x) as minx,max(pos_x) as maxx,min(pos_y) as miny,max(pos_y) as maxy ";
				$req_max = $req_max . "from positions ";
				$req_max = $req_max . "where pos_etage = $etage ";
				$stmt = $pdo->query($req_max);
				if($result = $stmt->fetch())
				{
					$min_x = $result['minx'];
					$max_x = $result['maxx'];
					$min_y = $result['miny'];
					$max_y = $result['maxy'];
					$total = 1;
					$automap_ok = true;
				}
			}
			else
			{
				$req_max = "select min(pos_x) as minx,max(pos_x) as maxx,min(pos_y) as miny,max(pos_y) as maxy ";
				$req_max = $req_max . "from perso_vue_pos_" . $v_etage . ",positions ";
				$req_max = $req_max . "where pvue_perso_cod = $perso_cod ";
				$req_max = $req_max . "and pvue_pos_cod = pos_cod ";
				$req_max = $req_max . "and pos_etage = $etage ";
				$stmt = $pdo->query($req_max);
				if($result = $stmt->fetch())
				{
					$min_x = $result['minx'];
					$max_x = $result['maxx'];
					$min_y = $result['miny'];
					$max_y = $result['maxy'];
					$total = 0;
					$automap_ok = isset($min_x);
				}
			}
			if ($automap_ok)
			{
				if ($total == 0)
				{
					$req = "select count(*) as nb from perso_vue_pos_" . $v_etage . " where pvue_perso_cod = $perso_cod ";
					$stmt = $pdo->query($req);
					$result = $stmt->fetch();
					$nb_v = $result['nb'];
					$req = "select count(*) as nb from positions where pos_etage = $etage ";
					$stmt = $pdo->query($req);
					$result = $stmt->fetch();
					$nb_t = $result['nb'];
					$p = round($nb_v*100/$nb_t,2);
					$req_pos = "select 	pos_cod,pos_x,pos_y,dauto_valeur, ";
					$req_pos = $req_pos . "(select count(pvue_perso_cod) from perso_vue_pos_" . $v_etage . " where pvue_pos_cod = pos_cod	and pvue_perso_cod = $perso_cod) as nombre, ";
					$req_pos = $req_pos . "distance(pos_cod,$pos_actuelle) as distance ";
					$req_pos = $req_pos . "from positions,donnees_automap ";
					$req_pos = $req_pos . "where pos_cod = dauto_pos_cod ";
					$req_pos = $req_pos . "and pos_etage = $etage ";
					$req_pos = $req_pos . "and pos_x between $min_x and $max_x ";
					$req_pos = $req_pos . "and pos_y between $min_y and $max_y ";
					$req_pos = $req_pos . "group by pos_cod,pos_x,pos_y,dauto_valeur,distance ";
					$req_pos = $req_pos . "order by pos_y desc,pos_x asc ";
				}
				else
				{
					$req_pos = "select 	pos_cod,pos_x,pos_y,dauto_valeur, ";
					$req_pos = $req_pos . "1 as nombre, ";
					$req_pos = $req_pos . "distance(pos_cod,$pos_actuelle) as distance ";
					$req_pos = $req_pos . "from positions,donnees_automap ";
					$req_pos = $req_pos . "where pos_cod = dauto_pos_cod ";
					$req_pos = $req_pos . "and pos_etage = $etage ";
					$req_pos = $req_pos . "and pos_x between $min_x and $max_x ";
					$req_pos = $req_pos . "and pos_y between $min_y and $max_y ";
					$req_pos = $req_pos . "group by pos_cod,pos_x,pos_y,dauto_valeur,distance ";
					$req_pos = $req_pos . "order by pos_y desc,pos_x asc ";
				}
				$stmt = $pdo->query($req_pos);

				$i = 0;
				while ($result = $stmt->fetch())
				{
					$isvue = 9;
					$dessus = 0;
					$comment = '';
					if ($etage == $etage_actuel)
					{
						if ($result['nombre'] != 0)
						{
							if ($result['distance'] <= $distance_vue)
							{
								$isvue = 1;
							}
							else
							{
								$isvue = 0;
							}
						}
					}
					else
					{
						if ($result['nombre'] != 0)
						{
							$isvue = 0;
						}
					}
					if ($result['pos_cod'] == $pos_actuelle)
					{
						$dessus = 3;
					}
					else
					{
						$dessus = $result['dauto_valeur'];
					}
					if (($dessus == 2) || ($dessus == 4) || ($dessus == 5) || ($dessus == 6)|| ($dessus == 7))
					{
						$pos_encours = $result['pos_cod'];

						$req_lieu = "select lieu_nom,tlieu_libelle,lieu_tlieu_cod
							from lieu,lieu_position,lieu_type
							where lpos_pos_cod = $pos_encours
								and lpos_lieu_cod = lieu_cod
								and lieu_tlieu_cod = tlieu_cod ";
						$stmt_lieu = $pdo->query($req_lieu);
						$result_lieu = $stmt_lieu->fetch();
						$type_lieu = $result_lieu['lieu_tlieu_cod'];
						$comment = $result_lieu['lieu_nom'] . "(" . $result_lieu['tlieu_libelle'] . ")";
						$comment = str_replace("'","\'",$comment);
						//On donne une nouvelle valeur au $dessus pour faire apparaître des couleurs différentes en fonction des bâtiments
						if (($type_lieu == 11) || ($type_lieu == 14) || ($type_lieu == 17) || ($type_lieu == 10)|| ($type_lieu == 13) || ($type_lieu == 9))
						{
							$dessus = $type_lieu;
						}
						if ($type_lieu == 6)
						{
							$dessus = 19;
						}
						if ($type_lieu == 1)
						{
							$dessus = 20;
						}
						if ($type_lieu == 4)
						{
							$dessus = 22;
						}
						if ($type_lieu == 33) // Les autels de prière sont identiques aux temples
						{
							$dessus = 17;
						}
						if ($type_lieu == 34) // Les grandes portes sont assimilées à des passages
						{
							$dessus = 10;
						}
						if ($type_lieu == 42) // Les guildes sont assimilés aux auberges
						{
							$dessus = 22;
						}
					}
					if ($isvue == 9)
					{
						$dessus = 0;
						$comment = '';
					}
					$ligne = '<td style="padding: 0px;"   ><img src="' . G_IMAGES . 'automap_' . $isvue . '_' . $dessus .
                             '.gif" title=" '.
                             $comment . 'X ' . $result['pos_x'] . ',Y ' . $result['pos_y'] . ')"></td>';
					if($y_encours != $result['pos_y'])
					{
						$y_encours = $result['pos_y'];
						$ligne = "</tr><tr style=\"padding: 0px;line-height: 0px;\" >" . $ligne;
					}
					echo $ligne;
					//$texte = "tc[$i]" .  "=" . "['" . $result['pos_x'] . "','" . $result['pos_y'] . "','$isvue','$dessus','$comment'];\r\n";
					//echo("$texte");
					$i = $i + 1;
				}
			}
			else
            {
                echo '<div style="width:300px;"><p>Qui suis-je&nbsp;? Où vais-je&nbsp;?<br />Dans quelle étagère&nbsp;?<br /><br /> Malgré tous ses efforts, ' . $perso->perso_nom . ' est incapable de se souvenir d’où il a mis ses pieds...</p></div>';
            }
		}
?>
	</table>
</td>
<?php 		if ($automap_ok)
		{
?>
<td>
	<table>

	<tr><td border="2" width="20" height="20" ><img alt="Bâtiment administratif" src="../images/automap_0_9.gif" style="width:8px;height:8px;"></td><td> Bâtiment administratif</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Passage magique" src="../images/automap_0_10.gif" style="width:8px;height:8px;"></td><td> Passage magique, sortie d'antre, ...</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Magasin" src="../images/automap_0_11.gif" style="width:8px;height:8px;"></td><td> Magasin</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Centre de maîtrise magique" src="../images/automap_0_13.gif" style="width:8px;height:8px;"></td><td> Centre de maitrise magique</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Magasin runique" src="../images/automap_0_14.gif" style="width:8px;height:8px;"></td><td> Magasin runique</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Temple" src="../images/automap_0_17.gif" style="width:8px;height:8px;"></td><td> Temple, autel de prière</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Centre d'entrainement" src="../images/automap_0_19.gif" style="width:8px;height:8px;"></td><td> Centre d'entrainement</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Banque" src="../images/automap_0_20.gif" style="width:8px;height:8px;"></td><td> Banque</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Escalier, grands escaliers" src="../images/automap_0_7.gif" style="width:8px;height:8px;"></td><td> Escalier, grands escaliers</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Dispensaire" src="../images/automap_0_4.gif" style="width:8px;height:8px;"></td><td> Dispensaire</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Auberge, Guilde" src="../images/automap_0_22.gif" style="width:8px;height:8px;"></td><td> Auberge, Guilde</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Pancartes, indications" src="../images/automap_1_5.gif" style="width:8px;height:8px;"></td><td> Pancartes, indications</td></tr>
		<tr><td border="2" width="20" height="20" ><img alt="Portails démoniaques" src="../images/automap_1_6.gif" style="width:8px;height:8px;"></td><td> Portails démoniaques</td></tr>
	</table>
</td>
<?php
		}
?>
</tr></table></div>
<?php
		if ($automap_ok)
			echo "<em class='centrer'>$p % de l’étage visité.</em>";

		$req = "select etage_libelle from etage where etage_numero = $etage ";
		$stmt = $pdo->query($req);
		$result = $stmt->fetch();
		echo "<br><br><!-- niveau en cours : " . $result['etage_libelle'] . " -->";
	break;

	case "choix":
$req_total = "select distinct pos_etage,etage_libelle,etage_numero,etage_reference from positions,etage where (pos_etage <= 0 
		or exists (select 1 from etage_visite 
		where vet_perso_cod = $perso_cod 
		and vet_etage = pos_etage)) 
		and pos_etage = etage_numero 
		order by etage_reference desc, pos_etage asc ";
$stmt      = $pdo->query($req_total);
?>
<form name="automap" method="post" action="fr_dr.php"
      onsubmit="getdata('fr_dr.php?t_frdr=<?php echo $t_frdr; ?>&etage=' + document.getElementById('etage').value, 'vue_droite'); return false;">
    <p>Voir l’automap de l’étage : <select name="etage" id="etage">
            <?php
            $etages_fermes = 0;
            while ($result = $stmt->fetch())
            {
                if ($etages_fermes == 0 && $result['etage_reference'] == -100)
                {
				$etages_fermes = 1;
				echo '<optgroup label="Étages d’animation actuellement fermés">';
			}
			printf("<option value=\"%s\" ",$result['pos_etage']);
			if ($result['pos_etage'] == $etage)
			{
				echo(" selected ");
			}
			echo(">");
			$reference = ($result['etage_numero'] == $result['etage_reference']);
			printf("%s</option>",($reference?'':' |-- ').$result['etage_libelle']);
		}
		if ( $etages_fermes == 1 )
		{
			echo '</optgroup>';
		}
		echo("</select>");
		echo(" <input type=\"submit\" value=\"Voir\" class=\"test\">");
		echo("</form>");

	break;
}
?>
