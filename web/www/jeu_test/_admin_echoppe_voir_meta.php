<?php
$verif_connexion::verif_appel();
echo "<p><strong>Liste des personnes méta guildées :</strong><br>";
$req                = "select perso_nom,perso_cod from perso,guilde_perso 
                      where pguilde_valide = 'O' 
                      and " . $champ_perso . " = 'O' 
                      and perso_cod = pguilde_perso_cod ";
$stmt               = $pdo->query($req);
$allpersometaguilde = $stmt->fetchAll();
if (count($allpersometaguilde) == 0)
{
    echo "<p>Aucun personnage meta guildé !";
} else
{
    echo "<table>";
    foreach ($allpersometaguilde as $result)
    {
        echo "<tr><td class=\"soustitre2\"><p><a href=\"visu_desc_perso.php?visu=", $result['perso_cod'], "\">", $result['perso_nom'], "</a></td></tr>";
    }
    echo "</table>";
}