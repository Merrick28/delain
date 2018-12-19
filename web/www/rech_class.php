<?php
include "includes/classes.php";
$db = new base_delain;
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="css/delain.css" rel="stylesheet">
<head>
    <title>Recherche</title>
</head>
<body>
<div class="bordiv">
    <?php
    if (!isset($methode)) {
        $methode = 'debut';
    }
    switch ($methode) {
        case 'debut':
            ?>
            <form method="post" action="rech_class.php">
                <input type="hidden" name="methode" value="suite">
                <p>Entrez le nom du perso que vous voulez rechercher (vous pouvez utiliser les % pour des caractères
                    standard)
                    <input type="text" name="nom" length="50">
                    <input type="submit" class="test centrer" value="Valider !">
            </form>
            <?php
            break;
        case 'suite':
            $nom = strtolower($nom);
            $nom = pg_escape_string($nom);
            $req = "select lower(perso_nom) as minusc,perso_cod,perso_nom from perso where lower(perso_nom) like E'$nom' and perso_type_perso = 1 and perso_actif = 'O' and perso_pnj != 1 order by minusc ";
            $db->query($req);
            if ($db->nf() == 0) {
                echo "<p>Pas de personnage trouvé !<br>";
                echo "<a href=\"rech_class.php\">Retour !</a>";
            } else {
                ?>
                <p>Choisissez parmi la liste suivante :
                <form method="post" action="rech_class.php">
                    <input type="hidden" name="methode" value="fin">
                    <select name="code">
                        <?php
                        while ($db->next_record()) {
                            echo "<option value=\"", $db->f("perso_cod"), "\">", $db->f("perso_nom"), "</option>";
                        }
                        ?>
                    </select>
                    <input type="submit" class="test centrer" value="Valider !">
                </form>
                <?php
            }
            break;
        case 'fin':
            $req = "select lower(perso_nom) as minusc from perso where perso_cod = $code ";
            $db->query($req);
            $db->next_record();
            $temp_nom = pg_escape_string($db->f("minusc"));
            $req = "select count(perso_cod) as nombre from perso where lower(perso_nom) < E'$temp_nom' and perso_actif = 'O' and perso_type_perso = 1  and perso_pnj != 1 ";
            $db->query($req);
            $db->next_record();
            $nombre = $db->f("nombre");
            $offset = (floor($nombre / 20) * 20);
            echo "<p><a href=\"classement_v2.php?sort=nom&sens=asc&debut=$offset\">Accéder au résultat</a>";
            break;
    }

    ?>
</div>
</body>
</html>
