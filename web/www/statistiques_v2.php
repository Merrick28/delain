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

$contenu_page .= '<div class="titre">Statistiques Escape-Game</div>';

$escape_list=[
  "Catacombe I" => ["etage_cod"=>157, "aquete_cod"=>158, "checkpoints" =>[73, 74, 90, 76, 78, 91, 92, 93]],
  "Catacombe II" => ["etage_cod"=>172, "aquete_cod"=>438, "checkpoints" =>[349, 281, 278, 289, 290, 280, 276, 279] ],
  "Catacombe III" => ["etage_cod"=>173, "aquete_cod"=>439, "checkpoints" =>[437, 369, 377, 378, 366, 368, 364, 367]  ],
  "Catacombe IV" => ["etage_cod"=>174, "aquete_cod"=>531, "checkpoints" =>[528, 462, 459, 470, 471, 461, 457, 460] ],
];

$count_etage = [] ;
$contenu_page .= ("<table cellspacing=\"2\" cellpadding=\"2\">");
$contenu_page .= ("<tr><td class=\"soustitre2\" colspan=\"9\"><p style=\"text-align:center;\">Répartition par Etage :</td></tr>");
$contenu_page .= ("<tr><td></td><td class=\"soustitre2\">Quêtes en cours</td><td class=\"soustitre2\">Quêtes abandonnées</td><td class=\"soustitre2\">Quêtes terminées</td><td class=\"soustitre2\">1ere Entrée</td><td class=\"soustitre2\">1ere Sortie</td><td class=\"soustitre2\">Nb de Persos</td><td class=\"soustitre2\">Nb de Familiers</td><td class=\"soustitre2\">Nb de Monstres</td></tr>");
foreach ($escape_list as $etage => $escape){

    $req          =
            "select etage_libelle
                ,sum(case when perso_type_perso=1 then 1 else 0 end) nb_perso
                ,sum(case when perso_type_perso=3 then 1 else 0 end) nb_fam  
                ,sum(case when perso_type_perso=2 then 1 else 0 end) nb_monstre 
                from perso_position join positions on pos_cod=ppos_pos_cod join perso on perso_cod=ppos_perso_cod  
                join etage on etage_cod=pos_etage
                 where perso_actif='O' and pos_etage = ".($escape["etage_cod"])."
                group by etage_libelle order by etage_libelle
    ";
    $stmt         = $pdo->query($req);
    $result = $stmt->fetch();


    $req2          =" select sum(case when aqperso_actif='O' then 1 else 0 end) nb_encours, sum(case when aqperso_actif<>'O' and aqperso_nb_termine=0 then 1 else 0 end) nb_abandon, sum(aqperso_nb_termine) nb_fini, min(aqperso_date_debut) as premier_entree ,min( case when aqperso_nb_termine !=0 then  aqperso_date_fin else null end ) as premier_sortie from quetes.aquete_perso where aqperso_aquete_cod in (".$escape["aquete_cod"].")  ";
    $stmt2         = $pdo->query($req2);
    $result2       = $stmt2->fetch();

    $count_etage[$etage] =  ($result['nb_perso']  ?? 0) ;

    $contenu_page .= "<tr><td class=\"soustitre2\">" . $etage . "</td><td class=\"soustitre2\">" .
        ($result2['nb_encours'] ?? '') . "</td><td class=\"soustitre2\">" .
        ($result2['nb_abandon'] ?? '') . "</td><td class=\"soustitre2\">" .
        ($result2['nb_fini'] ?? '') . "</td><td class=\"soustitre2\">" .
        ($result2['premier_entree'] ? substr($result2['premier_entree'], 0,19) : '') . "</td><td class=\"soustitre2\">" .
        ($result2['premier_sortie'] ? substr($result2['premier_sortie'], 0,19) : '') . "</td><td class=\"soustitre2\">" .
        ($result['nb_perso'] ?? 0) . "</td><td class=\"soustitre2\">" .
        ($result['nb_fam']  ?? 0) . "</td><td class=\"soustitre2\">"  .
        ($result['nb_monstre']  ?? 0) . "</td></tr>";
}
$contenu_page .= ("</table><br>");

$contenu_page .= ("<table cellspacing=\"2\" cellpadding=\"2\">");
$contenu_page .= ("<tr><td class=\"soustitre2\" colspan=\"10\"><p style=\"text-align:center;\">Répartition par Checkpoint :</td></tr>");
$contenu_page .= ("<tr><td></td><td class=\"soustitre2\">Le pénitent</td><td class=\"soustitre2\">Oriental</td><td class=\"soustitre2\">Saut de la foi</td><td class=\"soustitre2\">Skull</td><td class=\"soustitre2\">Labyrinthe</td><td class=\"soustitre2\">Démineur</td><td class=\"soustitre2\">Rock</td><td class=\"soustitre2\">Désert</td><td class=\"soustitre2\">Nom de dieu</td></tr>");

$req = "select aqelem_aquete_cod, count(*) as count from quetes.aquete_element where aqelem_aquete_cod in (73, 74, 90, 76, 78, 91, 92, 93, 349, 281, 278, 289, 290, 280, 276, 279, 437, 369, 377, 378, 366, 368, 364, 367, 528, 462, 459, 470, 471, 461, 457, 460) and aqelem_type='perso_condition' and aqelem_aqperso_cod is null group by aqelem_aquete_cod ";
$stmt   = $pdo->query($req);
$count_checkpoint = [] ;
while ($result = $stmt->fetch()) {
    $count_checkpoint[$result["aqelem_aquete_cod"]] = $result["count"] ;
}

foreach ($escape_list as $etage => $escape){


    $contenu_page .= "<tr><td class=\"soustitre2\">" . $etage . "</td><td width=\"90px;\" class=\"soustitre2\">". ($count_etage[$etage]>0 ? $count_etage[$etage] : '') . "</td>" ;
    foreach ($escape["checkpoints"] as $q) {
        $contenu_page .= "<td width=\"90px;\" class=\"soustitre2\">". ($count_checkpoint[$q]>1 ? (int)$count_checkpoint[$q]-1 : '') . "</td>" ;
    }
    $contenu_page .="</tr>";
}
$contenu_page .= ("</table><br>");



$contenu_page .= '<div class="titre">Statistiques des personnages</div>';


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
$contenu_page .= ("<hr />");

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


//$contenu_page .= "<p style=\"text-align:center;\"><a href=\"rech_class.php\">Faire une recherche !</a>";


$template     = $twig->load('page_generique.twig');
$options_twig = array(
    'CONTENU' => $contenu_page
);
echo $template->render(array_merge($options_twig_defaut, $options_twig));

