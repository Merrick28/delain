<?php
include "blocks/_tests_appels_page_externe.php";

$param = new parametres();
//
//Contenu de la div de droite
//
$contenu_page = '';
//
// on regarde si le joueur est bien sur le lieu qu'on attend
//
$erreur = 0;
$methode          = get_request_var('methode', 'entree');

$type_lieu = 33;
$nom_lieu = 'un autel de prière';

include "blocks/_test_lieu.php";

$req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
$stmt = $pdo->query($req);
$result = $stmt->fetch();
if ($result['perso_type_perso'] == 3)
{
    $erreur = 1;
    echo("<p>Les familiers ne font pas bon usage d'un autel de prière.");
}


//
// OK, tout est bon, on s'attaque à la suite
//
if ($erreur == 0)
{
    $req = 'select dper_dieu_cod,dniv_libelle,dieu_nom,dper_niveau,dper_points
				from dieu_perso,dieu_niveau,dieu where dper_perso_cod = ' . $perso_cod . '
				and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau
				and dieu_cod = dper_dieu_cod ';
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
    {
        $result = $stmt->fetch();
        $niveau_actu = $result['dper_niveau'];
        $dieu_perso = $result['dper_dieu_cod'];
    }
    $lieu = $tab_lieu['lieu_cod'];
    $req = "select dieu_cod,dieu_nom,dieu_description,lieu_description,dieu_ceremonie,dieu_pouvoir from dieu,lieu ";
    $req = $req . "where lieu_cod = " . $tab_lieu['lieu_cod'] . " and lieu_dieu_cod = dieu_cod ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $dieu_cod = $result['dieu_cod'];
    $dieu_nom = $result['dieu_nom'];
    $dieu_descr = $result['dieu_description'];
    $lieu_descr = $result['lieu_description'];
    $dieu_ceremonie = $result['dieu_ceremonie'];
    $dieu_pouvoir = $result['dieu_pouvoir'];
    switch ($methode)
    {
        case 'entree':
            // on cherche d'abord le dieu associé.
            echo "<p><img src=\"../images/temple.png\"><br />
			Vous êtes devant un autel dédié à <strong>", $dieu_nom, "</strong><br>";
            echo "<br><br><em>", $lieu_descr, "</em><br>";

            // on regarde s'il existe un lien avec le perso
            $req = 'select dper_dieu_cod,dniv_libelle,dieu_nom,dper_niveau,dper_points
				from dieu_perso,dieu_niveau,dieu where dper_perso_cod = ' . $perso_cod . '
				and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau
				and dieu_cod = dper_dieu_cod ';
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                // aucun rattachement
                echo "<p>Vous n'êtes fidèle d'aucun dieu ";
            } else
            {
                $result = $stmt->fetch();
                $perso_dieu_nom = $result['dieu_nom'];
                if ($result['dper_dieu_cod'] == $dieu_cod)
                {
                    echo "<p>Vous êtes ", $result['dniv_libelle'], " de ce dieu";
                    $points = $result['dper_points'];
                    $niveau_actu = $result['dper_niveau'];
                    $cout_pa = $param->getparm(55);

                    // Bénédiction
                    if ($niveau_actu >= 2)
                        echo '<p><a href="' . $PH_SELF . '?methode=benediction">Demander une bénédiction (' . $param->getparm(110) . ' PA) </a>';
                } else
                {
                    echo "<p>Vous êtes ", $result['dniv_libelle'], " de " . $perso_dieu_nom;
                }
            }
            // on regarde si on n'est pas rénégat, quand même.
            $req = "select dren_cod from dieu_renegat where dren_dieu_cod = $dieu_cod and dren_perso_cod = $perso_cod and dren_datfin > now()";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() != 0)
            {
                // RENEGAT !!!
                echo "<p>Vous êtes <strong>renégat</strong> !! Inutile de s'attarder en ce lieu, $dieu_nom ne veut même pas entendre parler de vous !";
            } else
            {
                ?>
                <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=prie1">- Je voudrais me recueillir pour
                        prier <?php echo $dieu_nom; ?></a> (<?php echo $param->getparm(48); ?> PA)</p>
                <?php
            }
            break;

        case 'prie1':
            $attention = 0;
            $req = "select dper_dieu_cod,dniv_libelle from dieu_perso,dieu_niveau where dper_perso_cod = $perso_cod ";
            $req = $req . "and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau ";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() != 0)
            {
                $result = $stmt->fetch();
                if ($result['dper_dieu_cod'] != $dieu_cod)
                {
                    $attention = 1;
                }
            }
            if ($attention == 0)
            {
                ?>
                <p>Vous vous apprêtez à prier <strong><?php echo $dieu_nom ?></strong><br>
                <a href="action.php?methode=prie&dieu=<?php echo $dieu_cod; ?>">Continuer ?</a>
                <?php
            } else
            {
                echo "<p>Vous êtes ", $result['dniv_libelle'], " d'un autre dieu. Inutile de s'attarder en ce lieu.<br>";
            }
            break;

        case 'benediction':
            if ($niveau_actu >= 2)
            {
                //
                // on commence par regarder si le dieu en question a assez de puissance pour accorder la bénédiction
                //
                $point_ben = ($niveau_actu - 2) * ($niveau_actu - 2);
                if ($points_ben < 2)
                    $points_ben = 2;
                if ($dieu_pouvoir < $points_ben)
                {
                    echo '<p>Votre Divinité n\'a pas assez de puissance pour exaucer votre souhait !';
                    break;
                }
                //
                // on fait quand même un contrôle de cohérence divinité/perso
                //
                if ($dieu_perso != $dieu_cod)
                {
                    echo "Vous ne pouvez demander des bénédictions que sur un lieu dédié à votre divinité.";
                    break;
                }
                //
                // on contrôle les PA
                //
                $req = 'select perso_pa from perso where perso_cod = ' . $perso_cod;
                $stmt = $pdo->query($req);
                $result = $stmt->fetch();
                $pa = $result['perso_pa'];
                if ($pa < $param->getparm(110))
                {
                    echo "<p>Vous n'avez pas assez de PA pour cette action.";
                    break;

                }
                //
                // à partir d'ici, tout devrait être bon.
                //
                $nb_tours = $niveau_actu * $niveau_actu;
                // on commence par enlever les points au dieu
                $req = "update dieu set dieu_pouvoir = dieu_pouvoir - " . $points_ben . " where dieu_cod = " . $dieu_cod;
                $stmt = $pdo->query($req);
                // on enlève les PA
                $req = 'update perso set perso_pa = perso_pa - getparm_n(110) where perso_cod = ' . $perso_cod;
                $stmt = $pdo->query($req);
                // et maintenant, on va switcher par rapport à la divinité.
                switch ($dieu_cod)
                {
                    case 1: // IO l'aveugle => chant du barde
                        $req = "select ajoute_bonus($perso_cod,'TOU', $nb_tours, 25)";
                        $stmt = $pdo->query($req);
                        $req = "select ajoute_bonus($perso_cod,'PAA', $nb_tours, -1)";
                        $stmt = $pdo->query($req);
                        $req = "select ajoute_bonus($perso_cod,'DEG', $nb_tours, 2)";
                        $stmt = $pdo->query($req);
                        $texte = "Io vous a entendu, vous vous sentez plus habile au combat.";
                        break;
                    case 2: // Balgur => soins importants
                        $req = "select perso_pv,perso_pv_max from perso
							where perso_cod = " . $perso_cod;
                        $stmt = $pdo->query($req);
                        $result = $stmt->fetch();
                        $v_pv = $result['perso_pv'];
                        $v_pv_max = $result['perso_pv_max'];
                        $pv_gagne = 0;
                        for ($i = 0; $i < 8; $i++)
                        {
                            $req = "select lancer_des(1,4) as resultat";
                            $stmt = $pdo->query($req);
                            $result = $stmt->fetch();
                            $pv_gagne += $result['resultat'];
                        }
                        $tot_pv = $v_pv + $pv_gagne;
                        if ($tot_pv > $v_pv_max)
                            $tot_pv = $v_pv_max;
                        $req = "update perso set perso_pv = " . $tot_pv . " where perso_cod = " . $perso_cod;
                        $stmt = $pdo->query($req);
                        $texte = "Balgur soigne vos blessures... ";
                        break;
                    case 3: // Galthée => reconstruction intense
                        $req = "select ajoute_bonus($perso_cod,'REG', $nb_tours, 10)";
                        $stmt = $pdo->query($req);
                        $texte = "Dans sa grande bonté, Galthée aide votre corps à se reconstruire.";
                        break;
                    case 4: // Elian => sort de défense
                        $req = "select ajoute_bonus($perso_cod,'ARM', $nb_tours, 2)";
                        $stmt = $pdo->query($req);
                        $texte = "Elian rend votre corps plus résistant aux blessures.";
                        break;
                    case 5: // Apiera => armure dure
                        $req = "select ajoute_bonus($perso_cod,'ARM', $nb_tours, 5)";
                        $stmt = $pdo->query($req);
                        $texte = "Apiera rend votre corps plus résistant aux blessures.";
                        break;
                    case 7:    // FAlis => Fou furieux
                        $req = "select ajoute_bonus($perso_cod,'PAA', $nb_tours, -3)";
                        $stmt = $pdo->query($req);
                        $texte = "Falis vous entend, et répond à votre demande de bénédiction. Vous vous sentez plus rapide au combat.";
                        break;
                    case 8: // Ecatis => Mange ta soupe
                        $req = "select ajoute_bonus($perso_cod,'TOU', $nb_tours, 25)";
                        $stmt = $pdo->query($req);
                        $texte = "Ecatis vous a entendu, vous vous sentez plus habile au combat.";
                        break;
                    case 9: // Tonto => Danse de Saint Guy
                        $req = "select ajoute_bonus($perso_cod,'ESQ', $nb_tours, 25)";
                        $stmt = $pdo->query($req);
                        $req = "select ajoute_bonus($perso_cod,'DSG', $nb_tours, 20)";
                        $stmt = $pdo->query($req);
                        $texte = "Tonto vous enseigne la technique de l'homme ivre.";
                        break;
                }
                echo $texte;
            }
            break;
    }
}

