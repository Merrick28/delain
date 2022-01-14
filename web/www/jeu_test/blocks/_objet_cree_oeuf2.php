<?php
$verif_connexion::verif_appel();
if ($etat_objet < 20)
    $contenu_page .= "<p>L’œuf est craquellé de toutes parts, quelques morceaux de la coquille se détachent... Il est sur le point d’éclore !</p>";
else if ($etat_objet < 40)
    $contenu_page .= "<p>L’œuf bouge de plus en plus les craquelures sont plus nettes et plus nombreuses.</p>";
else if ($etat_objet < 60)
    $contenu_page .= "<p>Les mouvements à l’intérieur de l’œuf sont plus nombreux, quelques craquelures apparaissent par endroits.</p>";
else if ($etat_objet < 80)
    $contenu_page .= "<p>On sent quelques légers mouvements à l’intérieur de l’œuf, la coquille se fendille légèrement.</p>";
else
    $contenu_page .= "<p>L’œuf est intact et inerte, rien à signaler.</p>";
