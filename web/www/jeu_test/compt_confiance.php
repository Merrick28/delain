<?php
include "blocks/_header_page_jeu.php";
ob_start();
if (!isset($compte))
{
    $req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cod ";
    $stmt = $pdo->query($req);

    // Compte trouvé
    if($result = $stmt->fetch())
        $compte = $result['pcompt_compt_cod'];
    // Compte non trouvé ; peut-être un familier ?
    else
    {
        $req = "select pcompt_compt_cod from perso_compte
			inner join perso_familier on pfam_perso_cod = pcompt_perso_cod
			where pfam_familier_cod = $perso_cod ";
        $stmt = $pdo->query($req);
        if($result = $stmt->fetch())
            $compte = $result['pcompt_compt_cod'];
        else
        {
            $compte = -1;
        }
    }
}
if (!isset($_GET['etat']))
{
    $etat = 'N';
} else
{
    $etat = $_GET['etat'];
}
if ($db->is_admin($compt_cod))
{
    $req = "select compt_nom from compte where compt_cod = $compte ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $nom_compte = $result['compt_nom'];
    if ($etat == 'N')
    {
        $req = "update compte set compt_confiance = 'O' where compt_cod = $compte ";
        $stmt = $pdo->query($req);
        echo "<p>Le compte <strong>" . $nom_compte . "</strong> a été passé en compte confiant. Il n’apparaîtra plus dans la liste des multi ";
    }
    if ($etat == 'O')
    {
        $req = "update compte set compt_confiance = 'N' where compt_cod = $compte ";
        $stmt = $pdo->query($req);
        echo "<p>Le compte <strong>" . $nom_compte . "</strong> a été passé en compte NON confiant. Il apparaîtra dans la liste des multi ";
    }
    if ($etat == 'S')
    {
        $req = "update compte set compt_confiance = 'S' where compt_cod = $compte ";
        $stmt = $pdo->query($req);
        echo "<p>Le compte <strong>" . $nom_compte . "</strong> a été passé en compte SURVEILLÉ. Un message sera envoyé aux Contôleurs à son sortir d’hibernation.";
    }
} else
{
    echo "<p>Erreur ! Vous n’êtes pas administrateur !</p>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";