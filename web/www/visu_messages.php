<?php
require_once "ident.php";
$pdo = new bddpdo();


$contenu_page = '';
$titre_page   = '';

$erreur = empty($_REQUEST['visu_perso']) || empty($compt_cod);
if (!$erreur) {
    $req_compte    = "select pcompt_compt_cod from perso_compte
		where pcompt_perso_cod = :perso
		or pcompt_perso_cod = ( select pfam_perso_cod from perso_familier where pfam_familier_cod = :perso)";
    $stmt          = $pdo->prepare($req_compte);
    $stmt          = $pdo->execute(array(":perso" => $_REQUEST['visu_perso']), $stmt);
    $result        = $stmt->fetch();
    $compte_trouve = $result['pcompt_compt_cod'];

    $erreur = ($compte_trouve === false || $compte_trouve != $compt_cod);

    if ($erreur) {    // on est peut-être dans le cas d’un sitting
        $req_sitting    = "select csit_compte_sitteur from compte_sitting
			inner join perso_compte on pcompt_compt_cod = csit_compte_sitte
			where pcompt_perso_cod = :perso
				and now() between csit_ddeb and csit_dfin";
        $stmt           = $pdo->prepare($req_sitting);
        $stmt           = $pdo->execute(array(":perso" => $_REQUEST['visu_perso']), $stmt);
        $result         = $stmt->fetch();
        $compte_sitting = $result['csit_compte_sitteur'];

        $erreur = ($compte_sitting === false || $compte_sitting != $compt_cod);
    }
}
if ($erreur) {
    $titre_page   = 'Erreur';
    $contenu_page = 'Erreur d’authentification !';
}

if (!$erreur && empty($_REQUEST['visu_msg'])) {    // Liste des messages
    $titre_page   = 'Voir les 10 derniers messages';
    $contenu_page =
        '<table><tr><th class="titre">Date</th><th class="titre">Expediteur</th><th class="titre">Titre</th></tr>';

    $req_msg = "select msg_cod, msg_titre, perso_nom, perso_cod, dmsg_lu,
			to_char(msg_date2, 'DD/MM/YYYY hh24:mi:ss') as msg_date from messages
		inner join messages_exp on emsg_msg_cod = msg_cod
		inner join messages_dest on dmsg_msg_cod = msg_cod
		inner join perso on perso_cod = emsg_perso_cod
		where dmsg_perso_cod = :perso
		order by msg_date2 desc limit 10;";
    $stmt = $pdo->prepare($req_msg);
    $stmt = $pdo->execute(array(":perso" =>  $_REQUEST['visu_perso']), $stmt);
    while ($result = $stmt->fetch()) {
        $msg_cod        = $result['msg_cod'];
        $expediteur     = $result['perso_nom'];
        $expediteur_cod = $result['perso_cod'];
        $dmsg_lu        = $result['dmsg_lu'] == 'O';
        $msg_date       = $result['msg_date'];

        $btitre1    = ($dmsg_lu) ? '' : '<strong>';
        $btitre2    = ($dmsg_lu) ? '' : '</strong>';
        $msg_titre  = $btitre1 . $result['msg_titre'] . $btitre2;
        $lien_titre = "?visu_perso=$visu_perso&visu_msg=$msg_cod";

        $contenu_page .= "<tr><td>$msg_date</td><td>$expediteur</td><td><a href='$lien_titre'>$msg_titre</a></td></tr>";
    }
    $contenu_page .= "</table>";
}

if (!$erreur && !empty($_REQUEST['visu_msg'])) {    // Lecture d’un message
    $req_msg_ok = "select 1 from messages_dest where dmsg_perso_cod = :perso AND dmsg_msg_cod = :msg";
    $stmt = $pdo->prepare($req_msg_ok);
    $stmt = $pdo->execute(array(":perso" => $_REQUEST['visu_perso'],
    ":msg" =>$_REQUEST['visu_msg'] ), $stmt);
    if (!$result = $stmt->fetch()) {
        $erreur       = true;
        $titre_page   = 'Erreur';
        $contenu_page = 'Message introuvable !';
    } else {
        $req_msg = "select msg_cod, msg_titre, e.perso_nom, e.perso_cod, msg_corps,
				to_char(msg_date2, 'DD/MM/YYYY hh24:mi:ss') as msg_date,
				string_agg(d.perso_nom, ', ') as destinataires
			from messages
			inner join messages_exp on emsg_msg_cod = msg_cod
			inner join messages_dest on dmsg_msg_cod = msg_cod
			inner join perso e on e.perso_cod = emsg_perso_cod
			inner join perso d on d.perso_cod = dmsg_perso_cod
			where msg_cod = :msg
			group by msg_cod, msg_titre, e.perso_nom, e.perso_cod, msg_corps, msg_date2";
        $stmt = $pdo->prepare($req_msg);
        $stmt = $pdo->execute(array(":msg" => $_REQUEST['visu_msg']), $stmt);
        if ($result = $stmt->fetch()) {
            $msg_cod        = $result['msg_cod'];
            $expediteur     = $result['perso_nom'];
            $expediteur_cod = $result['perso_cod'];
            $destinataires  = $result['destinataires'];
            $msg_date       = $result['msg_date'];
            $msg_titre      = $result['msg_titre'];
            $msg_corps      = $result['msg_corps'];

            $titre_page   = $msg_titre;
            $contenu_page = '<table>';
            $contenu_page .= "<tr><td class='titre'>Date</td><td>$msg_date</td></tr>";
            $contenu_page .= "<tr><td class='titre'>Expéditeur</td><td>$expediteur</td></tr>";
            $contenu_page .= "<tr><td class='titre'>Destinataires</td><td>$destinataires</td></tr>";
            $contenu_page .= "<tr><td colspan='2'>$msg_corps</td></tr>";

            // On marque comme lu
            $req_lu =
                "update messages_dest set dmsg_lu = 'O' where dmsg_msg_cod = :msg and dmsg_perso_cod = :perso and dmsg_lu <> 'O'";
            $stmt = $pdo->prepare($req_lu);
            $stmt = $pdo->execute(array(":msg" => $_REQUEST['visu_msg'],
                                      ":perso" => $_REQUEST['visu_perso']), $stmt);
        }
        $contenu_page .= "</table>";
    }
    $contenu_page .= '<p><a href="?visu_perso=' . $_REQUEST['visu_perso'] . '">Retour</a></p>';
}


$template     = $twig->load('template_jeu_sans_menu.twig');
$options_twig = array(
    'CONTENU' => $contenu_page,
    'TITRE'   => $titre_page
);
echo $template->render(array_merge($options_twig_defaut, $options_twig));
