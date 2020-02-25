<?php
$verif_connexion::verif_appel();
$req_deplace = "select passage($perso_cod) as deplace";
$stmt        = $pdo->query($req_deplace);
$result      = $stmt->fetch();
$result      = explode("#", $result['deplace']);
echo $result[0];
echo "<br>";
if ($result[1] == 0)
{

    /*CETTE PARTIE DEVRAIT ETRE REPRISE DANS UN FICHIER INCLUDE*/
    $is_phrase = rand(1, 100);
    if ($is_phrase > 80)
    {
        $is_phrase = rand(1, 100);
        if ($is_phrase > 50)
        {
            include "phrase.php";
            $idx_phrase = rand(1, 109);
            echo("<p><em>$phrase[$idx_phrase]</em><br /><br /></p>");
        } else
        {
            echo "<p><em>Rumeur :</em> " . $fonctions->get_rumeur() . "<br></p>";
        }
    }
}
