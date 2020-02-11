<?php
include "blocks/_tests_appels_page_externe.php";

?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="../css/delain.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
    <title>Escalier fermé</title>
</head>
<body background="../images/fond5.gif">
<div class="bordiv">
    <?php

    $type_lieu = 3;
    $nom_lieu = 'un escalier';

    include "blocks/_test_lieu.php";

    if ($erreur == 0) {
        $nb_depose = 0;
        echo "<form name=\"escalier\" method=\"post\" action=\"valide_escalier_ferme.php\">";
        echo "<input type=\"hidden\" name=\"objet\">";
        echo "<p>Cet escalier est fermé par une épaisse plaque de marbre. Dessus est gravé :<br>";
        echo "<em>Pour m'ouvrir, ramène ici les symboles du loup, du scorpion et du serpent.</em>";
        // partie du loup
        // on regarde si le médaillon est déposé
        $req = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 86 ";
        $stmt = $pdo->query($req);
        if ($stmt->rowCount() != 0) {
            echo "<p>Le médaillon du Loup a déjà été déposé.";
            $nb_depose = $nb_depose + 1;
        } else {
            if ($db->compte_objet($perso_cod, 86) != 0) {
                echo "<p>Vous possédez le médaillon du Loup. ";
                echo "<a href=\"javascript:document.escalier.objet.value=86;document.escalier.submit();\">Le déposer sur l'escalier ?</a>";
            } else // on recherche sa position
            {
                echo "<p>";
                $req = "select pos_x,pos_y,etage_libelle ";
                $req = $req . "from objets,objet_position,positions,etage ";
                $req = $req . "where obj_gobj_cod = 86 ";
                $req = $req . "and pobj_obj_cod = obj_cod ";
                $req = $req . "and pobj_pos_cod = pos_cod ";
                $req = $req . "and pos_etage = etage_numero ";
                $stmt = $pdo->query($req);
                if ($stmt->rowCount() != 0) {
                    while ($result = $stmt->fetch()) {
                        $x = $result['pos_x'];
                        $y = $result['pos_y'];
                        $etage = $result['etage_libelle'];
                        echo "Un médaillon du Loup se trouve en $x, $y dans le lieu $etage <br>";
                    }
                } else {
                    $req = "select pos_x,pos_y,etage_libelle ";
                    $req = $req . "from objets,perso_objets,positions,etage,perso_position,perso ";
                    $req = $req . "where obj_gobj_cod = 86 ";
                    $req = $req . "and perobj_obj_cod = obj_cod ";
                    $req = $req . "and perobj_perso_cod = ppos_perso_cod ";
                    $req = $req . "and perobj_perso_cod = perso_cod ";
                    $req = $req . "and perso_actif = 'O' ";
                    $req = $req . "and ppos_pos_cod = pos_cod ";
                    $req = $req . "and pos_etage = etage_numero ";
                    $stmt = $pdo->query($req);
                    while ($result = $stmt->fetch()) {
                        $x = $result['pos_x'];
                        $y = $result['pos_y'];
                        $etage = $result['etage_libelle'];
                        echo "Un médaillon du Loup se trouve en $x, $y dans le lieu $etage <br>";
                    }
                }

            }
        }

        // partie du scorpion
        // on regarde si le médaillon est déposé
        $req = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 87 ";
        $stmt = $pdo->query($req);
        if ($stmt->rowCount() != 0) {
            echo "<p>Le médaillon du Scorpion a déjà été déposé.";
            $nb_depose = $nb_depose + 1;
        } else {
            if ($db->compte_objet($perso_cod, 87) != 0) {
                echo "<p>Vous possédez le médaillon du Scorpion. ";
                echo "<a href=\"javascript:document.escalier.objet.value=87;document.escalier.submit();\">Le déposer sur l'escalier ?</a>";
            } else // on recherche sa position
            {
                echo "<p>";
                $req = "select pos_x,pos_y,etage_libelle ";
                $req = $req . "from objets,objet_position,positions,etage ";
                $req = $req . "where obj_gobj_cod = 87 ";
                $req = $req . "and pobj_obj_cod = obj_cod ";
                $req = $req . "and pobj_pos_cod = pos_cod ";
                $req = $req . "and pos_etage = etage_numero ";
                $stmt = $pdo->query($req);
                if ($stmt->rowCount() != 0) {
                    while ($result = $stmt->fetch()) {
                        $x = $result['pos_x'];
                        $y = $result['pos_y'];
                        $etage = $result['etage_libelle'];
                        echo "Un médaillon du Scorpion se trouve en $x, $y dans le lieu $etage <br>";
                    }
                } else {
                    $req = "select pos_x,pos_y,etage_libelle ";
                    $req = $req . "from objets,perso_objets,positions,etage,perso_position,perso ";
                    $req = $req . "where obj_gobj_cod = 87 ";
                    $req = $req . "and perobj_obj_cod = obj_cod ";
                    $req = $req . "and perobj_perso_cod = ppos_perso_cod ";
                    $req = $req . "and perobj_perso_cod = perso_cod ";
                    $req = $req . "and perso_actif = 'O' ";
                    $req = $req . "and ppos_pos_cod = pos_cod ";
                    $req = $req . "and pos_etage = etage_numero ";
                    $stmt = $pdo->query($req);
                    while ($result = $stmt->fetch()) {
                        $x = $result['pos_x'];
                        $y = $result['pos_y'];
                        $etage = $result['etage_libelle'];
                        echo "Un médaillon du Scorpion se trouve en $x, $y dans le lieu $etage <br>";
                    }
                }

            }
        }


        // partie du Serpent
        // on regarde si le médaillon est déposé
        $req = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 88 ";
        $stmt = $pdo->query($req);
        if ($stmt->rowCount() != 0) {
            echo "<p>Le médaillon du Serpent a déjà été déposé.";
            $nb_depose = $nb_depose + 1;
        } else {
            if ($db->compte_objet($perso_cod, 88) != 0) {
                echo "<p>Vous possédez le médaillon du Serpent. ";
                echo "<a href=\"javascript:document.escalier.objet.value=88;document.escalier.submit();\">Le déposer sur l'escalier ?</a>";
            } else // on recherche sa position
            {
                echo "<p>";
                $req = "select pos_x,pos_y,etage_libelle ";
                $req = $req . "from objets,objet_position,positions,etage ";
                $req = $req . "where obj_gobj_cod = 88 ";
                $req = $req . "and pobj_obj_cod = obj_cod ";
                $req = $req . "and pobj_pos_cod = pos_cod ";
                $req = $req . "and pos_etage = etage_numero ";
                $stmt = $pdo->query($req);
                if ($stmt->rowCount() != 0) {
                    while ($result = $stmt->fetch()) {
                        $x = $result['pos_x'];
                        $y = $result['pos_y'];
                        $etage = $result['etage_libelle'];
                        echo "Un médaillon du Serpent se trouve en $x, $y dans le lieu $etage <br>";
                    }
                } else {
                    $req = "select pos_x,pos_y,etage_libelle ";
                    $req = $req . "from objets,perso_objets,positions,etage,perso_position,perso ";
                    $req = $req . "where obj_gobj_cod = 88 ";
                    $req = $req . "and perobj_obj_cod = obj_cod ";
                    $req = $req . "and perobj_perso_cod = ppos_perso_cod ";
                    $req = $req . "and perobj_perso_cod = perso_cod ";
                    $req = $req . "and perso_actif = 'O' ";
                    $req = $req . "and ppos_pos_cod = pos_cod ";
                    $req = $req . "and pos_etage = etage_numero ";
                    $stmt = $pdo->query($req);
                    while ($result = $stmt->fetch()) {
                        $x = $result['pos_x'];
                        $y = $result['pos_y'];
                        $etage = $result['etage_libelle'];
                        echo "Un médaillon du Serpent se trouve en $x, $y dans le lieu $etage <br>";
                    }
                }
            }

        }
        echo "</form>";
        if ($nb_depose == 3) {
            echo "<p><strong>Les 3 médaillons ont été déposés ! </strong><a href=\"ouvre_escalier.php\">Ouvrir l'escalier ?</a><br><br>";
        }
    }
    ?>
</div>
</body>
</html>
