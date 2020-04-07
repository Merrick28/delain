<?php
$param     = new parametres();
$perso     = $verif_connexion->perso;
$fonctions = new fonctions();

require_once G_CHE . 'includes/message.php';
$dialogue               = '';
$erreur                 = false;
$message_erreur         = '<strong>Une erreur est survenue !</strong><br />';
$delai_acceptation_defi = $param->getparm(125);

$defi_regles = "
	<script type='text/javascript'>
		function affiche_regles()
		{
			var zone = document.getElementById('defi_regles');
			if (zone.style.display == 'none')
				zone.style.display = 'block';
			else
				zone.style.display = 'none';
		}
	</script>
	<a href='javascript:affiche_regles()'><strong>Règles</strong> (afficher / masquer)</a><div style=\"text-align: left; display:none;\" id='defi_regles'><hr />
	Vous pouvez lancer un défi à condition que vous ne soyez pas engagé dans un combat, à n’importe quel aventurier. Ce dernier a alors $delai_acceptation_defi jours pour répondre.<br />
	Lorsque le défi est relevé, vous et votre adversaire serez immédiatement téléportés dans une arène dédiée.<br />
	Vos PAs seront mis à zéro, votre DLT repoussée pour dans 3 heures.<br />
	Vos bonus et malus seront supprimés (y compris impalpabilité éventuelle)<br />
	Vos PV seront restaurés.<br />
	Votre état initial vous sera rendu dès la fin du défi, avec une impalpabilité de 1 DLT effective pour vous éviter de trop mauvaises surprises.<br />
	Pendant toute la durée du défi, il est interdit de commercer avec son adversaire.<br /><hr />
	Le défi a trois issues possibles : la victoire par KO (mort d’un protagoniste), la victoire par forfait (abandon d’un protagoniste, ou refus de relever le défi) ou le match nul. Ce dernier se déclare par commun accord des participants.<br />
	L’accord se fait en 3 étapes :<br />
	1) Le protagoniste A demande le match nul.<br />
	2) La demande est transmise au protagoniste B. Il peut choisir d’accompagner A dans sa requête, ou de refuser.<br />
	3) Si B et A abandonnent de conserve, le match est déclaré nul.<hr /></div>";

$methode     = get_request_var('methode', 'aucune');

switch ($methode)
{
    case 'defi_lance1':  // Lancement d’un défi. Écran de confirmation indiquant que cela provoquera une téléportation
        if (empty($cible_nom))
        {
            $erreur         = true;
            $message_erreur .= 'La cible n’est pas définie.';
            break;
        }
        $verif_nom    = pg_escape_string(trim($cible_nom));
        $req_verif    = "select f_cherche_perso('$verif_nom') as num_perso";
        $numero_cible = $pdo->get_value($req_verif, 'num_perso');
        if ($numero_cible == -1)
        {
            $erreur         = true;
            $message_erreur .= "$cible_nom n’existe pas.";
            break;
        }
        $req_verif = "select defi_possible($perso_cod, $numero_cible) as resultat";
        $verif     = $pdo->get_value($req_verif, 'resultat');

        $defi_ok = explode('#', $verif);
        if ($defi_ok[0] == -1)
        {
            $erreur         = true;
            $message_erreur .= $defi_ok[1];
            break;
        }
        $avertissement = '';
        if ($defi_ok[0] == 0)
            $avertissement = '<p style="color: darkred; font-weight: bold;">Attention ! ' . $defi_ok[1] . '</p>';

        $dialogue = "Vous vous apprêtez à lancer un défi contre $cible_nom. Votre adversaire dispose de $delai_acceptation_defi
			jours pour l’accepter ou le refuser.<br /><br />
			Vous pouvez personnaliser ci-dessous le message qui sera envoyé à votre cible.
			<form action='#' method='post'>
				<input type='hidden' value='5' name='m' />
				<input type='hidden' value='$numero_cible' name='defi_numero_cible' />
				<input type='hidden' value='defi_lance2' name='methode' />
				<textarea name='defi_message' rows='5' cols='30'>$cible_nom, je te défie ! Retrouvons-nous dans un coin tranquille, et voyons qui de nous deux se relèvera...</textarea><br />
				$avertissement
				<input type='submit' value='Lancer le défi !' class='test' /> - <a href='?m=5'>Annuler le défi</a>
			</form>";
        break;

    case 'defi_lance2':  // Lancement d’un défi, envoi de message et création en base.
        if (empty($defi_numero_cible))
        {
            $erreur         = true;
            $message_erreur .= 'La cible n’est pas définie.';
            break;
        }
        $defi_numero_cible = fonctions::format($defi_numero_cible, false);
        $defi_titre        = "$perso->perso_nom vous lance un défi !";
        if ($defi_message == '')
            $defi_message = 'Grand timide, votre adversaire n’a pas daigné agrémenter son geste de paroles.';
        else
            $defi_message =
                'Votre adversaire a également tenu à vous faire parvenir le message suivant : <hr />' . fonctions::format($defi_message);

        $defi_message = "Le personnage $nom vous a lancé un défi. Vous avez $delai_acceptation_defi jours pour l’accepter ou le refuser.
			Passé ce délai, votre adversaire sera considéré comme vainqueur par forfait.
			$defi_message <hr /><a href=\"perso2.php?m=5\">Rendez-vous sur votre Fiche de Personnage !</a>";

        $req_defi  = "select defi_creer($perso_cod, $defi_numero_cible) as resultat";
        $lancement = $pdo->get_value($req_defi, 'resultat');

        $resultat_lancement = explode('#', $lancement);
        if ($resultat_lancement[0] == -1)
        {
            $erreur         = true;
            $message_erreur .= $resultat_lancement[1];
            break;
        }
        $avertissement = '';
        if ($resultat_lancement[0] == 0)
            $avertissement =
                '<p style="color: darkred; font-weight: bold;">Attention ! ' . $resultat_lancement[1] . '</p>';

        message::Envoyer($perso_cod, $defi_numero_cible, $defi_titre, $defi_message, false);

        $dialogue = "Votre défi est lancé ! $avertissement";
        break;

    case 'defi_releve1':  // La cible relève le défi : confirmation indiquant que cela provoquera une téléportation
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod    = $result['defi_lanceur_cod'];
        $defi_cible_cod = $result['defi_cible_cod'];
        $defi_statut    = $result['defi_statut'];
        if ($defi_cible_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut != 0)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est plus possible de relever ce défi !';
            break;
        }

        $req_nom     = "select perso_nom from perso where perso_cod = $lanceur_cod";
        $lanceur_nom = $pdo->get_value($req_nom, 'perso_nom');

        $dialogue = "Vous vous apprêtez à relever le défi de $lanceur_nom.<br />
			Vous pouvez personnaliser ci-dessous le message qui sera envoyé à votre adversaire.
			<form action='#' method='post'>
				<input type='hidden' value='5' name='m' />
				<input type='hidden' value='$defi_cod' name='defi_cod' />
				<input type='hidden' value='defi_releve2' name='methode' />
				<textarea name='defi_message' rows='5' cols='30'>$lanceur_nom, je relève ton défi ! Tu vas regretter ton geste...</textarea><br />
				<input type='submit' value='Relever le défi !' class='test' /> - <a href='?m=5'>Annuler</a>
			</form>";
        break;

    case 'defi_releve2':  // Relevé du défi, téléportation.
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod    = $result['defi_lanceur_cod'];
        $defi_cible_cod = $result['defi_cible_cod'];
        $defi_statut    = $result['defi_statut'];
        if ($defi_cible_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut != 0)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est plus possible de relever ce défi !';
            break;
        }


        $defi_titre = "$perso->perso_nom relève votre défi !";
        if ($defi_message == '')
            $defi_message = 'Grand timide, votre adversaire n’a pas daigné agrémenter son geste de paroles.';
        else
            $defi_message =
                'Votre adversaire a également tenu à vous faire parvenir le message suivant : <hr />' . fonctions::format($defi_message);

        $defi_message = "Le personnage $nom a relevé votre défi !
			$defi_message";

        $req_defi  = "select defi_commencer($defi_cod) as resultat";
        $lancement = $pdo->get_value($req_defi, 'resultat');

        $resultat_lancement = explode('#', $lancement);
        if ($resultat_lancement[0] == -1)
        {
            $erreur         = true;
            $message_erreur .= $resultat_lancement[1];
            break;
        }

        message::Envoyer($perso_cod, $lanceur_cod, $defi_titre, $defi_message, false);

        $dialogue = "Le défi est relevé !";
        break;

    case 'defi_rejette1':  // Rejet du défi par la cible. Confirmation indiquant perte de réputation.
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod    = $result['defi_lanceur_cod'];
        $defi_cible_cod = $result['defi_cible_cod'];
        $defi_statut    = $result['defi_statut'];
        if ($defi_cible_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut != 0)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est plus possible de rejeter ce défi !';
            break;
        }

        $req_nom     = "select perso_nom from perso where perso_cod = $lanceur_cod";
        $lanceur_nom = $pdo->get_value($req_nom, 'perso_nom');

        $dialogue = "Vous vous apprêtez à rejeter le défi de $lanceur_nom.<br />
			Cela pourra occasionner une perte de réputation. Et lui pourra en retirer de la gloire !<br />
			Vous pouvez personnaliser ci-dessous le message qui sera envoyé à votre adversaire.
			<form action='#' method='post'>
				<input type='hidden' value='5' name='m' />
				<input type='hidden' value='$defi_cod' name='defi_cod' />
				<input type='hidden' value='defi_rejette2' name='methode' />
				<textarea name='defi_message' rows='5' cols='30'>$lanceur_nom, ton défi ne m’intéresse pas. Trop facile, aucune gloire à en tirer...</textarea><br />
				<input type='submit' value='Rejeter le défi.' class='test' /> - <a href='?m=5'>Annuler</a>
			</form>";
        break;

    case 'defi_rejette2':  // Rejet du défi par la cible. Perte de réputation de la cible.
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod    = $result['defi_lanceur_cod'];
        $defi_cible_cod = $result['defi_cible_cod'];
        $defi_statut    = $result['defi_statut'];
        if ($defi_cible_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut != 0)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est plus possible de rejeter ce défi !';
            break;
        }

        $req_nom = "select perso_nom from perso where perso_cod = $defi_cible_cod";
        $nom     = $pdo->get_value($req_nom, 'perso_nom');

        $defi_titre = "$nom rejette votre défi.";
        if ($defi_message == '')
            $defi_message = 'Grand timide, votre adversaire n’a pas daigné agrémenter son geste de paroles.';
        else
            $defi_message =
                'Votre adversaire a également tenu à vous faire parvenir le message suivant : <hr />' . fonctions::format($defi_message);

        $defi_message = "Le personnage $nom a rejeté votre défi !
			$defi_message";

        $req_defi = "select to_char(defi_abandonner($defi_cod, 'C'), '99D99') as resultat";
        $renommee = $pdo->get_value($req_defi, 'resultat');

        if ((int)$renommee != -1)
        {
            message::Envoyer($perso_cod, $lanceur_cod, $defi_titre, $defi_message, false);
            $dialogue = "Vous avez rejeté le défi. Cela vous a fait perdre $renommee points de renommée.";
        } else
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est plus possible de rejeter ce défi !';
        }
        break;

    case 'defi_abandonne1':  // Première étape de l’abandon : confirmation
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut, defi_abandon_etape
			from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod    = $result['defi_lanceur_cod'];
        $defi_cible_cod = $result['defi_cible_cod'];
        $defi_statut    = $result['defi_statut'];
        if ($defi_cible_cod != $perso_cod && $lanceur_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut > 1)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est plus possible d’abandonner ce défi !';
            break;
        }
        $adversaire     = ($defi_cible_cod != $perso_cod) ? $defi_cible_cod : $lanceur_cod;
        $req_nom        = "select perso_nom from perso where perso_cod = $adversaire";
        $adversaire_nom = $pdo->get_value($req_nom, 'perso_nom');

        if ($defi_statut == 0) // Défi pas encore commencé
        {
            if ($lanceur_cod != $perso_cod)
            {
                $erreur         = true;
                $message_erreur .= 'Seul le lanceur du défi peut annuler un défi non commencé !';
                break;
            }
        }
        $dialogue = "Vous vous apprêtez à abandonner votre défi face à $adversaire_nom.<br />
			Vous pouvez personnaliser ci-dessous le message qui sera envoyé à votre adversaire.
			<form action='#' method='post'>
				<input type='hidden' value='5' name='m' />
				<input type='hidden' value='$defi_cod' name='defi_cod' />
				<input type='hidden' value='defi_abandonne2' name='methode' />
				<textarea name='defi_message' rows='5' cols='30'></textarea><br />
				<input type='submit' value='Abandonner le défi.' class='test' /> - <a href='?m=5'>Annuler</a>
			</form>";
        break;

    case 'defi_abandonne2':  // Deuxième étape de l’abandon : envoi à l’adversaire et confirmation de l’abandon.
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut, defi_abandon_etape
			from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod        = $result['defi_lanceur_cod'];
        $defi_cible_cod     = $result['defi_cible_cod'];
        $defi_statut        = $result['defi_statut'];
        $defi_abandon_etape = $result['defi_abandon_etape'];
        if ($defi_cible_cod != $perso_cod && $lanceur_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut > 1)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est plus possible d’abandonner ce défi !';
            break;
        }
        $role = ($defi_cible_cod == $perso_cod) ? 'C' : 'L';  // Cible ou lanceur du défi


        if ($defi_statut == 0) // Défi pas encore commencé
        {
            if ($lanceur_cod != $perso_cod)
            {
                $erreur         = true;
                $message_erreur .= 'Seul le lanceur du défi peut annuler un défi non commencé !';
                break;
            }
        }
        $defi_titre = "$perso->perso_nom abandonne le défi.";
        if ($defi_message == '')
            $defi_message = 'Grand timide, votre adversaire n’a pas daigné agrémenter son geste de paroles.';
        else
            $defi_message =
                'Votre adversaire a également tenu à vous faire parvenir le message suivant : <hr />' . fonctions::format($defi_message);

        $defi_message = "$nom a annulé son défi !
			$defi_message";

        $req_defi = "select to_char(defi_abandonner($defi_cod, '$role'), '99D99') as resultat";
        $renommee = $pdo->get_value($req_defi, 'resultat');

        if ((int)$renommee != -1)
        {
            $adversaire = ($role == 'C') ? $lanceur_cod : $defi_cible_cod;  // Cible ou lanceur du défi
            message::Envoyer($perso_cod, $adversaire, $defi_titre, $defi_message, false);
            $dialogue = "Vous avez abandonné le défi. Cela vous a fait perdre $renommee points de renommée.";
        } else
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est plus possible d’abandonner ce défi !';
        }
        break;

    case 'defi_nul1':  // Première étape du match nul : confirmation.
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut, defi_abandon_etape
			from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod        = $result['defi_lanceur_cod'];
        $defi_cible_cod     = $result['defi_cible_cod'];
        $defi_statut        = $result['defi_statut'];
        $defi_abandon_etape = $result['defi_abandon_etape'];
        if ($defi_cible_cod != $perso_cod && $lanceur_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut != 1 || $defi_abandon_etape > 0)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est pas ou plus possible de déclarer ce défi match nul !';
            break;
        }
        $adversaire     = ($defi_cible_cod != $perso_cod) ? $defi_cible_cod : $lanceur_cod;
        $req_nom        = "select perso_nom from perso where perso_cod = $adversaire";
        $adversaire_nom = $pdo->get_value($req_nom, 'perso_nom');

        $dialogue = "Vous vous apprêtez à demander le match nul dans votre défi face à $adversaire_nom.<br />
			Vous pouvez personnaliser ci-dessous le message qui sera envoyé à votre adversaire.
			<form action='#' method='post'>
				<input type='hidden' value='5' name='m' />
				<input type='hidden' value='$defi_cod' name='defi_cod' />
				<input type='hidden' value='defi_nul2' name='methode' />
				<textarea name='defi_message' rows='5' cols='30'>Nous sommes de niveau égal, c’est évident. Cessons ces vaines aggressions et accordons-nous un match nul.</textarea><br />
				<input type='submit' value='Demander le match nul.' class='test' /> - <a href='?m=5'>Annuler</a>
			</form>";
        break;

    case 'defi_nul2':  // Deuxième étape du match nul : envoi à l’adversaire de la demande.
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut, defi_abandon_etape
			from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod        = $result['defi_lanceur_cod'];
        $defi_cible_cod     = $result['defi_cible_cod'];
        $defi_statut        = $result['defi_statut'];
        $defi_abandon_etape = $result['defi_abandon_etape'];
        if ($defi_cible_cod != $perso_cod && $lanceur_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut != 1 || $defi_abandon_etape > 0)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est pas ou plus possible de déclarer ce défi match nul !';
            break;
        }
        $role = ($defi_cible_cod == $perso_cod) ? 'C' : 'L';  // Cible ou lanceur du défi


        $defi_titre = "$perso->perso_nom demande le match nul pour votre défi.";
        $adversaire = ($role == 'C') ? $lanceur_cod : $defi_cible_cod;  // Cible ou lanceur du défi
        if ($defi_message == '')
            $defi_message = 'Grand timide, votre adversaire n’a pas daigné agrémenter son geste de paroles.';
        else
            $defi_message =
                'Votre adversaire a également tenu à vous faire parvenir le message suivant : <hr />' . fonctions::format($defi_message);

        $defi_message = "$nom demande le match nul dans le défi qui vous oppose !
			$defi_message";

        $req_defi =
            "update defi set defi_abandon_etape = 1, defi_abandon_initiateur = '$role' where defi_cod = $defi_cod";
        $stmt     = $pdo->query($req_defi);

        message::Envoyer($perso_cod, $adversaire, $defi_titre, $defi_message, false);

        $dialogue = "Vous avez demandé que le défi soit déclaré nul.";
        break;

    case 'defi_nul3':  // Troisième étape du match nul : match nul déclaré.
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut, defi_abandon_etape
			from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod        = $result['defi_lanceur_cod'];
        $defi_cible_cod     = $result['defi_cible_cod'];
        $defi_statut        = $result['defi_statut'];
        $defi_abandon_etape = $result['defi_abandon_etape'];
        if ($defi_cible_cod != $perso_cod && $lanceur_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut != 1 || $defi_abandon_etape != 1)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est pas ou plus possible de déclarer ce défi match nul !';
            break;
        }
        $role    = ($defi_cible_cod == $perso_cod) ? 'C' : 'L';  // Cible ou lanceur du défi
        $nom = $perso->perso_nom;

        $defi_titre = "Le défi est déclaré match nul.";
        $adversaire = ($role == 'C') ? $lanceur_cod : $defi_cible_cod;  // Cible ou lanceur du défi

        $defi_message = "Le défi qui vous opposait à $nom est déclaré nul !";

        $req_defi = "select defi_abandonner($defi_cod, '2')";
        $stmt     = $pdo->query($req_defi);

        message::Envoyer($perso_cod, $adversaire, $defi_titre, $defi_message, false);

        $dialogue = "Le défi est déclaré match nul.";
        break;

    case 'defi_nul4':  // Quatrième étape du match nul : l’adversaire refuse de déclarer le match nul.
        if (empty($defi_cod))
        {
            $erreur         = true;
            $message_erreur .= 'Paramètres incorrects.';
            break;
        }
        $defi_cod    = fonctions::format($defi_cod, false);
        $params_defi = "select defi_lanceur_cod, defi_cible_cod, defi_statut, defi_abandon_etape
			from defi where defi_cod = $defi_cod";
        if (!$pdo->get_one_record($params_defi))
        {
            $erreur         = true;
            $message_erreur .= 'Défi introuvable.';
            break;
        }
        $lanceur_cod        = $result['defi_lanceur_cod'];
        $defi_cible_cod     = $result['defi_cible_cod'];
        $defi_statut        = $result['defi_statut'];
        $defi_abandon_etape = $result['defi_abandon_etape'];
        if ($defi_cible_cod != $perso_cod && $lanceur_cod != $perso_cod)
        {
            $erreur         = true;
            $message_erreur .= 'Ce défi ne vous concerne pas !';
            break;
        }
        if ($defi_statut != 1 || $defi_abandon_etape != 1)
        {
            $erreur         = true;
            $message_erreur .= 'Il n’est pas ou plus possible de refuser le match nul !';
            break;
        }
        $role = ($defi_cible_cod == $perso_cod) ? 'C' : 'L';  // Cible ou lanceur du défi


        $defi_titre = "$perso->perso_nom refuse le match nul !";
        $adversaire = ($role == 'C') ? $lanceur_cod : $defi_cible_cod;  // Cible ou lanceur du défi

        $defi_message = "Votre adversaire, $nom, a refusé de déclarer comme nul le défi qui vous oppose !
			Vous devrez donc le continuer, à moins que vous ne préfériez tout abandonner ?";

        $req_defi = "update defi set defi_abandon_etape = 0, defi_abandon_initiateur = NULL where defi_cod = $defi_cod";
        $stmt     = $pdo->query($req_defi);

        message::Envoyer($perso_cod, $adversaire, $defi_titre, $defi_message, false);

        $dialogue = "Vous avez refusé de déclarer comme nul votre défi.";
        break;

    default:
        break;
}

if ($erreur)
    $contenu_page .= "<div class='bordiv' style='margin:10px;'>$message_erreur</div>";
elseif ($dialogue != '')
    $contenu_page .= "<div class='bordiv' style='margin:10px;'>$dialogue</div>";

$contenu_page .= $defi_regles;

// Affichage des défis en cours, ou du formulaire de création de défi.
$req = "SELECT defi_cod,
		defi_lanceur_cod,
		case defi_lanceur_cod when $perso_cod then 'Vous-même'
		                      else lanceur.perso_nom
		end as lanceur_nom,
		case $perso_cod when defi_lanceur_cod then cible.perso_cod
		                else lanceur.perso_cod
		end as adversaire_cod,
		case $perso_cod when defi_lanceur_cod then cible.perso_nom
		                else lanceur.perso_nom
		end as adversaire,
		to_char(coalesce(defi_date_fin, defi_date_releve, defi_date_debut), 'DD/MM/YYYY hh24:mi') as defi_date, defi_statut,
		coalesce(defi_abandon_initiateur, '') as defi_abandon_initiateur, defi_abandon_etape
	FROM defi
	INNER JOIN perso lanceur ON lanceur.perso_cod = defi_lanceur_cod
	INNER JOIN perso cible ON cible.perso_cod = defi_cible_cod
	WHERE $perso_cod IN (defi_lanceur_cod, defi_cible_cod) and defi_statut < 2";
if ($result = $pdo->get_one_record($req))
{
    $contenu_page .= '<table><tr><td class="titre" colspan="5">Défi en cours</td></tr>
		<tr>
		<td class="soustitre2"><strong>Adversaire</strong></td>
		<td class="soustitre2"><strong>Initié par...</strong></td>
		<td class="soustitre2" title="Date de début des combats ou de lancer du défi suivant le statut"><strong>Date</strong></td>
		<td class="soustitre2"><strong>Statut</strong></td>
		<td class="soustitre2"><strong>Actions</strong></td></tr>';

    // Données du défi
    $adversaire_cod          = $result['adversaire_cod'];
    $adversaire              = $result['adversaire'];
    $defi_lanceur_cod        = $result['defi_lanceur_cod'];
    $initiateur              = $result['lanceur_nom'];
    $defi_cod                = $result['defi_cod'];
    $defi_date               = $result['defi_date'];
    $defi_statut             = $result['defi_statut'];
    $defi_abandon_initiateur = $result['defi_abandon_initiateur'];
    $defi_abandon_etape      = $result['defi_abandon_etape'];
    $is_lanceur              = ($defi_lanceur_cod == $perso_cod);

    $statut_texte     = '';
    $action_html      = '';
    $lien_rejeter     = "<a href='?m=5&defi_cod=$defi_cod&methode=defi_rejette1'>Refuser !</a>";
    $lien_relever     = "<a href='?m=5&defi_cod=$defi_cod&methode=defi_releve1'>Relever !</a>";
    $lien_abandonner  = "<a href='?m=5&defi_cod=$defi_cod&methode=defi_abandonne1'>Abandonner...</a>";
    $lien_annuler     = "<a href='?m=5&defi_cod=$defi_cod&methode=defi_nul1'>Demander le match nul</a>";
    $lien_annuler_oui = "<a href='?m=5&defi_cod=$defi_cod&methode=defi_nul3'>Accepter</a>";
    $lien_annuler_non = "<a href='?m=5&defi_cod=$defi_cod&methode=defi_nul4'>refuser</a>";
    switch ($defi_statut)
    {
        case 0:
            $statut_texte = 'Défi lancé';
            if (!$is_lanceur)
                $action_html = "$lien_relever - $lien_rejeter";
            else
                $action_html = $lien_abandonner;
            break;

        case 1:
            if ($defi_abandon_etape == 0)
            {
                $statut_texte = 'Défi en cours !';
                $action_html  = "$lien_annuler - $lien_abandonner";
            }
            if ($defi_abandon_etape == 1 &&
                ($is_lanceur && $defi_abandon_initiateur == 'L'
                 || !$is_lanceur && $defi_abandon_initiateur == 'C'))
            {
                $statut_texte = 'En cours<br />Vous avez demandé le match nul.';
                $action_html  = "$lien_abandonner";
            }
            if ($defi_abandon_etape == 1 &&
                ($is_lanceur && $defi_abandon_initiateur == 'C'
                 || !$is_lanceur && $defi_abandon_initiateur == 'L'))
            {
                $statut_texte = "En cours<br />$adversaire a demandé le match nul.";
                $action_html  = "$lien_annuler_oui ou $lien_annuler_non le match nul ?";
            }
            break;
    }

    $contenu_page .= "<tr>
		<td class='soustitre2'><a href='visu_desc_perso.php?visu=$adversaire_cod'><strong>$adversaire</strong></a></td>
		<td class='soustitre2'>$initiateur</td>
		<td class='soustitre2'>$defi_date</td>
		<td class='soustitre2'>$statut_texte</td>
		<td class='soustitre2'>$action_html</td></tr></table>";
} else
{
    $contenu_page .= "<div><div style='text-align: left; width: 600px;'><strong>Lancer un défi !</strong>
		<form action='#' method='post'>
			<input type='hidden' value='5' name='m' />
			Je souhaite défier <input type='text' name='cible_nom' value='' />.
			<input type='hidden' value='defi_lance1' name='methode' />
			<input type='submit' value='Qu’il morde la poussière !' class='test' />
		</form></div></div>";
}

// Liste des défis que le perso a lancés / reçus
// Affichage sur deux colonnes : défis lancés puis défis reçus.
$contenu_page .= '<table><tr><td class="titre">Défis lancés</td><td class="titre">Défis reçus</td></tr>';
$contenu_page .= '<tr><td valign="top" style="max-height:300px; overflow:auto;">';

$req_defis_lanceur = "SELECT defi_cible_cod as adversaire_cod, perso_nom as adversaire,
		defi_vainqueur, to_char(coalesce(defi_date_fin, defi_date_releve, defi_date_debut), 'DD/MM/YYYY hh24:mi') as defi_date, defi_statut
	FROM defi
	INNER JOIN perso ON perso_cod = defi_cible_cod
	WHERE defi_lanceur_cod = $perso_cod and defi_statut > 1
	ORDER BY coalesce(defi_date_fin, defi_date_releve, defi_date_debut) desc";
$contenu_page      .= afficherDefisPasses($req_defis_lanceur, true);

$contenu_page .= '</td><td valign="top" style="max-height:300px; overflow:auto;">';

$req_defis_cible = "SELECT defi_lanceur_cod as adversaire_cod, perso_nom as adversaire,
		defi_vainqueur, to_char(coalesce(defi_date_fin, defi_date_releve, defi_date_debut), 'DD/MM/YYYY hh24:mi') as defi_date, defi_statut
	FROM defi
	INNER JOIN perso ON perso_cod = defi_lanceur_cod
	WHERE defi_cible_cod = $perso_cod and defi_statut > 1
	ORDER BY coalesce(defi_date_fin, defi_date_releve, defi_date_debut) desc";
$contenu_page    .= afficherDefisPasses($req_defis_cible, false);

$contenu_page .= '</td></tr></table>';

// Affiche la liste des défis passée en paramètres
function afficherDefisPasses($req_defis, $is_lanceur)
{

    $pdo          = new bddpdo;
    $stmt         = $pdo->query($req_defis);
    $resultat     = '';
    $existe_defis = ($stmt->rowCount() > 0);

    if (!$existe_defis)
    {
        $verbe    = ($is_lanceur) ? 'lancé' : 'reçu';
        $resultat = "<p style='font-weight: bold;'>Vous n’avez encore $verbe aucun défi.</p>";
    } else
    {
        $resultat = '<table><tr><td class="soustitre2" title="Date de fin, de début des combats ou de lancer du défi suivant le statut"><strong>Date</strong></td>
			<td class="soustitre2"><strong>Adversaire</strong></td>
			<td class="soustitre2"><strong>Statut</strong></td></tr>';
    }

    while ($result = $stmt->fetch())
    {
        // Données du défi
        $adversaire_cod = $result['adversaire_cod'];
        $adversaire     = $result['adversaire'];
        $defi_vainqueur = $result['defi_vainqueur'];
        $defi_date      = $result['defi_date'];
        $defi_statut    = $result['defi_statut'];

        $statut_texte = '';
        switch ($defi_statut)
        {
            case 2:
                $rejet        = $defi_vainqueur == 'L';
                $statut_texte = ($rejet) ? 'Rejeté' : 'Annulé';
                break;

            case 3:
                $gagne        = $is_lanceur && $defi_vainqueur == 'L'
                                || !$is_lanceur && $defi_vainqueur == 'C';
                $statut_texte = ($gagne) ? '<strong>Gagné par KO</strong>' : '<strong>Perdu par KO</strong>';
                break;

            case 4:
                $gagne        = $is_lanceur && $defi_vainqueur == 'L'
                                || !$is_lanceur && $defi_vainqueur == 'C';
                $statut_texte = ($gagne) ? 'Gagné par forfait' : 'Perdu par forfait';
                break;

            case 5:
                $statut_texte = 'Match nul';
                break;
        }

        $resultat .= "<tr><td class='soustitre2'>$defi_date</td>
			<td class='soustitre2'><a href='visu_desc_perso.php?visu=$adversaire_cod'><strong>$adversaire</strong></a></td>
			<td class='soustitre2'>$statut_texte</td></tr>";
    }
    if ($existe_defis) $resultat .= '</table>';
    return $resultat;
}
