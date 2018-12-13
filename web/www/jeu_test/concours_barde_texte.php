<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
$erreur = 0;
ob_start();

if(!isset($_POST['texte_cod']))
{
	echo '<p>Erreur : aucun texte sélectionné</p>';
	$erreur = 1;
}
if ($erreur == 0)
{
	$texte_cod = $_POST['texte_cod'];
	
	// Récupération des données du texte (ainsi que la saison)
	$req_texte = "SELECT ebar_cbar_cod, ebar_perso_cod, ebar_titre, ebar_texte, COALESCE(note, 0) as note, to_char(ebar_date,'DD/MM/YYYY') as date,
				COALESCE(nbvote, 0) as nbvote, perso_nom
				FROM concours_barde_epreuves
				INNER JOIN perso ON perso_cod = ebar_perso_cod 
				LEFT OUTER JOIN  (
						SELECT nbar_ebar_cod, count(*) as nbvote, SUM(COALESCE(nbar_note, 0)) as note
						FROM concours_barde_note GROUP BY nbar_ebar_cod) t
					ON t.nbar_ebar_cod = ebar_cod
				WHERE ebar_cod = $texte_cod";
	$db->query($req_texte);
	$db->next_record();
	$cbar_cod = $db->f('ebar_cbar_cod');
	$ebar_perso_cod = $db->f('ebar_perso_cod');
	$ebar_titre = $db->f('ebar_titre');
	$ebar_texte = $db->f('ebar_texte');
	$notetotale = $db->f('note');
	$date = $db->f('date');
	$nbvote = $db->f('nbvote');
	$perso_nom = $db->f('perso_nom');
	
	// Les membres du jury
	$req_jury = "SELECT jbar_cod, jbar_perso_cod, nbar_note, nbar_commentaire
					FROM concours_barde_jury
					LEFT OUTER JOIN concours_barde_note ON nbar_jbar_cod = jbar_cod AND nbar_ebar_cod = $texte_cod
					WHERE jbar_cbar_cod = $cbar_cod";
	$db->query($req_jury);
	
	$nbJury = $db->nf();
	$leJury = array();
	$lesNotes = array();
	$lesCommentaires = array();
	$isJury = false;
	$iself = -1;
	$notationComplete = true;
	$jury_cod = -1;
	$i = 0;
	while ($db->next_record())
	{
		$leJury[$i] = $db->f('jbar_cod');
		$lesNotes[$i] = $db->f('nbar_note');
		$lesCommentaires[$i] = $db->f('nbar_commentaire');
		if ($db->f('jbar_perso_cod') == $perso_cod)
		{
			$isJury = true;
			$jury_cod = $db->f('jbar_cod');
			$iself = $i;
		}
		if ($db->f('nbar_note') == null)
			$notationComplete = false;
		$i++;
	}

	// TRAITEMENT DES ACTIONS.
	if(isset($_POST['methode']))
	{
		switch($_POST['methode'])
		{
			case "noter":
				$note =  intval($_POST['note']);
				$commentaire =  $_POST['commentaire'];
				$commentaire = str_replace(";",chr(127),$commentaire);
				$commentaire = htmlspecialchars($commentaire);
				$commentaire = nl2br($commentaire);
				$commentaire = pg_escape_string($commentaire);

				$erreur = 0;
				if($note < 0 || $note > 20)
				{
					echo "<p>La note doit être un entier compris entre 0 et 20</p>";
					$erreur = 1;
				}
				if(!$isJury)
				{
					echo "<p>Vous n’êtes pas membre du jury !</p>";
					$erreur = 1;
				}
				if($erreur == 0)
				{
					$req_deja_note = "SELECT * FROM concours_barde_note WHERE nbar_ebar_cod = $texte_cod AND nbar_jbar_cod = $jury_cod";
					$db->query($req_deja_note);
					$req_note = '';
					if ($db->nf() == 0)
						$req_note = "INSERT INTO concours_barde_note (nbar_ebar_cod, nbar_jbar_cod, nbar_note, nbar_commentaire)
									VALUES ($texte_cod, $jury_cod, $note, e'$commentaire')";
					else
						$req_note = "UPDATE concours_barde_note SET nbar_note = $note, nbar_commentaire = e'$commentaire'
									WHERE nbar_ebar_cod = $texte_cod AND nbar_jbar_cod = $jury_cod";
					$db->query($req_note);
					echo "<p>Votre évaluation a été enregistrée !</p>";
					$lesNotes[$iself] = $note;
					$lesCommentaires[$iself] = $commentaire;
				}
			break;
			case "censurer":
			break;
		}
	}

?>
<p align="center"><br>
<?php 	
	echo '<table width="100%">';
	echo '<tr><td class="soustitre2"><strong>Auteur:</strong></td><td class="soustitre2">' . (($notationComplete) ? $perso_nom : '<em>- Caché -</em>') . '</td></tr>';
	if (!$notationComplete)
		echo '<tr><td class="soustitre2" colspan="2"><strong>Tant que tous les membres du jury n’ont pas donné leur évaluation, le candidat reste anonyme.</strong></td></tr>';
		
	echo '<tr><td class="soustitre2" colspan="2" style="text-align:center; font-weight:bold;">Évaluations du jury</td></tr>';

	for ($i = 0; $i < sizeof($leJury); $i++)
	{
		if (isset($lesNotes[$i]) && $lesNotes[$i] != null)
		{
			echo '<tr><td class="soustitre2">Juré ' . ($i + 1) . ' - <strong>Note : </strong>' . $lesNotes[$i] . '/20</td>
				<td class="soustitre2"><strong>Commentaire : </strong>' . str_replace(chr(127), ";", $lesCommentaires[$i]) . '</td></tr>';
		}
		else if ($jury_cod == $leJury[$i])
		{
			echo '<tr><td class="soustitre2">
					<form method="post">
						Juré ' . ($i + 1) . ' - Évaluer ce texte. <strong>Note : </strong>
						<input type="hidden" name="methode" value="noter" />
						<input type="hidden" name="texte_cod" value="' . $texte_cod . '" />
						<input type="text" name="note" value="" />(entre 0 et 20)</td><td>
						<strong>Commentaire : </strong><textarea rows="3" name="commentaire"></textarea>
						<input type="submit" value="Noter !" class="test" />
					</form>
				</td></tr>';
		}
	}
	echo '<tr><td class="soustitre2" colspan="2" style="text-align:center; font-weight:bold; font-size:1.1em;">&nbsp;&nbsp;</td></tr>
		<tr><td class="soustitre2"><strong>Date</strong></td><td class="soustitre2">' . $date . '</td></tr>
		<tr><td class="soustitre2"><strong>Titre</strong></td><td class="soustitre2" style="text-align:center; font-weight:bold; font-size:1.1em;">
			' . str_replace(chr(127), ";", $ebar_titre) . '
		</td></tr>
		<tr><td class="soustitre2" colspan="2" style="margin:20px;">
		' . str_replace(chr(127), ";", $ebar_texte) . '
		</td></tr>
		<tr><td class="soustitre2" colspan="2"><a href="concours_barde.php">Retour à la liste</a></td></tr>
		</table>';
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
