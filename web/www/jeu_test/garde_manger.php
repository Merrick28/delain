<?php 
include_once "verif_connexion.php";

/* Paramètres généraux */
$cout_pa		= 6; // Affiché sur le bouton d'action
$nom_script	= 'lieu.php';
$contenu		= '<p>Vous &ecirc;tes emprisonn&eacute; dans un sac de provision...</p>'; // Texte à afficher au joueur
$gm_texte = '0'; // Résultat de la fonction sortir_gm()

/* Tentative de sortie en cours */
if ( isset($_POST['sortir']) || isset($sortir) ) { // Register_globals ^^ ...
	$req = "select sortir_gm($perso_cod) as resultat";
	$db->query($req);
	$db->next_record();
	$gm_texte = $db->f('resultat');
	$contenu .= substr($gm_texte,1);
}

echo $contenu;

if ($gm_texte[0] != '1') { // Si le premier caratère est 1, on masque le menu d'action
?>
<p>
	<form name="sortirdusac" method="post" action="<?php echo $nom_script; ?>">
		Vous n’allez pas rester l&agrave; &eacute;ternellement ?
		<input name="sortir" type="hidden" value="sortir" />
		<input name="valider" type="submit" value="Sortir du sac (<?php echo $cout_pa; ?> PA)" class="test" />
	</form>
</p>
<?php }
?>
<p style="text-align:center;"><a href="frame_vue.php">Retour &agrave; la vue !</a></p>
