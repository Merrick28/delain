<?php
$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();


$mode_sortie = 'echo';
if ($contenu_page != '')
{
    $mode_sortie = 'variable';
}


// Factions présentes
$req_factions = "SELECT fac_cod, fac_nom FROM v_factions_lieux
	WHERE lieu_cod = $lieu_cod";
$stmt         = $pdo->query($req_factions);

if ($stmt->rowCount() > 0)
{
    // On a des factions dans ce lieu !
    if ($stmt->rowCount() == 1)
    {
        $contenu_page .= '<hr />Une personne semble recruter en ce lieu ! Elle vous aborde :<br />';
    } else
    {
        $contenu_page .= '<hr />Différentes factions semblent recruter en ce lieu ! Leurs représentants vous abordent :<br /><br />';


    }
    while ($result = $stmt->fetch())
    {
        $faction     = $result['fac_nom'];
        $faction_cod = $result['fac_cod'];

        $contenu_page .= "<p>« Rejoignez-nous ! <a href='factions.php?faction=$faction_cod'>Travaillez pour $faction</a>, et notre grandeur sera la vôtre ! »</p>";
    }


}

if ($mode_sortie == 'echo')
{
    echo $contenu_page;
    $contenu_page = '';
}