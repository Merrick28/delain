<?php
include "blocks/_tests_appels_page_externe.php";


// on regarde si le joueur est bien sur une banque
$erreur = 0;
$perso  = new perso;
$perso->charge($perso_cod);
if (!$perso->is_lieu())
{
    echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
    $erreur = 1;
}
if ($erreur == 0)
{
    $tab_lieu = $perso->get_lieu();
    if ($tab_lieu['lieu']->lieu_tlieu_cod != 2139)
    {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
    }
}

// on cherche le lieu cod
$req    = "select lpos_lieu_cod from lieu_position where lpos_pos_cod =  
(select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod) ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
$lieu   = $result['lpos_lieu_cod'];

// on active pour le retour
$req  = "select pge_perso_cod from perso_grand_escalier where pge_perso_cod = $perso_cod 
and pge_lieu_cod = $lieu ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
    $erreur         = 1;
    $methode        = get_request_var('methode', 'debut');
    switch ($methode)
    {
        case "debut":
            echo "<p>La porte vous est fermée pour le moment. Etant donné son épaisseur, et les nombreux systèmes de verouillages que vous y voyez, inutile d'essayer de la forcer.<br>";
            echo "<p>Vous pouvez essayer <a href=\"", $_SERVER['PHP_SELF'], "?methode=appeler\">d'appeler le geolier</a> ou bien <a href=\"", $_SERVER['PHP_SELF'], "?methode=corrompre\">de corromopre celui-ci.</a>";
            break;
        case "appeler":
            ?>
            <p>Vous vous apprêtez à héler le geolier au moment où celui-ci passe.<br>
            <p>Quel message souhaitez vous lui envoyer ?<br>
            <form name="appeler" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="appel2">
                <textarea name="corps" cols="40" rows="10"></textarea><br>
                <input class="test centrer" type="submit" value="Envoyer !">
            </form>
            <?php
            break;
        case "appel2":
            // numéro du message
            $message             = new message();
            $message->sujet      = "[Appel de prisonnier]";
            $message->corps      = $_REQUEST['corps'];
            $message->expediteur = $perso_cod;
            $req_msg_cod         = "select nextval('seq_msg_cod') as numero";
            $stmt                = $pdo->query($req_msg_cod);
            $result              = $stmt->fetch();
            $num_mes             = $result['numero'];
            // encodage du texte

            // destinataires

            $req  = "select  perso_cod
            from perso,guilde_perso 
            where perso_actif = 'O' 
            and pguilde_perso_cod = perso_cod 
            and pguilde_guilde_cod = 49 
            and pguilde_valide = 'O'
            and pguilde_rang_cod = 16 ";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $message->ajouteDestinataire($result['perso_cod']);
            }
            $message->envoieMessage();
            echo "<p>Votre message a bien été envoyé. Le geolier en prendra connaissance dès que possible.";
            break;
        case "corrompre":
            $req = "select perso_po from perso where perso_cod = $perso_cod ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            $or     = $result['perso_po'];
            ?>
            <p>Vous vous apprêtez à donner au geolier quelques brouzoufs afin de le corrompre.<br>
            <p>Vous avez actuellement <strong><?php echo $or; ?></strong> brouzoufs disponibles.
            <p>Quelle quantité souhaitez vous lui donner ?<br>
            <form name="appeler" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="corrompre2">
                <input type="text" name="or" value="0"><br>
                <input class="test centrer" type="submit" value="Envoyer !">
            </form>
            <?php
            break;
        case "corrompre2":
            $req    = "select corrompre($perso_cod,$or) as resultat ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>", $result['resultat'];
            break;
    }

} else
{
    $nom_lieu  = $tab_lieu['lieu']->lieu_nom;
    $desc_lieu = $tab_lieu['lieu']->lieu_description;
    echo("<p><strong>$nom_lieu</strong><br>$desc_lieu ");
    echo("<p><a href=\"valide_porte_prison.php\">Sortir de cette prison ? (4 PA)</a></p>");
}
