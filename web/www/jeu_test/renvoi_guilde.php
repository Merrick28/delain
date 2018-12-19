<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <div class="bordiv">
        <?php
        $erreur = 0;
        if (!isset($vperso)) {
            $vperso = $_POST['vperso'];
        }
        if (!isset($num_guilde)) {
            $num_guilde = $_POST['num_guilde'];
        }
        $req = "select rguilde_admin from guilde_perso,guilde_rang
								where pguilde_perso_cod = $vperso
								and pguilde_guilde_cod = rguilde_guilde_cod
								and pguilde_rang_cod = rguilde_rang_cod ";

        $db->query($req);
        $db->next_record();
        if ($db->f("rguilde_admin") == 'O') {
            echo "<p>Erreur ! Vous ne pouvez pas renvoyer un admin !";

            die('</div>');
        }
        if ($db->is_revolution($num_guilde)) {
            echo "<p>Vous ne pouvez pas intervenir dans la gestion de la guilde pendant une révolution !";

            die('</div>');
        }
        $req1 = "select guilde_nom from guilde where guilde_cod = $num_guilde";
        $db->query($req1);
        $db->next_record();
        $ancienne_guilde = $db->f("guilde_nom");
        $ancienne_guilde = "[Ancien membre de la guilde " . pg_escape_string($ancienne_guilde) . "]";

        if (!isset($methode)) {
            $methode = 'debut';
        }
        switch ($methode) {
            case 'debut':
                echo "Etes-vous sûr de vouloir virer ce membre de votre guilde ?
				<br><a href=\"" . $PHP_SELF . "?methode=validation&vperso=" . $vperso . "&num_guilde=" . $num_guilde . "\">Oui !</a>
				<br><br><a href=\"admin_guilde.php\">Non, retourner à l'administration de la guilde</a>";
                break;
            case 'validation':
                $req = "insert into perso_titre values(default,$vperso,e'$ancienne_guilde',now(),'2')";
                $db->query($req);
                $db->next_record();
                $req = "delete from guilde_perso where pguilde_guilde_cod = $num_guilde and pguilde_perso_cod = $vperso ";
                $res = pg_exec($dbconnect, $req);
                if (!$res) {
                    echo("<p>Une erreur est survenue !");
                } else {
                    $texte = "Vous avez été renvoyé de votre guilde par l\'administrateur de celle-ci.<br />";
                    $titre = "Renvoi de guilde.";
                    $req_num_mes = "select nextval('seq_msg_cod')";
                    $res_num_mes = pg_exec($dbconnect, $req_num_mes);
                    $tab_num_mes = pg_fetch_array($res_num_mes, 0);
                    $num_mes = $tab_num_mes[0];
                    $req_mes = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2) ";
                    $req_mes = $req_mes . "values ($num_mes, now(), '$titre', '$texte', now()) ";
                    $res_mes = pg_exec($dbconnect, $req_mes);
                    // on renseigne l'expéditeur
                    $req2 = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
                    $req2 = $req2 . "values ($num_mes,1,'N') ";
                    $res2 = pg_exec($dbconnect, $req2);
                    $req_dest = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes,$vperso,'N','N') ";
                    $res_dest = pg_exec($dbconnect, $req_dest);
                    ?>
                    <p>Le personnage a été supprimé de votre guilde.
                    <p><a href="admin_guilde.php">Retourner à l'administration de la guilde</a></p>
                    <?php
                }
                break;
        }


        $close = pg_close($dbconnect);
        ?>
    </div>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
