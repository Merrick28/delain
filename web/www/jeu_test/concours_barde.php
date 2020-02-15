<?php
include "blocks/_header_page_jeu.php";
ob_start();


// SAISON
if(isset($_POST['saison_concours']))
{
	$saison_concours =  intval($_POST['saison_concours']);
}
else
{
	$req_concours = 'SELECT MAX(cbar_cod) as cbar_cod from concours_barde WHERE CURRENT_DATE >= cbar_date_teaser';
	$stmt = $pdo->query($req_concours);
	$result = $stmt->fetch();
	$saison_concours = $result['cbar_cod'];
}

// Infos sur le concours
$req_concours = "SELECT cbar_saison, to_char(cbar_date_ouverture,'DD/MM/YYYY à hh24:mi:ss') as cbar_date_ouverture, 
					to_char(cbar_date_teaser,'DD/MM/YYYY à hh24:mi:ss') as cbar_date_teaser, 
					to_char(cbar_fermeture,'DD/MM/YYYY à hh24:mi:ss') as cbar_fermeture, cbar_description,
					case when CURRENT_DATE between cbar_date_teaser and cbar_date_ouverture then 1 else 0 end as introduction,
					case when CURRENT_DATE between cbar_date_ouverture and cbar_fermeture then 1 else 0 end as ouvert,
					case when CURRENT_DATE < cbar_date_teaser then 1 else 0 end as futur,
					case when CURRENT_DATE > cbar_fermeture then 1 else 0 end as ferme
				FROM concours_barde WHERE cbar_cod = $saison_concours";
$stmt = $pdo->query($req_concours);
$result = $stmt->fetch();
$saison = $result['cbar_saison'];
$date_ouverture = $result['cbar_date_ouverture'];
$date_teaser = $result['cbar_date_teaser'];
$fermeture = $result['cbar_fermeture'];
$description = $result['cbar_description'];
$introduction = ($result['introduction'] == 1);
$ouvert = ($result['ouvert'] == 1);
$futur = ($result['futur'] == 1);
$ferme = ($result['ferme'] == 1);

echo "<div class='barrTitle'>Concours de barde, saison $saison.</div>";

// TRAITEMENT DES ACTIONS.
if(isset($_POST['methode']))
{
	switch($_POST['methode'])
	{
		case "participer":
			if (!$ouvert)
			{
				echo '<p>Cette saison est fermée, participation impossible !</p>';
				break;
			}
			$titre = $_POST['titre'];
			$corps = $_POST['contenu'];
			$req = "SELECT ebar_perso_cod FROM concours_barde_epreuves WHERE ebar_perso_cod = $perso_cod AND ebar_cbar_cod = $saison_concours";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			if ($stmt->rowCount() != 0)
			{
				echo "<p><br><strong>Vous avez déjà participé au concours de barde pour la saison en cours !
					<br>Un barde ne peut pleinement s’exprimer dans un concours qu’une seule fois.</strong></p>";
				$erreur = 1;
			}
			else if (strlen($titre) >= 50)
			{
				echo "<p><strong>Votre titre est trop long, merci de le raccourcir !</strong></p>";
				$erreur = 1;
			}
			else
			{
				$titre = htmlspecialchars($titre);
				$corps = htmlspecialchars($corps);
				$corps = nl2br($corps);
				$titre = str_replace(";", " ", $titre);
				$titre = pg_escape_string($titre);
				$corps = str_replace(";", chr(127), $corps);
				$corps = pg_escape_string($corps);
				$req_ins_mes = "INSERT INTO concours_barde_epreuves (ebar_perso_cod, ebar_titre, ebar_texte, ebar_cbar_cod)
								VALUES ($perso_cod, e'$titre', e'$corps', $saison_concours)";
				$stmt = $pdo->query($req_ins_mes);
				echo "<p>Votre participation a été enregistrée !</p>";
			}
			break;
	}
}

$texte_etat = '';
if ($ouvert)
	$texte_etat = 'ouvert';
if ($introduction)
	$texte_etat = 'en préparation';
if ($futur)
	$texte_etat = 'futur';
if ($ferme)
	$texte_etat = 'fermé';

echo "<h2>Le concours est actuellement $texte_etat !</h2>";
?>
<p align="center">
	Dégainez percussions, cordes,<br />
	Et que Vers et verres débordent...<br />
	C’est la ré-ouverture de ce concours !!!<br />
	Pour gagner, montrez vos meilleurs tours,<br />
	Le public attend vos œuvres<br />
	Soyez à l’heure !<br /><br /></p>
	<p><strong>Le thème de la session :</strong></p>
<?php 
echo '<p class="soustitre2">' . nl2br($description) . '</p>';
?><br />
<p><strong>Les règles du concours :</strong></p>
<p>
	Déclamez vers ou prose, chantez à la guerrière, ou créez une épopée... Tout sera bon, et les jurés récompenseront les meilleures œuvres. Le nom des auteurs restera caché (y compris pour le jury) jusqu’à ce que tout le jury ait fait son évaluation.
	<br />
	Une seule participation par personnage est autorisée, familiers compris ! Aucune modification ne peut se faire une fois envoyé.
	Nous rappelons enfin qu'il s'agit ici d'un jeu, et ce y compris pour les membres du jury. Les remarques de ces derniers sont donc à prendre dans ce cadre.
	<br />
	Que les meilleurs gagnent !!!
	Les recueils des années précédentes vous attendent, pour chercher l’inspiration et se délecter des œuvres les plus appréciées par les anciens jurés.
</p>
<?php 
if ($ouvert && $fermeture)
	echo "<p><strong>Le concours fermera le $fermeture</strong></p>";
if ($introduction && $date_ouverture && !$ouvert)
{
	echo "<p><strong>Le concours ouvrira le $date_ouverture";
	if ($fermeture)
		echo " et fermera le $fermeture";
	echo "</strong></p>";
}
?>
<form name="voir_saison" method="post" action="concours_barde.php">
	<input type="hidden" name="saison_concours" value="">
</form>
<form name="voir_texte" method="post" action="concours_barde_texte.php">
	<input type="hidden" name="texte_cod" value="">
</form>
<form name="desc" method="post" action="visu_desc_perso.php">
	<input type="hidden" name="visu" value="">
</form>
<script language="javascript">
function voirSaison(code)
{
	document.voir_saison.saison_concours.value = code;
	document.voir_saison.submit();
}
function voirPerso(code)
{
	document.desc.visu.value = code;
	document.desc.submit();
}
function voirTexte(code)
{
	document.voir_texte.texte_cod.value = code;
	document.voir_texte.submit();
}
</script>
<table width="100%">
<tr>
	<td colspan="4" style="text-align:right;">
		<?php 
if(!isset($ordre))
	$ordre = 'date';
if($ordre == 'date')
	echo '<a href="' . $_SERVER['PHP_SELF'] . '?ordre=note">Trier par note</a>';
else
	echo '<a href="' . $_SERVER['PHP_SELF'] . '?ordre=date">Trier par ordre d’arrivée</a>';
?>
	</td>
</tr>
<tr>
	<td class="soustitre2"><strong>Auteur</strong></td>
	<td class="soustitre2"><strong>Titre</strong></td>
	<td class="soustitre2"><strong>Date</strong></td>
	<td class="soustitre2"><strong>Note</strong></td>
</tr>
<?php 
$req_jury = "SELECT jbar_perso_cod FROM concours_barde_jury WHERE jbar_cbar_cod = $saison_concours AND jbar_perso_cod = $perso_cod";
$stmt = $pdo->query($req_jury);
$isJury = ($stmt->rowCount() > 0);

$req_jury = "SELECT count(*) as nombre FROM concours_barde_jury WHERE jbar_cbar_cod = $saison_concours";
$stmt = $pdo->query($req_jury);
$result = $stmt->fetch();
$nbJury = $result['nombre'];

if ($isJury)
	$req_ins_mes = "SELECT ebar_cod, perso_nom, ebar_perso_cod, ebar_titre, COALESCE(note, 0) as note,
						to_char(ebar_date,'DD/MM/YYYY') as date, COALESCE(nbvote, 0) as nbvote, n2.nbar_note
					FROM concours_barde_epreuves
					INNER JOIN perso ON perso_cod = ebar_perso_cod
					LEFT OUTER JOIN (
							SELECT nbar_ebar_cod, count(*) as nbvote, SUM(COALESCE(nbar_note, 0)) as note 
							FROM concours_barde_note GROUP BY nbar_ebar_cod) t
						ON t.nbar_ebar_cod = ebar_cod
					INNER JOIN concours_barde_jury ON jbar_cbar_cod = ebar_cbar_cod
					LEFT OUTER JOIN concours_barde_note n2 ON n2.nbar_jbar_cod = jbar_cod AND n2.nbar_ebar_cod = ebar_cod
					WHERE jbar_perso_cod = $perso_cod AND ebar_cbar_cod = $saison_concours";
else
	$req_ins_mes = "SELECT ebar_cod, perso_nom, ebar_perso_cod, ebar_titre, COALESCE(note, 0) as note,
						to_char(ebar_date,'DD/MM/YYYY') as date, COALESCE(nbvote, 0) as nbvote, NULL as nbar_note
					FROM concours_barde_epreuves
					INNER JOIN perso ON perso_cod = ebar_perso_cod
					LEFT OUTER JOIN (
							SELECT nbar_ebar_cod, count(*) as nbvote, SUM(COALESCE(nbar_note, 0)) as note
							FROM concours_barde_note GROUP BY nbar_ebar_cod) t
						ON t.nbar_ebar_cod = ebar_cod
					WHERE ebar_cbar_cod = $saison_concours";
//echo "<div style='display:none'> $req_ins_mes </div>";
if($ordre == 'date')
	$req_ins_mes .= " ORDER BY ebar_cod DESC";
else
	$req_ins_mes .= " ORDER BY note DESC";

$stmt = $pdo->query($req_ins_mes);

while($result = $stmt->fetch())
{
	$nb_vote = $result['nbvote'];
	$publier_nom = $nb_vote == $nbJury;
	echo '<tr><td class="soustitre2">';

	if ($publier_nom)
	{
		echo '<a href="javascript:voirPerso(' . $result['ebar_perso_cod'] . ');">' . $result['perso_nom'] . '</a>';
	}
	else
	{
		echo "- caché -";
	}
	echo '</td>
		<td class="soustitre2"><a href="javascript:voirTexte(' . $result['ebar_cod'] . ');">' . $result['ebar_titre'];

	if ($isJury && $result['nbar_note'] == null)
	{
		echo ' <em>- non noté</em>';
	}
	echo '</a></td>';

	echo '<td class="soustitre2">' . $result['date'] . '</td>
			<td class="soustitre2">' . $result['note'] . '/' . ($nbJury * 20) . ' <em>(votes : ' . $result['nbvote'] . '/' . $nbJury . ')</em></td>
		</tr>';
}
echo '</table>';

if ($ouvert)
{
?>
<hr />
<p>Pour participer entrez ici le titre et le texte de votre composition originale :</p>
<form method="post">
	<input type="hidden" name="methode" value="participer">
	<p>Titre : <em>(limité à 50 caractères) </em><input type="text" name="titre" value=""></p>
	<p>Texte : <em>(Pas de taille minimale ou maximale, mais ne penchez pas dans l’excès)</em><br />
	<textarea name="contenu" cols="100" rows="15"></textarea></p>
	<input type="submit" value="Participer">
</form>
<?php 
}

$req_saisons = "SELECT cbar_cod, cbar_saison FROM concours_barde WHERE cbar_cod <> $saison_concours AND CURRENT_DATE >= cbar_date_teaser";
$stmt = $pdo->query($req_saisons);
while ($result = $stmt->fetch())
	echo '<a href="javascript:voirSaison(' . $result['cbar_cod'] . ');">Voir la saison ' . $result['cbar_saison'] . '</a> ';

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";