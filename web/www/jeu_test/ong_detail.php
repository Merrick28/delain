<form name="det_cadre" method="post" action="frame_vue.php">
    <input type="hidden" name="t_frdr" value="<?php echo $t_frdr; ?>">
    <input type="hidden" name="position">
    <input type="hidden" name="dist">
    <?php
    if (isset($_POST['position']))
    {
        $position = 1 * (int)$_POST['position'];
    }

    if (isset($_GET['position']))    // Le $_REQUEST n'est ps utilisable, car il y a des données en POST et d'autres en_GET dans la même requete.
    {
        $position = 1 * (int)$_GET['position'];
    }


    $verif_connexion = new verif_connexion();
    $verif_connexion->verif();
    $perso_cod = $verif_connexion->perso_cod;
    $compt_cod = $verif_connexion->compt_cod;


    if ((!isset($position)) || ($position == ''))
    {
        $sql      = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
        $stmt     = $pdo->query($sql);
        $result   = $stmt->fetch();
        $position = $result['ppos_pos_cod'];
    }
    $req      =
        "select ppos_pos_cod,distance_vue($perso_cod) as dist from perso_position where ppos_perso_cod = $perso_cod ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $pos_actu = $result['ppos_pos_cod'];
    $d_vue = $result['dist'];
    $req = "select distance($position,$pos_actu) as dist,trajectoire_vue($position,$pos_actu) as traj  ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    if ($result['dist'] > $d_vue)
    {
        die("Position trop éloignée !");
    }
    if ($result['traj'] != 1)
    {
        die("Position non visible !");
    }

    $req = "select pos_x,pos_y,etage_libelle from positions,etage
	where pos_cod = $position
	and pos_etage = etage_numero ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    ?>
    <div class="centrer"><?php echo $result['pos_x']; ?>, <?php echo $result['pos_y']; ?>
        , <?php echo $result['etage_libelle']; ?><br>

        <?php $req = "select lieu_nom, tlieu_libelle, lieu_refuge
		from lieu
		inner join lieu_type on lieu_tlieu_cod = tlieu_cod
		inner join lieu_position on lpos_lieu_cod = lieu_cod
		where lpos_pos_cod = $position";
        $stmt = $pdo->query($req);

        if($result = $stmt->fetch())
        {
            $lieu_nom = $result['lieu_nom'];
            $tlieu_libelle = $result['tlieu_libelle'];
            $lieu_refuge = ($result['lieu_refuge'] == 'O') ? 'refuge' : 'non protégé';
            echo "$lieu_nom ($tlieu_libelle - $lieu_refuge)<br />";
        }

        //#LAG: rechercher la liste de perso sur la case
        $req = "select lower(perso_nom) as minusc,etat_perso(perso_cod) as bless,perso_nom from perso,perso_position where ppos_pos_cod = $position
		and ppos_perso_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso = 1
		order by minusc";
        $stmt = $pdo->query($req);
        ?>

        <table border="0" cellspacing="2" cellpadding="2">
            <tr>
                <td class="soustitre2" valign="top"><strong><?php echo $stmt->rowCount(); ?> persos.</strong></br>
                    <?php

                    if ($stmt->rowCount() != 0)
                    {
                        while ($result = $stmt->fetch())
                        {
                            echo $result['perso_nom'];
                            if (($result['bless'] != "indemne") && ($result['bless'] != "égratigné"))
                                echo "<em> - " . $result['bless'], "</em>";
                            echo "<br>";
                        }
                    }


                    $req = "select lower(perso_nom) as minusc,etat_perso(perso_cod) as bless,perso_nom from perso,perso_position where ppos_pos_cod = $position
		and ppos_perso_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso in (2,3)
		order by minusc";
                    $stmt = $pdo->query($req);
                    ?>
                </td>
                <td class="soustitre2" valign="top"><strong><?php echo $stmt->rowCount(); ?> monstres : </strong><br>
                    <?php
                    if ($stmt->rowCount() != 0)
                    {
                        while ($result = $stmt->fetch())
                        {
                            echo $result['perso_nom'];
                            if (($result['bless'] != "indemne") && ($result['bless'] != "égratigné"))
                                echo "<em> - " . $result['bless'], "</em>";
                            echo "<br>";
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="soustitre2">
                    <?php
                    $req = "select count(pobj_cod) as nombre from objet_position  where pobj_pos_cod = $position ";
                    $stmt = $pdo->query($req);
                    $result = $stmt->fetch();
                    echo "<strong>", $result['nombre'], "&nbsp;objets au sol</strong>";
                    if ($result['nombre'] != 0)
                    {
                        echo "<p class=\"detail\">";
                        $req = "select tobj_libelle,count(*) as nb from objets,objet_position,objet_generique,type_objet
				where pobj_pos_cod = $position 
				and pobj_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod
				group by tobj_libelle ";
                        $stmt = $pdo->query($req);
                        while ($result = $stmt->fetch())
                        {
                            echo $result['tobj_libelle'], "&nbsp;:&nbsp;", $result['nb'], "<br>";
                        }
                    }
                    ?>
                </td>
                <td class="soustitre2">
                    <?php
                    $req = "select count(por_cod) as nombre from or_position  where por_pos_cod = $position ";
                    $stmt = $pdo->query($req);
                    $result = $stmt->fetch();
                    echo "<strong>", $result['nombre'], "&nbsp;tas de brouzoufs au sol</strong>";
                    if ($result['nombre'] != 0)
                    {
                        echo "<br>";
                        $req = "select sum(por_qte) as nb from or_position  where por_pos_cod = $position ";
                        $stmt = $pdo->query($req);
                        $result = $stmt->fetch();
                        echo "<p class=\"detail\">Total&nbsp;:&nbsp;", $result['nb'], "&nbsp;brouzoufs.";
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
</form>
