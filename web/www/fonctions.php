<?php 
require "includes/classes.php";
$db = new base_delain;
?>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
</head>
<body background="images/fond5.gif">
<?php 
if(!isset($methode))
{
	$methode = "debut";
}
include "jeu_test/tab_haut.php";
$dir = '/home/sdewitte/public_html/fonctions_prod';
switch($methode)
{
	case "debut":
		// listage des fichiers possibles
		if (is_dir($dir)) {
    		if ($dh = opendir($dir))
    		{
      		?>
      		<form method="post" action="<?php echo $PHP_SELF;?>">
      		<input type="hidden" name="methode" value="et2">
      		<p>Choisissez le fichier à intégrer :
				<select name="fichier">
      		<?php 
      		while (($file = readdir($dh)) !== false)
      		{
        			$tab_nom = explode(".",$file);
					$nb_nom = count($tab_nom);
					$n_extension = $nb_nom - 1;
					$extension = $tab_nom[$n_extension];
        			if ($extension == 'sql')
        			{
        				?>
        				<option value="<?php echo $file;?>"><?php echo $file;?></option>
        				<?php 
        			}
      		}
      		?>
      		</select>
      		<input type="submit" class="test" value="Suite !">
      		</form>
      		<?php 
      		closedir($dh);
    		}
		}
		break;
	case "et2":
		// on prend les arguments et le type de sortie
		?>
		<form method="post" action="<?php echo $PHP_SELF;?>">
      <input type="hidden" name="methode" value="et3">
      <input type="hidden" name="fichier" value="<?php echo $fichier;?>">
		<p>Entrez les informations nécessaires :
		<p>Arguments : <input type="text" name="arguments"> <i>(séparés par des virgules, dans l'ordre. Exemple : integer,integer ou bien text,integer ...)</i><br>
		<p>Type de sortie : <input type="text" name="sortie"> <i>Un type. Exemple : text ou integer, etc....</i>
		<input type="submit" class="test" value="Suite !">
		</form>
		<?php 
		break;
	case "et3":
		// connexion
		// on n'utilise pas la classe phplib en raison des nombreux points virgules qui vont poser problème
		$dbconnect = pg_connect("host=127.0.0.1 dbname=sdewitte user=sdewitte password=talwsatgig") or die("En cours de maintenance");; 
  		if(!$dbconnect)
        	{
	         echo("Une erreur est survenue.\n");
		 		exit;
       	}
		// construction de la chaine
		$chaine = "CREATE OR REPLACE FUNCTION ";
		$tab_nom = explode(".",$fichier);
		$chaine = $chaine . $tab_nom[0] . "(" . $arguments . ") returns " . $sortie . ' as $BODY$ '; 
		// ouverture du fichier
		$fichier = $dir . "/" . $fichier;
		echo "<p>Fichier : " . $fichier;
		echo "<p>Taille du fichier : " . filesize ($fichier);
		$handle = fopen($fichier, "r");
		$contents = fread ($handle, filesize ($fichier));
		echo "<hr>Code source : <br>";
		$chaine = $chaine . $contents;
		$chaine = $chaine . '$BODY$' . " LANGUAGE 'plpgsql' VOLATILE;";
  		echo nl2br($chaine);
  		echo "<hr>";
  		// lancement de la requête
  		$res = pg_query($dbconnect,$chaine) or die (pg_result_error($dbconnect));
  		if($res)
  		{
  			echo "<p>Fonction bien enregistrée ! Pensez à effacer les fichiers sources une fois les modifications terminées.";
  		}
  		fclose($handle);
		break;

}
include "jeu_test/tab_bas.php";
?>
</body>
</html>
