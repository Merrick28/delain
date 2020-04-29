<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
?>
<!DOCTYPE html>
<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="https://www.jdr-delain.net//css/delain.less" rel="stylesheet/less" type="text/css"/>
<head>
    <title>Don de poisson</title>
</head>

<body background="../images/fond5.gif">

<?php
$req_pos = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
$stmt = $pdo->query($req_pos);
$result = $stmt->fetch();
$pos_actuelle = $result['ppos_pos_cod'];
$req_vue = "select perso_cod,perso_nom,lower(perso_nom) as minusc from perso, perso_position where ppos_pos_cod = $pos_actuelle and ppos_perso_cod = perso_cod and perso_cod != $perso_cod  and perso_type_perso in (1,2) and perso_actif = 'O' order by minusc";
$stmt = $pdo->query($req_vue);
if ($stmt->rowCount() == 0) {
    echo "<p>Il n'y a pas de joueur à qui vous pouvez donner ce poisson !";
} else {
    ?>
    <form name="cree_transaction" method="post" action="action.php">
        <input type="hidden" name="methode" value="donne_poisson">
        <input type="hidden" name="obj" value="<?php echo $obj; ?>">
        <p>
        <div class="centrer">Choisissez le perso à qui vous souhaitez donner ce poisson : <select name="perso">
                <?php
                while ($result = $stmt->fetch()) {
                    printf("<option value=\"%s\">%s</option>", $result['perso_cod'], $result['perso_nom']);
                }
                ?>
            </select><br>
            <input type="submit" class="test" value="Donner le poisson !"></div>
    </form>
    <?php
}

?>
</body>
<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js"></script>
</html>