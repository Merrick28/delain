<?php
$verif_connexion::verif_appel();
$stmt             = $pdo->query($req);
$derniere_distrib = -1;
while ($result = $stmt->fetch())
{
    echo '<li>' . $result['date'] . ' : ' . $result['anim_texte'] . '</li>';
    $derniere_distrib = $result['duree'];
}
echo '</ul>';
echo '</div>';
