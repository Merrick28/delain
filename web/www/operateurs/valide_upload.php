<?php 
//include "../connexion.php";
include "verif_connexion.php";
error_reporting(E_ALL);
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php include "../jeu/tab_haut.php";

	$valide = 0;
	if ($valide == 0)
	{
	  $uploaddir = '/home/sdewitte/public_html/docs/';
		if (is_uploaded_file($_FILES['avatar']['tmp_name']))
		{
			echo "Fichier uploadé, étape 1 OK<br>";
		}
		else
		{
			echo "Erreur sur étape 1 OK<br>";
		}
	  if (move_uploaded_file($_FILES['avatar']['tmp_name'], '/home/sdewitte/public_html/docs/' . $_FILES['avatar']['name']))
	    {
	      $erreur = 0;
	    }
	  else
	    {
	      $erreur = 1;
	    }
	  if ($erreur == 0)
	    {
	          echo("<p>Votre document est enregistré !");
	    }
	    else
	    {
	    	echo("<p>Erreur pendant l'upload !");
	    	 print_r($_FILES);
	    }
	}	
	else
	  {
	    echo("<p>Une erreur est survenue pendant l'upload !");
	    print_r($_FILES);
	  }

include "../jeu/tab_bas.php";
?>
</body>
</html>