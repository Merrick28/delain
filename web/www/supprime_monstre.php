<!DOCTYPE html>
<html>
<?php
include 'jeu/verif_connexion.php';

$compte = new compte;

?>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
    <title>Suppression de monstre</title>
</head>
<body background="images/fond5.gif">
<div class="bordiv">

    <?php
    if ($compte->is_admin_monstre()) {
        $pdo = new bddpdo();
        $monstres = $_POST['monstres'];
        $monstres_array = explode(';', $monstres);
        foreach ($monstres_array as $monstre) {
            $monstre_numero = sprintf("%u", $monstre);

            if ($monstre_numero != 0) {
                $requete = "select tue_perso_final(620947,perso_cod) , 
                    perso_nom from perso,perso_position,positions 
                    where perso_cod = :monstre
                      and ppos_perso_cod = perso_cod 
                      and ppos_pos_cod = pos_cod 
                      and pos_etage = -100 
                      and perso_type_perso = 2";
                $stmt = $pdo->prepare($requete);
                $stmt = $pdo->execute(array(":monstre" => $monstre_numero),$stmt);

                while ($result = $stmt->fetch()) {
                    echo 'Suppression du monstre ' . $result['perso_nom'] . ' réussie<br />';
                }
            } else {
                echo 'Numéro inconnu: ' . $monstre . '<br />';
            }
        }
    } else {
        echo 'Mauvaise idée de vouloir tricher';
    }
    ?>

</div>

</body>
</html>
