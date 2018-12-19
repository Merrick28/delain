<?php
include "verif_connexion.php";
?>
<!DOCTYPE html>
<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="https://www.jdr-delain.net//css/delain.css" rel="stylesheet">
<head>
    <title>Don de poisson</title>
</head>

<body background="../images/fond5.gif">
<div class="bordiv">
    <?php
    $req_pos = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
    $db->query($req_pos);
    $db->next_record();
    $pos_actuelle = $db->f("ppos_pos_cod");
    $req_vue = "select perso_cod,perso_nom,lower(perso_nom) as minusc from perso, perso_position where ppos_pos_cod = $pos_actuelle and ppos_perso_cod = perso_cod and perso_cod != $perso_cod  and perso_type_perso in (1,2) and perso_actif = 'O' order by minusc";
    $db->query($req_vue);
    if ($db->nf() == 0)
    {
        echo "<p>Il n'y a pas de joueur à qui vous pouvez donner ce poisson !";
    } else
    {
        ?>
        <form name="cree_transaction" method="post" action="action.php">
            <input type="hidden" name="methode" value="donne_poisson">
            <input type="hidden" name="obj" value="<?php echo $obj; ?>">
            <p>
            <div class="centrer">Choisissez le perso à qui vous souhaitez donner ce poisson : <select name="perso">
                    <?php
                    while ($db->next_record())
                    {
                        printf("<option value=\"%s\">%s</option>", $db->f("perso_cod"), $db->f("perso_nom"));
                    }
                    ?>
                </select><br>
                <input type="submit" class="test" value="Donner le poisson !"></div>
        </form>
        <?php
    }

    ?>
</div>
</body>
</html>