<?php
include "includes/classes.php";
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="css/delain.less" rel="stylesheet/less" type="text/css"/>
<head>
    <title>Recherche</title>
</head>
<body>
<div class="bordiv">
    <?php

    $methode = get_request_var('methode','debut');
    switch ($methode)
    {
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
            $req =
                "select lower(perso_nom) as minusc,
                        perso_cod,
                        perso_nom 
                from perso 
                where lower(perso_nom) 
                like :nom and perso_type_perso = 1 
                and perso_actif = 'O' and perso_pnj != 1 
                order by minusc ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":nom" => '%' . $_REQUEST['nom'] . '%'), $stmt);
            $allperso = $stmt->fetchAll();
            if (count($allperso) == 0)
            {
                echo "<p>Pas de personnage trouvé !<br>";
                echo "<a href=\"rech_class.php\">Retour !</a>";
            } else
            {
                ?>
                <p>Choisissez parmi la liste suivante :
                <form method="post" action="rech_class.php">
                    <input type="hidden" name="methode" value="fin">
                    <select name="code">
                        <?php
                        foreach ($allperso as $detailperso)
                        {
                            echo "<option value=\"", $detailperso['perso_cod'], "\">", $detailperso['perso_nom'], "</option>";
                        }
                        ?>
                    </select>
                    <input type="submit" class="test centrer" value="Valider !">
                </form>
                <?php
            }
            break;
        case 'fin':
            $req    = "select lower(perso_nom) as minusc from perso where perso_cod = :code ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":code" => $_REQUEST['code']), $stmt);
            $result = $stmt->fetch();

            $temp_nom = $result['minusc'];
            $req      =
                "select count(perso_cod) as nombre 
                    from perso where lower(perso_nom) < :nom 
                    and perso_actif = 'O' and perso_type_perso = 1  and perso_pnj != 1 ";
            $stmt     = $pdo->prepare($req);
            $stmt     = $pdo->execute(array(":nom" => $temp_nom), $stmt);
            $result = $stmt->fetch();

            $nombre = $result['nombre'];
            $offset = (floor($nombre / 20) * 20);
            echo "<p><a href=\"classement_v2.php?sort=nom&sens=asc&debut=$offset\">Accéder au résultat</a>";
            break;
    }

    ?>
</div>
</body>
<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js"></script>
</html>
