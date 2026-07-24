<?php

include_once '../includes/tools.php';

$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Valeurs de Compteur</div><br />';

$erreur = false;
$message_erreur = '';
$log = '';

$compteur_cod = isset($_REQUEST['compteur_cod']) && is_numeric($_REQUEST['compteur_cod']) ? (int)$_REQUEST['compteur_cod'] : 0;

if ($compteur_cod <= 0) {
    echo "<div class='bordiv'><strong>Erreur !</strong><br /><pre>Aucun compteur spécifié.</pre></div>";
    echo '</div>';
    return;
}

// Récupération des infos du compteur (nom, type) pour affichage et contrôle
$req = "select compteur_libelle, compteur_type, compteur_init, compteur_min, compteur_max from compteur where compteur_cod = $compteur_cod";
$stmt = $pdo->query($req);
$compteur_info = $stmt->fetch();

if (!$compteur_info) {
    echo "<div class='bordiv'><strong>Erreur !</strong><br /><pre>Compteur n°$compteur_cod introuvable.</pre></div>";
    echo '</div>';
    return;
}

$compteur_lib = $compteur_info['compteur_libelle'];
$compteur_typ = $compteur_info['compteur_type'];
$compteur_init = $compteur_info['compteur_init'];
$compteur_min = $compteur_info['compteur_min'];
$compteur_max = $compteur_info['compteur_max'];

$methode = isset($_REQUEST['methode']) ? $_REQUEST['methode'] : '';

$comptval_cod = isset($_REQUEST['comptval_cod']) ? $_REQUEST['comptval_cod'] : '';
$comptval_perso = isset($_REQUEST['comptval_perso_cod']) ? $_REQUEST['comptval_perso_cod'] : '';
$comptval_valeur = isset($_REQUEST['comptval_valeur']) ? $_REQUEST['comptval_valeur'] : '';

switch ($methode) {
    case 'cptval_upd':    // Modifie la valeur d'une ligne
        $erreur = !isset($comptval_cod) || !isset($comptval_valeur) || !is_numeric($comptval_cod) || !is_numeric($comptval_valeur);
        $message_erreur = '';
        if ($erreur) {
            $message_erreur = 'Paramètres manquants ou incorrects.';
        } else {
            $req_verif = "select comptval_valeur, comptval_perso_cod from compteur_valeur where comptval_cod = $comptval_cod and comptval_compteur_cod = $compteur_cod";
            $stmt = $pdo->query($req_verif);
            $result = $stmt->fetch();
            $erreur = !$result;

            if ($erreur) {
                $message_erreur = 'Ligne de valeur introuvable.';
            } else {
                $valeur_orig = $result['comptval_valeur'];

                if ($valeur_orig != $comptval_valeur)
                    $log .= "	Modification de la valeur du compteur « $compteur_lib » (ligne n°$comptval_cod) : « $valeur_orig » => « $comptval_valeur ».\n";

                $req_upd = "update compteur_valeur set comptval_valeur = $comptval_valeur where comptval_cod = $comptval_cod";
                $stmt = $pdo->query($req_upd);
            }
        }
        break;

    case 'cptval_add':    // Créer une nouvelle ligne de valeur (compteur individuel uniquement)
        $erreur = !isset($comptval_valeur) || !is_numeric($comptval_valeur);
        $message_erreur = '';

        if ($compteur_typ == 1) {
            // compteur individuel : un perso_cod est requis
            $erreur = $erreur || !isset($comptval_perso) || !is_numeric($comptval_perso) || $comptval_perso == '';
        } else {
            // compteur global : pas de perso_cod (on force à null), et une seule ligne doit exister
            $comptval_perso = '';
        }

        if ($erreur) {
            $message_erreur = 'Paramètres manquants ou incorrects.';
        } else {
            $perso_sql = ($comptval_perso !== '') ? (int)$comptval_perso : 'null';

            $req_ins = "insert into compteur_valeur (comptval_compteur_cod, comptval_perso_cod, comptval_valeur)
				values ($compteur_cod, $perso_sql, $comptval_valeur)";
            $stmt = $pdo->query($req_ins);

            if ($stmt === false) {
                $erreur = true;
                $message_erreur = 'Une ligne existe déjà pour ce perso (ou pour le global), ou une autre erreur est survenue.';
            } else {
                $log .= "	Création d'une valeur pour le compteur « $compteur_lib »" . ($comptval_perso !== '' ? " (perso n°$comptval_perso)" : " (global)") . " : $comptval_valeur.\n";
            }
        }
        break;

    case 'cptval_del':    // Supprime une ligne de valeur
        $erreur = !isset($comptval_cod) || !is_numeric($comptval_cod);
        $message_erreur = '';

        if ($erreur) {
            $message_erreur = 'Paramètres manquants ou incorrects.';
        } else {
            $req_verif = "select comptval_valeur, comptval_perso_cod from compteur_valeur where comptval_cod = $comptval_cod and comptval_compteur_cod = $compteur_cod";
            $stmt = $pdo->query($req_verif);
            $result = $stmt->fetch();
            $erreur = !$result;

            if ($erreur) {
                $message_erreur = 'Ligne de valeur introuvable.';
            } else {
                $log .= "	Suppression de la valeur (ligne n°$comptval_cod) du compteur « $compteur_lib » : « {$result['comptval_valeur']} ».\n";

                $req_del = "delete from compteur_valeur where comptval_cod = $comptval_cod";
                $stmt = $pdo->query($req_del);
            }
        }
        break;
}

if (!$erreur && $log != '') {
    echo "<div class='bordiv'><strong>Mise à jour des valeurs de « $compteur_lib ».</strong><br /><pre>$log</pre></div>";
    writelog($log, 'params');
} else if ($erreur && $message_erreur != '') {
    echo "<div class='bordiv'><strong>Erreur !</strong><br /><pre>$message_erreur</pre></div>";
}

echo "<p>Valeurs du compteur <strong>$compteur_lib</strong> (n°$compteur_cod) — type : <strong>" . ($compteur_typ == 0 ? 'Global' : 'Individuel') . "</strong>";
if ($compteur_init !== null && $compteur_init !== '') echo " — init : $compteur_init";
if ($compteur_min !== null && $compteur_min !== '') echo " — min : $compteur_min";
if ($compteur_max !== null && $compteur_max !== '') echo " — max : $compteur_max";
echo "</p>";

echo "<br><table>";
echo "<tr>
		<td class='titre'><strong>CODE</strong></td>
		<td class='titre'><strong>Perso</strong></td>
		<td class='titre'><strong>Valeur</strong></td>
		<td class='titre' colspan='2'><strong>Action</strong></td>
	  </tr>";

// Ligne d'ajout (uniquement pertinent pour un compteur individuel, ou pour créer la ligne globale manquante)
echo "<tr><form method='POST' action='#'>
	<td class='titre' style='padding:2px;'></td>
	<td class='titre' style='padding:2px;'>";
if ($compteur_typ == 1) {
    echo "<input name='comptval_perso_cod' type='text' size='10' placeholder='perso_cod' />";
} else {
    echo "<em>Global</em>";
}
echo "</td>
	<td class='titre' style='padding:2px;'><input name='comptval_valeur' type='text' size='10' value='0' /></td>
	<td class='titre' style='padding:2px;' colspan='2'>
		<input type='hidden' name='methode' value='cptval_add' />
		<input type='hidden' name='compteur_cod' value='$compteur_cod' />
		<input type='submit' value='Ajouter' class='test' />
	</td>
	</form></tr>";

$req = "select cv.comptval_cod, cv.comptval_perso_cod, cv.comptval_valeur, p.perso_nom
        from compteur_valeur cv
        left join perso p on p.perso_cod = cv.comptval_perso_cod
        where cv.comptval_compteur_cod = $compteur_cod
        order by p.perso_nom nulls first, cv.comptval_cod";
$stmt = $pdo->query($req);

while ($result = $stmt->fetch()) {
    $comptval_cod = $result['comptval_cod'];
    $comptval_perso = $result['comptval_perso_cod'];
    $comptval_valeur = $result['comptval_valeur'];
    $perso_nom = $result['perso_nom'];

    $affichage_perso = ($comptval_perso === null) ? '<em>Global</em>' : htmlspecialchars($perso_nom) . " (n°$comptval_perso)";

    echo "<tr><form method='POST' action='#'>
		<td style='text-align: center; padding:2px;'>$comptval_cod</td>
		<td style='padding:2px;'>$affichage_perso</td>
		<td style='padding:2px;'><input name='comptval_valeur' type='text' size='10' value='$comptval_valeur' /></td>";

    echo "<td style='padding:2px;'>
		<input type='hidden' name='methode' value='cptval_upd' />
		<input type='hidden' name='compteur_cod' value='$compteur_cod' />
		<input type='hidden' name='comptval_cod' value='$comptval_cod' />
		<input type='submit' value='Modifier' class='test' />
		</td></form>";
    echo "<td style='padding:2px;'><form method='POST' action='#' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer cette valeur ?\")'>
		<input type='hidden' name='methode' value='cptval_del' />
		<input type='hidden' name='compteur_cod' value='$compteur_cod' />
		<input type='hidden' name='comptval_cod' value='$comptval_cod' />
		<input type='submit' value='Supprimer' class='test' />
		</form></td>";
    echo "</tr>";
}

echo "</table>";
echo "<br><p><a href='admin_params.php?onglet=compteur'>&laquo; Retour à la liste des compteurs</a></p><br><br>";
echo '</div>';
