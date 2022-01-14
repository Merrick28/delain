<?php
include "blocks/_header_page_jeu.php";
$fonctions = new fonctions();
$perso     = $verif_connexion->perso;
ob_start();
$erreur    = 0;
$temps_dlt = $_REQUEST['temps_dlt'];
if (!isset($temps_dlt))
{
    echo("<p>Vous devez saisir une valeur de temps !!");
    $erreur = 1;
}
if ($erreur == 0)
{
    if ($temps_dlt <= 0)
    {
        echo("<p>Vous ne pouvez pas saisir une valeur nulle ou négative !");
        $erreur = 1;
    }
}
if ($erreur == 0)
{
    $methode = get_request_var('methode', 'debut');
    switch ($methode)
    {
        case "debut":
            $req = "select to_char((perso_dlt + '$temps_dlt minutes'::interval),'DD/MM/YYYY à hh24:mi:ss') as nvdlt,
                               to_char(prochaine_dlt($perso_cod) + '$temps_dlt minutes'::interval,'DD/MM/YYYY à hh24:mi:ss') as nxtdlt
					from perso
					where perso_cod = $perso_cod ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            $nvdlt  = $result['nvdlt'];
            $nxtdlt = $result['nxtdlt'];
            ?>
            Etes vous sûr de vouloir décaler votre dlt de <?php echo $temps_dlt ?> minutes ? <br/>
            Votre prochaine dlt commencera le <strong><?php echo $nvdlt; ?></strong> <em>(la suivante le
            <strong><?php echo $nxtdlt; ?></strong>)</em>
            <br><strong><a
                href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=validation&temps_dlt=<?php echo $temps_dlt; ?>">Oui</a>
            <br><br><a href="perso2.php">Non</a></strong>
            <?php
            break;
        case "validation":
            $temps_dlt = round($temps_dlt);
            $req       =
                "update perso set perso_dlt = perso_dlt + '$temps_dlt minutes'::interval where perso_cod = $perso_cod ";
            $stmt      = $pdo->query($req);
            $perso->charge($perso_cod);

            echo("<p>Votre DLT a bien été repoussée de $temps_dlt minutes. ");

            echo "<p>Votre nouvelle DLT est à <strong>" . $fonctions->format_date($perso->perso_dlt) . "</strong>.";
            break;
    }
}
echo("<p style=\"text-align:center\"><a href=\"perso2.php\">Retour</a>");


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
