c<?php

//
//Contenu de la div de droite
//
$contenu_page  = '';
$contenu_page3 = '';
$erreur        = 0;

$perso = new perso;
$perso->charge($perso_cod);

//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
$req_comp = "select count(perso_cod) as nombre from perso,perso_position 
										where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod)
										and perso_quete = 'quete_ratier.php'
										and perso_cod = ppos_perso_cod";
$stmt     = $pdo->query($req_comp);
$result   = $stmt->fetch();
if ($result['nombre'] == 0)
{
    $erreur        = 1;
    $contenu_page3 .= 'Vous n\'avez pas accès à cette page !';
}
if (!isset($methode))
{
    $methode = 'debut';
}
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            $nb_queue_rat  = $perso->compte_objet(91);
            $nb_toile      = $perso->compte_objet(92);
            $nb_crochet    = $perso->compte_objet(94);
            $contenu_page3 .= '<form name="vente" method="post" action="' . $PHP_SELF . '">
														<input type="hidden" name="methode" value="vente_perso">
														<input type="hidden" name="objet">';
            $contenu_page3 .= 'Vos pas vous mènent à proximité d\'un individu bien singulier. 
														Légèrement vouté, l\'homme porte une tunique relativement modeste n\'en n\'arborant pas moins les armes du souverain du royaume de Delain, Hormandre III. Son visage renfrogné et son air acerbe n\'invitent guère à la familiarité. 
														Néanmoins, l\'individu se tourne enfin vers vous et vous adresse un sourire forcé. 
														C\'est alors que vous remarquez que sa besace et l\'intérieur de sa veste sont couverts de queues de rats, de crochets de serpents et de soies d\'araignées soigneusement disposés en petits paquets séparés.<br><br>
														
														<em>- " J\'suis un ratier royal ! " lâche l\'homme d\'un ton neutre en pointant son torse sans grande conviction " Not\' bon roi Hormandre m\'a chargé de nettoyer les crasses que vous, les aventuriers, faites en terrassant les sales créatures du Vilain Rougeaud ! N\'allez pas croire que j\'vous en tiens rigueur, n\'empêche, faut bien que quelqu\'un fasse le ménage ou ça ressemblera bientôt à une décharge ici, non ?"
														<br><br>Après vous avoir brièvement inspecté et prit en compte ce que vous avez à lui proposer, le ratier royal vous adresse à nouveau la parole :
			
														<br><br>- " Si j\'compte bien, ça nous fait, <strong>';
            if ($nb_queue_rat != 0)
            {
                $contenu_page3 .= $nb_queue_rat . ' queue(s) de rat, ';
            }
            if ($nb_toile != 0)
            {
                $contenu_page3 .= $nb_toile . ' soie(s) d\'araignée, ';
            }
            if ($nb_crochet != 0)
            {
                $contenu_page3 .= $nb_crochet . ' crochet(s) de serpents';
            }
            if ($nb_queue_rat != 0 or $nb_toile != 0 or $nb_crochet != 0)
            {
                $contenu_page3 .= '</strong> dans votre inventaire.';
            } else
            {
                $contenu_page3 .= ' </strong><strong>aucun objet intéressant dans votre inventaire</strong><br><br>';
            }
            if ($nb_queue_rat >= 10 or $nb_toile >= 10 or $nb_crochet >= 10)
            {
                $contenu_page3 .= '<br>Fichtre, on peut dire que vous n\'y allez pas de main morte vous ! Bon, à défaut de vous payer aussi grassement que les fonctionnaires des bâtiments administratifs - faut bien que je me paie quand même, c\'est pas vous qui usez vos godillots à arpenter tout le secteur !<br> - je peux vous prendre ';
                if ($nb_queue_rat >= 10)
                {
                    $contenu_page3 .= '<a href="javascript:document.vente.objet.value=91;document.vente.submit();"><strong>10 queues de rats </a></strong><em>(2PA)</em>, ';
                }
                if ($nb_toile >= 10)
                {
                    $contenu_page3 .= '<a href="javascript:document.vente.objet.value=92;document.vente.submit();"><strong> 10 soies d\'araignée </a></strong><em>(2PA)</em>,';
                }
                if ($nb_crochet >= 10)
                {
                    $contenu_page3 .= '<a href="javascript:document.vente.objet.value=94;document.vente.submit();"><strong> 10 crochets de serpents </a></strong><em>(2PA)</em>,';
                }
                $contenu_page3 .= '</strong>Tope là ?"';
            } else
            {
                $contenu_page3 .= '<br><br>"N\'êtes pas vraiment un vrai chasseur vous, si ? Ou alors, vous ne ramassez pas vos crasses. On ne s\'abaisse pas à ça hein quand on est un graaaaaand aventurier ! "</em> grommelle l\'homme en s\'éloignant de vous en haussant les épaules. ';
            }
            break;
        case "vente_perso":
            $objet         = $_POST['objet'];
            $req           = 'select vente_perso(' . $perso_cod . ',' . $objet . ') as resultat ';
            $stmt          = $pdo->query($req);
            $result        = $stmt->fetch();
            $contenu_page3 .= '- "Au plus vous en tuez, au plus j\'ai du boulot, continuez comme ça et on refera affaire, je vous le dis moi !" <br><br>';
            $contenu_page3 .= $result['resultat'];
            break;
    }
}
echo $contenu_page3;

