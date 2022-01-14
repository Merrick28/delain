<?php
die('Fichier non utilisé, à supprimer ?');
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
include G_CHE . "/includes/classes_monstre.php";
require_once "fonctions.php";
$pdo = new bddpdo();

?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
    <title>Login monstre</title>
</head>
<body background="images/fond5.gif">
<form name="login" method="post" action="validation_login_monstre.php" target="_top">
    <div class="bordiv">
        <p class="titre">Monstres dont la DLT n'est pas encore passée</p>
        <?php
        $req_monstre = "select dlt_passee(perso_cod) as dlt_passee,
            etat_perso(perso_cod) as etat,
            perso_cod,
            perso_nom,
            perso_pa,
            perso_pv,
            perso_pv_max,
           to_char(perso_dlt,'DD/MM/YYYY HH24:mi:ss') as dlt,
           pos_x,
           pos_y,
           pos_etage,
           (select count(dmsg_cod) from messages_dest where dmsg_perso_cod = perso_cod and dmsg_lu = 'N') as messages 
       ,perso_dirige_admin, perso_pnj 
       from perso,perso_position,positions 
       where (perso_type_perso = 2 or perso_pnj = 1) and perso_actif = 'O' 
       and ppos_perso_cod = perso_cod 
       and ppos_pos_cod = :position 
       and ppos_pos_cod = pos_cod 
       order by pos_x,pos_y,perso_nom ";
        $stmt        = $pdo->prepare($req_monstre);
        $stmt        = $pdo->execute(array(":position" => $_REQUEST['position']), $stmt);
        $allMonstre  = $stmt->fetchAll();

        $nb_monstre = count($allMonstre);
        if ($nb_monstre == 0)
        {
            echo("<p>Tous les monstres ont passé leur DLT");
        } else
        {
            echo("<table>");
            foreach ($allMonstre as $monstre)
            {
                ligne_login_monstre($monstre);
            }

            echo("</table>");
        }
        ?>

    </div>

</form>


</body>
</html>
