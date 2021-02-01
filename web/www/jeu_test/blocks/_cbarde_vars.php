<?php
$verif_connexion::verif_appel();
$saison         = $result['cbar_saison'];
$date_ouverture = $result['cbar_date_ouverture'];
$date_teaser    = $result['cbar_date_teaser'];
$fermeture      = $result['cbar_fermeture'];
$description    = $result['cbar_description'];
$introduction   = ($result['introduction'] == 1);
$ouvert         = ($result['ouvert'] == 1);
$futur          = ($result['futur'] == 1);
$ferme          = ($result['ferme'] == 1);
