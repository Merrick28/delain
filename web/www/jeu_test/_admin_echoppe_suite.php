<?php
$verif_connexion::verif_appel();
// preparation des requêtes
$req  =
    "select pguilde_perso_cod,perso_nom from guilde_perso,perso
where pguilde_guilde_cod = :key
and pguilde_perso_cod = perso_cod
and pguilde_valide = 'O'
and " . $champ_perso . " = 'O' ";
$stmtPerso = $pdo->prepare($req);
foreach ($guilde as $key => $val)
{
    $req    = "select guilde_nom," . $champ . " from guilde where guilde_cod = $key ";
    $stmt   = $pdo->query($req);
    $result = $stmt->fetch();
    if ($val != $result[$champ]) // changement
    {
        // d'abord on marque le changement
        $req  = "update guilde set " . $champ . " = '$val' where guilde_cod = $key ";
        $stmt = $pdo->query($req);
        // si c'est une suppression, on supprime les gens meta guildés
        if ($val == 'N')
        {

            $stmtPerso    = $pdo->execute(array(":key" => $key), $stmtPerso);
            $alldest = $stmtPerso->fetchAll();
            if (count($alldest) != 0)
            {
                $message             = new message();
                $message->corps      =
                    "Un administrateur de meta-guilde a décidé de ne plus rattacher votre guilde.<br>Vous perdez donc les droits liés à ce meta-guildage.<br />";
                $message->sujet      = "Fin de meta guildage.";
                $message->expediteur = $perso_cod;
                foreach ($alldest as $result)
                {
                    $message->ajouteDestinataire($result['pguilde_perso_cod']);
                    echo "<p>Le joueur <strong>", $result['perso_nom'], "</strong> a été supprimé du méta guildage.";
                }
                $message->envoieMessage(false);
            }


            $req  =
                "update guilde_perso set " . $champ_perso . " = 'N' where pguilde_guilde_cod = $key ";
            $stmt = $pdo->query($req);
            echo "<p>La guilde <strong>", $result['guilde_nom'], "</strong> a été supprimée des meta guildages.";
        } else
        {
            echo "<p>La guilde <strong>", $result['guilde_nom'], "</strong> a été ajoutée aux meta guildages.";
        }
    }
}
