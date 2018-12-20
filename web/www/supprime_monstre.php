<!DOCTYPE html>
<html>
<?php
include 'jeu/verif_connexion.php';
page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
?>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
    <title>Suppression de monstre</title>
</head>
<body background="images/fond5.gif">
<div class="bordiv">

    <?php
    if ($db->is_admin_monstre($compt_cod)) {
        $monstres = $_POST['monstres'];
        $monstres_array = explode(';', $monstres);
        foreach ($monstres_array as $monstre) {
            $monstre_numero = sprintf("%u", $monstre);

            if ($monstre_numero != 0) {
                $db = new base_delain;
                $requete = "select tue_perso_final(620947,perso_cod) , perso_nom from perso,perso_position,positions where perso_cod = $monstre_numero and ppos_perso_cod = perso_cod and ppos_pos_cod = pos_cod and pos_etage = -100 and perso_type_perso = 2";
//             $requete = "select perso_cod,perso_nom from perso,perso_position,positions where perso_cod = $monstre_numero and ppos_perso_cod = perso_cod and ppos_pos_cod = pos_cod and pos_etage = 6 and perso_type_perso = 2";
                $db->query($requete);
                while ($db->next_record()) {
                    echo 'Suppression du monstre ' . $db->f('perso_nom') . ' réussie<br />';
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
