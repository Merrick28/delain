<?php

$verif_auth      = false;
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;

$pdo = new bddpdo();

//
// identification
//


//
//Contenu de la div de droite
//
$contenu_page  = '';
$contenu_page  .= '<div class="titre">Statistiques</div>';
$req_nb_compte = "select count(compt_cod) as nb from compte where compt_actif != 'N'
    and compt_der_connex >= now() - '30 days'::INTERVAL
	and compt_monstre = 'N'
	and compt_quete = 'N'
	and compt_admin = 'N'
	and exists
	(select 1 from perso_compte,perso
	where pcompt_compt_cod = compt_cod
	and pcompt_perso_cod = perso_cod)";
$stmt          = $pdo->query($req_nb_compte);
$result        = $stmt->fetch();
$nb_compte     = $result['nb'];

$req_joueur   =
    "select count(perso_cod) as nb from perso where perso_type_perso = 1 and perso_actif != 'N' and perso_pnj != 1 and perso_dlt >= now() - '30 days'::INTERVAL ";
$stmt         = $pdo->query($req_joueur);
$result       = $stmt->fetch();
$nb_joueur    = $result['nb'];
$moyenne      = round($nb_joueur / $nb_compte, 2);
$contenu_page .= ("Il y a aujourd'hui <strong>$nb_joueur</strong> personnages pour <strong>$nb_compte</strong> comptes (soit une moyenne de $moyenne personnages par joueur),");

$req_joueur = "select count(perso_cod) as nb from perso where perso_type_perso = 2 and perso_actif = 'O' ";
$stmt       = $pdo->query($req_joueur);
$result     = $stmt->fetch();
$nb_monstre = $result['nb'];

$contenu_page .= (" et <strong>$nb_monstre</strong> monstres dans les souterrains qui n'attendent que vous !");

$contenu_page .= '<br /><em>Statistiques sur les 30 derniers jours seulement</em>';

$contenu_page .= '<div class="titre">Statistiques des personnages</div>';
// classement par niveau
$req_niveau   = "select perso_niveau,count(perso_cod) as nb from perso 
    where perso_actif != 'N' and perso_type_perso = 1 and perso_pnj != 1 and perso_dlt >= now() - '30 days'::INTERVAL 
    group by perso_niveau 
    order by perso_niveau desc ";
$stmt         = $pdo->query($req_niveau);
$contenu_page .= ("<table cellspacing=\"2\" cellpadding=\"2\">");
$contenu_page .= ("<tr><td class=\"soustitre2\" colspan=\"2\"><p style=\"text-align:center;\">Répartition par niveau</td></tr>");
$contenu_page .= ("<tr><td class=\"soustitre2\">Niveau :</td><td class=\"soustitre2\">Nombre de personnages :</td></tr>");
while ($result = $stmt->fetch())
{
    $contenu_page .= "<tr><td class=\"soustitre2\">" . $result['perso_niveau'] . "</td><td class=\"soustitre2\">" .
                     $result['nb'] . "</td></tr>";
}
$contenu_page .= ("</table>");
$contenu_page .= ("<hr />");

// classement par joueur et par sexe
$req          =
    "select race_nom,(select count(perso_cod) from perso where perso_actif != 'N' 
            and perso_type_perso = 1 and perso_race_cod = race_cod
            and perso_sex = 'M' and perso_dlt >= now() - '30 days'::INTERVAL) as m, 
            (select count(perso_cod) from perso where perso_actif != 'N' and perso_type_perso = 1 and perso_race_cod = race_cod and perso_sex = 'F' and perso_dlt >= now() - '30 days'::INTERVAL) as f 
from race where race_cod in (1,2,3,33) ";
$stmt         = $pdo->query($req);
$contenu_page .= ("<table cellspacing=\"2\" cellpadding=\"2\">");
$contenu_page .= ("<tr><td class=\"soustitre2\" colspan=\"3\"><p style=\"text-align:center;\">Répartition par race et par sexe :</td></tr>");
$contenu_page .= ("<tr><td></td><td class=\"soustitre2\">M</td><td class=\"soustitre2\">F</td></tr>");
while ($result = $stmt->fetch())
{
    $contenu_page .= "<tr><td class=\"soustitre2\">" . $result['race_nom'] . "</td><td class=\"soustitre2\">" .
                     $result['m'] . "</td><td class=\"soustitre2\">" . $result['f'] . "</td></tr>";
}
$contenu_page .= ("</table>");
$contenu_page .= ("<hr />");

// classement par étage
$contenu_page .= ("<table cellspacing=\"2\" cellpadding=\"2\">");
$contenu_page .= ("<tr><td class=\"soustitre2\" colspan=\"5\"><p style=\"text-align:center;\">Répartition par étage : <br><em>Seuls les étages connus sont visibles. De nombreux antres existent et restent à la découverte des joueurs/personnages</em></td></tr>");
$contenu_page .= ("<tr><td class=\"soustitre2\">Etage</td>
			<td class=\"soustitre2\">Personnages</td>
			<td class=\"soustitre2\">Niveau moyen</td>
			<td class=\"soustitre2\">Monstres</td>
			<td class=\"soustitre2\">Familiers</td></tr>");
$req          = "select etage_libelle, 
(select count(perso_cod) from perso,perso_position,positions 
where pos_etage = etage_numero 
and ppos_pos_cod = pos_cod 
and ppos_perso_cod = perso_cod 
and perso_type_perso = 1 and perso_dlt >= now() - '30 days'::INTERVAL 
and perso_actif != 'N' and perso_pnj != 1) as joueur, 
(select sum(perso_niveau) from perso,perso_position,positions 
where pos_etage = etage_numero 
and ppos_pos_cod = pos_cod 
and ppos_perso_cod = perso_cod 
and perso_type_perso = 1 and perso_dlt >= now() - '30 days'::INTERVAL 
and perso_actif != 'N' and perso_pnj != 1) as jnv, 
(select count(perso_cod) from perso,perso_position,positions
where pos_etage = etage_numero 
and ppos_pos_cod = pos_cod 
and ppos_perso_cod = perso_cod 
and perso_type_perso = 2 
and perso_actif != 'N' and perso_pnj != 1) as monstre,
(select count(perso_cod) from perso,perso_position,positions 
where pos_etage = etage_numero 
and ppos_pos_cod = pos_cod 
and ppos_perso_cod = perso_cod and perso_type_perso = 3 
and perso_actif != 'N' and perso_pnj != 1) as familier 
from etage 
where etage_numero <= 0
      and etage_numero != -100 
      order by etage_numero desc ";
$stmt         = $pdo->query($req);

while ($result = $stmt->fetch())
{
    $contenu_page .= "<tr><td class=\"soustitre2\">" . $result['etage_libelle'] . "</td>
				<td>" . $result['joueur'] . "</td>
				<td>" . ($result['joueur'] != 0 ?
            round($result['jnv'] / $result['joueur'] , 0) :
            0) . "</td>
				<td>" . $result['monstre'] . "</td>
				<td>" . $result['familier'] . "</td></tr>";
}


$contenu_page .= ("</table>");
$contenu_page .= "<p style=\"text-align:center;\"><a href=\"rech_class.php\">Faire une recherche !</a>";


$template     = $twig->load('page_generique.twig');
$options_twig = array(
    'CONTENU' => $contenu_page
);
echo $template->render(array_merge($options_twig_defaut, $options_twig));

