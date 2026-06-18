<?php
include_once '../includes/tools.php';

$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Compteurs Système</div><br />';

$erreur         = false;
$message_erreur = '';

// Pour factoriser le code, on commence par récupérer le nom des tables et colonnes pour le type de renommée sur lequel on travaille
$lesTypes = array('qa');

$lesTables = array(
    'qa' => 'compteur'
);

$lesNoms = array(
    'qa' => 'Compteurs Système',
);

$lesColonnes = array(
    'qa' => array(
        'cod' => 'compteur_cod',
        'typ' => 'compteur_type',
        'def' => 'compteur_init',
        'min' => 'compteur_min',
        'max' => 'compteur_max',
        'lib' => 'compteur_libelle'
    ),
);

$typeCompteur = '';
$log = '';

// Ce bloc peut sûrement être remplacé par une expression rationnelle ou un simple substring, pour déterminer le type...
// J’ai la flemme de le changer. Et je préfère le déterminisme ^^
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case 'cpt_qa_add':
    case 'cpt_qa_upd':
    case 'cpt_qa_del':
        $typeCompteur = 'qa';

    default:
        $typeCompteur = 'qa';
        break;
}
if ($typeCompteur != '')
{
    $table_cpt = $lesTables[$typeCompteur];
    $col_cod = $lesColonnes[$typeCompteur]['cod'];
    $col_def = $lesColonnes[$typeCompteur]['def'];
    $col_min= $lesColonnes[$typeCompteur]['min'];
    $col_max = $lesColonnes[$typeCompteur]['max'];
    $col_typ = $lesColonnes[$typeCompteur]['typ'];
    $col_lib = $lesColonnes[$typeCompteur]['lib'];
    $log_commpteur = $lesNoms[$typeCompteur];

    $compteur_cod = isset($_REQUEST['compteur_cod']) ? $_REQUEST['compteur_cod'] : '';
    $compteur_def = isset($_REQUEST['compteur_def']) ? $_REQUEST['compteur_def'] : '';
    $compteur_min = isset($_REQUEST['compteur_min']) ? $_REQUEST['compteur_min'] : '';
    $compteur_max = isset($_REQUEST['compteur_max']) ? $_REQUEST['compteur_max'] : '';
    $compteur_typ = isset($_REQUEST['compteur_typ']) ? $_REQUEST['compteur_typ'] : '';
    $compteur_lib = isset($_REQUEST['compteur_lib']) ? $_REQUEST['compteur_lib'] : '';
}

switch ($methode)
{
    case 'cpt_qa_upd':    // Modifie une ligne de renommée
        $erreur = !isset($compteur_cod) || !isset($compteur_def) || !isset($compteur_min) || !isset($compteur_max) || !isset($compteur_typ) || !isset($compteur_lib)
            || !is_numeric($compteur_cod) || !is_numeric($compteur_def) || !is_numeric($compteur_typ);
        $message_erreur = '';
        $compteur_def_orig = '';
        $compteur_min_orig = '';
        $compteur_max_orig = '';
        $compteur_typ_orig = '';
        $compteur_lib_orig = '';
        if ($erreur) $message_erreur = 'Paramètres manquants ou incorrects.';
        else
        {
            $req_verif = "select $col_def, $col_min, $col_max, $col_typ, $col_lib from $table_cpt where $col_cod = $compteur_cod";
            $stmt = $pdo->query($req_verif);

            $erreur = !$result = $stmt->fetch();
        }

        if ($erreur) {
            $message_erreur = 'Ligne de compteur';
        }
        else
        {
            $compteur_def_orig = $result[$col_def];
            $compteur_min_orig = $result[$col_min];
            $compteur_max_orig = $result[$col_max];
            $compteur_typ_orig = $result[$col_typ];
            $compteur_lib_orig = $result[$col_lib];
            $log .= "	Modification $log_commpteur n°$compteur_cod « $compteur_lib_orig ».\n";

            $compteur_lib = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $compteur_lib))));

            if ($compteur_lib_orig != $compteur_lib)
                $log .= "	Modification nom : « $compteur_lib ».\n";
            if ($compteur_typ_orig != $compteur_typ)
                $log .= "	Modification du type : « ".( $compteur_typ == 0 ? "Global" : "Individuel")." ».\n";
            if ($compteur_def_orig != $compteur_def)
                $log .= "	Modification de la valeur d'init : « $compteur_def ».\n";
            if ($compteur_min_orig != $compteur_min)
                $log .= "	Modification de la valeur min : « $compteur_min ».\n";
            if ($compteur_max_orig != $compteur_max)
                $log .= "	Modification de la valeur max : « $compteur_max ».\n";

            $req_upd = "update $table_cpt set 
                        $col_def = $compteur_def, 
                        $col_min = ".(is_numeric($compteur_min) ? $compteur_min : 'null' ).", 
                        $col_max = ".(is_numeric($compteur_max) ? $compteur_max : 'null' ).", 
                        $col_typ = $compteur_typ, $col_lib = '$compteur_lib' where $col_cod = $compteur_cod";
            $stmt = $pdo->query($req_upd);
        }
        break;

    case 'cpt_qa_add':    // Créer une ligne de compteur
        $erreur = !isset($compteur_def) || !isset($compteur_min) || !isset($compteur_max) || !isset($compteur_typ) || !isset($compteur_lib)  || !is_numeric($compteur_def) || !is_numeric($compteur_typ);
        $message_erreur = '';
        if ($erreur) {
            $message_erreur = 'Paramètres manquants ou incorrects.';
        }
        else
        {
            $compteur_lib = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $compteur_lib))));

            $log .= "	Création de $log_commpteur « $compteur_lib » du type ".( $compteur_typ == 0 ? "Global" : "Individuel"). " valeur d'init: $compteur_def .\n";

            $req_ins = "insert into $table_cpt ($col_def, $col_min, $col_max, $col_typ, $col_lib)
				values ($compteur_def, ".(is_numeric($compteur_min) ? $compteur_min : 'null' ).", ".(is_numeric($compteur_max) ? $compteur_max : 'null' ).", $compteur_typ, '$compteur_lib')";
            $stmt = $pdo->query($req_ins);
        }
        break;

    case 'cpt_qa_del':    // Supprime une ligne de compteur
        $erreur = !isset($compteur_cod) || !is_numeric($compteur_cod);
        $message_erreur = '';
        $compteur_def_orig = '';
        $compteur_typ_orig = '';
        $compteur_lib_orig = '';
        if ($erreur) {
            $message_erreur = 'Paramètres manquants ou incorrects.';
        }
        else
        {
            $req_verif = "select $col_def, $col_min, $col_max, $col_typ, $col_lib
				from $table_cpt where $col_cod = $compteur_cod";
            $stmt = $pdo->query($req_verif);

            $erreur = !$result = $stmt->fetch();
        }

        if ($erreur) {
            $message_erreur = 'Ligne de compteur.';
        }
        else
        {
            $compteur_def_orig = $result[$col_def];
            $compteur_min_orig = $result[$col_min];
            $compteur_max_orig = $result[$col_max];
            $compteur_typ_orig = $result[$col_typ];
            $compteur_lib_orig = $result[$col_lib];

            $log .= "	Suppression de $log_commpteur n°$compteur_cod « $compteur_lib_orig » du type ".( $compteur_typ == 0 ? "Global" : "Individuel")."valeur d'init: $compteur_def_orig valeur min: $compteur_min_orig valeur max: $compteur_max_orig .\n";

            $req_del = "delete from $table_cpt where $col_cod = $compteur_cod";
            $stmt = $pdo->query($req_del);
        }
        break;
}
if (!$erreur && $log != '')
{
    echo "<div class='bordiv'><strong>Mise à jour de $log_commpteur.</strong><br /><pre>$log</pre></div>";
    writelog($log,'params');
}
else if ($erreur && $message_erreur != '')
{
    echo "<div class='bordiv'><strong>Erreur !</strong><br /><pre>$message_erreur</pre></div>";
}

echo '<p>Liste des compteurs du jeu</p>';

echo '<script type="text/javascript">
	function montre_compteur(i)
	{
		document.getElementById("table_compteur_qa").style.display = "none";
		document.getElementById("table_compteur_" + i).style.display = "block";

		document.getElementById("lien_qa").style.fontWeight = "normal";
		document.getElementById("lien_" + i).style.fontWeight = "bold";
	}
	</script>';
/* echo '<p>Choisissez le type de compteur :
	<a href="javascript:montre_compteur(\'qa\');" id="lien_qa">QA</a>
	.</p>'; */

foreach ($lesTypes as $i)
{
    $table_cpt = $lesTables[$i];
    $col_cod = $lesColonnes[$i]['cod'];
    $col_def = $lesColonnes[$i]['def'];
    $col_min = $lesColonnes[$i]['min'];
    $col_max = $lesColonnes[$i]['max'];
    $col_typ = $lesColonnes[$i]['typ'];
    $col_lib = $lesColonnes[$i]['lib'];
    $log_commpteur = $lesNoms[$i];
    echo "<table id='table_compteur_$i'><tr>
			<td class='titre' colspan='9'><strong>$log_commpteur</strong></td></tr>";
    echo '<tr>
			<td class="titre"><strong>CODE</strong></td>
			<td class="titre"><strong>Titre</strong></td>
			<td class="titre"><strong>Type</strong></td>
			<td class="titre"><strong>Init</strong></td>
			<td class="titre"><strong>Min</strong></td>
			<td class="titre"><strong>Max</strong></td>
			<td class="titre" colspan="2"><strong>Action</strong></td>
			<td class="titre" ><strong>Problèmes détectés </strong></td>
		  </tr>';
    echo "<tr><form method='POST' action='#'>
		<td class='titre' style='padding:2px;'></td>
		<td class='titre' style='padding:2px;'><input name='compteur_lib' type='text' size='64' /></td>
		<td class='titre' style='padding:2px;'>".create_selectbox("compteur_typ", array("0"=>"Global", "1"=>"Individuel"), 0)."</td>
		<td class='titre' style='padding:2px;'><input name='compteur_def' type='text' size='6' value='0'/> </td>
		<td class='titre' style='padding:2px;'><input name='compteur_min' type='text' size='6' value=''/> </td>
		<td class='titre' style='padding:2px;'><input name='compteur_max' type='text' size='6' value=''/> </td>
		<td class='titre' style='padding:2px;' colspan='2'><input type='hidden' name='methode' value='cpt_" . $i . "_add' />
			<input type='submit' value='Ajouter' class='test' /></td><td class='titre'></td>
		</form></tr>";

    $req = "select $col_cod, $col_def, $col_min, $col_max, $col_typ, $col_lib from $table_cpt order by $col_cod";
    $stmt = $pdo->query($req);
    $prev_type = false;
    while ($result = $stmt->fetch())
    {
        $compteur_cod = $result[$col_cod];
        $compteur_def = $result[$col_def];
        $compteur_min = $result[$col_min];
        $compteur_max = $result[$col_max];
        $compteur_typ = $result[$col_typ];
        $compteur_lib = str_replace('\'', '’', $result[$col_lib]);

        $erreur = false;
        $message_erreur = '';

        echo "<tr><form method='POST' action='#'>
			<td style='text-align: center; padding:2px;'>$compteur_cod</td>
			<td style='padding:2px;'><input name='compteur_lib' type='text' size='64' value='$compteur_lib' /></td>
			<td style='padding:2px;'>".create_selectbox("compteur_typ", array("0"=>"Global", "1"=>"Individuel"), $compteur_typ)."</td> 
			<td style='padding:2px;'><input name='compteur_def' type='text' size='6' value='$compteur_def' /></td>
			<td style='padding:2px;'><input name='compteur_min' type='text' size='6' value='$compteur_min' /></td>
			<td style='padding:2px;'><input name='compteur_max' type='text' size='6' value='$compteur_max' /></td> ";


        echo "<td style='padding:2px;'>
			<input type='hidden' name='methode' value='cpt_" . $i . "_upd' />
			<input type='hidden' name='compteur_cod' value='$compteur_cod' />
			<input type='submit' value='Modifier' class='test' />
			</td></form>";
        echo "<td style='padding:2px;'><form method='POST' action='#' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer ce compteur ?\")'>
			<input type='hidden' name='methode' value='cpt_" . $i . "_del' />
			<input type='hidden' name='compteur_cod' value='$compteur_cod' />
			<input type='submit' value='Supprimer' class='test' />
			</form></td>";
        if ($erreur)
            echo "<td style='padding:2px; color:#660000'><p>$message_erreur</p></td>";
        else
            echo '<td></td>';
        echo "</tr>";
    }
}

if ($typeCompteur != '')
{
    echo "<script type='text/javascript'>
			montre_compteur('$typeCompteur');
		</script>";
}

echo '</table></div>';

