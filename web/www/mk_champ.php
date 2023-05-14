<?php
ini_set('include_path', '.:/home/delain/delain/web/phplib-7.4a/php:/home/delain/delain/web/www/includes');

include_once "delain_header.php";
include_once "classes.php";
$pdo = new bddpdo();
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="css/delain.css" rel="stylesheet">
<head>
    <title>Pages des champions</title>
</head>
<body>

?>
<div class="bordiv">
    <p class="titre">Les champions de Delain !</p>
    <hr>
    <div class="centrer">
        <?php
        echo '<em>Page générée ' . date('\l\e d/m/Y \à H:i') . '</em>';
        ?>
        <table>
            <?php
            $req  = "select typc_cod,typc_libelle from type_competences order by typc_libelle ";
            $stmt = $pdo->query($req);

            $req_c = "select comp_cod,comp_libelle from competences where comp_typc_cod = :typc
		and comp_cod not in (29,69,70,78,80,81,82,84,85,86,94,95,96) order by comp_libelle ";
            $stmt2 = $pdo->prepare($req_c);

            $req = "select pcomp_modificateur, perso_nom from perso_competences, perso, perso_compte, compte
			where pcomp_pcomp_cod = :comp
        		and pcomp_perso_cod = perso_cod
				and pcompt_perso_cod = perso_cod
				and compt_cod = pcompt_compt_cod
                and perso_type_perso = 1
    			and perso_actif = 'O'
    			and perso_pnj != 1
			    and coalesce(perso_test,'N') != 'O'
			    and compt_monstre='N' and compt_admin='N'
            order by pcomp_modificateur desc
			limit 10";

            $stmt3 = $pdo->prepare($req);

            while ($result = $stmt->fetch())
            {
                ?>
                <tr>
                    <td colspan="4" class="titre"><?php echo $result['typc_libelle']; ?></td>
                </tr>
                <?php
                $stmt2 = $pdo->execute(array(":typc" => $result['typc_cod']), $stmt2);

                $cpt_comp   = 0;
                $nombre_max = 3;
                while ($result2 = $stmt2->fetch())
                {
                    //Sélection des top pj, à l'exclusion des pnj et des pj non actifs
                    $stmt3          = $pdo->execute(array(":comp" => $result2['comp_cod']), $stmt3);
                    $all3           = $stmt3->fetchAll();
                    $nombre_courant = 0;
                    $comp_courante  = 0;
                    if (count($all3) != 0)
                    {
                        // début de ligne
                        if (fmod($cpt_comp, 2) == 0)
                            echo "<tr>";
                        echo "<td nowrap class=\"soustitre2\"><p><strong>" . $result2['comp_libelle'] . "</strong></p></td>";
                        echo "<td>";
                        foreach ($all3 as $detail3)
                        {
                            $valeur = $detail3['pcomp_modificateur'];
                            $nombre_courant++;
                            if ($nombre_courant > $nombre_max && $valeur < $comp_courante)
                                break;
                            $comp_courante = $valeur;
                            echo "<strong>$valeur %</strong> - " . $detail3['perso_nom'] . "<br>";
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
        </table>
    </div>
    <hr>
    <br><strong><u>Richesse des souterrains :</u></strong><br><br>

    <?php
    $req    =
        "select sum(perso_po) as po,max(perso_po) as po_max,max(perso_for) as force,max(perso_dex) as dex,max(perso_con) as constit,max(perso_int) as intelligence from perso where perso_type_perso != 2 and perso_pnj != 1 and perso_test != 'O'";
    $stmt   = $pdo->query($req);
    $result = $stmt->fetch();

    $po_global     = $result['po'];
    $po_max        = $result['po_max'];
    $force         = $result['force'];
    $dexterite     = $result['dex'];
    $constit       = $result['constit'];
    $intelligence  = $result['intelligence'];
    $req           = "select sum(perso_po) as po_monstre from perso where perso_type_perso = 2";
    $stmt          = $pdo->query($req);
    $result        = $stmt->fetch();
    $po_monstre    = $result['po_monstre'];
    $req           = "select sum(perso_po) as po_familier from perso where perso_type_perso = 3";
    $stmt          = $pdo->query($req);
    $result        = $stmt->fetch();
    $po_familier   = $result['po_familier'];
    $req           = "select sum(pbank_or) as po_banque,max(pbank_or) as po_banque_max from perso_banque";
    $stmt          = $pdo->query($req);
    $result        = $stmt->fetch();
    $po_banque     = $result['po_banque'];
    $po_banque_max = $result['po_banque_max'];


    echo "Les aventuriers possèdent tous ensemble <strong>" . number_format($po_global, 0, '', ' ') . "</strong> brouzoufs<br>Le plus riche possède <strong>" . number_format($po_max, 0, '', ' ') . "</strong> brouzoufs<br><strong>" . number_format($po_monstre, 0, '', ' ') . "</strong> brouzoufs sont portés par les monstres, et seulement <strong>" . number_format($po_familier, 0, '', ' ') . "</strong> par les familiers.<br>Non content de cela, les aventuriers ont thésaurisé en banque <strong>" . number_format($po_banque, 0, '', ' ') . "</strong> de brouzoufs, avec un petit veinard qui détient un petit pactole à l'abris de <strong>" . number_format($po_banque_max, 0, '', ' ') . "</strong> ! Mieux vaut que la banque ne se fasse pas braquer !
<br><br>En même temps, le plus intelligent possède une intelligence de <strong>" . $intelligence . "</strong> alors que le plus fort a <strong>" . $force . "</strong> en force, la plus grosse dextérité est de <strong>" . $dexterite . "</strong> et la plus grosse constitution est de <strong>" . $constit . "</strong>. Alors comment vous situez vous par rapport à cela ?";
    ?>
    <br><br>
    <hr>
    <div class="centrer">
        <table>
            <?php
            $req         = "select max(total2_max) as total_max2,gmon_race_cod,race_nom from (select gmon_race_cod,sum(total_max) as total2_max,ptab_perso_cod from
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
            $stmt        = $pdo->query($req);
            $allchasseur = $stmt->fetchAll();

            $req_p = "select total_max2,gmon_race_cod,ptab_perso_cod,perso_nom from (select gmon_race_cod,sum(total_max) as total_max2,ptab_perso_cod from
						          (select ptab_perso_cod,sum(ptab_total * gmon_niveau * gmon_niveau) as total_max,gmon_race_cod
						              from perso_tableau_chasse,monstre_generique,perso
						              where gmon_cod = ptab_gmon_cod
						              and gmon_race_cod = :race
						              and perso_cod = ptab_perso_cod
						              and perso_pnj != 1
						              group by gmon_race_cod,ptab_perso_cod,ptab_gmon_cod) as foo
						          group by gmon_race_cod,ptab_perso_cod
									order by total_max2) as foo2,perso
									where total_max2 = :totalmax
									and perso_cod = ptab_perso_cod";
            $stmt2 = $pdo->prepare($req_p);
            if (count($allchasseur) != 0)
            {
                echo '<p class="titre">TABLEAU Des plus grands chasseurs</p><table width="80%">
   		<tr><td class="titre">Les monstres par race</td><td class="titre">Les plus grands chasseurs<br></td></tr>';
                foreach ($allchasseur as $detailchasseur)
                {
                    $race       = $detailchasseur['gmon_race_cod'];
                    $race_nom   = $detailchasseur['race_nom'];
                    $total_max2 = $detailchasseur['total_max2'];

                    echo "<tr>";
                    echo "<td nowrap class=\"soustitre2\"><p><strong>" . $race_nom . "</strong></p></td>";
                    echo "<td>";

                    $stmt2 = $pdo->execute(array(":totalmax" => $total_max2,
                                                 ":race"     => $race), $stmt2);


                    while ($result2 = $stmt2->fetch())
                    {
                        echo $result2['perso_nom'] . "<br>";
                    }
                    echo "</td>";
                }
            }

            ?>
        </table>
        <br><strong><em>à l'intérieur d'une race, les monstres sont pondérés par leur puissance.
                <br>Par exemple, un morbelin n'aura pas la même valeur qu'un capitaine morbelin dans ce
                classement</em></strong>
        <br>
        <hr>

        <table>
            <?php
            $req    = "select distinct ptab_gmon_cod,gmon_niveau, gmon_nom from perso_tableau_chasse,monstre_generique
						where gmon_cod = ptab_gmon_cod
						and ptab_solo = '1'
						order by gmon_nom";
            $stmt   = $pdo->query($req);
            $allmon = $stmt->fetchAll();


            $req   = 'select ptab_perso_cod,sum(ptab_solo) as tmax
				from perso_tableau_chasse,perso
				where ptab_gmon_cod = :gmon
				and perso_cod = ptab_perso_cod
				and perso_pnj != 1
				and perso_type_perso != 2
				group by ptab_perso_cod
				order by tmax desc
				limit 1';
            $stmt2 = $pdo->prepare($req);

            $req   = "	select distinct perso_nom,sum(ptab_solo)
		     		from perso,perso_tableau_chasse
		     		where ptab_gmon_cod = :ptab_gmon_cod
		     		and ptab_perso_cod = perso_cod
		     		and perso_pnj != 1
		     		and perso_type_perso != 2
						group by perso_nom
						having sum(ptab_solo) = :tmax";
            $stmt3 = $pdo->prepare($req);

            if (count($allmon) != 0)
            {
                echo '<p class="titre">TABLEAU Des plus grands chasseurs en <strong>SOLO</strong></p><table width="80%">
   				<tr><td class="titre">Monstre</td><td class="titre">Nom</td><td class="titre">Total achevé en solo</td></tr>';
                foreach ($allmon as $detailmon)
                {
                    /* On sélectionne le total le plus élevé dans la chasse aux monstres*/

                    $stmt2     = $pdo->execute(array(":gmon" => $detailmon['ptab_gmon_cod']), $stmt2);
                    $result2   = $stmt2->fetch();
                    $total_max = $result2['tmax'];
                    if ($total_max != 0)
                    {
                        /* On affiche tous les noms qui correspondent à ce total après avoir testé qu'ils existent bien*/
                        echo '<tr><td class="soustitre2"><strong>' . $detailmon['gmon_nom']  . '</strong></td><td>';

                        $stmt3 = $pdo->execute(array(
                                                   ":ptab_gmon_cod" => $detailmon['ptab_gmon_cod'],
                                                   ":tmax"          => $total_max
                                               ), $stmt3);
                        $all3  = $stmt3->fetchAll();




                        if (count($all3) == 0)
                        {
                            echo '<em>Champion disparu....</em>';
                        } else
                        {
                            foreach($all3 as $detail3)
                            {
                                echo $detail3['perso_nom'] . '<br>';
                            }

                        }
                        echo '</td><td class="soustitre2">' . $total_max . '</td><td></tr>';
                    }

                }
            }
            ?>
        </table>
    </div>
</div>

</body>
</html>
