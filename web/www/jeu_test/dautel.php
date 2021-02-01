<?php
$perso = $verif_connexion->perso;
$param = new parametres();
//
//Contenu de la div de droite
//
$contenu_page = '';
//
// on regarde si le joueur est bien sur le lieu qu'on attend
//
$erreur  = 0;
$methode = get_request_var('methode', 'entree');

$type_lieu = 33;
$nom_lieu  = 'un autel de prière';

define('APPEL', 1);
include "blocks/_test_lieu.php";

if ($perso->perso_type_perso == 3)
{
    $erreur = 1;
    echo("<p>Les familiers ne font pas bon usage d'un autel de prière.");
}


//
// OK, tout est bon, on s'attaque à la suite
//
if ($erreur == 0)
{
    $req  = 'select dper_dieu_cod,dniv_libelle,dieu_nom,dper_niveau,dper_points
				from dieu_perso,dieu_niveau,dieu 
                where dper_perso_cod = :perso_cod 
				and dniv_dieu_cod = dper_dieu_cod 
                and dniv_niveau = dper_niveau
				and dieu_cod = dper_dieu_cod ';
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);


    if ($stmt->rowCount() != 0)
    {
        $result      = $stmt->fetch();
        $niveau_actu = $result['dper_niveau'];
        $dieu_perso  = $result['dper_dieu_cod'];
    }
    $lieu           = $tab_lieu['lieu_cod'];
    $req            =
        "select dieu_cod,dieu_nom,dieu_description,lieu_description,dieu_ceremonie,dieu_pouvoir from dieu,lieu 
        where lieu_cod = :lieu_cod and lieu_dieu_cod = dieu_cod ";
    $stmt           = $pdo->prepare($req);
    $stmt           = $pdo->execute(array(":lieu_cod" => $tab_lieu['lieu_cod']), $stmt);
    $result         = $stmt->fetch();
    $dieu_cod       = $result['dieu_cod'];
    $dieu_nom       = $result['dieu_nom'];
    $dieu_descr     = $result['dieu_description'];
    $lieu_descr     = $result['lieu_description'];
    $dieu_ceremonie = $result['dieu_ceremonie'];
    $dieu_pouvoir   = $result['dieu_pouvoir'];
    switch ($methode)
    {
        case 'entree':
            // on cherche d'abord le dieu associé.
            echo "<p><img src=\"../images/temple.png\"><br />
			Vous êtes devant un autel dédié à <strong>", $dieu_nom, "</strong><br>";
            echo "<br><br><em>", $lieu_descr, "</em><br>";

            // on regarde s'il existe un lien avec le perso
            $req  = 'select dper_dieu_cod,dniv_libelle,dieu_nom,dper_niveau,dper_points
				from dieu_perso,dieu_niveau,dieu where dper_perso_cod = :perso_cod
				and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau
				and dieu_cod = dper_dieu_cod ';
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            if ($stmt->rowCount() == 0)
            {
                // aucun rattachement
                echo "<p>Vous n'êtes fidèle d'aucun dieu ";
            } else
            {
                $result         = $stmt->fetch();
                $perso_dieu_nom = $result['dieu_nom'];
                if ($result['dper_dieu_cod'] == $dieu_cod)
                {
                    echo "<p>Vous êtes ", $result['dniv_libelle'], " de ce dieu";
                    $points      = $result['dper_points'];
                    $niveau_actu = $result['dper_niveau'];
                    $cout_pa     = $param->getparm(55);

                    // Bénédiction
                    if ($niveau_actu >= 2)
                        echo '<p><a href="' . $PH_SELF . '?methode=benediction">Demander une bénédiction (' . $param->getparm(110) . ' PA) </a>';
                } else
                {
                    echo "<p>Vous êtes ", $result['dniv_libelle'], " de " . $perso_dieu_nom;
                }
            }
            // on regarde si on n'est pas rénégat, quand même.
            $req  =
                "select dren_cod from dieu_renegat where dren_dieu_cod = :dieu_cod and dren_perso_cod = :perso_cod and dren_datfin > now()";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso_cod" => $perso_cod,
                                        ":dieu_cod"  => $dieu_cod), $stmt);
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
            $req    =
                "select dper_dieu_cod,dniv_libelle from dieu_perso,dieu_niveau where dper_perso_cod = :perso_cod 
                and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
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
                if ($perso->perso_pa < $param->getparm(110))
                {
                    echo "<p>Vous n'avez pas assez de PA pour cette action.";
                    break;

                }
                //
                // à partir d'ici, tout devrait être bon.
                //
                require "blocks/_dautel_dtemple.php";
            }
            break;
    }
}

