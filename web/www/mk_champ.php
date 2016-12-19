<?php 
require_once '/usr/local/phplib-7.4a/php/prepend.php';
#require '/home/delain/public_html/www/includes/delain_header.php';
require '/home/delain/public_html/www/includes/classes.php';
$db = new base_delain;
$db2 = new base_delain;
$db3 = new base_delain;
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="http://images.jdr-delain.net/fond5.gif">
<?php include "jeu_test/tab_haut.php";
?>
<p class="titre">Les champions de Delain !</p><hr>
<center>
<?php 
    echo '<i>Page générée ' . date('\l\e d/m/Y \à H:i') . '</i>';
?>
<table>
<?php 
$req = "select typc_cod,typc_libelle from type_competences order by typc_libelle ";
$db->query($req);
while($db->next_record())
{
?>
	<tr><td colspan="4" class="titre"><?php echo $db->f("typc_libelle");?></td></tr>
<?php 
	$req_c = "select comp_cod,comp_libelle from competences where comp_typc_cod = " . $db->f("typc_cod") . "
		and comp_cod not in (29,69,70,78,80,81,82,84,85,86,94,95,96) order by comp_libelle ";
	$db2->query($req_c);
	$cpt_comp = 0;
	$nombre_max = 3;
	while($db2->next_record())
	{
		//Sélection des top pj, à l'exclusion des pnj et des pj non actifs
		$req = "select pcomp_modificateur, perso_nom from perso_competences, perso
			where pcomp_pcomp_cod = " . $db2->f("comp_cod") . "
        		and pcomp_perso_cod = perso_cod
                and perso_type_perso = 1
    			and perso_actif = 'O'
    			and perso_pnj != 1
            order by pcomp_modificateur desc
			limit 10";
		$db3->query($req);
        $nombre_courant = 0;
        $comp_courante = 0;
		if ($db3->nf() != 0)
		{
            // début de ligne
            if (fmod($cpt_comp, 2) == 0)
                echo "<tr>";
            echo "<td nowrap class=\"soustitre2\"><p><b>" . $db2->f("comp_libelle") . "</b></p></td>";
            echo "<td>";
            
            while($db3->next_record())
            {
                $valeur = $db3->f("pcomp_modificateur");
                $nombre_courant ++;
                if ($nombre_courant > $nombre_max && $valeur < $comp_courante)
                    break;
                $comp_courante = $valeur;
                echo "<b>$valeur %</b> - " . $db3->f("perso_nom") . "<br>";
            }
            echo "</td>";
            if (fmod($cpt_comp, 2) != 0)
            {
                echo("</tr>");
            }
            $cpt_comp = $cpt_comp + 1;
        }
    }
    if (fmod($cpt_comp, 2) != 0)
	{
		echo "<td></td><td></td></tr>";
	}
}
?>
</table></center>
<hr><br><b><u>Richesse des souterrains :</u></b><br><br>

<?php 
$req = "select sum(perso_po) as po,max(perso_po) as po_max,max(perso_for) as force,max(perso_dex) as dex,max(perso_con) as constit,max(perso_int) as intelligence from perso where perso_type_perso != 2 and perso_pnj != 1";
$db->query($req);
$db->next_record();
$po_global = $db->f("po");
$po_max = $db->f("po_max");
$force = $db->f("force");
$dexterite = $db->f("dex");
$constit = $db->f("constit");
$intelligence = $db->f("intelligence");
$req = "select sum(perso_po) as po_monstre from perso where perso_type_perso = 2";
$db->query($req);
$db->next_record();
$po_monstre = $db->f("po_monstre");
$req = "select sum(perso_po) as po_familier from perso where perso_type_perso = 3";
$db->query($req);
$db->next_record();
$po_familier = $db->f("po_familier");
$req = "select sum(pbank_or) as po_banque,max(pbank_or) as po_banque_max from perso_banque";
$db->query($req);
$db->next_record();
$po_banque = $db->f("po_banque");
$po_banque_max = $db->f("po_banque_max");


echo "Les aventuriers possèdent tous ensemble <b>". number_format($po_global, 0, '', ' ') . "</b> brouzoufs<br>Le plus riche possède <b>". number_format($po_max, 0, '', ' ') ."</b> brouzoufs<br><b>". number_format($po_monstre, 0, '', ' ') ."</b> brouzoufs sont portés par les monstres, et seulement <b>". number_format($po_familier, 0, '', ' ') . "</b> par les familiers.<br>Non content de cela, les aventuriers ont thésaurisé en banque <b>". number_format($po_banque, 0, '', ' ') ."</b> de brouzoufs, avec un petit veinard qui détient un petit pactole à l'abris de <b>". number_format($po_banque_max, 0, '', ' ') ."</b> ! Mieux vaut que la banque ne se fasse pas braquer !
<br><br>En même temps, le plus intelligent possède une intelligence de <b>". $intelligence ."</b> alors que le plus fort a <b>". $force ."</b> en force, la plus grosse dextérité est de <b>". $dexterite ."</b> et la plus grosse constitution est de <b>". $constit ."</b>. Alors comment vous situez vous par rapport à cela ?"  ;
?>
<br><br>
<hr>
<center><table>
<?php 
		$req = "select max(total2_max) as total_max2,gmon_race_cod,race_nom from (select gmon_race_cod,sum(total_max) as total2_max,ptab_perso_cod from
			          (select ptab_perso_cod,sum(ptab_total * gmon_niveau * gmon_niveau) as total_max,gmon_race_cod
			              from perso_tableau_chasse,monstre_generique,perso
			              where gmon_cod = ptab_gmon_cod
			              and ptab_perso_cod = perso_cod
			              and perso_pnj !=1
			              group by gmon_race_cod,ptab_perso_cod,ptab_gmon_cod) as foo
								group by gmon_race_cod,ptab_perso_cod
								order by total2_max) as foo2,race
								where gmon_race_cod = race_cod
								group by gmon_race_cod,race_nom";
		$db->query($req);
		if ($db->nf() != 0)
		{
			echo '<p class="titre">TABLEAU Des plus grands chasseurs</p><table width="80%">
   		<tr><td class="titre">Les monstres par race</td><td class="titre">Les plus grands chasseurs<br></td></tr>';
			while($db->next_record())
			{
			$race = $db->f("gmon_race_cod");
			$race_nom = $db->f("race_nom");
			$total_max2 = $db->f("total_max2");

			echo "<tr>";
			echo "<td nowrap class=\"soustitre2\"><p><b>". $race_nom . "</b></p></td>";
			echo "<td>";
			$req_p = "select total_max2,gmon_race_cod,ptab_perso_cod,perso_nom from (select gmon_race_cod,sum(total_max) as total_max2,ptab_perso_cod from
						          (select ptab_perso_cod,sum(ptab_total * gmon_niveau * gmon_niveau) as total_max,gmon_race_cod
						              from perso_tableau_chasse,monstre_generique,perso
						              where gmon_cod = ptab_gmon_cod
						              and gmon_race_cod = $race
						              and perso_cod = ptab_perso_cod
						              and perso_pnj != 1
						              group by gmon_race_cod,ptab_perso_cod,ptab_gmon_cod) as foo
						          group by gmon_race_cod,ptab_perso_cod
									order by total_max2) as foo2,perso
									where total_max2 = $total_max2
									and perso_cod = ptab_perso_cod";
			$db2->query($req_p);
					while($db2->next_record())
					{
						echo $db2->f("perso_nom") . "<br>";
					}
					echo "</td>";
			}
		}

?>
</table>
<br><b><i>à l'intérieur d'une race, les monstres sont pondérés par leur puissance.
<br>Par exemple, un morbelin n'aura pas la même valeur qu'un capitaine morbelin dans ce classement</i></b>
<br>
<hr>

<table>
<?php 
$req = "select distinct ptab_gmon_cod,gmon_niveau, gmon_nom from perso_tableau_chasse,monstre_generique
						where gmon_cod = ptab_gmon_cod
						and ptab_solo = '1'
						order by gmon_nom";
$db->query($req);
if ($db->nf() != 0)
{
   echo '<p class="titre">TABLEAU Des plus grands chasseurs en <b>SOLO</b></p><table width="80%">
   				<tr><td class="titre">Monstre</td><td class="titre">Nom</td><td class="titre">Total achevé en solo</td></tr>';
   while($db->next_record())
	{
		/* On sélectionne le total le plus élevé dans la chasse aux monstres*/
     $req = 'select ptab_perso_cod,sum(ptab_solo) as tmax
				from perso_tableau_chasse,perso
				where ptab_gmon_cod = ' . $db->f('ptab_gmon_cod') . '
				and perso_cod = ptab_perso_cod
				and perso_pnj != 1
				and perso_type_perso != 2
				group by ptab_perso_cod
				order by tmax desc
				limit 1';
     /*$req = "select distinct perso_nom
     		from perso,perso_tableau_chasse
     		where ptab_gmon_cod = " . $db->f('ptab_gmon_cod')  . "
     		and ptab_solo = '1'
     		and ptab_total = " . $db2->f('tmax') . "
     		and ptab_perso_cod = perso_cod";*/

		$db2->query($req);
		if ($db2->nf() != 0 )
		{
			$db2->next_record();
			$total_max = $db2->f("tmax");
			if ($total_max != 0)
			{
			   /* On affiche tous les noms qui correspondent à ce total après avoir testé qu'ils existent bien*/
					echo '<tr><td class="soustitre2"><b>' . $db->f("gmon_nom") . '<!-- ' . $db->f('ptab_gmon_cod') . '--></b></td><td>';
		     	$req = "	select distinct perso_nom,sum(ptab_solo)
		     		from perso,perso_tableau_chasse
		     		where ptab_gmon_cod = " . $db->f('ptab_gmon_cod')  . "
		     		and ptab_perso_cod = perso_cod
		     		and perso_pnj != 1
		     		and perso_type_perso != 2
						group by perso_nom
						having sum(ptab_solo) = " . $db2->f('tmax');
		     	$db3->query($req);
		     	if($db3->nf() == 0)
		     		echo '<i>Champion disparu....</i>';
					else
					{
		     		while($db3->next_record())
		     			echo $db3->f("perso_nom") . '<br>';
		     	}
		     echo '</td><td class="soustitre2">' . $db2->f("tmax") . '</td><td></tr>';
		   }
        }
	}
}
?>
</table></center>
<br><hr>
<?php 
include "jeu_test/tab_bas.php";

// Nettoyage des champions de coterie
$lesFichiers = array();
$fichiersExclus = array();
$fichiersExclus[] = '.';
$fichiersExclus[] = '..';
$fichiersExclus[] = '';
$repertoires = array();
$repertoires[] = './public_html/www/jeu/statiques';
$repertoires[] = './public_html/www/jeu_test/statiques';
foreach ($repertoires as $key => $repertoire)
{
	if (is_dir($repertoire))
	{
		$rep = opendir($repertoire);
		while (false !== ($fichier = readdir($rep)))
			if (array_search(strtolower($fichier), $fichiersExclus) === FALSE && !is_dir($fichier))
				$lesFichiers[] = $repertoire . '/' . $fichier;
	}
}
foreach ($lesFichiers as $key => $unFichier)
{
	unlink($unFichier);
}
		$rep = opendir('.');
		while (false !== ($fichier = readdir($rep)))
			echo "<!-- " . $rep . '/' . $fichier . " -->";

?>
</body>
</html>
