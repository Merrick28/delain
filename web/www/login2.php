<?php
//echo G_CHE;
include G_CHE . "includes/constantes.php";
include G_CHE . "includes/classes.php";
include "ident.php";
if (!$verif_auth)
{
    header('Location:' . $type_flux . G_URL . 'inter.php');
}
$db = new base_delain;
$req2 = "select count(sid) as nombre from sessions_active where changed >= to_char((now()-'5 minutes'::interval),'YYYYMMDDHH24MISS')";
$db->query($req2);
$db->next_record();
$nombre = $db->f("nombre");
if ($nombre > 120)
{
    ?>
    <div class="bordiv">
        <p>Il y a <?php echo("$nombre"); ?> connecté(s) sur Delain en ce moment.</p>
        <p>En raison d'une surcharge du serveur, les connexions simultanées sont limitées.<br>Merci de réessayer
            ultérieurement.
        <p>Merci de votre compréhension.

    </div>

    <?php
} else
{
    ?>

    <?php
    //page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
    if ($verif_auth)
    {
        //$sess->delete();
        $auth->logout();
        //$auth->auth_loginform();
        header('Location:' . G_URL . 'login2.php');
    }
}	

