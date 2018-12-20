<?php
include "blocks/_header_page_jeu.php";

$droit_modif = 'dcompt_modif_gmon';
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    echo "<hr /><p>Cette page permet d’avoir un aperçu rapide des du plan des souterrains.</p><hr />";

    $requete_etages = "select e1.etage_reference as er1, e1.etage_numero as e1, e1.etage_libelle as nom1, e2.etage_reference as er2, e2.etage_numero as e2, e2.etage_libelle as nom2, count(*) as nb
		from positions p1
		inner join lieu_position on lpos_pos_cod = p1.pos_cod
		inner join lieu on lieu_cod = lpos_lieu_cod
		inner join positions p2 on p2.pos_cod = lieu_dest
		inner join etage e1 on e1.etage_numero = p1.pos_etage
		inner join etage e2 on e2.etage_numero = p2.pos_etage
		where e1.etage_numero<>e2.etage_numero and e1.etage_reference <> -100 and e2.etage_reference <> -100
		group by e1.etage_reference, e1.etage_numero, e1.etage_libelle, e2.etage_numero, e2.etage_libelle, e2.etage_reference
		order by min(e1.etage_reference, e2.etage_reference) desc,
			min(e1.etage_numero, e2.etage_numero) desc,
			max(e1.etage_reference, e2.etage_reference) asc,
			max(e1.etage_numero, e2.etage_numero) asc";

    $lesEtages = array();
    $nomEtages = array();
    $departs = array();

    // Récupération des données
    $db->query($requete_etages);
    while ($db->next_record())
    {
        if (!isset($lesEtages[$db->f("er1")]))
            $lesEtages[$db->f("er1")] = array();
        if (!isset($lesEtages[$db->f("er2")]))
            $lesEtages[$db->f("er2")] = array();
        if (!isset($nomEtages[$db->f("e1")]))
            $nomEtages[$db->f("e1")] = $db->f("nom1");
        if (!isset($nomEtages[$db->f("e2")]))
            $nomEtages[$db->f("e2")] = $db->f("nom2");

        if ($db->f("e1") != $db->f("er1"))
            $lesEtages[$db->f("er1")][$db->f("e1")] = $db->f("e1");
        if ($db->f("e2") != $db->f("er2"))
            $lesEtages[$db->f("er2")][$db->f("e2")] = $db->f("e2");

        if (!isset($departs[$db->f("e1")]))
            $departs[$db->f("e1")] = array();

        $departs[$db->f("e1")][$db->f("e2")] = $db->f("nb");
    }

    // Affichage des données sous forme de tableau
    foreach ($lesEtages as $etageRef => $sousEtages)
    {
        echo '<div align="center">';
        $nombre = sizeof($sousEtages);
        $lig = 0;
        $col = 0;
        $lig_milieu = 0;
        $col_milieu = 0;
        $reste = 0;
        switch ($nombre)
        {
            case 0:
                $lig = 1;
                $col = 1;
                $reste = 0;
                $lig_milieu = 0;
                $col_milieu = 0;
                break;
            case 1:
            case 2:
                $lig = 1;
                $col = 5;
                $reste = 2 - $nombre;
                $lig_milieu = 1;
                $col_milieu = 3;
                break;
            case 3:
            case 4:
            case 5:
                $lig = 3;
                $col = 5;
                $reste = 5 - $nombre;
                $lig_milieu = 3;
                $col_milieu = 3;
                break;
            case 6:
            case 7:
            case 8:
            case 9:
                $lig = 3;
                $col = 9;
                $reste = 9 - $nombre;
                $lig_milieu = 3;
                $col_milieu = 5;
                break;
            case 10:
            case 11:
            case 12:
            case 13:
            case 14:
                $lig = 5;
                $col = 9;
                $reste = 14 - $nombre;
                $lig_milieu = 3;
                $col_milieu = 5;
                break;
        }
        $l = 1;
        $c = 1;
        echo '<table>';
        if ($nombre == 0)
            echo "<tr><td id='$etageRef' class='soustitre' style='height:30px;'>" . $nomEtages[$etageRef] . '</td></tr>';
        else
        {
            foreach ($sousEtages as $etage)
            {
                $refOk = false;
                if ($c == 1)
                    echo '<tr>';
                if ($l == $lig_milieu && $c == $col_milieu)
                {
                    $refOk = true;
                    echo "<td id='$etageRef' class='soustitre' style='height:30px;'>" . $nomEtages[$etageRef] . '</td>';
                    echo '<td></td>';
                    $c += 2;
                }
                echo "<td id='$etage' class='soustitre2' style='height:30px;'>" . $nomEtages[$etage] . '</td>';
                $c++;
                if ($c > $col)
                {
                    $c = 1;
                    echo '</tr>';
                    $l++;
                    if ($l <= $lig)
                    {
                        echo '<tr>';
                        for ($i = 0; $i < $col; $i++)
                            echo '<td></td>';
                        echo '</tr>';
                        $l++;
                    }
                } else
                {
                    $c++;
                    echo '<td></td>';
                }
            }
            for ($l; $l <= $lig; $l++)
            {
                if ($c >= $col)
                    $c = 1;
                for ($c; $c <= $col; $c)
                {
                    if ($c == 1)
                        echo '<tr>';
                    if ($l == $lig_milieu && $c == $col_milieu && !$refOk)
                    {
                        echo "<td id='$etageRef' class='soustitre' style='height:30px;'>" . $nomEtages[$etageRef] . '</td>';
                        echo '<td></td>';
                        $c += 2;
                    }
                    echo '<td></td>';
                    $c++;
                    if ($c > $col)
                    {
                        echo '</tr>';
                        $l++;
                        if ($l <= $lig)
                        {
                            echo '<tr>';
                            for ($i = 0; $i < $col; $i++)
                                echo '<td></td>';
                            echo '</tr>';
                            $l++;
                        }
                    } else
                    {
                        $c++;
                        echo '<td></td>';
                    }
                }
            }
        }
        echo '</table>';
        echo '</div>';
        echo '<div style="height:20px"></div>';
    }

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";